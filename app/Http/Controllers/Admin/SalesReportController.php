<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderPayment;
use App\Models\User;
use App\Services\SalesReportService;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

/** Super admin: weekly / monthly sales and collection per salesman. */
class SalesReportController extends Controller
{
    public function __construct(protected SalesReportService $reports) {}

    public function index(Request $request)
    {
        $period = $this->reports->period((string) $request->input('type', 'month'), $request->input('date'));
        $rows = $this->reports->summary($period);
        $totals = $this->reports->totals($rows);
        $pendingCount = OrderPayment::query()->pending()->count();

        return view('admin.sales_report.index', compact('period', 'rows', 'totals', 'pendingCount'));
    }

    /** Drill-down for one salesman and one month. */
    public function show(Request $request, User $user)
    {
        $data = $this->salesmanData($user, $request->input('date'));

        return view('admin.sales_report.show', $data + ['salesman' => $user]);
    }

    public function export(Request $request): StreamedResponse
    {
        $period = $this->reports->period((string) $request->input('type', 'month'), $request->input('date'));
        $rows = $this->reports->summary($period);

        $header = ['Salesman', 'Orders', 'Sales'];

        $lines = $rows->map(fn (array $row) => [$row['user']->name, $row['orders'], $row['sales']]);

        $name = 'sales-collection-'.$period['type'].'-'.$period['start']->toDateString().'.csv';

        return $this->csv($name, [['Sales & collection: '.$period['label']], $header, ...$lines]);
    }

    public function exportSalesman(Request $request, User $user): StreamedResponse
    {
        $data = $this->salesmanData($user, $request->input('date'));

        $lines = [[$user->name.' – '.$data['period']['label']], ['Week', 'Orders', 'Sales']];
        foreach ($data['weeks'] as $week) {
            $lines[] = [$week['label'], $week['orders'], $week['sales']];
        }

        $lines[] = [];
        $lines[] = ['Orders'];
        $lines[] = ['Order #', 'Date', 'Shop', 'Payment type', 'Total'];
        foreach ($data['orders'] as $order) {
            $lines[] = [$order->id, $this->istDate($order->created_at), $order->shop_name, $order->payment_type, $order->total_amount];
        }

        return $this->csv('salesman-'.$user->id.'-'.$data['period']['start']->format('Y-m').'.csv', $lines);
    }

    protected function salesmanData(User $user, ?string $date): array
    {
        $period = $this->reports->period('month', $date);
        $weeks = $this->reports->weekly($user->id, $period['start']);
        $orders = $this->reports->orders($user->id, $period);

        $totals = [
            'orders' => array_sum(array_column($weeks, 'orders')),
            'sales' => array_sum(array_column($weeks, 'sales')),
        ];

        return compact('period', 'weeks', 'orders', 'totals');
    }

    protected function csv(string $filename, iterable $lines): StreamedResponse
    {
        return response()->streamDownload(function () use ($lines) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // UTF-8 BOM so Excel shows ₹ / – correctly
            foreach ($lines as $line) {
                fputcsv($out, $line);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    protected function istDate($value): string
    {
        return CarbonImmutable::parse($value, 'UTC')->setTimezone(SalesReportService::TZ)->format('Y-m-d');
    }
}
