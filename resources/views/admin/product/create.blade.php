@extends('admin.components.layout')

@section('header', 'Add New Product')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('products') }}" class="text-sm text-indigo-600 hover:underline flex items-center gap-1">
            ← Kembali ke Daftar Produk
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-8">
            @if ($errors->any())
                <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-6 text-sm">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Produk</label>
                    <input type="text" name="name" required
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition"
                        placeholder="Contoh: Premium Access Member">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi Produk</label>
                    <textarea name="description" rows="4"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition"
                        placeholder="Deskripsi singkat tentang produk..."></textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Kategori</label>
                    <input type="text" name="category"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition"
                        placeholder="Contoh: Akses Premium, E-book, dll.">
                </div>

                <div>
                    <label for="image">Gambar Produk</label>
                    <input type="file" name="image" id="image" accept="image/*"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Jumlah Stok</label>
                        <input type="number" name="stok" required
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition"
                            placeholder="0">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Harga (Rp)</label>
                        <div class="relative">
                            <span class="absolute left-4 top-3 text-gray-400">Rp</span>
                            <input type="number" name="price" required
                                class="w-full pl-12 pr-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition"
                                placeholder="0">
                        </div>
                    </div>
                </div>

                <div class="pt-4 flex items-center gap-3">
                    <button type="submit"
                        class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-xl transition shadow-lg shadow-indigo-200 cursor-pointer">
                        Simpan Produk
                    </button>
                    <a href="/admin/products"
                        class="px-6 py-3 bg-gray-100 text-gray-600 font-semibold rounded-xl hover:bg-gray-200 transition cursor-pointer text-center">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
