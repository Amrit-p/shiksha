<?php

namespace App\Services;

use App\Models\Coupon;
use Illuminate\Validation\ValidationException;

/**
 * Validates a coupon for an order total and works out the discount.
 * A "shop" is identified by the owner phone, or the GST number when given.
 */
class CouponService
{
    /**
     * @return array{coupon: Coupon, discount: float}
     *
     * @throws ValidationException (key: coupon_code)
     */
    public function evaluate(string $code, float $subtotal, ?string $shopPhone, ?string $shopGst, bool $lock = false): array
    {
        $code = strtoupper(trim($code));

        $coupon = Coupon::query()
            ->where('code', $code)
            ->when($lock, fn ($q) => $q->lockForUpdate())
            ->first();

        if (! $coupon || ! $coupon->is_active) {
            $this->fail('Invalid coupon code.');
        }

        $today = now()->startOfDay();
        if ($coupon->starts_at && $today->lt($coupon->starts_at)) {
            $this->fail('This coupon is valid from '.$coupon->starts_at->format('d M Y').'.');
        }
        if ($coupon->ends_at && $today->gt($coupon->ends_at)) {
            $this->fail('This coupon expired on '.$coupon->ends_at->format('d M Y').'.');
        }

        if ($coupon->min_order_amount && $subtotal < (float) $coupon->min_order_amount) {
            $this->fail('Minimum order value for this coupon is ₹'.number_format((float) $coupon->min_order_amount, 2).'.');
        }

        if ($coupon->usage_limit_total && $coupon->countedOrders()->count() >= $coupon->usage_limit_total) {
            $this->fail('This coupon has reached its usage limit.');
        }

        if ($coupon->usage_limit_per_shop) {
            $shopPhone = trim((string) $shopPhone);
            $shopGst = strtoupper(trim((string) $shopGst));

            if ($shopPhone === '' && $shopGst === '') {
                $this->fail('Enter the shop owner phone before applying this coupon.');
            }

            $usedByShop = $coupon->countedOrders()
                ->where(function ($q) use ($shopPhone, $shopGst) {
                    if ($shopPhone !== '') {
                        $q->orWhere('owner_phone', $shopPhone);
                    }
                    if ($shopGst !== '') {
                        $q->orWhereRaw('UPPER(gst_number) = ?', [$shopGst]);
                    }
                })
                ->count();

            if ($usedByShop >= $coupon->usage_limit_per_shop) {
                $this->fail('This shop has already used this coupon'.($coupon->usage_limit_per_shop > 1 ? " {$coupon->usage_limit_per_shop} times" : '').'.');
            }
        }

        return ['coupon' => $coupon, 'discount' => $this->discountFor($coupon, $subtotal)];
    }

    public function discountFor(Coupon $coupon, float $subtotal): float
    {
        if ($coupon->type === Coupon::TYPE_PERCENT) {
            $discount = $subtotal * (float) $coupon->value / 100;
            if ($coupon->max_discount) {
                $discount = min($discount, (float) $coupon->max_discount);
            }
        } else {
            $discount = (float) $coupon->value;
        }

        return round(max(0, min($discount, $subtotal)), 2);
    }

    protected function fail(string $message): never
    {
        throw ValidationException::withMessages(['coupon_code' => $message]);
    }
}
