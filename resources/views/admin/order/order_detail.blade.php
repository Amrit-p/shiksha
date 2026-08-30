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

    {{-- ORDER SUMMARY --}}
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">Order Summary</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3"><b>Order ID:</b> #{{ $order->id }}</div>
                <div class="col-md-3"><b>Date:</b> {{ $order->created_at->format('d-m-Y H:i') }}</div>
                <div class="col-md-3">
                    <b>Status:</b>
                    <span class="badge badge-success">{{ $order->order_status }}</span>
                </div>
                <div class="col-md-3">
                    <b>Payment:</b>
                    <span class="badge badge-info">{{ ucfirst($order->payment_status) }}</span>
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
                    <p><b>Total Amount:</b> ₹{{ number_format($order->total_amount, 2) }}</p>
                    <p>
                        <b>Status:</b>
                        <span class="badge badge-success">{{ ucfirst($order->payment_status) }}</span>
                    </p>
                </div>
            </div>
        </div>

    </div>

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
                    <tr>
                        <th colspan="6" class="text-right">Grand Total</th>
                        <th>₹{{ number_format($grandTotal,2) }}</th>
                    </tr>
                </tfoot>

            </table>

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
