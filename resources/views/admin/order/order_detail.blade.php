@extends('admin.layouts.main')
@section('title', 'Order Detail')

@section('content')
<div class="container-fluid mt-4">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="ik ik-shopping-cart mr-2"></i> Order Details
        </h4>
        <a href="{{  url()->previous() }}" class="btn btn-secondary btn-sm">
            <i class="ik ik-arrow-left"></i> Back
        </a>
    </div>

    <div class="row">@include('admin.include.message')</div>

    {{-- ORDER SUMMARY --}}
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">Order Summary</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3"><b>Order ID:</b> #{{ $order->id }}</div>
                <div class="col-md-3"><b>Date:</b> {{ $order->created_at->format('d-m-Y H:i') }}</div>
                <div class="col-md-3">
                    <b>Status:</b>
                    <span class="badge {{ $order->order_status === 'partially_delivered' ? 'badge-warning' : 'badge-success' }}">{{ orderStatusLabel($order->order_status) }}</span>
                </div>
                <div class="col-md-3">
                    <b>Payment:</b>
                    {!! paymentStatusBadge($order->payment_status) !!}
                </div>
            </div>
        </div>
    </div>

    {{-- CUSTOMER & PAYMENT --}}
    <div class="row">

        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">Customer Details</div>
                <div class="card-body">
                    <p><b>Name:</b> {{ $order->owner_name }}</p>
                    <p><b>Email:</b> {{ $order->owner_email ?? '-' }}</p>
                    <p><b>Phone:</b> {{ $order->owner_phone }}</p>
                    <p><b>Address:</b> {{ $order->owner_address }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">Payment Details</div>
                <div class="card-body">
                    <p><b>Method:</b> {{ $order->payment_type }}</p>
                    @if ((float) $order->discount_amount > 0)
                        <p><b>Coupon:</b> {{ $order->coupon_code }} (-₹{{ number_format($order->discount_amount, 2) }} on ₹{{ number_format($order->subtotal_amount, 2) }})</p>
                    @endif
                    <p><b>Total Amount:</b> ₹{{ number_format($order->total_amount, 2) }}</p>
                    <p>
                        <b>Status:</b>
                        {!! paymentStatusBadge($order->payment_status) !!}
                    </p>
                </div>
            </div>
        </div>

    </div>

    {{-- PAYMENTS RECEIVED --}}
    @php
        $verifiedPaid = (float) $order->payments->where('status', 'verified')->sum('amount');
        $pendingPaid = (float) $order->payments->where('status', 'pending')->sum('amount');
        $remaining = max(0, round((float) $order->total_amount - $verifiedPaid - $pendingPaid, 2));
    @endphp
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Payments Received</span>
            <span class="small">
                Verified <b>₹{{ number_format($verifiedPaid, 2) }}</b>
                · Pending <b>₹{{ number_format($pendingPaid, 2) }}</b>
                · Balance <b>₹{{ number_format(max(0, (float) $order->total_amount - $verifiedPaid), 2) }}</b>
            </span>
        </div>
        <div class="card-body table-responsive">
            @if($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif
            <table class="table table-sm">
                <thead><tr><th>#</th><th>Received on</th><th class="text-right">Amount</th><th>Mode / Ref.</th><th>Logged by</th><th>Status</th><th class="text-right">Action</th></tr></thead>
                <tbody>
                    @forelse($order->payments as $payment)
                        <tr>
                            <td>{{ $payment->id }}</td>
                            <td>{{ $payment->paid_on->format('d M Y') }}</td>
                            <td class="text-right">₹{{ number_format((float) $payment->amount, 2) }}</td>
                            <td>
                                {{ $payment->modeLabel() }}
                                @if($payment->reference)<div class="small text-muted">{{ $payment->reference }}</div>@endif
                                @if($payment->note)<div class="small text-muted">{{ $payment->note }}</div>@endif
                                @if($payment->proof_path)
                                    @can('admin')<a class="small" href="{{ route('admin.payments.proof', $payment) }}" target="_blank">Proof</a>@endcan
                                @endif
                            </td>
                            <td>{{ $payment->recorder?->name ?? ($payment->is_backfill ? 'Backfill' : '—') }}</td>
                            <td>
                                <span class="badge badge-{{ $payment->statusBadge() }}">{{ ucfirst($payment->status) }}</span>
                                @if($payment->review_reason)<div class="small text-muted">{{ $payment->review_reason }}</div>@endif
                            </td>
                            <td class="text-right text-nowrap">
                                @can('admin')
                                    @if($payment->status === 'pending')
                                        <form method="POST" action="{{ route('admin.payments.verify', $payment) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success">Verify</button>
                                        </form>
                                        <button type="button" class="btn btn-sm btn-outline-danger payment-reason-btn" data-action="reject" data-id="{{ $payment->id }}" data-url="{{ route('admin.payments.reject', $payment) }}">Reject</button>
                                    @elseif($payment->status === 'verified')
                                        <button type="button" class="btn btn-sm btn-outline-secondary payment-reason-btn" data-action="reverse" data-id="{{ $payment->id }}" data-url="{{ route('admin.payments.reverse', $payment) }}">Reverse</button>
                                    @endif
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-3">No payments recorded yet.</td></tr>
                    @endforelse
                </tbody>
            </table>

            @can('admin')
                @if(! $order->isCancelled() && $remaining > 0)
                    <form method="POST" action="{{ route('admin.payments.store', $order->id) }}" enctype="multipart/form-data" class="border-top pt-3">
                        @csrf
                        <h6>Record a payment <small class="text-muted">(counts as verified)</small></h6>
                        <div class="form-row">
                            <div class="form-group col-md-2"><input type="number" step="0.01" min="0.01" max="{{ $remaining }}" name="amount" class="form-control form-control-sm" value="{{ old('amount', $remaining) }}" required></div>
                            <div class="form-group col-md-2"><input type="date" name="paid_on" class="form-control form-control-sm" value="{{ old('paid_on', now('Asia/Kolkata')->toDateString()) }}" max="{{ now('Asia/Kolkata')->toDateString() }}" required></div>
                            <div class="form-group col-md-2">
                                <select name="mode" class="form-control form-control-sm" required>
                                    @foreach(\App\Models\OrderPayment::MODES as $value => $label)
                                        <option value="{{ $value }}" @selected(old('mode') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-2"><input name="reference" class="form-control form-control-sm" placeholder="Reference" value="{{ old('reference') }}"></div>
                            <div class="form-group col-md-2"><input type="file" name="proof" class="form-control-file" accept="image/*,application/pdf"></div>
                            <div class="form-group col-md-2"><button type="submit" class="btn btn-sm btn-success btn-block">Save Payment</button></div>
                        </div>
                        <input name="note" class="form-control form-control-sm" placeholder="Note (optional)" value="{{ old('note') }}">
                    </form>
                @endif
            @endcan
        </div>
    </div>
    @include('admin.payments._review_modal')

    {{-- ORDER ITEMS --}}
    <div class="card mb-4">
        <div class="card-header">Order Items</div>
        <div class="card-body p-0">

            <table class="table table-bordered mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th>Image</th>
                        <th>Variant / Attribute</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                </thead>

                <tbody>
                @php
                    $grandTotal = 0;
                    $sr = 1;
                @endphp

                @foreach($items as $item)

                    {{-- BASIC DATA --}}
                    @php
                    
                        $productName = $item['product']['name'] ?? '-';
                        $productImg  = $item['product']['image'] ?? null;
                        $variantName = $item['variant']['name'] ?? null;
                        $product_code_type_name = $item['variant']['product_code_type_name'] ?? null;

                        $hasAttributes = !empty($item['attributes']) && is_array($item['attributes']);
                        $customFields = (!empty($item['custom_fields']) && is_array($item['custom_fields']))
                            ? $item['custom_fields']
                            : [];
                    @endphp

                    {{-- SIMPLE PRODUCT --}}
                    @if(!$hasAttributes)

                        @php
                            $qty   = (int) ($item['qty'] ?? 1);
                            $price = (float) ($item['unit_price'] ?? 0);
                            $total = (float) ($item['total'] ?? ($price * $qty));
                            $grandTotal += $total;
                            
                        @endphp

                        <tr>
                            <td>{{ $sr++ }}</td>

                            <td><b>{{ $productName }}</b></td>

                            <td>
                                @if($productImg)
                                    <img src="{{ asset('storage/'.$productImg) }}" width="60">
                                @endif
                            </td>

                            <td>Simple Product</td>

                            <td>{{ $qty }}</td>
                            <td>₹{{ number_format($price,2) }}</td>
                            <td>₹{{ number_format($total,2) }}</td>
                        </tr>

                    @else
                    {{-- VARIANT PRODUCT --}}
                    {{-- @dd($item); --}}
                        @foreach($item['attributes'] as $attr)

                            @php
                            // dd($attr);
                                $aQty   = (int) ($attr['qty'] ?? 1);
                                $aPrice = (float) ($attr['unit_price'] ?? 0);
                                $aTotal = (float) ($attr['total'] ?? ($aPrice * $aQty));
                                $grandTotal += $aTotal;
                            @endphp

                            <tr>
                                <td>{{ $sr++ }}</td>

                                <td><b>{{ $productName }}</b></td>

                                <td>
                                    @if(!empty($attr['image']))
                                        <img src="{{ asset('storage/'.$attr['image']) }}" width="60">
                                    @elseif($productImg)
                                        <img src="{{ asset('storage/'.$productImg) }}" width="60">
                                    @endif
                                </td>

                                <td>
                                    @if($product_code_type_name)
                                        <div><b>Type:</b> {{ $product_code_type_name }}</div>
                                    @endif
                                    @if($variantName)
                                        <div><b>Variant:</b> {{ $variantName }}</div>
                                    @endif

                                     {{-- CUSTOM FIELDS --}}
                                    @if(!empty($customFields))
                                        @foreach($customFields as $cf)
                                            @php
                                                $cfName = $cf['type_name'] ?? null;
                                                $cfValue = $cf['value'] ?? null;

                                                if (is_null($cfName) && isset($cf['step_index'])) {
                                                    $cfName = 'Step ' . ((int)$cf['step_index'] + 1);
                                                }
                                            @endphp

                                            @if(!empty($cfValue))
                                                <div>
                                                    <b>{{ $cfName ?? 'Field' }}:</b> {{ $cfValue }}
                                                </div>
                                            @endif
                                        @endforeach
                                    @endif
                                    <div>
                                        <b>{{ $attr['attribute_name'] ?? '' }}</b>
                                        @if(!empty($attr['option_name']))
                                            - {{ $attr['option_name'] }}
                                        @endif
                                    </div>
                                      @if(!empty($attr['is_por']) && ((string)$attr['is_por'] === '1'))
                                        <div><b>POR:</b> True</div>
                                    @endif
                                </td>

                                <td>{{ $aQty }}</td>
                                <td>₹{{ number_format($aPrice,2) }}</td>
                                <td>₹{{ number_format($aTotal,2) }}</td>
                            </tr>

                        @endforeach
                    @endif

                @endforeach

                </tbody>

                <tfoot>
                    @if ((float) $order->discount_amount > 0)
                    <tr>
                        <th colspan="6" class="text-right">Subtotal</th>
                        <th>₹{{ number_format($grandTotal,2) }}</th>
                    </tr>
                    <tr>
                        <th colspan="6" class="text-right">Discount ({{ $order->coupon_code }})</th>
                        <th class="text-success">-₹{{ number_format($order->discount_amount,2) }}</th>
                    </tr>
                    @endif
                    <tr>
                        <th colspan="6" class="text-right">Grand Total</th>
                        <th>₹{{ number_format($grandTotal - (float) $order->discount_amount,2) }}</th>
                    </tr>
                </tfoot>

            </table>

        </div>
    </div>

    {{-- DELIVERY (partial / full) --}}
    <div class="card mb-4">
        <div class="card-header">Delivery Status</div>
        <div class="card-body p-0">
            @include('partials.order_delivery', ['editable' => true])
        </div>
    </div>

    {{-- ACTIONS --}}
    <div class="text-right mb-5">
        <a href="{{ route('admin.order.invoice.preview', $order->id) }}?print=1" target="_blank" class="btn btn-primary">
            <i class="ik ik-printer"></i> Print Invoice
        </a>
    </div>

</div>
@endsection
