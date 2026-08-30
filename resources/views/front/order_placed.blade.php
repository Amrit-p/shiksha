@extends('front/layout')
@section('page_title','Order Placed')
@section('container')

<section id="aa-product-category" style="padding-top: 3rem; padding-bottom: 3rem;">
    <div class="container">
        <div class="row text-center">

            <h2 class="text-success mb-3">🎉 Order Placed Successfully</h2>
            <p class="mb-2">Thank you for shopping with us</p>

            <div class="card mt-4 w-100">
                <div class="card-body text-left">

                    <p><strong>Order ID:</strong> #{{ $order->id }}</p>
                    <p>
                        <strong>Order Date:</strong>
                        {{ date('d M Y, h:i A', strtotime($order->created_at)) }}
                    </p>

                    <h5 class="mb-3">Items</h5>

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Details</th>
                                <th>Qty</th>
                                <th>Price</th>
                                <th>Total</th>
                            </tr>
                        </thead>

                        <tbody>
                            @php $grandTotal = 0; @endphp

                            @foreach($items as $item)

 
                                {{-- ================= SIMPLE PRODUCT ================= --}}
                                @if(empty($item['attributes']))

                                    @php
                                        $grandTotal += $item['total'];
                                    @endphp

                                    <tr>
                                        <td>
                                            @if(!empty($item['product']['image']))
                                                <img src="{{ asset('storage/'.$item['product']['image']) }}"
                                                     style="width:60px">
                                            @endif
                                            <br>
                                            {{ $item['product']['name'] }}
                                        </td>

                                        <td>Simple Product</td>

                                        <td>{{ $item['qty'] }}</td>

                                        <td>Rs {{ number_format($item['unit_price'], 2) }}</td>

                                        <td>Rs {{ number_format($item['total'], 2) }}</td>
                                    </tr>

                                @else
                                {{-- ================= VARIANT PRODUCT (ATTRIBUTE WISE) ================= --}}

                                    @foreach($item['attributes'] as $attr)

                                        @php
                                            $grandTotal += $attr['total'];

                                            // IMAGE LOGIC
                                            $image = $attr['image']
                                                ?? ($item['product']['image'] ?? null);
                                        @endphp

                                        <tr>
                                            <td>
                                                @if($image)
                                                    <img src="{{ asset('storage/'.$image) }}"
                                                         style="width:60px">
                                                @endif
                                                <br>
                                                {{ $item['product']['name'] }}
                                            </td>

                                            <td>
                                                @if(!empty($item['variant']) && !empty($item['variant']['product_code_type_name']))
                                                    <div>
                                                        <strong>Type:</strong>
                                                        {{ $item['variant']['product_code_type_name'] }}
                                                    </div>
                                                @endif
                                                @if(!empty($item['variant']))
                                                    <div>
                                                        <strong>Variant:</strong>
                                                        {{ $item['variant']['name'] }}
                                                    </div>
                                                @endif

                                                 {{-- ✅ CUSTOM FIELDS DISPLAY --}}
                                            @if(!empty($item['custom_fields']))
                                                @foreach($item['custom_fields'] as $cf)
                                                    <div>
                                                        <strong>{{ $cf['type_name'] ?? 'Field' }}:</strong>
                                                        {{ $cf['value'] }}
                                                    </div>
                                                @endforeach
                                            @endif
                                                
                                                @if(!empty($attr['attribute_name']) && !empty( $attr['option_name']))
                                                <div>
                                                   Attribute: <strong>{{ $attr['attribute_name'] }}</strong>
                                                    ({{ $attr['option_name'] }})
                                                </div>
                                                  @endif


                                                @if(!empty($attr['is_por']) || $attr['is_por'] == "1"|| $attr['is_por'] == 1 )
                                                    <div>
                                                        <strong>POR:</strong> True
                                                    </div>
                                                @endif
                                                
                                            </td>

                                            <td>{{ $attr['qty'] }}</td>

                                            <td>Rs {{ number_format($attr['unit_price'], 2) }}</td>

                                            <td>Rs {{ number_format($attr['total'], 2) }}</td>
                                        </tr>

                                    @endforeach
                                @endif

                            @endforeach
                        </tbody>

                        <tfoot>
                            <tr>
                                <th colspan="4" class="text-right">Grand Total</th>
                                <th>Rs {{ number_format($grandTotal, 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>

                </div>
            </div>

            <div class="mt-4">
                <a href="{{ url('/') }}" class="btn btn-outline-primary">
                    Continue Shopping
                </a>

                <a href="{{ route('front.order') }}" class="btn btn-primary ml-2">
                    View My Orders
                </a>
            </div>

        </div>
    </div>
</section>

@endsection
