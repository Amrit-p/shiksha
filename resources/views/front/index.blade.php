@extends('front/layout')
@section('title','Home Page')

<style>
  /* PROMO SECTION */
#aa-promo {
  padding: 50px 0;
  background: linear-gradient(135deg, #f8f9fb, #ffffff);
}

.aa-promo-area {
  overflow: hidden;
}

.aa-promo-right {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 20px;
}

/* Promo Card */
.aa-single-promo-right {
  position: relative;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
  transition: all 0.35s ease;
}

.aa-single-promo-right:hover {
  transform: translateY(-6px);
  box-shadow: 0 18px 45px rgba(0, 0, 0, 0.15);
}

/* Banner Image */
.aa-promo-banner {
  position: relative;
      margin: 10;

}

.aa-promo-banner img {
  width: 100%;
  height: 260px;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.aa-single-promo-right:hover img {
  transform: scale(1.08);
}

/* Overlay */
.aa-promo-banner::after {
  content: "";
  position: absolute;
  inset: 0;
  background: linear-gradient(
    to bottom,
    rgba(0, 0, 0, 0.15),
    rgba(0, 0, 0, 0.65)
  );
}

/* Text Content */
.aa-prom-content {
  position: absolute;
  bottom: 20px;
  left: 20px;
  right: 20px;
  text-align: center;
  z-index: 2;
}

.aa-prom-content h4 {
  margin: 0;
}

.aa-prom-content a {
  color: #ffffff;
  font-size: 20px;
  font-weight: 600;
  text-decoration: none;
  letter-spacing: 0.4px;
  transition: color 0.3s ease;
}

.aa-prom-content a:hover {
  color: #ffd700;
}

/* Responsive */
@media (max-width: 768px) {
  .aa-promo-banner img {
    height: 200px;
   }

  .aa-prom-content a {
    font-size: 18px;
  }
}

@media (max-width: 480px) {
  .aa-promo-banner img {
    height: 170px;
  }

  .aa-prom-content a {
    font-size: 16px;
  }
}

</style>
 
 @section('container')

 <!--default code -->
 {{-- <section id="aa-slider">
    <div class="aa-slider-area">
      <div id="sequence" class="seq">
        <div class="seq-screen">
          <ul class="seq-canvas">
            <!-- single slide item -->
           @foreach ($home_banner as  $item)
            <li>
              <div class="seq-model">
                <img data-seq src="{{asset('storage/'.$item->image)}}" alt="Men slide img" />
              </div>
              <div class="seq-title">
               <span data-seq>Save Up to 75% Off</span>                
                <h2 data-seq>Electricity</h2>                
                <p data-seq>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Minus, illum.</p>
                <a  target="blank" href="http://google.com" class="aa-shop-now-btn aa-secondary-btn">{{$item->btn_text}}</a>
              </div>
            </li>
              @endforeach
          </ul>
        </div>
        <!-- slider navigation btn -->
        <fieldset class="seq-nav" aria-controls="sequence" aria-label="Slider buttons">
          <a type="button" class="seq-prev" aria-label="Previous"><span class="fa fa-angle-left"></span></a>
          <a type="button" class="seq-next" aria-label="Next"><span class="fa fa-angle-right"></span></a>
        </fieldset>
      </div>
    </div>
  </section> --}}
  <!-- default section --> 


  <!-- my custom desin -->
  {{-- Slider hidden on salesman page
<section id="aa-slider">
    <div class="aa-slider-area">
      <div id="sequence" class="seq">
        <div class="seq-screen">
          <ul class="seq-canvas">

            <!-- single slide item -->
          @foreach ($home_banner as  $item)

           <li>
              <div class="seq-model">
                <img data-seq src="{{asset('storage/'.$item->image)}}" alt="Products slide img" />
              </div>

            </li>

          @endforeach
            <!-- single slide item -->
          <li>
              <div class="seq-model">
                <img data-seq src="storage/category/banner-one.jpg" alt="Products img" />
              </div>

            </li>

          </ul>
        </div>
        <!-- slider navigation btn -->
        <fieldset class="seq-nav" aria-controls="sequence" aria-label="Slider buttons">
          <a type="button" class="seq-prev" aria-label="Previous"><span class="fa fa-angle-left"></span></a>
          <a type="button" class="seq-next" aria-label="Next"><span class="fa fa-angle-right"></span></a>
        </fieldset>
      </div>
    </div>
  </section>
  --}}

  <!--end my custom design -->

  {{-- Current schemes / banners (admin > Banner). Bootstrap 3 carousel, no Sequence.js. --}}
  @if ($home_banner->isNotEmpty())
  <style>
    /* The theme floats #aa-header and #menu, so this section must clear them or it slides underneath. */
    #scheme-banners { clear: both; width: 100%; padding: 24px 0 8px; }
    #scheme-banners .scheme-carousel { border-radius: 14px; overflow: hidden; box-shadow: 0 10px 30px rgba(23, 16, 31, .12); background: #f4effa; }
    #scheme-banners .item { position: relative; aspect-ratio: 1920 / 700; }
    #scheme-banners .item img { width: 100%; height: 100%; object-fit: cover; display: block; }
    #scheme-banners .scheme-caption {
      position: absolute; left: 0; right: 0; bottom: 0; padding: 60px 40px 34px; text-align: left;
      background: linear-gradient(180deg, rgba(23, 16, 31, 0) 0%, rgba(23, 16, 31, .72) 100%);
    }
    #scheme-banners .scheme-caption h3 { margin: 0 0 12px; color: #fff; font-size: 28px; font-weight: 700; line-height: 1.25; max-width: 70%; }
    #scheme-banners .scheme-caption .scheme-btn {
      display: inline-block; padding: 9px 22px; border-radius: 999px; background: #ec008c; color: #fff;
      font-weight: 600; font-size: 14px; letter-spacing: .3px; transition: background .2s;
    }
    #scheme-banners .scheme-caption .scheme-btn:hover { background: #c20073; color: #fff; text-decoration: none; }
    #scheme-banners .carousel-control {
      width: 44px; height: 44px; top: 50%; margin-top: -22px; border-radius: 50%; opacity: .9;
      background: rgba(255, 255, 255, .9) !important; color: #ec008c; text-shadow: none;
      display: flex; align-items: center; justify-content: center; font-size: 26px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, .18); transition: background .2s, color .2s;
    }
    #scheme-banners .carousel-control.left { left: 16px; }
    #scheme-banners .carousel-control.right { right: 16px; }
    #scheme-banners .carousel-control:hover { background: #ec008c !important; color: #fff; opacity: 1; }
    #scheme-banners .carousel-indicators { bottom: 10px; margin-bottom: 0; }
    #scheme-banners .carousel-indicators li { width: 9px; height: 9px; margin: 0 3px; border: 0; background: rgba(255, 255, 255, .55); transition: width .2s; }
    #scheme-banners .carousel-indicators .active { width: 24px; height: 9px; margin: 0 3px; border-radius: 5px; background: #ec008c; }
    @media (max-width: 767px) {
      #scheme-banners { padding: 14px 0 4px; }
      #scheme-banners .scheme-carousel { border-radius: 10px; }
      #scheme-banners .item { aspect-ratio: 16 / 9; }
      #scheme-banners .scheme-caption { padding: 36px 16px 30px; }
      #scheme-banners .scheme-caption h3 { font-size: 17px; max-width: 100%; margin-bottom: 8px; }
      #scheme-banners .scheme-caption .scheme-btn { padding: 6px 16px; font-size: 13px; }
      #scheme-banners .carousel-control { width: 34px; height: 34px; margin-top: -17px; font-size: 20px; }
      #scheme-banners .carousel-control.left { left: 8px; }
      #scheme-banners .carousel-control.right { right: 8px; }
    }
  </style>
  <section id="scheme-banners">
    <div class="container">
      <div id="schemeCarousel" class="carousel slide scheme-carousel" data-ride="carousel" data-interval="5000">
        <div class="carousel-inner" role="listbox">
          @foreach ($home_banner as $i => $banner)
            <div class="item {{ $i === 0 ? 'active' : '' }}">
              @if ($banner->btn_link && ! $banner->btn_text)<a href="{{ $banner->btn_link }}">@endif
              <img src="{{ asset('storage/'.$banner->image) }}" alt="{{ $banner->title ?: 'Current scheme' }}">
              @if ($banner->btn_link && ! $banner->btn_text)</a>@endif
              @if ($banner->title || ($banner->btn_text && $banner->btn_link))
              <div class="scheme-caption">
                @if ($banner->title)<h3>{{ $banner->title }}</h3>@endif
                @if ($banner->btn_text && $banner->btn_link)
                  <a href="{{ $banner->btn_link }}" class="scheme-btn">{{ $banner->btn_text }} <span class="fa fa-angle-right"></span></a>
                @endif
              </div>
              @endif
            </div>
          @endforeach
        </div>
        @if ($home_banner->count() > 1)
        <ol class="carousel-indicators">
          @foreach ($home_banner as $i => $banner)
            <li data-target="#schemeCarousel" data-slide-to="{{ $i }}" class="{{ $i === 0 ? 'active' : '' }}"></li>
          @endforeach
        </ol>
        <a class="left carousel-control" href="#schemeCarousel" role="button" data-slide="prev" aria-label="Previous"><span class="fa fa-angle-left"></span></a>
        <a class="right carousel-control" href="#schemeCarousel" role="button" data-slide="next" aria-label="Next"><span class="fa fa-angle-right"></span></a>
        @endif
      </div>
    </div>
  </section>
  @endif

  {{-- Trust badges strip (Admin > Trust Badges) --}}
  @include('front.partials.trust_badges', ['variant' => 'full'])

  {{-- "Current Schemes" popup: once per day per browser, after the page loader fades out. --}}
  @if ($scheme_banners->isNotEmpty())
  <div class="modal fade" id="schemesModal" tabindex="-1" role="dialog" aria-labelledby="schemesModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="schemesModalLabel">Current Schemes</h4>
        </div>
        <div class="modal-body">
          @foreach ($scheme_banners as $banner)
            <div style="{{ $loop->last ? '' : 'margin-bottom:20px;' }}">
              <img src="{{ asset('storage/'.$banner->image) }}" alt="{{ $banner->title }}" style="width:100%;border-radius:4px;">
              @if ($banner->title)<h4 style="margin:10px 0 4px;">{{ $banner->title }}</h4>@endif
              @if ($banner->ends_at)<small class="text-muted">Valid till {{ $banner->ends_at->format('d M Y') }}</small>@endif
              @if ($banner->btn_text && $banner->btn_link)
                <div style="margin-top:8px;"><a href="{{ $banner->btn_link }}" class="aa-secondary-btn" style="display:inline-block;padding:6px 16px;">{{ $banner->btn_text }}</a></div>
              @endif
            </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
  <script>
    (function () {
      var key = 'schemes_popup_seen_{{ auth()->id() }}';
      var today = new Date().toISOString().slice(0, 10);
      var seen = null;
      try { seen = localStorage.getItem(key); } catch (e) {}
      if (seen === today) return;
      // jQuery/Bootstrap load at the end of the layout, so wait for window load.
      window.addEventListener('load', function () {
        setTimeout(function () {
          if (!window.jQuery || !jQuery.fn.modal) return;
          jQuery('#schemesModal').modal('show');
          try { localStorage.setItem(key, today); } catch (e) {}
        }, 900);
      });
    })();
  </script>
  @endif




















  <!--my design -->

  <section id="aa-promo" class="product-categories">
    <div class="container">
      <div class="product-categories-list">

       @foreach($home_categories as $list)
        <a href="{{route('front.category',$list->slug)}}">
          <div class="product-categories-data">
            <img class="img-fluid" src="{{asset('storage/'.$list->image)}}" alt="img" />
            <div class="product-categories-content">
              <h4>{{$list->name}}</h4>
            </div>
          </div>
          </a>   
          @endforeach
      </div>
    </div>
  </section>

  <!-- my section end -->




  
  <!-- / slider -->
  <!-- Start Promo section -->
  {{-- <section id="aa-promo">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <div class="aa-promo-area">
            <div class="row">
              <div class="col-md-12 no-padding">
                <div class="aa-promo-right">
                  @foreach($home_categories as $list)
                  <div class="aa-single-promo-right">
                    <div class="aa-promo-banner">                      
                      <img src="{{asset('storage/'.$list->image)}}" alt="img">                      
                      <div class="aa-prom-content">
                        <h4><a href="{{url('category/'.$list->slug)}}">{{$list->name}}</a></h4>                        
                      </div>
                    </div>
                  </div>
                  @endforeach
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section> --}}


  {{-- ################### --}}
                                                     

  <!-- / Promo section -->
  <!-- Products section -->
  {{-- <section id="aa-product">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <div class="row">
            <div class="aa-product-area">
              <div class="aa-product-inner">
                <!-- start prduct navigation -->
                 <ul class="nav nav-tabs aa-products-tab">
                 @foreach($home_categories as $list)
                    <li class=""><a href="#cat{{$list->id}}" data-toggle="tab">{{$list->name}}</a></li>
                 @endforeach
 
                  </ul>
                  <!-- Tab panes -->
                  <div class="tab-content">
                    <!-- Start men product category -->
                    @php
                    $loop_count=1;
                    @endphp
                    @foreach($home_categories as $list)
                    @php
                    $cat_class="";
                    if($loop_count==1){
                      $cat_class="in active"; 
                      $loop_count++;
                    }
                    @endphp
                    <div class="tab-pane fade {{$cat_class}}" id="cat{{$list->id}}">
                      <ul class="aa-product-catg">
                      @if(isset($home_categories_product[$list->id][0]))

                       @foreach($home_categories_product[$list->id] as $productArr)
                         <li>
                          <figure>
                            <a class="aa-product-img" href="{{route('front.product', $productArr->slug)}}"><img src="{{asset('storage/'.$productArr->image)}}" alt="{{$productArr->title}}"></a>

                            <!--Add To Cart Button In Index -->
                            <a class="aa-add-card-btn" href="javascript:void(0);" onclick="home_add_to_cart('{{$productArr->id}}','{{$home_product_attr[$productArr->id][0]->wattage}}')"><span class="fa fa-shopping-cart"></span>Add To Cart</a>
                               <!--<a class="aa-add-card-btn" href="{{route('front.product', $productArr->slug)}}" onclick="add_to_cart('{{$product[0]->id}}','{{$product_attr[$product[0]->id][0]->wattage}}')><span class="fa fa-shopping-cart"></span>Add To Cart</a> -->
                            <figcaption>
                              <h4 class="aa-product-title"><a href="{{route('front.product', $productArr->slug)}}">{{$productArr->title}}</a></h4>
                              <span class="aa-product-price">Rs {{$home_product_attr[$productArr->id][0]->price}}</span><span class="aa-product-price"><del>Rs {{$home_product_attr[$productArr->id][0]->mrp}}</del></span>
                            </figcaption>
                          </figure>                          
                        </li>  
                        @endforeach    
                        @else
                        <li>
                          <figure>
                            No data found
                          <figure>
                        <li> 
                        @endif
                      </ul>
                    </div>
                    @endforeach
                  </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section> --}}



  
  <!-- / Products section -->
  <!-- banner section -->
  {{-- <section id="aa-banner">
    <div class="container">
      <div class="row">
        <div class="col-md-12">        
          <div class="row">
            <div class="aa-banner-area">
            <a href="#"><img src="{{asset('front_assets/img/fashion-banner.jpg')}}" alt="fashion banner img"></a>
          </div>
          </div>
        </div>
      </div>
    </div>
  </section> --}}

  


  <!-- popular section -->
  {{-- <section id="aa-popular-category">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <div class="row">
            <div class="aa-popular-category-area">
              <!-- start prduct navigation -->
             <ul class="nav nav-tabs aa-products-tab">
                <li class="active"><a href="#featured" data-toggle="tab">Featured</a></li>
                <li><a href="#tranding" data-toggle="tab">Tranding</a></li>
                <li><a href="#discounted" data-toggle="tab">Discounted</a></li>                    
              </ul>
              <!-- Tab panes -->
              <div class="tab-content">
                <!-- Start men popular category -->
                <div class="tab-pane fade in active" id="featured">
                  <ul class="aa-product-catg aa-feature-slider">
                    <!-- start single product item -->
                    
                    @if(isset($home_featured_product[$list->id][0]))
                       @foreach($home_featured_product[$list->id] as $productArr)
                        <li>
                          <figure>
                            <a class="aa-product-img" href="{{route('front.product', $productArr->slug)}}"><img src="{{asset('storage/'.$productArr->image)}}" alt="{{$productArr->title}}"></a>
                            <a class="aa-add-card-btn" href="{{route('front.product', $productArr->slug)}}"><span class="fa fa-shopping-cart"></span>Add To Cart</a>
                            <figcaption>
                              <h4 class="aa-product-title"><a href="{{route('front.product', $productArr->slug)}}">{{$productArr->title}}</a></h4>
                              <span class="aa-product-price">Rs {{$home_featured_product_attr[$productArr->id][0]->price}}</span><span class="aa-product-price"><del>Rs {{$home_featured_product_attr[$productArr->id][0]->mrp}}</del></span>
                            </figcaption>
                          </figure>                          
                        </li>  
                        @endforeach    
                        @else
                        <li>
                          <figure>
                            No data found
                          <figure>
                        <li>
                        @endif                            

                  </ul>
                </div>
                <!-- / popular product category -->
                
                <!-- start featured product category -->
                <div class="tab-pane fade" id="tranding">
                 <ul class="aa-product-catg aa-tranding-slider">
                    <!-- start single product item -->
                    @if(isset($home_tranding_product[$list->id][0]))
                       @foreach($home_tranding_product[$list->id] as $productArr)
                        <li>
                          <figure>
                            <a class="aa-product-img" href="{{route('front.product', $productArr->slug)}}"><img src="{{asset('storage/'.$productArr->image)}}" alt="{{$productArr->title}}"></a>
                            <a class="aa-add-card-btn" href="{{route('front.product', $productArr->slug)}}"><span class="fa fa-shopping-cart"></span>Add To Cart</a>
                            <figcaption>
                              <h4 class="aa-product-title"><a href="{{route('front.product', $productArr->slug)}}">{{$productArr->title}}</a></h4>
                              <span class="aa-product-price">Rs {{$home_tranding_product_attr[$productArr->id][0]->price}}</span><span class="aa-product-price"><del>Rs {{$home_tranding_product_attr[$productArr->id][0]->mrp}}</del></span>
                            </figcaption>
                          </figure>                          
                        </li>  
                        @endforeach    
                        @else
                        <li>
                          <figure>
                            No data found
                          <figure>
                        <li>
                        @endif                
                  </ul>
                </div>
 

                <!-- / featured product category -->

                <!-- start latest product category -->
                <div class="tab-pane fade" id="discounted">
                  <ul class="aa-product-catg aa-discounted-slider">
                    <!-- start single product item -->
                   <!-- start single product item -->
                   @php
            //  format($list->id);
             @endphp

                    @if(isset($home_discounted_product[$list->id][0]))
                       @foreach($home_discounted_product[$list->id] as $productArr)
                        <li>
                          <figure>
                            <a class="aa-product-img" href="{{route('front.product', $productArr->slug)}}"><img src="{{asset('storage/'.$productArr->image)}}" alt="{{$productArr->title}}"></a>
                            <a class="aa-add-card-btn" href="{{route('front.product', $productArr->slug)}}"><span class="fa fa-shopping-cart"></span>Add To Cart</a>
                            <figcaption>
                              <h4 class="aa-product-title"><a href="{{route('front.product', $productArr->slug)}}">{{$productArr->title}}</a></h4>
                              <!-- Comment it run smothly -->
                              <span class="aa-product-price">Rs {{$home_discounted_product_attr[$productArr->id][0]->price}}</span><span class="aa-product-price"><del>Rs {{$home_discounted_product_attr[$productArr->id][0]->mrp}}</del></span>
                            </figcaption>
                          </figure>                          
                        </li>  
                        @endforeach    
                        @else
                        <li>
                          <figure>
                            No data found
                          </figure>
                        <li>
                        @endif                                                                                     
                  </ul>
                </div>
                <!-- / latest product category -->              
              </div>
            </div>
          </div> 
        </div>
      </div>
    </div>
  </section> --}}



  <!-- / popular section -->
  <!-- Support section -->
  {{-- <section id="aa-support">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <div class="aa-support-area">
            <!-- single support -->
            <div class="col-md-4 col-sm-4 col-xs-12">
              <div class="aa-support-single">
                <span class="fa fa-truck"></span>
                <h4>FREE SHIPPING</h4>
                <P>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quam, nobis.</P>
              </div>
            </div>
            <!-- single support -->
            <div class="col-md-4 col-sm-4 col-xs-12">
              <div class="aa-support-single">
                <span class="fa fa-clock-o"></span>
                <h4>30 DAYS MONEY BACK</h4>
                <P>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quam, nobis.</P>
              </div>
            </div>
            <!-- single support -->
            <div class="col-md-4 col-sm-4 col-xs-12">
              <div class="aa-support-single">
                <span class="fa fa-phone"></span>
                <h4>SUPPORT 24/7</h4>
                <P>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quam, nobis.</P>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section> --}}
  <!-- / Support section -->
  
  <!-- Client Brand -->
  {{-- <section id="aa-client-brand">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <div class="aa-client-brand-area">
            <ul class="aa-client-brand-slider">
              @foreach($home_brand as $list)
              <li><a href="#"><img src="{{asset('storage/'.$list->image)}}" alt="{{$list->name}}"></a></li>
              @endforeach
            </ul>
          </div>
        </div>
      </div>
    </div>
  </section> --}}
  <!-- / Client Brand -->

   <input type="hidden" id="qty" value="1"/>
  <form id="frmAddToCart">
    <input type="hidden" id="wattage" name="wattage"/>
     <input type="hidden" id="pqty" name="pqty"/>
    <input type="hidden" id="product_id" name="product_id"/>           
    @csrf
  </form> 

@endsection