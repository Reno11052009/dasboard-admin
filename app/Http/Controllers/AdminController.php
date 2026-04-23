<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 'monthly');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $userCount = User::count();
        $productCount = Product::count();
        $orderCount = Order::count();

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

        return view('index', compact('userCount', 'productCount', 'orderCount', 'totalSales', 'chartData', 'period', 'startDate', 'endDate'));
    }

    public function sales()
    {
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

    public function users()
    {
        $users = User::latest()->get();
        return view('users', compact('users'));
    }

    public function products()
    {
        $products = \App\Models\Product::latest()->get();
        return view('product.product', compact('products'));
    }

    public function storeProduct(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'stok' => 'required|integer',
            'price' => 'required|numeric',
            'image' => 'nullable|image|max:2048',
            'category' => 'nullable|string|max:255',
        ]);

        $product = new \App\Models\Product();
        $product->name = $request->name;
        $product->description = $request->description;
        $product->stok = $request->stok;
        $product->price = $request->price;
        $product->category = $request->category;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            $product->image = $imagePath;
        }

        $product->save();

        return redirect()->route('products')->with('success', 'Product created successfully.');
    }

    public function editProduct($id)
    {
        $product = \App\Models\Product::findOrFail($id);
        return view('product.edit', compact('product'));
    }

    public function updateProduct(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'stok' => 'required|integer',
            'price' => 'required|numeric',
            'image' => 'nullable|image|max:2048',
            'category' => 'nullable|string|max:255',
        ]);

        $product = \App\Models\Product::findOrFail($id);
        $product->name = $request->name;
        $product->description = $request->description;
        $product->stok = $request->stok;
        $product->price = $request->price;
        $product->category = $request->category;

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $imagePath = $request->file('image')->store('products', 'public');
            $product->image = $imagePath;
        }

        $product->save();

        return redirect()->route('products')->with('success', 'Product updated successfully.');
    }

    public function deleteProduct($id)
    {
        $product = \App\Models\Product::findOrFail($id);
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        $product->delete();

        return redirect()->route('products')->with('success', 'Product deleted successfully.');
    }
}
