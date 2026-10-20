<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderPaymentRequest;
use App\Models\Order;
use App\Models\OrderPayment;
use App\Models\User;
use App\Services\OrderPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/** Super admin: payment verification queue and admin-recorded payments. */
class OrderPaymentController extends Controller
{
    public function __construct(protected OrderPaymentService $payments) {}

    public function index(Request $request)
    {
        $status = $request->input('status', OrderPayment::STATUS_PENDING);
        $statuses = [OrderPayment::STATUS_PENDING, OrderPayment::STATUS_VERIFIED, OrderPayment::STATUS_REJECTED, OrderPayment::STATUS_REVERSED, 'all'];
        if (! in_array($status, $statuses, true)) {
            $status = OrderPayment::STATUS_PENDING;
        }

        $payments = OrderPayment::query()
            ->with(['order:id,shop_name,total_amount,order_status', 'salesman:id,name', 'recorder:id,name', 'reviewer:id,name'])
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->when($request->integer('salesman_id'), fn ($q, $id) => $q->where('user_id', $id))
            ->orderByDesc($status === OrderPayment::STATUS_PENDING ? 'created_at' : 'paid_on')
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        $counts = OrderPayment::query()->selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status');
        $salesmen = User::query()->whereIn('id', OrderPayment::query()->select('user_id'))->orderBy('name')->get(['id', 'name']);

        return view('admin.payments.index', compact('payments', 'status', 'statuses', 'counts', 'salesmen'));
    }

    /** Record a payment from the admin order detail page (verified immediately). */
    public function store(OrderPaymentRequest $request, Order $order)
    {
        $this->payments->log($order, $request->validated(), $request->user(), $request->file('proof'));

        return back()->with('success', 'Payment recorded. Order is now '.paymentStatusLabel($order->fresh()->payment_status).'.');
    }

    public function verify(Request $request, OrderPayment $payment)
    {
        $this->payments->verify($payment, $request->user());

        return back()->with('success', "Payment #{$payment->id} verified.");
    }

    public function reject(Request $request, OrderPayment $payment)
    {
        $data = $request->validate(['reason' => 'required|string|min:3|max:1000']);
        $this->payments->reject($payment, $request->user(), $data['reason']);

        return back()->with('success', "Payment #{$payment->id} rejected.");
    }

    public function reverse(Request $request, OrderPayment $payment)
    {
        $data = $request->validate(['reason' => 'required|string|min:3|max:1000']);
        $this->payments->reverse($payment, $request->user(), $data['reason']);

        return back()->with('success', "Payment #{$payment->id} reversed.");
    }

    public function proof(OrderPayment $payment)
    {
        abort_unless($payment->proof_path && Storage::disk(OrderPaymentService::PROOF_DISK)->exists($payment->proof_path), 404);

        return Storage::disk(OrderPaymentService::PROOF_DISK)->response($payment->proof_path);
    }
}
