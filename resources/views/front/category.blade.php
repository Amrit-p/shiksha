@extends('front/layout')
@section('page_title', 'Category')
@section('container')

    <style>
        /* ===== CATEGORY PRODUCT UI FIX ===== */

        .aa-product-catg li {
            height: 100%;
        }

        .aa-product-catg figure {
            border: 1px solid #eee;
            padding: 10px;
            background: #fff;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        /* IMAGE BOX */
        .product-img-wrap {
            width: 100%;
            height: 220px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* IMAGE */
        .product-img-wrap img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        /* TITLE */
        .aa-product-title {
            margin-top: 10px;
            min-height: 40px;
        }

        /* PRICE */
        .aa-product-price {
            font-size: 15px;
            font-weight: 600;
        }

        /* BUTTON */
        .aa-add-card-btn {
            margin-top: auto;
            text-align: center;
        }

        @media (max-width: 768px) {
            .product-img-wrap {
                height: 180px;
            }
        }
    </style>

    <section id="aa-product-category">
        <div class="container">
            <div class="row">

                {{-- ================= PRODUCT LIST ================= --}}
                <div class="col-lg-12 col-md-12 col-sm-12 ">

                    <div class="aa-product-catg-content">

                        {{-- SORT --}}
                        <div class="aa-product-catg-head">
                            <div class="aa-product-catg-head-left">
                                <form class="aa-sort-form">
                                    <label>Sort by</label>
                                    <select id="sort_by_value" onchange="sort_by()">
                                        <option value="">Default</option>
                                        <option value="name">Name</option>
                                        <option value="price_desc">Price - Desc</option>
                                        <option value="price_asc">Price - Asc</option>
                                        <option value="date">Date</option>
                                    </select>
                                </form>
                                {{ $sort_txt }}
                            </div>
                            <div class="aa-product-catg-head-right">
                                <a id="grid-catg" href="#"><span class="fa fa-th"></span></a>
                                <a id="list-catg" href="#"><span class="fa fa-list"></span></a>
                            </div>
                        </div>

                        {{-- PRODUCTS --}}
                        <div class="aa-product-catg-body">
                            <ul class="aa-product-catg">

                                @if (isset($product[0]))
                                    @foreach ($product as $item)
                                        @php
                                            $variant = $product_attr[$item->id][0] ?? null;
                                        @endphp

                                        <li>
                                            <figure>

                                                {{-- IMAGE --}}
                                                <a class="aa-product-img" href="{{ route('front.product', $item->slug) }}">
                                                    <div class="product-img-wrap">
                                                        <img src="{{ $item->image ? asset('storage/' . $item->image) : asset('front-assets/img/no-image.png') }}"
                                                            alt="{{ $item->title }}">
                                                    </div>
                                                </a>

                                                {{-- ADD TO CART --}}
                                                {{-- @if ($variant)
                                                <a class="aa-add-card-btn"
                                                   href="javascript:void(0)"
                                                   onclick="home_add_to_cart('{{ $item->id }}','{{ $variant->id }}')">
                                                    <span class="fa fa-shopping-cart"></span>
                                                    Add To Cart
                                                </a>
                                            @endif --}}

                                                {{-- INFO --}}
                                                <figcaption>
                                                    <h4 class="aa-product-title">
                                                        <a href="{{ route('front.product', $item->slug) }}">
                                                            {{ $item->title }}
                                                        </a>
                                                    </h4>

                                                    {{-- @if ($variant)
                                                    <span class="aa-product-price">
                                                        ₹ {{ number_format($variant->price,2) }}
                                                    </span>
                                                    <span class="aa-product-price">
                                                        <del>₹ {{ number_format($variant->mrp,2) }}</del>
                                                    </span>
                                                @endif --}}
                                                </figcaption>

                                            </figure>
                                        </li>
                                    @endforeach
                                @else
                                    <li>No products found</li>
                                @endif

                            </ul>
                        </div>
                    </div>
                    <div class="mt-4 d-flex justify-content-center">
    {{ $product->links() }}
</div>

                </div>


                {{-- ================= SIDEBAR ================= --}}
                {{-- <div class="col-lg-3 col-md-3 col-sm-4 col-md-pull-9">
                    <aside class="aa-sidebar"> --}}

                        {{-- PRICE FILTER --}}
                        {{-- <div class="aa-sidebar-widget">
                            <h3>Shop By Price</h3> --}}
                            {{-- <form>
                            <input type="number" id="min_price" placeholder="Min">
                            <input type="number" id="max_price" placeholder="Max">
                            <button type="button"
                                    class="aa-filter-btn"
                                    onclick="sort_price_filter()">
                                Filter
                            </button>
                        </form> --}}
                            <!-- price range -->
                            {{-- <div class="aa-sidebar-price-range">
                                <form action="">
                                    <div id="skipstep" class="noUi-target noUi-ltr noUi-horizontal noUi-background">
                                    </div>
                                    <span id="skip-value-lower" class="example-val">30.00</span>
                                    <span id="skip-value-upper" class="example-val">100.00</span>
                                    <button class="aa-filter-btn" type="button"
                                        onclick="sort_price_filter()">Filter</button>
                                </form>
                            </div>
                        </div>

                    </aside>
                </div> --}}

            </div>
        </div>
    </section>

    {{-- ADD TO CART FORM --}}
    <form id="frmAddToCart">
        @csrf
        <input type="hidden" id="product_id" name="product_id">
        <input type="hidden" id="variant_id" name="variant_id">
        <input type="hidden" id="pqty" name="pqty" value="1">
    </form>

    {{-- FILTER FORM --}}
    <form id="categoryFilter">
        <input type="hidden" id="sort" name="sort" value="{{ $sort }}">
        <input type="hidden" id="filter_price_start" name="filter_price_start" value="{{ $filter_price_start }}">
        <input type="hidden" id="filter_price_end" name="filter_price_end" value="{{ $filter_price_end }}">
    </form>


    <script>
        /* ===============================
       SORT BY
    ================================ */
        function sort_by() {
            const sortVal = document.getElementById('sort_by_value').value;
            document.getElementById('sort').value = sortVal;
            document.getElementById('categoryFilter').submit();
        }

        /* ===============================
           PRICE FILTER
        ================================ */
        function sort_price_filter() {

            const minPrice = document.getElementById('min_price')?.value || '';
            const maxPrice = document.getElementById('max_price')?.value || '';

            if (minPrice !== '' && maxPrice !== '' && Number(minPrice) > Number(maxPrice)) {
                alert('Min price cannot be greater than Max price');
                return;
            }

            document.getElementById('filter_price_start').value = minPrice;
            document.getElementById('filter_price_end').value = maxPrice;

            document.getElementById('categoryFilter').submit();
        }

        /* ===============================
           ADD TO CART
           (Simple + Variant Compatible)
        ================================ */
        function home_add_to_cart(productId, variantId) {

            if (!productId || !variantId) {
                alert('Invalid product');
                return;
            }

            document.getElementById('product_id').value = productId;
            document.getElementById('variant_id').value = variantId;
            document.getElementById('pqty').value = 1;

            fetch("{{ url('/add-to-cart') }}", {
                    method: "POST",
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        variant_id: variantId,
                        qty: 1
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === true) {
                        alert('Product added to cart');
                    } else {
                        alert(data.message ?? 'Something went wrong');
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Server error');
                });
        }
    </script>

@endsection
