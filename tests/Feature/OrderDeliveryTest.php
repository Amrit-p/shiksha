<?php

use App\Services\OrderDeliveryService;

test('lines flatten simple and variant items with positional keys', function () {
    $lines = app(OrderDeliveryService::class)->lines(orderWithSnapshot());

    expect(array_column($lines, 'key'))->toBe(['0', '1-0', '1-1'])
        ->and(array_column($lines, 'ordered'))->toBe([10, 5, 3])
        ->and(array_sum(array_column($lines, 'pending')))->toBe(18);
});

test('partial deliveries move the order to partially delivered, then delivered', function () {
    $order = orderWithSnapshot();
    $admin = userWithRole('Admin');

    $this->actingAs($admin)->post(route('admin.order.delivery.store', $order->id), [
        'delivered_on' => now()->toDateString(),
        'qty' => ['0' => 4, '1-0' => 5, '1-1' => 0],
    ])->assertRedirect();

    expect($order->fresh()->order_status)->toBe('partially_delivered')
        ->and(app(OrderDeliveryService::class)->pendingQty($order))->toBe(9);

    $this->actingAs($admin)->post(route('admin.order.delivery.store', $order->id), [
        'delivered_on' => now()->toDateString(),
        'qty' => ['0' => 6, '1-1' => 3],
    ]);

    $order->refresh();
    expect($order->order_status)->toBe('delivered')
        ->and($order->payment_status)->toBe('pending') // delivery no longer marks the order paid; verified payments do
        ->and($order->deliveries()->count())->toBe(2);
});

test('cannot deliver more than pending', function () {
    $order = orderWithSnapshot();

    $this->actingAs(userWithRole('Admin'))->post(route('admin.order.delivery.store', $order->id), [
        'delivered_on' => now()->toDateString(),
        'qty' => ['0' => 11],
    ])->assertSessionHasErrors('qty');

    expect($order->deliveries()->count())->toBe(0)
        ->and($order->fresh()->order_status)->toBe('pending');
});

test('status popup: Partial Pending records the entered quantities', function () {
    $order = orderWithSnapshot();

    $this->actingAs(userWithRole('Manager'))->patch(route('admin.order.update'), [
        'edit_id' => $order->id,
        'order_status' => 'partially_delivered',
        'delivered_on' => now()->toDateString(),
        'qty' => ['0' => 10, '1-0' => 2, '1-1' => 0],
    ])->assertSessionHas('success');

    expect($order->fresh()->order_status)->toBe('partially_delivered')
        ->and(app(OrderDeliveryService::class)->pendingQty($order))->toBe(6)
        ->and(orderStatusLabel('partially_delivered'))->toBe('Partial Pending');
});

test('status popup: Delivered with items left out is rejected and records nothing', function () {
    $order = orderWithSnapshot();

    $this->actingAs(userWithRole('Admin'))->patch(route('admin.order.update'), [
        'edit_id' => $order->id,
        'order_status' => 'delivered',
        'delivered_on' => now()->toDateString(),
        'qty' => ['0' => 10, '1-0' => 5, '1-1' => 1],
    ])->assertSessionHas('error');

    expect($order->deliveries()->count())->toBe(0)
        ->and($order->fresh()->order_status)->toBe('pending');
});

test('status popup: Delivered with everything pending finishes the order', function () {
    $order = orderWithSnapshot();
    app(OrderDeliveryService::class)->record($order, ['0' => 4], now()->toDateString(), null, null);

    $this->actingAs(userWithRole('Admin'))->patch(route('admin.order.update'), [
        'edit_id' => $order->id,
        'order_status' => 'delivered',
        'delivered_on' => now()->toDateString(),
        'qty' => ['0' => 6, '1-0' => 5, '1-1' => 3],
    ])->assertSessionHas('success');

    $order->refresh();
    expect($order->order_status)->toBe('delivered')
        ->and($order->payment_status)->toBe('pending') // delivery no longer marks the order paid; verified payments do
        ->and($order->deliveries()->count())->toBe(2);
});

test('order edit JSON and delivery details popup expose per-item quantities', function () {
    $order = orderWithSnapshot();
    app(OrderDeliveryService::class)->record($order, ['1-0' => 5], now()->toDateString(), 'van 1', null);
    $manager = userWithRole('Manager');

    $this->actingAs($manager)->getJson(route('admin.order.edit', $order->id))
        ->assertOk()->assertJsonPath('pending', 13)->assertJsonCount(3, 'lines');

    $this->actingAs($manager)->get(route('admin.order.delivery.details', $order->id))
        ->assertOk()->assertSeeText('5 / 18 delivered')->assertSee('13 pending')->assertSee('van 1')
        ->assertSee('Partial Pending');
});

test('read-only breakdown shows delivered and pending lines with history', function () {
    // The full salesman page needs the live carts schema (carts.variant_id is not in the migrations),
    // so render the shared partial it uses.
    $order = orderWithSnapshot();
    $service = app(OrderDeliveryService::class);
    $service->record($order, ['0' => 4], now()->toDateString(), 'first lot', null);
    $order->load('deliveries.items');

    $html = (string) $this->view('partials.order_delivery', [
        'order' => $order, 'deliveryLines' => $service->lines($order), 'editable' => false,
    ]);

    expect($html)->toContain('first lot')->toContain('Bulb')->toContain('Colour - Red')
        ->not->toContain('Record Delivery');
});
