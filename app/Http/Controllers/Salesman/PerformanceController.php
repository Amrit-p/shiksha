<?php

namespace App\Http\Controllers\Salesman;

use App\Http\Controllers\Controller;
use App\Services\SalesReportService;
use Illuminate\Http\Request;

/** Salesman: a basic report of their own monthly and weekly sales and the pieces they sold. */
class PerformanceController extends Controller
{
    public function index(Request $request, SalesReportService $reports)
    {
        $period = $reports->period('month', $request->input('date'));
        $weeks = $reports->weekly($request->user()->id, $period['start']);

        return view('front.performance', [
            'period' => $period,
            'weeks' => $weeks,
            'totals' => [
                'orders' => array_sum(array_column($weeks, 'orders')),
                'pieces' => array_sum(array_column($weeks, 'pieces')),
                'sales' => array_sum(array_column($weeks, 'sales')),
            ],
        ]);
    }
}
