<?php

namespace App\Services;

use App\Mail\TemplatedMail;
use App\Models\Order;
use App\Models\OrderPayment;
use App\Models\SmtpSetting;
use App\Models\User;
use App\Models\WebsiteSetting;
use App\Notifications\CreditOrderPlaced;
use App\Notifications\OutOfStockOrdered;
use App\Notifications\PaymentLogged;
use App\Notifications\PaymentReviewed;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class OrderNotificationService
{
    public function __construct(
        protected MailTemplateService $mailTemplates,
        protected SmtpConfigurator $smtp,
    ) {}

    /**
     * Notify admins (in-app bell + email) that a credit order was placed.
     * Never throws: the order is already committed, so failures are only logged.
     */
    public function creditOrderPlaced(Order $order): void
    {
        $order->loadMissing('user');

        try {
            $admins = User::permission(['admin', 'manage_orders'])->get();
            if ($admins->isNotEmpty()) {
                Notification::send($admins, new CreditOrderPlaced($order));
            }
        } catch (\Throwable $e) {
            Log::error('Credit order in-app notification failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }

        try {
            $adminEmail = SmtpSetting::current()?->admin_email
                ?: WebsiteSetting::getValue('admin_notification_email');

            if (! $adminEmail) {
                return;
            }

            $placedAt = $order->created_at ?? now();

            $mail = $this->mailTemplates->build('admin_credit_order_notification', [
                'order_id' => $order->id,
                'salesman_name' => $order->user?->name ?? 'A salesman',
                'shop_name' => $order->shop_name ?: '—',
                'owner_name' => $order->owner_name ?: '—',
                'owner_phone' => $order->owner_phone ?: '—',
                'city' => $order->city ?: '—',
                'total_qty' => (int) $order->total_qty,
                'total_amount' => number_format((float) $order->total_amount, 2),
                'credit_days' => (int) $order->credit_days,
                'due_date' => $placedAt->copy()->addDays((int) $order->credit_days)->format('d M Y'),
                'order_date' => $placedAt->format('d M Y, h:i A'),
                'order_url' => route('admin.order.detail', $order->id),
            ]);

            if ($mail) {
                $this->smtp->apply();
                Mail::to($adminEmail)->send(new TemplatedMail($mail['subject'], $mail['body']));
            }
        } catch (\Throwable $e) {
            Log::error('Credit order email notification failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /** A salesman logged a payment: admin bell + admin email. Never throws. */
    public function paymentLogged(OrderPayment $payment): void
    {
        $payment->loadMissing('order', 'salesman');

        try {
            $admins = User::permission('admin')->get();
            if ($admins->isNotEmpty()) {
                Notification::send($admins, new PaymentLogged($payment));
            }
        } catch (\Throwable $e) {
            Log::error('Payment logged in-app notification failed', ['payment_id' => $payment->id, 'error' => $e->getMessage()]);
        }

        try {
            $adminEmail = SmtpSetting::current()?->admin_email
                ?: WebsiteSetting::getValue('admin_notification_email');

            if (! $adminEmail) {
                return;
            }

            $order = $payment->order;

            $mail = $this->mailTemplates->build('admin_payment_logged', [
                'salesman_name' => $payment->salesman?->name ?? 'A salesman',
                'amount' => number_format((float) $payment->amount, 2),
                'order_id' => $payment->order_id,
                'shop_name' => $order?->shop_name ?: '—',
                'paid_on' => $payment->paid_on?->format('d M Y'),
                'mode' => $payment->modeLabel(),
                'reference' => $payment->reference ?: '—',
                'order_total' => number_format((float) $order?->total_amount, 2),
                'balance' => number_format(max(0, (float) $order?->total_amount - $order?->verifiedPaid() - $order?->pendingPaid()), 2),
                'review_url' => route('admin.payments.index', ['status' => 'pending']),
            ]);

            if ($mail) {
                $this->smtp->apply();
                Mail::to($adminEmail)->send(new TemplatedMail($mail['subject'], $mail['body']));
            }
        } catch (\Throwable $e) {
            Log::error('Payment logged email failed', ['payment_id' => $payment->id, 'error' => $e->getMessage()]);
        }
    }

    /** Tell the salesman (bell) that their payment was verified, rejected or reversed. Never throws. */
    public function paymentReviewed(OrderPayment $payment): void
    {
        try {
            $payment->salesman?->notify(new PaymentReviewed($payment));
        } catch (\Throwable $e) {
            Log::error('Payment reviewed notification failed', ['payment_id' => $payment->id, 'error' => $e->getMessage()]);
        }
    }

    /** Tell admins (bell only) which items an order pushed to 0 stock. Never throws. */
    public function outOfStock(Order $order, array $items): void
    {
        try {
            $admins = User::permission(['admin', 'manage_orders'])->get();
            if ($admins->isNotEmpty()) {
                Notification::send($admins, new OutOfStockOrdered($order, $items));
            }
        } catch (\Throwable $e) {
            Log::error('Out of stock notification failed', ['order_id' => $order->id, 'error' => $e->getMessage()]);
        }
    }
}
