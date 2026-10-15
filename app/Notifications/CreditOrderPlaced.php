<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Notifications\Notification;

/**
 * In-app (database) notification shown in the admin header bell.
 * The email side is sent by OrderNotificationService through the editable email template.
 */
class CreditOrderPlaced extends Notification
{
    public function __construct(public Order $order) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Credit order #'.$this->order->id,
            'message' => ($this->order->shop_name ?: 'A shop').' · ₹'.number_format((float) $this->order->total_amount, 2)
                .' · '.(int) $this->order->credit_days.' days credit',
            'order_id' => $this->order->id,
            'url' => route('admin.order.detail', $this->order->id),
            'icon' => 'ik-credit-card',
        ];
    }
}
