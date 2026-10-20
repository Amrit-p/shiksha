@extends('admin.layouts.main')
@section('title', $banner->exists ? 'Edit Banner' : 'Add Banner')
@section('content')
<div class="container-fluid">
  <div class="card">
    <div class="card-header"><h3>{{ $banner->exists ? 'Edit Banner' : 'Add Banner' }}</h3></div>
    <div class="card-body">
      @if ($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
      @endif
      <form method="POST" enctype="multipart/form-data"
        action="{{ $banner->exists ? route('admin.banner.update', $banner) : route('admin.banner.store') }}">
        @csrf
        @if ($banner->exists) @method('PUT') @endif
        <div class="row">
          <div class="col-md-6 form-group">
            <label>Title / scheme name</label>
            <input name="title" class="form-control" maxlength="255" value="{{ old('title', $banner->title) }}" placeholder="e.g. Diwali scheme: 5% extra on wires">
          </div>
          <div class="col-md-3 form-group">
            <label>Button text</label>
            <input name="btn_text" class="form-control" maxlength="50" value="{{ old('btn_text', $banner->btn_text) }}" placeholder="Optional">
          </div>
          <div class="col-md-3 form-group">
            <label>Button link</label>
            <input name="btn_link" type="url" class="form-control" maxlength="255" value="{{ old('btn_link', $banner->btn_link) }}" placeholder="https://...">
          </div>
          <div class="col-md-6 form-group">
            <label>Image {{ $banner->exists ? '(leave empty to keep current)' : '*' }}</label>
            <input type="file" name="image" accept="image/*" class="form-control" {{ $banner->exists ? '' : 'required' }}>
            <small class="form-text text-muted">Wide image works best (about 1600 &times; 500). Max 4 MB.</small>
            @if ($banner->image)
              <img src="{{ asset('storage/'.$banner->image) }}" alt="" class="mt-2" style="max-width:100%;max-height:120px;">
            @endif
          </div>
          <div class="col-md-2 form-group">
            <label>Show from</label>
            <input type="date" name="starts_at" class="form-control" value="{{ old('starts_at', $banner->starts_at?->toDateString()) }}">
          </div>
          <div class="col-md-2 form-group">
            <label>Show until</label>
            <input type="date" name="ends_at" class="form-control" value="{{ old('ends_at', $banner->ends_at?->toDateString()) }}">
          </div>
          <div class="col-md-2 form-group">
            <label>Sort order</label>
            <input type="number" min="0" name="sort_order" class="form-control" value="{{ old('sort_order', $banner->sort_order) }}">
          </div>
          <div class="col-md-12 form-group">
            <label class="mr-4 mb-0"><input type="checkbox" name="status" value="1" @checked(old('status', (int) $banner->status === 1))> Active</label>
            <label class="mb-0"><input type="checkbox" name="show_in_popup" value="1" @checked(old('show_in_popup', $banner->show_in_popup))> Show in "Current Schemes" popup</label>
          </div>
        </div>
        <button class="btn btn-primary" type="submit">Save Banner</button>
        <a href="{{ route('admin.banner') }}" class="btn btn-secondary">Cancel</a>
      </form>
    </div>
  </div>
</div>
@endsection
