<!-- ===================================== -->
<!-- MODAL - Add Variant Name -->
<!-- ===================================== -->
<div class="modal fade" id="addVariantNameModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md modal-top" role="document">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title mb-0">
                    <i class="ik ik-sliders mr-2"></i> Add Variant Name
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <!-- ✅ Alert container for success/error messages -->
                <div id="variantNameAlert" style="display:none;" class="alert alert-dismissible fade show"
                    role="alert">
                    <span id="variantNameAlertText"></span>
                    <button type="button" class="close" onclick="$('#variantNameAlert').hide()">
                        <span>&times;</span>
                    </button>
                </div>

                <form id="variantNameForm" action="{{ route('admin.variant-name.store') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label>Variant Name *</label>
                        <input type="text" name="name" class="form-control"
                            placeholder="e.g., Color, Size, Weight">
                        <small class="form-text text-muted">
                            Enter a unique variant name (min 2 characters)
                        </small>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" form="variantNameForm" id="saveVariantNameBtn">
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
            $('#addVariantNameModal').on('show.bs.modal', function() {
                $('#variantNameForm')[0].reset();
                $('#variantNameForm').find('.is-invalid').removeClass('is-invalid');
                $('#variantNameForm').find('.invalid-feedback').remove();
                $('#variantNameAlert').hide();
            });

            // ✅ Form submission with AJAX
            $('#variantNameForm').on('submit', function(e) {
                e.preventDefault();

                const form = $(this);
                const submitBtn = $('#saveVariantNameBtn');

                // Clear previous errors
                form.find('.is-invalid').removeClass('is-invalid');
                form.find('.invalid-feedback').remove();
                $('#variantNameAlert').hide();

                let hasError = false;

                const nameInput = form.find('input[name="name"]');
                const nameValue = nameInput.val().trim();

                // Frontend validation
                if (!nameValue) {
                    showError(nameInput, 'Variant name is required');
                    hasError = true;
                } else if (nameValue.length < 2) {
                    showError(nameInput, 'Variant name must be at least 2 characters');
                    hasError = true;
                } else if (nameValue.length > 255) {
                    showError(nameInput, 'Variant name must not exceed 255 characters');
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
                        showAlert('success', response.message ||
                            'Variant name added successfully!');

                        // Reset form
                        form[0].reset();

                        // Hide modal after 1 second
                        setTimeout(function() {
                            $('#addVariantNameModal').modal('hide');

                            // Reload page to show new variant in dropdown
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
                                        'This variant name already exists! Please use a different name.'
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
                const alertBox = $('#variantNameAlert');
                const alertText = $('#variantNameAlertText');

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
