{{-- Salesman order detail: payments collected on this order, and the form to log a new one. --}}
@php
    $verifiedPaid = (float) $order->payments->where('status', 'verified')->sum('amount');
    $pendingPaid = (float) $order->payments->where('status', 'pending')->sum('amount');
    $remaining = max(0, round((float) $order->total_amount - $verifiedPaid - $pendingPaid, 2));
    $statusClass = ['verified' => 'success', 'rejected' => 'danger', 'reversed' => 'default', 'pending' => 'warning'];
@endphp
<div class="cart-view-area" id="payments">
    <h3>Payments Collected</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <p>
        Order total <b>₹{{ number_format((float) $order->total_amount, 2) }}</b>
        · Verified <b class="text-success">₹{{ number_format($verifiedPaid, 2) }}</b>
        · Awaiting verification <b class="text-warning">₹{{ number_format($pendingPaid, 2) }}</b>
        · Left to collect <b>₹{{ number_format($remaining, 2) }}</b>
    </p>

    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr><th>Received on</th><th>Amount</th><th>Mode / Ref.</th><th>Proof</th><th>Status</th><th></th></tr>
            </thead>
            <tbody>
                @forelse($order->payments as $payment)
                    <tr>
                        <td>{{ $payment->paid_on->format('d M Y') }}</td>
                        <td>₹{{ number_format((float) $payment->amount, 2) }}</td>
                        <td>
                            {{ $payment->modeLabel() }}
                            @if($payment->reference)<br><small class="text-muted">{{ $payment->reference }}</small>@endif
                            @if($payment->note)<br><small class="text-muted">{{ $payment->note }}</small>@endif
                        </td>
                        <td>
                            @if($payment->proof_path)
                                <a href="{{ route('front.payments.proof', $payment) }}" target="_blank">View</a>
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            <span class="label label-{{ $statusClass[$payment->status] ?? 'default' }}">{{ $payment->isPending() ? 'Awaiting verification' : ucfirst($payment->status) }}</span>
                            @if($payment->review_reason)<br><small class="text-danger">{{ $payment->review_reason }}</small>@endif
                        </td>
                        <td class="text-nowrap">
                            @if($payment->isPending())
                                <a href="#editPayment{{ $payment->id }}" data-toggle="collapse" class="btn btn-xs btn-default">Edit</a>
                                <form method="POST" action="{{ route('front.payments.destroy', $payment) }}" style="display:inline" onsubmit="return confirm('Delete this payment?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-danger">Delete</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @if($payment->isPending())
                        <tr class="collapse" id="editPayment{{ $payment->id }}">
                            <td colspan="6">
                                @include('front.partials.payment_form', [
                                    'action' => route('front.payments.update', $payment),
                                    'method' => 'PUT',
                                    'payment' => $payment,
                                    'max' => $remaining + (float) $payment->amount,
                                    'submit' => 'Update Payment',
                                ])
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr><td colspan="6" class="text-center text-muted">No payments logged yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($order->isCancelled())
        <p class="text-muted">This order is cancelled, so it cannot take payments.</p>
    @elseif($remaining > 0)
        <h4>Log a payment</h4>
        <p class="text-muted small">It counts toward your collection once the admin verifies it.</p>
        @include('front.partials.payment_form', [
            'action' => route('front.payments.store', $order->id),
            'method' => 'POST',
            'payment' => null,
            'max' => $remaining,
            'submit' => 'Save Payment',
        ])
    @endif
</div>
