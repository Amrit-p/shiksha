<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderPayment;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

/**
 * Every rule about order payments lives here: who may log/edit/review, the no-overpayment check,
 * cancelled-order guards and keeping orders.payment_status in sync with verified payments.
 */
class OrderPaymentService
{
    public const PROOF_DISK = 'local';

    public function __construct(protected OrderNotificationService $notifications) {}

    /**
     * Record money received for an order. Admin entries are verified straight away;
     * salesman entries wait for verification.
     *
     * @param  array{amount: numeric, paid_on: string, mode: string, reference?: ?string, note?: ?string}  $data
     */
    public function log(Order $order, array $data, User $actor, ?UploadedFile $proof = null): OrderPayment
    {
        $isAdmin = $actor->can('admin');

        if (! $isAdmin && (int) $order->user_id !== (int) $actor->id) {
            abort(403);
        }

        $payment = DB::transaction(function () use ($order, $data, $actor, $proof, $isAdmin) {
            $order = Order::query()->lockForUpdate()->findOrFail($order->id);

            if ($order->isCancelled()) {
                $this->fail('amount', "Order #{$order->id} is cancelled, so it cannot take payments.");
            }

            $this->assertWithinBalance($order, (float) $data['amount']);

            $payment = OrderPayment::create([
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'recorded_by' => $actor->id,
                'amount' => $data['amount'],
                'paid_on' => $data['paid_on'],
                'mode' => $data['mode'],
                'reference' => $data['reference'] ?? null,
                'note' => $data['note'] ?? null,
                'proof_path' => $proof?->store('payment-proofs', self::PROOF_DISK),
                'status' => $isAdmin ? OrderPayment::STATUS_VERIFIED : OrderPayment::STATUS_PENDING,
                'reviewed_by' => $isAdmin ? $actor->id : null,
                'reviewed_at' => $isAdmin ? now() : null,
            ]);

            $this->syncOrderStatus($order);

            return $payment;
        });

        if ($payment->isPending()) {
            $this->notifications->paymentLogged($payment);
        }

        return $payment;
    }

    /** Salesman edits their own payment while it still awaits verification. */
    public function update(OrderPayment $payment, array $data, User $actor, ?UploadedFile $proof = null): OrderPayment
    {
        $this->assertEditable($payment, $actor);

        return DB::transaction(function () use ($payment, $data, $proof) {
            $order = Order::query()->lockForUpdate()->findOrFail($payment->order_id);
            $this->assertWithinBalance($order, (float) $data['amount'], $payment->id);

            $attributes = [
                'amount' => $data['amount'],
                'paid_on' => $data['paid_on'],
                'mode' => $data['mode'],
                'reference' => $data['reference'] ?? null,
                'note' => $data['note'] ?? null,
            ];

            if ($proof) {
                $this->deleteProof($payment);
                $attributes['proof_path'] = $proof->store('payment-proofs', self::PROOF_DISK);
            }

            $payment->update($attributes);

            return $payment;
        });
    }

    public function delete(OrderPayment $payment, User $actor): void
    {
        $this->assertEditable($payment, $actor);

        $this->deleteProof($payment);
        $payment->delete();
    }

    public function verify(OrderPayment $payment, User $admin): OrderPayment
    {
        return $this->review($payment, $admin, OrderPayment::STATUS_PENDING, OrderPayment::STATUS_VERIFIED, null);
    }

    public function reject(OrderPayment $payment, User $admin, string $reason): OrderPayment
    {
        return $this->review($payment, $admin, OrderPayment::STATUS_PENDING, OrderPayment::STATUS_REJECTED, $reason);
    }

    public function reverse(OrderPayment $payment, User $admin, string $reason): OrderPayment
    {
        return $this->review($payment, $admin, OrderPayment::STATUS_VERIFIED, OrderPayment::STATUS_REVERSED, $reason);
    }

    /** Paid status comes only from verified payments: pending (nothing), partial, or paid (covers the total). */
    public function syncOrderStatus(Order $order): void
    {
        $verified = (float) OrderPayment::query()->where('order_id', $order->id)->verified()->sum('amount');
        $total = (float) $order->total_amount;

        $status = match (true) {
            $verified <= 0 => 'pending',
            $verified + 0.005 >= $total => 'paid',
            default => 'partial',
        };

        if ($order->payment_status !== $status) {
            $order->forceFill(['payment_status' => $status])->save();
        }
    }

    /**
     * Called before an order is cancelled: refuse while verified money sits on it,
     * and auto-reject anything still awaiting verification.
     */
    public function prepareCancel(Order $order, User $admin): void
    {
        if (OrderPayment::query()->where('order_id', $order->id)->verified()->exists()) {
            $this->fail('order_id', "Order #{$order->id} has verified payments. Reverse them before cancelling the order.");
        }

        OrderPayment::query()->where('order_id', $order->id)->pending()->get()
            ->each(fn (OrderPayment $payment) => $this->reject($payment, $admin, 'Order cancelled'));
    }

    protected function review(OrderPayment $payment, User $admin, string $from, string $to, ?string $reason): OrderPayment
    {
        if (! $admin->can('admin')) {
            abort(403);
        }

        $payment = DB::transaction(function () use ($payment, $admin, $from, $to, $reason) {
            $payment = OrderPayment::query()->lockForUpdate()->findOrFail($payment->id);

            if ($payment->status !== $from) {
                $this->fail('status', "This payment is already {$payment->status}.");
            }

            if ($to === OrderPayment::STATUS_VERIFIED) {
                $order = Order::query()->lockForUpdate()->findOrFail($payment->order_id);
                if ($order->isCancelled()) {
                    $this->fail('status', "Order #{$order->id} is cancelled, so this payment cannot be verified.");
                }
            }

            $payment->update([
                'status' => $to,
                'reviewed_by' => $admin->id,
                'reviewed_at' => now(),
                'review_reason' => $reason,
            ]);

            $this->syncOrderStatus($payment->order);

            return $payment;
        });

        $this->notifications->paymentReviewed($payment);

        return $payment;
    }

    /** Verified + pending payments (excluding $ignoreId) plus $amount may not exceed the order total. */
    protected function assertWithinBalance(Order $order, float $amount, ?int $ignoreId = null): void
    {
        $reserved = (float) OrderPayment::query()
            ->where('order_id', $order->id)
            ->open()
            ->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))
            ->sum('amount');

        $remaining = round((float) $order->total_amount - $reserved, 2);

        if ($amount > $remaining + 0.005) {
            $this->fail('amount', $remaining > 0
                ? 'Amount cannot exceed the remaining balance of Rs. '.number_format($remaining, 2).' (pending payments included).'
                : 'This order is already fully covered by verified or pending payments.');
        }
    }

    protected function assertEditable(OrderPayment $payment, User $actor): void
    {
        if ((int) $payment->user_id !== (int) $actor->id && ! $actor->can('admin')) {
            abort(403);
        }

        if (! $payment->isPending()) {
            $this->fail('status', 'Only payments awaiting verification can be changed.');
        }
    }

    protected function deleteProof(OrderPayment $payment): void
    {
        if ($payment->proof_path) {
            Storage::disk(self::PROOF_DISK)->delete($payment->proof_path);
        }
    }

    protected function fail(string $field, string $message): never
    {
        throw ValidationException::withMessages([$field => $message]);
    }
}
