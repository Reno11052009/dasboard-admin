@extends('components.layout')

@section('header', 'Order Management')

@section('content')
<div class="flex flex-wrap justify-between items-center gap-4 mb-6">
    <h2 class="text-xl font-bold text-gray-800">Daftar Pesanan</h2>
    <form method="get" class="flex flex-wrap items-center gap-3">
        <select name="status" onchange="this.form.submit()" class="px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white">
            <option value="all">All Status</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
        </select>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." class="px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
    </form>
</div>

<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <p class="text-gray-400 text-xs uppercase font-medium">Pending</p>
        <p class="text-2xl font-bold text-amber-600">{{ $statusCounts['pending']->count ?? 0 }}</p>
    </div>
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <p class="text-gray-400 text-xs uppercase font-medium">Processing</p>
        <p class="text-2xl font-bold text-blue-600">{{ $statusCounts['processing']->count ?? 0 }}</p>
    </div>
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <p class="text-gray-400 text-xs uppercase font-medium">Completed</p>
        <p class="text-2xl font-bold text-emerald-600">{{ $statusCounts['completed']->count ?? 0 }}</p>
    </div>
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <p class="text-gray-400 text-xs uppercase font-medium">Cancelled</p>
        <p class="text-2xl font-bold text-red-600">{{ $statusCounts['cancelled']->count ?? 0 }}</p>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4 text-xs uppercase font-semibold text-gray-500">Order ID</th>
                    <th class="px-6 py-4 text-xs uppercase font-semibold text-gray-500">Customer</th>
                    <th class="px-6 py-4 text-xs uppercase font-semibold text-gray-500">Product</th>
                    <th class="px-6 py-4 text-xs uppercase font-semibold text-gray-500">Qty</th>
                    <th class="px-6 py-4 text-xs uppercase font-semibold text-gray-500">Total</th>
                    <th class="px-6 py-4 text-xs uppercase font-semibold text-gray-500">Status</th>
                    <th class="px-6 py-4 text-xs uppercase font-semibold text-gray-500">Date</th>
                    <th class="px-6 py-4 text-xs uppercase font-semibold text-gray-500 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($orders as $order)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 text-sm text-gray-600">#{{ $order->id }}</td>
                    <td class="px-6 py-4 text-sm text-gray-800">{{ $order->user->name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-800">{{ $order->product->name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $order->quantity }}</td>
                    <td class="px-6 py-4 text-sm text-gray-800 font-medium">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                    <td class="px-6 py-4">
                        @php
                            $statusClasses = [
                                'pending' => 'bg-amber-100 text-amber-700',
                                'processing' => 'bg-blue-100 text-blue-700',
                                'completed' => 'bg-emerald-100 text-emerald-700',
                                'cancelled' => 'bg-red-100 text-red-700',
                            ];
                        @endphp
                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ $statusClasses[$order->status] ?? 'bg-gray-100 text-gray-700' }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ \Carbon\Carbon::parse($order->order_date)->format('d M Y') }}</td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('orders.edit', $order->id) }}" class="p-1.5 bg-amber-50 text-amber-600 rounded-md hover:bg-amber-100 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <form action="{{ route('orders.delete', $order->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pesanan ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 bg-red-50 text-red-600 rounded-md hover:bg-red-100 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-10 text-center text-gray-400 italic">
                        Belum ada data pesanan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection