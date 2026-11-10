@extends('admin.layouts.main')
@section('title','Sales & Collection')
@section('content')
@include('admin.include.message')
@php
  $money = fn ($v) => '₹' . number_format((float) $v, 2);
  $isMonth = $period['type'] === 'month';
  $dateParam = fn ($date) => ['type' => $period['type'], 'date' => $date];
@endphp
<div class="container-fluid">
  <div class="card">
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
      <h3 class="mb-0">Sales &amp; Collection</h3>
      <div class="d-flex flex-wrap align-items-center mt-2 mt-md-0">
        @if($pendingCount)
          <a href="{{ route('admin.payments.index') }}" class="btn btn-sm btn-warning mr-2">{{ $pendingCount }} payment(s) to verify</a>
        @endif
        <a href="{{ route('admin.sales.export', $dateParam($isMonth ? $period['start']->format('Y-m') : $period['start']->toDateString())) }}" class="btn btn-sm btn-success"><i class="ik ik-download"></i> CSV</a>
      </div>
    </div>
    <div class="card-body">
      <form method="GET" class="form-inline mb-3">
        <div class="btn-group mr-3 mb-2" role="group">
          <a href="{{ route('admin.sales.report', ['type' => 'month']) }}" class="btn btn-sm {{ $isMonth ? 'btn-primary' : 'btn-outline-primary' }}">Monthly</a>
          <a href="{{ route('admin.sales.report', ['type' => 'week']) }}" class="btn btn-sm {{ $isMonth ? 'btn-outline-primary' : 'btn-primary' }}">Weekly</a>
        </div>
        <a href="{{ route('admin.sales.report', $dateParam($period['prev'])) }}" class="btn btn-sm btn-light mb-2">&larr;</a>
        <strong class="mx-3 mb-2">{{ $period['label'] }}</strong>
        <a href="{{ route('admin.sales.report', $dateParam($period['next'])) }}" class="btn btn-sm btn-light mb-2">&rarr;</a>
        <input type="hidden" name="type" value="{{ $period['type'] }}">
        <input type="{{ $isMonth ? 'month' : 'date' }}" name="date" class="form-control form-control-sm ml-3 mb-2"
               value="{{ $isMonth ? $period['start']->format('Y-m') : $period['start']->toDateString() }}" onchange="this.form.submit()">
      </form>

      <p class="text-muted small">
        Sales = orders placed in the period (cancelled excluded).
      </p>

      <div class="table-responsive">
        <table class="table table-hover table-bordered">
          <thead class="thead-light">
            <tr>
              <th>Salesman</th>
              <th class="text-right">Orders</th>
              <th class="text-right">Sales</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @forelse($rows as $row)
              <tr>
                <td>
                  <a href="{{ route('admin.sales.salesman', [$row['user']->id, 'date' => $period['start']->format('Y-m')]) }}"><b>{{ $row['user']->name }}</b></a>
                  <div class="small text-muted">{{ $row['user']->mobile ?: $row['user']->email }}</div>
                </td>
                <td class="text-right">{{ $row['orders'] }}</td>
                <td class="text-right">{{ $money($row['sales']) }}</td>
                <td class="text-right">
                  <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.sales.salesman', [$row['user']->id, 'date' => $period['start']->format('Y-m')]) }}">Details</a>
                </td>
              </tr>
            @empty
              <tr><td colspan="4" class="text-center text-muted py-4">No salesmen found.</td></tr>
            @endforelse
          </tbody>
          @if($rows->isNotEmpty())
            <tfoot>
              <tr class="font-weight-bold">
                <td>Total</td>
                <td class="text-right">{{ $totals['orders'] }}</td>
                <td class="text-right">{{ $money($totals['sales']) }}</td>
                <td></td>
              </tr>
            </tfoot>
          @endif
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
