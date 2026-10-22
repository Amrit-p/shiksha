@extends('admin.layouts.main')
@section('title','Sales & Collection')
@section('content')
@include('admin.include.message')
@php
  $money = fn ($v) => '₹' . number_format((float) $v, 2);
  $pct = fn ($v) => $v === null ? '—' : $v . '%';
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
        <a href="{{ route('admin.sales.targets', ['month' => $period['start']->format('Y-m')]) }}" class="btn btn-sm btn-outline-secondary mr-2"><i class="ik ik-target"></i> Targets</a>
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
        Sales = orders placed in the period (cancelled excluded). Collected = verified payments by the date received.
        Outstanding and overdue are as of today. {{ $isMonth ? '' : 'Targets are monthly, so they show in the Monthly view.' }}
      </p>

      <div class="table-responsive">
        <table class="table table-hover table-bordered">
          <thead class="thead-light">
            <tr>
              <th>Salesman</th>
              <th class="text-right">Orders</th>
              <th class="text-right">Sales</th>
              @if($isMonth)<th class="text-right">Sales target</th>@endif
              <th class="text-right">Collected</th>
              @if($isMonth)<th class="text-right">Collection target</th>@endif
              <th class="text-right">Pending verification</th>
              <th class="text-right">Outstanding</th>
              <th class="text-right">Overdue</th>
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
                @if($isMonth)
                  <td class="text-right">
                    {{ $row['sales_target'] ? $money($row['sales_target']) : '—' }}
                    @if($row['sales_pct'] !== null)
                      <div class="small {{ $row['sales_pct'] >= 100 ? 'text-success' : 'text-muted' }}">{{ $pct($row['sales_pct']) }}</div>
                    @endif
                  </td>
                @endif
                <td class="text-right text-success">{{ $money($row['collected']) }}</td>
                @if($isMonth)
                  <td class="text-right">
                    {{ $row['collection_target'] ? $money($row['collection_target']) : '—' }}
                    @if($row['collection_pct'] !== null)
                      <div class="small {{ $row['collection_pct'] >= 100 ? 'text-success' : 'text-muted' }}">{{ $pct($row['collection_pct']) }}</div>
                    @endif
                  </td>
                @endif
                <td class="text-right {{ $row['pending'] > 0 ? 'text-warning' : '' }}">{{ $money($row['pending']) }}</td>
                <td class="text-right">{{ $money($row['outstanding']) }}</td>
                <td class="text-right {{ $row['overdue'] > 0 ? 'text-danger font-weight-bold' : '' }}">{{ $money($row['overdue']) }}</td>
                <td class="text-right">
                  <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.sales.salesman', [$row['user']->id, 'date' => $period['start']->format('Y-m')]) }}">Details</a>
                </td>
              </tr>
            @empty
              <tr><td colspan="{{ $isMonth ? 10 : 8 }}" class="text-center text-muted py-4">No salesmen found.</td></tr>
            @endforelse
          </tbody>
          @if($rows->isNotEmpty())
            <tfoot>
              <tr class="font-weight-bold">
                <td>Total</td>
                <td class="text-right">{{ $totals['orders'] }}</td>
                <td class="text-right">{{ $money($totals['sales']) }}</td>
                @if($isMonth)<td class="text-right">{{ $totals['sales_target'] ? $money($totals['sales_target']) : '—' }}</td>@endif
                <td class="text-right">{{ $money($totals['collected']) }}</td>
                @if($isMonth)<td class="text-right">{{ $totals['collection_target'] ? $money($totals['collection_target']) : '—' }}</td>@endif
                <td class="text-right">{{ $money($totals['pending']) }}</td>
                <td class="text-right">{{ $money($totals['outstanding']) }}</td>
                <td class="text-right">{{ $money($totals['overdue']) }}</td>
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
