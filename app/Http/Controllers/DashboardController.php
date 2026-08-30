<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\VariantAttribute;
use App\Models\Enquiry;
use App\Models\EnquiryStatus;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProducts  = Product::count();
        $totalOrders    = Order::count();

        $totalCustomers = Order::groupBy('owner_phone')->count();
        $totalSales     = Order::where('order_status', 'completed')->sum('total_amount');

        $totalEnquiries = Enquiry::count();
        $todayEnquiries = Enquiry::whereDate('created_at', today())->count();
        $weekEnquiries = Enquiry::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
        $monthEnquiries = Enquiry::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();

        $enquiryStatusCounts = EnquiryStatus::query()
            ->withCount('enquiries')
            ->orderBy('sort_order')
            ->get();

        // Low stock: items where stock < min_stock (min_stock > 0 so we only alert when threshold is set)
        $lowStockSimpleProducts = Product::where('product_type', 1)
            ->where('min_stock', '>', 0)
            ->whereColumn('stock', '<', 'min_stock')
            ->orderBy('stock')
            ->get();

        $lowStockVariantAttributes = VariantAttribute::with([
            'variant.product',
            'variant.variantname',
            'option',
        ])
            ->where('min_stock', '>', 0)
            ->whereColumn('stock', '<', 'min_stock')
            ->whereHas('variant.product')
            ->orderBy('stock')
            ->get();

        return view('admin.pages.dashboard', compact(
            'totalProducts',
            'totalOrders',
            'totalCustomers',
            'totalSales',
            'lowStockSimpleProducts',
            'lowStockVariantAttributes',
            'totalEnquiries',
            'todayEnquiries',
            'weekEnquiries',
            'monthEnquiries',
            'enquiryStatusCounts'
        ));
    }
}
