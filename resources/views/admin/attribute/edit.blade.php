<!-- ===================================== -->
<!-- MODAL - Edit Attribute -->
<!-- ===================================== -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editAttributeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title mb-0">
                    <i class="ik ik-edit mr-2"></i> Edit Attribute
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <!-- ✅ Alert container for success/error messages -->
                <div id="editAttributeAlert" style="display:none;" class="alert alert-dismissible fade show"
                    role="alert">
                    <span id="editAttributeAlertText"></span>
                    <button type="button" class="close" onclick="$('#editAttributeAlert').hide()">
                        <span>&times;</span>
                    </button>
                </div>

                <form id="editAttributeForm" action="{{ route('admin.attribute.update') }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <!-- HIDDEN ID -->
                    <input type="hidden" name="edit_id" class="edit_id">

                    <!-- NAME FIELD -->
                    <div class="form-group">
                        <label>Attribute Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control edit_name"
                            placeholder="e.g., Color, Size, Brand..." value="">
                        <small class="form-text text-muted">
                            Enter a unique attribute name (min 2 characters)
                        </small>
                    </div>

                    <!-- STATUS FIELD -->
                    <div class="form-group">
                        <label>Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-control edit_status">
                            <option value="">Choose Status</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>

                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary" form="editAttributeForm" id="updateAttributeBtn">
                    <span class="btn-text">Update Attribute</span>
                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                </button>
            </div>

        </div>
    </div>
</div>

@push('script')
    <script>
        $(document).ready(function() {

            /* ================= EDIT BUTTON CLICK ================= */
            $(document).on('click', '.edit_btn', function(e) {
                e.preventDefault();

                let id = $(this).data('id');

                if (!id) {
                    console.error('ID not found');
                    alert('Error: Unable to get attribute ID');
                    return;
                }

                console.log('Edit Attribute ID:', id);

                let form = $('#editAttributeForm');
                form[0].reset();

                // Clear previous errors
                form.find('.is-invalid').removeClass('is-invalid');
                form.find('.invalid-feedback').remove();
                $('#editAttributeAlert').hide();

                // Open modal
                $('#editModal').modal('show');

                // Build URL and fetch attribute data
                let url = "{{ route('admin.attribute.edit', ':id') }}";
                url = url.replace(':id', id);

                $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        console.log('Fetch Response:', response);

                        if (response.success && response.data) {
                            let data = response.data;
                            $('.edit_id').val(data.id);
                            $('.edit_name').val(data.name);
                            $('.edit_status').val(data.status);
                            console.log('Form populated with data:', data);
                        } else {
                            showEditAlert('danger', 'Error: Could not load attribute data');
                            $('#editModal').modal('hide');
                        }
                    },
                    error: function(xhr) {
                        console.error('AJAX Error:', xhr);
                        showEditAlert('danger', 'Error: Failed to load attribute data!');
                        $('#editModal').modal('hide');
                    }
                });
            });

            /* ================= CLEAR FORM WHEN MODAL CLOSES ================= */
            $('#editModal').on('hidden.bs.modal', function() {
                $('#editAttributeForm')[0].reset();
                $('#editAttributeForm').find('.is-invalid').removeClass('is-invalid');
                $('#editAttributeForm').find('.invalid-feedback').remove();
                $('#editAttributeAlert').hide();
            });

            /* ================= FORM SUBMISSION WITH AJAX ================= */
            $('#editAttributeForm').on('submit', function(e) {
                e.preventDefault();

                const form = $(this);
                const submitBtn = $('#updateAttributeBtn');

                // Clear previous errors
                form.find('.is-invalid').removeClass('is-invalid');
                form.find('.invalid-feedback').remove();
                $('#editAttributeAlert').hide();

                let hasError = false;

                const nameInput = form.find('input[name="name"]');
                const nameValue = nameInput.val().trim();
                const statusSelect = form.find('select[name="status"]');
                const statusValue = statusSelect.val();

                // Frontend validation - Name
                if (!nameValue) {
                    showEditError(nameInput, 'Attribute name is required');
                    hasError = true;
                } else if (nameValue.length < 2) {
                    showEditError(nameInput, 'Attribute name must be at least 2 characters');
                    hasError = true;
                } else if (nameValue.length > 255) {
                    showEditError(nameInput, 'Attribute name must not exceed 255 characters');
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

                // ✅ Submit via AJAX
                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: form.serialize(),
                    dataType: 'json',
                    success: function(response) {
                        console.log('Success Response:', response);

                        // Show success message
                        showEditAlert('success', response.message ||
                            'Attribute updated successfully!');

                        // Hide modal and reload after 1 second
                        setTimeout(function() {
                            $('#editModal').modal('hide');
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
                                showEditError(nameInput, nameError);

                                // Show alert for unique constraint
                                if (nameError.toLowerCase().includes('already been taken') ||
                                    nameError.toLowerCase().includes('unique')) {
                                    showEditAlert('danger', '⚠️ ' + nameError);
                                }
                            }

                            // Handle status errors
                            if (errors.status) {
                                showEditError(statusSelect, errors.status[0]);
                            }

                            // Handle edit_id errors
                            if (errors.edit_id) {
                                showEditAlert('danger', 'Error: ' + errors.edit_id[0]);
                            }
                        } else if (xhr.status === 422) {
                            showEditAlert('danger',
                                'Validation error occurred. Please check your input.');
                        } else {
                            showEditAlert('danger', 'Something went wrong! Please try again.');
                        }
                    },
                    complete: function() {
                        // Reset button state
                        submitBtn.prop('disabled', false);
                        submitBtn.find('.btn-text').text('Update Attribute');
                        submitBtn.find('.spinner-border').addClass('d-none');
                    }
                });
            });

            /* ================= HELPER FUNCTIONS ================= */
            function showEditError(input, message) {
                input.addClass('is-invalid');
                input.closest('.form-group')
                    .append('<div class="invalid-feedback d-block">' + message + '</div>');
            }

            function showEditAlert(type, message) {
                const alertBox = $('#editAttributeAlert');
                const alertText = $('#editAttributeAlertText');

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
