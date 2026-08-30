@extends('front/layout')
@section('page_title', 'Checkout')
@section('container')

    <section id="checkout">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="checkout-area">

                        <form id="frmPlaceOrder">
                            @csrf

                            <div class="row">

                                {{-- ================= LEFT SIDE ================= --}}
                                <div class="col-md-8">

                                    {{-- SALES EXECUTIVE --}}
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h4 class="panel-title">Sales Executive</h4>
                                        </div>
                                        <div class="panel-body">
                                            <input type="text" class="form-control" value="{{ auth()->user()->name }}"
                                                readonly>
                                            <input type="hidden" name="salesman_id" value="{{ auth()->id() }}">
                                        </div>
                                    </div>

                                    {{-- SHOP --}}
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h4 class="panel-title">Shop / Order Source</h4>
                                        </div>
                                        <div class="panel-body">
                                            <input type="text" name="shop_name" class="form-control"
                                                placeholder="Shop / Origination Name *">
                                            <small class="text-danger error-shop_name"></small>
                                        </div>
                                    </div>

                                    {{-- OWNER DETAILS --}}
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h4 class="panel-title">Shop Owner Details</h4>
                                        </div>
                                        <div class="panel-body">

                                            <div class="row">
                                                <div class="col-md-4">
                                                    <input type="text" name="owner_name" class="form-control"
                                                        placeholder="Owner Name *">
                                                    <small class="text-danger error-owner_name"></small>
                                                </div>
                                                <div class="col-md-4">
                                                    {{-- <input type="text" name="owner_phone" class="form-control"
                                                        placeholder="Phone *">
                                                    <small class="text-danger error-owner_phone"></small> --}}

                                                    <input type="text" name="owner_phone" class="form-control"
                                                        placeholder="Phone *" maxlength="10" inputmode="numeric"
                                                        oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                                    <small class="text-danger error-owner_phone"></small>
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="email" name="owner_email" class="form-control"
                                                        placeholder="Email">
                                                    <small class="text-danger error-owner_email"></small>
                                                </div>
                                            </div>
                                            <br>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <input type="text" name="city" class="form-control" placeholder="City">
                                                    {{-- <small class="text-danger error-owner_name"></small> --}}
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="text" name="state" class="form-control"
                                                        placeholder="State">
                                                    {{-- <small class="text-danger error-owner_name"></small> --}}
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="text" name="pin_code" class="form-control"
                                                        placeholder="Pin code">
                                                    {{-- <small class="text-danger error-owner_name"></small> --}}
                                                </div>

                                                {{-- <div class="col-md-12" style="margin-top:10px;">

                                                    <!-- Transportation Included Checkbox -->
                                                    <label style="font-weight:bold;">
                                                        <input type="checkbox" name="transport_included"
                                                            id="transport_included">
                                                        Transportation Included
                                                    </label>

                                                    <!-- Transportation Extra Fields -->
                                                    <div id="transport_fields" style="display:none; margin-top:10px;">

                                                        <input type="text" name="landmark" class="form-control mb-2"
                                                            placeholder="Landmark *">
                                                        <small class="text-danger error-landmark"></small>
                                                        <br>

                                                        <input type="text" name="transport_name" class="form-control"
                                                            placeholder="Transport Name *">
                                                        <small class="text-danger error-transport_name"></small>

                                                    </div>

                                                </div> --}}

                                            </div>

                                            <br>

                                            <textarea name="owner_address" class="form-control"
                                                placeholder="Address *"></textarea>
                                            <small class="text-danger error-owner_address"></small>

                                            <br>

                                            {{-- <input type="text" name="gst_number" class="form-control"
                                                placeholder="GST"> --}}
                                            <div class="row">
                                                <div class="col-md-12" style="margin-top:10px;">

                                                    <!-- Transportation Included Checkbox -->
                                                    <label style="font-weight:bold;">
                                                        <input type="checkbox" name="transport_included"
                                                            id="transport_included">
                                                        Transportation Included
                                                    </label>
                                                </div>
                                                <div class="row mt-2" style="width:100%; margin-left:0px;">
                                                    <!-- Landmark (hidden by default) -->

                                                    <!-- GST Number (always visible) -->
                                                    <div class="col-md-4">
                                                        <input type="text" name="gst_number" class="form-control"
                                                            placeholder="GST Number">
                                                        <small class="text-danger error-gst_number"></small>
                                                    </div>
                                                    <div class="col-md-4" {{-- id="landmark_field" style="display:none;"
                                                        --}}>
                                                        <input type="text" name="landmark" class="form-control"
                                                            placeholder="Landmark *" required>
                                                        <small class="text-danger error-landmark"></small>
                                                    </div>


                                                    <!-- Transport Name (hidden by default) -->
                                                    <div class="col-md-4" id="transport_field" style="display:none;">
                                                        <input type="text" name="transport_name" class="form-control"
                                                            placeholder="Transport Name *" required>
                                                        <small class="text-danger error-transport_name"></small>
                                                    </div>
                                                </div>

                                            </div>

                                        </div>
                                    </div>

                                </div>

                                {{-- ================= RIGHT SIDE ================= --}}
                                <div class="col-md-4">

                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h4 class="panel-title">Order Summary</h4>
                                        </div>
                                        <div class="panel-body">

                                            <table class="table table-bordered">
                                                @php
                                                    $totalQty = 0;
                                                    $totalAmount = 0;
                                                @endphp
                                                @foreach ($cart_data as $item)
                                                    @php
