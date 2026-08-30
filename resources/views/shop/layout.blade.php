<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $metaTitle ?? ($shopSettings['title'] ?? 'Shiksha') }}</title>
    <meta name="description" content="{{ $metaDescription ?? ($shopSettings['description'] ?? '') }}">
    @if(!empty($metaKeywords ?? $shopSettings['keywords']))
        <meta name="keywords" content="{{ $metaKeywords ?? $shopSettings['keywords'] }}">
    @endif
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:title" content="{{ $metaTitle ?? ($shopSettings['title'] ?? 'Shiksha') }}">
    <meta property="og:description" content="{{ $metaDescription ?? ($shopSettings['description'] ?? '') }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ $shopSettings['logo'] ?? asset('front_assets/img/logo.png') }}">
    @if(!empty($shopSettings['favicon']))
        <link rel="icon" href="{{ str_starts_with($shopSettings['favicon'], 'http') ? $shopSettings['favicon'] : asset('storage/'.$shopSettings['favicon']) }}">
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,700;1,9..40,400&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('shop_assets/css/style.css') }}?v=4">
    <style>
        :root {
            --brand-pink: {{ $shopSettings['primary'] ?? '#E6007E' }};
            --brand-blue: {{ $shopSettings['secondary'] ?? '#00ADEF' }};
        }
    </style>
    @stack('head')
</head>
<body
    data-shop-cart-add="{{ route('shop.cart.add') }}"
    data-shop-cart-update="{{ route('shop.cart.update') }}"
    data-shop-cart-remove="{{ route('shop.cart.remove') }}"
    data-shop-cart-clear="{{ route('shop.cart.clear') }}"
