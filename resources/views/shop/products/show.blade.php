@extends('shop.layout')

@section('content')
@php
  $images = collect([$product->image])->merge($product->gallary_images->pluck('image'))->filter()->values();
  $price = $product->sale_price ?: $product->price;
@endphp
<div class="page-wrap page-hero">
    <div class="chip">{{ $product->category->name ?? 'Product' }}</div>
    <h1>{{ $product->title }}</h1>
</div>

<div class="page-wrap detail-layout">
    <div class="gallery">
        <div class="gallery-main">
            <img data-gallery-main src="{{ $images->first() ? asset('storage/'.$images->first()) : asset('front_assets/img/logo.png') }}" alt="{{ $product->title }}">
        </div>
        @if($images->count() > 1)
            <div class="thumbs">
                @foreach($images as $i => $image)
                    <img data-thumb class="{{ $i === 0 ? 'active' : '' }}" src="{{ asset('storage/'.$image) }}" alt="{{ $product->title }}">
                @endforeach
            </div>
        @endif
    </div>

    <div class="detail-copy panel" style="padding:1.5rem;">
        <div class="meta-row">
            @if($product->unit)<span class="chip">Unit: {{ $product->unit->name }}</span>@endif
            <span class="chip">
                @if($product->stock === null || $product->stock > 0) Available @else Out of stock @endif
            </span>
        </div>
        <div class="price-lg">
            @if($price !== null && $price !== '' && (float)$price > 0)
                ₹{{ number_format((float)$price, 2) }}
            @else
                Enquire for price
            @endif
        </div>
        <p style="color:var(--brand-muted);line-height:1.7;">{{ $product->short_description }}</p>

        @if($product->variants->count())
            <div style="margin:1rem 0;">
                <strong>Variations</strong>
                <div style="margin-top:.6rem;display:grid;gap:.5rem;">
                    @foreach($product->variants as $variant)
                        <div class="chip" style="justify-content:space-between;width:100%;border-radius:14px;">
                            <span>
                                {{ $variant->variantname->name ?? $variant->name ?? 'Variant' }}
                                @if($variant->sku) · {{ $variant->sku }} @endif
                            </span>
                            <button
                                type="button"
                                class="btn btn-primary"
                                style="padding:.45rem .8rem;"
                                data-add-to-cart="{{ route('shop.cart.add') }}"
                                data-product-id="{{ $product->id }}"
                                data-variant-id="{{ $variant->id }}"
                            >Add</button>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="qty-row">
            <label for="qty">Qty</label>
            <input id="qty" class="form-control" type="number" min="1" value="1" data-qty-input>
        </div>

        <div style="display:flex;gap:.75rem;flex-wrap:wrap;">
            <button
                type="button"
                class="btn btn-primary"
                data-add-to-cart="{{ route('shop.cart.add') }}"
                data-product-id="{{ $product->id }}"
            >Add to Enquiry Cart</button>
            <a class="btn btn-secondary" href="{{ route('shop.enquiry.create') }}">Make Enquiry</a>
        </div>

        @if($product->description)
            <div style="margin-top:1.5rem;">
                <h3 style="font-family:var(--font-display);">Description</h3>
                <div style="color:var(--brand-muted);line-height:1.7;">{!! nl2br(e(strip_tags($product->description))) !!}</div>
            </div>
        @endif
    </div>
</div>

@if($related->count())
<section class="section" style="padding-top:0;">
    <div class="wrap">
        <div class="section-head">
            <div><h2>Related Products</h2></div>
        </div>
        @include('shop.products._grid', ['products' => $related])
    </div>
</section>
@endif
@endsection
