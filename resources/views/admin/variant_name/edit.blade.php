<!-- ===================================== -->
<!-- MODAL - Edit Variant Name -->
<!-- ===================================== -->
<div class="modal fade" id="editVariantNameModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md modal-top" role="document">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title mb-0">
                    <i class="ik {{ $heder_font }} mr-2"></i> {{ $modal_header }}
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <!-- ✅ Alert container for success/error messages -->
                <div id="editVariantNameAlert" style="display:none;" class="alert alert-dismissible fade show"
                    role="alert">
                    <span id="editVariantNameAlertText"></span>
                    <button type="button" class="close" onclick="$('#editVariantNameAlert').hide()">
                        <span>&times;</span>
                    </button>
                </div>

                <form id="editVariantNameForm" action="{{ route('admin.variant-name.update') }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <!-- HIDDEN ID -->
                    <input type="hidden" name="edit_id" class="edit_id">

                    <!-- NAME -->
                    <div class="form-group">
                        <label>Variant Name *</label>
                        <input type="text" name="name" class="form-control edit_name"
                            placeholder="e.g., Color, Size, Weight">
                        <small class="form-text text-muted">
                            Enter a unique variant name (min 2 characters)
                        </small>
                    </div>

                    <!-- STATUS -->
                    <div class="form-group">
                        <label>Status *</label>
                        <select name="status" class="form-control edit_status">
                            <option value="">Choose Status</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>

                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" form="editVariantNameForm" id="updateVariantNameBtn">
                    <span class="btn-text">Update</span>
                    <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                </button>
            </div>

        </div>
    </div>
</div>

@push('script')
    <script>
        $(document).ready(function() {

            /* ================= EDIT BUTTON CLICK ================= */
            $(document).on('click', '.edit_btn', function() {
                let id = $(this).data('id');
                if (!id) return;

                let form = $('#editVariantNameForm');
                form[0].reset();

                // Clear previous errors
                form.find('.is-invalid').removeClass('is-invalid');
                form.find('.invalid-feedback').remove();
                $('#editVariantNameAlert').hide();

                $('#editVariantNameModal').modal('show');

                let url = "{{ route('admin.variant-name.edit', ':id') }}";
                url = url.replace(':id', id);

                $.get(url, function(response) {
                    let data = response.data;

                    form.find('.edit_id').val(data.id);
                    form.find('.edit_name').val(data.name);
                    form.find('.edit_status').val(data.status);
                });
            });

            // ✅ Clear form when modal closes
            $('#editVariantNameModal').on('hidden.bs.modal', function() {
                $('#editVariantNameForm')[0].reset();
                $('#editVariantNameForm').find('.is-invalid').removeClass('is-invalid');
                $('#editVariantNameForm').find('.invalid-feedback').remove();
                $('#editVariantNameAlert').hide();
            });

            // ✅ Form submission with AJAX
            $('#editVariantNameForm').on('submit', function(e) {
                e.preventDefault();

                const form = $(this);
                const submitBtn = $('#updateVariantNameBtn');

                // Clear previous errors
                form.find('.is-invalid').removeClass('is-invalid');
                form.find('.invalid-feedback').remove();
                $('#editVariantNameAlert').hide();

                let hasError = false;

                const nameInput = form.find('input[name="name"]');
                const nameValue = nameInput.val().trim();
                const statusSelect = form.find('select[name="status"]');
                const statusValue = statusSelect.val();

                // Frontend validation - Name
                if (!nameValue) {
                    showEditError(nameInput, 'Variant name is required');
                    hasError = true;
                } else if (nameValue.length < 2) {
                    showEditError(nameInput, 'Variant name must be at least 2 characters');
                    hasError = true;
                } else if (nameValue.length > 255) {
                    showEditError(nameInput, 'Variant name must not exceed 255 characters');
                    hasError = true;
                }

                // Frontend validation - Status
                if (!statusValue) {
                    showEditError(statusSelect, 'Status is required');
                    hasError = true;
                }

                if (hasError) {
                    return false;
                }

                // Show loading state
                submitBtn.prop('disabled', true);
                submitBtn.find('.btn-text').text('Updating...');
                submitBtn.find('.spinner-border').removeClass('d-none');

                // ✅ Submit via AJAX to check uniqueness on backend
                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        // Success
                        showEditAlert('success', response.message ||
                            'Variant name updated successfully!');

                        // Hide modal after 1 second
                        setTimeout(function() {
                            $('#editVariantNameModal').modal('hide');

                            // Reload page to show updated variant
                            location.reload();
                        }, 1000);
                    },
                    error: function(xhr) {
                        // Handle validation errors
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;

                            if (errors.name) {
                                showEditError(nameInput, errors.name[0]);

                                // Also show alert for unique constraint
                                if (errors.name[0].toLowerCase().includes('taken') ||
                                    errors.name[0].toLowerCase().includes('already') ||
                                    errors.name[0].toLowerCase().includes('exist')) {
                                    showEditAlert('danger',
                                        'This variant name already exists! Please use a different name.'
                                        );
                                }
                            }

                            if (errors.status) {
                                showEditError(statusSelect, errors.status[0]);
                            }

                            if (errors.edit_id) {
                                showEditAlert('danger', errors.edit_id[0]);
                            }
                        } else {
                            // Server error
                            showEditAlert('danger', 'Something went wrong! Please try again.');
                        }
                    },
                    complete: function() {
                        // Reset button state
                        submitBtn.prop('disabled', false);
                        submitBtn.find('.btn-text').text('Update');
                        submitBtn.find('.spinner-border').addClass('d-none');
                    }
                });
            });

            function showEditError(input, message) {
                input.addClass('is-invalid');
                input.closest('.form-group')
                    .append('<div class="invalid-feedback d-block">' + message + '</div>');
            }

            function showEditAlert(type, message) {
                const alertBox = $('#editVariantNameAlert');
                const alertText = $('#editVariantNameAlertText');

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
