<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDeliveryItem extends Model
{
    protected $fillable = ['order_delivery_id', 'line_key', 'qty'];

    public function delivery()
    {
        return $this->belongsTo(OrderDelivery::class, 'order_delivery_id');
    }
}
