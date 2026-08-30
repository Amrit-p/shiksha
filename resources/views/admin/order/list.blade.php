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

  $(document).on('click', '.make-paid-btn', function (e) {
    e.preventDefault();
    const id = $(this).data('id');
    if (!id || !confirm('Mark this order as paid?')) return;
    $.ajax({
      url: "{{ route('admin.order.make_paid') }}",
      type: 'POST',
      data: { _token: csrfToken, order_id: id },
      success: function (res) {
        if (res.success) {
          if (ordersTable) ordersTable.ajax.reload(null, false);
        } else {
          alert(res.message || 'Failed');
        }
      },
      error: function (xhr) {
        const m = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Request failed';
        alert(m);
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
