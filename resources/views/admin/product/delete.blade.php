 @push('script')
  <script>
      $(document).on('click', '.delete_btn', function(e) {
        e.preventDefault();
        // alert('deleted');
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
            var _id = $(this).data('id'); // make sure 'this' is correct in context
            // alert(_id);
            url = "{{ route('admin.product.delete',':id') }}";
            url = url.replace(':id', _id)
            $.ajax({
              url: url,
              type: 'DELETE',
              data: {
                _token: '{{ csrf_token() }}'
              },
              success: function(response) {
                Swal.fire(
                  "Deleted!",
                  "Your file has been deleted.",
                  "success"
                );
                $('.dataTable').DataTable().ajax.reload();
              },
              error: function(xhr) {
                Swal.fire(
                  "Error!",
                  "An error occurred while deleting the client.",
                  "error"
                );
              }
            });
          }
        });
      });
  </script>

  @endpush