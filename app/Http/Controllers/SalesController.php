<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Carbon\Carbon;

class SalesController extends Controller
{
    public function index()
    {
        if (!auth()->check()) {
            return redirect('/')->with('error', 'Silakan login terlebih dahulu');
        }
        
        $totalSales = Order::where('status', 'completed')->sum('total_price');
        $totalOrders = Order::count();
        $completedOrders = Order::where('status', 'completed')->count();
        $pendingOrders = Order::where('status', 'pending')->count();

        $monthlySales = Order::selectRaw('MONTH(order_date) as month, SUM(total_price) as total, COUNT(*) as count')
            ->where('status', 'completed')
            ->whereYear('order_date', Carbon::now()->year)
            ->groupByRaw('MONTH(order_date)')
            ->get();

        $topProducts = Order::with('product')
            ->where('status', 'completed')
            ->selectRaw('product_id, SUM(quantity) as total_qty, SUM(total_price) as total')
            ->groupBy('product_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $recentOrders = Order::with('user', 'product')
            ->latest()
            ->limit(10)
            ->get();

        return view('sales', compact('totalSales', 'totalOrders', 'completedOrders', 'pendingOrders', 'monthlySales', 'topProducts', 'recentOrders'));
    }
}