<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title')</title>
    <link href="{{asset('front_assets/css/font-awesome.css')}}" rel="stylesheet">
    <link href="{{asset('front_assets/css/bootstrap.css')}}" rel="stylesheet">
    <link href="{{asset('front_assets/css/jquery.smartmenus.bootstrap.css')}}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{asset('front_assets/css/jquery.simpleLens.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('front_assets/css/slick.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('front_assets/css/nouislider.css')}}">
    <link id="switcher" href="{{asset('front_assets/css/theme-color/default-theme.css')}}" rel="stylesheet">
    <link href="{{asset('front_assets/css/sequence-theme.modern-slide-in.css')}}" rel="stylesheet" media="all">
    <link href="{{asset('front_assets/css/style.css')}}" rel="stylesheet">

    <!-- Google Font -->
    <link href='https://fonts.googleapis.com/css?family=Lato' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Raleway' rel='stylesheet' type='text/css'>
        <script>
    var PRODUCT_IMAGE="{{asset('storage')}}";
    </script>

    <style>
:root {
    --brand-primary: #ec008c;
    --brand-secondary: #18a9e6;
    --brand-accent: #f07db7;
    --brand-dark: #232323;
}

.search-icon-inside form {
    position: relative;
    max-width: 400px;
}
.f-13{
    font-size: 12px !important;
}

.search-icon-inside input {
    width: 100%;
    padding: 12px 40px 12px 16px;
    border-radius: 30px;
    border: 1px solid #ddd;
    outline: none;
}

.search-icon-inside i {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #777;
}

/* Global search dropdown */
.global-search-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    margin-top: 4px;
    max-height: 360px;
    overflow-y: auto;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    box-shadow: 0 6px 16px rgba(0,0,0,0.12);
    z-index: 9999;
}
.global-search-dropdown .search-result-item {
    display: flex;
    align-items: center;
    padding: 10px 12px;
    border-bottom: 1px solid #eee;
    cursor: pointer;
    text-decoration: none;
    color: #333;
}
.global-search-dropdown .search-result-item:hover {
    background: #f5f5f5;
}
.global-search-dropdown .search-result-item:last-child {
    border-bottom: none;
}
.global-search-dropdown .search-result-item img {
    width: 48px;
    height: 48px;
    object-fit: cover;
    margin-right: 12px;
    border-radius: 4px;
}
.global-search-dropdown .search-result-item .search-result-info {
    flex: 1;
}
.global-search-dropdown .search-result-item .search-result-title {
    font-weight: 600;
}
.global-search-dropdown .search-result-item .search-result-cat {
    font-size: 12px;
    color: #777;
}
.global-search-dropdown .search-no-results {
    padding: 16px;
    color: #777;
    text-align: center;
}

.navbar-nav li > a:not(.has-submenu) .caret {
    display: none;
}
.cart-meta {
    font-size: 13px;
    color: #666;
}
.cart-meta span {
    display: block;
}


#aa-header .aa-header-bottom .aa-cartbox .aa-cartbox-summary {
 
    max-height: 500px;
    overflow-y: auto;
    overflow-x: hidden;
}

#aa-header .aa-header-top {
    border-top: 3px solid var(--brand-primary);
    background: linear-gradient(90deg, #ffffff 0%, #f5fbff 100%);
}

#aa-header .aa-header-top .aa-head-top-nav-right li a:hover,
#aa-header .aa-header-top .aa-head-top-nav-right li a:focus,
#aa-header .aa-header-top .cellphone span {
    color: var(--brand-secondary);
}

#aa-header .aa-header-top .cellphone p {
    color: var(--brand-dark);
}

#aa-header .aa-header-bottom {
    border-bottom: 1px solid #cfeefe;
}

#menu {
    /* background: linear-gradient(90deg, var(--brand-secondary) 0%, var(--brand-primary) 100%);4 */

        background: linear-gradient(90deg, #ec008c 0%, var(--brand-primary) 100%);
}

#menu .menu-area .navbar-default .navbar-nav li > a:hover,
#menu .menu-area .navbar-default .navbar-nav li > a:focus {
    color: #fff;
    background-color: rgba(255, 255, 255, 0.12);
}

#aa-header .aa-header-bottom .aa-cartbox .aa-cart-link .aa-cart-notify {
    color: var(--brand-accent);
    border-color: var(--brand-accent);
}

#aa-header .aa-header-bottom .aa-cartbox .aa-cart-link .aa-cart-notify:after {
    border-top-color: var(--brand-accent);
}

#aa-header .aa-header-bottom .aa-cartbox .aa-cart-link {
    color: var(--brand-primary);
}

#aa-header .aa-header-bottom .aa-cartbox .aa-cart-link .brand-cart-icon {
    color: var(--brand-primary);
    font-size: 34px;
    line-height: 1;
}

#aa-header .aa-header-bottom .aa-cartbox .aa-cartbox-summary ul li .aa-cartbox-info a {
    color: var(--brand-dark);
}

#aa-header .aa-header-bottom .aa-cartbox .aa-cartbox-summary ul li .aa-cartbox-info a:hover,
#aa-header .aa-header-bottom .aa-cartbox .aa-cartbox-summary ul li .aa-cartbox-info a:focus {
    color: var(--brand-primary);
}

.search-icon-inside input:focus {
    border-color: var(--brand-accent);
    box-shadow: 0 0 0 2px rgba(47, 143, 213, 0.14);
}

.search-icon-inside i {
    color: var(--brand-secondary);
}

.aa-primary-btn,
.aa-browse-btn,
.aa-filter-btn,
.aa-cart-view-btn {
    background-color: var(--brand-primary);
    border-color: var(--brand-primary);
    color: #fff !important;
}

