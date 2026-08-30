@extends('shop.layout')

@section('content')
<div class="page-wrap page-hero">
    <h1>Make Enquiry</h1>
    <p>Share your details and our team will contact you about the selected products.</p>
</div>

<div class="page-wrap" style="padding-bottom:3rem;display:grid;grid-template-columns:1.1fr .9fr;gap:1.25rem;">
    <form class="panel" style="padding:1.25rem;" method="POST" action="{{ route('shop.enquiry.store') }}">
        @csrf
        @if($errors->any())
            <div class="alert alert-error">
                <ul style="margin:0;padding-left:1.1rem;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="form-grid">
            <div>
                <label>Name *</label>
                <input class="form-control" type="text" name="name" value="{{ old('name') }}" required>
            </div>
            <div>
                <label>Email *</label>
                <input class="form-control" type="email" name="email" value="{{ old('email') }}" required>
            </div>
            <div>
                <label>Phone *</label>
                <input class="form-control" type="text" name="phone" value="{{ old('phone') }}" required>
            </div>
            <div>
                <label>Company</label>
                <input class="form-control" type="text" name="company" value="{{ old('company') }}">
            </div>
            <div class="full">
                <label>Address</label>
                <input class="form-control" type="text" name="address" value="{{ old('address') }}">
            </div>
            <div>
                <label>City</label>
                <input class="form-control" type="text" name="city" value="{{ old('city') }}">
            </div>
            <div>
                <label>State</label>
                <input class="form-control" type="text" name="state" value="{{ old('state') }}">
            </div>
            <div>
                <label>PIN Code</label>
                <input class="form-control" type="text" name="pin_code" value="{{ old('pin_code') }}">
            </div>
            <div class="full">
                <label>Message / Notes</label>
                <textarea class="form-control" name="message" rows="5">{{ old('message') }}</textarea>
            </div>
        </div>
        <div style="margin-top:1.25rem;">
            <button class="btn btn-primary" type="submit">Submit Enquiry</button>
        </div>
    </form>

    <aside class="panel" style="padding:1.25rem;">
        <h3 style="margin-top:0;font-family:var(--font-display);">Selected Products</h3>
        @foreach($items as $item)
            <div style="display:flex;gap:.75rem;padding:.85rem 0;border-bottom:1px solid var(--brand-line);">
                <img src="{{ !empty($item['image']) ? asset('storage/'.$item['image']) : asset('front_assets/img/logo.png') }}" alt="" style="width:64px;height:64px;object-fit:cover;border-radius:12px;">
                <div>
                    <strong>{{ $item['title'] }}</strong>
                    <div style="color:var(--brand-muted);font-size:.9rem;">Qty: {{ $item['qty'] }}</div>
                    @if(!empty($item['variation_label']))
                        <div style="color:var(--brand-muted);font-size:.85rem;">{{ $item['variation_label'] }}</div>
                    @endif
                </div>
            </div>
        @endforeach
        <a class="btn btn-outline" style="margin-top:1rem;width:100%;" href="{{ route('shop.cart') }}">Edit cart</a>
    </aside>
</div>
@endsection
