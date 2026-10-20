<?php

namespace App\Notifications;

use App\Models\OrderPayment;
use Illuminate\Notifications\Notification;

/** Salesman bell: their payment was verified, rejected or reversed by the admin. */
class PaymentReviewed extends Notification
{
    public function __construct(public OrderPayment $payment) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $amount = '₹'.number_format((float) $this->payment->amount, 2);

        return [
            'title' => 'Payment '.$this->payment->status.' · order #'.$this->payment->order_id,
            'message' => $amount.' received '.$this->payment->paid_on?->format('d M Y')
                .($this->payment->review_reason ? ' · '.$this->payment->review_reason : ''),
            'payment_id' => $this->payment->id,
            'order_id' => $this->payment->order_id,
            'url' => route('front.order_detail', $this->payment->order_id),
            'icon' => match ($this->payment->status) {
                OrderPayment::STATUS_VERIFIED => 'fa-check-circle',
                default => 'fa-times-circle',
            },
        ];
    }
}
