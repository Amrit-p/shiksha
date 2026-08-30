  
<div class="modal fade" id="addBannerModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-md modal-top" role="document">
    <div class="modal-content">

<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0">
        <i class="ik ik-sliders mr-2"></i> Add Color
    </h5>

    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>



      <div class="modal-body">
        <form id="bannerForm" action="{{ route('admin.color.store') }}" method="POST" enctype="multipart/form-data">
          @csrf

          <div class="form-group">
            <label>Name</label>
            <input placeholder="Color.." type="text" name="name" class="form-control">
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

 
