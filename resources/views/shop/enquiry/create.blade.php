@extends('shop.theme')

@section('content')

<!-- ============================ Page hero ============================ -->
<section class="page-hero">
  <span class="glow-orb glow-orb--pink" style="width:400px;height:400px;top:-140px;left:-100px"></span>
  <span class="glow-orb glow-orb--blue" style="width:380px;height:380px;bottom:-170px;right:-80px"></span>
  <div class="container z-1">
    <span class="eyebrow eyebrow-light" data-reveal="down"><i class="bi bi-clipboard-check"></i> Almost there</span>
    <h1 data-reveal="up">Send your <span class="text-gradient-glow">enquiry</span></h1>
    <p data-reveal="up" style="--reveal-delay:90ms">
      Add your details and our sales desk comes back with pricing, availability and delivery —
      usually within one business day.
    </p>
    <div class="breadcrumb-pill" data-reveal="up" style="--reveal-delay:170ms">
      <a href="{{ route('shop.home') }}">Home</a><span class="sep">/</span>
      <a href="{{ route('shop.cart') }}">Cart</a><span class="sep">/</span><span class="current">Enquiry</span>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">

    <div class="step-line" data-reveal="up">
      <div class="step-node is-active"><span class="sn-dot">1</span><span class="sn-label">Build list</span></div>
      <span class="step-bar"></span>
      <div class="step-node is-active"><span class="sn-dot">2</span><span class="sn-label">Your details</span></div>
      <span class="step-bar"></span>
      <div class="step-node"><span class="sn-dot">3</span><span class="sn-label">We quote</span></div>
      <span class="step-bar"></span>
      <div class="step-node"><span class="sn-dot">4</span><span class="sn-label">Supply</span></div>
    </div>

    <div class="row g-4 g-xl-5">
      <!-- Form -->
      <div class="col-lg-7">
        <div class="summary-card" data-reveal="left">
          <h5 class="mb-1">Your details</h5>
          <p class="small text-muted-2 mb-4">Fields marked <span class="text-danger">*</span> are required.</p>

          @if($errors->any())
            <div class="alert alert-danger">
              <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
              </ul>
            </div>
          @endif

          <form method="POST" action="{{ route('shop.enquiry.store') }}">
            @csrf
            <div class="row g-3">
              <div class="col-md-6">
                <div class="form-floating-pill mb-0">
                  <label for="enqName">Full name <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="enqName" name="name" value="{{ old('name') }}" placeholder="Your name" required minlength="2">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-floating-pill mb-0">
                  <label for="enqCompany">Company / firm</label>
                  <input type="text" class="form-control" id="enqCompany" name="company" value="{{ old('company') }}" placeholder="Optional">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-floating-pill mb-0">
                  <label for="enqPhone">Phone / WhatsApp <span class="text-danger">*</span></label>
                  <input type="tel" class="form-control" id="enqPhone" name="phone" value="{{ old('phone') }}" placeholder="+91 98765 43210" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-floating-pill mb-0">
                  <label for="enqEmail">Email address <span class="text-danger">*</span></label>
                  <input type="email" class="form-control" id="enqEmail" name="email" value="{{ old('email') }}" placeholder="you@example.com" required>
                </div>
              </div>
              <div class="col-12">
                <div class="form-floating-pill mb-0">
                  <label for="enqAddress">Address</label>
                  <input type="text" class="form-control" id="enqAddress" name="address" value="{{ old('address') }}" placeholder="Street / building">
                </div>
              </div>
              <div class="col-md-5">
                <div class="form-floating-pill mb-0">
                  <label for="enqCity">City</label>
                  <input type="text" class="form-control" id="enqCity" name="city" value="{{ old('city') }}" placeholder="City">
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-floating-pill mb-0">
                  <label for="enqState">State</label>
                  <input type="text" class="form-control" id="enqState" name="state" value="{{ old('state') }}" placeholder="State">
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-floating-pill mb-0">
                  <label for="enqPin">PIN code</label>
                  <input type="text" class="form-control" id="enqPin" name="pin_code" value="{{ old('pin_code') }}" placeholder="302022">
                </div>
              </div>
              <div class="col-12">
                <div class="form-floating-pill mb-0">
                  <label for="enqNotes">Anything else we should know?</label>
                  <textarea class="form-control" id="enqNotes" name="message" rows="4" placeholder="Timelines, site details, wattage or finish preferences…">{{ old('message') }}</textarea>
                </div>
              </div>
              <div class="col-12">
                <label class="filter-check mb-2">
                  <input type="checkbox" required>
                  <span class="small">I agree to {{ $shopSettings['name'] ?? 'Shiksha' }} contacting me about this enquiry. <span class="text-danger">*</span></span>
                </label>
              </div>
              <div class="col-12">
                <button type="submit" class="btn btn-brand btn-lg w-100">Send enquiry <i class="bi bi-send ms-1"></i></button>
                <div class="d-flex flex-wrap justify-content-center gap-3 mt-3 small text-muted-2">
                  <span><i class="bi bi-clock-history me-1"></i>Reply in 1 business day</span>
                  <span><i class="bi bi-shield-lock me-1"></i>No obligation</span>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>

      <!-- Summary -->
      <div class="col-lg-5">
        <div class="cart-table-wrap" data-reveal="right">
          <div class="d-flex align-items-center justify-content-between px-4 py-3" style="border-bottom:1px solid var(--sl-line);background:var(--sl-paper-2)">
            <h5 class="mb-0"><i class="bi bi-bag-check me-2"></i>Your list</h5>
            <a href="{{ route('shop.cart') }}" class="btn btn-sm p-0 border-0 bg-transparent small text-muted-2"><i class="bi bi-pencil me-1"></i>Edit</a>
          </div>
          @foreach($items as $item)
            <div class="cart-line">
              <img src="{{ !empty($item['image']) ? asset('storage/'.$item['image']) : asset('theme/img/logo.png') }}" alt="{{ $item['title'] }}">
              <div class="flex-grow-1">
                <h6 class="mb-1">{{ $item['title'] }}</h6>
                @if(!empty($item['variation_label']))<div class="small text-muted-2">{{ $item['variation_label'] }}</div>@endif
                <div class="small text-muted-2">Qty: {{ $item['qty'] }}</div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </div>

  </div>
</section>

@endsection
