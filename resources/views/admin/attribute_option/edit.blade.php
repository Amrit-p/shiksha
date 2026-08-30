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
            <form id="editForm" action="{{ route('admin.attribute_option.update') }}" method="POST">
              @csrf
              @method('PATCH')
              <input name="edit_id" type="hidden" class="edit_id">
              <div class="form-group">
                <label>Name <span class="text-danger">*</span></label>
                <input name="name" id="edit_name" class="form-control edit_name" required>
                <small class="text-danger d-none" id="edit_name_error">This name already exists for this attribute.</small>
              </div>

            <div class="form-group">
              <label>Attribute <span class="text-danger">*</span></label>
              <select name="attribute_id" id="edit_attribute_id" class="attribute_id_edit form-control" required>
                <option value="">Choose Attribute</option>
                @foreach ($attributes as $item)
                  <option value="{{ $item->id }}">{{ $item->name }}</option>
                @endforeach
              </select>
            </div>

              <div class="form-group">
                <label>Status</label>
                <select name="status" class="form-control edit_status">
                  <option value="">Choose Status</option>
                  <option value="1">Active</option>
                  <option value="0">Deactive</option>
                </select>
              </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary" id="edit_save_btn">Update</button>
          </div>

          </form>

 

        </div>
      </div>
    </div>


    @push('script')
      <script>
        // ✅ Real-time uniqueness validation for EDIT
        let editCheckTimeout;
        
        function checkEditUniqueness() {
          clearTimeout(editCheckTimeout);
          
          const name = $('#edit_name').val().trim();
          const attributeId = $('#edit_attribute_id').val();
          const currentId = $('.edit_id').val();
          
          // Reset if empty
          if (!name || !attributeId) {
            $('#edit_name').removeClass('is-invalid');
            $('#edit_name_error').addClass('d-none');
            $('#edit_save_btn').prop('disabled', false);
            return;
          }
          
          // Debounce check (wait 500ms after user stops typing)
          editCheckTimeout = setTimeout(function() {
            $.ajax({
              url: "{{ route('admin.attribute_option.check-unique') }}",
              type: 'POST',
              data: {
                _token: "{{ csrf_token() }}",
                name: name,
                attribute_id: attributeId,
                id: currentId  // Exclude current record from check
              },
              success: function(response) {
                if (response.exists) {
                  $('#edit_name').addClass('is-invalid');
                  $('#edit_name_error').removeClass('d-none');
                  $('#edit_save_btn').prop('disabled', true);
                } else {
                  $('#edit_name').removeClass('is-invalid');
                  $('#edit_name_error').addClass('d-none');
                  $('#edit_save_btn').prop('disabled', false);
                }
              }
            });
          }, 500);
        }
        
        // Trigger validation on name or attribute change
        $(document).on('input', '#edit_name', checkEditUniqueness);
        $(document).on('change', '#edit_attribute_id', checkEditUniqueness);

        // Original edit button logic
        $(document).on('click', '.edit_btn', function(e) {
          e.preventDefault();
          var id = $(this).data('id');

          if (!id) {
            console.error('ID not found');
            return;
          }
          
          // Reset validation state
          $('#edit_name').removeClass('is-invalid');
          $('#edit_name_error').addClass('d-none');
          $('#edit_save_btn').prop('disabled', false);
          
          console.log('Edit ID:', id);
          $('#editModal').modal('show');
          
          var url = "{{ route('admin.attribute_option.edit', ':id') }}"
          url = url.replace(':id', id);

          $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {
              console.log(response)
              let data = response.data;
              $('.edit_id').val(data.id);
              $('.edit_name').val(data.name);
              $('.attribute_id_edit').val(data?.attribute_id).change();
              $('.edit_status').val(data.status);
            },
            error: function(xhr) {
              alert('Something went wrong');
            }
          });
        });
        
      </script>
    @endpush