<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    public const TYPE_PERCENT = 'percent';
    public const TYPE_FLAT = 'flat';

    protected $fillable = [
        'code',
        'description',
        'type',
        'value',
        'max_discount',
        'min_order_amount',
        'starts_at',
        'ends_at',
        'usage_limit_total',
        'usage_limit_per_shop',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'max_discount' => 'decimal:2',
            'min_order_amount' => 'decimal:2',
            'starts_at' => 'date',
            'ends_at' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function setCodeAttribute(?string $value): void
    {
        $this->attributes['code'] = strtoupper(trim((string) $value));
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /** Orders that count towards usage limits (cancelled orders free their use). */
    public function countedOrders()
    {
        return $this->orders()->where(function ($q) {
            $q->whereNull('order_status')->orWhere('order_status', '!=', 'cancelled');
        });
    }

    public function summary(): string
    {
        $text = $this->type === self::TYPE_PERCENT
            ? rtrim(rtrim(number_format((float) $this->value, 2), '0'), '.').'% off'
            : '₹'.number_format((float) $this->value, 2).' off';

        if ($this->type === self::TYPE_PERCENT && $this->max_discount) {
            $text .= ' (max ₹'.number_format((float) $this->max_discount, 2).')';
        }

        return $text;
    }
}
