@extends('admin.layouts.main')
@section('title', 'Trust Badges')
@section('content')
<div class="container-fluid">
  <div class="row">@include('admin.include.message')</div>
  <div class="card">
    <div class="card-header d-flex justify-content-between">
      <div>
        <h3 class="mb-0">Trust Badges</h3>
        <small class="text-muted">Shown as a small sliding strip on the salesman home page (under the scheme banner) and on every product page (under Add to Cart).</small>
      </div>
      <a href="{{ route('admin.trust-badges.create') }}" class="btn btn-primary btn-sm align-self-start"><i class="ik ik-plus"></i> Add Badge</a>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-bordered mb-0">
          <thead>
            <tr><th style="width:80px;">Icon</th><th>Title</th><th>Short line</th><th style="width:80px;">Order</th><th style="width:100px;">Status</th><th style="width:120px;">Action</th></tr>
          </thead>
          <tbody>
            @forelse ($badges as $badge)
              <tr>
                <td><img src="{{ asset('storage/'.$badge->icon) }}" alt="" style="width:48px;height:48px;object-fit:contain;"></td>
                <td><b>{{ $badge->title }}</b></td>
                <td>{{ $badge->subtitle ?: '-' }}</td>
                <td>{{ $badge->sort_order }}</td>
                <td><span class="badge {{ $badge->is_active ? 'badge-success' : 'badge-secondary' }}">{{ $badge->is_active ? 'Shown' : 'Hidden' }}</span></td>
                <td>
                  <a href="{{ route('admin.trust-badges.edit', $badge) }}" title="Edit"><i class="ik ik-edit-2 f-16 mr-10 text-green"></i></a>
                  <form method="POST" action="{{ route('admin.trust-badges.toggle', $badge) }}" class="d-inline">@csrf
                    <button class="btn btn-link p-0 mr-10" title="{{ $badge->is_active ? 'Hide' : 'Show' }}"><i class="ik {{ $badge->is_active ? 'ik-eye-off text-orange' : 'ik-eye text-green' }} f-16"></i></button>
                  </form>
                  <form method="POST" action="{{ route('admin.trust-badges.destroy', $badge) }}" class="d-inline" onsubmit="return confirm('Delete this badge?')">@csrf @method('DELETE')
                    <button class="btn btn-link p-0" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></button>
                  </form>
                </td>
              </tr>
            @empty
              <tr><td colspan="6" class="text-center text-muted p-4">No trust badges yet. Add things like "ISI Certified", "100% Copper", "2 Year Warranty".</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
