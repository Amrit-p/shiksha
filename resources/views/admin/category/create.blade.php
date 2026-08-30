@push('head')
  <style>
    .image-upload-box {
      width: 150px;
      height: 150px;
      border: 2px dashed #ced4da;
      border-radius: 6px;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      background: #f8f9fa;
      position: relative;
    }

    .image-upload-box:hover {
      border-color: #007bff;
    }

    .image-upload-box img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: none;
    }

    .placeholder-text {
      color: #6c757d;
      font-size: 14px;
    }
  </style>
@endpush

<div class="modal fade" id="addBannerModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-md modal-top" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title mb-0">
          <i class="ik ik-image mr-2"></i> {{ $modal_header }}
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>


      <div class="modal-body">
        <form id="bannerForm" action="{{ route('admin.category.store') }}" method="POST" enctype="multipart/form-data">
          @csrf
               <div class="form-group">
                  <label>Category Name</label>
                  <input placeholder="type here.." type="text" name="name" class="form-control" required>
                </div>

                <div class="form-group">
                  <label>Parent Category</label>
                  <select name="category_id" class="form-control">
                    <option value="">Choose Parent Category</option>
                    @foreach ($data['categories'] as $item)
                      <option value="{{ $item->id }}">{{ $item->name }}</option>
                    @endforeach
                  </select>
                </div>

                <div class="form-group">
                  <label>Description</label>
                  <textarea placeholder="type here.." name="description" class="form-control" rows="3"></textarea>
                </div>

             <div class="form-group">
                <label>Is Home</label>
                <select name="is_home" id="is_home" class="is_home form-control edit_status">
                  {{-- <option value="">Choose Option</option> --}}
                  <option value="1">yes</option>
                  <option value="0">no</option>
                </select>
              </div>

                <div class="form-group">
                  <label>Status</label>
                  <select name="status" class="form-control">
                    <option value="1">Active</option>
                    <option value="0">Deactive</option>
                  </select>
                </div>

          <!-- IMAGE SELECT BOX -->
          <div class="form-group">
            <label>Category Image</label>
            <!-- LABEL acts as clickable upload box -->
            {{-- <label for="imageInput" class="image-upload-box">
              <span class="placeholder-text">Click to upload</span>
              <img id="previewImage">
            </label> --}}

            <!-- REAL file input (NOT hidden attribute) -->
            {{-- <input type="file" name="image" id="imageInput" accept="image/*" class="d-none"> --}}
            <input type="file" name="image" id="imageInput" accept="image/*">
          </div>
        </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary" form="bannerForm">Save</button>
      </div>

    </div>
  </div>
</div>

@push('script')
  <script>
    $(document).on('change', '#imageInput', function() {
      if (this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
          $('#previewImage').attr('src', e.target.result).show();
          $('.placeholder-text').hide();
        };
        reader.readAsDataURL(this.files[0]);
      }
    });
  </script>
@endpush
