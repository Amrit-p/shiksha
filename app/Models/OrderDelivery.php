<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDelivery extends Model
{
    protected $fillable = ['order_id', 'delivered_on', 'note', 'created_by'];

    protected function casts(): array
    {
        return ['delivered_on' => 'date'];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function items()
    {
        return $this->hasMany(OrderDeliveryItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
