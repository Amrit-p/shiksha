<div class="modal fade" id="addUnitModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md modal-top" role="document">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title mb-0">
                    <i class="ik ik-package mr-2"></i> Add Unit
                </h5>

                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form id="unitForm"
                      action="{{ route('admin.unit.store') }}"
                      method="POST">
                    @csrf

                    <div class="form-group">
                        <label>Unit Name *</label>
                        <input type="text"
                               name="name"
                               class="form-control"
                               placeholder="Meter, Piece, Roll">
                    </div>

                    <div class="form-group">
                        <label>Short Name *</label>
                        <input type="text"
                               name="short_name"
                               class="form-control"
                               placeholder="m, pcs, roll">
                    </div>

                    {{-- optional: conversion logic ready --}}
                    {{-- <div class="form-group">
                        <label>Multiplier (Base Unit)</label>
                        <input type="number"
                               step="0.0001"
                               name="multiplier"
                               class="form-control"
                               placeholder="1, 9, 12 etc">
                        <small class="text-muted">
                            Example: 1 Roll = 9 Meter → multiplier = 9
                        </small>
                    </div> --}}

                </form>
            </div>

            <div class="modal-footer">
                <button type="button"
                        class="btn btn-secondary"
                        data-dismiss="modal">
                    Cancel
                </button>

                <button type="submit"
                        class="btn btn-primary"
                        form="unitForm">
                    Save
                </button>
            </div>

        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('unitForm');

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        // Remove old errors
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        form.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

        let hasError = false;

        const nameInput = form.querySelector('input[name="name"]');
        const shortNameInput = form.querySelector('input[name="short_name"]');

        // Unit Name validation
        if (!nameInput.value.trim()) {
            showError(nameInput, 'Unit name is required');
            hasError = true;
        }

        // Short Name validation
        if (!shortNameInput.value.trim()) {
            showError(shortNameInput, 'Short name is required');
            hasError = true;
        }

        if (!hasError) {
            form.submit();
        }
    });

    function showError(input, message) {
        input.classList.add('is-invalid');

        const error = document.createElement('div');
        error.className = 'invalid-feedback';
        error.innerText = message;

        input.parentNode.appendChild(error);
    }

});
</script>
