<?php

namespace App\Services;

use App\Mail\TemplatedMail;
use App\Models\Order;
use App\Models\SmtpSetting;
use App\Models\User;
use App\Models\WebsiteSetting;
use App\Notifications\CreditOrderPlaced;
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
            $admins = User::permission('admin')->get();
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
}
