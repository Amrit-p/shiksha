@extends('front/layout')
@section('page_title', 'My Cart')
@section('container')
    <style>
        /* Wrapper */
        .qty-pill {
            display: inline-flex;
            align-items: center;
            gap: 14px;
            padding: 6px 14px;
            border: 1px solid #e2e2e2;
            border-radius: 999px;
            background: #ffffff;
        }

        /* Buttons */
        .qty-action {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            border: none;
            background: #f5f5f5;
            font-size: 18px;
            line-height: 1;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        /* Hover */
        .qty-action:hover {
            background: #eaeaea;
        }

        /* Minus / Plus color */
        .qty-action.minus {
            color: var(--brand-primary);
        }

        .qty-action.plus {
            color: var(--brand-secondary);
        }

        /* Qty number */
        .qty-value {
            min-width: 20px;
            text-align: center;
            font-size: 15px;
            font-weight: 600;
            color: #222;
        }

        #cart-view .cart-view-area .cart-view-table .table tbody tr td .aa-cart-title {
            color: var(--brand-dark);
            font-weight: 600;
        }

        #cart-view .cart-view-area .cart-view-table .table tbody tr td .aa-cart-title:hover,
        #cart-view .cart-view-area .cart-view-table .table tbody tr td .aa-cart-title:focus {
            color: var(--brand-primary);
        }
    </style>
    <section id="cart-view" class="mt-4 mb-5">
        <div class="container">
            <div class="row">
                <div class="col-md-12">

                    <div class="cart-view-area">
                        <div class="cart-view-table">
                            @if (isset($list) && count($list) > 0)

                                <div class="table-responsive">
                                    <table class="table table-bordered text-center align-middle">
                                        <thead class="thead-light">
                                            <tr>
                                                <th style="width:40px;"></th>
                                                <th style="width:90px;">Image</th>
                                                <th class="text-left">Product</th>
                                                <th style="width:120px;">Price</th>
                                                <th style="width:120px;">Quantity</th>
                                                <th style="width:120px;">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            @php $grandTotal = 0; @endphp

                                            @foreach ($list as $item)
                                                @php

                                                    $productPrice = 0;
                                                    $imageUrl = '';
                                                    if ($item->product_type == '1' || $item->product_type == 1) {
                                                        $productPrice =
                                                            $item->product_sale_price > 0 &&
                                                            $item->product_sale_price < $item->product_price
                                                                ? $item->product_sale_price
                                                                : $item->product_price;
                                                        $imageUrl = $item->product_image;
                                                    } else {
                                                        // ✅ POR price override
                                                        if (!empty($item->is_por) && $item->is_por == 1) {
                                                            $productPrice = $item->cart_price; // price from carts table
                                                        } else {
                                                            $productPrice =
                                                                $item->variant_attribute_sell_price > 0 &&
                                                                $item->variant_attribute_sell_price <
                                                                    $item->variant_attribute_mrp
                                                                    ? $item->variant_attribute_sell_price
                                                                    : $item->variant_attribute_mrp;
                                                        }
                                                        $imageUrl = $item->variant_attribute_image;
                                                        // $totalPrice=$totalPrice+($item->qty*$attributePrice);
                                                    }
                                                    $lineTotal = $productPrice * $item->qty;
                                                    $grandTotal += $lineTotal;

                                                @endphp

                                                <tr id="cart_row_{{ $item->cart_id }}">

                                                    {{-- REMOVE --}}
                                                    <td>
                                                        <a href="javascript:void(0)"
                                                            onclick="removeCartItem('{{ $item->cart_id }}')"
                                                            style="color:var(--brand-primary);font-size:18px;">
                                                            &times;
                                                        </a>
                                                    </td>

                                                    {{-- IMAGE --}}
                                                    <td>
                                                        <a href="{{ route('front.product', $item->slug) }}">
                                                            <img src="{{ asset('storage/' . $imageUrl) }}" alt="img"
                                                                style="width:70px;height:auto;">
                                                        </a>
                                                    </td>

                                                    {{-- PRODUCT INFO --}}
                                                    <td class="text-left">
                                                        <a class="aa-cart-title" href="{{ route('front.product', $item->slug) }}">
                                                            {{ $item->product_title }}
                                                        </a>


                                                        @if ($item->product_code_type_name)
                                                            <div class="small text-muted mt-1">
                                                                Type:
                                                                <strong>{{ $item->product_code_type_name }}</strong>
                                                            </div>
                                                        @endif
                                                        @if ($item->variant_name)
                                                            <div class="small text-muted mt-1">
                                                                Variant:
                                                                <strong>{{ $item->variant_name }}</strong>
                                                            </div>
                                                        @endif

                                                        @if ($item->attribute_name || $item->attribute_option)
                                                            <div class="small text-muted">
                                                                {{ $item->attribute_name }}:
                                                                <strong>{{ $item->attribute_option }}</strong>
                                                            </div>
                                                        @endif
                                                    </td>

                                                    {{-- PRICE --}}
                                                    <td>
                                                        Rs {{ number_format($productPrice, 2) }}
                                                    </td>

                                                    {{-- QUANTITY --}}
                                                    <td>
                                                        {{-- <input type="number" min="1" value="{{ $item->qty }}"
                                                            class="aa-cart-quantity"
                                                            onchange="updateCartQty('{{ $item->cart_id }}', this.value)"> --}}


                                                        <div class="qty-pill">
                                                            <button type="button" class="qty-action minus"
                                                                onclick="qtyMinus({{ $item->cart_id }})">
                                                                −
                                                            </button>

                                                            <span class="qty-value" id="qty_{{ $item->cart_id }}">
                                                                {{ $item->qty }}
                                                            </span>

                                                            <button type="button" class="qty-action plus"
                                                                onclick="qtyPlus({{ $item->cart_id }})">
                                                                +
                                                            </button>
                                                        </div>


                                                        {{-- <div class="aa-prod-quantity">
                                                            <div class="qty-wrapper">

                                                                <button type="button" class="qty-btn minus"
                                                                    onclick="qtyMinus({{ $item->cart_id }})">−</button>

                                                                <input type="number" id="qty_{{ $item->cart_id }}"
                                                                    value="{{ $item->qty }}" min="1"
                                                                    max="10" readonly>

                                                                <button type="button" class="qty-btn plus"
                                                                    onclick="qtyPlus({{ $item->cart_id }})">+</button>

                                                            </div>
                                                        </div> --}}

                                                    </td>

                                                    {{-- TOTAL --}}
                                                    <td id="line_total_{{ $item->cart_id }}">
                                                        Rs {{ number_format($lineTotal, 2) }}
                                                    </td>
                                                </tr>
                                            @endforeach

                                        </tbody>

                                        <tfoot>
                                            <tr>
                                                <td colspan="5" class="text-right font-weight-bold">
                                                    Grand Total
                                                </td>
                                                <td class="font-weight-bold">
                                                    Rs {{ number_format($grandTotal, 2) }}
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                                <div class="text-right mt-3">
                                    <a href="{{ route('front.checkout') }}" class="btn btn-success btn-lg">
                                        Proceed to Checkout
                                    </a>
                                </div>
                            @else
                                <h3 class="text-center text-muted">Your cart is empty</h3>
                            @endif

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    {{-- ================= JS ================= --}}
    <script>
        function qtyPlus(cartId) {
            let el = document.getElementById('qty_' + cartId);
            let qty = parseInt(el.innerText);
            qty++;
            el.innerText = qty;
            updateCartQty(cartId, qty);
        }

        function qtyMinus(cartId) {
            let el = document.getElementById('qty_' + cartId);
            let qty = parseInt(el.innerText);
            if (qty <= 1) return;
            qty--;
            el.innerText = qty;
            updateCartQty(cartId, qty);
        }

        function updateCartQty(cartId, qty) {

            if (qty <= 0) {
                alert('Quantity must be at least 1');
                return;
            }
            console.log(qty);


            $.ajax({
                url: '/update-cart-qty',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    cart_id: cartId,
                    qty: qty
                },
                success: function() {
                    location.reload();
                },
                error: function(xhr) {
                    var msg = (xhr.responseJSON && xhr.responseJSON.msg) ? xhr.responseJSON.msg : 'Unable to update quantity.';
                    alert(msg);
                    location.reload();
                }
            });
        }

        function removeCartItem(cartId) {

            if (!confirm('Remove this item from cart?')) return;

            $.ajax({
                url: "{{ route('front.cart.remove') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    cart_id: cartId
                },
                success: function(res) {

                    if (res.status === true) {

                        // remove row
                        $('#cart_row_' + cartId).fadeOut(300, function() {
                            $(this).remove();
                        });

                        // ✅ update totals WITHOUT reload
                        $('#cart-total').text(res.totalPrice);
                        $('#cart-count').text(res.totalQty);
                        location.reload();


                        // ✅ optional: cart empty state
                        if (res.totalQty == 0) {
                            $('#cart-body').html('<p>Your cart is empty</p>');
                        }


                    }
                }
            });
        }
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        function updateQty(cartId, qty, price) {

            if (qty <= 0) {
                alert('Invalid qty');
                return;
            }

            $.post("{{ route('front.cart.updateQty') }}", {
                _token: "{{ csrf_token() }}",
                cart_id: cartId,
                qty: qty,
                price: price
            }, function(res) {

                if (!res.status) return;

                $('#total_' + cartId).text('Rs ' + (qty * price).toFixed(2));
                $('#cart_total').text(res.cart_total.toFixed(2));
            });
        }

        function removeItem(cartId) {

            if (!confirm('Remove item?')) return;

            $.post("{{ route('front.cart.remove') }}", {
                _token: "{{ csrf_token() }}",
                cart_id: cartId
            }, function(res) {

                if (!res.status) return;

                $('#row_' + cartId).remove();
                $('#cart_total').text(res.cart_total.toFixed(2));
            });
        }
    </script>


@endsection
