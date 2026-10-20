@extends('admin.layouts.main')
@section('title','Payments')
@section('content')
@include('admin.include.message')
<div class="container-fluid">
  <div class="card">
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
      <h3 class="mb-0">Payments</h3>
      <form method="GET" class="form-inline">
        <input type="hidden" name="status" value="{{ $status }}">
        <select name="salesman_id" class="form-control form-control-sm" onchange="this.form.submit()">
          <option value="">All salesmen</option>
          @foreach($salesmen as $s)
            <option value="{{ $s->id }}" @selected(request('salesman_id') == $s->id)>{{ $s->name }}</option>
          @endforeach
        </select>
      </form>
    </div>
    <div class="card-body">
      @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
      @endif

      <ul class="nav nav-pills mb-3">
        @foreach($statuses as $s)
          <li class="nav-item">
            <a class="nav-link {{ $status === $s ? 'active' : '' }}" href="{{ route('admin.payments.index', array_filter(['status' => $s, 'salesman_id' => request('salesman_id')])) }}">
              {{ $s === 'pending' ? 'To verify' : ucfirst($s) }}
              @if($s !== 'all' && ($counts[$s] ?? 0))
                <span class="badge {{ $s === 'pending' ? 'badge-warning' : 'badge-light' }}">{{ $counts[$s] }}</span>
              @endif
            </a>
          </li>
        @endforeach
      </ul>

      <div class="table-responsive">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>#</th>
              <th>Salesman</th>
              <th>Order</th>
              <th>Received on</th>
              <th class="text-right">Amount</th>
              <th>Mode / Ref.</th>
              <th>Proof</th>
              <th>Status</th>
              <th class="text-right">Action</th>
            </tr>
          </thead>
          <tbody>
            @forelse($payments as $payment)
              <tr>
                <td>{{ $payment->id }}</td>
                <td>{{ $payment->salesman?->name ?? '—' }}</td>
                <td>
                  <a href="{{ route('admin.order.detail', $payment->order_id) }}">#{{ $payment->order_id }}</a>
                  <div class="small text-muted">{{ $payment->order?->shop_name }} · ₹{{ number_format((float) $payment->order?->total_amount, 2) }}</div>
                </td>
                <td>{{ $payment->paid_on->format('d M Y') }}<div class="small text-muted">logged {{ $payment->created_at->diffForHumans() }}</div></td>
                <td class="text-right font-weight-bold">₹{{ number_format((float) $payment->amount, 2) }}</td>
                <td>
                  {{ $payment->modeLabel() }}
                  @if($payment->reference)<div class="small text-muted">{{ $payment->reference }}</div>@endif
                  @if($payment->note)<div class="small text-muted" title="{{ $payment->note }}">{{ \Illuminate\Support\Str::limit($payment->note, 40) }}</div>@endif
                </td>
                <td>
                  @if($payment->proof_path)
                    <a href="{{ route('admin.payments.proof', $payment) }}" target="_blank"><i class="ik ik-image"></i> View</a>
                  @else
                    —
                  @endif
                </td>
                <td>
                  <span class="badge badge-{{ $payment->statusBadge() }}">{{ ucfirst($payment->status) }}</span>
                  @if($payment->review_reason)<div class="small text-muted">{{ $payment->review_reason }}</div>@endif
                  @if($payment->reviewer)<div class="small text-muted">by {{ $payment->reviewer->name }}</div>@endif
                </td>
                <td class="text-right text-nowrap">
                  @if($payment->status === 'pending')
                    <form method="POST" action="{{ route('admin.payments.verify', $payment) }}" class="d-inline">
                      @csrf
                      <button type="submit" class="btn btn-sm btn-success">Verify</button>
                    </form>
                    <button type="button" class="btn btn-sm btn-outline-danger payment-reason-btn" data-action="reject" data-id="{{ $payment->id }}" data-url="{{ route('admin.payments.reject', $payment) }}">Reject</button>
                  @elseif($payment->status === 'verified')
                    <button type="button" class="btn btn-sm btn-outline-secondary payment-reason-btn" data-action="reverse" data-id="{{ $payment->id }}" data-url="{{ route('admin.payments.reverse', $payment) }}">Reverse</button>
                  @endif
                </td>
              </tr>
            @empty
              <tr><td colspan="9" class="text-center text-muted py-4">No payments here.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>

      @if($payments->hasPages())
        <div class="mt-3">{{ $payments->links('pagination::bootstrap-4') }}</div>
      @endif
    </div>
  </div>
</div>
@include('admin.payments._review_modal')
@endsection
