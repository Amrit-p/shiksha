@extends('admin.layouts.main')
@section('title', 'Edit Custom Field Type')

@section('content')
<div class="container-fluid">
    
    <div class="page-header mb-4">
        <div class="row align-items-end">
            <div class="col-lg-8">
                <div class="page-header-title">
                    <i class="ik ik-layers bg-primary text-white p-3 rounded-circle"></i>
                    <div class="d-inline-block ms-3">
                        <h4 class="mb-0">Edit Custom Field Type</h4>
                        <small class="text-muted">Update custom selection field</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.custom-field-type.update', $customFieldType->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="fw-bold">Field Name <span class="text-danger">*</span></label>
                        <input type="text" 
                               name="name" 
                               class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name', $customFieldType->name) }}"
                               placeholder="e.g., Shape, Size, Material"
                               required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">This will be shown as the field label</small>
                    </div>

                    <div class="col-md-3">
                        <label class="fw-bold">Display Order</label>
                        <input type="number" 
                               name="display_order" 
                               class="form-control @error('display_order') is-invalid @enderror" 
                               value="{{ old('display_order', $customFieldType->display_order) }}"
                               min="0">
                        @error('display_order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Lower numbers appear first</small>
                    </div>

                    <div class="col-md-3">
                        <label class="fw-bold">Status</label>
                        <select name="status" class="form-control">
                            <option value="1" {{ old('status', $customFieldType->status) == 1 ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('status', $customFieldType->status) == 0 ? 'selected' : '' }}>Inactive</option>
                        </select>
                        <small class="text-muted">Active fields can be used</small>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="fw-bold">Description (Optional)</label>
                    <textarea name="description" 
                              class="form-control @error('description') is-invalid @enderror" 
                              rows="3"
                              placeholder="Brief description of this field type">{{ old('description', $customFieldType->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                @if($customFieldType->variantCustomFields->count() > 0)
                    <div class="alert alert-warning">
                        <i class="ik ik-alert-circle mr-2"></i>
                        <strong>Note:</strong> This field type is currently being used in 
                        <strong>{{ $customFieldType->variantCustomFields->count() }}</strong> variant(s).
                        Changes will affect all products using this field.
                    </div>
                @endif

                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.custom-field-type.index') }}" class="btn btn-secondary">
                        <i class="ik ik-arrow-left"></i> Back
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="ik ik-save"></i> Update Field Type
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection