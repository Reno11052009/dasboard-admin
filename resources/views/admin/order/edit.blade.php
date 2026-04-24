@extends('components.layout')

@section('header', 'Edit Order')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('orders') }}" class="text-sm text-indigo-600 hover:underline flex items-center gap-1">
            ← Kembali ke Daftar Pesanan
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-8">
            <form action="{{ route('orders.update', $order->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Order ID</label>
                        <div class="px-4 py-3 bg-gray-50 rounded-xl text-gray-600">#{{ $order->id }}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Order Date</label>
                        <div class="px-4 py-3 bg-gray-50 rounded-xl text-gray-600">{{ \Carbon\Carbon::parse($order->order_date)->format('d M Y') }}</div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Customer</label>
                        <div class="px-4 py-3 bg-gray-50 rounded-xl text-gray-600">{{ $order->user->name }}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                        <div class="px-4 py-3 bg-gray-50 rounded-xl text-gray-600">{{ $order->user->email }}</div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Product</label>
                        <div class="px-4 py-3 bg-gray-50 rounded-xl text-gray-600">{{ $order->product->name }}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Quantity</label>
                        <div class="px-4 py-3 bg-gray-50 rounded-xl text-gray-600">{{ $order->quantity }} unit</div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Total Price</label>
                    <div class="px-4 py-3 bg-gray-50 rounded-xl text-gray-800 font-bold">Rp {{ number_format($order->total_price, 0, ',', '.') }}</div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                    <select name="status" required
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition bg-white">
                        <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <div class="pt-4 flex items-center gap-3">
                    <button type="submit"
                        class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-xl transition shadow-lg shadow-indigo-200">
                        Update Status
                    </button>
                    <a href="{{ route('orders') }}"
                        class="px-6 py-3 bg-gray-100 text-gray-600 font-semibold rounded-xl hover:bg-gray-200 transition">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection