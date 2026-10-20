@extends('admin.layouts.main')
@section('title','Salesman Targets')
@section('content')
@include('admin.include.message')
<div class="container-fluid">
  <div class="mb-3">
    <a href="{{ route('admin.sales.report', ['type' => 'month', 'date' => $period['start']->format('Y-m')]) }}">&larr; Sales &amp; Collection</a>
  </div>
  <div class="card">
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
      <h3 class="mb-0">Monthly Targets</h3>
      <form method="GET" class="form-inline">
        <label class="mr-2 small text-muted">Override month</label>
        <input type="month" name="month" class="form-control form-control-sm" value="{{ $period['start']->format('Y-m') }}" onchange="this.form.submit()">
      </form>
    </div>
    <div class="card-body">
      @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
      @endif
      <p class="text-muted small">
        The <b>standard</b> target applies every month. Fill in an <b>override for {{ $period['label'] }}</b> to replace it for that month only (e.g. festival season).
        Leave both override fields empty to use the standard target. Amounts are in ₹.
      </p>
      <form method="POST" action="{{ route('admin.sales.targets.update') }}">
        @csrf
        <input type="hidden" name="month" value="{{ $period['start']->format('Y-m') }}">
        <div class="table-responsive">
          <table class="table table-bordered table-sm">
            <thead class="thead-light">
              <tr>
                <th rowspan="2" class="align-middle">Salesman</th>
                <th colspan="2" class="text-center">Standard (every month)</th>
                <th colspan="2" class="text-center">Override for {{ $period['label'] }}</th>
              </tr>
              <tr>
                <th>Sales</th><th>Collection</th><th>Sales</th><th>Collection</th>
              </tr>
            </thead>
            <tbody>
              @forelse($salesmen as $salesman)
                @php($d = $defaults->get($salesman->id))
                @php($o = $overrides->get($salesman->id))
                <tr>
                  <td>{{ $salesman->name }}</td>
                  <td><input type="number" step="0.01" min="0" class="form-control form-control-sm" name="default[{{ $salesman->id }}][sales]" value="{{ $d?->sales_target }}"></td>
                  <td><input type="number" step="0.01" min="0" class="form-control form-control-sm" name="default[{{ $salesman->id }}][collection]" value="{{ $d?->collection_target }}"></td>
                  <td><input type="number" step="0.01" min="0" class="form-control form-control-sm" name="override[{{ $salesman->id }}][sales]" value="{{ $o?->sales_target }}" placeholder="{{ $d?->sales_target }}"></td>
                  <td><input type="number" step="0.01" min="0" class="form-control form-control-sm" name="override[{{ $salesman->id }}][collection]" value="{{ $o?->collection_target }}" placeholder="{{ $d?->collection_target }}"></td>
                </tr>
              @empty
                <tr><td colspan="5" class="text-center text-muted py-4">No salesmen found.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
        @if($salesmen->isNotEmpty())
          <button type="submit" class="btn btn-primary">Save Targets</button>
        @endif
      </form>
    </div>
  </div>
</div>
@endsection
