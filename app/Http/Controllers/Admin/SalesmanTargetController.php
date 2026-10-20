<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalesmanTarget;
use App\Models\User;
use App\Services\SalesReportService;
use Illuminate\Http\Request;

/** Super admin: standing monthly targets per salesman, with optional overrides for one month. */
class SalesmanTargetController extends Controller
{
    public function index(Request $request, SalesReportService $reports)
    {
        $period = $reports->period('month', $request->input('month'));
        $month = $period['start']->toDateString();

        $salesmen = User::query()
            ->whereIn('id', $reports->summary($period)->pluck('user.id'))
            ->orderBy('name')
            ->get(['id', 'name']);

        $targets = SalesmanTarget::query()
            ->whereIn('user_id', $salesmen->pluck('id'))
            ->where(fn ($q) => $q->whereNull('month')->orWhereDate('month', $month))
            ->get();

        $defaults = $targets->whereNull('month')->keyBy('user_id');
        $overrides = $targets->whereNotNull('month')->keyBy('user_id');

        return view('admin.sales_report.targets', compact('period', 'salesmen', 'defaults', 'overrides'));
    }

    public function update(Request $request, SalesReportService $reports)
    {
        $data = $request->validate([
            'month' => 'required|date_format:Y-m',
            'default' => 'array',
            'default.*.sales' => 'nullable|numeric|min:0',
            'default.*.collection' => 'nullable|numeric|min:0',
            'override' => 'array',
            'override.*.sales' => 'nullable|numeric|min:0',
            'override.*.collection' => 'nullable|numeric|min:0',
        ]);

        $month = $reports->period('month', $data['month'])['start']->toDateString();

        foreach (['default' => null, 'override' => $month] as $key => $monthValue) {
            foreach ($data[$key] ?? [] as $userId => $values) {
                $sales = $values['sales'] ?? null;
                $collection = $values['collection'] ?? null;
                $existing = SalesmanTarget::query()->where('user_id', (int) $userId)
                    ->when($monthValue, fn ($q) => $q->whereDate('month', $monthValue), fn ($q) => $q->whereNull('month'))
                    ->first();

                if ($sales === null && $collection === null) {
                    $existing?->delete();

                    continue;
                }

                ($existing ?? new SalesmanTarget(['user_id' => (int) $userId, 'month' => $monthValue]))
                    ->fill(['sales_target' => $sales, 'collection_target' => $collection])
                    ->save();
            }
        }

        return redirect()->route('admin.sales.targets', ['month' => $data['month']])->with('success', 'Targets saved.');
    }
}
