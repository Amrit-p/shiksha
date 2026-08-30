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
            <form action="{{ route('admin.category.update') }}" method="POST" enctype="multipart/form-data">
              @csrf
              @method('PATCH')
              <input name="edit_id" type="hidden" class="edit_id">
              <div class="form-group">
                <label>Name</label>
                <input name="name" class="form-control edit_name">
              </div>

              <div class="form-group">
                <label>Parent Category</label>
                <select name="category_id" class="edit_category_id form-control">
                  <option  value="">Choose Parent Category</option>
                  @foreach ($data['categories'] as $item)
                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="form-group">
                <label>Description</label>
                <textarea name="description" class="form-control edit_description" rows="3"></textarea>
              </div>


              <div class="form-group">
                <label>Is Home</label>
                <select name="is_home" class="form-control edit_status">
                  <option value="">Choose Option</option>
                  <option value="1">yes</option>
                  <option value="0">no</option>
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

          <!-- IMAGE SELECT BOX -->
          <div class="form-group" style="margin-left:20px">
            <label>Category Image</label>
            <input type="file" name="image" id="imageInput" accept="image/*">
          </div>

          
          {{-- <!-- IMAGE SELECT BOX -->
          <div class="form-group" style="margin-left:20px">
            <label>Category Image</label>
            <!-- LABEL acts as clickable upload box -->
            <label for="imageInput" class="image-upload-box">
              <span class="placeholder-text">Click to upload</span>
              <img id="previewImage">
            </label>
            <!-- REAL file input (NOT hidden attribute) -->
            <input type="file" name="image" id="imageInput" accept="image/*" class="d-none">
          </div> --}}

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Update Category</button>
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

          if (!id) {
            console.error('ID not found');
            return;
          }
          // Debug
          console.log('Edit ID:', id);
          // Example 1: Open Bootstrap 4 modal
          $('#editModal').modal('show');
          // Example 2: Load data via AJAX (Laravel style)
          var url = "{{ route('admin.category.edit', ':id') }}"
          url = url.replace(':id', id);

          $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {
              // alert('test edit');

              // var fix_category_id = "";

              // if(data.category_id != 0){
              //   fix_category_id = data.category_id;
              // }
              


              let data = response.data;
              $('.edit_id').val(data.id);
              $('.is_home').val(data.is_home);
              $('.edit_name').val(data.name);
              $('.edit_category_id').val(data.category_id);
              $('.edit_description').text(data.description);
              $('.edit_status').val(data.status);
            },
            error: function(xhr) {
              alert('Something went wrong');
            }
          });
        });
        
      </script>
    @endpush
