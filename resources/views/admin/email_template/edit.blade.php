@extends('admin.layouts.main')
@section('title','Edit Email Template')
@section('content')
@include('admin.include.message')
<div class="container-fluid">
  <div class="card">
    <div class="card-header"><h3>Edit Template</h3></div>
    <div class="card-body">
      <form method="POST" action="{{ route('admin.email-templates.update', $template->id) }}">
        @csrf
        @method('PUT')
        <div class="form-group"><label>Name</label><input class="form-control" name="name" value="{{ old('name', $template->name) }}" required></div>
        <div class="form-group"><label>Subject</label><input class="form-control" name="subject" value="{{ old('subject', $template->subject) }}" required></div>
        <div class="form-group"><label>Body (HTML allowed)</label><textarea class="form-control" name="body" rows="12" required>{{ old('body', $template->body) }}</textarea></div>
        <div class="form-check mb-3">
          <input class="form-check-input" type="checkbox" name="is_active" value="1" @checked(old('is_active', $template->is_active))>
          <label class="form-check-label">Active</label>
        </div>
        <button class="btn btn-primary" type="submit">Save</button>
      </form>
    </div>
  </div>
</div>
@endsection
