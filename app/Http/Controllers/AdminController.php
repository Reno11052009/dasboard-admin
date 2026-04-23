<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Notifications\ProductActionNotification;
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

        return view('index', compact('userCount', 'productCount', 'orderCount', 'totalSales', 'chartData', 'period', 'startDate', 'endDate', 'topSellingProducts', 'lowStockProducts', 'recentSales', 'productSalesData'));
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

        // return view('sales', compact('totalSales', 'totalOrders', 'completedOrders', 'pendingOrders', 'monthlySales', 'topProducts', 'recentOrders'));
    }

    public function users(Request $request)
    {
        $search = $request->get('search');
        
        $query = User::query();
        
        if ($search) {
            $query->where('name', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%");
        }
        
        $users = $query->latest()->get();
        return view('user.users', compact('users'));
    }

    public function products(Request $request)
    {
        $search = $request->get('search');
        
        $query = \App\Models\Product::query();
        
        if ($search) {
            $query->where('name', 'like', "%$search%")
                ->orWhere('category', 'like', "%$search%");
        }
        
        $products = $query->latest()->get();
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

        $user->notify(new ProductActionNotification('create', $product->name));

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

        $user->notify(new ProductActionNotification('update', $product->name));

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

    public function createUser()
    {
        return view('user.create');
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
        ]);

        return redirect()->route('users')->with('success', 'User created successfully.');
    }

    public function editUser($id)
    {
        $user = User::findOrFail($id);
        return view('user.edit', compact('user'));
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:8|confirmed',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->password) {
            $user->password = $request->password;
        }

        $user->save();

        return redirect()->route('users')->with('success', 'User updated successfully.');
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('users')->with('success', 'User deleted successfully.');
    }

    public function orders(Request $request)
    {
        $status = $request->get('status');
        $search = $request->get('search');

        $query = Order::with('user', 'product');

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%$search%");
            })->orWhereHas('product', function ($q) use ($search) {
                $q->where('name', 'like', "%$search%");
            });
        }

        $orders = $query->latest()->get();
        $statusCounts = Order::selectRaw('status, COUNT(*) as count')->groupBy('status')->get()->keyBy('status');

        return view('order.orders', compact('orders', 'statusCounts'));
    }

    public function editOrder($id)
    {
        $order = Order::with('user', 'product')->findOrFail($id);
        return view('order.edit', compact('order'));
    }

    public function updateOrder(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled',
        ]);

        $order = Order::findOrFail($id);
        $order->status = $request->status;
        $order->save();

        return redirect()->route('orders')->with('success', 'Order status updated successfully.');
    }

    public function deleteOrder($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();

        return redirect()->route('orders')->with('success', 'Order deleted successfully.');
    }

    public function logout(Request $request)
    {
        $request->session()->flush();
        $request->session()->regenerate();
        
        return redirect('/')->with('success', 'Logged out successfully.');
    }
}
