@extends('shop.theme')

@section('content')
@php
  $images = collect([$product->image])->merge($product->gallary_images->pluck('image'))->filter()->unique()->values();
  $mainImage = $images->first() ? asset('storage/'.$images->first()) : asset('theme/img/logo.png');
  $price = $product->sale_price ?: $product->price;
  $hasPrice = $price !== null && $price !== '' && (float) $price > 0;
  $inStock = $product->stock === null || $product->stock > 0;
@endphp

<!-- ============================ Breadcrumb strip ============================ -->
<section class="page-hero" style="padding-bottom:clamp(34px,4vw,56px)">
  <span class="glow-orb glow-orb--pink" style="width:380px;height:380px;top:-140px;left:-90px"></span>
  <span class="glow-orb glow-orb--blue" style="width:360px;height:360px;bottom:-170px;right:-80px"></span>
  <div class="container z-1">
    <h1 class="h2 mb-0">{{ $product->title }}</h1>
    <div class="breadcrumb-pill" style="margin-top:1rem">
      <a href="{{ route('shop.home') }}">Home</a><span class="sep">/</span>
      <a href="{{ route('shop.products') }}">Products</a><span class="sep">/</span>
      <span class="current">{{ $product->category->name ?? 'Product' }}</span>
    </div>
  </div>
</section>

<!-- ============================ Product detail ============================ -->
<section class="section" id="productDetail">
  <div class="container">
    <div class="row g-4 g-xl-5">

      <!-- Gallery -->
      <div class="col-lg-6" data-reveal="left">
        <div class="pd-gallery-main" id="pdMain">
          <img data-gallery-main src="{{ $mainImage }}" alt="{{ $product->title }}">
        </div>
        @if($images->count() > 1)
          <div class="d-flex flex-wrap gap-2 mt-3">
            @foreach($images as $i => $image)
              <img data-thumb src="{{ asset('storage/'.$image) }}" alt="{{ $product->title }}"
                   class="{{ $i === 0 ? 'active' : '' }}"
                   style="width:76px;height:76px;object-fit:cover;border-radius:14px;border:2px solid var(--sl-line);cursor:pointer;background:#fff">
            @endforeach
          </div>
        @endif
      </div>

      <!-- Info -->
      <div class="col-lg-6" data-reveal="right">
        <div class="product-cat">{{ $product->category->name ?? 'Product' }}</div>
        <h2 class="mb-2">{{ $product->title }}</h2>

        <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
          <span class="chip-static {{ $inStock ? '' : 'text-danger' }}">
            <i class="bi {{ $inStock ? 'bi-check-circle' : 'bi-x-circle' }} me-1"></i>{{ $inStock ? 'Available' : 'Out of stock' }}
          </span>
          @if($product->unit)<span class="chip-static"><i class="bi bi-rulers me-1"></i>Unit: {{ $product->unit->name }}</span>@endif
          @if($product->wattage)<span class="chip-static"><i class="bi bi-lightning-charge me-1"></i>{{ $product->wattage }}</span>@endif
        </div>

        @if($hasPrice)
          <div class="mb-3"><span class="h3 mb-0">₹{{ number_format((float) $price, 2) }}</span></div>
        @else
          <span class="product-enquire mb-3 d-inline-flex"><i class="bi bi-tag me-1"></i>Enquire for price</span>
        @endif

        <p class="lead-lg">
          {{ $product->short_description ?: 'Pricing on this item depends on quantity, specification and current stock. Add it to your enquiry cart with the quantity you need and we will send a written quote — usually the same working day.' }}
        </p>

        @if($product->variants->count())
          <div class="mb-3">
            <strong class="d-block mb-2">Choose a variation</strong>
            <div class="d-grid gap-2">
              @foreach($product->variants as $variant)
                <div class="d-flex align-items-center justify-content-between gap-2 p-2 ps-3 rounded-xl" style="background:var(--sl-paper-2);border:1px solid var(--sl-line)">
                  <span class="small">
                    {{ $variant->variantname->name ?? $variant->name ?? 'Variant' }}
                    @if($variant->sku)<span class="text-muted-2"> · {{ $variant->sku }}</span>@endif
                  </span>
                  <button type="button" class="btn btn-outline-brand btn-sm"
                          data-add-to-cart="{{ route('shop.cart.add') }}"
                          data-product-id="{{ $product->id }}"
                          data-variant-id="{{ $variant->id }}"
                          data-qty="1">
                    <i class="bi bi-plus-lg"></i> Add
                  </button>
                </div>
              @endforeach
            </div>
          </div>
        @endif

        <div class="d-flex flex-wrap align-items-center gap-3 mb-3">
          <div class="qty-stepper">
            <button type="button" data-qty-minus aria-label="Decrease quantity"><i class="bi bi-dash-lg"></i></button>
            <input type="number" id="pdQty" value="1" min="1" max="999" aria-label="Quantity" data-qty-input>
            <button type="button" data-qty-plus aria-label="Increase quantity"><i class="bi bi-plus-lg"></i></button>
          </div>
          <button type="button" class="btn btn-brand btn-lg flex-grow-1"
                  data-add-to-cart="{{ route('shop.cart.add') }}"
                  data-product-id="{{ $product->id }}">
            <i class="bi bi-clipboard-plus me-1"></i> Add to enquiry cart
          </button>
        </div>

        <a href="{{ route('shop.cart') }}" class="btn btn-dark-pill btn-lg w-100 mb-3">
          Go to enquiry cart <i class="bi bi-arrow-right ms-1"></i>
        </a>

        <ul class="list-unstyled small text-muted-2 mb-0">
          @if($product->slug)<li class="mb-1"><i class="bi bi-upc-scan me-2"></i>Reference: <span class="text-ink">{{ $product->slug }}</span></li>@endif
          <li class="mb-1"><i class="bi bi-tag me-2"></i>Category:
            <a href="{{ route('shop.products', ['category' => $product->category->slug ?? $product->category_id]) }}" class="link-underline-anim">{{ $product->category->name ?? 'Product' }}</a>
          </li>
          <li><i class="bi bi-box-seam me-2"></i>Stocked lines dispatch in 24&ndash;48 hours; project quantities to schedule</li>
        </ul>

        <div class="pd-assure">
          <div><i class="bi bi-clipboard-check"></i> Written quote</div>
          <div><i class="bi bi-boxes"></i> Bulk rates</div>
          <div><i class="bi bi-truck"></i> Pan-India dispatch</div>
          <div><i class="bi bi-headset"></i> Technical support</div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ============================ Tabs ============================ -->
