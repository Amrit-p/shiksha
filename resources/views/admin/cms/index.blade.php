@extends('admin.layouts.main')
@section('title','CMS Pages')
@section('content')
@include('admin.include.message')
<div class="container-fluid">
  <div class="page-header d-flex justify-content-between align-items-center">
    <div class="page-header-title">
      <i class="ik ik-file-text bg-blue"></i>
      <div class="d-inline"><h5>CMS Pages</h5></div>
    </div>
    <a href="{{ route('admin.cms.create') }}" class="btn btn-primary">Add Page</a>
  </div>
  <div class="card">
    <div class="card-body table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th>Title</th>
            <th>Slug</th>
            <th>Footer</th>
            <th>Active</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          @foreach($pages as $page)
            <tr>
              <td>{{ $page->title }}</td>
              <td>{{ $page->slug }}</td>
              <td>{{ $page->show_in_footer ? 'Yes' : 'No' }}</td>
              <td>{{ $page->is_active ? 'Yes' : 'No' }}</td>
              <td>
                <a href="{{ route('admin.cms.edit', $page->id) }}" class="btn btn-sm btn-info">Edit</a>
                <form action="{{ route('admin.cms.destroy', $page->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete page?')">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-danger" type="submit">Delete</button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
