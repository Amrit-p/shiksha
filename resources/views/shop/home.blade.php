@extends('shop.theme')

@section('content')

<!-- ============================ Hero ============================ -->
<section class="hero">
  <span class="glow-orb glow-orb--pink hero-glow-1"></span>
  <span class="glow-orb glow-orb--blue hero-glow-2"></span>
  <span class="glow-orb glow-orb--gold hero-glow-3"></span>

  <div class="container z-1">
    <div class="row align-items-center g-5">
      <div class="col-lg-6">
        <span class="eyebrow eyebrow-light" data-reveal="down"><i class="bi bi-stars"></i> Lighting &amp; electricals · Est. 2001</span>

        <h1 class="hero-title" data-reveal="up" style="--reveal-delay:80ms">
          Tell us what you<br>
          need. We quote it<br>
          <span class="text-gradient-glow typed-word" data-typed="today|fairly|in writing|in bulk"></span><span class="typed-caret"></span>
        </h1>

        <p class="hero-lead" data-reveal="up" style="--reveal-delay:180ms">
          LED lighting, fans, geysers, MCBs, wires and fittings — supplied to homes,
          contractors and dealers across India. Build an enquiry list and get a written quote,
          usually the same working day.
        </p>

        <div class="hero-cta" data-reveal="up" style="--reveal-delay:260ms">
          <a href="{{ route('shop.products') }}" class="btn btn-brand btn-lg">Browse the catalogue <i class="bi bi-arrow-right ms-1"></i></a>
          <a href="{{ route('shop.cart') }}" class="btn btn-ghost-light btn-lg">Send an enquiry</a>
        </div>

        <div class="hero-stats" data-reveal="up" style="--reveal-delay:340ms">
          <div class="hero-stat">
            <div class="hs-num"><span data-count="{{ $productCount ?? 0 }}">0</span></div>
            <div class="hs-label">Products in the catalogue</div>
          </div>
          <div class="hero-stat">
            <div class="hs-num"><span data-count="{{ $categoryCount ?? 0 }}">0</span></div>
            <div class="hs-label">Product categories</div>
          </div>
          <div class="hero-stat">
            <div class="hs-num"><span data-count="24">0</span><small>h</small></div>
            <div class="hs-label">Typical quote turnaround</div>
          </div>
        </div>
      </div>

      <div class="col-lg-6">
        <div class="hero-visual" data-reveal="zoom" style="--reveal-delay:180ms">
          <span class="hero-ring"></span>
          <img src="{{ asset('theme/img/hero-scene.svg') }}" alt="Cluster of {{ $shopSettings['name'] ?? 'Shiksha' }} LED fixtures" width="760" height="760">

          <div class="float-card float-card--a">
            <i class="bi bi-clipboard-check"></i>
            <div><div class="fc-title">Written Quotes</div><div class="fc-sub">No hidden pricing</div></div>
          </div>
          <div class="float-card float-card--b">
            <i class="bi bi-boxes"></i>
            <div><div class="fc-title">Bulk Slab Rates</div><div class="fc-sub">Project quantities</div></div>
          </div>
          <div class="float-card float-card--c">
            <i class="bi bi-truck"></i>
            <div><div class="fc-title">Pan-India Dispatch</div><div class="fc-sub">Road &amp; courier</div></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ============================ Marquee ============================ -->
<div class="brand-marquee">
  <div class="marquee-track">
    @foreach(range(1,2) as $rep)
    <span class="marquee-item"><i class="bi bi-award"></i> BIS Certified</span>
    <span class="marquee-item"><i class="bi bi-receipt"></i> GST Invoicing</span>
    <span class="marquee-item"><i class="bi bi-lightning-charge"></i> Energy Efficient LEDs</span>
    <span class="marquee-item"><i class="bi bi-hand-thumbs-up"></i> Made in India</span>
    <span class="marquee-item"><i class="bi bi-people"></i> Dealer Enquiries Welcome</span>
    <span class="marquee-item"><i class="bi bi-headset"></i> Technical Support</span>
    @endforeach
  </div>
</div>

<!-- ============================ How it works ============================ -->
<section class="section-sm">
  <div class="container">
    <div class="row g-3">
      <div class="col-12 col-sm-6 col-lg-3" data-reveal="up">
        <div class="perk"><i class="bi bi-clipboard-plus"></i>
          <div><h6>1. Build a list</h6><span>Add products and quantities</span></div>
        </div>
      </div>
      <div class="col-12 col-sm-6 col-lg-3" data-reveal="up" style="--reveal-delay:90ms">
        <div class="perk"><i class="bi bi-send"></i>
          <div><h6>2. Send it over</h6><span>One form, no account needed</span></div>
        </div>
      </div>
      <div class="col-12 col-sm-6 col-lg-3" data-reveal="up" style="--reveal-delay:180ms">
        <div class="perk"><i class="bi bi-receipt"></i>
          <div><h6>3. Get a quote</h6><span>Itemised, within a business day</span></div>
        </div>
      </div>
      <div class="col-12 col-sm-6 col-lg-3" data-reveal="up" style="--reveal-delay:270ms">
        <div class="perk"><i class="bi bi-truck"></i>
          <div><h6>4. We supply</h6><span>Dispatch across India</span></div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ============================ Categories ============================ -->
