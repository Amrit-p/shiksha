@extends('admin.layouts.main')
@section('title','Products')
@push('head')
<style>
  /* Hide default DataTables search box */
#my-datatable_filter {
    display: none !important;
}
</style>

@section('content')

@include('admin.include.message')
	<div class="container-fluid">
    	<div class="page-header">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="ik ik-headphones bg-green"></i>
                        <div class="d-inline">
                            <h5>Products</h5>
                            <span>View, delete and update products</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <nav class="breadcrumb-container" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="/dashboard"><i class="ik ik-home"></i></a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="#">Products</a>
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <div class="row">
            <!-- list layout 1 start -->
            <div class="col-md-12">
				<div class="card">
		            <div class="card-header row">
		                <div class="col col-sm-1">
		                    <div class="card-options d-inline-block">
		                        <div class="dropdown d-inline-block">
		                            {{-- <a class="nav-link dropdown-toggle" href="#" id="moreDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ik ik-more-horizontal"></i></a> --}}
		                            <div class="dropdown-menu dropdown-menu-left" aria-labelledby="moreDropdown">
		                                <a class="dropdown-item" href="#">Delete</a>
		                                <a class="dropdown-item" href="#">More Action</a>
		                            </div>
		                        </div>
		                    </div>
		                </div>

		                <div class="col col-sm-6">
		                    <div class="card-search with-adv-search dropdown">
		                        <form action="">
		                            <input type="text" class="form-control global_filter" id="global_filter" placeholder="Search.." required="">
		                            <button type="submit" class="btn btn-icon"><i class="ik ik-search"></i></button>
		                            <button type="button" id="adv_wrap_toggler_1" class="adv-btn ik ik-chevron-down dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
		                            <div class="adv-search-wrap dropdown-menu dropdown-menu-right" aria-labelledby="adv_wrap_toggler_1">
		                                <div class="row">
		                                    <div class="col-md-12">
		                                        <div class="form-group">
		                                            <input type="text" class="form-control column_filter" id="col0_filter" placeholder="Title" data-column="0">
		                                        </div>
		                                    </div>
		                                    {{-- <div class="col-md-6">
		                                        <div class="form-group">
		                                            <input type="text" class="form-control column_filter" id="col1_filter" placeholder="Price" data-column="1">
		                                        </div>
		                                    </div> --}}
		                                    {{-- <div class="col-md-6">
		                                        <div class="form-group">
		                                            <input type="text" class="form-control column_filter" id="col2_filter" placeholder="SKU" data-column="2">
		                                        </div>
		                                    </div> --}}
		                                    {{-- <div class="col-md-4">
		                                        <div class="form-group">
		                                            <input type="text" class="form-control column_filter" id="col3_filter" placeholder="Qty" data-column="3">
		                                        </div>
		                                    </div> --}}
		                                    <div class="col-md-4">
		                                        <div class="form-group">
		                                            <input type="text" class="form-control column_filter" id="col4_filter" placeholder="Category" data-column="4">
		                                        </div>
		                                    {{-- </div>
		                                    <div class="col-md-4">
		                                        <div class="form-group">
		                                            <input type="text" class="form-control column_filter" id="col5_filter" placeholder="Tag" data-column="5">
		                                        </div>
		                                    </div> --}}
		                                </div>
		                                <button class="btn btn-theme">Search</button>
		                            </div>
		                        </form>
		                    </div>
		                </div>

		            </div>

		                <div class="col-sm-5">
							<div class="card-options text-right">
		                        {{-- <span class="mr-5" id="top">1 - 50 of 2,500</span>
		                        <a href="#"><i class="ik ik-chevron-left"></i></a>
		                        <a href="#"><i class="ik ik-chevron-right"></i></a> --}}
			                    <a href="{{route('admin.product.create')}}" class=" btn btn-outline-primary btn-semi-rounded ">Add Product</a>
		                    </div>


		                </div>



		            <div class="card-body">
		                <table id="my-datatable" class="table">
		                    <thead>
		                        <tr>
		                            <th class="nosort" width="10">
		                                <label class="custom-control custom-checkbox m-0">
		                                    <input type="checkbox" class="custom-control-input" id="selectall" name="" value="option2">
		                                    <span class="custom-control-label">&nbsp;</span>
		                                </label>
		                            </th>
		                            {{-- <th class="nosort">Image</th> --}}
		                            <th>Title</th>
		                            <th>Slug</th>
									<th>Product Type</th>
		                            <th>Categories</th>
		                            <th>Action</th>
		                        </tr>
		                    </thead>
		                    <tbody>
		                    	{{-- <tr>
		                    		<td>
		                    			<label class="custom-control custom-checkbox">
		                    				<input type="checkbox" class="custom-control-input select_all_child" id="" name="" value="option2">
		                    				<span class="custom-control-label">&nbsp;</span>
		                    			</label>
		                    		</td>
		                    		<td>
		                    			<img src="/img/products/helmet.jpg" class="table-user-thumb" alt="">
		                    		</td>
		                    		<td>Helmet</td>
		                    		<td>EH1234</td>
		                    		<td>
		                    			Fashion,
		                    		</td>
		                    		<td>500</td>
		                    		<td>450</td>
		                    		<td>100</td>
		                    		<td>
		                    			<a href="#productView" data-toggle="modal" data-target="#productView"><i class="ik ik-eye f-16 mr-15"></i></a>
		                    			<a href="/products/9/edit"><i class="ik ik-edit f-16 mr-15 text-green"></i></a>
		                    			<a href="#!"><i class="ik ik-trash-2 f-16 text-red"></i></a>
		                    		</td>
		                    	</tr> --}}
		                    </tbody>
		                </table>
		            </div>
		        </div>
		    </div>
            

