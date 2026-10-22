@extends('front/layout')
@section('title', 'My Performance')
@section('container')
@php
    $money = fn ($v) => '₹' . number_format((float) $v, 2);
    $salesTarget = (float) ($target->sales_target ?? 0);
    $collectionTarget = (float) ($target->collection_target ?? 0);
    $statusClass = ['verified' => 'success', 'rejected' => 'danger', 'reversed' => 'default', 'pending' => 'warning'];
    $progress = function (float $actual, float $target) {
        return $target > 0 ? min(100, round($actual / $target * 100)) : null;
    };
@endphp
<section id="cart-view">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="cart-view-area">
                    <div class="clearfix" style="margin-bottom:15px">
                        <h3 class="pull-left" style="margin-top:5px">My Performance</h3>
                        <form method="GET" class="form-inline pull-right">
                            <a href="{{ route('front.performance', ['date' => $period['prev']]) }}" class="btn btn-default btn-sm">&larr;</a>
                            <strong style="margin:0 10px">{{ $period['label'] }}</strong>
                            <a href="{{ route('front.performance', ['date' => $period['next']]) }}" class="btn btn-default btn-sm">&rarr;</a>
                            <input type="month" name="date" class="form-control input-sm" style="margin-left:10px" value="{{ $period['start']->format('Y-m') }}" onchange="this.form.submit()">
                        </form>
                    </div>

                    <div class="row">
                        @foreach([
                            ['Sales', $totals['sales'], $salesTarget, $totals['orders'] . ' order(s)'],
                            ['Collected (verified)', $totals['collected'], $collectionTarget, null],
                        ] as [$label, $actual, $goal, $hint])
                            @php($pct = $progress($actual, $goal))
                            <div class="col-sm-6 col-md-3">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <div class="text-muted small">{{ $label }}</div>
                                        <div style="font-size:22px;font-weight:bold">{{ $money($actual) }}</div>
                                        @if($pct !== null)
                                            <div class="progress" style="margin:6px 0 2px;height:8px">
                                                <div class="progress-bar {{ $pct >= 100 ? 'progress-bar-success' : '' }}" style="width:{{ $pct }}%"></div>
                                            </div>
                                            <small class="text-muted">{{ round($actual / $goal * 100, 1) }}% of {{ $money($goal) }} target</small>
                                        @else
                                            <small class="text-muted">{{ $hint ?? 'No target set' }}</small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        <div class="col-sm-6 col-md-3">
                            <div class="panel panel-default"><div class="panel-body">
                                <div class="text-muted small">Awaiting verification</div>
                                <div style="font-size:22px;font-weight:bold">{{ $money($totals['pending']) }}</div>
                                <small class="text-muted">Logged this month, not yet verified</small>
                            </div></div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="panel panel-default"><div class="panel-body">
                                <div class="text-muted small">Still to collect (today)</div>
                                <div style="font-size:22px;font-weight:bold">{{ $money($dues['outstanding']) }}</div>
                                <small class="{{ $dues['overdue'] > 0 ? 'text-danger' : 'text-muted' }}">{{ $money($dues['overdue']) }} overdue</small>
                            </div></div>
                        </div>
                    </div>

                    <h4>Week by week</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead><tr><th>Week (Mon–Sun)</th><th class="text-right">Orders</th><th class="text-right">Sales</th><th class="text-right">Collected</th><th class="text-right">Awaiting verification</th></tr></thead>
                            <tbody>
                                @foreach($weeks as $week)
                                    <tr>
                                        <td>{{ $week['label'] }}</td>
                                        <td class="text-right">{{ $week['orders'] }}</td>
                                        <td class="text-right">{{ $money($week['sales']) }}</td>
                                        <td class="text-right">{{ $money($week['collected']) }}</td>
                                        <td class="text-right">{{ $money($week['pending']) }}</td>
                                    </tr>
                                @endforeach
                                <tr style="font-weight:bold">
                                    <td>Month</td>
                                    <td class="text-right">{{ $totals['orders'] }}</td>
                                    <td class="text-right">{{ $money($totals['sales']) }}</td>
                                    <td class="text-right">{{ $money($totals['collected']) }}</td>
                                    <td class="text-right">{{ $money($totals['pending']) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <h4>
                        My payments
                        <small>
                            @foreach(['' => 'All', 'pending' => 'Awaiting', 'verified' => 'Verified', 'rejected' => 'Rejected', 'reversed' => 'Reversed'] as $value => $label)
                                <a href="{{ route('front.performance', array_filter(['date' => $period['start']->format('Y-m'), 'status' => $value])) }}"
                                   style="margin-left:8px;{{ (string) $status === $value ? 'font-weight:bold;text-decoration:underline' : '' }}">{{ $label }}</a>
                            @endforeach
                        </small>
                    </h4>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead><tr><th>Received on</th><th>Order</th><th class="text-right">Amount</th><th>Mode</th><th>Status</th></tr></thead>
                            <tbody>
                                @forelse($payments as $payment)
                                    <tr>
                                        <td>{{ $payment->paid_on->format('d M Y') }}</td>
                                        <td><a href="{{ route('front.order_detail', $payment->order_id) }}#payments">#{{ $payment->order_id }}</a> {{ $payment->order?->shop_name }}</td>
                                        <td class="text-right">{{ $money($payment->amount) }}</td>
                                        <td>{{ $payment->modeLabel() }}</td>
                                        <td>
                                            <span class="label label-{{ $statusClass[$payment->status] ?? 'default' }}">{{ $payment->isPending() ? 'Awaiting verification' : ucfirst($payment->status) }}</span>
                                            @if($payment->review_reason)<br><small class="text-danger">{{ $payment->review_reason }}</small>@endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center text-muted">No payments for this month.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