// dd($item->price);
                                                      $productPrice=0;
                                                    if($item->product_type=='1'|| $item->product_type==1){
                                                        $productPrice=$item->product_sale_price > 0  && $item->product_sale_price < $item->product_price ?$item->product_sale_price:$item->product_price;
                                                        // $totalPrice=$totalPrice+($item->qty*$productPrice);
                                                    }else{
                                                         if (!empty($item->is_por) && $item->is_por == 1) {
                                                            $productPrice = $item->price; // price from carts table
                                                        } else {
                                                            $productPrice =
                                                                $item->variant_attribute_sell_price > 0 &&
                                                                $item->variant_attribute_sell_price <
                                                                    $item->variant_attribute_mrp
                                                                    ? $item->variant_attribute_sell_price
                                                                    : $item->variant_attribute_mrp;
                                                        }


                                                        // $productPrice=$item->variant_attribute_sell_price > 0  && $item->variant_attribute_sell_price < $item->variant_attribute_mrp ?$item->variant_attribute_sell_price:$item->variant_attribute_mrp;
                                                        // $totalPrice=$totalPrice+($item->qty*$attributePrice);
                                                    }
                                                        $totalQty += $item->qty;
                                                        $totalAmount += $item->qty * $productPrice;
                                                    @endphp
                                                    <tr>
                                                        {{-- @dd($item); --}}
                                                        <td>{{ ucfirst($item->product_title) }}
                                                            @if ($item && $item->product_code_type_name)
                                                                <br> Type : {{ $item->product_code_type_name }}
                                                            @endif
                                                            @if ($item && $item->variant_name)
                                                                <br> Variant : {{ $item->variant_name }} <br>
                                                            @endif
                                                            @if ($item && $item->attribute_name && $item->attribute_option_name)
                                                                Attribute : {{ $item->attribute_name }}
                                                                {{ $item->attribute_option_name }}<br>
                                                            @endif
                                                            @if ($item && !empty($item->is_por) && $item->is_por == 1)
                                                                    POR : True
                                                                @endif
                                                        </td>
                                                        <td>{{ $item->qty ?? '' }}</td>
                                                        <td>₹{{ number_format($item->qty * $productPrice, 2) ?? '' }}</td>
                                                    </tr>
                                                @endforeach
                                                <tr>
                                                    <th>Total</th>
                                                    <th>{{ $totalQty }}</th>
                                                    <th>₹{{ number_format($totalAmount, 2) }}</th>
                                                </tr>
                                            </table>

                                            <input type="hidden" name="punch_in_time" value="{{ now() }}">
                                            <input type="hidden" name="order_status" value="Punch In">

                                            <lable style="display: block; margin-bottom:8px; font-weight: bold;">
                                                <input type="checkbox" name="gst_included" value="1" checked> GST Included
                                            </lable>

                                            <lable style="font-weight:bold;">
                                                <input type="radio" name="payment_type" value="COD" checked> Cash on
                                                Delivery
                                            </lable>
                                            <br>

                                            <label style="font-weight: bold;">
                                                <input type="radio" name="payment_type" value="credit"> Credit
                                            </label>

                                            <div id="credit_days_box" style="display:none; margin-top:8px;">
                                                <input type="number" name="credit_days" class="form-control"
                                                    placeholder="Number of Credit Days *">
                                                <small class="text-danger error-credit_days"></small>
                                            </div>


                                            {{-- <label>
                                                <input type="checkbox" name="payment_type" value="COD"> Cash on
                                                Delivery
                                            </label>
                                            <br>

                                            <label>
                                                <input type="checkbox" name="payment_type" value="credit"> Credit
                                            </label> --}}

                                            <br><br>

                                            <button type="button" id="placeOrderBtn" class="aa-browse-btn btn-block">
                                                Punch Order
                                            </button>

                                            <div id="order_place_msg" class="text-danger mt-2"></div>

                                        </div>
                                    </div>

                                </div>

                            </div>
                        </form>

                    </div>
                {{-- </div>
            </div>
        </div> --}}
    </section>
    {{-- hide --}}


    <!-- Toggle Script -->
    <script>
        const checkbox = document.getElementById('transport_included');
        // const landmarkField = document.getElementById('landmark_field');
        const transportField = document.getElementById('transport_field');

        checkbox.addEventListener('change', function () {
            // const show = this.checked;
            // landmarkField.style.display = show ? 'block' : 'none';
            transportField.style.display = this.checked ? 'block' : 'none';
        });
    </script>


    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const paymentRadios = document.querySelectorAll('input[name="payment_type"]');
            const creditBox = document.getElementById('credit_days_box');

            paymentRadios.forEach(radio => {
                radio.addEventListener('change', function () {
                    if (this.value === 'credit') {
                        creditBox.style.display = 'block';
                    } else {
                        creditBox.style.display = 'none';
                    }
                });
            });

        });
    </script>
    {{-- ================= PURE JAVASCRIPT ================= --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const form = document.getElementById('frmPlaceOrder');
            const btn = document.getElementById('placeOrderBtn');
            const msg = document.getElementById('order_place_msg');

            function clearErrors() {
                msg.innerText = '';
                document.querySelectorAll('small.text-danger').forEach(el => el.innerText = '');
            }

            btn.addEventListener('click', function () {

                clearErrors();

                let valid = true;

                const shopName = form.shop_name.value.trim();
                const ownerName = form.owner_name.value.trim();
                const ownerPhone = form.owner_phone.value.trim();
                const ownerEmail = form.owner_email.value.trim();
                const ownerAddress = form.owner_address.value.trim();

                if (!shopName) {
                    document.querySelector('.error-shop_name').innerText = 'Required';
                    valid = false;
                }

                if (!ownerName) {
                    document.querySelector('.error-owner_name').innerText = 'Required';
                    valid = false;
                }

                if (!ownerPhone) {
                    document.querySelector('.error-owner_phone').innerText = 'Required';
                    valid = false;
                }

                if (ownerPhone && !/^[0-9]{10}$/.test(ownerPhone)) {
                    document.querySelector('.error-owner_phone').innerText = 'Enter valid 10 digit number';
                    valid = false;
                }

                // if (ownerPhone && !/^[6-9]\d{9}$/.test(ownerPhone)) {
                //     document.querySelector('.error-owner_phone').innerText = 'Invalid phone';
                //     valid = false;
                // }

                if (ownerEmail && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(ownerEmail)) {
                    document.querySelector('.error-owner_email').innerText = 'Invalid email';
                    valid = false;
                }

                if (!ownerAddress) {
                    document.querySelector('.error-owner_address').innerText = 'Required';
                    valid = false;
                }

                if($('#transport_included').is(':checked')) {
                    // const landmark = form.landmark.value.trim();
                    // const transportName = form.transport_name.value.trim();

                    if (!$('input[name="landmark"]').val()) {
                        document.querySelector('.error-landmark').innerText = 'Required';
                        valid = false;
                    }

                    if (!$('input[name="transport_name"]').val()) {
            $('.error-transport_name').text('Required');
            valid = false;
        }
                }

        //                 if (!$('input[name="landmark"]').val()) {
        //     $('.error-landmark').text('Required');
        //     return false;
        // }

        // if (!$('input[name="transport_name"]').val()) {
        //     $('.error-transport_name').text('Required');
        //     return false;
        // }

                // if (!valid) return;
                            if(!valid){
                    e.preventDefault();
                    return false;
                }

                btn.disabled = true;
                btn.innerText = 'Placing Order...';

                fetch("{{ route('front.place.order') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': form.querySelector('[name=_token]').value
                    },
                    body: new FormData(form)
                })
                    .then(res => res.json())
                    .then(res => {
                        if (res.status === 'success') {
                            window.location.href = res.redirect;
                        } else {
                            msg.innerText = res.msg || 'Something went wrong';
                            btn.disabled = false;
                            btn.innerText = 'Punch Order';
                        }
                    })
                    .catch(() => {
                        msg.innerText = 'Server error';
                        btn.disabled = false;
                        btn.innerText = 'Punch Order';
                    });
            });

        });
    </script>

@endsection
