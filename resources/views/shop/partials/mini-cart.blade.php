@php
    $items = array_values($items ?? []);
    $subtotal = collect($items)->sum(fn ($i) => ((float) ($i['unit_price'] ?? 0)) * (int) ($i['qty'] ?? 0));
@endphp

@if(count($items))
  <div class="side-cart-body">
    @foreach($items as $item)
      <div class="side-cart-item" data-cart-line>
        <img src="{{ !empty($item['image']) ? asset('storage/'.$item['image']) : asset('theme/img/logo.png') }}" alt="{{ $item['title'] }}">
        <div class="sci-info">
          <a href="{{ route('shop.product', $item['slug']) }}" class="sci-title">{{ $item['title'] }}</a>
          @if(!empty($item['variation_label']))<div class="sci-meta">{{ $item['variation_label'] }}</div>@endif
          <div class="sci-meta">
            Qty {{ $item['qty'] }}
            @if(!empty($item['unit_price']) && (float) $item['unit_price'] > 0)
              &times; ₹{{ number_format((float) $item['unit_price'], 2) }}
            @endif
          </div>
        </div>
        <button type="button" class="sci-remove" data-cart-remove="{{ $item['key'] }}" aria-label="Remove {{ $item['title'] }}">
          <i class="bi bi-x-lg"></i>
        </button>
      </div>
    @endforeach
  </div>

  <div class="side-cart-foot">
    @if($subtotal > 0)
      <div class="sc-subtotal"><span>Subtotal</span><strong>₹{{ number_format($subtotal, 2) }}</strong></div>
    @else
      <p class="sc-note small text-muted-2 mb-3">Enquiry-based items — we'll send pricing with your quote.</p>
    @endif
    <div class="sc-actions">
      <a href="{{ route('shop.cart') }}" class="btn btn-outline-brand">View Cart</a>
      <a href="{{ route('shop.enquiry.create') }}" class="btn btn-brand">Checkout</a>
    </div>
  </div>
@else
  <div class="side-cart-empty">
    <i class="bi bi-bag-x"></i>
    <p class="mb-3">Your cart is empty</p>
    <a href="{{ route('shop.products') }}" class="btn btn-brand btn-sm">Browse products</a>
  </div>
@endif
