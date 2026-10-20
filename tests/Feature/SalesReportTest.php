<?php

use App\Mail\TemplatedMail;
use App\Models\Order;
use App\Models\OrderPayment;
use App\Models\SalesmanTarget;
use App\Models\WebsiteSetting;
use App\Services\OrderPaymentService;
use App\Services\SalesReportService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

beforeEach(fn () => Carbon::setTestNow(Carbon::parse('2026-10-20 12:00', 'Asia/Kolkata')));
afterEach(fn () => Carbon::setTestNow());

/** Order placed at an Indian local time (stored in UTC like the app does). */
function placedAt(string $ist, array $attributes = []): Order
{
    $order = orderWithSnapshot($attributes);
    $order->forceFill(['created_at' => Carbon::parse($ist, 'Asia/Kolkata')->utc()])->save();

    return $order;
}

function payment(Order $order, float $amount, string $paidOn, string $status = 'verified'): OrderPayment
{
    return OrderPayment::create([
        'order_id' => $order->id, 'user_id' => $order->user_id, 'amount' => $amount,
        'paid_on' => $paidOn, 'mode' => 'cash', 'status' => $status,
    ]);
}

function rowFor($rows, int $userId): array
{
    return $rows->first(fn ($row) => $row['user']->id === $userId);
}

test('monthly sales use Indian dates and skip cancelled orders; collection uses verified payments by date received', function () {
    $salesman = userWithRole('Sales Executive');
    $base = ['user_id' => $salesman->id];

    $o1 = placedAt('2026-10-10 11:00', $base);
    placedAt('2026-10-31 23:30', $base);                                  // last evening of the month, still October
    placedAt('2026-10-01 00:30', $base);                                  // Sept 30 in UTC, October in India
    placedAt('2026-09-30 23:00', $base);                                  // September
    placedAt('2026-10-12 10:00', $base + ['order_status' => 'cancelled']);

    payment($o1, 400, '2026-10-31');
    payment($o1, 100, '2026-10-05', 'pending');
    payment($o1, 50, '2026-10-06', 'rejected');
    payment($o1, 200, '2026-09-30');

    $reports = app(SalesReportService::class);
    $row = rowFor($reports->summary($reports->period('month', '2026-10')), $salesman->id);

    expect($row['orders'])->toBe(3)
        ->and($row['sales'])->toBe(3000.0)
        ->and($row['collected'])->toBe(400.0)
        ->and($row['pending'])->toBe(100.0);
});

test('weeks run Monday to Sunday in Indian time', function () {
    $salesman = userWithRole('Sales Executive');
    placedAt('2026-10-18 23:00', ['user_id' => $salesman->id]);                          // Sunday night: previous week
    placedAt('2026-10-19 01:00', ['user_id' => $salesman->id, 'total_amount' => 250]);   // Monday, though still Sunday in UTC

    $reports = app(SalesReportService::class);
    $period = $reports->period('week', '2026-10-21');
    $row = rowFor($reports->summary($period), $salesman->id);

    expect($period['label'])->toBe('19 Oct – 25 Oct 2026')
        ->and($row['orders'])->toBe(1)
        ->and($row['sales'])->toBe(250.0);

    $weeks = $reports->weekly($salesman->id, $reports->period('month', '2026-10')['start']);
    expect(array_column($weeks, 'label'))->toBe(['01 Oct – 04 Oct', '05 Oct – 11 Oct', '12 Oct – 18 Oct', '19 Oct – 25 Oct', '26 Oct – 31 Oct'])
        ->and($weeks[2]['orders'])->toBe(1)
        ->and($weeks[3]['sales'])->toBe(250.0);
});

test('outstanding and overdue: credit due after credit days, COD due on delivery', function () {
    $salesman = userWithRole('Sales Executive');
    $base = ['user_id' => $salesman->id];

    $overdueCredit = placedAt('2026-10-01 10:00', $base + ['payment_type' => 'credit', 'credit_days' => 10, 'total_amount' => 1000]);
    payment($overdueCredit, 400, '2026-10-05');
    placedAt('2026-10-15 10:00', $base + ['payment_type' => 'credit', 'credit_days' => 30, 'total_amount' => 500]);
    $deliveredCod = placedAt('2026-10-16 10:00', $base + ['payment_type' => 'COD', 'total_amount' => 300]);
    DB::table('order_deliveries')->insert(['order_id' => $deliveredCod->id, 'delivered_on' => '2026-10-18', 'created_at' => now(), 'updated_at' => now()]);
    placedAt('2026-10-17 10:00', $base + ['payment_type' => 'COD', 'total_amount' => 200]);
    placedAt('2026-10-02 10:00', $base + ['payment_status' => 'paid', 'total_amount' => 900]);

    $dues = app(SalesReportService::class)->dues($salesman->id)[$salesman->id];

    expect($dues['outstanding'])->toBe(1600.0)
        ->and($dues['overdue'])->toBe(900.0);
});

