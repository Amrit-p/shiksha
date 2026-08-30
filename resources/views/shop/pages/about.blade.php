@extends('shop.theme')

@section('content')
@php $brand = $shopSettings['name'] ?? 'Shiksha'; @endphp

<!-- ============================ Page hero ============================ -->
<section class="page-hero">
  <span class="glow-orb glow-orb--pink" style="width:420px;height:420px;top:-140px;left:-100px"></span>
  <span class="glow-orb glow-orb--blue" style="width:400px;height:400px;bottom:-180px;right:-90px"></span>
  <div class="container z-1">
    <span class="eyebrow eyebrow-light" data-reveal="down"><i class="bi bi-buildings"></i> {{ $section?->subtitle ?? 'Since 2001' }}</span>
    <h1 data-reveal="up">{!! $section?->title ?? 'Lighting &amp; electricals,<br><span class="text-gradient-glow">supplied right</span>' !!}</h1>
    <p data-reveal="up" style="--reveal-delay:90ms">
      {{ $section?->content ? \Illuminate\Support\Str::limit(strip_tags($section->content), 180) : 'A supplier of LED lighting, fans, geysers and electrical fittings to homes, contractors and dealers across India — enquiry-based pricing, written quotes and pan-India dispatch.' }}
    </p>
    <div class="breadcrumb-pill" data-reveal="up" style="--reveal-delay:170ms">
      <a href="{{ route('shop.home') }}">Home</a><span class="sep">/</span><span class="current">About Us</span>
    </div>
  </div>
</section>

<!-- ============================ Story ============================ -->
<section class="section" id="story">
  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-lg-6" data-reveal="left">
        <div class="split-media-stack">
          <div class="sm-back showcase-media" data-tilt data-tilt-max="5">
            <img src="{{ asset('theme/img/showcase-studio.svg') }}" alt="{{ $brand }} counter and warehouse" width="900" height="640">
          </div>
          <div class="sm-front d-none d-md-block">
            <img src="{{ asset('theme/img/showcase-living.svg') }}" alt="A space lit by {{ $brand }} fixtures" width="900" height="640">
          </div>
        </div>
      </div>

      <div class="col-lg-6 ps-lg-5" data-reveal="right">
        <span class="eyebrow"><i class="bi bi-book"></i> Our story</span>
        <div class="divider-glow"></div>
        <h2>{{ $section?->title ?? 'A supplier, not a storefront' }}</h2>
        @if($section && $section->content)
          <div class="pd-description mb-4">{!! nl2br(e($section->content)) !!}</div>
        @else
          <p>{{ $brand }} supplies LED lighting, fans, geysers, MCBs, wires and fittings to homes, builders, contractors and dealers across India. We stock a wide catalogue and quote against your exact requirement.</p>
          <p class="mb-4">Electrical pricing moves with quantity, specification, brand and current stock — so instead of a fixed list price, we give you a written quote itemised against what you actually need, usually the same working day.</p>
        @endif
        <div class="d-flex flex-wrap gap-2">
          <a href="{{ route('shop.products') }}" class="btn btn-brand">Browse the catalogue <i class="bi bi-arrow-right ms-1"></i></a>
          <a href="{{ route('shop.contact') }}" class="btn btn-outline-brand">Talk to the sales desk</a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ============================ Stats ============================ -->
<section class="section-sm">
  <div class="container">
    <div class="row g-3 g-lg-4">
      <div class="col-6 col-lg-3" data-reveal="zoom">
        <div class="stat-tile light"><i class="bi bi-box-seam"></i>
          <div class="stat-num"><span data-count="296">0</span><small>+</small></div>
          <div class="stat-label">Products supplied</div>
        </div>
      </div>
      <div class="col-6 col-lg-3" data-reveal="zoom" style="--reveal-delay:90ms">
        <div class="stat-tile light"><i class="bi bi-grid-3x3-gap"></i>
          <div class="stat-num"><span data-count="17">0</span></div>
          <div class="stat-label">Product categories</div>
        </div>
      </div>
      <div class="col-6 col-lg-3" data-reveal="zoom" style="--reveal-delay:180ms">
        <div class="stat-tile light"><i class="bi bi-geo-alt"></i>
          <div class="stat-num"><span data-count="40">0</span><small>+</small></div>
          <div class="stat-label">Cities served</div>
        </div>
      </div>
      <div class="col-6 col-lg-3" data-reveal="zoom" style="--reveal-delay:270ms">
        <div class="stat-tile light"><i class="bi bi-clock-history"></i>
          <div class="stat-num"><span data-count="24">0</span><small>h</small></div>
          <div class="stat-label">Quote turnaround</div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ============================ Why quote ============================ -->
<section class="section bg-soft">
  <div class="container">
    <div class="section-head center" data-reveal="up">
      <span class="eyebrow"><i class="bi bi-patch-check"></i> How we work</span>
      <div class="divider-glow"></div>
      <h2>What you get with <span class="text-gradient">every quote</span></h2>
    </div>
    <div class="row g-4">
      <div class="col-md-6 col-lg-3" data-reveal="rise">
        <div class="feature-card h-100"><div class="feature-ico"><i class="bi bi-clipboard-check"></i></div>
          <h5>Quotes in writing</h5><p>Every enquiry comes back itemised — product, specification, quantity, rate and GST.</p></div>
      </div>
      <div class="col-md-6 col-lg-3" data-reveal="rise" style="--reveal-delay:90ms">
        <div class="feature-card h-100"><div class="feature-ico feature-ico--blue"><i class="bi bi-boxes"></i></div>
          <h5>Bulk &amp; project rates</h5><p>Slab pricing that improves with quantity, with delivery scheduled to your programme.</p></div>
      </div>
      <div class="col-md-6 col-lg-3" data-reveal="rise" style="--reveal-delay:180ms">
        <div class="feature-card h-100"><div class="feature-ico feature-ico--gold"><i class="bi bi-headset"></i></div>
          <h5>Technical guidance</h5><p>Tell us the application and we help pick wattage, rating or cable size before you order.</p></div>
      </div>
      <div class="col-md-6 col-lg-3" data-reveal="rise" style="--reveal-delay:270ms">
        <div class="feature-card h-100"><div class="feature-ico"><i class="bi bi-truck"></i></div>
          <h5>Dispatch across India</h5><p>Road freight and courier, packed for transit, with freight stated up front.</p></div>
      </div>
    </div>
  </div>
</section>

<!-- ============================ CTA ============================ -->
<section class="section">
  <div class="container">
    <div class="newsletter-wrap text-center" data-reveal="zoom">
      <div class="position-relative" style="z-index:2">
        <span class="eyebrow eyebrow-light"><i class="bi bi-clipboard-plus"></i> Ready when you are</span>
        <h2 class="mb-3">Build a list. Get a quote.</h2>
        <p class="mb-4 mx-auto" style="max-width:580px">
          Add the products and quantities you need, send them in one go, and our sales desk replies with
          pricing, availability and delivery — usually the same working day.
        </p>
        <div class="d-flex flex-wrap justify-content-center gap-2">
          <a href="{{ route('shop.products') }}" class="btn btn-glow btn-lg" data-magnetic="0.22">Browse products <i class="bi bi-arrow-right ms-1"></i></a>
          <a href="{{ route('shop.contact') }}" class="btn btn-ghost-light btn-lg">Contact us</a>
        </div>
      </div>
    </div>
  </div>
</section>

@endsection
