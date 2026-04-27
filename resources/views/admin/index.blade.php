@extends('admin.components.layout')

@section('header', 'Dashboard Overview')

@section('content')
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 text-center overflow-hidden flex flex-col justify-center">
        <p class="text-xl lg:text-2xl font-bold text-slate-800 truncate" title="{{ $userCount }}">{{ $userCount }}</p>
        <p class="text-xs lg:text-sm text-gray-500 truncate" title="Total Users">Total Users</p>
    </div>
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 text-center overflow-hidden flex flex-col justify-center">
        <p class="text-xl lg:text-2xl font-bold text-indigo-600 truncate" title="{{ $productCount }}">{{ $productCount }}</p>
        <p class="text-xs lg:text-sm text-gray-500 truncate" title="Total Products">Total Products</p>
    </div>
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 text-center overflow-hidden flex flex-col justify-center">
        <p class="text-xl lg:text-2xl font-bold text-amber-600 truncate" title="{{ $orderCount }}">{{ $orderCount }}</p>
        <p class="text-xs lg:text-sm text-gray-500 truncate" title="Total Orders">Total Orders</p>
    </div>
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 text-center overflow-hidden flex flex-col justify-center">
        <p class="text-xl lg:text-2xl font-bold text-emerald-600 truncate" title="{{ number_format($totalSales, 0, ',', '.') }}">{{ number_format($totalSales, 0, ',', '.') }}</p>
        <p class="text-xs lg:text-sm text-gray-500 truncate" title="Total Pendapatan">Total Pendapatan</p>
        @if(isset($salesChange))
        <p class="text-xs truncate {{ $salesChange >= 0 ? 'text-green-500' : 'text-red-500' }}">
            {{ $salesChange >= 0 ? '+' : '' }}{{ $salesChange }}%
        </p>
        @endif
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 lg:col-span-2">
        <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
            <h3 class="font-bold text-gray-700">Sales Analytics</h3>
            <div class="flex flex-wrap items-center gap-3">
                <form method="get" class="flex flex-wrap items-center gap-2">
                    <select name="period" onchange="this.form.submit()" class="px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="monthly" {{ ($period ?? 'monthly') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                        <option value="yearly" {{ ($period ?? '') == 'yearly' ? 'selected' : '' }}>Yearly</option>
                        <option value="custom" {{ request('start_date') ? 'selected' : '' }}>Custom</option>
                    </select>
                    @if(($period ?? '') == 'custom' || request('start_date'))
                    <input type="date" name="start_date" value="{{ $startDate ?? '' }}" class="px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Start Date">
                    <input type="date" name="end_date" value="{{ $endDate ?? '' }}" class="px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="End Date">
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">Filter</button>
                    @endif
                </form>
            </div>
        </div>
        <div class="h-64">
            <canvas id="salesChart"></canvas>
        </div>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <h3 class="font-bold text-gray-700 mb-4">Product Distribution</h3>
        <div class="h-64">
            <canvas id="productSalesChart"></canvas>
        </div>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <h3 class="font-bold text-gray-700 mb-4">Orders Analytics</h3>
        <div class="h-64">
            <canvas id="ordersChart"></canvas>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <h3 class="font-bold text-gray-700 mb-4">Top Selling Products</h3>
        <div class="space-y-3">
            @forelse($topSellingProducts as $order)
            <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                <div>
                    <p class="font-medium text-gray-800">{{ $order->product->name ?? 'Unknown' }}</p>
                    <p class="text-sm text-gray-500">{{ $order->total_qty }} terjual</p>
                </div>
                <p class="font-semibold text-emerald-600">Rp {{ number_format($order->total, 0, ',', '.') }}</p>
            </div>
            @empty
            <p class="text-gray-500 text-sm">Belum ada data</p>
            @endforelse
        </div>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <h3 class="font-bold text-gray-700 mb-4">Low Stock Products</h3>
        <div class="space-y-3">
            @forelse($lowStockProducts as $product)
            <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                <div>
                    <p class="font-medium text-gray-800">{{ $product->name }}</p>
                    <p class="text-sm text-gray-500">{{ $product->category }}</p>
                </div>
                <p class="font-semibold {{ $product->stok < 10 ? 'text-red-600' : 'text-orange-600' }}">{{ $product->stok }} stok</p>
            </div>
            @empty
            <p class="text-gray-500 text-sm">Belum ada produk</p>
            @endforelse
        </div>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-gray-700">Recent Sales</h3>
            <a href="{{ route('orders') }}" class="text-sm text-indigo-600 hover:text-indigo-800 hover:underline font-medium">View All &rarr;</a>
        </div>
        <div class="space-y-3">
            @forelse($recentSales as $sale)
            <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                <div>
                    <p class="font-medium text-gray-800">{{ $sale->user->name ?? 'Unknown' }}</p>
                    <p class="text-sm text-gray-500">{{ $sale->product->name ?? 'Unknown' }}</p>
                </div>
                <div class="text-right">
                    <p class="font-semibold text-emerald-600">Rp {{ number_format($sale->total_price, 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-500">{{ $sale->order_date->format('d M') }}</p>
                </div>
            </div>
            @empty
            <p class="text-gray-500 text-sm">Belum ada penjualan</p>
            @endforelse
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('salesChart').getContext('2d');
    const chartType = '{{ $period ?? "monthly" }}' === 'yearly' ? 'bar' : 'line';
    new Chart(ctx, {
        type: chartType,
        data: {
            labels: {!! json_encode($chartData['labels'] ?? []) !!},
            datasets: [{
                label: 'Sales',
                data: {!! json_encode($chartData['data'] ?? []) !!},
                borderColor: '#4f46e5',
                backgroundColor: 'rgba(79, 70, 229, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } }
        }
    });

    new Chart(document.getElementById('ordersChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($chartData['labels'] ?? []) !!},
            datasets: [{
                label: 'Orders',
                data: {!! json_encode($chartData['orderData'] ?? []) !!},
                backgroundColor: 'rgba(16, 185, 129, 0.8)',
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { precision: 0 } }
            }
        }
    });

    new Chart(document.getElementById('productSalesChart').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($productSalesData['labels'] ?? []) !!},
            datasets: [{
                data: {!! json_encode($productSalesData['data'] ?? []) !!},
                backgroundColor: ['#4f46e5', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'],
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const idx = context.dataIndex;
                            const percentages = {!! json_encode($productSalesData['percentages'] ?? []) !!};
                            const label = context.label || '';
                            const value = context.raw || 0;
                            const pct = percentages[idx] || 0;
                            return label + ': ' + value + ' (' + pct + '%)';
                        }
                    }
                }
            }
        }
    });
</script>
@endsection