.aa-primary-btn:hover,
.aa-primary-btn:focus,
.aa-browse-btn:hover,
.aa-browse-btn:focus {
    color: #fff !important;
    background-color: var(--brand-secondary);
    border-color: var(--brand-secondary);
}

.aa-footer-social a:hover,
.aa-footer-social a:focus,
#aa-footer .aa-footer-bottom .aa-footer-bottom-area > p a:hover,
#aa-footer .aa-footer-bottom .aa-footer-bottom-area > p a:focus {
    color: var(--brand-secondary);
}

/* Global brand consistency for all frontend pages */
.aa-product-catg li .aa-product-title a:hover,
.aa-product-catg li .aa-product-title a:focus,
.aa-latest-blog-single .aa-blog-info .aa-blog-title a:hover,
.aa-latest-blog-single .aa-blog-info .aa-read-mor-btn,
#aa-product-category .aa-sidebar .aa-sidebar-widget .aa-catg-nav li a:hover,
#aa-product-category .aa-sidebar .aa-sidebar-widget .aa-catg-nav li a:focus,
#aa-product-details .aa-product-details-area .aa-product-details-content .aa-product-view-content .aa-prod-quantity .aa-prod-category a {
    color: var(--brand-secondary);
}

a {
    color: inherit;
}

a:hover,
a:focus {
    color: var(--brand-secondary);
}

h2,
.aa-product-catg li .aa-product-price,
#aa-checkout .checkout-area .checkout-right h4,
#aa-product-category .aa-sidebar .aa-sidebar-widget h3,
#aa-blog-archive .aa-blog-archive-area .aa-blog-sidebar .aa-sidebar-widget h3 {
    color: var(--brand-primary);
}

.aa-products-tab li a:hover,
.aa-products-tab li a:focus,
.aa-products-tab li.active a,
.aa-products-tab li.active a:hover,
.aa-products-tab li.active a:focus {
    border-bottom-color: var(--brand-secondary) !important;
    color: var(--brand-secondary);
}

.aa-product-catg li .aa-product-hvr-content a:hover,
.aa-product-catg li .aa-product-hvr-content a:focus,
.aa-product-catg li figure .aa-add-card-btn:hover,
.aa-product-catg li figure .aa-add-card-btn:focus {
    color: var(--brand-accent);
}

.aa-secondary-btn {
    color: var(--brand-secondary);
}

.aa-secondary-btn:hover,
.aa-secondary-btn:focus,
.aa-add-to-cart-btn:hover,
.aa-add-to-cart-btn:focus {
    color: var(--brand-primary);
    border-color: var(--brand-primary);
}

.scrollToTop,
#wpf-loader-two,
#wpf-loader-two .wpf-loader-two-inner:before,
#wpf-loader-two .wpf-loader-two-inner:after {
    background-color: var(--brand-primary);
    border-color: var(--brand-primary);
}

.scrollToTop:hover,
.scrollToTop:focus {
    background-color: var(--brand-secondary);
    border-color: var(--brand-secondary);
    color: #fff;
}

.scrollToTop {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #fff;
}

.scrollToTop .fa {
    color: #fff;
    line-height: 1;
}

/* Slider arrows: alignment + branded colors */
#aa-slider .aa-slider-area .seq .seq-prev,
#aa-slider .aa-slider-area .seq .seq-next {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background-color: var(--brand-primary);
    border: 1px solid var(--brand-primary);
    color: #fff;
}

#aa-slider .aa-slider-area .seq .seq-prev:hover,
#aa-slider .aa-slider-area .seq .seq-prev:focus,
#aa-slider .aa-slider-area .seq .seq-next:hover,
#aa-slider .aa-slider-area .seq .seq-next:focus {
    background-color: var(--brand-secondary);
    border-color: var(--brand-secondary);
    color: #fff;
}

#aa-slider .aa-slider-area .seq .seq-prev .fa,
#aa-slider .aa-slider-area .seq .seq-next .fa {
    color: #fff;
    font-size: 24px;
    line-height: 1;
}

.pagination > .active > a,
.pagination > .active > span,
.pagination > .active > a:hover,
.pagination > .active > span:hover,
.pagination > .active > a:focus,
.pagination > .active > span:focus {
    background-color: var(--brand-secondary);
    border-color: var(--brand-secondary);
}

.pagination > li > a:hover,
.pagination > li > span:hover,
.pagination > li > a:focus,
.pagination > li > span:focus {
    color: var(--brand-secondary);
}

.pagination > .disabled > span,
.pagination > .disabled > span:hover,
.pagination > .disabled > span:focus,
.pagination > .disabled > a,
.pagination > .disabled > a:hover,
.pagination > .disabled > a:focus {
    color: #b05f8f;
}

input:focus,
textarea:focus,
select:focus,
.form-control:focus {
    border-color: var(--brand-accent) !important;
    box-shadow: 0 0 0 2px rgba(47, 143, 213, 0.12);
}

.badge,
.label {
    background-color: var(--brand-secondary);
}

.noUi-connect {
    background: var(--brand-accent) !important;
}

.aa-cartbox-checkout {
    background-color: var(--brand-primary) !important;
    border-color: var(--brand-primary) !important;
    color: #fff !important;
}

.aa-cartbox-checkout:hover,
.aa-cartbox-checkout:focus {
    color: #fff !important;
    background-color: var(--brand-secondary) !important;
    border-color: var(--brand-secondary) !important;
}

/* Legacy frontend classes mapped to brand palette */
.left_cat_active {
    color: var(--brand-primary) !important;
}

