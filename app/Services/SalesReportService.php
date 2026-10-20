<?php

namespace App\Services;

use App\Models\OrderPayment;
use App\Models\SalesmanTarget;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Salesman sales / collection figures.
 *
 * - Sales: non-cancelled orders by created_at (orders.total_amount, i.e. after coupon discount).
 * - Collection: verified payments by paid_on; pending ones are reported separately.
 * - Weeks run Monday to Sunday. Periods are Indian calendar days: created_at is stored in UTC,
 *   so period bounds are converted from Asia/Kolkata before querying it. paid_on is a plain date.
 */
class SalesReportService
{
    public const TZ = 'Asia/Kolkata';

    /**
     * Period for a month (?date=2026-10) or week (?date=any day in it).
     *
     * @return array{type: string, start: CarbonImmutable, end: CarbonImmutable, label: string, prev: string, next: string, is_current: bool}
     */
    public function period(string $type, ?string $date = null): array
    {
        $type = $type === 'week' ? 'week' : 'month';

        try {
            $anchor = $date ? CarbonImmutable::parse($date, self::TZ) : $this->today();
        } catch (\Throwable) {
            $anchor = $this->today();
        }

        if ($type === 'week') {
            $start = $anchor->startOfWeek(CarbonImmutable::MONDAY)->startOfDay();
            $end = $start->addDays(6)->endOfDay();
            $label = $start->format('d M').' – '.$end->format('d M Y');
            $prev = $start->subWeek()->toDateString();
            $next = $start->addWeek()->toDateString();
        } else {
            $start = $anchor->startOfMonth()->startOfDay();
            $end = $anchor->endOfMonth()->endOfDay();
            $label = $start->format('F Y');
            $prev = $start->subMonth()->format('Y-m');
            $next = $start->addMonth()->format('Y-m');
        }

        $today = $this->today();

        return [
            'type' => $type,
            'start' => $start,
            'end' => $end,
            'label' => $label,
            'prev' => $prev,
            'next' => $next,
            'is_current' => $today->betweenIncluded($start, $end),
        ];
    }

