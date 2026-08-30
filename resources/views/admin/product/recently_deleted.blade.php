{{-- @extends('admin.layouts.main')
@section('title', 'Recently Deleted Products')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <h3>Recently Deleted Products</h3>
        <a href="{{ route('admin.product.index') }}" class="btn btn-success btn-semi-rounded float-right mb-3">Back to Products</a>
    </div>

    <div class="card mt-3">
        <div class="card-body">
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
@endsection

@push('script')
<script>
$(document).ready(function(){

    // Initialize Recently Deleted DataTable
    $('#deleted-datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.product.recentlyDeleted') }}",
        columns: [
            {data: 'title', name: 'title'},
            {data: 'deleted_at', name: 'deleted_at'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ]
    });

    // Restore Product via AJAX
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

@extends('admin.layouts.main')
@section('title', 'Recently Deleted Products')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <h3>Recently Deleted Products</h3>
        <a href="{{ route('admin.product') }}" class="btn btn-outline-primary btn-semi-rounded float-right mb-3">
           Back to Products
        </a>
    </div>

    <div class="card mt-3">
        <div class="card-body">
            <table class="table table-bordered" id="deleted-products-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Deleted At</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($products as $product)
                        <tr id="product-row-{{ $product->id }}">
                            <td>{{ $product->title }}</td>
                            <td>{{ $product->deleted_at }}</td>
                            <td>
                                <button class="btn btn-sm btn-primary swal-action-btn"
                                        data-id="{{ $product->id }}"
                                        data-action="restore">
                                    Restore
                                </button>

                                <button class="btn btn-sm btn-danger swal-action-btn"
                                        data-id="{{ $product->id }}"
                                        data-action="delete">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">
                                No deleted products found
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
    </div>
</div>
@endsection

@push('script')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function(){

    $(document).on('click', '.swal-action-btn', function(e){
        e.preventDefault();

        let id = $(this).data('id');
        let action = $(this).data('action'); // restore or delete

        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {

                confirmButton: "btn btn-primary mx-2 ",
                cancelButton: "btn btn-danger"
            },
            // buttonsStyling: false
        });

        let confirmText = action === 'delete' ? "Yes, permanently delete it!" : "Yes, restore it!";
        let successText = action === 'delete' ? "Product has been permanently deleted." : "Product has been restored successfully.";

        swalWithBootstrapButtons.fire({
            title: `Are you sure you want to ${action} this product?`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: confirmText,
            cancelButtonText: "No, cancel!",
            reverseButtons: true
        }).then((result) => {
            if(result.isConfirmed){
                // AJAX URL
                let url = action === 'restore'
                    ? "{{ url('/admin/product/restore') }}/" + id
                    : "{{ url('/admin/product/permanently-delete') }}/" + id; // Use permanentlyDelete

                $.ajax({
                    url: url,
                    type: "POST",
                    data: {_token: '{{ csrf_token() }}'},
                    success: function(res){
                        if(res.success){
                            swalWithBootstrapButtons.fire(
                                'Success!',
                                successText,
                                'success'
                            ).then(() => {
                                if(action === 'restore'){
                                    // redirect to index page after restore
                                    window.location.href = "{{ route('admin.product.recentlyDeletedPage') }}";
                                } else {
                                    // remove row from table after permanent delete
                                    $('#product-row-' + id).remove();

                                    // show "No products found" if table empty
                                    if($('#deleted-products-table tbody tr').length === 0){
                                        $('#deleted-products-table tbody').html('<tr><td colspan="3" class="text-center">No deleted products found</td></tr>');
                                    }
                                }
                            });
                        } else {
                            swalWithBootstrapButtons.fire('Error!', res.message, 'error');
                        }
                    },
                    error: function(){
                        swalWithBootstrapButtons.fire('Error!', 'Server error!', 'error');
                    } 
                });

            }
            // else if(result.dismiss === Swal.DismissReason.cancel){
            //     swalWithBootstrapButtons.fire('Cancelled', 'error');
            // }
        });
    });

});
</script>
@endpush
