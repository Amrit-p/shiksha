@extends('front/layout')
@section('page_title', 'Order Detail')
@section('container')

<section id="cart-view">
    <div class="container">
        <div class="row">

            {{-- LEFT : SHOP / OWNER DETAILS --}}
            <div class="col-md-6">
                <div class="order_detail">
                    <h3>Shop / Owner Details</h3>

                    <b>Shop Name:</b> {{ $order->shop_name }} <br>
                    <b>Owner Name:</b> {{ $order->owner_name }} <br>
                    <b>Phone:</b> {{ $order->owner_phone }} <br>

                    @if(!empty($order->owner_email))
                        <b>Email:</b> {{ $order->owner_email }} <br>
                    @endif

                    <b>Address:</b> {{ $order->owner_address }} <br>

                    @if(!empty($order->gst_number))
                        <b>GST:</b> {{ $order->gst_number }}
                    @endif
                </div>
            </div>

            {{-- RIGHT : ORDER INFO --}}
            <div class="col-md-6">
                <div class="order_detail">
                    <h3>Order Information</h3>

                    <b>Order ID:</b> #{{ $order->id }} <br>
                    <b>Order Status:</b> {{ $order->order_status }} <br>
                    <b>Payment Type:</b> {{ $order->payment_type }} <br>
                    <b>Payment Status:</b> {{ $order->payment_status }} <br>
                    <b>Punched At:</b> {{ $order->punch_in_time ?? $order->created_at }}
                </div>
            </div>

            {{-- ORDER ITEMS --}}
            <div class="col-md-12">
                <div class="cart-view-area">
                    <div class="cart-view-table">
                        <div class="table-responsive">

                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Image</th>
                                        <th>Variant / Attribute</th>
                                        <th>Price</th>
                                        <th>Qty</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @php
                                        $totalQty = 0;
                                        $totalAmt = 0;
                                    @endphp

                                    @foreach($items as $item)

                                        @php
                                            $pName = $item['product']['name'] ?? '-';
                                            $pImg  = $item['product']['image'] ?? null;
                                            $variantName = $item['variant']['name'] ?? null;
                                            $productCodeType = $item['variant']['product_code_type_name'] ?? null;
                                            $hasAttrs = !empty($item['attributes']);
                                        @endphp

                                        {{-- ================= SIMPLE PRODUCT ================= --}}
                                        @if(!$hasAttrs)
                                            @php
                                                $qty = (int)($item['qty'] ?? 0);
                                                $price = (float)($item['unit_price'] ?? 0);
                                                $lineTotal = (float)($item['total'] ?? ($price * $qty));

                                                $totalQty += $qty;
                                                $totalAmt += $lineTotal;
                                            @endphp

                                            <tr>
                                                <td>{{ $pName }}</td>

                                                <td>
                                                    @if($pImg)
                                                        <img src="{{ asset('storage/'.$pImg) }}" width="60">
                                                    @endif
                                                </td>

                                                <td>Simple Product</td>

                                                <td>₹{{ number_format($price, 2) }}</td>
                                                <td>{{ $qty }}</td>
                                                <td>₹{{ number_format($lineTotal, 2) }}</td>
                                            </tr>

                                        @else
                                        {{-- ================= VARIANT PRODUCT (ATTRIBUTE WISE) ================= --}}
                                            @foreach($item['attributes'] as $attr)

                                                @php
                                                    $aName = $attr['attribute_name'] ?? '';
                                                    $oName = $attr['option_name'] ?? '';
                                                    // quantity per attribute
                                                    $aQty = (int)($attr['qty'] ?? 1);

                                                    // ✅ FINAL PRICE LOGIC (FIX)
                                                    $aPrice = (float)(
                                                        $attr['unit_price']?? 0
                                                    );

                                                    $aTotal = (float)(
                                                        $attr['total']
                                                        ?? ($aPrice * $aQty)
                                                    );

                                                    $totalQty += $aQty;
                                                    $totalAmt += $aTotal;
                                                @endphp

                                                <tr>
                                                    <td>{{ $pName }}</td>

                                                    <td>
                                                        @if(!empty($attr['image']))
                                                            <img src="{{ asset('storage/'.$attr['image']) }}" width="60">
                                                        @elseif($pImg)
                                                            <img src="{{ asset('storage/'.$pImg) }}" width="60">
                                                        @endif
                                                    </td>

                                                    <td>  
                                                        @if ($productCodeType)
                                                            <div><b>Type:</b> {{ $productCodeType }}</div>  
                                                            
                                                        @endif
                                                        @if($variantName)
                                                            <div><b>Variant:</b> {{ $variantName }}</div>
                                                        @endif

                                                        
                                                        {{-- ✅ CUSTOM FIELDS DISPLAY --}}
                                                        @if(!empty($item['custom_fields']))
                                                            @foreach($item['custom_fields'] as $cf)
                                                                <div>
                                                                    <b>{{ $cf['type_name'] ?? 'Field' }}:</b>
                                                                    {{ $cf['value'] ?? '' }}
                                                                </div>
                                                            @endforeach
                                                        @endif
                                                        <div>
                                                            <b>Attribute:</b> {{ $aName }}
                                                            @if($oName) - {{ $oName }} @endif
                                                        </div>

                                                        @if(!empty($attr['is_por']) || $attr['is_por'] == "1"|| $attr['is_por'] == 1 )
                                                            <div>
                                                                <strong>POR:</strong> True
                                                            </div>
                                                        @endif

                                                    </td>

                                                    <td>₹{{ number_format($aPrice, 2) }}</td>
                                                    <td>{{ $aQty }}</td>
                                                    <td>₹{{ number_format($aTotal, 2) }}</td>
                                                </tr>

                                            @endforeach
                                        @endif

                                    @endforeach

                                    {{-- TOTAL --}}
                                    <tr>
                                        <td colspan="4" class="text-right"><b>Total</b></td>
                                        <td><b>{{ $totalQty }}</b></td>
                                        <td><b>₹{{ number_format($totalAmt, 2) }}</b></td>
                                    </tr>

                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
@endsection
