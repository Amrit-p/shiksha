<div class="modal fade" id="addBannerModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md modal-top" role="document">
      <div class="modal-content">

        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title mb-0">
            <i class="ik ik-sliders mr-2"></i> Add Attribute Option
          </h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>



        <div class="modal-body">
          <form id="bannerForm" action="{{ route('admin.attribute_option.store') }}" method="POST"
            enctype="multipart/form-data">
            @csrf

            <div class="form-group">
              <label>Name <span class="text-danger">*</span></label>
              <input placeholder="Red.." type="text" name="name" id="create_name" class="form-control" required>
              <small class="text-danger d-none" id="create_name_error">This name already exists for this attribute.</small>
            </div>

            <div class="form-group">
              <label>Attribute <span class="text-danger">*</span></label>
              <select name="attribute_id" id="create_attribute_id" class="form-control" required>
                <option value="">Choose Attribute</option>
                @foreach ($attributes as $item)
                  <option value="{{ $item->id }}">{{ $item->name }}</option>
                @endforeach
              </select>
            </div>

            <div class="form-group">
              <label>Choose Status</label>
              <select name="status" class="form-control">
                <option selected value="1">Active</option>
                <option value="0">Deactive</option>
              </select>
            </div>
          </form>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" id="create_save_btn" form="bannerForm">Save</button>
        </div>

      </div>
    </div>
  </div>

  @push('script')
  <script>
    // ✅ Real-time uniqueness validation for CREATE
    let createCheckTimeout;
    
    function checkCreateUniqueness() {
      clearTimeout(createCheckTimeout);
      
      const name = $('#create_name').val().trim();
      const attributeId = $('#create_attribute_id').val();
      
      // Reset if empty
      if (!name || !attributeId) {
        $('#create_name').removeClass('is-invalid');
        $('#create_name_error').addClass('d-none');
        $('#create_save_btn').prop('disabled', false);
        return;
      }
      
      // Debounce check (wait 500ms after user stops typing)
      createCheckTimeout = setTimeout(function() {
        $.ajax({
          url: "{{ route('admin.attribute_option.check-unique') }}",
          type: 'POST',
          data: {
            _token: "{{ csrf_token() }}",
            name: name,
            attribute_id: attributeId
          },
          success: function(response) {
            if (response.exists) {
              $('#create_name').addClass('is-invalid');
              $('#create_name_error').removeClass('d-none');
              $('#create_save_btn').prop('disabled', true);
            } else {
              $('#create_name').removeClass('is-invalid');
              $('#create_name_error').addClass('d-none');
              $('#create_save_btn').prop('disabled', false);
            }
          }
        });
      }, 500);
    }
    
    // Trigger validation on name or attribute change
    $(document).on('input', '#create_name', checkCreateUniqueness);
    $(document).on('change', '#create_attribute_id', checkCreateUniqueness);
    
    // Reset form when modal closes
    $('#addBannerModal').on('hidden.bs.modal', function () {
      $('#bannerForm')[0].reset();
      $('#create_name').removeClass('is-invalid');
      $('#create_name_error').addClass('d-none');
      $('#create_save_btn').prop('disabled', false);
    });
  </script>
  @endpush