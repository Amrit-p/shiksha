@push('head')
  <style>
    /* Hide default DataTables search box */
    #my-datatable_filter {
      display: none !important;
    }
  </style>
@endpush 
 <div class="card-search with-adv-search dropdown">
   <form action="">
     <input type="text" class="form-control global_filter" id="global_filter" placeholder="Search.." required="">
     <button type="submit" class="btn btn-icon"><i class="ik ik-search"></i></button>
     <button type="button" id="adv_wrap_toggler_1" class="adv-btn ik ik-chevron-down dropdown-toggle"
       data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
     <div class="adv-search-wrap dropdown-menu dropdown-menu-right" aria-labelledby="adv_wrap_toggler_1">
       <div class="row">
         <div class="col-md-12">
           <div class="form-group">
             <input type="text" class="form-control column_filter" id="col0_filter" placeholder="Title"
               data-column="0">
           </div>
         </div>
         <div class="col-md-6">
           <div class="form-group">
             <input type="text" class="form-control column_filter" id="col1_filter" placeholder="Price"
               data-column="1">
           </div>
         </div>
         <div class="col-md-6">
           <div class="form-group">
             <input type="text" class="form-control column_filter" id="col2_filter" placeholder="SKU"
               data-column="2">
           </div>
         </div>
         <div class="col-md-4">
           <div class="form-group">
             <input type="text" class="form-control column_filter" id="col3_filter" placeholder="Qty"
               data-column="3">
           </div>
         </div>
         <div class="col-md-4">
           <div class="form-group">
             <input type="text" class="form-control column_filter" id="col4_filter" placeholder="Category"
               data-column="4">
           </div>
         </div>
         <div class="col-md-4">
           <div class="form-group">
             <input type="text" class="form-control column_filter" id="col5_filter" placeholder="Tag"
               data-column="5">
           </div>
         </div>
       </div>
       <button class="btn btn-theme">Search</button>
     </div>
   </form>
 </div>
