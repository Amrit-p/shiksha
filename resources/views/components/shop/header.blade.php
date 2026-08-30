@props([
    'settings' => [],
    'categories' => null,
    'cartCount' => 0,
    'cartItems' => [],
])

@php
    $categories = $categories ?? collect();
    $phone = $settings['phone'] ?? '+91 98765 43210';
    $phoneHref = preg_replace('/[^0-9+]/', '', $phone);
    $email = $settings['email'] ?? 'info@shiksha.local';
    $logo = asset('theme/img/logo.png');
    $brandName = $settings['name'] ?? 'Shiksha';
@endphp

<!-- ============================ Header ============================ -->
<header class="site-header">
  <div class="topbar">
    <div class="container">
      <div class="topbar-inner">
        <div class="topbar-marquee">
          <i class="bi bi-lightning-charge-fill"></i>
          <span>Bulk, trade &amp; project enquiries — <strong class="text-white">quoted within one business day</strong></span>
        </div>
        <div class="topbar-links">
          <a href="tel:{{ $phoneHref }}"><i class="bi bi-telephone me-1"></i>{{ $phone }}</a>
          <a href="{{ route('shop.contact') }}"><i class="bi bi-geo-alt me-1"></i>Visit showroom</a>
          <a href="{{ route('shop.cart') }}"><i class="bi bi-clipboard-check me-1"></i>My enquiry list</a>
        </div>
      </div>
    </div>
  </div>

  <div class="header-main">
    <div class="container">
      <div class="header-inner">
        <a class="brand" href="{{ route('shop.home') }}" aria-label="{{ $brandName }} home">
          <img src="{{ $logo }}" alt="{{ $brandName }} logo">
          <span class="brand-text d-none d-sm-flex">
            <span class="brand-name">{{ $brandName }}</span>
            <span class="brand-sub">Lighting</span>
          </span>
        </a>

        <nav class="main-nav" aria-label="Main navigation">
          <div class="nav-item"><a class="nav-link {{ request()->routeIs('shop.home') ? 'active' : '' }}" href="{{ route('shop.home') }}">Home</a></div>
          <div class="nav-item"><a class="nav-link {{ request()->routeIs('shop.products') || request()->routeIs('shop.product') || request()->routeIs('shop.search') ? 'active' : '' }}" href="{{ route('shop.products') }}">Products</a></div>
          @if($categories->count())
          <div class="nav-item">
            <a class="nav-link" href="{{ route('shop.products') }}">Categories <i class="bi bi-chevron-down"></i></a>
            <div class="nav-drop">
              <div class="row g-2">
                @foreach($categories->take(6) as $category)
                <div class="col-6">
                  <a class="drop-link" href="{{ route('shop.products', ['category' => $category->slug ?? $category->id]) }}">
                    <span class="drop-ico"><i class="bi bi-lightbulb"></i></span>
                    <span class="flex-new"><span class="drop-title">{{ $category->name }}</span><span class="drop-desc">Browse the range</span></span>
                  </a>
                </div>
                @endforeach
                <div class="col-12">
                  <a class="drop-link" href="{{ route('shop.products') }}">
                    <span class="drop-ico"><i class="bi bi-grid-3x3-gap"></i></span>
                    <span><span class="drop-title">All categories</span><span class="drop-desc">Browse the full catalogue</span></span>
                  </a>
                </div>
              </div>
            </div>
          </div>
          @endif
          <div class="nav-item"><a class="nav-link {{ request()->routeIs('shop.about') ? 'active' : '' }}" href="{{ route('shop.about') }}">About Us</a></div>
          <div class="nav-item"><a class="nav-link {{ request()->routeIs('shop.contact') ? 'active' : '' }}" href="{{ route('shop.contact') }}">Contact</a></div>
        </nav>

        <div class="header-actions">
          <button type="button" class="icon-btn" data-search-open aria-label="Search products">
            <i class="bi bi-search"></i>
          </button>
          <a class="icon-btn" href="{{ route('shop.cart') }}" aria-label="Enquiry cart" data-side-cart-open>
            <i class="bi bi-clipboard-check"></i>
            <span class="count-badge" data-cart-count @if(($cartCount ?? 0) < 1) hidden @endif>{{ $cartCount ?? 0 }}</span>
          </a>
          <button type="button" class="hamburger" aria-label="Open menu" aria-expanded="false" aria-controls="mobileNav">
            <span></span><span></span><span></span>
          </button>
        </div>
      </div>
    </div>
  </div>
