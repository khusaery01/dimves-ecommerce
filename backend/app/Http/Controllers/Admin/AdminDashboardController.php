<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Promo;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Stat cards — hari ini
        $todayRevenue = Order::whereIn('status', ['Selesai', 'Diproses'])
            ->whereDate('created_at', today())
            ->sum('grand_total');

        $activeOrders = Order::whereIn('kitchen_status', ['waiting', 'preparing', 'ready'])->count();

        $availableMenus = Menu::where('status', true)->where('stock', '>', 0)->count();

        $activeVouchers = Promo::where('is_active', true)
            ->where('end_date', '>=', today())
            ->count();

        // Stat bulan ini
        $totalOrders = Order::whereIn('status', ['Selesai', 'Diproses'])
            ->whereMonth('created_at', now()->month)
            ->count();

        $monthRevenue = Order::whereIn('status', ['Selesai', 'Diproses'])
            ->whereMonth('created_at', now()->month)
            ->sum('grand_total');

        // Data grafik omzet 7 hari terakhir
        $chartData = Order::whereIn('status', ['Selesai', 'Diproses'])
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->groupBy('date')
            ->orderBy('date')
            ->get(DB::raw('DATE(created_at) as date, SUM(grand_total) as total'));

        $chartLabels = collect();
        $chartValues = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $found = $chartData->firstWhere('date', $date);
            $chartLabels->push(now()->subDays($i)->format('D, d M'));
            $chartValues->push($found ? (float) $found->total : 0);
        }

        // 5 pesanan terbaru
        $recentOrders = Order::with(['user', 'items'])->latest()->limit(5)->get();

        return view('admin.dashboard.index', compact(
            'todayRevenue',
            'activeOrders',
            'availableMenus',
            'activeVouchers',
            'totalOrders',
            'monthRevenue',
            'chartLabels',
            'chartValues',
            'recentOrders'
        ));
    }
}
