    <!-- Order Status Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editCategoryModalLabel"
      aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title mb-0">
              <i class="ik {{ $heder_font }} mr-2"></i> {{ $modal_header }} <span class="edit_order_no"></span>
            </h5>
            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>

          <form action="{{ route('admin.order.update') }}" method="POST" id="orderStatusForm">
          <div class="modal-body">
              @csrf
              @method('PATCH')
              <input name="edit_id" type="hidden" class="edit_id">

              <div class="form-group">
                <label for="order_status">Order Status</label>
                <select class="form-control" id="order_status" name="order_status">
                  <option value="pending">Pending</option>
                  <option value="order_received">Order Received</option>
                  <option value="fulfilled">Fulfilled</option>
                  <option value="dispatched">Dispatched</option>
                  <option value="on_the_way">On the Way</option>
                  <option value="partially_delivered">Partial Pending (some items delivered)</option>
                  <option value="delivered">Delivered (all items)</option>
                  <option value="rto">RTO</option>
                  <option value="cancelled">Cancelled</option>
                </select>
                <small class="form-text text-muted" id="delivery_summary"></small>
              </div>

              {{-- Shown for Partial Pending / Delivered while items are pending: records one delivery round. --}}
              <div id="delivery-fields" style="display:none;">
                <div class="table-responsive">
                  <table class="table table-bordered table-sm mb-2">
                    <thead class="thead-light">
                      <tr>
                        <th>Item</th>
                        <th style="width:80px;">Ordered</th>
                        <th style="width:85px;">Delivered</th>
                        <th style="width:75px;">Pending</th>
                        <th style="width:120px;">Deliver now</th>
                      </tr>
                    </thead>
                    <tbody id="delivery-lines"></tbody>
                  </table>
                </div>
                <div class="row">
                  <div class="col-md-4 form-group">
                    <label>Delivered on</label>
                    <input type="date" name="delivered_on" class="form-control" value="{{ now()->toDateString() }}" max="{{ now()->toDateString() }}">
                  </div>
                  <div class="col-md-8 form-group">
                    <label>Note (optional)</label>
                    <input type="text" name="note" class="form-control" maxlength="1000" placeholder="e.g. LR no., vehicle, rest next week">
                  </div>
                </div>
                <small class="text-muted" id="delivery_hint"></small>
              </div>
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
        (function () {
          var orderLines = [];
          var pendingTotal = 0;

          function esc(s) { return $('<div>').text(s == null ? '' : s).html(); }

          function renderDeliveryFields() {
            var status = $('#order_status').val();
            var isDelivery = status === 'partially_delivered' || status === 'delivered';
            var show = isDelivery && pendingTotal > 0;

            $('#delivery-fields').toggle(show);
            $('#delivery-fields input').prop('disabled', !show);
            if (!show) return;

            // Delivered: everything pending goes now. Partial Pending: admin enters what went out.
            var rows = orderLines.map(function (l) {
              var input = l.pending > 0
                ? '<input type="number" class="form-control form-control-sm" min="0" max="' + l.pending + '" name="qty[' + esc(l.key) + ']" value="' + (status === 'delivered' ? l.pending : 0) + '"' + (status === 'delivered' ? ' readonly' : '') + '>'
                : '<span class="text-success">Done</span>';
              return '<tr>'
                + '<td><b>' + esc(l.name) + '</b>' + (l.detail ? '<div><small>' + esc(l.detail) + '</small></div>' : '') + '</td>'
                + '<td>' + l.ordered + '</td><td>' + l.delivered + '</td>'
                + '<td class="' + (l.pending > 0 ? 'text-danger font-weight-bold' : 'text-success') + '">' + l.pending + '</td>'
                + '<td>' + input + '</td></tr>';
            });
            $('#delivery-lines').html(rows.join(''));
            $('#delivery_hint').text(status === 'delivered'
              ? 'All pending items will be marked delivered.'
              : 'Enter the quantity delivered now. Items left over stay pending and the order shows as Partial Pending.');
          }

          $(document).on('change', '#order_status', renderDeliveryFields);

          $(document).on('submit', '#orderStatusForm', function (e) {
            var status = $('#order_status').val();
            if (status !== 'partially_delivered' || pendingTotal <= 0) return;
            var any = $('#delivery-lines input').toArray().some(function (el) { return parseInt(el.value, 10) > 0; });
            if (!any) {
              e.preventDefault();
              $('#delivery_hint').addClass('text-danger').text('Enter a delivered quantity for at least one item.');
            }
          });

          $(document).on('click', '.edit_btn', function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            if (!id) return;

            orderLines = [];
            pendingTotal = 0;
            $('#delivery-fields').hide();
            $('#delivery_summary, #delivery_hint').text('').removeClass('text-danger');
            $('.edit_order_no').text('#' + id);
            $('#editModal').modal('show');

            var url = "{{ route('admin.order.edit', ':id') }}".replace(':id', id);
            $.get(url).done(function (response) {
              var data = response.data;
              orderLines = response.lines || [];
              pendingTotal = response.pending || 0;

              $('.edit_id').val(data.id);
              $('#order_status').val(data.order_status);

              var ordered = orderLines.reduce(function (s, l) { return s + l.ordered; }, 0);
              if (ordered > 0) {
                $('#delivery_summary').text((ordered - pendingTotal) + ' of ' + ordered + ' items delivered, ' + pendingTotal + ' pending.');
              }
              $('#order_status option[value="partially_delivered"], #order_status option[value="delivered"]')
                .prop('disabled', !!response.is_cancelled);
              $('#order_status option[value="partially_delivered"]').prop('disabled', !!response.is_cancelled || pendingTotal <= 0);

              renderDeliveryFields();
            }).fail(function () {
              $('#delivery_summary').addClass('text-danger').text('Could not load order.');
            });
          });
        })();
      </script>
    @endpush
