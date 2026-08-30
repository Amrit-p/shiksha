@extends('admin.layouts.main')
@section('title','Contact Messages')
@section('content')
@include('admin.include.message')
<div class="container-fluid">
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h3 class="mb-0">Contact Messages</h3>
      @if($unread)
        <span class="badge badge-danger">{{ $unread }} unread</span>
      @endif
    </div>
    <div class="card-body table-responsive">
      <table class="table table-hover">
        <thead>
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Message</th>
            <th>Received</th>
            <th>Status</th>
            <th class="text-right">Action</th>
          </tr>
        </thead>
        <tbody>
          @forelse($messages as $m)
            <tr class="{{ $m->is_read ? '' : 'font-weight-bold' }}">
              <td>{{ $m->name }}</td>
              <td><a href="mailto:{{ $m->email }}">{{ $m->email }}</a></td>
              <td>{{ $m->phone ?: '—' }}</td>
              <td>{{ \Illuminate\Support\Str::limit($m->message, 60) }}</td>
              <td>{{ $m->created_at->format('d M Y, h:i A') }}</td>
              <td>
                @if($m->is_read)
                  <span class="badge badge-secondary">Read</span>
                @else
                  <span class="badge badge-success">New</span>
                @endif
              </td>
              <td class="text-right">
                <a class="btn btn-sm btn-info" href="{{ route('admin.contact-messages.show', $m->id) }}">View</a>
                <form action="{{ route('admin.contact-messages.destroy', $m->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this message?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="7" class="text-center text-muted py-4">No contact messages yet.</td></tr>
          @endforelse
        </tbody>
      </table>

      @if($messages->hasPages())
        <div class="mt-3">{{ $messages->links('pagination::bootstrap-4') }}</div>
      @endif
    </div>
  </div>
</div>
@endsection
