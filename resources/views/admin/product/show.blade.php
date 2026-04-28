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
            
            <div class="mt-8 pt-6 border-t border-gray-100 flex justify-end gap-3">
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
            </div>
        </div>
    </div>

    <!-- Edit History / Inventory Adjustments -->
    <div class="mt-8 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 bg-gray-50/50">
            <h3 class="text-lg font-bold text-gray-800">History Perubahan Produk & Stok</h3>
            <p class="text-sm text-gray-500 mt-1">Total stok saat ini ({{ $product->stok }} unit) dikalkulasi berdasarkan akumulasi penambahan dan pengurangan dari history di bawah ini.</p>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="bg-gray-50 text-gray-700 uppercase text-xs font-semibold rounded-lg">
                        <tr>
                            <th class="px-4 py-3 rounded-l-lg">Tanggal</th>
                            <th class="px-4 py-3">Pengguna</th>
                            <th class="px-4 py-3">Aksi</th>
                            <th class="px-4 py-3">Perubahan Stok & Harga</th>
                            <th class="px-4 py-3 rounded-r-lg">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($adjustments as $adj)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-4 py-4">{{ $adj->created_at->format('d M Y H:i') }}</td>
                            <td class="px-4 py-4 font-medium">{{ $adj->user ? $adj->user->name : 'Sistem' }}</td>
                            <td class="px-4 py-4">
                                @if($adj->action === 'created')
                                    <span class="px-2.5 py-1 bg-emerald-100 text-emerald-700 text-[11px] font-bold rounded-md">Dibuat</span>
                                @elseif($adj->action === 'updated')
                                    <span class="px-2.5 py-1 bg-blue-100 text-blue-700 text-[11px] font-bold rounded-md">Diedit</span>
                                @elseif($adj->action === 'order')
                                    <span class="px-2.5 py-1 bg-amber-100 text-amber-700 text-[11px] font-bold rounded-md">Terjual</span>
                                @else
                                    <span class="px-2.5 py-1 bg-gray-100 text-gray-700 text-[11px] font-bold rounded-md">Penyesuaian</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-xs font-mono">
                                @if($adj->stok > 0)
                                    <div class="mb-1"><span class="font-bold text-gray-700">Stok:</span> <span class="text-emerald-600 font-bold bg-emerald-50 px-2 py-0.5 rounded">+{{ $adj->stok }}</span></div>
                                @elseif($adj->stok < 0)
                                    <div class="mb-1"><span class="font-bold text-gray-700">Stok:</span> <span class="text-red-600 font-bold bg-red-50 px-2 py-0.5 rounded">{{ $adj->stok }}</span></div>
                                @endif

                                @if($adj->harga > 0)
                                    <div class="mb-1"><span class="font-bold text-gray-700">Harga:</span> <span class="text-emerald-600 font-bold bg-emerald-50 px-2 py-0.5 rounded">+ Rp {{ number_format($adj->harga, 0, ',', '.') }}</span></div>
                                @elseif($adj->harga < 0)
                                    <div class="mb-1"><span class="font-bold text-gray-700">Harga:</span> <span class="text-red-600 font-bold bg-red-50 px-2 py-0.5 rounded">- Rp {{ number_format(abs($adj->harga), 0, ',', '.') }}</span></div>
                                @endif

                                @if($adj->stok == 0 && $adj->harga == 0)
                                    <span class="text-gray-400">Tidak ada perubahan stok/harga</span>
                                @endif
                            </td>
                            <td class="px-4 py-4">{{ $adj->note ?: '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">Belum ada history perubahan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


@endsection
