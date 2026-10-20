@push('script')
<script>
  let ordersTable = null;
  const csrfToken = '{{ csrf_token() }}';

  function getSalesmanId() {
    return typeof window.__SALESMAN_ORDER_LIST__ !== 'undefined' && window.__SALESMAN_ORDER_LIST__
      ? String(window.__SALESMAN_ORDER_LIST__)
      : '';
  }

  function getOrderStatusFilter() {
    const $el = $('#order_status_filter');
    return $el.length ? ($el.val() || '') : '';
  }

  $(document).ready(function () {
    load_datatable();

    // Reload data only — do not destroy/reinit (that + .empty() removed <thead> and hid headers).
    $('#order_status_filter').on('change', function () {
      if (ordersTable) {
        ordersTable.ajax.reload(null, true);
      }
    });
  });

  function load_datatable() {
    if (ordersTable) {
      ordersTable.destroy();
      ordersTable = null;
    }

    ordersTable = $("#my-datatable").DataTable({
      order: [],
      processing: true,
      serverSide: true,
      serverMethod: 'GET',
      ajax: {
        url: "{{ route('admin.order.list') }}",
        data: function (d) {
          d.salesman_id = getSalesmanId();
          d.order_status = getOrderStatusFilter();
        }
      },
      columns: [
        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
        { data: 'shop_name', name: 'shop_name' },
        { data: 'owner_name', name: 'owner_name' },
        { data: 'owner_address', name: 'owner_address' },
        { data: 'total_amount', name: 'total_amount' },
        { data: 'order_status', name: 'order_status' },
        { data: 'delivery', name: 'delivery', orderable: false, searchable: false },
        { data: 'payment_status', name: 'payment_status' },
        { data: 'user_name', name: 'user_name' },
        { data: 'created_at', name: 'created_at' },
        { data: 'action', name: 'action', orderable: false, searchable: false }
      ],
      dom: "<'row'<'col-sm-6'><'col-sm-6'f>>" +
           "<'row'<'col-sm-12'tr>>" +
           "<'row'<'col-sm-5'li><'col-sm-7'p>>",
      language: {
        processing: '<i class="ace-icon fa fa-spinner fa-spin orange bigger-500" style="font-size:60px;margin-top:50px;"></i>',
        lengthMenu: "Show _MENU_ entries",
        info: "Showing _START_ to _END_ of _TOTAL_ entries"
      },
      order: [[0, "desc"]],
    });

    $(document).off('keyup', '#global_filter').on('keyup', '#global_filter', function() {
      ordersTable.search($(this).val()).draw();
    });
  }

  // Delivery Details popup: per-item ordered / delivered / pending + delivery history.
  $(document).on('click', '.delivery-details-btn', function (e) {
    e.preventDefault();
    const id = $(this).data('id');
    const $body = $('#deliveryDetailsBody');
    $body.html('<div class="p-4 text-center text-muted">Loading...</div>');
    $('#deliveryDetailsModal').modal('show');
    $.get("{{ route('admin.order.delivery.details', ':id') }}".replace(':id', id))
      .done(function (html) { $body.html(html); })
      .fail(function () { $body.html('<div class="p-4 text-danger">Could not load delivery details.</div>'); });
  });

  // Record Payment: admin-entered payments are verified immediately and drive the order's payment status.
  $(document).on('click', '.make-paid-btn', function (e) {
    e.preventDefault();
    const $form = $('#recordPaymentForm');
    $form[0].reset();
    $('#record_payment_order_id').val($(this).data('id'));
    $('#record_payment_amount').val($(this).data('balance'));
    $('#record_payment_paid_on').val(new Date().toISOString().slice(0, 10));
    $('#record_payment_balance').text(Number($(this).data('balance')).toFixed(2));
    $('#record_payment_error').addClass('d-none').text('');
    $('#recordPaymentModal').modal('show');
  });

  $(document).on('submit', '#recordPaymentForm', function (e) {
    e.preventDefault();
    const $err = $('#record_payment_error');
    $err.addClass('d-none').text('');
    $.ajax({
      url: "{{ route('admin.order.make_paid') }}",
      type: 'POST',
      data: $(this).serialize() + '&_token=' + encodeURIComponent(csrfToken),
      success: function (res) {
        $('#recordPaymentModal').modal('hide');
        if (ordersTable) ordersTable.ajax.reload(null, false);
      },
      error: function (xhr) {
        const r = xhr.responseJSON || {};
        const first = r.errors ? Object.values(r.errors)[0][0] : null;
        $err.removeClass('d-none').text(first || r.message || 'Request failed');
      }
    });
  });

  $(document).on('click', '.cancel-order-btn', function (e) {
    e.preventDefault();
    const id = $(this).data('id');
    $('#cancel_order_id').val(id);
    $('#cancel_note').val('');
    $('#cancel_note_error').addClass('d-none').text('');
    $('#cancelOrderModal').modal('show');
  });

  $(document).on('click', '#confirm_cancel_order', function () {
    const id = $('#cancel_order_id').val();
    const note = ($('#cancel_note').val() || '').trim();
    const $err = $('#cancel_note_error');
    $err.addClass('d-none').text('');
    if (note.length < 3) {
      $err.removeClass('d-none').text('Cancel note is required (at least 3 characters).');
      return;
    }
    $.ajax({
      url: "{{ route('admin.order.cancel') }}",
      type: 'POST',
      data: { _token: csrfToken, order_id: id, cancel_note: note },
      success: function (res) {
        if (res.success) {
          $('#cancelOrderModal').modal('hide');
          if (ordersTable) ordersTable.ajax.reload(null, false);
        } else {
          alert(res.message || 'Failed');
        }
      },
      error: function (xhr) {
        let msg = 'Request failed';
        if (xhr.responseJSON) {
          if (xhr.responseJSON.message) msg = xhr.responseJSON.message;
          if (xhr.responseJSON.errors && xhr.responseJSON.errors.cancel_note) {
            msg = xhr.responseJSON.errors.cancel_note[0];
          }
        }
        $('#cancel_note_error').removeClass('d-none').text(msg);
      }
    });
  });