.order_id_btn {
    background-color: var(--brand-primary) !important;
}

.aa-product-view-content .aa-prod-view-size a:hover,
.aa-product-view-content .aa-prod-view-size a:focus,
#aa-product-details .aa-product-details-area .aa-product-details-content .aa-product-view-content .aa-prod-view-size a:hover,
#aa-product-details .aa-product-details-area .aa-product-details-content .aa-product-view-content .aa-prod-view-size a:focus {
    background-color: var(--brand-primary);
    border-color: var(--brand-primary);
    color: #fff;
}

#aa-product-category .aa-sidebar .aa-sidebar-widget h3,
#aa-blog-archive .aa-blog-archive-area .aa-blog-sidebar .aa-sidebar-widget h3 {
    border-bottom-color: var(--brand-primary);
}

#aa-product-category .aa-sidebar .aa-sidebar-widget .tag-cloud a:hover,
#aa-product-category .aa-sidebar .aa-sidebar-widget .tag-cloud a:focus,
#aa-blog-archive .aa-blog-archive-area .aa-blog-sidebar .aa-sidebar-widget .tag-cloud a:hover,
#aa-blog-archive .aa-blog-archive-area .aa-blog-sidebar .aa-sidebar-widget .tag-cloud a:focus {
    background-color: var(--brand-secondary);
    color: #fff;
}

.slick-prev,
.slick-next {
    background-color: var(--brand-secondary);
}

.aa-latest-blog-single .aa-blog-img:hover .aa-blog-img-caption,
#aa-testimonial .aa-testimonial-area .aa-testimonial-slider li.slick-active {
    background-color: var(--brand-primary);
}

.navbar-default .navbar-toggle:hover,
.navbar-default .navbar-toggle:focus {
    background-color: var(--brand-secondary);
    border-color: var(--brand-secondary);
    color: #fff;
}

#aa-header .aa-header-bottom .aa-cartbox .aa-cartbox-summary ul li .aa-remove-product:hover,
#aa-header .aa-header-bottom .aa-cartbox .aa-cartbox-summary ul li .aa-remove-product:focus {
    border-color: var(--brand-primary);
    color: var(--brand-primary);
}

#aa-error .aa-error-area,
#aa-error .aa-error-area h2 {
    border-color: var(--brand-primary);
}

#aa-error .aa-error-area a:hover,
#aa-error .aa-error-area a:focus {
    color: var(--brand-secondary);
    border-color: var(--brand-secondary);
}

.global-search-dropdown .search-result-item:hover {
    background: #f2fbff;
}

.global-search-dropdown .search-result-item .search-result-title {
    color: var(--brand-dark);
}

.global-search-dropdown .search-result-item .search-result-cat {
    color: #6a6a6a;
}

.aa-header-top .aa-head-top-nav-right li a {
    color: var(--brand-dark);
}

.aa-header-top .aa-head-top-nav-right li a:hover,
.aa-header-top .aa-head-top-nav-right li a:focus {
    color: var(--brand-primary);
}

#menu .menu-area .navbar-default .navbar-nav > li > a {
    color: #fff;
}

#menu .menu-area .navbar-default .navbar-nav .dropdown-menu li a {
    color: var(--brand-dark);
}

#menu .menu-area .navbar-default .navbar-nav .dropdown-menu li a:hover,
#menu .menu-area .navbar-default .navbar-nav .dropdown-menu li a:focus {
    background-color: var(--brand-secondary);
    color: #fff;
}

#aa-footer .aa-footer-top {
    border-top: 3px solid var(--brand-secondary);
}

#aa-footer .aa-footer-bottom {
    border-top: 1px solid #f1d6e8;
}

#aa-footer .aa-footer-widget h3 {
    color: var(--brand-dark);
}

#aa-footer .aa-footer-social a {
    color: var(--brand-primary);
}

#aa-footer .aa-footer-social a:hover,
#aa-footer .aa-footer-social a:focus {
    color: var(--brand-secondary);
}

#aa-footer .aa-footer-widget address .fa {
    color: var(--brand-secondary);
}

#aa-footer .aa-footer-bottom .aa-footer-bottom-area > p {
    color: #555;
}

.aa-product-catg li figure {
    border-color: #f0e8ee;
}

.aa-product-catg li .aa-product-title a {
    color: var(--brand-dark);
}

.aa-product-catg li .aa-product-title a:hover,
.aa-product-catg li .aa-product-title a:focus {
    color: var(--brand-primary);
}

.aa-add-to-cart-btn {
    border-color: #d8d8d8;
}

.aa-add-to-cart-btn:hover,
.aa-add-to-cart-btn:focus {
    background-color: var(--brand-primary);
    color: #fff;
    border-color: var(--brand-primary);
}

.aa-product-catg li .aa-price-block .aa-product-price {
    color: var(--brand-primary);
}

.aa-product-catg li .aa-price-block .aa-product-price del {
    color: #888;
}

.aa-product-catg li .aa-product-hvr-content a {
    color: #666;
}

.aa-product-catg li .aa-product-hvr-content a:hover,
.aa-product-catg li .aa-product-hvr-content a:focus {
    color: var(--brand-secondary);
}

.aa-sidebar-widget .aa-catg-nav li a,
.aa-footer-widget .aa-footer-nav li a {
    color: #555;
}

.aa-sidebar-widget .aa-catg-nav li a:hover,
.aa-footer-widget .aa-footer-nav li a:hover {
    color: var(--brand-primary);
}

#aa-support .aa-support-area .aa-support-single span {
    color: var(--brand-secondary);
}

#aa-support .aa-support-area .aa-support-single h4 {
    color: var(--brand-dark);
}

