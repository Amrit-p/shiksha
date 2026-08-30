@extends('front/layout')
@section('page_title','Order')
@section('container')

<!-- catg header banner section -->
<section id="aa-catg-head-banner">
   <div class="aa-catg-head-banner-area">
     <div class="container">
      
     </div>
   </div>
  </section>
  <!-- / catg header banner section -->         

  <section id="cart-view">
   <div class="container">
     <div class="row">
       <div class="col-md-12">
         <div class="cart-view-area">
           <div class="cart-view-table">
             <form action="">
             
               <div class="table-responsive">
                  <table class="table">
                    <thead>
                      <tr>
                        <th>Order Id</th>
                        <th>Order Status</th>
                        <th>Payment Status</th>
                        <th>Total Amt</th>
                        {{-- <th>Payment ID</th> --}}
                        <th>Placed At</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($orders as $list)
                      <tr>
                    <td class="text-center">
                      <a href="{{ route('front.order_detail', $list->id) }}"
                        class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1">
                        <i class="ik ik-eye"></i> View Order
                      </a>
                    </td>
                        <td>{{$list->order_status}}</td>
                        <td>{{$list->payment_status}}</td>
                        <td>{{$list->total_amount}}</td>
                        {{-- <td>{{$list->payment_id}}</td> --}}
                        <td>{{dmyHelper($list->created_at)}}</td>
                      </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
             </form>
             <!-- Cart Total view -->
           
		   </div>
         </div>
       </div>
     </div>
   </div>
 </section> 
@endsection