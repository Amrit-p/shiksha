<?php

use App\Models\Coupon;
use App\Services\CouponService;
use Illuminate\Validation\ValidationException;

function coupon(array $attributes = []): Coupon
{
    return Coupon::create($attributes + ['code' => 'SAVE10', 'type' => 'percent', 'value' => 10, 'is_active' => true]);
}

function couponError(callable $fn): ?string
{
    try {
        $fn();
    } catch (ValidationException $e) {
        return $e->validator->errors()->first('coupon_code');
    }

    return null;
}

test('percent coupon respects the max discount cap; flat never exceeds the total', function () {
    $service = app(CouponService::class);
    coupon();

    expect($service->evaluate('save10', 1000, '1', null)['discount'])->toBe(100.0);

    coupon(['code' => 'CAP', 'max_discount' => 50]);
    expect($service->evaluate('CAP', 1000, '1', null)['discount'])->toBe(50.0);

    coupon(['code' => 'FLAT', 'type' => 'flat', 'value' => 500]);
    expect($service->evaluate('FLAT', 300, '1', null)['discount'])->toBe(300.0);
});

test('date window, minimum order and inactive coupons are rejected', function () {
    $service = app(CouponService::class);
    coupon(['code' => 'OLD', 'ends_at' => now()->subDay()]);
    coupon(['code' => 'SOON', 'starts_at' => now()->addDay()]);
    coupon(['code' => 'MIN', 'min_order_amount' => 500]);
    coupon(['code' => 'OFF', 'is_active' => false]);
    coupon(['code' => 'TODAY', 'starts_at' => now(), 'ends_at' => now()]);

    expect(couponError(fn () => $service->evaluate('OLD', 1000, '1', null)))->toContain('expired')
        ->and(couponError(fn () => $service->evaluate('SOON', 1000, '1', null)))->toContain('valid from')
        ->and(couponError(fn () => $service->evaluate('MIN', 499, '1', null)))->toContain('Minimum order')
        ->and(couponError(fn () => $service->evaluate('OFF', 1000, '1', null)))->toBe('Invalid coupon code.')
        ->and(couponError(fn () => $service->evaluate('TODAY', 1000, '1', null)))->toBeNull();
});

test('per-shop limit counts by phone or GST and ignores cancelled orders', function () {
    $service = app(CouponService::class);
    $coupon = coupon(['usage_limit_per_shop' => 1]);

    $used = orderWithSnapshot(['owner_phone' => '9000000001', 'gst_number' => '27ABCDE1234F1Z5', 'coupon_id' => $coupon->id]);

    expect(couponError(fn () => $service->evaluate('SAVE10', 1000, '9000000001', null)))->toContain('already used')
        ->and(couponError(fn () => $service->evaluate('SAVE10', 1000, '9000000099', '27abcde1234f1z5')))->toContain('already used')
        ->and(couponError(fn () => $service->evaluate('SAVE10', 1000, '9000000002', null)))->toBeNull();

    $used->update(['order_status' => 'cancelled']);
    expect(couponError(fn () => $service->evaluate('SAVE10', 1000, '9000000001', null)))->toBeNull();
});

test('total usage limit', function () {
    $coupon = coupon(['usage_limit_total' => 1]);
    orderWithSnapshot(['coupon_id' => $coupon->id]);

    expect(couponError(fn () => app(CouponService::class)->evaluate('SAVE10', 1000, '5', null)))->toContain('usage limit');
});
