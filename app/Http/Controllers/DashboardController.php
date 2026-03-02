<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penjualan;
use App\Models\Product;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();

        // Statistics
        $salesToday = Penjualan::whereDate('created_at', $today)->sum('total');
        $transactionsToday = Penjualan::whereDate('created_at', $today)->count();
        $salesMonth = Penjualan::whereDate('created_at', '>=', $startOfMonth)->sum('total');
        $transactionsMonth = Penjualan::whereDate('created_at', '>=', $startOfMonth)->count();

        // Recent Transactions
        $recentTransactions = Penjualan::with('user')->latest()->take(5)->get();

        // Low Stock Products
        $lowStockProducts = Product::whereColumn('stock', '<=', 'stock_min')->take(5)->get();

        // Recent Logins
        $recentLogins = \App\Models\LoginLog::with('user')->latest()->take(10)->get();

        // Chart Data: Sales Last 7 Days
        $chartLabels = [];
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $chartLabels[] = $date->format('d M');
            $chartData[] = Penjualan::whereDate('created_at', $date)->sum('total');
        }

        // Top 5 Products
        $topProductsChart = \App\Models\PenjualanDetail::select('product_id', \DB::raw('SUM(qty_jual) as total_qty'))
            ->whereHas('penjualan', function ($q) {
                // To ensure global store scope applies, we join via penjualan
            })
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->take(5)
            ->with('product')
            ->get();
            
        $topProductsLabels = [];
        $topProductsData = [];
        foreach ($topProductsChart as $tp) {
            $topProductsLabels[] = rtrim(mb_strimwidth($tp->product->product_name ?? 'Unknown', 0, 15, '...'));
            $topProductsData[] = $tp->total_qty;
        }

        $subscriptionDaysLeft = null;
        $qrisBalance = 0;
        if (auth()->user()->role === 'owner') {
            $store = auth()->user()->store;
            if ($store) {
                if ($store->subscription_until) {
                    $subscriptionDaysLeft = (int) \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($store->subscription_until), false);
                } else {
                    $subscriptionDaysLeft = -1;
                }
                $qrisBalance = $store->qris_balance;
            }
        }

        return view('dashboard.index', compact(
            'salesToday', 
            'transactionsToday', 
            'salesMonth', 
            'transactionsMonth',
            'recentTransactions',
            'lowStockProducts',
            'recentLogins',
            'chartLabels',
            'chartData',
            'topProductsLabels',
            'topProductsData',
            'subscriptionDaysLeft',
            'qrisBalance'
        ));
    }
}
