@extends('shop.layout')

@section('content')
<div class="page-wrap page-hero">
    <h1>Enquiry Cart</h1>
    <p>Review selected products, then submit your enquiry.</p>
</div>

<div class="page-wrap" style="padding-bottom:3rem;">
    <div class="panel" style="padding:1.25rem;">
        @if(count($items))
            <div style="display:flex;justify-content:space-between;gap:1rem;margin-bottom:1rem;flex-wrap:wrap;">
                <div><strong>{{ $count }}</strong> item(s) selected</div>
                <div style="display:flex;gap:.6rem;">
                    <button type="button" class="btn btn-outline" data-cart-clear>Clear cart</button>
                    <a class="btn btn-primary" href="{{ route('shop.enquiry.create') }}">Make Enquiry</a>
                </div>
            </div>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                        <tr>
                            <td>
                                <div class="cart-item">
                                    <img src="{{ !empty($item['image']) ? asset('storage/'.$item['image']) : asset('front_assets/img/logo.png') }}" alt="">
                                    <div>
                                        <strong>{{ $item['title'] }}</strong>
                                        @if(!empty($item['variation_label']))
                                            <div style="color:var(--brand-muted);font-size:.9rem;">{{ $item['variation_label'] }}</div>
                                        @endif
                                        @if(!empty($item['sku']))
                                            <div style="color:var(--brand-muted);font-size:.85rem;">SKU: {{ $item['sku'] }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if(isset($item['unit_price']) && $item['unit_price'] !== null)
                                    ₹{{ number_format((float)$item['unit_price'], 2) }}
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                <input class="form-control" style="width:90px;" type="number" min="0" value="{{ $item['qty'] }}" data-cart-qty="{{ $item['key'] }}">
                            </td>
                            <td>
                                <button type="button" class="btn btn-outline" data-cart-remove="{{ $item['key'] }}">Remove</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div style="margin-top:1.25rem;display:flex;justify-content:space-between;gap:1rem;flex-wrap:wrap;">
                <a class="btn btn-outline" href="{{ route('shop.products') }}">Continue shopping</a>
                <a class="btn btn-primary" href="{{ route('shop.enquiry.create') }}">Proceed to Enquiry</a>
            </div>
        @else
            <div class="empty-state">
                <h3>Your enquiry cart is empty</h3>
                <p>Browse products and add items to start an enquiry.</p>
                <a class="btn btn-primary" href="{{ route('shop.products') }}">Browse products</a>
            </div>
        @endif
    </div>
</div>
@endsection
