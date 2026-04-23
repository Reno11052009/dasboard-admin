@extends('components.layout')

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
        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 cursor-pointer">Cari</button>
        @if(request('search'))
        <a href="{{ route('products') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 cursor-pointer">Reset</a>
        @endif
    </div>
</form>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead class="bg-gray-50 border-b border-gray-100">
            <tr>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500">GAMBAR</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500">NAMA PRODUK</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500">STOK</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500">HARGA</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 text-right">AKSI</th>
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
                        <span class="text-gray-400 text-xs">No Image</span>
                    </div>
                    @endif
                </td>
                <td class="px-6 py-4 font-medium text-gray-800">{{ $product->name }}</td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 {{ $product->stok < 10 ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600' }} rounded text-xs font-bold">
                        {{ $product->stok }}
                    </span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                <td class="px-6 py-4 text-right space-x-2">
                    <a href="{{ route('product.edit', $product->id) }}" class="text-indigo-600 hover:underline cursor-pointer">Edit</a>
                    <form action="{{ route('product.delete', $product->id) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline cursor-pointer" onclick="return confirm('Hapus produk ini?')">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
