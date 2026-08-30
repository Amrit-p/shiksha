@extends('admin.layouts.main')
@section('title','Email Templates')
@section('content')
@include('admin.include.message')
<div class="container-fluid">
  <div class="card">
    <div class="card-header"><h3>Email Templates</h3></div>
    <div class="card-body table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th>Name</th>
            <th>Slug</th>
            <th>Subject</th>
            <th>Active</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          @foreach($templates as $template)
            <tr>
              <td>{{ $template->name }}</td>
              <td>{{ $template->slug }}</td>
              <td>{{ $template->subject }}</td>
              <td>{{ $template->is_active ? 'Yes' : 'No' }}</td>
              <td><a class="btn btn-sm btn-info" href="{{ route('admin.email-templates.edit', $template->id) }}">Edit</a></td>
            </tr>
          @endforeach
        </tbody>
      </table>
      <p class="text-muted mt-3">Supported variables: @{{customer_name}}, @{{enquiry_id}}, @{{customer_email}}, @{{customer_phone}}, @{{enquiry_status}}, @{{products}}, @{{enquiry_url}}</p>
    </div>
  </div>
</div>
@endsection
