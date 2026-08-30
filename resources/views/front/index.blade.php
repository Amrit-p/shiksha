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
         {{-- <li>
              <div class="seq-model">
                <img data-seq src="storage/category/banner-one.jpg" alt="Products img" />
              </div>
            
            </li>  --}}
           
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

  <!--end my custom design -->




















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