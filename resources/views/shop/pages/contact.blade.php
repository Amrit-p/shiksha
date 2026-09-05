@extends('shop.theme')

@section('content')
@php
  $brand = $shopSettings['name'] ?? 'Shiksha';
  $phone = $shopSettings['phone'] ?? null;
  $phoneHref = $phone ? preg_replace('/[^0-9+]/', '', $phone) : null;
  $email = $shopSettings['email'] ?? null;
@endphp

<!-- ============================ Page hero ============================ -->
<section class="page-hero">
  <span class="glow-orb glow-orb--pink" style="width:420px;height:420px;top:-140px;left:-100px"></span>
  <span class="glow-orb glow-orb--blue" style="width:400px;height:400px;bottom:-180px;right:-90px"></span>
  <div class="container z-1">
    <span class="eyebrow eyebrow-light" data-reveal="down"><i class="bi bi-chat-dots"></i> We reply in one business day</span>
    <h1 data-reveal="up">Let's <span class="text-gradient-glow">talk</span></h1>
    <p data-reveal="up" style="--reveal-delay:90ms">
      Product questions, a trade enquiry or a bulk order — the same team answers all of it.
    </p>
    <div class="breadcrumb-pill" data-reveal="up" style="--reveal-delay:170ms">
      <a href="{{ route('shop.home') }}">Home</a><span class="sep">/</span><span class="current">Contact</span>
    </div>
  </div>
</section>

<!-- ============================ Contact cards ============================ -->
<section class="section">
  <div class="container">
    <div class="row g-4 justify-content-center">
      @if($phone)
      <div class="col-md-6 col-lg-4" data-reveal="rise">
        <div class="contact-card">
          <div class="contact-ico"><i class="bi bi-telephone"></i></div>
          <h5>Call us</h5>
          <p class="small mb-2">Mon–Sat, 10am – 7pm IST</p>
          <a href="tel:{{ $phoneHref }}" class="link-underline-anim">{{ $phone }}</a>
        </div>
      </div>
      @endif
      @if($email)
      <div class="col-md-6 col-lg-4" data-reveal="rise" style="--reveal-delay:90ms">
        <div class="contact-card">
          <div class="contact-ico"><i class="bi bi-envelope"></i></div>
          <h5>Email us</h5>
          <p class="small mb-2">One business day turnaround</p>
          <a href="mailto:{{ $email }}" class="link-underline-anim">{{ $email }}</a>
        </div>
      </div>
      @endif
    </div>
  </div>
</section>

<!-- ============================ Form + info ============================ -->
<section class="section pt-0">
  <div class="container">
    <div class="row g-4 g-xl-5 justify-content-center">

      <div class="col-lg-8 col-xl-7" data-reveal="up">
        <div class="p-4 p-lg-5 rounded-2xl" style="background:#fff;border:1px solid var(--sl-line);box-shadow:var(--sl-shadow-sm)">
          <span class="eyebrow"><i class="bi bi-send"></i> Send a message</span>
          <h2 class="mb-2">How can we help?</h2>
          <p class="mb-3">Fill this in and the right person will reply — not a ticket queue.</p>
          <p class="small text-muted-2 mb-4">
            <i class="bi bi-clipboard-check me-1"></i>Quoting several products?
            <a href="{{ route('shop.products') }}" class="link-underline-anim">Build an enquiry list</a> instead — it keeps
            the model codes and quantities together so we can price it in one go.
          </p>

          @if($errors->any())
            <div class="alert alert-danger">
              <ul class="mb-0 ps-3">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
          @endif

          <form method="POST" action="{{ route('shop.contact.submit') }}">
            @csrf
            <div class="row g-3">
              <div class="col-md-6">
                <div class="form-floating-pill mb-0">
                  <label for="cName">Full name <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="cName" name="name" value="{{ old('name') }}" placeholder="Your name" required minlength="2">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-floating-pill mb-0">
                  <label for="cEmail">Email address <span class="text-danger">*</span></label>
                  <input type="email" class="form-control" id="cEmail" name="email" value="{{ old('email') }}" placeholder="you@example.com" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-floating-pill mb-0">
                  <label for="cPhone">Phone</label>
                  <input type="tel" class="form-control" id="cPhone" name="phone" value="{{ old('phone') }}" placeholder="+91 98765 43210">
                </div>
              </div>
              <div class="col-12">
                <div class="form-floating-pill mb-0">
                  <label for="cMessage">Your message <span class="text-danger">*</span></label>
                  <textarea class="form-control" id="cMessage" name="message" rows="5" placeholder="Tell us what you need, quantities, timelines…" required minlength="12">{{ old('message') }}</textarea>
                </div>
              </div>
              <div class="col-12">
                <label class="filter-check">
                  <input type="checkbox" required>
                  <span class="small">I agree to {{ $brand }} contacting me about this enquiry. <span class="text-danger">*</span></span>
                </label>
              </div>
              <div class="col-12">
                <button type="submit" class="btn btn-brand btn-lg">Send message <i class="bi bi-arrow-right ms-1"></i></button>
              </div>
            </div>
          </form>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- ============================ FAQ ============================ -->
<section class="section bg-soft">
  <div class="container">
    <div class="section-head center" data-reveal="up">
      <span class="eyebrow"><i class="bi bi-question-circle"></i> Frequently asked</span>
      <div class="divider-glow"></div>
      <h2>Answers before you <span class="text-gradient">ask</span></h2>
    </div>

    <div class="row justify-content-center">
      <div class="col-lg-9">
        <div class="accordion accordion-soft" id="faqAccordion" data-reveal="up">
          <div class="accordion-item">
            <h2 class="accordion-header">
              <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">Why are there no prices on the website?</button>
            </h2>
            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
              <div class="accordion-body">Electrical pricing moves with quantity, specification, brand and current stock. Send an enquiry with your quantities and you get the real number, itemised and in writing.</div>
            </div>
          </div>
          <div class="accordion-item">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">How quickly will I get a quote?</button>
            </h2>
            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
              <div class="accordion-body">Usually within one business day, often the same afternoon. Long lists or variants we confirm with the factory can take a day longer — we tell you if that is the case.</div>
            </div>
          </div>
          <div class="accordion-item">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">Do you offer trade &amp; project pricing?</button>
            </h2>
            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
              <div class="accordion-body">Yes — slab rates for contractors, dealers and distributors, with GST invoicing and delivery scheduled to your programme. Mention your requirement in the enquiry.</div>
            </div>
          </div>
          <div class="accordion-item">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">Do you dispatch across India?</button>
            </h2>
            <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
              <div class="accordion-body">Yes — road freight and courier, packed for transit, with freight costs stated up front in the quotation.</div>
            </div>
          </div>
        </div>

        @if($phone)
        <div class="text-center mt-4" data-reveal="up">
          <p class="mb-2">Still stuck?</p>
          <a href="tel:{{ $phoneHref }}" class="btn btn-brand" data-magnetic="0.2"><i class="bi bi-telephone me-1"></i> Call the team</a>
        </div>
        @endif
      </div>
    </div>
  </div>
</section>

@endsection
