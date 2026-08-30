<!-- Edit Unit Modal -->
<div class="modal fade" id="editUnitModal" tabindex="-1" role="dialog"
     aria-labelledby="editUnitModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title mb-0">
                    <i class="ik {{ $heder_font }} mr-2"></i> {{ $modal_header }}
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <form id="editUnitForm" action="{{ route('admin.unit.update') }}" method="POST">
                @csrf
                @method('PATCH')

                <div class="modal-body">

                    <input type="hidden" name="edit_id" class="edit_id">

                    <div class="form-group">
                        <label>Unit Name *</label>
                        <input type="text" name="name" class="form-control edit_name">
                    </div>

                    <div class="form-group">
                        <label>Short Name *</label>
                        <input type="text" name="short_name" class="form-control edit_short_name">
                    </div>

                    <div class="form-group">
                        <label>Status *</label>
                        <select name="status" class="form-control edit_status">
                            <option value="">Choose Status</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Close
                    </button>
                    <button type="submit" class="btn btn-primary">
                        Update Unit
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
@push('script')
<script>
$(document).ready(function () {

    /* ===================== EDIT BUTTON CLICK ===================== */
    $(document).on('click', '.edit_btn', function (e) {
        e.preventDefault();

        const id = $(this).data('id');
        if (!id) return;

        // Reset form + errors
        const form = $('#editUnitForm');
        form[0].reset();
        clearErrors(form);

        $('#editUnitModal').modal('show');

        let url = "{{ route('admin.unit.edit', ':id') }}";
        url = url.replace(':id', id);

        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                const data = response.data;

                form.find('.edit_id').val(data.id);
                form.find('.edit_name').val(data.name);
                form.find('.edit_short_name').val(data.short_name);
                form.find('.edit_status').val(data.status);
            },
            error: function () {
                alert('Something went wrong');
            }
        });
    });

    /* ===================== FORM VALIDATION ===================== */
    $('#editUnitForm').on('submit', function (e) {
        e.preventDefault();

        const form = $(this);
        clearErrors(form);

        let hasError = false;

        const name = form.find('input[name="name"]');
        const shortName = form.find('input[name="short_name"]');
        const status = form.find('select[name="status"]');

        if (!name.val().trim()) {
            showError(name, 'Unit name is required');
            hasError = true;
        }

        if (!shortName.val().trim()) {
            showError(shortName, 'Short name is required');
            hasError = true;
        }

        if (!status.val()) {
            showError(status, 'Please select status');
            hasError = true;
        }

        if (!hasError) {
            form[0].submit();
        }
    });

    /* ===================== HELPERS ===================== */
    function showError(input, message) {
        input.addClass('is-invalid');
        input.closest('.form-group')
             .append('<div class="invalid-feedback">' + message + '</div>');
    }

    function clearErrors(form) {
        form.find('.is-invalid').removeClass('is-invalid');
        form.find('.invalid-feedback').remove();
    }

});
</script>
@endpush