>
<div class="shop-shell">
    <div class="shop-topbar">
        <div class="wrap">
            <div>
                @if(!empty($shopSettings['phone']))
                    <span><i class="fa-solid fa-phone"></i> {{ $shopSettings['phone'] }}</span>
                @endif
                @if(!empty($shopSettings['email']))
                    <span style="margin-left:1rem;"><i class="fa-solid fa-envelope"></i> {{ $shopSettings['email'] }}</span>
                @endif
            </div>
            <div>Browse products · Build enquiry · We contact you</div>
        </div>
    </div>

    <header class="shop-header">
        <div class="wrap">
            <a class="brand" href="{{ route('shop.home') }}">
                <img src="{{ $shopSettings['logo'] ?? asset('front_assets/img/logo.png') }}" alt="{{ $shopSettings['name'] ?? 'Shiksha' }}">
                <span class="brand-name">{{ strtoupper($shopSettings['name'] ?? 'Shiksha') }}</span>
            </a>

            <nav class="nav nav-desktop" aria-label="Primary">
                <a href="{{ route('shop.home') }}" class="{{ request()->routeIs('shop.home') ? 'active' : '' }}">Home</a>
                <a href="{{ route('shop.products') }}" class="{{ request()->routeIs('shop.products*') || request()->routeIs('shop.product') || request()->routeIs('shop.search') ? 'active' : '' }}">Products</a>
                <a href="{{ route('shop.about') }}" class="{{ request()->routeIs('shop.about') ? 'active' : '' }}">About</a>
                <a href="{{ route('shop.contact') }}" class="{{ request()->routeIs('shop.contact') ? 'active' : '' }}">Contact</a>
                <a href="{{ route('shop.enquiry.create') }}">Enquiry</a>
            </nav>

            <div class="header-actions">
                <form class="search-form" action="{{ route('shop.search') }}" method="GET">
                    <input type="search" name="q" value="{{ request('q') }}" placeholder="Search products, SKU..." aria-label="Search">
                    <button type="submit" aria-label="Search"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>
                <a class="icon-btn" href="{{ route('shop.cart') }}" aria-label="Enquiry cart">
                    <i class="fa-solid fa-bag-shopping"></i>
                    <span class="cart-badge" data-cart-count @if(($shopCartCount ?? 0) < 1) hidden @endif>{{ $shopCartCount ?? 0 }}</span>
                </a>
                <button class="mobile-toggle" type="button" data-nav-toggle aria-label="Menu" aria-expanded="false"><i class="fa-solid fa-bars"></i></button>
            </div>
        </div>
        <div class="wrap search-form-mobile">
            <form class="search-form" action="{{ route('shop.search') }}" method="GET">
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Search products, SKU..." aria-label="Search">
                <button type="submit" aria-label="Search"><i class="fa-solid fa-magnifying-glass"></i></button>
            </form>
        </div>
    </header>

    {{-- Mobile nav lives outside header so backdrop-filter does not trap position:fixed --}}
    <div class="nav-backdrop" data-nav-backdrop></div>
    <nav class="nav nav-drawer" data-nav aria-label="Mobile">
        <a href="{{ route('shop.home') }}" class="{{ request()->routeIs('shop.home') ? 'active' : '' }}">Home</a>
        <a href="{{ route('shop.products') }}" class="{{ request()->routeIs('shop.products*') || request()->routeIs('shop.product') || request()->routeIs('shop.search') ? 'active' : '' }}">Products</a>
        <a href="{{ route('shop.about') }}" class="{{ request()->routeIs('shop.about') ? 'active' : '' }}">About</a>
        <a href="{{ route('shop.contact') }}" class="{{ request()->routeIs('shop.contact') ? 'active' : '' }}">Contact</a>
        <a href="{{ route('shop.enquiry.create') }}">Enquiry</a>
    </nav>

    <main class="shop-main">
        @if(session('success'))
            <div class="page-wrap" style="padding-top:1rem;"><div class="alert alert-success">{{ session('success') }}</div></div>
        @endif
        @if(session('error'))
            <div class="page-wrap" style="padding-top:1rem;"><div class="alert alert-error">{{ session('error') }}</div></div>
        @endif
        @yield('content')
    </main>

    <footer class="shop-footer">
        <div class="wrap footer-grid">
            <div>
                <h4>{{ $shopSettings['name'] ?? 'Shiksha' }}</h4>
                <p>{{ $shopSettings['description'] ?? 'Professional lighting solutions.' }}</p>
                <div class="socials" style="margin-top:1rem;">
                    @foreach(['facebook','instagram','youtube','linkedin'] as $social)
                        @if(!empty($shopSettings[$social]))
                            <a href="{{ $shopSettings[$social] }}" target="_blank" rel="noopener">{{ ucfirst($social) }}</a>
                        @endif
                    @endforeach
                </div>
            </div>
            <div>
                <h4>Explore</h4>
                <p><a href="{{ route('shop.products') }}">Products</a></p>
                <p><a href="{{ route('shop.about') }}">About</a></p>
                <p><a href="{{ route('shop.contact') }}">Contact</a></p>
                <p><a href="{{ route('shop.cart') }}">Enquiry Cart</a></p>
            </div>
            <div>
                <h4>Policies</h4>
                @forelse($footerPages ?? [] as $page)
                    <p><a href="{{ route('shop.page', $page->slug) }}">{{ $page->title }}</a></p>
                @empty
                    <p><a href="{{ route('shop.page', 'privacy-policy') }}">Privacy Policy</a></p>
                @endforelse
            </div>
            <div>
                <h4>Contact</h4>
                @if(!empty($shopSettings['phone']))<p>{{ $shopSettings['phone'] }}</p>@endif
                @if(!empty($shopSettings['email']))<p>{{ $shopSettings['email'] }}</p>@endif
                @if(!empty($shopSettings['address']))<p>{{ $shopSettings['address'] }}</p>@endif
            </div>
        </div>
        <div class="wrap footer-bottom">
            <div>&copy; {{ date('Y') }} {{ $shopSettings['name'] ?? 'Shiksha' }}. All rights reserved.</div>
            <div>Built for product discovery &amp; enquiries</div>
        </div>
    </footer>
</div>
<script src="{{ asset('shop_assets/js/shop.js') }}?v=4"></script>
@stack('scripts')
</body>
</html>
