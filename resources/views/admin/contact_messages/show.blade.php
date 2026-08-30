@extends('admin.layouts.main')
@section('title','Contact Message')
@section('content')
@include('admin.include.message')
<div class="container-fluid">
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h3 class="mb-0">Contact Message</h3>
      <a href="{{ route('admin.contact-messages.index') }}" class="btn btn-sm btn-secondary">&larr; Back to list</a>
    </div>
    <div class="card-body">
      <dl class="row">
        <dt class="col-sm-3">Name</dt>
        <dd class="col-sm-9">{{ $message->name }}</dd>

        <dt class="col-sm-3">Email</dt>
        <dd class="col-sm-9"><a href="mailto:{{ $message->email }}">{{ $message->email }}</a></dd>

        <dt class="col-sm-3">Phone</dt>
        <dd class="col-sm-9">{{ $message->phone ?: '—' }}</dd>

        <dt class="col-sm-3">Received</dt>
        <dd class="col-sm-9">{{ $message->created_at->format('d M Y, h:i A') }}</dd>

        <dt class="col-sm-3">IP address</dt>
        <dd class="col-sm-9">{{ $message->ip_address ?: '—' }}</dd>
      </dl>

      <hr>
      <h5>Message</h5>
      <p style="white-space:pre-wrap;">{{ $message->message }}</p>

      <div class="mt-4">
        <a class="btn btn-primary" href="mailto:{{ $message->email }}?subject=Re: your enquiry">Reply by email</a>
        <form action="{{ route('admin.contact-messages.destroy', $message->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this message?')">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-danger">Delete</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
