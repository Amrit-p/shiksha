@extends('admin.layouts.main')
@section('title', $page->exists ? 'Edit Page' : 'Create Page')
@section('content')
@include('admin.include.message')
<div class="container-fluid">
  <div class="card">
    <div class="card-header"><h3>{{ $page->exists ? 'Edit' : 'Create' }} CMS Page</h3></div>
    <div class="card-body">
      <form method="POST" action="{{ $page->exists ? route('admin.cms.update', $page->id) : route('admin.cms.store') }}">
        @csrf
        @if($page->exists) @method('PUT') @endif
        <div class="form-group">
          <label>Title</label>
          <input type="text" name="title" class="form-control" value="{{ old('title', $page->title) }}" required>
        </div>
        <div class="form-group">
          <label>Slug</label>
          <input type="text" name="slug" class="form-control" value="{{ old('slug', $page->slug) }}">
        </div>
        <div class="form-group">
          <label>Content</label>
          <textarea name="content" class="form-control" rows="10">{{ old('content', $page->content) }}</textarea>
        </div>
        <div class="form-group">
          <label>Meta Title</label>
          <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title', $page->meta_title) }}">
        </div>
        <div class="form-group">
          <label>Meta Description</label>
          <textarea name="meta_description" class="form-control" rows="3">{{ old('meta_description', $page->meta_description) }}</textarea>
        </div>
        <div class="form-group">
          <label>Meta Keywords</label>
          <input type="text" name="meta_keywords" class="form-control" value="{{ old('meta_keywords', $page->meta_keywords) }}">
        </div>
        <div class="form-group">
          <label>Sort Order</label>
          <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $page->sort_order ?? 0) }}">
        </div>
        <div class="form-check mb-2">
          <input class="form-check-input" type="checkbox" name="show_in_footer" value="1" @checked(old('show_in_footer', $page->show_in_footer))>
          <label class="form-check-label">Show in footer</label>
        </div>
        <div class="form-check mb-3">
          <input class="form-check-input" type="checkbox" name="is_active" value="1" @checked(old('is_active', $page->is_active ?? true))>
          <label class="form-check-label">Active</label>
        </div>
        <button class="btn btn-primary" type="submit">Save</button>
      </form>
    </div>
  </div>
</div>
@endsection
