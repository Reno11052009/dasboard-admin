@extends('components.layout')

@section('header', 'Product Management')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-xl font-bold text-gray-800">Daftar Produk</h2>
    <a href="/admin/products/create" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm transition">
        + Produk Baru
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead class="bg-gray-50 border-b border-gray-100">
            <tr>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500">NAMA PRODUK</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500">STOK</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500">HARGA</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 text-right">AKSI</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($products as $product)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-6 py-4 font-medium text-gray-800">{{ $product->name }}</td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 {{ $product->stok < 10 ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600' }} rounded text-xs font-bold">
                        {{ $product->stok }}
                    </span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                <td class="px-6 py-4 text-right space-x-2">
                    <a href="{{ route('product.edit', $product->id) }}" class="text-indigo-600 hover:underline">Edit</a>
                    <form action="{{ route('product.delete', $product->id) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline" onclick="return confirm('Hapus produk ini?')">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