</header>

<!-- ============================ Mobile nav ============================ -->
<div class="mobile-nav-backdrop"></div>
<aside class="mobile-nav" id="mobileNav" aria-hidden="true" aria-label="Mobile navigation">
  <div class="mobile-nav-head">
    <a class="brand" href="{{ route('shop.home') }}">
      <img src="{{ $logo }}" alt="{{ $brandName }}">
      <span class="brand-text">
        <span class="brand-name">{{ $brandName }}</span>
        <span class="brand-sub">Lighting</span>
      </span>
    </a>
    <button type="button" class="mobile-nav-close" aria-label="Close menu"><i class="bi bi-x-lg"></i></button>
  </div>

  <div class="mobile-nav-body">
    <ul class="mobile-nav-list">
      <li><a class="mobile-nav-link" href="{{ route('shop.home') }}">Home <i class="bi bi-arrow-right"></i></a></li>
      <li><a class="mobile-nav-link" href="{{ route('shop.products') }}">Products <i class="bi bi-arrow-right"></i></a></li>
      @if($categories->count())
      <li>
        <a class="mobile-nav-link mobile-nav-toggle-sub" href="#">Categories <i class="bi bi-chevron-down"></i></a>
        <ul class="mobile-sub">
          @foreach($categories as $category)
          <li><a href="{{ route('shop.products', ['category' => $category->slug ?? $category->id]) }}">{{ $category->name }}</a></li>
          @endforeach
          <li><a href="{{ route('shop.products') }}">All categories</a></li>
        </ul>
      </li>
      @endif
      <li><a class="mobile-nav-link" href="{{ route('shop.about') }}">About Us <i class="bi bi-arrow-right"></i></a></li>
      <li><a class="mobile-nav-link" href="{{ route('shop.contact') }}">Contact <i class="bi bi-arrow-right"></i></a></li>
      <li><a class="mobile-nav-link" href="{{ route('shop.cart') }}">My Enquiry <i class="bi bi-arrow-right"></i></a></li>
    </ul>
  </div>

  <div class="mobile-nav-foot">
    <div class="mobile-contact-row"><i class="bi bi-telephone-fill"></i><a href="tel:{{ $phoneHref }}" class="text-white-50">{{ $phone }}</a></div>
    <div class="mobile-contact-row"><i class="bi bi-envelope-fill"></i><a href="mailto:{{ $email }}" class="text-white-50">{{ $email }}</a></div>
  </div>
</aside>

<!-- ============================ Search overlay ============================ -->
<div class="search-overlay" aria-hidden="true" data-suggest-url="{{ route('shop.search.suggest') }}">
  <button type="button" class="search-close" aria-label="Close search"><i class="bi bi-x-lg"></i></button>
  <div class="search-panel">
    <form class="search-field" action="{{ route('shop.search') }}" method="GET" role="search">
      <i class="bi bi-search"></i>
      <input type="search" name="q" value="{{ request('q') }}" placeholder="Search COB lights, fans, MCBs, wires…" aria-label="Search products">
    </form>
    <div class="search-tags">
      <a href="#" class="search-tag">COB</a>
      <a href="#" class="search-tag">Panel</a>
      <a href="#" class="search-tag">Fan</a>
      <a href="#" class="search-tag">MCB</a>
      <a href="#" class="search-tag">Wire</a>
      <a href="#" class="search-tag">Geyser</a>
    </div>
    <div class="search-results">
      <p class="text-center text-white-50 mb-0 py-3 small">Type a product name and press Enter to search the catalogue.</p>
    </div>
  </div>
</div>

<!-- ============================ Side cart drawer ============================ -->
<div class="side-cart-backdrop" data-side-cart-backdrop></div>
<aside class="side-cart" data-side-cart aria-hidden="true" aria-label="Your cart">
  <div class="side-cart-head">
    <h5 class="mb-0"><i class="bi bi-bag me-2"></i>Your Cart</h5>
    <button type="button" class="side-cart-close" data-side-cart-close aria-label="Close cart"><i class="bi bi-x-lg"></i></button>
  </div>
  <div class="side-cart-scroll" data-mini-cart>
    @include('shop.partials.mini-cart', ['items' => $cartItems ?? []])
  </div>
</aside>