@if($categories->count())
<section class="section bg-soft" id="categories">
  <div class="container">
    <div class="section-head center" data-reveal="up">
      <span class="eyebrow"><i class="bi bi-grid"></i> Browse by category</span>
      <div class="divider-glow"></div>
      <h2>Everything on the <span class="text-gradient">electrical list</span></h2>
      <p>{{ $productCount ?? 0 }} products across {{ $categoryCount ?? 0 }} categories — find what you need, set a quantity, send the enquiry.</p>
    </div>

    <div class="row g-3 g-lg-4">
      @foreach($categories as $category)
      <div class="col-6 col-md-3" data-reveal="rise" style="--reveal-delay:{{ ($loop->index % 4) * 70 }}ms">
        <a class="cat-tile" href="{{ route('shop.products', ['category' => $category->slug ?? $category->id]) }}">
          <span class="cat-tile-media">
            <img src="{{ $category->image ? asset('storage/'.$category->image) : asset('theme/img/logo.png') }}" alt="{{ $category->name }}" loading="lazy" width="400" height="400">
          </span>
          <span class="cat-tile-body">
            <span class="cat-tile-name">{{ $category->name }}</span>
            <span class="cat-tile-count">{{ $category->products_count ?? 0 }} {{ \Illuminate\Support\Str::plural('product', $category->products_count ?? 0) }}</span>
          </span>
        </a>
      </div>
      @endforeach
    </div>

    <div class="text-center mt-4 mt-lg-5" data-reveal="up">
      <a href="{{ route('shop.products') }}" class="btn btn-brand btn-lg" data-magnetic="0.2">
        Browse all {{ $productCount ?? 0 }} products <i class="bi bi-arrow-right ms-1"></i>
      </a>
    </div>
  </div>
</section>
@endif

<!-- ============================ Featured products ============================ -->
@if($featured->count())
<section class="section" id="featured">
  <div class="container">
    <div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4 mb-lg-5">
      <div class="section-head mb-0" data-reveal="left">
        <span class="eyebrow"><i class="bi bi-star-fill"></i> From the catalogue</span>
        <div class="divider-glow"></div>
        <h2 class="mb-0">Featured <span class="text-gradient">products</span></h2>
      </div>
      <a href="{{ route('shop.products') }}" class="btn btn-outline-brand" data-reveal="right">
        View all products <i class="bi bi-arrow-right ms-1"></i>
      </a>
    </div>

    <div class="row g-3 g-lg-4">
      @foreach($featured as $product)
        <x-shop.product-card :product="$product" :index="$loop->index" />
      @endforeach
    </div>
  </div>
</section>
@endif

<!-- ============================ Showcase split ============================ -->
<section class="section bg-soft overflow-hidden">
  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-lg-6" data-reveal="left">
        <div class="split-media-stack">
          <div class="sm-back showcase-media" data-tilt data-tilt-max="5">
            <img src="{{ asset('theme/img/showcase-living.svg') }}" alt="Interior lit with {{ $shopSettings['name'] ?? 'Shiksha' }} LED fixtures" width="900" height="640">
          </div>
          <div class="sm-front d-none d-md-block">
            <img src="{{ asset('theme/img/showcase-studio.svg') }}" alt="{{ $shopSettings['name'] ?? 'Shiksha' }} warehouse and counter" width="900" height="640">
          </div>
        </div>
      </div>

      <div class="col-lg-6 ps-lg-5" data-reveal="right">
        <span class="eyebrow"><i class="bi bi-shop"></i> A supplier, not a storefront</span>
        <div class="divider-glow"></div>
        <h2>Why we quote instead of <span class="text-gradient">listing prices</span></h2>
        <p>
          Electrical pricing moves with quantity, specification, brand and current stock. A fixed
          list price would either overcharge the small buyer or offer nothing to the
          contractor buying two hundred pieces.
        </p>

        <ul class="check-list my-4">
          <li><i class="bi bi-check-lg"></i><span><strong class="text-ink">You get the real number</strong> — priced against your actual quantity, not a shelf rate.</span></li>
          <li><i class="bi bi-check-lg"></i><span><strong class="text-ink">Specification confirmed first</strong> — wattage, finish and variant checked before you commit.</span></li>
          <li><i class="bi bi-check-lg"></i><span><strong class="text-ink">Stock checked live</strong> — we tell you what ships now and what needs scheduling.</span></li>
          <li><i class="bi bi-check-lg"></i><span><strong class="text-ink">Dealer and project terms</strong> quoted openly, with GST invoicing on everything.</span></li>
        </ul>

        <div class="d-flex flex-wrap gap-2">
          <a href="{{ route('shop.about') }}" class="btn btn-brand">About {{ $shopSettings['name'] ?? 'Shiksha' }} <i class="bi bi-arrow-right ms-1"></i></a>
          <a href="{{ route('shop.contact') }}" class="btn btn-outline-brand">Talk to the sales desk</a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ============================ Newsletter ============================ -->
<section class="section-sm pb-5">
  <div class="container">
    <div class="newsletter-wrap" data-reveal="zoom">
      <div class="row align-items-center g-4 position-relative" style="z-index:2">
        <div class="col-lg-6">
          <span class="eyebrow eyebrow-light"><i class="bi bi-envelope-paper"></i> Trade updates</span>
          <h2 class="mb-2">New lines and price lists</h2>
          <p class="mb-0">Get new catalogue additions, revised rate cards and stock updates by email. One email a fortnight, no spam.</p>
        </div>
        <div class="col-lg-6">
          <form class="newsletter-form" data-newsletter novalidate>
            <input type="email" placeholder="you@example.com" aria-label="Email address" required>
            <button type="submit" class="btn btn-glow">Subscribe <i class="bi bi-arrow-right ms-1"></i></button>
          </form>
          <p class="small mb-0 mt-3 opacity-75"><i class="bi bi-shield-lock me-1"></i>We never share your address. Unsubscribe any time.</p>
        </div>
      </div>
    </div>
  </div>
</section>

@endsection
