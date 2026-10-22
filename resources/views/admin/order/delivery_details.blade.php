{{-- Loaded into the "Delivery Details" popup on the order list. --}}
@php
    $ordered = array_sum(array_column($deliveryLines, 'ordered'));
    $delivered = array_sum(array_column($deliveryLines, 'delivered'));
@endphp
<div class="d-flex justify-content-between align-items-center px-3 pt-3 pb-2">
    <div>
        <b>Order #{{ $order->id }}</b> &middot; {{ $order->shop_name ?: $order->owner_name }}
        <span class="badge {{ $order->order_status === 'partially_delivered' ? 'badge-warning' : ($order->order_status === 'delivered' ? 'badge-success' : 'badge-secondary') }} ml-1">{{ orderStatusLabel($order->order_status) }}</span>
    </div>
    <div class="text-right">
        <div><b>{{ $delivered }}</b> / {{ $ordered }} delivered</div>
        <small class="{{ $ordered - $delivered > 0 ? 'text-danger' : 'text-success' }}">{{ $ordered - $delivered }} pending</small>
    </div>
</div>
@include('partials.order_delivery', ['editable' => false])
<div class="text-right p-3">
    <a href="{{ route('admin.order_detail', $order->id) }}" class="btn btn-sm btn-outline-primary">Open full order</a>
</div>
