<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());

        // Omzet total pesanan Selesai / Diproses
        $totalRevenue = Order::whereIn('status', ['Selesai', 'Diproses'])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->sum('grand_total');

        $totalOrders = Order::whereIn('status', ['Selesai', 'Diproses'])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->count();

        // Top 5 Menu Terlaris
        $topMenus = OrderItem::select('menu_id', DB::raw('SUM(quantity) as total_sold'), DB::raw('SUM(subtotal) as total_amount'))
            ->whereHas('order', function ($q) use ($startDate, $endDate) {
                $q->whereIn('status', ['Selesai', 'Diproses'])
                  ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            })
            ->with('menu')
            ->groupBy('menu_id')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        return view('admin.reports.index', compact('totalRevenue', 'totalOrders', 'topMenus', 'startDate', 'endDate'));
    }
}
