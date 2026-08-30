@extends('admin.layouts.main')
@section('title', 'Custom Field Types')

@section('content')
<div class="container-fluid">
    
    <div class="page-header mb-4">
        <div class="row align-items-end">
            <div class="col-lg-8">
                <div class="page-header-title">
                    <i class="ik ik-layers bg-primary text-white p-3 rounded-circle"></i>
                    <div class="d-inline-block ms-3">
                        <h4 class="mb-0">Custom Field Types</h4>
                        <small class="text-muted">Manage custom selection fields (Shape, Size, Material, etc.)</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 text-end">
                <a href="{{ route('admin.custom-field-type.create') }}" class="btn btn-primary">
                    <i class="ik ik-plus"></i> Add New Field Type
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            {{ $errors->first() }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">#</th>
                            <th width="25%">Name</th>
                            <th width="15%">Slug</th>
                            <th width="30%">Description</th>
                            <th width="10%">Order</th>
                            <th width="10%">Status</th>
                            <th width="5%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customFieldType as $index => $type)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <strong>{{ $type->name }}</strong>
                                    <br>
                                    <small class="text-muted">
                                        Used in {{ $type->variantCustomFields->count() }} variant(s)
                                    </small>
                                </td>
                                <td>
                                    <code>{{ $type->slug }}</code>
                                </td>
                                <td>
                                    <small>{{ $type->description ?? '-' }}</small>
                                </td>
                                <td>{{ $type->display_order }}</td>
                                <td>
                                    <form action="{{ route('admin.custom-field-type.toggle-status', $type->id) }}" 
                                          method="POST" 
                                          style="display:inline;">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" 
                                                class="btn btn-sm {{ $type->status ? 'btn-success' : 'btn-secondary' }}">
                                            {{ $type->status ? 'Active' : 'Inactive' }}
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.custom-field-type.edit', $type->id) }}" 
                                           class="btn btn-outline-primary" 
                                           title="Edit">
                                            <i class="ik ik-edit"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-outline-danger" 
                                                onclick="confirmDelete({{ $type->id }})"
                                                title="Delete">
                                            <i class="ik ik-trash-2"></i>
                                        </button>
                                    </div>

                                    <form id="delete-form-{{ $type->id }}" 
                                          action="{{ route('admin.custom-field-type.destroy', $type->id) }}" 
                                          method="POST" 
                                          style="display:none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="ik ik-inbox" style="font-size:48px;opacity:0.3;"></i>
                                    <p class="text-muted mt-2">No custom field types found. Click "Add New Field Type" to create one.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="alert alert-info mt-4">
        <i class="ik ik-info mr-2"></i>
        <strong>What are Custom Field Types?</strong>
        <p class="mb-0 mt-2">
            Custom field types are additional selection options you can add to variant products. 
            For example: Shape (Round/Square), Size (6 inch/8 inch), Material (Plastic/Metal), etc.
            <br>
            These appear as dropdown options when creating products, similar to how "Variant Type" works.
        </p>
    </div>

</div>
@endsection

@push('script')
<script>
function confirmDelete(id) {
    if (confirm('Are you sure you want to delete this custom field type?')) {
        document.getElementById('delete-form-' + id).submit();
    }
}
</script>
@endpush