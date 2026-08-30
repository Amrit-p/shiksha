@extends('shop.theme')

@section('content')

<!-- ============================ Page hero ============================ -->
<section class="page-hero">
  <span class="glow-orb glow-orb--pink" style="width:420px;height:420px;top:-140px;left:-100px"></span>
  <span class="glow-orb glow-orb--blue" style="width:400px;height:400px;bottom:-180px;right:-90px"></span>
  <div class="container z-1">
    <span class="eyebrow eyebrow-light" data-reveal="down"><i class="bi bi-grid-3x3-gap"></i> The full catalogue</span>
    <h1 data-reveal="up">Browse <span class="text-gradient-glow">all products</span></h1>
    <p data-reveal="up" style="--reveal-delay:90ms">
      Filter by category, price or availability, add what you need to your enquiry cart, and send it in one go —
      we come back with pricing, availability and delivery.
    </p>
    <div class="breadcrumb-pill" data-reveal="up" style="--reveal-delay:170ms">
      <a href="{{ route('shop.home') }}">Home</a><span class="sep">/</span><span class="current">Products</span>
    </div>
  </div>
</section>

<!-- ============================ Catalogue ============================ -->
<section class="section" id="shopTop">
  <div class="container">
    <div class="row g-4 g-xl-5">

      <!-- Filters -->
      <div class="col-lg-4 col-xl-3">
        <div class="filter-panel" data-reveal="left" data-filters-panel>
          <form data-product-filters action="{{ route('shop.products') }}" method="GET">

            <div class="filter-block">
              <div class="filter-title"><span><i class="bi bi-search me-2"></i>Search</span></div>
              <input type="search" class="form-control" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Name, SKU or model code…">
            </div>

            <div class="filter-block">
              <div class="filter-title"><span>Category</span></div>
              <select class="form-select" name="category">
                <option value="">All categories</option>
                @foreach($categories as $category)
                  <option value="{{ $category->id }}" @selected(($filters['category'] ?? '') == $category->id)>{{ $category->name }}</option>
                @endforeach
              </select>
            </div>

            <div class="filter-block">
              <div class="filter-title"><span>Availability</span></div>
              <select class="form-select" name="availability">
                <option value="">Any</option>
                <option value="in_stock" @selected(($filters['availability'] ?? '') === 'in_stock')>In stock</option>
              </select>
            </div>

            <div class="filter-block">
              <div class="filter-title"><span>Price range</span></div>
              <div class="d-flex gap-2">
                <input type="number" class="form-control" name="min_price" value="{{ $filters['min_price'] ?? '' }}" min="0" step="0.01" placeholder="Min">
                <input type="number" class="form-control" name="max_price" value="{{ $filters['max_price'] ?? '' }}" min="0" step="0.01" placeholder="Max">
              </div>
            </div>

            <div class="filter-block">
              <div class="filter-title"><span>Sort by</span></div>
              <select class="form-select" name="sort">
                <option value="latest" @selected(($filters['sort'] ?? 'latest') === 'latest')>Latest</option>
                <option value="name" @selected(($filters['sort'] ?? '') === 'name')>Name: A–Z</option>
                <option value="price_asc" @selected(($filters['sort'] ?? '') === 'price_asc')>Price: Low to High</option>
                <option value="price_desc" @selected(($filters['sort'] ?? '') === 'price_desc')>Price: High to Low</option>
              </select>
            </div>

            <div class="filter-block d-grid gap-2">
              <button type="submit" class="btn btn-brand w-100">Apply filters</button>
              <a href="{{ route('shop.products') }}" class="btn btn-outline-brand w-100">
                <i class="bi bi-arrow-counterclockwise me-1"></i> Reset all
              </a>
            </div>

            <div class="filter-block">
              <div class="p-3 rounded-xl text-center" style="background:var(--sl-grad);color:#fff">
                <i class="bi bi-clipboard-check fs-3 d-block mb-2"></i>
                <h6 class="text-white mb-1">Bulk or project?</h6>
                <p class="small mb-3 opacity-85">Send us the list and quantities — we quote slab rates.</p>
                <a href="{{ route('shop.cart') }}" class="btn btn-ghost-light btn-sm w-100">Your enquiry cart</a>
              </div>
            </div>

          </form>
        </div>
      </div>

      <!-- Results -->
      <div class="col-lg-8 col-xl-9">
        <div class="shop-toolbar" data-reveal="up">
          <div class="shop-count me-auto">
            <strong data-product-count>{{ $products->total() }}</strong> products found
          </div>
          <button type="button" class="btn btn-outline-brand btn-sm d-lg-none" data-filter-toggle>
            <i class="bi bi-sliders me-1"></i> Filters
          </button>
          <a class="btn btn-brand btn-sm" href="{{ route('shop.cart') }}"><i class="bi bi-clipboard-check me-1"></i> Enquiry cart</a>
        </div>

        <div class="row product-grid g-3 g-lg-4" data-product-grid>
          @include('shop.products._grid', ['products' => $products, 'cols' => 'col-6 col-lg-4'])
        </div>

        <div data-product-pagination>
          @include('shop.products._pagination', ['products' => $products])
        </div>

        <p class="text-center small text-muted-2 mt-4 mb-0">
          Can't find an item? <a href="{{ route('shop.contact') }}" class="link-underline-anim">Ask us</a> — we stock more than the catalogue lists.
        </p>
      </div>

    </div>
  </div>
</section>

<!-- ============================ How it works ============================ -->
<section class="section-sm bg-soft">
  <div class="container">
    <div class="row g-3">
      <div class="col-12 col-sm-6 col-lg-3" data-reveal="up">
        <div class="perk"><i class="bi bi-clipboard-plus"></i><div><h6>1. Build a list</h6><span>Add products and quantities</span></div></div>
      </div>
      <div class="col-12 col-sm-6 col-lg-3" data-reveal="up" style="--reveal-delay:90ms">
        <div class="perk"><i class="bi bi-send"></i><div><h6>2. Send it</h6><span>One form, no account needed</span></div></div>
      </div>
      <div class="col-12 col-sm-6 col-lg-3" data-reveal="up" style="--reveal-delay:180ms">
        <div class="perk"><i class="bi bi-receipt"></i><div><h6>3. Get a quote</h6><span>Within one business day</span></div></div>
      </div>
      <div class="col-12 col-sm-6 col-lg-3" data-reveal="up" style="--reveal-delay:270ms">
        <div class="perk"><i class="bi bi-truck"></i><div><h6>4. We supply</h6><span>Pan-India dispatch</span></div></div>
      </div>
    </div>
  </div>
</section>

@endsection
