<?php

use App\Models\OrderPayment;
use App\Models\User;
use App\Notifications\PaymentLogged;
use App\Notifications\PaymentReviewed;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ViewErrorBag;

function salesmanOrder(array $attributes = []): array
{
    $salesman = userWithRole('Sales Executive', ['mobile' => '9000000001']);

    return [$salesman, orderWithSnapshot($attributes + ['user_id' => $salesman->id])];
}

function paymentData(array $overrides = []): array
{
    return $overrides + ['amount' => 400, 'paid_on' => now('Asia/Kolkata')->toDateString(), 'mode' => 'upi', 'reference' => 'UPI123'];
}

beforeEach(fn () => Notification::fake());

test('salesman logs a pending payment with proof on their own order and the admin is notified', function () {
    Storage::fake('local');
    [$salesman, $order] = salesmanOrder();
    $admin = userWithRole('Admin');

    $this->actingAs($salesman)
        ->post(route('front.payments.store', $order->id), paymentData(['proof' => UploadedFile::fake()->image('receipt.jpg')]))
        ->assertRedirect(route('front.order_detail', $order->id));

    $payment = OrderPayment::sole();
    expect($payment->status)->toBe('pending')
        ->and($payment->user_id)->toBe($salesman->id)
        ->and((float) $payment->amount)->toBe(400.0)
        ->and($order->fresh()->payment_status)->toBe('pending');

    Storage::disk('local')->assertExists($payment->proof_path);
    Notification::assertSentTo($admin, PaymentLogged::class);

    $this->actingAs($salesman)->get(route('front.payments.proof', $payment))->assertOk();
});

test('salesman cannot log a payment on another salesman\'s order', function () {
    [, $order] = salesmanOrder();
    $other = User::factory()->create(['mobile' => '9000000002']);
    $other->givePermissionTo('frontend_shop');

    $this->actingAs($other)->post(route('front.payments.store', $order->id), paymentData())->assertForbidden();

    expect(OrderPayment::count())->toBe(0);
});

test('overpayment is blocked, counting pending payments', function () {
    [$salesman, $order] = salesmanOrder();

    $this->actingAs($salesman)->post(route('front.payments.store', $order->id), paymentData(['amount' => 700]));
    $this->actingAs($salesman)->post(route('front.payments.store', $order->id), paymentData(['amount' => 301]))
        ->assertSessionHasErrors('amount');
    $this->actingAs($salesman)->post(route('front.payments.store', $order->id), paymentData(['amount' => 300]))
        ->assertSessionHasNoErrors();

    expect(OrderPayment::count())->toBe(2);
});

test('future dates are rejected', function () {
    [$salesman, $order] = salesmanOrder();

    $this->actingAs($salesman)
        ->post(route('front.payments.store', $order->id), paymentData(['paid_on' => now('Asia/Kolkata')->addDays(2)->toDateString()]))
        ->assertSessionHasErrors('paid_on');
});

test('verify moves the order to partial then paid; reject needs a reason and does not count', function () {
    [$salesman, $order] = salesmanOrder();
    $admin = userWithRole('Admin');

    $this->actingAs($salesman)->post(route('front.payments.store', $order->id), paymentData(['amount' => 600]));
    $this->actingAs($salesman)->post(route('front.payments.store', $order->id), paymentData(['amount' => 400]));
    [$first, $second] = OrderPayment::orderBy('id')->get();

    $this->actingAs($admin)->post(route('admin.payments.verify', $first))->assertSessionHas('success');
    expect($order->fresh()->payment_status)->toBe('partial');
    Notification::assertSentTo($salesman, PaymentReviewed::class);

    $this->actingAs($admin)->post(route('admin.payments.reject', $second), ['reason' => ''])->assertSessionHasErrors('reason');
    $this->actingAs($admin)->post(route('admin.payments.reject', $second), ['reason' => 'Cheque bounced']);
    expect($second->fresh()->status)->toBe('rejected')
        ->and($second->fresh()->review_reason)->toBe('Cheque bounced')
        ->and($order->fresh()->payment_status)->toBe('partial');

    // A rejected payment is final.
    $this->actingAs($admin)->post(route('admin.payments.verify', $second))->assertSessionHasErrors('status');

    $this->actingAs($salesman)->post(route('front.payments.store', $order->id), paymentData(['amount' => 400]));
    $this->actingAs($admin)->post(route('admin.payments.verify', OrderPayment::latest('id')->first()));
    expect($order->fresh()->payment_status)->toBe('paid');
});

test('reversing a verified payment puts the balance back', function () {
    [$salesman, $order] = salesmanOrder();
    $admin = userWithRole('Admin');

    $this->actingAs($salesman)->post(route('front.payments.store', $order->id), paymentData(['amount' => 1000]));
    $payment = OrderPayment::sole();
    $this->actingAs($admin)->post(route('admin.payments.verify', $payment));
    expect($order->fresh()->payment_status)->toBe('paid');

    $this->actingAs($admin)->post(route('admin.payments.reverse', $payment), ['reason' => 'Entered twice']);

    expect($payment->fresh()->status)->toBe('reversed')
        ->and($order->fresh()->payment_status)->toBe('pending');
});

