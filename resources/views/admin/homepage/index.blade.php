@extends('admin.layouts.main')
@section('title','Homepage Sections')
@section('content')
@include('admin.include.message')
<div class="container-fluid">
  <div class="card">
    <div class="card-header"><h3>Homepage Sections</h3></div>
    <div class="card-body table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th>Key</th>
            <th>Title</th>
            <th>Active</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          @foreach($sections as $section)
            <tr>
              <td>{{ $section->key }}</td>
              <td>{{ $section->title }}</td>
              <td>{{ $section->is_active ? 'Yes' : 'No' }}</td>
              <td><a class="btn btn-sm btn-info" href="{{ route('admin.homepage.edit', $section->id) }}">Edit</a></td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
