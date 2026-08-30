@extends('admin.layouts.main')
@section('title','Edit Homepage Section')
@section('content')
@include('admin.include.message')
<div class="container-fluid">
  <div class="card">
    <div class="card-header"><h3>Edit Section: {{ $section->key }}</h3></div>
    <div class="card-body">
      <form method="POST" action="{{ route('admin.homepage.update', $section->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="form-group"><label>Title</label><input class="form-control" name="title" value="{{ old('title', $section->title) }}"></div>
        <div class="form-group"><label>Subtitle</label><input class="form-control" name="subtitle" value="{{ old('subtitle', $section->subtitle) }}"></div>
        <div class="form-group"><label>Content</label><textarea class="form-control" name="content" rows="8">{{ old('content', $section->content) }}</textarea></div>
        <div class="form-group"><label>Button Text</label><input class="form-control" name="button_text" value="{{ old('button_text', $section->button_text) }}"></div>
        <div class="form-group"><label>Button Link</label><input class="form-control" name="button_link" value="{{ old('button_link', $section->button_link) }}"></div>
        <div class="form-group"><label>Image</label><input type="file" class="form-control" name="image" accept="image/*"></div>
        <div class="form-group"><label>Sort Order</label><input type="number" class="form-control" name="sort_order" value="{{ old('sort_order', $section->sort_order) }}"></div>
        <div class="form-check mb-3">
          <input class="form-check-input" type="checkbox" name="is_active" value="1" @checked(old('is_active', $section->is_active))>
          <label class="form-check-label">Active</label>
        </div>
        <button class="btn btn-primary" type="submit">Save</button>
      </form>
    </div>
  </div>
</div>
@endsection