test('salesman can edit or delete a pending payment, but not a verified one', function () {
    [$salesman, $order] = salesmanOrder();

    $this->actingAs($salesman)->post(route('front.payments.store', $order->id), paymentData(['amount' => 500]));
    $payment = OrderPayment::sole();

    $this->actingAs($salesman)->put(route('front.payments.update', $payment), paymentData(['amount' => 1000]))->assertSessionHasNoErrors();
    expect((float) $payment->fresh()->amount)->toBe(1000.0);

    $this->actingAs(userWithRole('Admin'))->post(route('admin.payments.verify', $payment));

    $this->actingAs($salesman)->put(route('front.payments.update', $payment), paymentData(['amount' => 10]))->assertSessionHasErrors('status');
    $this->actingAs($salesman)->delete(route('front.payments.destroy', $payment))->assertSessionHasErrors('status');
    expect(OrderPayment::count())->toBe(1);
});

test('cancelled orders take no payments; cancel is blocked by verified payments and rejects pending ones', function () {
    [$salesman, $order] = salesmanOrder();
    $admin = userWithRole('Admin');

    $this->actingAs($salesman)->post(route('front.payments.store', $order->id), paymentData(['amount' => 300]));
    $this->actingAs($salesman)->post(route('front.payments.store', $order->id), paymentData(['amount' => 200]));
    [$first, $second] = OrderPayment::orderBy('id')->get();
    $this->actingAs($admin)->post(route('admin.payments.verify', $first));

    $this->actingAs($admin)->postJson(route('admin.order.cancel'), ['order_id' => $order->id, 'cancel_note' => 'Shop closed'])
        ->assertStatus(422);
    expect($order->fresh()->order_status)->not->toBe('cancelled');

    $this->actingAs($admin)->post(route('admin.payments.reverse', $first), ['reason' => 'Refunded']);
    $this->actingAs($admin)->postJson(route('admin.order.cancel'), ['order_id' => $order->id, 'cancel_note' => 'Shop closed'])
        ->assertOk();

    expect($order->fresh()->order_status)->toBe('cancelled')
        ->and($second->fresh()->status)->toBe('rejected')
        ->and($second->fresh()->review_reason)->toBe('Order cancelled');

    $this->actingAs($salesman)->post(route('front.payments.store', $order->id), paymentData(['amount' => 100]))
        ->assertSessionHasErrors('amount');
});

test('admin Record Payment is verified immediately; marking delivered no longer marks paid', function () {
    [, $order] = salesmanOrder();
    $admin = userWithRole('Admin');

    $this->actingAs($admin)->patch(route('admin.order.update'), ['edit_id' => $order->id, 'order_status' => 'dispatched']);
    $order->update(['order_status' => 'delivered']);
    expect($order->fresh()->payment_status)->toBe('pending');

    $this->actingAs($admin)->postJson(route('admin.order.make_paid'), ['order_id' => $order->id] + paymentData(['amount' => 250, 'mode' => 'cash']))
        ->assertOk()->assertJson(['success' => true]);

    $payment = OrderPayment::sole();
    expect($payment->status)->toBe('verified')
        ->and($payment->reviewed_by)->toBe($admin->id)
        ->and($order->fresh()->payment_status)->toBe('partial');

    Notification::assertNotSentTo($admin, PaymentLogged::class);
});

test('managers cannot verify payments or open the payment queue', function () {
    [$salesman, $order] = salesmanOrder();
    $this->actingAs($salesman)->post(route('front.payments.store', $order->id), paymentData());
    $manager = userWithRole('Manager');

    $this->actingAs($manager)->get(route('admin.payments.index'))->assertForbidden();
    $this->actingAs($manager)->post(route('admin.payments.verify', OrderPayment::sole()))->assertForbidden();
});

test('pages render: salesman payments section, admin queue and order detail', function () {
    [$salesman, $order] = salesmanOrder();
    $this->actingAs($salesman)->post(route('front.payments.store', $order->id), paymentData());

    // The full salesman layout can't render in the test DB (it reads carts.variant_id, which no migration creates),
    // so render the payments section on its own.
    $html = view('front.partials.order_payments', [
        'order' => $order->fresh()->load('payments'),
        'errors' => new ViewErrorBag,
    ])->render();
    expect($html)->toContain('Payments Collected')->toContain('Awaiting verification')->toContain('Log a payment');

    $admin = userWithRole('Admin');
    $this->actingAs($admin)->get(route('admin.payments.index'))->assertOk()->assertSee('UPI123');
    $this->actingAs($admin)->get(route('admin.order.detail', $order->id))->assertOk()->assertSee('Payments Received');
});
