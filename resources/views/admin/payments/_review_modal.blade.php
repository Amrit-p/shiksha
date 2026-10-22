{{-- Reject / reverse a payment with a required reason. Buttons with .payment-reason-btn fill it in. --}}
<div class="modal fade" id="paymentReasonModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form class="modal-content" method="POST" id="paymentReasonForm">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title mb-0" id="paymentReasonTitle">Reject payment</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <p class="small text-muted" id="paymentReasonHint"></p>
        <label>Reason <span class="text-danger">*</span></label>
        <textarea name="reason" class="form-control" rows="3" minlength="3" maxlength="1000" required></textarea>
        <small class="form-text text-muted">The salesman sees this reason.</small>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-danger" id="paymentReasonSubmit">Reject</button>
      </div>
    </form>
  </div>
</div>
@push('script')
<script>
  $(document).on('click', '.payment-reason-btn', function () {
    const reverse = $(this).data('action') === 'reverse';
    $('#paymentReasonForm').attr('action', $(this).data('url'))[0].reset();
    $('#paymentReasonTitle').text((reverse ? 'Reverse' : 'Reject') + ' payment #' + $(this).data('id'));
    $('#paymentReasonSubmit').text(reverse ? 'Reverse' : 'Reject');
    $('#paymentReasonHint').text(reverse
      ? 'The amount stops counting as collected and the order balance goes back up.'
      : 'Rejection is final. The salesman will need to log a new payment.');
    $('#paymentReasonModal').modal('show');
  });
</script>
@endpush
