<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->check()) {
            return redirect('/')->with('error', 'Silakan login terlebih dahulu');
        }

        $period = $request->get('period', 'monthly');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $userCount = User::count();
        $productCount = Product::count();
        $orderCount = Order::count();

        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();

        $currentMonthSales = Order::where('order_date', '>=', $thisMonth)->where('status', 'completed')->sum('total_price');
        $lastMonthSales = Order::whereBetween('order_date', [$lastMonth, $thisMonth->subDay()])->where('status', 'completed')->sum('total_price');

        $salesChange = 0;
        if ($lastMonthSales > 0) {
            $salesChange = round(($currentMonthSales - $lastMonthSales) / $lastMonthSales * 100, 1);
        }

        $currentMonthOrders = Order::where('order_date', '>=', $thisMonth)->count();
        $lastMonthOrders = Order::whereBetween('order_date', [$lastMonth, $thisMonth->subDay()])->count();

        $ordersChange = 0;
        if ($lastMonthOrders > 0) {
            $ordersChange = round(($currentMonthOrders - $lastMonthOrders) / $lastMonthOrders * 100, 1);
        }

        $query = Order::where('status', 'completed');

        if ($startDate && $endDate) {
            $query->whereBetween('order_date', [$startDate, $endDate]);
            $totalSales = $query->sum('total_price');

            $start = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);
            $months = $start->diffInMonths($end);

            $salesData = Order::selectRaw('MONTH(order_date) as month, SUM(total_price) as total')
                ->whereBetween('order_date', [$startDate, $endDate])
                ->where('status', 'completed')
                ->groupBy('month')
                ->get();

            $labels = [];
            $data = [];
            for ($i = $months; $i >= 0; $i--) {
                $month = $end->copy()->subMonths($i);
                $monthNum = (int) $month->format('n');
                $labels[] = strtolower($month->format('M'));
                $order = $salesData->firstWhere('month', $monthNum);
                $data[] = $order ? (float) $order->total : 0;
            }
        } elseif ($period === 'yearly') {
            $totalSales = $query->sum('total_price');

            $salesData = Order::selectRaw('YEAR(order_date) as year, SUM(total_price) as total')
                ->where('status', 'completed')
                ->groupBy('year')
                ->get();

            $currentYear = Carbon::now()->year;
            $labels = [];
            $data = [];
            for ($i = 4; $i >= 0; $i--) {
                $year = $currentYear - $i;
                $labels[] = (string) $year;
                $order = $salesData->firstWhere('year', $year);
                $data[] = $order ? (float) $order->total : 0;
            }
        } else {
            $sixMonthsAgo = Carbon::now()->subMonths(5);
            $query = Order::where('order_date', '>=', $sixMonthsAgo)->where('status', 'completed');
            $totalSales = $query->sum('total_price');

            $salesData = Order::selectRaw('MONTH(order_date) as month, SUM(total_price) as total')
                ->where('order_date', '>=', $sixMonthsAgo)
                ->where('status', 'completed')
                ->groupBy('month')
                ->get();

            $labels = [];
            $data = [];
            for ($i = 5; $i >= 0; $i--) {
                $month = Carbon::now()->subMonths($i);
                $monthNum = (int) $month->format('n');
                $monthName = $month->format('M');
                $labels[] = strtolower($monthName);
                $order = $salesData->firstWhere('month', $monthNum);
                $data[] = $order ? (float) $order->total : 0;
            }
        }

        $chartData = [
            'labels' => $labels,
            'data' => $data,
        ];

        $topSellingProducts = Order::with('product')
            ->where('status', 'completed')
            ->selectRaw('product_id, SUM(quantity) as total_qty, SUM(total_price) as total')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        $lowStockProducts = Product::orderBy('stok', 'asc')->limit(5)->get();

        $recentSales = Order::with('user', 'product')
            ->where('status', 'completed')
            ->latest()
            ->limit(5)
            ->get();

        $productSales = Order::with('product')
            ->where('status', 'completed')
            ->selectRaw('product_id, SUM(quantity) as total_qty')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        $totalQty = $productSales->sum('total_qty');

        $productSalesData = [
            'labels' => $productSales->pluck('product.name')->toArray(),
            'data' => $productSales->pluck('total_qty')->toArray(),
            'percentages' => $productSales->map(function($item) use ($totalQty) {
                return $totalQty > 0 ? round($item->total_qty / $totalQty * 100, 1) : 0;
            })->toArray(),
        ];

        return view('admin.index', compact('userCount', 'productCount', 'orderCount', 'totalSales', 'chartData', 'period', 'startDate', 'endDate', 'topSellingProducts', 'lowStockProducts', 'recentSales', 'productSalesData', 'salesChange', 'ordersChange'));
    }
}
