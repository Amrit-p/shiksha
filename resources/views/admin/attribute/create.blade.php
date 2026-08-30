<!-- Add Attribute Modal -->
<div class="modal fade" id="addAttributeModal" tabindex="-1" role="dialog" aria-labelledby="addAttributeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title mb-0">
                    <i class="ik ik-plus mr-2"></i> Add Attribute
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <!-- ✅ Alert container for success/error messages -->
                <div id="createAttributeAlert" style="display:none;" class="alert alert-dismissible fade show"
                    role="alert">
                    <span id="createAttributeAlertText"></span>
                    <button type="button" class="close" onclick="$('#createAttributeAlert').hide()">
                        <span>&times;</span>
                    </button>
                </div>

                <form id="attributeForm" action="{{ route('admin.attribute.store') }}" method="POST">
                    @csrf

                    <!-- NAME FIELD -->
                    <div class="form-group">
                        <label>Attribute Name <span class="text-danger">*</span></label>
                        <input placeholder="e.g., Color, Size, Brand..." type="text" name="name"
                            class="form-control" value="">
                        <small class="form-text text-muted">
                            Enter a unique attribute name (min 2 characters)
                        </small>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" form="attributeForm" id="createAttributeBtn">
                    <span class="btn-text">Save</span>
                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                </button>
            </div>

        </div>
    </div>
</div>

@push('script')
    <script>
        $(document).ready(function() {

            /* ================= CLEAR FORM WHEN MODAL OPENS ================= */
            $('#addAttributeModal').on('show.bs.modal', function() {
                $('#attributeForm')[0].reset();
                $('#attributeForm').find('.is-invalid').removeClass('is-invalid');
                $('#attributeForm').find('.invalid-feedback').remove();
                $('#createAttributeAlert').hide();
            });

            /* ================= CLEAR FORM WHEN MODAL CLOSES ================= */
            $('#addAttributeModal').on('hidden.bs.modal', function() {
                $('#attributeForm')[0].reset();
                $('#attributeForm').find('.is-invalid').removeClass('is-invalid');
                $('#attributeForm').find('.invalid-feedback').remove();
                $('#createAttributeAlert').hide();
            });

            /* ================= FORM SUBMISSION WITH AJAX ================= */
            $('#attributeForm').on('submit', function(e) {
                e.preventDefault();

                const form = $(this);
                const submitBtn = $('#createAttributeBtn');

                // Clear previous errors
                form.find('.is-invalid').removeClass('is-invalid');
                form.find('.invalid-feedback').remove();
                $('#createAttributeAlert').hide();

                let hasError = false;

                const nameInput = form.find('input[name="name"]');
                const nameValue = nameInput.val().trim();

                // Frontend validation - Name
                if (!nameValue) {
                    showCreateError(nameInput, 'Attribute name is required');
                    hasError = true;
                } else if (nameValue.length < 2) {
                    showCreateError(nameInput, 'Attribute name must be at least 2 characters');
                    hasError = true;
                } else if (nameValue.length > 255) {
                    showCreateError(nameInput, 'Attribute name must not exceed 255 characters');
                    hasError = true;
                }

                if (hasError) {
                    return false;
                }

                // Show loading state
                submitBtn.prop('disabled', true);
                submitBtn.find('.btn-text').text('Saving...');
                submitBtn.find('.spinner-border').removeClass('d-none');

                // ✅ Submit via AJAX
                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: form.serialize(),
                    dataType: 'json',
                    success: function(response) {
                        console.log('Success Response:', response);

                        // Show success message
                        showCreateAlert('success', response.message ||
                            'Attribute created successfully!');

                        // Clear form
                        form[0].reset();

                        // Hide modal and reload after 1 second
                        setTimeout(function() {
                            $('#addAttributeModal').modal('hide');
                            location.reload();
                        }, 1000);
                    },
                    error: function(xhr) {
                        console.log('Error Response:', xhr);
                        console.log('Status:', xhr.status);
                        console.log('Response JSON:', xhr.responseJSON);

                        // Handle validation errors (422 Unprocessable Entity)
                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                            const errors = xhr.responseJSON.errors;
                            console.log('Validation Errors:', errors);

                            // Handle name errors
                            if (errors.name) {
                                const nameError = errors.name[0];
                                console.log('Name Error:', nameError);
                                showCreateError(nameInput, nameError);

                                // Show alert for unique constraint
                                if (nameError.toLowerCase().includes('already been taken') ||
                                    nameError.toLowerCase().includes('unique')) {
                                    showCreateAlert('danger', '⚠️ ' + nameError);
                                }
                            }
                        } else if (xhr.status === 422) {
                            showCreateAlert('danger',
                                'Validation error occurred. Please check your input.');
                        } else {
                            showCreateAlert('danger',
                            'Something went wrong! Please try again.');
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

            /* ================= HELPER FUNCTIONS ================= */
            function showCreateError(input, message) {
                input.addClass('is-invalid');
                input.closest('.form-group')
                    .append('<div class="invalid-feedback d-block">' + message + '</div>');
            }

            function showCreateAlert(type, message) {
                const alertBox = $('#createAttributeAlert');
                const alertText = $('#createAttributeAlertText');

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
