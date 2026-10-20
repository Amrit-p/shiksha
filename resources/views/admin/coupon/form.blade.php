@extends('admin.layouts.main')
@section('title', $coupon->exists ? 'Edit Coupon' : 'New Coupon')
@section('content')
<div class="container-fluid">
  <div class="card">
    <div class="card-header"><h3>{{ $coupon->exists ? 'Edit Coupon '.$coupon->code : 'New Coupon' }}</h3></div>
    <div class="card-body">
      @if ($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
      @endif
      <form method="POST" action="{{ $coupon->exists ? route('admin.coupons.update', $coupon) : route('admin.coupons.store') }}">
        @csrf
        @if ($coupon->exists) @method('PUT') @endif
        <div class="row">
          <div class="col-md-4 form-group">
            <label>Code *</label>
            <input name="code" class="form-control" style="text-transform:uppercase" maxlength="40" required value="{{ old('code', $coupon->code) }}" placeholder="DIWALI10">
          </div>
          <div class="col-md-8 form-group">
            <label>Description</label>
            <input name="description" class="form-control" maxlength="255" value="{{ old('description', $coupon->description) }}" placeholder="Shown to admins only">
          </div>
          <div class="col-md-3 form-group">
            <label>Type *</label>
            <select name="type" id="coupon_type" class="form-control">
              <option value="percent" @selected(old('type', $coupon->type) === 'percent')>Percentage (%)</option>
              <option value="flat" @selected(old('type', $coupon->type) === 'flat')>Flat amount (₹)</option>
            </select>
          </div>
          <div class="col-md-3 form-group">
            <label>Value *</label>
            <input type="number" step="0.01" min="0.01" name="value" class="form-control" required value="{{ old('value', $coupon->value) }}">
          </div>
          <div class="col-md-3 form-group" id="max_discount_group">
            <label>Max discount (₹)</label>
            <input type="number" step="0.01" min="0" name="max_discount" class="form-control" value="{{ old('max_discount', $coupon->max_discount) }}" placeholder="No cap">
          </div>
          <div class="col-md-3 form-group">
            <label>Minimum order (₹)</label>
            <input type="number" step="0.01" min="0" name="min_order_amount" class="form-control" value="{{ old('min_order_amount', $coupon->min_order_amount) }}" placeholder="None">
          </div>
          <div class="col-md-3 form-group">
            <label>Valid from</label>
            <input type="date" name="starts_at" class="form-control" value="{{ old('starts_at', $coupon->starts_at?->toDateString()) }}">
          </div>
          <div class="col-md-3 form-group">
            <label>Valid until (inclusive)</label>
            <input type="date" name="ends_at" class="form-control" value="{{ old('ends_at', $coupon->ends_at?->toDateString()) }}">
          </div>
          <div class="col-md-3 form-group">
            <label>Total uses</label>
            <input type="number" min="1" name="usage_limit_total" class="form-control" value="{{ old('usage_limit_total', $coupon->usage_limit_total) }}" placeholder="Unlimited">
          </div>
          <div class="col-md-3 form-group">
            <label>Uses per shop</label>
            <input type="number" min="1" name="usage_limit_per_shop" class="form-control" value="{{ old('usage_limit_per_shop', $coupon->usage_limit_per_shop) }}" placeholder="Unlimited">
            <small class="form-text text-muted">Shop = owner phone (or GST number). Cancelled orders don't count.</small>
          </div>
          <div class="col-md-12 form-group">
            <label class="mb-0"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $coupon->is_active))> Active</label>
          </div>
        </div>
        <button class="btn btn-primary" type="submit">Save Coupon</button>
        <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary">Cancel</a>
      </form>
    </div>
  </div>
</div>
@push('script')
<script>
  (function () {
    var type = document.getElementById('coupon_type');
    var group = document.getElementById('max_discount_group');
    function sync() { group.style.display = type.value === 'percent' ? '' : 'none'; }
    type.addEventListener('change', sync); sync();
  })();
</script>
@endpush
@endsection
