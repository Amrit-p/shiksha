@extends('shop.layout')

@section('content')
@php
  $hero = $sections['hero'] ?? null;
  $about = $sections['about_preview'] ?? null;
  $why = $sections['why_us'] ?? null;
  $cta = $sections['cta'] ?? null;
  $whyItems = [];
  if ($why && $why->content) {
      $decoded = json_decode($why->content, true);
      $whyItems = is_array($decoded) ? $decoded : [];
  }
  $banner = $banners->first();
@endphp

<section class="hero">
    <div class="float-orb orb-a"></div>
    <div class="float-orb orb-b"></div>
    <div class="wrap hero-grid">
        <div class="hero-copy">
            <div class="chip" style="margin-bottom:1rem;">{{ $shopSettings['name'] ?? 'Shiksha' }} Lighting</div>
            <h1>{{ $hero->title ?? 'Illuminate Every Space with Shiksha' }}</h1>
            <p>{{ $hero->subtitle ?? 'Professional lighting solutions for homes, offices, and commercial projects.' }}</p>
            <p>{{ $hero->content ?? '' }}</p>
            <div class="hero-actions">
                <a class="btn btn-primary" href="{{ $hero->button_link ?? route('shop.products') }}">{{ $hero->button_text ?? 'Explore Products' }}</a>
                <a class="btn btn-outline" href="{{ route('shop.cart') }}">Make Enquiry</a>
            </div>
        </div>
        <div class="hero-panel">
            @if($banner && $banner->image)
                <img src="{{ asset('storage/'.$banner->image) }}" alt="Shiksha banner">
            @else
                <img src="{{ $shopSettings['logo'] ?? asset('front_assets/img/logo.png') }}" alt="Shiksha" style="object-fit:contain;padding:3rem;background:#fff;">
            @endif
        </div>
    </div>
</section>

@if($categories->count())
<section class="section">
    <div class="wrap">
        <div class="section-head">
            <div>
                <h2>Shop by Category</h2>
                <p>Find the right products for your project.</p>
            </div>
            <a href="{{ route('shop.products') }}">View all</a>
        </div>
        <div class="category-grid">
            @foreach($categories as $category)
                <a class="category-card" href="{{ route('shop.products', ['category' => $category->id]) }}">
                    @if($category->image)
                        <img src="{{ asset('storage/'.$category->image) }}" alt="{{ $category->name }}">
                    @else
                        <img src="{{ asset('front_assets/img/logo.png') }}" alt="{{ $category->name }}">
                    @endif
                    <strong>{{ $category->name }}</strong>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

<section class="section" style="padding-top:0;">
    <div class="wrap">
        <div class="section-head">
            <div>
                <h2>Featured Products</h2>
                <p>Handpicked products from the Shiksha catalogue.</p>
            </div>
            <a href="{{ route('shop.products') }}">Browse catalogue</a>
        </div>
        @include('shop.products._grid', ['products' => $featured])
    </div>
</section>

<section class="section" style="padding-top:0;">
    <div class="wrap">
        <div class="section-head">
            <div>
                <h2>Popular Products</h2>
                <p>Products customers enquire about most.</p>
            </div>
        </div>
        @include('shop.products._grid', ['products' => $popular])
    </div>
</section>

@if($about)
<section class="section" style="padding-top:0;">
    <div class="wrap panel" style="padding:2rem;display:grid;grid-template-columns:1.2fr 0.8fr;gap:2rem;align-items:center;">
        <div>
            <div class="chip">{{ $about->subtitle }}</div>
            <h2 style="font-family:var(--font-display);font-size:2.2rem;margin:0.6rem 0;">{{ $about->title }}</h2>
            <p style="color:var(--brand-muted);line-height:1.7;">{{ $about->content }}</p>
            @if($about->button_link)
                <a class="btn btn-secondary" href="{{ $about->button_link }}">{{ $about->button_text ?? 'Learn More' }}</a>
            @endif
        </div>
        <div style="min-height:220px;border-radius:24px;background:linear-gradient(145deg,rgba(230,0,126,.15),rgba(0,173,239,.18));display:grid;place-items:center;">
            <img src="{{ $shopSettings['logo'] }}" alt="Shiksha" style="width:140px;height:140px;object-fit:contain;">
        </div>
    </div>
</section>
@endif

@if(count($whyItems))
<section class="section" style="padding-top:0;">
    <div class="wrap">
        <div class="section-head">
            <div>
                <h2>{{ $why->title }}</h2>
                <p>{{ $why->subtitle }}</p>
            </div>
        </div>
        <div class="feature-grid">
            @foreach($whyItems as $item)
                <div class="feature-card">
                    <h3>{{ $item['title'] ?? '' }}</h3>
                    <p>{{ $item['text'] ?? '' }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

@if($cta)
<div class="wrap">
    <div class="cta-band">
        <div>
            <h2>{{ $cta->title }}</h2>
            <p>{{ $cta->subtitle }}</p>
        </div>
        <a class="btn btn-ghost" href="{{ $cta->button_link ?? route('shop.cart') }}">{{ $cta->button_text ?? 'Make an Enquiry' }}</a>
    </div>
</div>
@endif
@endsection
