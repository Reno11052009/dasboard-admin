@extends('admin.components.layout')

@section('header', 'Product Management')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-xl font-bold text-gray-800">Daftar Produk</h2>
    <a href="/admin/products/create" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm transition cursor-pointer">
        + Produk Baru
    </a>
</div>

<form method="get" class="mb-6">
    <div class="flex gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari produk..." class="px-4 py-2 border rounded-lg w-full max-w-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
        <select name="category" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white cursor-pointer" onchange="this.form.submit()">
            <option value="">Semua Kategori</option>
            @foreach($categories as $cat)
            <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
            @endforeach
        </select>
        <select name="limit" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white cursor-pointer" onchange="this.form.submit()">
            <option value="10" {{ request('limit') == 10 ? 'selected' : '' }}>10 Baris</option>
            <option value="25" {{ request('limit') == 25 ? 'selected' : '' }}>25 Baris</option>
            <option value="50" {{ request('limit') == 50 ? 'selected' : '' }}>50 Baris</option>
            <option value="all" {{ request('limit') == 'all' ? 'selected' : '' }}>Semua</option>
        </select>
        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 cursor-pointer">Filter</button>
        @if(request('search') || request('limit') || request('category'))
        <a href="{{ route('products') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 cursor-pointer">Reset</a>
        @endif
    </div>
</form>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 min-w-[100px]">GAMBAR</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 min-w-[200px]">NAMA PRODUK</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 min-w-[150px]">KATEGORI</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 min-w-[100px]">STOK</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 min-w-[150px]">HARGA</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 text-right min-w-[120px]">AKSI</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($products as $product)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-16 h-16 object-cover rounded-lg">
                        @else
                        <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                            <span class="text-gray-400 text-xs text-center leading-tight">No Image</span>
                        </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 font-medium text-gray-800">{{ $product->name }}</td>
                    <td class="px-6 py-4">{{ $product->category ?? 'Tidak ada kategori' }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 {{ $product->stok < 10 ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600' }} rounded text-xs font-bold whitespace-nowrap">
                            {{ $product->stok }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600 whitespace-nowrap">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('product.edit', $product->id) }}" class="p-1.5 bg-amber-50 text-amber-600 rounded-md hover:bg-amber-100 transition cursor-pointer" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <form action="{{ route('product.delete', $product->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 bg-red-50 text-red-600 rounded-md hover:bg-red-100 transition cursor-pointer" onclick="confirmDelete(event, this)" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($products instanceof \Illuminate\Pagination\LengthAwarePaginator && $products->hasPages())
    <div class="p-4 border-t border-gray-100">
        {{ $products->links() }}
    </div>
    @endif
</div>
@endsection
