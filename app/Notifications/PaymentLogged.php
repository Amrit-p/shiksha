<?php

namespace App\Notifications;

use App\Models\OrderPayment;
use Illuminate\Notifications\Notification;

/** Admin bell: a salesman logged a payment that needs verifying. */
class PaymentLogged extends Notification
{
    public function __construct(public OrderPayment $payment) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $order = $this->payment->order;

        return [
            'title' => 'Payment to verify · order #'.$this->payment->order_id,
            'message' => ($this->payment->salesman?->name ?? 'A salesman').' · ₹'.number_format((float) $this->payment->amount, 2)
                .' · '.$this->payment->modeLabel().' · '.($order?->shop_name ?: 'Shop'),
            'payment_id' => $this->payment->id,
            'order_id' => $this->payment->order_id,
            'url' => route('admin.payments.index', ['status' => 'pending']),
            'icon' => 'ik-dollar-sign',
        ];
    }
}