{{-- recently delete button --}}
{{-- <div class="col-sm-2 text-left mb-3">
    <button id="recentlyDeletedBtn" class="btn btn-success btn-semi-rounded">Recently Deleted</button>
</div> --}}
{{-- <div class="col-sm-2 text-left mb-3">
<a href="{{ route('admin.product.recentlyDeletedPage') }}" class="btn btn-success btn-semi-rounded">Recently Deleted</a>
</div> --}}

{{-- <div class="modal fade" id="recentlyDeletedModal" tabindex="-1" role="dialog" aria-labelledby="recentlyDeletedModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-warning text-white">
        <h5 class="modal-title">Recently Deleted Products</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table id="deleted-datatable" class="table table-bordered">
          <thead>
            <tr>
              <th>Title</th>
              <th>Deleted At</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>
</div> --}}

		    <!-- list layout 1 end -->
		</div>
	</div>
	<div class="modal fade edit-layout-modal pr-0" id="productView" tabindex="-1" role="dialog" aria-labelledby="productViewLabel" aria-hidden="true">
    @include('admin.product.list')
    @include('admin.product.delete')

    {{-- @push('script')
<script>
$(document).ready(function(){

    // Open Recently Deleted Modal
    $('#recentlyDeletedBtn').click(function(){
        $('#recentlyDeletedModal').modal('show');
        loadDeletedDatatable();
    });

    function loadDeletedDatatable(){
        $('#deleted-datatable').DataTable({
            processing: true,
            serverSide: true,
            destroy: true,
            ajax: "{{ route('admin.product.recently_deleted') }}",
            columns: [
                {data: 'title', name: 'title'},
                {data: 'deleted_at', name: 'deleted_at'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ]
        });
    }

    // Restore Product
    $(document).on('click', '.restoreBtn', function(){
        var id = $(this).data('id');
        if(confirm('Restore this product?')){
            $.ajax({
                url: '/admin/products/restore/' + id,
                method: 'POST',
                data: {_token: '{{ csrf_token() }}'},
                success: function(res){
                    if(res.success){
                        alert(res.message);
                        $('#deleted-datatable').DataTable().ajax.reload();
                        $('#my-datatable').DataTable().ajax.reload();
                    } else {
                        alert(res.message);
                    }
                }
            });
        }
    });

});
</script>
@endpush --}}

@endsection
