<?php

namespace App\Http\Controllers\Salesman;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderPaymentRequest;
use App\Models\Order;
use App\Models\OrderPayment;
use App\Services\OrderPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/** Salesman: log, edit and delete payments collected on their own orders (pending until the admin verifies). */
class PaymentController extends Controller
{
    public function __construct(protected OrderPaymentService $payments) {}

    public function store(OrderPaymentRequest $request, Order $order)
    {
        $this->payments->log($order, $request->validated(), $request->user(), $request->file('proof'));

        return redirect()->route('front.order_detail', $order->id)->with('success', 'Payment saved. It will count once the admin verifies it.');
    }

    public function update(OrderPaymentRequest $request, OrderPayment $payment)
    {
        $this->payments->update($payment, $request->validated(), $request->user(), $request->file('proof'));

        return redirect()->route('front.order_detail', $payment->order_id)->with('success', 'Payment updated.');
    }

    public function destroy(Request $request, OrderPayment $payment)
    {
        $this->payments->delete($payment, $request->user());

        return redirect()->route('front.order_detail', $payment->order_id)->with('success', 'Payment deleted.');
    }

    public function proof(Request $request, OrderPayment $payment)
    {
        abort_unless((int) $payment->user_id === (int) $request->user()->id || $request->user()->can('admin'), 403);
        abort_unless($payment->proof_path && Storage::disk(OrderPaymentService::PROOF_DISK)->exists($payment->proof_path), 404);

        return Storage::disk(OrderPaymentService::PROOF_DISK)->response($payment->proof_path);
    }
}
