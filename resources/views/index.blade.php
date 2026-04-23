@extends('components.layout')

@section('header', 'Dashboard Overview')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <h3 class="text-gray-400 text-sm font-medium">TOTAL USERS</h3>
        <p class="text-2xl font-bold text-slate-800">{{ $userCount }}</p>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <h3 class="text-gray-400 text-sm font-medium">TOTAL PRODUCTS</h3>
        <p class="text-2xl font-bold text-indigo-600">{{ $productCount }}</p>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <h3 class="text-gray-400 text-sm font-medium">TOTAL SALES</h3>
        <p class="text-2xl font-bold text-emerald-600">{{ number_format($totalSales, 0, ',', '.') }}</p>
    </div>
</div>

<div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 mb-6">
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
</script>
@endsection
