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

                var url = "{{ route('admin.variant-name.delete', ':id') }}";
                url = url.replace(':id', _id);

                $.ajax({
                    url: url,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function () {
                        Swal.fire(
                            "Deleted!",
                            "Variant name has been deleted.",
                            "success"
                        );
                        $('#my-datatable').DataTable().ajax.reload();
                    },
                    error: function () {
                        Swal.fire(
                            "Error!",
                            "Something went wrong while deleting.",
                            "error"
                        );
                    }
                });
            }
        });
    });
</script>
@endpush
