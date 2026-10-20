@extends('admin.layouts.main')
@section('title', 'Banners & Schemes')
@section('content')
<div class="container-fluid">
  <div class="row">@include('admin.include.message')</div>
  <div class="card">
    <div class="card-header d-flex justify-content-between">
      <div>
        <h3 class="mb-0">Banners &amp; Schemes</h3>
        <small class="text-muted">Active banners within their dates show in the salesman home slider. Tick "Schemes popup" to also show one in the daily popup after login.</small>
      </div>
      <a href="{{ route('admin.banner.create') }}" class="btn btn-primary btn-sm align-self-start"><i class="ik ik-plus"></i> Add Banner</a>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-bordered mb-0">
          <thead>
            <tr><th style="width:110px;">Image</th><th>Title / Button</th><th>Dates</th><th>Order</th><th>Popup</th><th>Status</th><th style="width:120px;">Action</th></tr>
          </thead>
          <tbody>
            @forelse ($banners as $banner)
              <tr>
                <td><img src="{{ asset('storage/'.$banner->image) }}" alt="" style="width:100px;height:50px;object-fit:cover;"></td>
                <td>
                  <b>{{ $banner->title ?: '-' }}</b>
                  @if ($banner->btn_text)<div><small>{{ $banner->btn_text }} &rarr; {{ $banner->btn_link ?: 'no link' }}</small></div>@endif
                </td>
                <td>
                  {{ $banner->starts_at?->format('d M Y') ?? 'Any time' }} &ndash; {{ $banner->ends_at?->format('d M Y') ?? 'No end' }}
                  @if ($banner->ends_at && $banner->ends_at->lt(today()))<span class="badge badge-secondary">Expired</span>
                  @elseif ($banner->starts_at && $banner->starts_at->gt(today()))<span class="badge badge-info">Scheduled</span>@endif
                </td>
                <td>{{ $banner->sort_order }}</td>
                <td>{!! $banner->show_in_popup ? '<span class="badge badge-primary">Yes</span>' : '-' !!}</td>
                <td>
                  <span class="badge {{ $banner->isCurrent() ? 'badge-success' : 'badge-secondary' }}">{{ (int) $banner->status === 1 ? ($banner->isCurrent() ? 'Live' : 'Active') : 'Inactive' }}</span>
                </td>
                <td>
                  <a href="{{ route('admin.banner.edit', $banner) }}" title="Edit"><i class="ik ik-edit-2 f-16 mr-10 text-green"></i></a>
                  <form method="POST" action="{{ route('admin.banner.toggle', $banner) }}" class="d-inline">@csrf
                    <button class="btn btn-link p-0 mr-10" title="{{ (int) $banner->status === 1 ? 'Deactivate' : 'Activate' }}"><i class="ik {{ (int) $banner->status === 1 ? 'ik-slash text-orange' : 'ik-check-circle text-green' }} f-16"></i></button>
                  </form>
                  <form method="POST" action="{{ route('admin.banner.delete', $banner) }}" class="d-inline" onsubmit="return confirm('Delete this banner?')">@csrf @method('DELETE')
                    <button class="btn btn-link p-0" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></button>
                  </form>
                </td>
              </tr>
            @empty
              <tr><td colspan="7" class="text-center text-muted p-4">No banners yet.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
    <div class="card-footer">{{ $banners->links() }}</div>
  </div>
</div>
@endsection
