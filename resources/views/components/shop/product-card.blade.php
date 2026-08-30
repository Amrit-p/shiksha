@props([
    'product',
    'index' => 0,
    'cols' => 'col-12 col-sm-6 col-lg-3 col-xxl-3',
    'reveal' => true,
])

@php
    $image = $product->image ? asset('storage/'.$product->image) : asset('theme/img/logo.png');
    $url = route('shop.product', $product->slug);
    $catName = $product->category->name ?? 'Product';
    $rawPrice = $product->sale_price ?: $product->price;
    $hasPrice = $rawPrice !== null && $rawPrice !== '' && (float) $rawPrice > 0;
    $badge = $product->wattage ?: ($product->is_featured ? 'Featured' : null);
@endphp

<div class="{{ $cols }}" @if($reveal) data-reveal="rise" style="--reveal-delay:{{ ($index % 4) * 80 }}ms" @endif>
  <article class="product-card" data-tilt data-tilt-max="5">
    @if($badge)
      <div class="product-badges"><span class="p-badge p-badge--code">{{ $badge }}</span></div>
    @endif
    <a class="product-media" href="{{ $url }}">
      <img src="{{ $image }}" alt="{{ $product->title }}" loading="lazy" width="600" height="600">
    </a>
    <div class="product-body">
      <div class="product-cat">{{ $catName }}</div>
      <h3 class="product-title"><a href="{{ $url }}">{{ $product->title }}</a></h3>
      <div class="product-foot">
        <span class="product-enquire">
          @if($hasPrice)
            <span class="product-price">₹{{ number_format((float) $rawPrice, 2) }}</span>
          @else
            <i class="bi bi-tag me-1"></i>Enquire for price
          @endif
        </span>
        <button type="button" class="btn-cart-mini"
                data-add-to-cart="{{ route('shop.cart.add') }}"
                data-product-id="{{ $product->id }}"
                aria-label="Add {{ $product->title }} to enquiry">
          <i class="bi bi-plus-lg"></i>
        </button>
      </div>
    </div>
  </article>
</div>
