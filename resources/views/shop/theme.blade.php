<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $metaTitle ?? ($shopSettings['title'] ?? 'Shiksha') }}</title>
    <meta name="description" content="{{ $metaDescription ?? ($shopSettings['description'] ?? '') }}">
    @if(!empty($metaKeywords ?? ($shopSettings['keywords'] ?? null)))
        <meta name="keywords" content="{{ $metaKeywords ?? $shopSettings['keywords'] }}">
    @endif
    <meta name="theme-color" content="{{ $shopSettings['primary'] ?? '#e6007e' }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:title" content="{{ $metaTitle ?? ($shopSettings['title'] ?? 'Shiksha') }}">
    <meta property="og:description" content="{{ $metaDescription ?? ($shopSettings['description'] ?? '') }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ asset('theme/img/logo.png') }}">
    @if(!empty($shopSettings['favicon']))
        <link rel="icon" href="{{ str_starts_with($shopSettings['favicon'], 'http') ? $shopSettings['favicon'] : asset('storage/'.$shopSettings['favicon']) }}">
    @else
        <link rel="icon" href="{{ asset('theme/img/favicon.svg') }}" type="image/svg+xml">
    @endif
    <link rel="apple-touch-icon" href="{{ asset('theme/img/logo.png') }}">

    <link rel="preload" href="{{ asset('theme/fonts/outfit-1.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ asset('theme/fonts/plusjakartasans-3.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="stylesheet" href="{{ asset('theme/fonts/fonts.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/vendor/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/vendor/bootstrap-icons/font/bootstrap-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/css/style.css') }}?v={{ filemtime(public_path('theme/css/style.css')) }}">
    <style>
        .footer-social{display:flex;gap:.5rem;flex-wrap:wrap}
        .footer-social a{display:grid;place-items:center;width:38px;height:38px;border-radius:50%;
            background:rgba(255,255,255,.08);color:#fff;font-size:1rem;transition:.3s var(--sl-ease)}
        .footer-social a:hover{background:var(--sl-brand);transform:translateY(-2px)}
        .product-price{font-weight:700;color:var(--sl-ink)}
        .chip-static{display:inline-flex;align-items:center;padding:.35rem .75rem;border-radius:999px;
            font-size:.82rem;font-weight:600;background:var(--sl-paper-3);color:var(--sl-body);border:1px solid var(--sl-line)}
        .pd-description{color:var(--sl-body);line-height:1.75}
        .cart-line{display:flex;gap:1rem;align-items:center;padding:1rem 1.25rem;border-bottom:1px solid var(--sl-line)}
        .cart-line:last-child{border-bottom:0}
        .cart-line img{width:74px;height:74px;object-fit:cover;border-radius:14px;background:#fff;flex:none}
        .cms-content{color:var(--sl-body);line-height:1.8}
        .cms-content h1,.cms-content h2,.cms-content h3,.cms-content h4{color:var(--sl-ink);margin:1.6rem 0 .8rem;line-height:1.25}
        .cms-content h1:first-child,.cms-content h2:first-child,.cms-content h3:first-child{margin-top:0}
        .cms-content p{margin:0 0 1rem}
        .cms-content ul,.cms-content ol{margin:0 0 1rem 1.25rem}
        .cms-content li{margin-bottom:.4rem}
        .cms-content a{color:var(--sl-brand);text-decoration:underline}
        .cms-content img{max-width:100%;height:auto;border-radius:var(--sl-r)}
        /* Cart feedback toast (shop.js) — override Bootstrap's hidden .toast */
        .shop-toast{display:block !important;position:fixed;left:50%;bottom:28px;
            transform:translateX(-50%) translateY(24px);z-index:3000;max-width:90vw;
            padding:.85rem 1.4rem;border-radius:999px;color:#fff;font-weight:600;font-size:.95rem;
            box-shadow:var(--sl-shadow-lg);opacity:0;visibility:hidden;pointer-events:none;
            transition:opacity .3s var(--sl-ease),transform .3s var(--sl-ease),visibility .3s}
        .shop-toast.show{opacity:1;visibility:visible;transform:translateX(-50%) translateY(0)}
        .btn.is-loading{pointer-events:none;opacity:.85}
        /* Side cart drawer */
        .side-cart-backdrop{position:fixed;inset:0;background:rgba(23,16,31,.5);z-index:2100;
            opacity:0;visibility:hidden;transition:opacity .35s var(--sl-ease),visibility .35s}
        .side-cart-backdrop.is-open{opacity:1;visibility:visible}
        .side-cart{position:fixed;top:0;right:0;height:100%;width:min(420px,92vw);background:var(--sl-paper);
            z-index:2200;display:flex;flex-direction:column;box-shadow:var(--sl-shadow-lg);
            transform:translateX(100%);visibility:hidden;transition:transform .35s var(--sl-ease-out),visibility .35s}
        .side-cart.is-open{transform:none;visibility:visible}
        .side-cart-head{display:flex;align-items:center;justify-content:space-between;
            padding:1.15rem 1.4rem;border-bottom:1px solid var(--sl-line);flex:none}
        .side-cart-head h5{font-size:1.15rem}
        .side-cart-close{width:38px;height:38px;border:0;border-radius:50%;background:var(--sl-paper-3);
            color:var(--sl-ink);display:grid;place-items:center;cursor:pointer;transition:.25s var(--sl-ease)}
        .side-cart-close:hover{background:var(--sl-brand);color:#fff}
        .side-cart-scroll{flex:1;min-height:0;display:flex;flex-direction:column}
        .side-cart-body{flex:1;overflow-y:auto;padding:.4rem 1.4rem}
        .side-cart-item{display:flex;gap:.85rem;align-items:flex-start;padding:.95rem 0;border-bottom:1px solid var(--sl-line)}
        .side-cart-item img{width:60px;height:60px;object-fit:cover;border-radius:12px;background:#fff;flex:none}
        .sci-info{flex:1;min-width:0}
        .sci-title{font-weight:600;color:var(--sl-ink);text-decoration:none;display:block;line-height:1.3}
        .sci-title:hover{color:var(--sl-brand)}
        .sci-meta{font-size:.85rem;color:var(--sl-muted);margin-top:.15rem}
        .sci-remove{flex:none;background:none;border:0;color:var(--sl-muted);width:32px;height:32px;
            border-radius:8px;cursor:pointer;transition:.2s var(--sl-ease)}
        .sci-remove:hover{background:var(--sl-paper-3);color:var(--sl-danger)}
        .side-cart-foot{flex:none;padding:1.15rem 1.4rem;border-top:1px solid var(--sl-line);background:var(--sl-paper-2)}
        .sc-subtotal{display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;font-size:1.1rem;color:var(--sl-ink)}
        .sc-actions{display:grid;grid-template-columns:1fr 1fr;gap:.6rem}
        .side-cart-empty{flex:1;display:grid;place-content:center;justify-items:center;text-align:center;padding:2rem;color:var(--sl-muted)}
        .side-cart-empty i{font-size:2.6rem;margin-bottom:.6rem}
    </style>
    @stack('head')
</head>
<body
    data-shop-cart-add="{{ route('shop.cart.add') }}"
    data-shop-cart-update="{{ route('shop.cart.update') }}"
    data-shop-cart-remove="{{ route('shop.cart.remove') }}"
    data-shop-cart-clear="{{ route('shop.cart.clear') }}"
    data-shop-cart-mini="{{ route('shop.cart.mini') }}"
>

<!-- ============================ Preloader ============================ -->
<div id="preloader">
  <div class="preloader-inner">
    <div class="preloader-bulb"></div>
    <div class="preloader-text">{{ $shopSettings['name'] ?? 'Shiksha' }}</div>
    <div class="preloader-bar"><span></span></div>
  </div>
</div>

<div id="scroll-progress"></div>
<div id="cursor-glow" aria-hidden="true"></div>

<x-shop.header
    :settings="$shopSettings"
    :categories="$navCategories ?? null"
    :cart-count="$shopCartCount ?? 0"
    :cart-items="$shopCartItems ?? []"
/>

<span
    data-flash
    @if(session('success')) data-flash-success="{{ session('success') }}" @endif
    @if(session('error')) data-flash-error="{{ session('error') }}" @endif
    hidden
></span>

<main>
    @yield('content')
</main>

<x-shop.footer
    :settings="$shopSettings"
    :categories="$navCategories ?? null"
    :pages="$footerPages ?? null"
/>

<script src="{{ asset('theme/vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('theme/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('theme/js/main.js') }}?v={{ filemtime(public_path('theme/js/main.js')) }}"></script>
<script src="{{ asset('shop_assets/js/shop.js') }}?v={{ filemtime(public_path('shop_assets/js/shop.js')) }}"></script>
@stack('scripts')
</body>
</html>
