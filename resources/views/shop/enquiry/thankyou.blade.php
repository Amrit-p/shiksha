@extends('shop.layout')

@section('content')
<div class="page-wrap" style="padding:4rem 0;text-align:center;">
    <div class="panel" style="padding:2.5rem;max-width:640px;margin:0 auto;">
        <div class="chip" style="margin:0 auto 1rem;">Enquiry received</div>
        <h1 style="font-family:var(--font-display);margin:0 0 .75rem;">Thank you!</h1>
        <p style="color:var(--brand-muted);line-height:1.7;">
            Your enquiry <strong>{{ $enquiryNumber }}</strong> has been submitted successfully.
            Our team will contact you shortly.
        </p>
        <div style="display:flex;gap:.75rem;justify-content:center;flex-wrap:wrap;margin-top:1.5rem;">
            <a class="btn btn-primary" href="{{ route('shop.products') }}">Continue browsing</a>
            <a class="btn btn-outline" href="{{ route('shop.home') }}">Back to home</a>
        </div>
    </div>
</div>
@endsection
