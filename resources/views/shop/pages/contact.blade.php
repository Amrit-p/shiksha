@extends('shop.layout')

@section('content')
<div class="page-wrap page-hero">
    <h1>Contact Us</h1>
    <p>Send a message or start a product enquiry.</p>
</div>
<div class="page-wrap" style="padding-bottom:3rem;display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;">
    <form class="panel" style="padding:1.25rem;" method="POST" action="{{ route('shop.contact.submit') }}">
        @csrf
        <div class="form-grid">
            <div class="full">
                <label>Name *</label>
                <input class="form-control" type="text" name="name" value="{{ old('name') }}" required>
            </div>
            <div>
                <label>Email *</label>
                <input class="form-control" type="email" name="email" value="{{ old('email') }}" required>
            </div>
            <div>
                <label>Phone</label>
                <input class="form-control" type="text" name="phone" value="{{ old('phone') }}">
            </div>
            <div class="full">
                <label>Message *</label>
                <textarea class="form-control" name="message" rows="5" required>{{ old('message') }}</textarea>
            </div>
        </div>
        <button class="btn btn-primary" style="margin-top:1rem;" type="submit">Send Message</button>
    </form>
    <div class="panel" style="padding:1.25rem;">
        <h3 style="font-family:var(--font-display);margin-top:0;">Get in touch</h3>
        @if(!empty($settings['phone']))<p><strong>Phone:</strong> {{ $settings['phone'] }}</p>@endif
        @if(!empty($settings['email']))<p><strong>Email:</strong> {{ $settings['email'] }}</p>@endif
        @if(!empty($settings['address']))<p><strong>Address:</strong> {{ $settings['address'] }}</p>@endif
        <a class="btn btn-secondary" href="{{ route('shop.products') }}">Browse products</a>
    </div>
</div>
@endsection
