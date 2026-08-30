<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $order->id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; margin: 20px; }
        .invoice-title { font-size: 18px; font-weight: bold; margin-bottom: 4px; }
        .invoice-id { font-size: 14px; color: #666; margin-bottom: 16px; }
        hr { border: none; border-top: 1px solid #ddd; margin: 16px 0; }
        .row { overflow: hidden; margin-bottom: 16px; }
        .col-half { width: 48%; float: left; }
        .col-half.right { float: right; text-align: right; }
        .strong { font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #ddd; padding: 8px 10px; text-align: left; }
        th { background: #f5f5f5; font-weight: bold; }
        tfoot th { text-align: right; }
        .no-print { margin-top: 20px; text-align: right; }
        .btn { display: inline-block; padding: 8px 16px; background: #28a745; color: #fff; text-decoration: none; border-radius: 4px; font-size: 13px; }
        @media print { .no-print { display: none !important; } }
    </style>
</head>
<body>

    <div class="invoice-title">Order Invoice</div>
    <div class="invoice-id">Invoice #{{ $order->id }}</div>
    <hr>

    <div class="row">
        <div class="col-half">
            <span class="strong">Bill To</span><br>
            {{ $order->owner_name }}<br>
            @if($order->owner_phone) {{ $order->owner_phone }}<br> @endif
            @if($order->owner_email) {{ $order->owner_email }}<br> @endif
            @if($order->owner_address) {{ $order->owner_address }} @endif
        </div>
        <div class="col-half right">
            <span class="strong">Order Date:</span> {{ $order->created_at->format('d M Y') }}<br>
            <span class="strong">Payment:</span> {{ $order->payment_type ?? '-' }}<br>
            <span class="strong">Status:</span> {{ $order->order_status ?? '-' }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Variant / Attribute</th>
                <th>Price</th>
                <th>Qty</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @php $grandTotal = 0; @endphp
            @foreach($items as $item)
                @php
                    $hasAttributes = !empty($item['attributes']);
                    $customFields = (!empty($item['custom_fields']) && is_array($item['custom_fields']))
                        ? $item['custom_fields']
                        : [];
                @endphp

                @if(!$hasAttributes)
                    @php
                        $qty = (int) ($item['qty'] ?? 1);
                        $price = (float) ($item['unit_price'] ?? 0);
                        $total = (float) ($item['total'] ?? ($price * $qty));
                        $grandTotal += $total;
                    @endphp
                    <tr>
                        <td>{{ $item['product']['name'] ?? '-' }}</td>
                        <td>—</td>
                        <td>₹{{ number_format($price, 2) }}</td>
                        <td>{{ $qty }}</td>
                        <td>₹{{ number_format($total, 2) }}</td>
                    </tr>
                @else
                    @foreach($item['attributes'] as $attr)
                        @php
                            $qty = (int) ($attr['qty'] ?? 1);
                            $price = (float) ($attr['unit_price'] ?? 0);
                            $total = (float) ($attr['total'] ?? ($price * $qty));
                            $grandTotal += $total;
                        @endphp
                        <tr>
                            <td>{{ $item['product']['name'] ?? '-' }}</td>
                            <td>
                                @if(!empty($item['variant']['product_code_type_name']))
                                    <strong>Type:</strong> {{ $item['variant']['product_code_type_name'] }}<br>
                                @endif
                                @if(!empty($item['variant']['name']))
                                    <strong>Variant:</strong> {{ $item['variant']['name'] }}<br>
                                @endif
                                @if(!empty($customFields))
                                    @foreach($customFields as $cf)
                                        @php
                                            $cfName = $cf['type_name'] ?? 'Field';
                                            if (isset($cf['step_index'])) $cfName = 'Step ' . ((int)$cf['step_index'] + 1);
                                            $cfValue = $cf['value'] ?? null;
                                        @endphp
                                        @if(!empty($cfValue))
                                            <strong>{{ $cfName }}:</strong> {{ $cfValue }}<br>
                                        @endif
                                    @endforeach
                                @endif
                                <strong>{{ $attr['attribute_name'] ?? '' }}</strong>
                                @if(!empty($attr['option_name'])) — {{ $attr['option_name'] }} @endif
                                @if(!empty($attr['is_por']) && (string)$attr['is_por'] === '1') <br>POR: Yes @endif
                            </td>
                            <td>₹{{ number_format($price, 2) }}</td>
                            <td>{{ $qty }}</td>
                            <td>₹{{ number_format($total, 2) }}</td>
                        </tr>
                    @endforeach
                @endif
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" style="text-align: right;">Grand Total</th>
                <th>₹{{ number_format($grandTotal, 2) }}</th>
            </tr>
        </tfoot>
    </table>

    @if(empty($forPdf))
    <div class="no-print">
        <a href="{{ route('admin.order.invoice.pdf', $order->id) }}" class="btn">Download PDF</a>
    </div>
    @if(request()->get('print'))
    <script>window.onload = function() { window.print(); }</script>
    @endif
    @endif

</body>
</html>
