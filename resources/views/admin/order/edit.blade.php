    <!-- Edit Category Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editCategoryModalLabel"
      aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">

          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title mb-0">
              <i class="ik {{ $heder_font }} mr-2"></i> {{ $modal_header }}
            </h5>
            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>


          <div class="modal-body">
            <form action="{{ route('admin.order.update') }}" method="POST">
              @csrf
              @method('PATCH')
              <input name="edit_id" type="hidden" class="edit_id">
              {{-- <div class="form-group">
                <label>Name</label>
                <input name="name" class="form-control edit_name">
              </div> --}}

              <div class="form-group">
              <label for="order_status">Order Status</label>
              <select class="form-control" id="order_status" name="order_status">
                  <option value="pending">Pending</option>
                  <option value="order_received">Order Received</option>
                  <option value="fulfilled">Fulfilled</option>
                  <option value="dispatched">Dispatched</option>
                  <option value="on_the_way">On the Way</option>
                  {{-- <option value="payment_received">Payment Received</option> --}}
                  {{-- <option value="packed">Packed</option> --}}
                  {{-- <option value="completed">Completed</option> --}}
                  {{-- <option value="out_for_delivery">Out for Delivery</option> --}}
                  <option value="delivered">Delivered</option>
                  <option value="rto">RTO</option>
                  <option value="cancelled">Cancelled</option>
                  {{-- <option value="payment_failed">Payment Failed</option> --}}
                  {{-- <option value="returned">Returned</option> --}}
                  {{-- <option value="refunded">Refunded</option> --}}
              </select>
          </div>

              {{-- <div class="form-group">
                <label>Status</label>
                <select name="status" class="form-control edit_status">
                  <option value="">Choose Status</option>
                  <option value="1">Active</option>
                  <option value="0">Deactive</option>
                </select>
              </div> --}}
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Update Order</button>
          </div>
          </form>
        </div>
      </div>
    </div>


    @push('script')
      <script>
        $(document).on('click', '.edit_btn', function(e) {
          e.preventDefault();

 
          var id = $(this).data('id');
            // alert(id);


          if (!id) {
            console.error('ID not found');
            return;
          }
          // Debug
          console.log('Edit ID:', id);
          // Example 1: Open Bootstrap 4 modal
          $('#editModal').modal('show');
          // Example 2: Load data via AJAX (Laravel style)
          var url = "{{ route('admin.order.edit', ':id') }}"
          url = url.replace(':id', id);

          $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {



              // alert('test');
              // alert('test edit');
              let data = response.data;
              $('.edit_id').val(data.id);
              $('#order_status').val(data.order_status);
              $('.edit_status').val(data.status);
            },
            error: function(xhr) {
              alert('Something went wrong');
            }
          });
        });
        
      </script>
    @endpush
