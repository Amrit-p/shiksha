@extends('admin.layouts.main')
@section('title', $salesman->name . ' · Sales & Collection')
@push('head')
  <link rel="stylesheet" href="{{ asset('backend_assets/plugins/chartist/dist/chartist.min.css') }}">
  <style>
    #salesChart .ct-series-a .ct-bar { stroke: #4e73df; stroke-width: 18px; }
    .chart-legend span { display: inline-block; width: 12px; height: 12px; border-radius: 2px; margin: 0 4px -1px 12px; }
  </style>
@endpush
@section('content')
@include('admin.include.message')
@php
  $money = fn ($v) => '₹' . number_format((float) $v, 2);
  $monthParam = fn ($m) => [$salesman->id, 'date' => $m];
@endphp
<div class="container-fluid">
  <div class="mb-3">
    <a href="{{ route('admin.sales.report', ['type' => 'month', 'date' => $period['start']->format('Y-m')]) }}">&larr; All salesmen</a>
  </div>

  <div class="card">
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
      <h3 class="mb-0">{{ $salesman->name }}</h3>
      <div class="d-flex align-items-center mt-2 mt-md-0">
        <a href="{{ route('admin.sales.salesman', $monthParam($period['prev'])) }}" class="btn btn-sm btn-light">&larr;</a>
        <strong class="mx-3">{{ $period['label'] }}</strong>
        <a href="{{ route('admin.sales.salesman', $monthParam($period['next'])) }}" class="btn btn-sm btn-light mr-3">&rarr;</a>
        <a href="{{ route('admin.sales.salesman.export', $monthParam($period['start']->format('Y-m'))) }}" class="btn btn-sm btn-success"><i class="ik ik-download"></i> CSV</a>
      </div>
    </div>
    <div class="card-body">
      <div class="row">
        @foreach([
          ['Sales', $money($totals['sales']), $totals['orders'] . ' order(s)'],
        ] as [$label, $value, $hint])
          <div class="col-6 mb-3">
            <div class="border rounded p-3 h-100">
              <div class="text-muted small text-uppercase">{{ $label }}</div>
              <div class="h4 mb-1">{{ $value }}</div>
              <div class="small text-muted">{{ $hint }}</div>
            </div>
          </div>
        @endforeach
      </div>

      <h5 class="mt-2">Week by week <small class="text-muted chart-legend"><span style="background:#4e73df"></span>Sales</small></h5>
      <div id="salesChart" class="ct-chart" style="height:260px"></div>

      <div class="table-responsive mt-3">
        <table class="table table-sm table-bordered">
          <thead class="thead-light">
            <tr><th>Week (Mon–Sun)</th><th class="text-right">Orders</th><th class="text-right">Sales</th></tr>
          </thead>
          <tbody>
            @foreach($weeks as $week)
              <tr>
                <td>{{ $week['label'] }}</td>
                <td class="text-right">{{ $week['orders'] }}</td>
                <td class="text-right">{{ $money($week['sales']) }}</td>
              </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr class="font-weight-bold">
              <td>Month</td>
              <td class="text-right">{{ $totals['orders'] }}</td>
              <td class="text-right">{{ $money($totals['sales']) }}</td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header"><h3 class="mb-0">Orders this month</h3></div>
        <div class="card-body table-responsive">
          <table class="table table-sm table-hover">
            <thead><tr><th>#</th><th>Shop</th><th class="text-right">Total</th></tr></thead>
            <tbody>
              @forelse($orders as $order)
                <tr>
                  <td><a href="{{ route('admin.order.detail', $order->id) }}">#{{ $order->id }}</a></td>
                  <td>{{ $order->shop_name ?: '—' }}<div class="small text-muted">{{ ucfirst((string) $order->payment_type) }}</div></td>
                  <td class="text-right">{{ $money($order->total_amount) }}</td>
                </tr>
              @empty
                <tr><td colspan="3" class="text-center text-muted py-3">No orders this month.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
@push('script')
  <script src="{{ asset('backend_assets/plugins/chartist/dist/chartist.min.js') }}"></script>
  <script>
    new Chartist.Bar('#salesChart', {
      labels: @json(array_column($weeks, 'label')),
      series: [@json(array_column($weeks, 'sales'))]
    }, {
      seriesBarDistance: 20,
      axisY: { onlyInteger: true, labelInterpolationFnc: function (v) { return '₹' + Number(v).toLocaleString('en-IN'); } }
    });
  </script>
@endpush
