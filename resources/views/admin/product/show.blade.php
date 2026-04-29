@extends('admin.components.layout')

@section('header', 'Product Details')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('products') }}" class="text-sm text-indigo-600 hover:underline flex items-center gap-1">
            ← Kembali ke Daftar Produk
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-8">
            <div class="flex flex-col md:flex-row gap-8">
                <!-- Image -->
                <div class="w-full md:w-1/3 flex-shrink-0">
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-auto aspect-square object-cover rounded-xl border border-gray-100 shadow-sm">
                    @else
                        <div class="w-full aspect-square bg-gray-100 rounded-xl flex items-center justify-center border border-gray-200">
                            <span class="text-gray-400 font-medium">No Image Available</span>
                        </div>
                    @endif
                </div>

                <!-- Product Info -->
                <div class="w-full md:w-2/3 flex flex-col justify-center">
                    <div class="mb-2">
                        <span class="px-3 py-1 bg-indigo-50 text-indigo-600 text-xs font-bold rounded-full uppercase tracking-wider">{{ $product->category ?? 'Tanpa Kategori' }}</span>
                    </div>

                    <h2 class="text-3xl font-bold text-gray-800 mb-4">{{ $product->name }}</h2>

                    <div class="text-2xl font-bold text-emerald-600 mb-6">
                        Rp {{ number_format($product->price, 0, ',', '.') }}
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                            <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Stok Tersedia</p>
                            <p class="text-lg font-semibold {{ $product->stok < 10 ? 'text-red-600' : 'text-gray-800' }}">
                                {{ $product->stok }} unit
                            </p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                            <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Terakhir Diupdate</p>
                            <p class="text-lg font-semibold text-gray-800">
                                {{ $product->updated_at ? $product->updated_at->format('d M Y') : '-' }}
                            </p>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-2">Deskripsi Produk</h3>
                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 text-gray-600 text-sm leading-relaxed min-h-[100px] whitespace-pre-wrap">{{ $product->description ?: 'Tidak ada deskripsi untuk produk ini.' }}</div>
                    </div>
                </div>
            </div>

            {{-- <div class="mt-8 pt-6 border-t border-gray-100 flex justify-end gap-3">
                <a href="{{ route('product.edit', $product->id) }}" class="px-6 py-2 bg-amber-500 hover:bg-amber-600 text-white font-semibold rounded-lg transition shadow-sm">
                    Edit Produk
                </a>
                <form action="{{ route('product.delete', $product->id) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="button" onclick="confirmDelete(event, this)" class="px-6 py-2 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-lg transition shadow-sm cursor-pointer">
                        Hapus Produk
                    </button>
                </form>
            </div> --}}
        </div>
    </div>

</div>


@endsection