test('a month override beats the standing target, and both are saved from the targets page', function () {
    $salesman = userWithRole('Sales Executive');
    $admin = userWithRole('Admin');

    $this->actingAs($admin)->post(route('admin.sales.targets.update'), [
        'month' => '2026-10',
        'default' => [$salesman->id => ['sales' => 10000, 'collection' => 8000]],
        'override' => [$salesman->id => ['sales' => 2000, 'collection' => null]],
    ])->assertRedirect(route('admin.sales.targets', ['month' => '2026-10']));

    placedAt('2026-10-10 10:00', ['user_id' => $salesman->id, 'total_amount' => 1000]);
    $reports = app(SalesReportService::class);

    $october = rowFor($reports->summary($reports->period('month', '2026-10')), $salesman->id);
    $november = rowFor($reports->summary($reports->period('month', '2026-11')), $salesman->id);

    expect($october['sales_target'])->toBe(2000.0)
        ->and($october['sales_pct'])->toBe(50.0)
        ->and($october['collection_target'])->toBeNull()
        ->and($november['sales_target'])->toBe(10000.0)
        ->and(SalesmanTarget::count())->toBe(2);

    // Clearing the override falls back to the standard target.
    $this->actingAs($admin)->post(route('admin.sales.targets.update'), [
        'month' => '2026-10',
        'override' => [$salesman->id => ['sales' => null, 'collection' => null]],
    ]);
    expect(SalesmanTarget::count())->toBe(1);
});

test('admin report pages render and CSV downloads; managers are refused', function () {
    $salesman = userWithRole('Sales Executive', ['name' => 'Ravi Kumar']);
    $order = placedAt('2026-10-10 10:00', ['user_id' => $salesman->id]);
    payment($order, 400, '2026-10-12');
    $admin = userWithRole('Admin');

    $this->actingAs($admin)->get(route('admin.sales.report'))->assertOk()->assertSee('Ravi Kumar')->assertSee('₹1,000.00');
    $this->actingAs($admin)->get(route('admin.sales.report', ['type' => 'week', 'date' => '2026-10-07']))->assertOk();
    $this->actingAs($admin)->get(route('admin.sales.salesman', [$salesman->id, 'date' => '2026-10']))->assertOk()->assertSee('Week by week');
    $this->actingAs($admin)->get(route('admin.sales.targets'))->assertOk()->assertSee('Ravi Kumar');

    $csv = $this->actingAs($admin)->get(route('admin.sales.export', ['type' => 'month', 'date' => '2026-10']));
    $csv->assertOk()->assertHeader('content-type', 'text/csv; charset=UTF-8');
    expect($csv->streamedContent())->toContain('"Ravi Kumar",1,1000,400,0,600,0');

    $detail = $this->actingAs($admin)->get(route('admin.sales.salesman.export', [$salesman->id, 'date' => '2026-10']));
    expect($detail->streamedContent())->toContain('"05 Oct – 11 Oct",1,1000,0,0')->toContain('"12 Oct – 18 Oct",0,0,400,0');

    $manager = userWithRole('Manager');
    $this->actingAs($manager)->get(route('admin.sales.report'))->assertForbidden();
    $this->actingAs($manager)->get(route('admin.sales.export'))->assertForbidden();
});

test('a logged payment emails the admin notification address', function () {
    Mail::fake();
    WebsiteSetting::setValue('admin_notification_email', 'owner@example.com', 'email');
    $salesman = userWithRole('Sales Executive');
    $order = orderWithSnapshot(['user_id' => $salesman->id]);

    $this->actingAs($salesman)->post(route('front.payments.store', $order->id), [
        'amount' => 250, 'paid_on' => '2026-10-20', 'mode' => 'cheque', 'reference' => 'CHQ-77',
    ]);

    Mail::assertSent(TemplatedMail::class, fn ($mail) => $mail->hasTo('owner@example.com'));
});

test('backfill gives each already-paid order one verified payment', function () {
    $paid = orderWithSnapshot(['payment_status' => 'paid', 'total_amount' => 750]);
    orderWithSnapshot(['payment_status' => 'paid', 'order_status' => 'cancelled']);
    orderWithSnapshot();

    $migration = require database_path('migrations/2026_10_21_100100_backfill_order_payments.php');
    $migration->up();
    $migration->up(); // safe to run twice

    $payment = OrderPayment::sole();
    expect($payment->order_id)->toBe($paid->id)
        ->and((float) $payment->amount)->toBe(750.0)
        ->and($payment->status)->toBe('verified')
        ->and($payment->is_backfill)->toBeTrue();

    app(OrderPaymentService::class)->syncOrderStatus($paid);
    expect($paid->fresh()->payment_status)->toBe('paid');
});
