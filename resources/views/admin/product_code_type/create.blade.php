<!-- ===================================== -->
<!-- MODAL - Add Product Code Type -->
<!-- ===================================== -->
<div class="modal fade" id="addProductCodeTypeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md modal-top" role="document">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title mb-0">
                    <i class="ik ik-sliders mr-2"></i> Add Product Code Type
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <!-- ✅ Alert container for success/error messages -->
                <div id="productCodeTypeAlert" style="display:none;" class="alert alert-dismissible fade show" role="alert">
                    <span id="productCodeTypeAlertText"></span>
                    <button type="button" class="close" onclick="$('#productCodeTypeAlert').hide()">
                        <span>&times;</span>
                    </button>
                </div>

                <form id="productCodeTypeForm" action="{{ route('admin.product-code-type.store') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label>Product Code Type *</label>
                        <input type="text" name="name" class="form-control"
                               placeholder="e.g., SKU Type, Barcode Type, Internal Code">
                        <small class="form-text text-muted">
                            Enter a unique product code type (min 2 characters)
                        </small>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" form="productCodeTypeForm" id="saveProductCodeTypeBtn">
                    <span class="btn-text">Save</span>
                    <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                </button>
            </div>

        </div>
    </div>
</div>

@push('script')
<script>
    $(document).ready(function() {

        // ✅ Clear form when modal opens
        $('#addProductCodeTypeModal').on('show.bs.modal', function() {
            $('#productCodeTypeForm')[0].reset();
            $('#productCodeTypeForm').find('.is-invalid').removeClass('is-invalid');
            $('#productCodeTypeForm').find('.invalid-feedback').remove();
            $('#productCodeTypeAlert').hide();
        });

        // ✅ Form submission with AJAX
        $('#productCodeTypeForm').on('submit', function(e) {
            e.preventDefault();

            const form = $(this);
            const submitBtn = $('#saveProductCodeTypeBtn');

            // Clear previous errors
            form.find('.is-invalid').removeClass('is-invalid');
            form.find('.invalid-feedback').remove();
            $('#productCodeTypeAlert').hide();

            let hasError = false;

            const nameInput = form.find('input[name="name"]');
            const nameValue = nameInput.val().trim();

            // Frontend validation
            if (!nameValue) {
                showError(nameInput, 'Product code type is required');
                hasError = true;
            } else if (nameValue.length < 2) {
                showError(nameInput, 'Product code type must be at least 2 characters');
                hasError = true;
            } else if (nameValue.length > 255) {
                showError(nameInput, 'Product code type must not exceed 255 characters');
                hasError = true;
            }

            if (hasError) {
                return false;
            }

            // Show loading state
            submitBtn.prop('disabled', true);
            submitBtn.find('.btn-text').text('Saving...');
            submitBtn.find('.spinner-border').removeClass('d-none');

            // ✅ Submit via AJAX to check uniqueness on backend
            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: form.serialize(),
                success: function(response) {
                    // Success
                    showAlert('success', response.message || 'Product code type added successfully!');

                    // Reset form
                    form[0].reset();

                    // Hide modal after 1 second
                    setTimeout(function() {
                        $('#addProductCodeTypeModal').modal('hide');

                        // Reload page to show new value in dropdown
                        location.reload();
                    }, 1000);
                },
                error: function(xhr) {
                    // Handle validation errors
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;

                        if (errors.name) {
                            showError(nameInput, errors.name[0]);

                            // Also show alert for unique constraint
                            if (errors.name[0].toLowerCase().includes('taken') ||
                                errors.name[0].toLowerCase().includes('already') ||
                                errors.name[0].toLowerCase().includes('exist')) {
                                showAlert('danger',
                                    'This product code type already exists! Please use a different name.'
                                );
                            }
                        }
                    } else {
                        // Server error
                        showAlert('danger', 'Something went wrong! Please try again.');
                    }
                },
                complete: function() {
                    // Reset button state
                    submitBtn.prop('disabled', false);
                    submitBtn.find('.btn-text').text('Save');
                    submitBtn.find('.spinner-border').addClass('d-none');
                }
            });
        });

        function showError(input, message) {
            input.addClass('is-invalid');
            input.closest('.form-group')
                .append('<div class="invalid-feedback d-block">' + message + '</div>');
        }

        function showAlert(type, message) {
            const alertBox = $('#productCodeTypeAlert');
            const alertText = $('#productCodeTypeAlertText');

            alertBox.removeClass('alert-success alert-danger alert-warning');
            alertBox.addClass('alert-' + type);
            alertText.text(message);
            alertBox.show();

            // Auto-hide success alerts after 3 seconds
            if (type === 'success') {
                setTimeout(function() {
                    alertBox.fadeOut();
                }, 3000);
            }
        }

    });
</script>
@endpush
