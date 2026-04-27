@extends('admin.components.layout')

@section('header', 'Product Management')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-xl font-bold text-gray-800">Daftar Produk</h2>
    @if(Auth::user()->isSuperAdmin() || Auth::user()->hasPermission('product.create'))
    <a href="/admin/products/create" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm transition cursor-pointer">
        + Produk Baru
    </a>
    @endif
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
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 min-w-[100px]">STATUS</th>
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
                    <td class="px-6 py-4 font-medium text-gray-800">
                        {{ $product->name }}
                        @if($product->status == 'pending')
                            <div class="text-[10px] text-orange-500 mt-1">Butuh Persetujuan</div>
                        @elseif($product->status == 'rejected')
                            <div class="text-[10px] text-red-500 mt-1">Ditolak</div>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($product->status == 'approved')
                        <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-[10px] font-bold">Approved</span>
                        @elseif($product->status == 'pending')
                        <span class="px-2 py-1 bg-orange-100 text-orange-700 rounded-full text-[10px] font-bold">Pending</span>
                        @else
                        <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-[10px] font-bold">Rejected</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">{{ $product->category ?? 'Tidak ada kategori' }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 {{ $product->stok < 10 ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600' }} rounded text-xs font-bold whitespace-nowrap">
                            {{ $product->stok }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600 whitespace-nowrap">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('product.show', $product->id) }}" class="p-1.5 bg-indigo-50 text-indigo-600 rounded-md hover:bg-indigo-100 transition cursor-pointer" title="Detail">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                            </a>
                            
                            @if((Auth::user()->isSuperAdmin() || Auth::user()->hasPermission('product.master')) && $product->status == 'pending')
                            <form action="{{ route('product.updateStatus', $product->id) }}" method="POST" class="inline">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="approved">
                                <button type="submit" class="p-1.5 bg-emerald-50 text-emerald-600 rounded-md hover:bg-emerald-100 transition cursor-pointer" title="Setujui Produk">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </button>
                            </form>
                            <form action="{{ route('product.updateStatus', $product->id) }}" method="POST" class="inline">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="rejected">
                                <button type="submit" class="p-1.5 bg-orange-50 text-orange-600 rounded-md hover:bg-orange-100 transition cursor-pointer" title="Tolak Produk">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </form>
                            @endif
                            @if(Auth::user()->isSuperAdmin() || Auth::user()->hasPermission('product.edit'))
                            <a href="{{ route('product.edit', $product->id) }}" class="p-1.5 bg-amber-50 text-amber-600 rounded-md hover:bg-amber-100 transition cursor-pointer" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            @endif
                            @if(Auth::user()->isSuperAdmin() || Auth::user()->hasPermission('product.delete'))
                            <form action="{{ route('product.delete', $product->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 bg-red-50 text-red-600 rounded-md hover:bg-red-100 transition cursor-pointer" onclick="confirmDelete(event, this)" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                            @endif
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