    /** One row per salesman for the period, plus outstanding / overdue as of today and month targets. */
    public function summary(array $period): Collection
    {
        [$from, $to] = $this->utcBounds($period);
        [$fromDate, $toDate] = $this->dateBounds($period);

        $sales = $this->ordersQuery()
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('user_id')
            ->selectRaw('user_id, COUNT(*) as orders, COALESCE(SUM(total_amount), 0) as sales')
            ->get()->keyBy('user_id');

        $payments = DB::table('order_payments')
            ->whereIn('status', [OrderPayment::STATUS_VERIFIED, OrderPayment::STATUS_PENDING])
            ->where('paid_on', '>=', $fromDate)->where('paid_on', '<', $toDate)
            ->groupBy('user_id')
            ->selectRaw("user_id,
                COALESCE(SUM(CASE WHEN status = 'verified' THEN amount ELSE 0 END), 0) as collected,
                COALESCE(SUM(CASE WHEN status = 'pending' THEN amount ELSE 0 END), 0) as pending")
            ->get()->keyBy('user_id');

        $dues = $this->dues();

        $ids = collect($this->salesmanIds())
            ->merge($sales->keys())->merge($payments->keys())->merge(array_keys($dues))
            ->unique()->values()->all();

        $targets = $period['type'] === 'month' ? SalesmanTarget::mapForMonth($ids, $period['start']) : [];

        return User::query()->whereIn('id', $ids)->orderBy('name')->get(['id', 'name', 'email', 'mobile'])
            ->map(function (User $user) use ($sales, $payments, $dues, $targets) {
                $row = [
                    'user' => $user,
                    'orders' => (int) ($sales[$user->id]->orders ?? 0),
                    'sales' => (float) ($sales[$user->id]->sales ?? 0),
                    'collected' => (float) ($payments[$user->id]->collected ?? 0),
                    'pending' => (float) ($payments[$user->id]->pending ?? 0),
                    'outstanding' => $dues[$user->id]['outstanding'] ?? 0.0,
                    'overdue' => $dues[$user->id]['overdue'] ?? 0.0,
                    'sales_target' => ((float) ($targets[$user->id]->sales_target ?? 0)) ?: null,
                    'collection_target' => ((float) ($targets[$user->id]->collection_target ?? 0)) ?: null,
                ];

                $row['sales_pct'] = $this->percent($row['sales'], $row['sales_target']);
                $row['collection_pct'] = $this->percent($row['collected'], $row['collection_target']);

                return $row;
            })
            ->values();
    }

    /** Totals row for a summary() result. */
    public function totals(Collection $rows): array
    {
        return collect(['orders', 'sales', 'collected', 'pending', 'outstanding', 'overdue', 'sales_target', 'collection_target'])
            ->mapWithKeys(fn ($key) => [$key => $rows->sum($key)])
            ->all();
    }

    /** Monday–Sunday buckets of one month for one salesman (weeks clipped to the month). */
    public function weekly(int $userId, CarbonImmutable $month): array
    {
        $period = $this->period('month', $month->format('Y-m'));
        [$from, $to] = $this->utcBounds($period);

        $weeks = [];
        $cursor = $period['start'];
        while ($cursor->lte($period['end'])) {
            $weekEnd = $cursor->endOfWeek(CarbonImmutable::SUNDAY)->min($period['end'])->endOfDay();
            $weeks[] = [
                'start' => $cursor,
                'end' => $weekEnd,
                'label' => $cursor->format('d M').' – '.$weekEnd->format('d M'),
                'orders' => 0,
                'sales' => 0.0,
                'collected' => 0.0,
                'pending' => 0.0,
            ];
            $cursor = $weekEnd->addDay()->startOfDay();
        }

        $bucket = function (CarbonImmutable $day) use (&$weeks): ?int {
            foreach ($weeks as $i => $week) {
                if ($day->betweenIncluded($week['start'], $week['end'])) {
                    return $i;
                }
            }

            return null;
        };

        $this->ordersQuery()->where('user_id', $userId)->whereBetween('created_at', [$from, $to])
            ->get(['created_at', 'total_amount'])
            ->each(function ($order) use (&$weeks, $bucket) {
                $i = $bucket(CarbonImmutable::parse($order->created_at, 'UTC')->setTimezone(self::TZ));
                if ($i !== null) {
                    $weeks[$i]['orders']++;
                    $weeks[$i]['sales'] += (float) $order->total_amount;
                }
            });

        DB::table('order_payments')->where('user_id', $userId)
            ->whereIn('status', [OrderPayment::STATUS_VERIFIED, OrderPayment::STATUS_PENDING])
            ->where('paid_on', '>=', $this->dateBounds($period)[0])->where('paid_on', '<', $this->dateBounds($period)[1])
            ->get(['paid_on', 'amount', 'status'])
            ->each(function ($payment) use (&$weeks, $bucket) {
                $i = $bucket(CarbonImmutable::parse(substr((string) $payment->paid_on, 0, 10), self::TZ));
                if ($i !== null) {
                    $weeks[$i][$payment->status === OrderPayment::STATUS_VERIFIED ? 'collected' : 'pending'] += (float) $payment->amount;
                }
            });

        return $weeks;
    }

    /** Non-cancelled orders of one salesman placed in the period, with verified amount received. */
    public function orders(int $userId, array $period): Collection
    {
        [$from, $to] = $this->utcBounds($period);

        return $this->ordersQuery()->where('user_id', $userId)->whereBetween('created_at', [$from, $to])
            ->select('id', 'shop_name', 'owner_name', 'total_amount', 'payment_type', 'payment_status', 'order_status', 'created_at')
            ->selectSub($this->verifiedSumSql(), 'verified_paid')
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Outstanding (unverified balance) and overdue amounts per salesman, as of today.
     * Credit orders are due created_at + credit_days; COD orders are due on their latest delivery date.
     *
     * @return array<int, array{outstanding: float, overdue: float}>
     */
    public function dues(?int $userId = null): array
    {
        $today = $this->today()->toDateString();

        $rows = $this->ordersQuery()
            ->where(fn ($q) => $q->whereNull('payment_status')->orWhere('payment_status', '!=', 'paid'))
            ->when($userId, fn ($q) => $q->where('user_id', $userId))
            ->select('id', 'user_id', 'total_amount', 'payment_type', 'credit_days', 'created_at')
            ->selectSub($this->verifiedSumSql(), 'verified_paid')
            ->selectSub(DB::table('order_deliveries')->selectRaw('MAX(delivered_on)')->whereColumn('order_deliveries.order_id', 'orders.id'), 'last_delivered_on')
            ->get();

        $dues = [];
        foreach ($rows as $row) {
            $balance = round((float) $row->total_amount - (float) $row->verified_paid, 2);
            if ($balance <= 0) {
                continue;
            }

            $dueOn = $this->dueDate($row);
            $dues[$row->user_id]['outstanding'] = ($dues[$row->user_id]['outstanding'] ?? 0) + $balance;
            $dues[$row->user_id]['overdue'] = ($dues[$row->user_id]['overdue'] ?? 0)
                + ($dueOn !== null && $today > $dueOn ? $balance : 0);
        }

        return $dues;
    }

    /** Y-m-d the order's payment falls due, or null when it isn't due yet (undelivered COD). */
    public function dueDate(object $order): ?string
    {
        if (strtolower((string) $order->payment_type) === 'credit') {
            return CarbonImmutable::parse($order->created_at, 'UTC')->setTimezone(self::TZ)
                ->addDays((int) $order->credit_days)->toDateString();
        }

        return $order->last_delivered_on ? substr((string) $order->last_delivered_on, 0, 10) : null;
    }

    public function today(): CarbonImmutable
    {
        return CarbonImmutable::now(self::TZ);
    }

    /** UTC datetime strings matching the period's Indian calendar bounds (for created_at). */
    protected function utcBounds(array $period): array
    {
        return [
            $period['start']->setTimezone('UTC')->toDateTimeString(),
            $period['end']->setTimezone('UTC')->toDateTimeString(),
        ];
    }

    /**
     * [first day, day after last day] for paid_on, used as >= / <. A half-open range matches whether
     * the date column holds "Y-m-d" or "Y-m-d 00:00:00" (how SQLite stores a date-cast value).
     */
    public function dateBounds(array $period): array
    {
        return [$period['start']->toDateString(), $period['end']->addDay()->toDateString()];
    }

    protected function ordersQuery()
    {
        return DB::table('orders')->where(fn ($q) => $q->whereNull('order_status')->orWhere('order_status', '!=', 'cancelled'));
    }

    protected function verifiedSumSql()
    {
        return DB::table('order_payments')
            ->selectRaw('COALESCE(SUM(amount), 0)')
            ->whereColumn('order_payments.order_id', 'orders.id')
            ->where('order_payments.status', OrderPayment::STATUS_VERIFIED);
    }

    /** Users who can place orders (frontend_shop), so salesmen with no activity still show up. */
    protected function salesmanIds(): array
    {
        try {
            return User::permission('frontend_shop')->pluck('id')->all();
        } catch (\Throwable) {
            return [];
        }
    }

    protected function percent(float $actual, ?float $target): ?float
    {
        return $target ? round($actual / $target * 100, 1) : null;
    }
}
