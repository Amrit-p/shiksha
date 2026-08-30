@push('script')
<script>
    $(document).on('click', '.delete_btn', function (e) {
        e.preventDefault();

        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {

                var _id = $(this).data('id');

                var url = "{{ route('admin.product-code-type.delete', ':id') }}";
                url = url.replace(':id', _id);

                $.ajax({
                    url: url,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (res) {
                        if (res && res.status === false) {
                            Swal.fire("Cannot delete", res.message || "This type is still in use.", "warning");
                            return;
                        }
                        Swal.fire(
                            "Deleted!",
                            (res && res.message) ? res.message : "Product Code Type has been deleted.",
                            "success"
                        );
                        $('#my-datatable').DataTable().ajax.reload();
                    },
                    error: function (xhr) {
                        var msg = "Something went wrong while deleting.";
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        Swal.fire("Cannot delete", msg, "error");
                    }
                });
            }
        });
    });
</script>
@endpush
