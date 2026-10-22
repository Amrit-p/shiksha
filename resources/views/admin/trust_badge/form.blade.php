@extends('admin.layouts.main')
@section('title', $badge->exists ? 'Edit Trust Badge' : 'Add Trust Badge')
@section('content')
<div class="container-fluid">
  <div class="card">
    <div class="card-header"><h3>{{ $badge->exists ? 'Edit Trust Badge' : 'Add Trust Badge' }}</h3></div>
    <div class="card-body">
      @if ($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
      @endif
      <form method="POST" enctype="multipart/form-data"
        action="{{ $badge->exists ? route('admin.trust-badges.update', $badge) : route('admin.trust-badges.store') }}">
        @csrf
        @if ($badge->exists) @method('PUT') @endif
        <div class="row">
          <div class="col-md-5 form-group">
            <label>Title *</label>
            <input name="title" class="form-control" maxlength="60" required value="{{ old('title', $badge->title) }}" placeholder="e.g. ISI Certified">
          </div>
          <div class="col-md-5 form-group">
            <label>Short line</label>
            <input name="subtitle" class="form-control" maxlength="120" value="{{ old('subtitle', $badge->subtitle) }}" placeholder="e.g. Quality tested products">
          </div>
          <div class="col-md-2 form-group">
            <label>Sort order</label>
            <input type="number" min="0" name="sort_order" class="form-control" value="{{ old('sort_order', $badge->sort_order) }}">
          </div>
          <div class="col-md-6 form-group">
            <label>Icon {{ $badge->exists ? '(leave empty to keep current)' : '*' }}</label>
            <input type="file" name="icon" accept="image/png,image/jpeg,image/webp" class="form-control" {{ $badge->exists ? '' : 'required' }}>
            <small class="form-text text-muted">Square PNG/JPG/WebP, ideally transparent background, about 128 &times; 128. Max 1 MB.</small>
            @if ($badge->icon)
              <img src="{{ asset('storage/'.$badge->icon) }}" alt="" class="mt-2" style="width:64px;height:64px;object-fit:contain;">
            @endif
          </div>
          <div class="col-md-12 form-group">
            <label class="mb-0"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $badge->is_active))> Show on salesman site</label>
          </div>
        </div>
        <button class="btn btn-primary" type="submit">Save Badge</button>
        <a href="{{ route('admin.trust-badges.index') }}" class="btn btn-secondary">Cancel</a>
      </form>
    </div>
  </div>
</div>
@endsection
