@extends('admin.layouts.main')
@section('title', 'Add Custom Field Type')

@section('content')
<div class="container-fluid">
    
    <div class="page-header mb-4">
        <div class="row align-items-end">
            <div class="col-lg-8">
                <div class="page-header-title">
                    <i class="ik ik-layers bg-primary text-white p-3 rounded-circle"></i>
                    <div class="d-inline-block ms-3">
                        <h4 class="mb-0">Add Custom Field Type</h4>
                        <small class="text-muted">Create a new custom selection field</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.custom-field-type.store') }}" method="POST">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="fw-bold">Field Name <span class="text-danger">*</span></label>
                        <input type="text" 
                               name="name" 
                               class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name') }}"
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
                               value="{{ old('display_order', 0) }}"
                               min="0">
                        @error('display_order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Lower numbers appear first</small>
                    </div>

                    <div class="col-md-3">
                        <label class="fw-bold">Status</label>
                        <select name="status" class="form-control">
                            <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>Inactive</option>
                        </select>
                        <small class="text-muted">Active fields can be used</small>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="fw-bold">Description (Optional)</label>
                    <textarea name="description" 
                              class="form-control @error('description') is-invalid @enderror" 
                              rows="3"
                              placeholder="Brief description of this field type">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="alert alert-info">
                    <i class="ik ik-info mr-2"></i>
                    <strong>Examples:</strong>
                    <ul class="mb-0 mt-2">
                        <li><strong>Shape</strong> - For products with different shapes (Round, Square, Rectangular)</li>
                        <li><strong>Size</strong> - For products with different sizes (6 inch, 8 inch, 10 inch)</li>
                        <li><strong>Material</strong> - For products with different materials (Plastic, Metal, Wood)</li>
                        <li><strong>Finish</strong> - For products with different finishes (Matte, Glossy, Brushed)</li>
                    </ul>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.custom-field-type.index') }}" class="btn btn-secondary">
                        <i class="ik ik-arrow-left"></i> Back
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="ik ik-save"></i> Save Field Type
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection