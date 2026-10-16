<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Notifications\Notification;

/** In-app (admin bell) alert: an order pushed items to 0 stock. $items = [['name' => ..., 'short' => int], ...] */
class OutOfStockOrdered extends Notification
{
    public function __construct(public Order $order, public array $items) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $lines = array_map(
            fn ($i) => $i['name'].($i['short'] > 0 ? ' (short by '.$i['short'].')' : ''),
            $this->items
        );

        return [
            'title' => 'Out of stock · order #'.$this->order->id,
            'message' => implode(', ', $lines),
            'order_id' => $this->order->id,
            'url' => route('admin.order.detail', $this->order->id),
            'icon' => 'ik-alert-triangle',
        ];
    }
}
