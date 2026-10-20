<?php

namespace App\Http\Controllers\Salesman;

use App\Http\Controllers\Controller;
use App\Models\OrderPayment;
use App\Models\SalesmanTarget;
use App\Services\SalesReportService;
use Illuminate\Http\Request;

/** Salesman: their own weekly / monthly sales, collection, targets and logged payments. */
class PerformanceController extends Controller
{
    public function index(Request $request, SalesReportService $reports)
    {
        $user = $request->user();
        $period = $reports->period('month', $request->input('date'));
        $weeks = $reports->weekly($user->id, $period['start']);
        [$from, $to] = $reports->dateBounds($period);

        $totals = [
            'orders' => array_sum(array_column($weeks, 'orders')),
            'sales' => array_sum(array_column($weeks, 'sales')),
            'collected' => array_sum(array_column($weeks, 'collected')),
            'pending' => array_sum(array_column($weeks, 'pending')),
        ];

        $status = $request->input('status');
        $payments = OrderPayment::query()
            ->with('order:id,shop_name')
            ->where('user_id', $user->id)
            ->where('paid_on', '>=', $from)->where('paid_on', '<', $to)
            ->when(in_array($status, ['pending', 'verified', 'rejected', 'reversed'], true), fn ($q) => $q->where('status', $status))
            ->orderByDesc('paid_on')->orderByDesc('id')
            ->get();

        return view('front.performance', [
            'period' => $period,
            'weeks' => $weeks,
            'totals' => $totals,
            'payments' => $payments,
            'status' => $status,
            'dues' => $reports->dues($user->id)[$user->id] ?? ['outstanding' => 0.0, 'overdue' => 0.0],
            'target' => SalesmanTarget::forMonth($user->id, $period['start']),
        ]);
    }
}
