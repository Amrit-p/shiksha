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
        'subtotal_amount',
        'discount_amount',
        'total_amount',
        'coupon_id',
        'coupon_code',

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
        'transport_gst',
        'transport_mobile',
        'contact_person',
        'delivery_type',
        'dispatch_date',
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

    public function deliveries()
    {
        return $this->hasMany(OrderDelivery::class)->latest('delivered_on')->latest('id');
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function payments()
    {
        return $this->hasMany(OrderPayment::class)->latest('paid_on')->latest('id');
    }

    /* ================= PAYMENTS ================= */

    public function isCancelled(): bool
    {
        return strtolower((string) $this->order_status) === 'cancelled';
    }

    public function verifiedPaid(): float
    {
        return (float) $this->payments()->verified()->sum('amount');
    }

    public function pendingPaid(): float
    {
        return (float) $this->payments()->pending()->sum('amount');
    }

    /** Amount still to be verified as received. */
    public function balance(): float
    {
        return max(0, round((float) $this->total_amount - $this->verifiedPaid(), 2));
    }
}