.aa-blog-details #respond .form-submit input,
#aa-contact .aa-contact-area .aa-contact-address .aa-contact-address-left .comments-form button {
    background-color: var(--brand-primary);
    border-color: var(--brand-primary);
    color: #fff;
}

.aa-blog-details #respond .form-submit input:hover,
#aa-contact .aa-contact-area .aa-contact-address .aa-contact-address-left .comments-form button:hover {
    background-color: var(--brand-secondary);
    border-color: var(--brand-secondary);
    color: #fff !important;
}

.aa-blog-details .aa-blog-navigation .aa-blog-prev,
.aa-blog-details .aa-blog-navigation .aa-blog-next {
    background-color: var(--brand-primary);
    border-color: var(--brand-primary);
}

.aa-blog-details .aa-blog-navigation .aa-blog-prev:hover,
.aa-blog-details .aa-blog-navigation .aa-blog-next:hover {
    background-color: var(--brand-secondary);
    border-color: var(--brand-secondary);
}

</style>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->


  </head>
  <body class="productPage">
   <!-- wpf loader Two -->
    <div id="wpf-loader-two">
      <div class="wpf-loader-two-inner">
        <span>Loading</span>
      </div>
    </div>
    <!-- / wpf loader Two -->
  <!-- SCROLL TOP BUTTON -->
    <a class="scrollToTop" href="#"><i class="fa fa-chevron-up"></i></a>
  <!-- END SCROLL TOP BUTTON -->


  <!-- Start header section -->
  <header id="aa-header">
    <!-- start header top  -->
    <div class="aa-header-top">
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <div class="aa-header-top-area">
              <!-- start header top left -->
              <div class="aa-header-top-left">

                <!-- start cellphone -->
                <div class="cellphone hidden-xs">
                  <p><span class="fa fa-phone"></span>+91-9876543210</p>
                </div>
                <!-- / cellphone -->
              </div>
              <!-- / header top left -->
              <div class="aa-header-top-right">
                <ul class="aa-head-top-nav-right">
                  <li><a href="{{route('front.order')}}">My Order</a></li>

                  <li class="hidden-xs"><a href="{{route('front.view.cart')}}">My Cart</a></li>
                  {{-- <li><a href="" data-toggle="modal" data-target="#login-modal">Logout</a></li> --}}
                  <li><a href="{{route('logout') }}">Logout</a></li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- / header top  -->

    <!-- start header bottom  -->
    <div class="aa-header-bottom">
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <div class="aa-header-bottom-area">
              <!-- logo  -->
              <div class="aa-logo">
                <a href="{{route('front.index')}}">
                <img src="{{ asset('front_assets/img/logo.png') }}" alt="Shiksha Lights logo" style="width: 96px; height: 68px; object-fit: contain; margin-left: 8px;" />
                </a>
              </div>





              <!-- / logo  -->
               <!-- cart box -->
              {{-- <div class="aa-cartbox">
                <a class="aa-cart-link" href="#">
                  <span class="fa fa-shopping-basket"></span>
                  <span class="aa-cart-title">SHOPPING CART</span>
                  <span class="aa-cart-notify">2</span>
                </a>
                <div class="aa-cartbox-summary">
                  <ul>
                    <li>
                      <a class="aa-cartbox-img" href="#"><img src="{{asset('front_assets/img/woman-small-2.jpg')}}" alt="img"></a>
                      <div class="aa-cartbox-info">
                        <h4><a href="#">Product Name</a></h4>
                        <p>1 x $250</p>
                      </div>
                      <a class="aa-remove-product" href="#"><span class="fa fa-times"></span></a>
                    </li>
                    <li>
                      <a class="aa-cartbox-img" href="#"><img src="{{asset('front_assets/img/woman-small-1.jpg')}}" alt="img"></a>
                      <div class="aa-cartbox-info">
                        <h4><a href="#">Product Name</a></h4>
                        <p>1 x $250</p>
                      </div>
                      <a class="aa-remove-product" href="#"><span class="fa fa-times"></span></a>
                    </li>
                    <li>
                      <span class="aa-cartbox-total-title">
                        Total
                      </span>
                      <span class="aa-cartbox-total-price">
                        $500
                      </span>
                    </li>
                  </ul>
                  <a class="aa-cartbox-checkout aa-primary-btn" href="javascript:void(0)">Checkout</a>
                </div>
              </div> --}}
              <!-- / cart box -->



               <!-- cart box -->
              @php
              $getAddToCartTotalItem=getAddToCartTotalItem();
              $totalCartItem=count($getAddToCartTotalItem);
              // dd($getAddToCartTotalItem);
              $totalPrice=0;
              @endphp
              <div class="aa-cartbox">
                {{-- <a class="aa-cart-link" href="#" id="cartBox">
                  <span class="fa fa-shopping-basket"></span>
                  <span class="aa-cart-title">SHOPPING CART</span>
                  <span class="aa-cart-notify">{{$totalCartItem}}</span>
                </a> --}}


               <a class="aa-cart-link" href="#" id="cartBox">
                  <span class="fa fa-shopping-cart brand-cart-icon" aria-hidden="true"></span>
                  <!-- <span class="aa-cart-title">SHOPPING CART</span> -->
                  <span class="aa-cart-notify">{{$totalCartItem}}</span>
                </a>
                <div class="aa-cartbox-summary">
               @if($totalCartItem>0)

                  <ul>
                    @foreach($getAddToCartTotalItem as $cartItem)

                    @php


                  if($cartItem->product_type=='1'|| $cartItem->product_type==1){

                      $productPrice=$cartItem->product_sale_price > 0  && $cartItem->product_sale_price < $cartItem->product_price ?$cartItem->product_sale_price:$cartItem->product_price;
                      $totalPrice=$totalPrice+($cartItem->qty*$productPrice);
                  }else{
                        if (!empty($cartItem->is_por) && $cartItem->is_por == 1) {
                                $price = $cartItem->price; // price from carts table
                                $totalPrice=$totalPrice+($cartItem->qty*$price);

                         } else {
                            $attributePrice=$cartItem->variant_attribute_sell_price > 0  && $cartItem->variant_attribute_sell_price < $cartItem->variant_attribute_mrp ?$cartItem->variant_attribute_sell_price:$cartItem->variant_attribute_mrp;
                            $totalPrice=$totalPrice+($cartItem->qty*$attributePrice);
                          }
                    
                  }
                    @endphp
                    <li>
                      <a class="aa-cartbox-img" href="#"><img src="{{asset('storage/'.$cartItem->image)}}" alt="img"></a>
                      <div class="aa-cartbox-info">
                        <small><a href="#">{{ucfirst($cartItem->product_title)}}</a></small>
                        @if(!empty($cartItem->product_code_type_name))
                            <br><small class=" f-13">
                                <strong>Type:</strong> {{ $cartItem->product_code_type_name }}
                            </small>
                        @endif
                        @if(!empty($cartItem->variant_name))
                            <br><small class="cart-variant f-13">
                                <strong>Variant:</strong> {{ $cartItem->variant_name }}
                            </small>
                        @endif

                        @if(!empty($cartItem->attribute_name))
                            <br><small class="f-13">
                                <strong>{{ $cartItem->attribute_name }}:</strong>
                                {{ $cartItem->attribute_option_name }}
                            </small>
                        @endif


                        <p class="f-13">

                          @if ($cartItem->product_type=='1'|| $cartItem->product_type==1)
                              @php
                                  $price=$cartItem->product_sale_price > 0  && $cartItem->product_sale_price < $cartItem->product_price ?$cartItem->product_sale_price:$cartItem->product_price;
                              @endphp

                          @else
                              @php 

                                    if (!empty($cartItem->is_por) && $cartItem->is_por == 1) {
                                          $price = $cartItem->price; // price from carts table
                                    } else {
                                            $price=$cartItem->variant_attribute_sell_price > 0  && $cartItem->variant_attribute_sell_price < $cartItem->variant_attribute_mrp ?$cartItem->variant_attribute_sell_price:$cartItem->variant_attribute_mrp;

                                    }
                                    // $price=$cartItem->variant_attribute_sell_price > 0  && $cartItem->variant_attribute_sell_price < $cartItem->variant_attribute_mrp ?$cartItem->variant_attribute_sell_price:$cartItem->variant_attribute_mrp;
                              @endphp
                          @endif
                          {{$cartItem->qty}} * Rs {{$price}}
                        </p>
                      </div>
                    </li>
                    @endforeach
                    <li>
                      <span class="aa-cartbox-total-title">
                        Total
                      </span>
                      <span class="aa-cartbox-total-price">
                        Rs {{round($totalPrice,2)}}
                      </span>
                    </li>
                  </ul>
                  <a class="aa-cartbox-checkout aa-primary-btn" href="{{ route('front.view.cart') }}">View Cart</a>
                  <a class="aa-cartbox-checkout aa-primary-btn" href="{{ route('front.checkout') }}">Checkout</a>

                @endif
                </div>
              </div>
              <!-- / cart box -->





              <!-- search box (global product search) -->
              <div class="aa-search-box search-icon-inside global-search-wrap" style="position: relative;">
                  <form id="global-search-form" action="{{ route('front.search') }}" method="get" role="search">
                      <i class="fa fa-search"></i>
                      <input type="text" name="q" id="global-search-input" placeholder="Search Shiksha Lights…" autocomplete="off" />
                  </form>
                  <div id="global-search-dropdown" class="global-search-dropdown" style="display: none;"></div>
              </div>

              <!-- / search box -->
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- / header bottom  -->
  </header>
  <!-- / header section -->
  <!-- menu -->
  <section id="menu">
    <div class="container">
      <div class="menu-area">
        <!-- Navbar -->
        <div class="navbar navbar-default" role="navigation">
          <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
          </div>
          <div class="navbar-collapse collapse">

              {!!getTopNavCat()!!}

            <!-- Left nav -->
            {{--
            <ul class="nav navbar-nav">
              <li><a href="{{url('/')}}">Home</a></li>


              <li>
                <a href="#">Men <span class="caret"></span></a>
                <ul class="dropdown-menu">
                  <li><a href="#">Jeans</a></li>
                  <li><a href="#">Trousers</a></li>
                  <li><a href="#">And more.. <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                      <li><a href="#">Sleep Wear</a></li>
                      <li><a href="#">Sandals</a></li>
                      <li><a href="#">Loafers</a></li>
                    </ul>
                  </li>
                </ul>
              </li>
               <li><a href="#">Women <span class="caret"></span></a>
                <ul class="dropdown-menu">
                  <li><a href="#">Kurta & Kurti</a></li>
                  <li><a href="#">Trousers</a></li>
                  <li><a href="#">Casual</a></li>
                  <li><a href="#">Sports</a></li>
                  <li><a href="#">Formal</a></li>
                  <li><a href="#">Sarees</a></li>
                  <li><a href="#">Shoes</a></li>
                  <li><a href="#">And more.. <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                      <li><a href="#">Sleep Wear</a></li>
                      <li><a href="#">Sandals</a></li>
                      <li><a href="#">Loafers</a></li>
                      <li><a href="#">And more.. <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                          <li><a href="#">Rings</a></li>
                          <li><a href="#">Earrings</a></li>
                          <li><a href="#">Jewellery Sets</a></li>
                          <li><a href="#">Lockets</a></li>
                          <li class="disabled"><a class="disabled" href="#">Disabled item</a></li>
                          <li><a href="#">Jeans</a></li>
                          <li><a href="#">Polo T-Shirts</a></li>
                          <li><a href="#">SKirts</a></li>
                          <li><a href="#">Jackets</a></li>
                          <li><a href="#">Tops</a></li>
                          <li><a href="#">Make Up</a></li>
                          <li><a href="#">Hair Care</a></li>
                          <li><a href="#">Perfumes</a></li>
                          <li><a href="#">Skin Care</a></li>
                          <li><a href="#">Hand Bags</a></li>
                          <li><a href="#">Single Bags</a></li>
                          <li><a href="#">Travel Bags</a></li>
                          <li><a href="#">Wallets & Belts</a></li>
                          <li><a href="#">Sunglases</a></li>
                          <li><a href="#">Nail</a></li>
                        </ul>
                      </li>
                    </ul>
                  </li>
                </ul>
              </li>
              <li><a href="#">Kids <span class="caret"></span></a>
                <ul class="dropdown-menu">
                  <li><a href="#">Casual</a></li>
                  <li><a href="#">Sports</a></li>
                  <li><a href="#">Formal</a></li>
                  <li><a href="#">Standard</a></li>
                  <li><a href="#">T-Shirts</a></li>
                  <li><a href="#">Shirts</a></li>
                  <li><a href="#">Jeans</a></li>
                  <li><a href="#">Trousers</a></li>
                  <li><a href="#">And more.. <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                      <li><a href="#">Sleep Wear</a></li>
                      <li><a href="#">Sandals</a></li>
                      <li><a href="#">Loafers</a></li>
                    </ul>
                  </li>
                </ul>
              </li>
              <li><a href="#">Sports</a></li>
             <li><a href="#">Digital <span class="caret"></span></a>
                <ul class="dropdown-menu">
                  <li><a href="#">Camera</a></li>
                  <li><a href="#">Mobile</a></li>
                  <li><a href="#">Tablet</a></li>
                  <li><a href="#">Laptop</a></li>
                  <li><a href="#">Accesories</a></li>
                </ul>
              </li>
              <li><a href="#">Furniture</a></li>
              <li><a href="javascript:void(0)">Blog <span class="caret"></span></a>
                <ul class="dropdown-menu">
                  <li><a href="javascript:void(0)">Blog Style 1</a></li>
                  <li><a href="javascript:void(0)">Blog Style 2</a></li>
                  <li><a href="javascript:void(0)">Blog Single</a></li>
                </ul>
              </li>
              <li><a href="javascript:void(0)">Contact</a></li>
              <li><a href="#">Pages <span class="caret"></span></a>
                <ul class="dropdown-menu">
                  <li><a href="javascript:void(0)">Shop Page</a></li>
                  <li><a href="javascript:void(0)">Shop Single</a></li>
                  <li><a href="javascript:void(0)">404 Page</a></li>
                </ul>
              </li>
            </ul>  --}}


          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>
  </section>



  <!-- / menu -->
  <!-- Start slider -->

  @section('container')
  @show

  <!-- footer -->