<section class="section-sm pt-0">
  <div class="container">
    <div data-reveal="up">
      <ul class="nav nav-tabs-pill mb-4" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-desc" type="button" role="tab"><i class="bi bi-card-text me-1"></i> Overview</button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-ask" type="button" role="tab"><i class="bi bi-chat-dots me-1"></i> Ask about this product</button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-supply" type="button" role="tab"><i class="bi bi-truck me-1"></i> Supply &amp; Support</button>
        </li>
      </ul>

      <div class="tab-content">
        <div class="tab-pane fade show active" id="tab-desc" role="tabpanel">
          <div class="row g-4">
            <div class="col-lg-8">
              <h4 class="mb-3">About this product</h4>
              @if($product->description)
                <div class="pd-description">{!! nl2br(e(strip_tags($product->description))) !!}</div>
              @else
                <p>This item is part of the {{ $shopSettings['name'] ?? 'Shiksha' }} range of electrical and lighting products supplied to homes, builders, contractors and dealers across India. Full specifications, available variants, finishes and wattages are confirmed at the time of quotation.</p>
              @endif
              <h5 class="mt-4 mb-3">What you get with a quote</h5>
              <ul class="check-list">
                <li><i class="bi bi-check-lg"></i><span>Itemised pricing against your exact quantity</span></li>
                <li><i class="bi bi-check-lg"></i><span>Confirmed specification sheet and available variants</span></li>
                <li><i class="bi bi-check-lg"></i><span>Current stock position and realistic dispatch dates</span></li>
                <li><i class="bi bi-check-lg"></i><span>Slab rates where the quantity qualifies for a better price</span></li>
              </ul>
            </div>
            <div class="col-lg-4">
              <div class="p-4 rounded-xl h-100" style="background:var(--sl-paper-2);border:1px solid var(--sl-line)">
                <h6 class="mb-3"><i class="bi bi-lightbulb me-2 text-gradient"></i>Not sure which variant?</h6>
                <p class="small mb-3">Tell us the application — room size, mounting type, wattage or load — and we will recommend the right item from the range before you commit to a quantity.</p>
                <a href="{{ route('shop.contact') }}" class="btn btn-outline-brand btn-sm w-100">Ask our team</a>
              </div>
            </div>
          </div>
        </div>

        <div class="tab-pane fade" id="tab-ask" role="tabpanel">
          <div class="row g-4">
            <div class="col-lg-7">
              <h4 class="mb-3">Send a question about this product</h4>
              <form method="POST" action="{{ route('shop.contact.submit') }}">
                @csrf
                <div class="row g-3">
                  <div class="col-md-6">
                    <div class="form-floating-pill mb-0">
                      <label for="pdName">Full name <span class="text-danger">*</span></label>
                      <input type="text" class="form-control" id="pdName" name="name" value="{{ old('name') }}" placeholder="Your name" required minlength="2">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-floating-pill mb-0">
                      <label for="pdPhone">Phone / WhatsApp</label>
                      <input type="tel" class="form-control" id="pdPhone" name="phone" value="{{ old('phone') }}" placeholder="+91 98765 43210">
                    </div>
                  </div>
                  <div class="col-12">
                    <div class="form-floating-pill mb-0">
                      <label for="pdEmail">Email address <span class="text-danger">*</span></label>
                      <input type="email" class="form-control" id="pdEmail" name="email" value="{{ old('email') }}" placeholder="you@example.com" required>
                    </div>
                  </div>
                  <div class="col-12">
                    <div class="form-floating-pill mb-0">
                      <label for="pdQuestion">Your question <span class="text-danger">*</span></label>
                      <textarea class="form-control" id="pdQuestion" name="message" rows="5" required minlength="12">{{ old('message', 'Regarding: '.$product->title."\n\n") }}</textarea>
                    </div>
                  </div>
                  <div class="col-12">
                    <button type="submit" class="btn btn-brand btn-lg">Send question <i class="bi bi-send ms-1"></i></button>
                  </div>
                </div>
              </form>
            </div>
            <div class="col-lg-5">
              <div class="p-4 rounded-xl h-100" style="background:var(--sl-paper-2);border:1px solid var(--sl-line)">
                <h6 class="mb-3"><i class="bi bi-telephone me-2 text-gradient"></i>Faster over the phone?</h6>
                <p class="small mb-3">Our sales desk is open Monday to Saturday, 10am&ndash;7pm IST. Quote the product name above and we can check stock while you are on the call.</p>
                @if(!empty($shopSettings['phone']))
                  <a href="tel:{{ preg_replace('/[^0-9+]/','',$shopSettings['phone']) }}" class="btn btn-brand btn-sm w-100 mb-2">{{ $shopSettings['phone'] }}</a>
                @endif
                @if(!empty($shopSettings['email']))
                  <a href="mailto:{{ $shopSettings['email'] }}" class="btn btn-outline-brand btn-sm w-100">Email us</a>
                @endif
              </div>
            </div>
          </div>
        </div>

        <div class="tab-pane fade" id="tab-supply" role="tabpanel">
          <div class="row g-4">
            <div class="col-md-6">
              <h5 class="mb-3"><i class="bi bi-truck me-2 text-gradient"></i>Supply</h5>
              <ul class="check-list">
                <li><i class="bi bi-check-lg"></i><span>Stocked lines dispatched within 24&ndash;48 hours of order confirmation.</span></li>
                <li><i class="bi bi-check-lg"></i><span>Project and bulk quantities scheduled against your site programme.</span></li>
                <li><i class="bi bi-check-lg"></i><span>Pan-India dispatch by road and courier; freight confirmed in the quote.</span></li>
                <li><i class="bi bi-check-lg"></i><span>Dealer and distributor terms available on request.</span></li>
              </ul>
            </div>
            <div class="col-md-6">
              <h5 class="mb-3"><i class="bi bi-headset me-2 text-gradient"></i>Support</h5>
              <ul class="check-list">
                <li><i class="bi bi-check-lg"></i><span>Technical guidance on selection, wattage and load before you order.</span></li>
                <li><i class="bi bi-check-lg"></i><span>Manufacturer warranty applies as stated on the product documentation.</span></li>
                <li><i class="bi bi-check-lg"></i><span>Replacement handled through our sales desk with the original invoice.</span></li>
                <li><i class="bi bi-check-lg"></i><span>Damaged in transit? Send photos within 48 hours of delivery.</span></li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ============================ Related ============================ -->
@if($related->count())
<section class="section bg-soft">
  <div class="container">
    <div class="section-head center" data-reveal="up">
      <span class="eyebrow"><i class="bi bi-collection"></i> You may also need</span>
      <div class="divider-glow"></div>
      <h2>Add to the <span class="text-gradient">same enquiry</span></h2>
    </div>
    <div class="row product-grid g-3 g-lg-4">
      @include('shop.products._grid', ['products' => $related, 'cols' => 'col-6 col-lg-3'])
    </div>
  </div>
</section>
@endif

@endsection
