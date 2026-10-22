@extends('admin.layouts.main')
@section('title','Coupons')
@section('content')
<div class="container-fluid">
  <div class="row">@include('admin.include.message')</div>
  <div class="card">
    <div class="card-header d-flex justify-content-between">
      <h3>Coupons</h3>
      <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary btn-sm"><i class="ik ik-plus"></i> New Coupon</a>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-bordered mb-0">
          <thead>
            <tr>
              <th>Code</th><th>Discount</th><th>Min order</th><th>Valid</th><th>Used</th><th>Per shop</th><th>Status</th><th style="width:150px;">Action</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($coupons as $coupon)
              <tr>
                <td><b>{{ $coupon->code }}</b>@if ($coupon->description)<div><small class="text-muted">{{ $coupon->description }}</small></div>@endif</td>
                <td>{{ $coupon->summary() }}</td>
                <td>{{ $coupon->min_order_amount ? '₹'.number_format($coupon->min_order_amount, 2) : '-' }}</td>
                <td>
                  {{ $coupon->starts_at?->format('d M Y') ?? 'Any time' }} &ndash; {{ $coupon->ends_at?->format('d M Y') ?? 'No end' }}
                  @if ($coupon->ends_at && $coupon->ends_at->lt(today()))<span class="badge badge-secondary">Expired</span>@endif
                </td>
                <td>{{ $coupon->used_count }}{{ $coupon->usage_limit_total ? ' / '.$coupon->usage_limit_total : '' }}</td>
                <td>{{ $coupon->usage_limit_per_shop ?: 'Unlimited' }}</td>
                <td>
                  <span class="badge {{ $coupon->is_active ? 'badge-success' : 'badge-danger' }}">{{ $coupon->is_active ? 'Active' : 'Inactive' }}</span>
                </td>
                <td>
                  <a href="{{ route('admin.coupons.edit', $coupon) }}" title="Edit"><i class="ik ik-edit-2 f-16 mr-10 text-green"></i></a>
                  <form method="POST" action="{{ route('admin.coupons.toggle', $coupon) }}" class="d-inline">@csrf
                    <button class="btn btn-link p-0 mr-10" title="{{ $coupon->is_active ? 'Deactivate' : 'Activate' }}"><i class="ik {{ $coupon->is_active ? 'ik-slash text-orange' : 'ik-check-circle text-green' }} f-16"></i></button>
                  </form>
                  <form method="POST" action="{{ route('admin.coupons.destroy', $coupon) }}" class="d-inline" onsubmit="return confirm('Delete coupon {{ $coupon->code }}? Past orders keep their discount.')">@csrf @method('DELETE')
                    <button class="btn btn-link p-0" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></button>
                  </form>
                </td>
              </tr>
            @empty
              <tr><td colspan="8" class="text-center text-muted p-4">No coupons yet.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
    <div class="card-footer">{{ $coupons->links() }}</div>
  </div>
</div>
@endsection