<footer id="aa-footer">

    <div class="aa-footer-top">
     <div class="container">
        <div class="row">
        <div class="col-md-12">
          <div class="aa-footer-top-area">
             <div class="aa-footer-widget">
                  <h3 style="text-align: center;">Get In Touch</h3>
                   <div class="aa-footer-social custom-social">
                      <a href="#"><span class="fa fa-facebook"></span></a>
                      <a href="#"><span class="fa fa-twitter"></span></a>
                      <a href="#"><span class="fa fa-google-plus"></span></a>
                      <a href="#"><span class="fa fa-youtube"></span></a>
                    </div>
                     <address class="custom-address">
                      <p>Amritsar, Punjab</p>
                      <p><span class="fa fa-phone"></span>+91-9876543210</p>
                      <p><span class="fa fa-envelope"></span>info@shikshlights.com</p>
                    </address>
                  <!-- <ul class="aa-footer-nav">
                    <li><a href="#">Home</a></li>
                    <li><a href="#">Our Services</a></li>
                    <li><a href="#">Our Products</a></li>
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Contact Us</a></li>
                  </ul> -->
                </div>

            <!-- <div class="row">
              <div class="col-md-3 col-sm-6">
                <div class="aa-footer-widget">
                  <h3>Main Menu</h3>
                  <ul class="aa-footer-nav">
                    <li><a href="#">Home</a></li>
                    <li><a href="#">Our Services</a></li>
                    <li><a href="#">Our Products</a></li>
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Contact Us</a></li>
                  </ul>
                </div>
              </div>
              <div class="col-md-3 col-sm-6">
                <div class="aa-footer-widget">
                  <div class="aa-footer-widget">
                    <h3>Knowledge Base</h3>
                    <ul class="aa-footer-nav">
                      <li><a href="#">Delivery</a></li>
                      <li><a href="#">Returns</a></li>
                      <li><a href="#">Services</a></li>
                      <li><a href="#">Discount</a></li>
                      <li><a href="#">Special Offer</a></li>
                    </ul>
                  </div>
                </div>
              </div>
              <div class="col-md-3 col-sm-6">
                <div class="aa-footer-widget">
                  <div class="aa-footer-widget">
                    <h3>Useful Links</h3>
                    <ul class="aa-footer-nav">
                      <li><a href="#">Site Map</a></li>
                      <li><a href="#">Search</a></li>
                      <li><a href="#">Advanced Search</a></li>
                      <li><a href="#">Suppliers</a></li>
                      <li><a href="#">FAQ</a></li>
                    </ul>
                  </div>
                </div>
              </div>
              <div class="col-md-3 col-sm-6">
                <div class="aa-footer-widget">
                  <div class="aa-footer-widget">
                    <h3>Contact Us</h3>
                    <address>
                      <p> 25 Astor Pl, NY 10003, USA</p>
                      <p><span class="fa fa-phone"></span>+1 212-982-4589</p>
                      <p><span class="fa fa-envelope"></span>dailyshop@gmail.com</p>
                    </address>
                    <div class="aa-footer-social">
                      <a href="#"><span class="fa fa-facebook"></span></a>
                      <a href="#"><span class="fa fa-twitter"></span></a>
                      <a href="#"><span class="fa fa-google-plus"></span></a>
                      <a href="#"><span class="fa fa-youtube"></span></a>
                    </div>
                  </div>
                </div>
              </div>
            </div> -->
          </div>
        </div>
      </div>
     </div>
    </div>

    <!-- <div class="aa-footer-top">
     <div class="container">
        <div class="row">
        <div class="col-md-12">
          <div class="aa-footer-top-area">
            <div class="row">
              <div class="col-md-3 col-sm-6">
                <div class="aa-footer-widget">
                  <h3>Main Menu</h3>
                  <ul class="aa-footer-nav">
                    <li><a href="#">Home</a></li>
                    <li><a href="#">Our Services</a></li>
                    <li><a href="#">Our Products</a></li>
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Contact Us</a></li>
                  </ul>
                </div>
              </div>
              <div class="col-md-3 col-sm-6">
                <div class="aa-footer-widget">
                  <div class="aa-footer-widget">
                    <h3>Knowledge Base</h3>
                    <ul class="aa-footer-nav">
                      <li><a href="#">Delivery</a></li>
                      <li><a href="#">Returns</a></li>
                      <li><a href="#">Services</a></li>
                      <li><a href="#">Discount</a></li>
                      <li><a href="#">Special Offer</a></li>
                    </ul>
                  </div>
                </div>
              </div>
              <div class="col-md-3 col-sm-6">
                <div class="aa-footer-widget">
                  <div class="aa-footer-widget">
                    <h3>Useful Links</h3>
                    <ul class="aa-footer-nav">
                      <li><a href="#">Site Map</a></li>
                      <li><a href="#">Search</a></li>
                      <li><a href="#">Advanced Search</a></li>
                      <li><a href="#">Suppliers</a></li>
                      <li><a href="#">FAQ</a></li>
                    </ul>
                  </div>
                </div>
              </div>
              <div class="col-md-3 col-sm-6">
                <div class="aa-footer-widget">
                  <div class="aa-footer-widget">
                    <h3>Contact Us</h3>
                    <address>
                      <p> 25 Astor Pl, NY 10003, USA</p>
                      <p><span class="fa fa-phone"></span>+1 212-982-4589</p>
                      <p><span class="fa fa-envelope"></span>dailyshop@gmail.com</p>
                    </address>
                    <div class="aa-footer-social">
                      <a href="#"><span class="fa fa-facebook"></span></a>
                      <a href="#"><span class="fa fa-twitter"></span></a>
                      <a href="#"><span class="fa fa-google-plus"></span></a>
                      <a href="#"><span class="fa fa-youtube"></span></a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
     </div>
    </div> -->
    <!-- footer-bottom -->
    <div class="aa-footer-bottom">
      <div class="container">
        <div class="row">
        <div class="col-md-12">
          <div style="text-align: center;" class="aa-footer-bottom-area">
            <p style="text-align: center;">Designed by <a href="https://peerinfotech.com/">Peer Infotech</a></p>
            <!-- <div class="aa-footer-payment">
              <span class="fa fa-cc-mastercard"></span>
              <span class="fa fa-cc-visa"></span>
              <span class="fa fa-paypal"></span>
              <span class="fa fa-cc-discover"></span>
            </div> -->
          </div>
        </div>
      </div>
      </div>
    </div>
  </footer>


  {{-- <footer id="aa-footer">
    <!-- footer bottom -->
    <div class="aa-footer-top">
     <div class="container">
        <div class="row">
        <div class="col-md-12">
          <div class="aa-footer-top-area">
            <div class="row">
              <div class="col-md-3 col-sm-6">
                <div class="aa-footer-widget">
                  <h3>Main Menu</h3>
                  <ul class="aa-footer-nav">
                    <li><a href="#">Home</a></li>
                    <li><a href="#">Our Services</a></li>
                    <li><a href="#">Our Products</a></li>
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Contact Us</a></li>
                  </ul>
                </div>
              </div>
              <div class="col-md-3 col-sm-6">
                <div class="aa-footer-widget">
                  <div class="aa-footer-widget">
                    <h3>Knowledge Base</h3>
                    <ul class="aa-footer-nav">
                      <li><a href="#">Delivery</a></li>
                      <li><a href="#">Returns</a></li>
                      <li><a href="#">Services</a></li>
                      <li><a href="#">Discount</a></li>
                      <li><a href="#">Special Offer</a></li>
                    </ul>
                  </div>
                </div>
              </div>
              <div class="col-md-3 col-sm-6">
                <div class="aa-footer-widget">
                  <div class="aa-footer-widget">
                    <h3>Useful Links</h3>
                    <ul class="aa-footer-nav">
                      <li><a href="#">Site Map</a></li>
                      <li><a href="#">Search</a></li>
                      <li><a href="#">Advanced Search</a></li>
                      <li><a href="#">Suppliers</a></li>
                      <li><a href="#">FAQ</a></li>
                    </ul>
                  </div>
                </div>
              </div>
              <div class="col-md-3 col-sm-6">
                <div class="aa-footer-widget">
                  <div class="aa-footer-widget">
                    <h3>Contact Us</h3>
                    <address>
                      <p> 25 Astor Pl, NY 10003, USA</p>
                      <p><span class="fa fa-phone"></span>+1 212-982-4589</p>
                      <p><span class="fa fa-envelope"></span>dailyshop@gmail.com</p>
                    </address>
                    <div class="aa-footer-social">
                      <a href="#"><span class="fa fa-facebook"></span></a>
                      <a href="#"><span class="fa fa-twitter"></span></a>
                      <a href="#"><span class="fa fa-google-plus"></span></a>
                      <a href="#"><span class="fa fa-youtube"></span></a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
     </div>
    </div>
    <!-- footer-bottom -->
    <div class="aa-footer-bottom">
      <div class="container">
        <div class="row">
        <div class="col-md-12">
          <div class="aa-footer-bottom-area">
            <p>Designed by <a href="http://www.markups.io/">Peer info tech private limited</a></p>
            <div class="aa-footer-payment">
              <span class="fa fa-cc-mastercard"></span>
              <span class="fa fa-cc-visa"></span>
              <span class="fa fa-paypal"></span>
              <span class="fa fa-cc-discover"></span>
            </div>
          </div>
        </div>
      </div>
      </div>
    </div>
  </footer> --}}
  <!-- / footer -->

  <!-- Login Modal -->
  {{-- <div class="modal fade" id="login-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-body">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4>Login or Register</h4>
          <form class="aa-login-form" action="">
            <label for="">Username or Email address<span>*</span></label>
            <input type="text" placeholder="Username or email">
            <label for="">Password<span>*</span></label>
            <input type="password" placeholder="Password">
            <button class="aa-browse-btn" type="submit">Login</button>
            <label for="rememberme" class="rememberme"><input type="checkbox" id="rememberme"> Remember me </label>
            <p class="aa-lost-password"><a href="#">Lost your password?</a></p>
            <div class="aa-register-now">
              Don't have an account?<a href="javascript:void(0)">Register now!</a>
            </div>
          </form>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div>     --}}

  @stack('scripts')
  <!-- jQuery library -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
  <script src="{{asset('front_assets/js/bootstrap.js')}}"></script>
  <script type="text/javascript" src="{{asset('front_assets/js/jquery.smartmenus.js')}}"></script>
  <script type="text/javascript" src="{{asset('front_assets/js/jquery.smartmenus.bootstrap.js')}}"></script>
  <script src="{{asset('front_assets/js/sequence.js')}}"></script>
  <script src="{{asset('front_assets/js/sequence-theme.modern-slide-in.js')}}"></script>
  <script type="text/javascript" src="{{asset('front_assets/js/jquery.simpleGallery.js')}}"></script>
  <script type="text/javascript" src="{{asset('front_assets/js/jquery.simpleLens.js')}}"></script>
  <script type="text/javascript" src="{{asset('front_assets/js/slick.js')}}"></script>
  <script type="text/javascript" src="{{asset('front_assets/js/nouislider.js')}}"></script>
  <script src="{{asset('front_assets/js/custom.js')}}"></script>
  <script>
  (function () {
    var searchUrl = '{{ route("front.search") }}';
    var input = document.getElementById('global-search-input');
    var dropdown = document.getElementById('global-search-dropdown');
    var form = document.getElementById('global-search-form');
    if (!input || !dropdown) return;

    var debounceTimer;
    function doSearch() {
      var q = (input.value || '').trim();
      if (q.length < 1) {
        dropdown.style.display = 'none';
        dropdown.innerHTML = '';
        return;
      }
      fetch(searchUrl + '?q=' + encodeURIComponent(q), { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
        .then(function (r) { return r.json(); })
        .then(function (data) {
          if (!data.length) {
            dropdown.innerHTML = '<div class="search-no-results">No products found</div>';
            dropdown.style.display = 'block';
            return;
          }
          dropdown.innerHTML = data.map(function (item) {
            var img = item.image ? '<img src="' + item.image + '" alt="">' : '<img src="" alt="" style="background:#eee;min-width:48px;min-height:48px;">';
            var cat = item.category_name ? '<div class="search-result-cat">' + escapeHtml(item.category_name) + '</div>' : '';
            return '<a class="search-result-item" href="' + escapeHtml(item.url) + '">' + img + '<div class="search-result-info"><div class="search-result-title">' + escapeHtml(item.title) + '</div>' + cat + '</div></a>';
          }).join('');
          dropdown.style.display = 'block';
        })
        .catch(function () {
          dropdown.innerHTML = '<div class="search-no-results">Search unavailable</div>';
          dropdown.style.display = 'block';
        });
    }
    function escapeHtml(s) {
      var div = document.createElement('div');
      div.textContent = s;
      return div.innerHTML;
    }

    input.addEventListener('input', function () {
      clearTimeout(debounceTimer);
      debounceTimer = setTimeout(doSearch, 300);
    });
    input.addEventListener('focus', function () {
      if (dropdown.innerHTML) dropdown.style.display = 'block';
    });
    document.addEventListener('click', function (e) {
      if (!input.contains(e.target) && !dropdown.contains(e.target)) dropdown.style.display = 'none';
    });
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var q = (input.value || '').trim();
      if (q.length > 0) doSearch();
    });
  })();
  </script>
  </body>
</html>
