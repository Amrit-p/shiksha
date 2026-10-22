{{--
  Delivered / pending breakdown + delivery history for an order.
  Expects: $order, $deliveryLines (OrderDeliveryService::lines), $editable (bool, admin form).
--}}
@php
    $editable = ($editable ?? false) && strtolower((string) $order->order_status) !== 'cancelled';
    $totalPending = array_sum(array_column($deliveryLines, 'pending'));
    $lineNames = collect($deliveryLines)->keyBy('key');
@endphp

@if ($editable && $totalPending > 0)
<form method="POST" action="{{ route('admin.order.delivery.store', $order->id) }}" id="record-delivery">
    @csrf
@endif

<table class="table table-bordered mb-0">
    <thead>
        <tr>
            <th>Item</th>
            <th style="width:90px;">Ordered</th>
            <th style="width:90px;">Delivered</th>
            <th style="width:90px;">Pending</th>
            @if ($editable && $totalPending > 0)
                <th style="width:150px;">Deliver now</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @foreach ($deliveryLines as $line)
            <tr>
                <td>
                    <b>{{ $line['name'] }}</b>
                    @if ($line['detail'])<div><small>{{ $line['detail'] }}</small></div>@endif
                </td>
                <td>{{ $line['ordered'] }}</td>
                <td>{{ $line['delivered'] }}</td>
                <td style="font-weight:700;color:{{ $line['pending'] > 0 ? '#d9534f' : '#2e7d32' }};">{{ $line['pending'] }}</td>
                @if ($editable && $totalPending > 0)
                    <td>
                        @if ($line['pending'] > 0)
                            <input type="number" class="form-control form-control-sm" min="0" max="{{ $line['pending'] }}"
                                name="qty[{{ $line['key'] }}]" value="{{ old('qty.'.$line['key'], $line['pending']) }}">
                        @else
                            <span class="text-muted">Done</span>
                        @endif
                    </td>
                @endif
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th class="text-right">Total</th>
            <th>{{ array_sum(array_column($deliveryLines, 'ordered')) }}</th>
            <th>{{ array_sum(array_column($deliveryLines, 'delivered')) }}</th>
            <th>{{ $totalPending }}</th>
            @if ($editable && $totalPending > 0)<th></th>@endif
        </tr>
    </tfoot>
</table>

@if ($editable && $totalPending > 0)
    <div class="p-3">
        @error('qty')<div class="alert alert-danger">{{ $message }}</div>@enderror
        <div class="row">
            <div class="col-md-3 form-group">
                <label>Delivered on</label>
                <input type="date" name="delivered_on" class="form-control" max="{{ now()->toDateString() }}"
                    value="{{ old('delivered_on', now()->toDateString()) }}" required>
                @error('delivered_on')<small class="text-danger">{{ $message }}</small>@enderror
            </div>
            <div class="col-md-6 form-group">
                <label>Note (optional)</label>
                <input type="text" name="note" class="form-control" maxlength="1000" value="{{ old('note') }}"
                    placeholder="e.g. LR no., vehicle, remaining items next week">
            </div>
            <div class="col-md-3 form-group d-flex align-items-end">
                <button type="submit" class="btn btn-primary btn-block" onclick="return confirm('Record this delivery?')">
                    <i class="ik ik-truck"></i> Record Delivery
                </button>
            </div>
        </div>
        <small class="text-muted">Quantities default to everything pending. Lower them for a partial delivery; the order becomes
            "Partially Delivered" until nothing is pending, then "Delivered".</small>
    </div>
</form>
@endif

@if ($order->deliveries->isNotEmpty())
    <div class="p-3">
        <h6 style="margin-top:10px;">Delivery history</h6>
        <table class="table table-sm table-bordered mb-0">
            <thead>
                <tr><th style="width:110px;">Date</th><th>Items delivered</th><th>Note</th>@if ($editable)<th style="width:140px;">By</th>@endif</tr>
            </thead>
            <tbody>
                @foreach ($order->deliveries as $delivery)
                    <tr>
                        <td>{{ $delivery->delivered_on->format('d-m-Y') }}</td>
                        <td>
                            @foreach ($delivery->items as $item)
                                @php($l = $lineNames->get($item->line_key))
                                <div>{{ $l['name'] ?? 'Item' }}@if (!empty($l['detail'])) <small>({{ $l['detail'] }})</small>@endif &times; {{ $item->qty }}</div>
                            @endforeach
                        </td>
                        <td>{{ $delivery->note ?: '-' }}</td>
                        @if ($editable)<td>{{ $delivery->creator->name ?? '-' }}</td>@endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
