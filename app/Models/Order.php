<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',

        // shop / owner
        'shop_name',
        'owner_name',
        'owner_phone',
        'owner_email',
        'owner_address',
        'gst_number',

        // amounts
        'total_qty',
        'total_amount',

        // payment
        'payment_type',
        'payment_status',
        'payment_id',
        'order_status',
        // status
        'order_status_id',

        // time
        'punch_in_time',
        'city',
        'state',
        'pin_code',
        'product_json',
        'is_gst',
        'is_transport',
        'transport_name',
        'landmark',
        'credit_days',

        'cancel_note',
        'cancelled_at',

    ];

    protected $casts = [
        'cancelled_at' => 'datetime',
    ];



    /* ================= RELATIONSHIPS ================= */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function status()
    {
        return $this->belongsTo(OrderStatus::class, 'order_status_id');
    }

    public function items()
    {
        return $this->hasMany(OrderDetails::class);
    }
}
