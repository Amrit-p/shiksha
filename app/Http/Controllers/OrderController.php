<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
use App\Models\Color;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\OrderPayment;
use App\Models\User;
use App\Services\OrderDeliveryService;
use App\Services\OrderPaymentService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{
    /** Canonical order status slugs (filter + edit modal). */
    public const ORDER_STATUS_SLUGS = [
        'pending',
        'order_received',
        'fulfilled',
        'dispatched',
        'on_the_way',
        'rto',
        'partially_delivered',
        'delivered',
        'cancelled',
    ];

  public function index()
  {
    return view('admin.order.index', [
      'orderStatusOptions' => self::ORDER_STATUS_SLUGS,
    ]);
  }

  /**
   * Salesmen with at least one placed order (orders.user_id).
   */
  public function salesmenIndex()
  {
    $salesmen = User::query()
      ->withCount('placedOrders')
      ->has('placedOrders')
      ->orderByDesc('placed_orders_count')
      ->orderBy('name')
      ->get();

    return view('admin.order.salesmen_index', compact('salesmen'));
  }

  /**
   * Order list filtered to one salesman (same DataTable as main order list).
   */
  public function salesmanOrders(User $user)
  {
    return view('admin.order.salesman_orders', [
      'salesman' => $user,
      'orderStatusOptions' => self::ORDER_STATUS_SLUGS,
    ]);
  }

public function getList(Request $request)
{
    $query = DB::table('orders')
        ->leftJoin('users', 'users.id', '=', 'orders.user_id')
        ->select(
            'orders.id',
            'orders.shop_name',
            'orders.owner_name',
            'orders.owner_email',
            'orders.owner_phone',
            'orders.owner_address',
            'orders.city',
            'orders.state',
            'orders.pin_code',
            'orders.total_amount',
            'orders.order_status',
            'orders.payment_status',
            'orders.created_at',
            'orders.product_json as product_snapshot',
            'orders.total_qty',
            DB::raw('users.name as user_name'),
            DB::raw("(SELECT COALESCE(SUM(op.amount), 0) FROM order_payments op
                WHERE op.order_id = orders.id AND op.status = 'verified') as verified_paid"),
            DB::raw('(SELECT COALESCE(SUM(odi.qty), 0) FROM order_delivery_items odi
                INNER JOIN order_deliveries od ON od.id = odi.order_delivery_id
                WHERE od.order_id = orders.id) as delivered_qty')
        )
        ->orderByDesc('orders.id');

    $salesmanId = $request->integer('salesman_id');
    if ($salesmanId > 0) {
      $query->where('orders.user_id', $salesmanId);
    }

    $statusFilter = $request->input('order_status');
    if (is_string($statusFilter) && $statusFilter !== '' && in_array($statusFilter, self::ORDER_STATUS_SLUGS, true)) {
      $query->where('orders.order_status', $statusFilter);
    }

    return DataTables::of($query)
        ->addIndexColumn()

        // Filters (same as your code but safe)
        ->filterColumn('user_name', function ($q, $keyword) {
            $q->whereRaw("LOWER(users.name) LIKE ?", ['%' . strtolower($keyword) . '%']);
        })
        ->filterColumn('shop_name', function ($q, $keyword) {
            $q->whereRaw("LOWER(orders.shop_name) LIKE ?", ['%' . strtolower($keyword) . '%']);
        })
        ->filterColumn('owner_name', function ($q, $keyword) {
            $q->whereRaw("LOWER(orders.owner_name) LIKE ?", ['%' . strtolower($keyword) . '%']);
        })
        ->filterColumn('owner_address', function ($q, $keyword) {
            $q->whereRaw("LOWER(orders.owner_address) LIKE ?", ['%' . strtolower($keyword) . '%']);
        })
        ->filterColumn('order_status', function ($q, $keyword) {
            $q->whereRaw("LOWER(orders.order_status) LIKE ?", ['%' . strtolower($keyword) . '%']);
        })
        ->filterColumn('payment_status', function ($q, $keyword) {
            $q->whereRaw("LOWER(orders.payment_status) LIKE ?", ['%' . strtolower($keyword) . '%']);
        })

        ->editColumn('customer_detail', function ($data) {
            return '
                <strong>' . e($data->owner_name) . '</strong><br>
                <small>' . e($data->owner_email) . ' | ' . e($data->owner_phone) . '</small>
            ';
        })

        ->editColumn('address_detail', function ($data) {
            $short = Str::limit($data->owner_address, 40);
            return '
                <span title="' . e($data->owner_address) . '">' . e($short) . '</span><br>
                <small>' . e($data->city) . ', ' . e($data->state) . ' - ' . e($data->pin_code) . '</small>
            ';
        })

        // ✅ Product meta built from product_snapshot JSON (safe after delete)
        ->addColumn('product_meta', function ($data) {
            $snapshot = [];
            if (!empty($data->product_snapshot)) {
                $decoded = json_decode($data->product_snapshot, true);
                if (is_array($decoded)) {
                    $snapshot = $decoded;
                }
            }

            if (empty($snapshot)) {
                return '-';
            }

            $variantNames = [];
            $attributePairs = [];

            foreach ($snapshot as $row) {
                $variantName = $row['variant']['name'] ?? null;
                if (!empty($variantName)) {
                    $variantNames[] = $variantName;
                }

                $attrs = $row['attributes'] ?? [];
                if (is_array($attrs)) {
                    foreach ($attrs as $a) {
                        $aName = $a['attribute_name'] ?? null;
                        $oName = $a['option_name'] ?? null;

                        if (!empty($aName) && !empty($oName)) {
                            $attributePairs[] = $aName . ' : ' . $oName;
                        }
                    }
                }
            }

            $variantNames = array_values(array_unique(array_filter($variantNames)));
            $attributePairs = array_values(array_unique(array_filter($attributePairs)));

            $html = '';

            if (!empty($variantNames)) {
                $html .= '<div><strong>Variant:</strong> ' . e(implode(', ', $variantNames)) . '</div>';
            }

            if (!empty($attributePairs)) {
                $html .= '<div><strong>Attribute:</strong> ' . e(implode(', ', $attributePairs)) . '</div>';
            }

            return $html ?: '-';
        })

        ->editColumn('created_at', function ($data) {
            return dmyHelper($data->created_at);
        })

        ->editColumn('payment_status', fn ($data) => paymentStatusBadge($data->payment_status))

        ->editColumn('order_status', function ($data) {
            $slug = (string) $data->order_status;
            $badge = match ($slug) {
                OrderDeliveryService::STATUS_PARTIAL => 'badge-warning',
                'delivered' => 'badge-success',
                'cancelled', 'rto' => 'badge-danger',
                default => 'badge-secondary',
            };
            $html = '<span class="badge ' . $badge . '">' . e(orderStatusLabel($slug)) . '</span>';
            if ($slug === OrderDeliveryService::STATUS_PARTIAL) {
                $pending = max((int) $data->total_qty - (int) $data->delivered_qty, 0);
                $html .= '<div><small class="text-danger">' . $pending . ' pending</small></div>';
            }
            return $html;
        })

        // Delivered / ordered quantity (sum of all delivery rounds vs orders.total_qty).
        ->addColumn('delivery', function ($data) {
            $ordered = (int) $data->total_qty;
            $delivered = min((int) $data->delivered_qty, $ordered);
            if ($ordered <= 0) {
                return '-';
            }
            $class = $delivered >= $ordered ? 'text-success' : ($delivered > 0 ? 'text-warning' : 'text-muted');
            return '<a href="javascript:void(0)" class="delivery-details-btn ' . $class . '" data-id="' . $data->id . '" title="Delivery details">'
                . '<b>' . $delivered . '</b> / ' . $ordered . '</a>';
        })

        ->addColumn('action', function ($data) {
            $isPaid = strtolower((string) $data->payment_status) === 'paid';
            $isCancelled = strtolower((string) $data->order_status) === 'cancelled';
            $balance = max(0, round((float) $data->total_amount - (float) $data->verified_paid, 2));

            $makePaid = $isPaid || $isCancelled
                ? ''
                : '<a class="dropdown-item make-paid-btn" href="javascript:void(0)" data-id="' . $data->id . '" data-balance="' . $balance . '">
                        <i class="ik ik-dollar-sign text-success"></i> Record Payment
                   </a>';

            $cancelOrder = $isCancelled
                ? ''
                : '<a class="dropdown-item cancel-order-btn text-danger" href="javascript:void(0)" data-id="' . $data->id . '">
                        <i class="ik ik-x-circle"></i> Cancel Order
                   </a>';

            return '
                <div class="dropdown d-inline-block">
                    <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="ik ik-more-vertical"></i>
                    </a>

                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item edit_btn" href="javascript:void(0)" data-id="' . $data->id . '">
                            <i class="ik ik-edit"></i> Order Status
                        </a>
                        ' . $makePaid . '
                        ' . $cancelOrder . '
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="' . route('admin.order.invoice.preview', $data->id) . '" target="_blank">
                            <i class="ik ik-file-text"></i> Preview Invoice
                        </a>
                        <a class="dropdown-item" href="' . route('admin.order.invoice.pdf', $data->id) . '">
                            <i class="ik ik-download"></i> Download PDF
                        </a>

                        <a class="dropdown-item" href="' . route('admin.order_detail', $data->id) . '">
                            <i class="ik ik-eye"></i> Order Detail
                        </a>
                        <a class="dropdown-item delivery-details-btn" href="javascript:void(0)" data-id="' . $data->id . '">
                            <i class="ik ik-truck"></i> Delivery Details
                        </a>
                    </div>
                </div>
            ';
        })

        ->rawColumns(['customer_detail', 'address_detail', 'product_meta', 'order_status', 'payment_status', 'delivery', 'action'])
        ->make(true);
}

  public function store(Request $request)
  {

    $request->all();
    // $formData = $request->except(['_token']);
    // $formData['slug'] = Str::slug($request->name);
    // $create = Color::create($formData);
    // if ($create) {
    //   MessageFlashHelper('success', 'Attribute Store Successfully');
    // } else {
    //   MessageFlashHelper('error', 'Something went wrong!');
    // }
    // return redirect()->back();
  }
  public function delete($id)
  {
    $delete = Order::find($id)->delete();
    if ($delete) {
      return response()->json(['message' => 'Order Deleted Successfully!', 'success' => true], 200);
    } else {
      return response()->json(['message' => 'something went wrong!', 'success' => false], 500);
    }
  }

  public function edit($id)
  {
    $data = Order::find($id);
    if ($data) {
      $deliveries = app(OrderDeliveryService::class);
      $lines = $deliveries->lines($data);

      return response()->json([
        'data' => $data,
        'lines' => $lines,
        'pending' => array_sum(array_column($lines, 'pending')),
        'is_cancelled' => strtolower((string) $data->order_status) === 'cancelled',
        'success' => true,
      ], 200);
    } else {
      return response()->json(['success' => false], 500);
    }
  }

  public function update(Request $request, OrderDeliveryService $deliveries)
  {
    $request->validate([
      'edit_id' => 'required|integer|exists:orders,id',
      'order_status' => ['required', 'string', Rule::in(self::ORDER_STATUS_SLUGS)],
    ]);

    $order = Order::findOrFail($request->edit_id);
    $isDeliveryStatus = in_array($request->order_status, [OrderDeliveryService::STATUS_PARTIAL, 'delivered'], true);

    // Partial Pending / Delivered with items still pending: record a delivery round from the per-item qty form.
    if ($isDeliveryStatus && ($pending = $deliveries->pendingQty($order)) > 0) {
      return $this->recordDeliveryFromStatus($request, $order, $deliveries);
    }

    if ($request->order_status === OrderDeliveryService::STATUS_PARTIAL) {
      MessageFlashHelper('error', "Order #{$order->id} has no pending items, so it cannot be Partial Pending.");
      return redirect()->back();
    }

    // Payment status is driven only by verified payments (OrderPaymentService), never by delivery.
    $order->update(['order_status' => $request->order_status]);

    MessageFlashHelper('success', 'Order Updated Successfully!');
    return redirect()->back();
  }

  /** Status popup submit for Partial Pending / Delivered: deliver the entered qty, then status follows what is left. */
  private function recordDeliveryFromStatus(Request $request, Order $order, OrderDeliveryService $deliveries)
  {
    $data = $request->validate([
      'delivered_on' => 'required|date|before_or_equal:today',
      'note' => 'nullable|string|max:1000',
      'qty' => 'required|array',
      'qty.*' => 'nullable|integer|min:0',
    ]);

    $qty = array_map('intval', $data['qty']);

    if ($request->order_status === 'delivered') {
      foreach ($deliveries->lines($order) as $line) {
        if ($line['pending'] > 0 && ($qty[$line['key']] ?? 0) < $line['pending']) {
          MessageFlashHelper('error', "Order #{$order->id}: \"{$line['name']}\" still has items pending. Choose Partial Pending if not everything was delivered.");
          return redirect()->back();
        }
      }
    }

    try {
      $deliveries->record($order, $qty, $data['delivered_on'], $data['note'] ?? null, $request->user());
    } catch (\Illuminate\Validation\ValidationException $e) {
      MessageFlashHelper('error', "Order #{$order->id}: " . $e->validator->errors()->first());
      return redirect()->back();
    }

    $order->refresh();
    $left = $deliveries->pendingQty($order);
    MessageFlashHelper('success', "Order #{$order->id} is now " . orderStatusLabel($order->order_status)
      . ($left > 0 ? " ({$left} item(s) still pending)." : '.'));

    return redirect()->back();
  }

  /** HTML for the "Delivery Details" popup on the order list. */
  public function deliveryDetails($id, OrderDeliveryService $deliveries)
  {
    $order = Order::findOrFail($id);
    $deliveryLines = $deliveries->lines($order);
    $order->load('deliveries.items', 'deliveries.creator');

    return view('admin.order.delivery_details', compact('order', 'deliveryLines'));
  }

  /** "Record Payment" from the order list: an admin-entered payment is verified straight away. */
  public function makePaid(Request $request, OrderPaymentService $payments)
  {
    $data = $request->validate([
      'order_id' => 'required|integer|exists:orders,id',
      'amount' => 'required|numeric|min:0.01',
      'paid_on' => 'required|date|before_or_equal:today',
      'mode' => ['required', Rule::in(array_keys(OrderPayment::MODES))],
      'reference' => 'nullable|string|max:100',
      'note' => 'nullable|string|max:1000',
    ]);

    $order = Order::findOrFail($data['order_id']);
    $payments->log($order, $data, $request->user());

    return response()->json([
      'success' => true,
      'message' => 'Payment recorded. Order is now ' . paymentStatusLabel($order->fresh()->payment_status) . '.',
    ]);
  }

  public function cancelOrder(Request $request, OrderPaymentService $payments)
  {
    $request->validate([
      'order_id' => 'required|integer|exists:orders,id',
      'cancel_note' => 'required|string|min:3|max:2000',
    ], [
      'cancel_note.required' => 'A cancel note is required.',
      'cancel_note.min' => 'Cancel note must be at least 3 characters.',
    ]);

    $order = Order::findOrFail($request->order_id);
    if (strtolower((string) $order->order_status) === 'cancelled') {
      return response()->json(['success' => false, 'message' => 'Order is already cancelled.'], 422);
    }

    // Throws (422) while verified payments exist; auto-rejects pending ones.
    $payments->prepareCancel($order, $request->user());

    $order->update([
      'order_status' => 'cancelled',
      'cancel_note' => $request->cancel_note,
      'cancelled_at' => now(),
    ]);

    return response()->json(['success' => true, 'message' => 'Order cancelled.']);
  }
public function order_detail($id, OrderDeliveryService $deliveries)
{
    $order = Order::findOrFail($id);

    // Decode snapshot safely
    $items = [];
    if (!empty($order->product_json)) {
        $decoded = json_decode($order->product_json, true);
        if (is_array($decoded)) {
            $items = $decoded;
        }
    }

    if (empty($items)) {
        return redirect()->route('admin.order')
            ->with('error', 'Order snapshot not found');
    }

    $deliveryLines = $deliveries->lines($order);
    $order->load('deliveries.items', 'deliveries.creator', 'payments.recorder');

    return view('admin.order.order_detail', compact('order', 'items', 'deliveryLines'));
}

/** Record one delivery round (partial or final) from the order detail page. */
public function storeDelivery(Request $request, $id, OrderDeliveryService $deliveries)
{
    $order = Order::findOrFail($id);

    $data = $request->validate([
        'delivered_on' => 'required|date|before_or_equal:today',
        'note' => 'nullable|string|max:1000',
        'qty' => 'required|array',
        'qty.*' => 'nullable|integer|min:0',
    ]);

    $deliveries->record($order, $data['qty'], $data['delivered_on'], $data['note'] ?? null, $request->user());

    $order->refresh();
    MessageFlashHelper('success', 'Delivery recorded. Order is now ' . orderStatusLabel($order->order_status) . '.');

    return redirect()->route('admin.order_detail', $order->id);
}

public function invoicePreview($id)
{
    $order = Order::findOrFail($id);
    $items = $this->getOrderItems($order);
    return view('admin.order.invoice_pdf', compact('order', 'items') + ['forPdf' => false]);
}

/**
 * Download order invoice as PDF (invoice only, no webpage layout).
 * Filename: Invoice-Order-{id}.pdf
 */
public function invoicePdf($id)
{
    $order = Order::findOrFail($id);
    $items = $this->getOrderItems($order);
    $pdf = Pdf::loadView('admin.order.invoice_pdf', compact('order', 'items') + ['forPdf' => true]);
    $filename = 'Invoice-Order-' . $order->id . '.pdf';
    return $pdf->download($filename);
}

/**
 * Decode order product snapshot for invoice display.
 */
private function getOrderItems(Order $order): array
{
    $items = [];
    if (!empty($order->product_json)) {
        $decoded = json_decode($order->product_json, true);
        if (is_array($decoded)) {
            $items = $decoded;
        }
    }
    return $items;
}

  // public function edit($id){
  //   return  Order::find($id);
  //  }
}
