<div class="modal fade" id="cancelOrderModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Cancel order</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="cancel_order_id" value="">
                <div class="form-group mb-0">
                    <label>Cancel note <span class="text-danger">*</span></label>
                    <textarea id="cancel_note" class="form-control" rows="4" placeholder="Reason for cancellation (required)"></textarea>
                    <small class="text-danger d-none" id="cancel_note_error"></small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-danger" id="confirm_cancel_order">Confirm cancel</button>
            </div>
        </div>
    </div>
</div>
