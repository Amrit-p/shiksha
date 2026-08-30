@extends('shop.theme')

@section('content')
<div data-cart-page></div>

<!-- ============================ Page hero ============================ -->
<section class="page-hero">
  <span class="glow-orb glow-orb--pink" style="width:400px;height:400px;top:-140px;left:-100px"></span>
  <span class="glow-orb glow-orb--blue" style="width:380px;height:380px;bottom:-170px;right:-80px"></span>
  <div class="container z-1">
    <span class="eyebrow eyebrow-light" data-reveal="down"><i class="bi bi-clipboard-check"></i> One list, one reply</span>
    <h1 data-reveal="up">Your <span class="text-gradient-glow">enquiry cart</span></h1>
    <p data-reveal="up" style="--reveal-delay:90ms">
      Set the quantity you need against each product, then head to the enquiry form — our sales desk
      comes back with pricing, availability and delivery.
    </p>
    <div class="breadcrumb-pill" data-reveal="up" style="--reveal-delay:170ms">
      <a href="{{ route('shop.home') }}">Home</a><span class="sep">/</span><span class="current">Enquiry cart</span>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">

    <div class="step-line" data-reveal="up">
      <div class="step-node is-active"><span class="sn-dot">1</span><span class="sn-label">Build list</span></div>
      <span class="step-bar"></span>
      <div class="step-node"><span class="sn-dot">2</span><span class="sn-label">Your details</span></div>
      <span class="step-bar"></span>
      <div class="step-node"><span class="sn-dot">3</span><span class="sn-label">We quote</span></div>
      <span class="step-bar"></span>
      <div class="step-node"><span class="sn-dot">4</span><span class="sn-label">Supply</span></div>
    </div>

    @if(count($items))
    <div class="row g-4 g-xl-5">
      <div class="col-lg-8">
        <div class="cart-table-wrap" data-reveal="up">
          <div class="d-flex align-items-center justify-content-between px-4 py-3" style="border-bottom:1px solid var(--sl-line);background:var(--sl-paper-2)">
            <h5 class="mb-0"><i class="bi bi-clipboard-check me-2"></i>Products <span class="text-muted-2 fw-normal">({{ $count }} units)</span></h5>
            <button type="button" class="btn btn-sm p-0 border-0 bg-transparent small text-muted-2" data-cart-clear>
              <i class="bi bi-trash3 me-1"></i>Clear list
            </button>
          </div>

          @foreach($items as $item)
            <div class="cart-line" data-cart-line>
              <img src="{{ !empty($item['image']) ? asset('storage/'.$item['image']) : asset('theme/img/logo.png') }}" alt="{{ $item['title'] }}">
              <div class="flex-grow-1">
                <h6 class="mb-1"><a href="{{ route('shop.product', $item['slug']) }}" class="text-ink">{{ $item['title'] }}</a></h6>
                @if(!empty($item['variation_label']))<div class="small text-muted-2">{{ $item['variation_label'] }}</div>@endif
                @if(!empty($item['sku']))<div class="small text-muted-2">SKU: {{ $item['sku'] }}</div>@endif
                <div class="small mt-1">
                  @if(isset($item['unit_price']) && $item['unit_price'] !== null && (float)$item['unit_price'] > 0)
                    <span class="product-price">₹{{ number_format((float) $item['unit_price'], 2) }}</span>
                  @else
                    <span class="product-enquire"><i class="bi bi-tag me-1"></i>Enquire for price</span>
                  @endif
                </div>
              </div>
              <div class="qty-stepper">
                <button type="button" data-qty-minus aria-label="Decrease quantity"><i class="bi bi-dash-lg"></i></button>
                <input type="number" min="0" max="999" value="{{ $item['qty'] }}" data-cart-qty="{{ $item['key'] }}" aria-label="Quantity">
                <button type="button" data-qty-plus aria-label="Increase quantity"><i class="bi bi-plus-lg"></i></button>
              </div>
              <button type="button" class="icon-btn" data-cart-remove="{{ $item['key'] }}" aria-label="Remove {{ $item['title'] }}" style="width:44px;height:44px;flex:none">
                <i class="bi bi-x-lg"></i>
              </button>
            </div>
          @endforeach
        </div>

        <div class="d-flex flex-wrap gap-2 mt-4" data-reveal="up">
          <a href="{{ route('shop.products') }}" class="btn btn-outline-brand"><i class="bi bi-arrow-left me-1"></i> Add more products</a>
        </div>

        <div class="p-4 rounded-xl mt-4" style="background:var(--sl-paper-2);border:1px solid var(--sl-line)" data-reveal="up">
          <div class="d-flex align-items-center gap-2 mb-1">
            <i class="bi bi-info-circle text-gradient fs-5"></i>
            <strong class="text-ink">Why there are no prices here</strong>
          </div>
          <p class="small text-muted-2 mb-0">
            Rates move with quantity, specification and current stock. Sending the list gets you a
            written quote against your exact requirement rather than a list price you would have to negotiate anyway.
          </p>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="summary-card" data-reveal="right">
          <h5 class="mb-1">Ready to enquire?</h5>
          <p class="small text-muted-2 mb-4">{{ count($items) }} {{ \Illuminate\Support\Str::plural('product', count($items)) }} · {{ $count }} units total</p>
          <a href="{{ route('shop.enquiry.create') }}" class="btn btn-brand btn-lg w-100 mb-2">
            Proceed to enquiry <i class="bi bi-arrow-right ms-1"></i>
          </a>
          <a href="{{ route('shop.products') }}" class="btn btn-outline-brand w-100">Keep browsing</a>

          <div class="mt-4 pt-4" style="border-top:1px solid var(--sl-line)">
            <p class="small text-muted-2 mb-2">Prefer to talk it through?</p>
            @if(!empty($shopSettings['phone']))
              <a href="tel:{{ preg_replace('/[^0-9+]/','',$shopSettings['phone']) }}" class="btn btn-outline-brand w-100 mb-2"><i class="bi bi-telephone me-1"></i> {{ $shopSettings['phone'] }}</a>
            @endif
            @if(!empty($shopSettings['email']))
              <a href="mailto:{{ $shopSettings['email'] }}" class="btn btn-outline-brand w-100"><i class="bi bi-envelope me-1"></i> Email the sales desk</a>
            @endif
          </div>
        </div>
      </div>
    </div>
    @else
    <div class="text-center py-5" data-reveal="up">
      <div class="mx-auto mb-4" style="width:110px;height:110px;border-radius:50%;display:grid;place-items:center;background:var(--sl-paper-3)">
        <i class="bi bi-clipboard-x" style="font-size:3rem;color:var(--sl-line-strong)"></i>
      </div>
      <h3 class="mb-2">Your enquiry cart is empty</h3>
      <p class="mb-4">Add the products you need from the catalogue and send them to us in one go.</p>
      <a href="{{ route('shop.products') }}" class="btn btn-brand btn-lg">Browse products <i class="bi bi-arrow-right ms-1"></i></a>
    </div>
    @endif

  </div>
</section>

@endsection
