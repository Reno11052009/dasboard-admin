<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->check()) {
            return redirect('/')->with('error', 'Silakan login terlebih dahulu');
        }

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

        return view('admin.order.orders', compact('orders', 'statusCounts'));
    }

    public function edit($id)
    {
        if (!auth()->check()) {
            return redirect('/')->with('error', 'Silakan login terlebih dahulu');
        }

        $order = Order::with('user', 'product')->findOrFail($id);
        return view('admin.order.edit', compact('order'));
    }

    public function update(Request $request, $id)
    {
        if (!auth()->check()) {
            return redirect('/')->with('error', 'Silakan login terlebih dahulu');
        }

        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled',
        ]);

        $order = Order::findOrFail($id);
        $order->status = $request->status;
        $order->save();

        return redirect()->route('orders')->with('success', 'Order status updated successfully.');
    }

    public function destroy($id)
    {
        if (!auth()->check()) {
            return redirect('/')->with('error', 'Silakan login terlebih dahulu');
        }

        $order = Order::findOrFail($id);
        $order->delete();

        return redirect()->route('orders')->with('success', 'Order deleted successfully.');
    }
}
