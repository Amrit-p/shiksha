{{-- Payment entry form (Bootstrap 3). Expects $action, $method, $payment (nullable), $max, $submit. --}}
@php($today = now('Asia/Kolkata')->toDateString())
<form method="POST" action="{{ $action }}" enctype="multipart/form-data">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif
    <div class="row">
        <div class="col-sm-3 form-group">
            <label>Amount (₹) <span class="text-danger">*</span></label>
            <input type="number" step="0.01" min="0.01" max="{{ $max }}" name="amount" class="form-control" required
                   value="{{ $payment ? $payment->amount : old('amount', $max) }}">
            <small class="text-muted">Up to ₹{{ number_format($max, 2) }}</small>
        </div>
        <div class="col-sm-3 form-group">
            <label>Received on <span class="text-danger">*</span></label>
            <input type="date" name="paid_on" class="form-control" max="{{ $today }}" required
                   value="{{ $payment ? $payment->paid_on->toDateString() : old('paid_on', $today) }}">
        </div>
        <div class="col-sm-3 form-group">
            <label>Mode <span class="text-danger">*</span></label>
            <select name="mode" class="form-control" required>
                @foreach(\App\Models\OrderPayment::MODES as $value => $label)
                    <option value="{{ $value }}" @selected(($payment?->mode ?? old('mode')) === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-sm-3 form-group">
            <label>Reference</label>
            <input name="reference" class="form-control" maxlength="100" placeholder="UPI txn / cheque no."
                   value="{{ $payment ? $payment->reference : old('reference') }}">
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6 form-group">
            <label>Note</label>
            <input name="note" class="form-control" maxlength="1000" value="{{ $payment ? $payment->note : old('note') }}">
        </div>
        <div class="col-sm-4 form-group">
            <label>Proof photo {{ $payment?->proof_path ? '(replace)' : '' }}</label>
            <input type="file" name="proof" accept="image/*,application/pdf">
            <small class="text-muted">Cheque, receipt or UPI screenshot. Max 5 MB.</small>
        </div>
        <div class="col-sm-2 form-group">
            <label>&nbsp;</label>
            <button type="submit" class="btn btn-success btn-block">{{ $submit }}</button>
        </div>
    </div>
</form>