</script>
@endpush

<div class="modal fade" id="recordPaymentModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form class="modal-content" id="recordPaymentForm">
      <div class="modal-header">
        <h5 class="modal-title mb-0"><i class="ik ik-dollar-sign mr-2"></i> Record Payment</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="order_id" id="record_payment_order_id">
        <p class="text-muted small mb-3">Balance to collect: <b>Rs. <span id="record_payment_balance">0.00</span></b>. Payments you record here count as verified.</p>
        <div class="form-row">
          <div class="form-group col-6">
            <label>Amount</label>
            <input type="number" step="0.01" min="0.01" class="form-control" name="amount" id="record_payment_amount" required>
          </div>
          <div class="form-group col-6">
            <label>Received on</label>
            <input type="date" class="form-control" name="paid_on" id="record_payment_paid_on" max="{{ now()->toDateString() }}" required>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-6">
            <label>Mode</label>
            <select class="form-control" name="mode" required>
              @foreach(\App\Models\OrderPayment::MODES as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group col-6">
            <label>Reference</label>
            <input class="form-control" name="reference" maxlength="100" placeholder="UPI / cheque no.">
          </div>
        </div>
        <div class="form-group mb-0">
          <label>Note</label>
          <textarea class="form-control" name="note" rows="2" maxlength="1000"></textarea>
        </div>
        <div class="alert alert-danger mt-3 mb-0 d-none" id="record_payment_error"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-success">Save Payment</button>
      </div>
    </form>
  </div>
</div>

<div class="modal fade" id="deliveryDetailsModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title mb-0"><i class="ik ik-truck mr-2"></i> Delivery Details</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body p-0" id="deliveryDetailsBody"></div>
    </div>
  </div>
</div>
