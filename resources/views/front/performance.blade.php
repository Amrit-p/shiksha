@extends('front/layout')
@section('title', 'My Performance')
@section('container')
@php
    $money = fn ($v) => '₹' . number_format((float) $v, 2);
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
                        <div class="col-sm-6 col-md-3">
                            <div class="panel panel-default"><div class="panel-body">
                                <div class="text-muted small">Sales</div>
                                <div style="font-size:22px;font-weight:bold">{{ $money($totals['sales']) }}</div>
                                <small class="text-muted">{{ $totals['orders'] }} order(s)</small>
                            </div></div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="panel panel-default"><div class="panel-body">
                                <div class="text-muted small">Products sold</div>
                                <div style="font-size:22px;font-weight:bold">{{ number_format($totals['pieces']) }}</div>
                                <small class="text-muted">Total pieces</small>
                            </div></div>
                        </div>
                    </div>

                    <h4>Week by week</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead><tr><th>Week (Mon–Sun)</th><th class="text-right">Orders</th><th class="text-right">Sales</th></tr></thead>
                            <tbody>
                                @foreach($weeks as $week)
                                    <tr>
                                        <td>{{ $week['label'] }}</td>
                                        <td class="text-right">{{ $week['orders'] }}</td>
                                        <td class="text-right">{{ $money($week['sales']) }}</td>
                                    </tr>
                                @endforeach
                                <tr style="font-weight:bold">
                                    <td>Month</td>
                                    <td class="text-right">{{ $totals['orders'] }}</td>
                                    <td class="text-right">{{ $money($totals['sales']) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
