@extends('shop.theme')

@section('content')

<section class="page-hero">
  <span class="glow-orb glow-orb--pink" style="width:420px;height:420px;top:-140px;left:-100px"></span>
  <span class="glow-orb glow-orb--blue" style="width:400px;height:400px;bottom:-180px;right:-90px"></span>
  <div class="container z-1 text-center">
    <span class="eyebrow eyebrow-light" data-reveal="down"><i class="bi bi-check-circle"></i> Enquiry received</span>
    <h1 data-reveal="up">Thank you</h1>
  </div>
</section>

<section class="section pt-0">
  <div class="container">
    <div class="summary-card mx-auto text-center" style="max-width:640px" data-reveal="zoom">
      <div class="mx-auto mb-4" style="width:96px;height:96px;border-radius:50%;display:grid;place-items:center;background:var(--sl-grad);color:#fff">
        <i class="bi bi-clipboard-check" style="font-size:2.6rem"></i>
      </div>
      <h2 class="mb-2">Your enquiry is in</h2>
      <p class="text-muted-2 mb-3">
        Reference <strong class="text-ink">{{ $enquiryNumber }}</strong> has been submitted successfully.
        Our sales desk will reply with pricing and availability — usually within one business day.
      </p>
      <div class="d-flex flex-wrap gap-2 justify-content-center mt-4">
        <a class="btn btn-brand" href="{{ route('shop.products') }}">Continue browsing <i class="bi bi-arrow-right ms-1"></i></a>
        <a class="btn btn-outline-brand" href="{{ route('shop.home') }}">Back to home</a>
      </div>
    </div>
  </div>
</section>

@endsection
