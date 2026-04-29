@extends('admin.components.layout')

@section('header', 'Inventory Adjustments')

@section('content')
<div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-6">
    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        <h2 class="text-xl font-bold text-gray-800">Inventory Adjustments</h2>
        <div class="flex gap-2">
            <form action="{{ route('inventory.index') }}" method="GET" class="flex gap-2 w-full md:w-auto">
                {{-- <select name="action" class="bg-gray-50 border border-gray-200 text-gray-700 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5 outline-none transition">
                    <option value="all" {{ request('action') == 'all' ? 'selected' : '' }}>Semua Aksi</option>
                    <option value="created" {{ request('action') == 'created' ? 'selected' : '' }}>Dibuat</option>
                    <option value="updated" {{ request('action') == 'updated' ? 'selected' : '' }}>Diedit</option>
                    <option value="deleted" {{ request('action') == 'deleted' ? 'selected' : '' }}>Dihapus</option>
                    <option value="adjustment" {{ request('action') == 'adjustment' ? 'selected' : '' }}>Penyesuaian</option>
                    <option value="order" {{ request('action') == 'order' ? 'selected' : '' }}>Terjual</option>
                </select> --}}
                <div class="relative w-full md:w-64">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 p-2.5 outline-none transition" placeholder="Cari nama produk atau user...">
                </div>
                <button type="submit" class="p-2.5 text-sm font-medium text-white bg-indigo-600 rounded-lg border border-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:outline-none focus:ring-indigo-300 transition shadow-sm cursor-pointer">
                    <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                    </svg>
                    <span class="sr-only">Search</span>
                </button>
            </form>
            <button onclick="openEditModal()" class="flex items-center gap-2 p-2.5 text-sm font-bold text-indigo-700 bg-indigo-50 rounded-lg border border-indigo-100 hover:bg-indigo-100 focus:ring-4 focus:outline-none focus:ring-indigo-100 transition shadow-sm cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                <span>Edit Stok</span>
            </button>
        </div>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-gray-600">
            <thead class="bg-gray-50 text-gray-700 uppercase text-xs font-semibold rounded-t-2xl border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4">Tanggal</th>
                    <th class="px-6 py-4">Produk</th>
                    <th class="px-6 py-4">Pengguna</th>
                    <th class="px-6 py-4">Aksi</th>
                    <th class="px-6 py-4 text-center">In</th>
                    <th class="px-6 py-4 text-center">Out</th>
                    <th class="px-6 py-4 text-center">Total</th>
                    <th class="px-6 py-4">Catatan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($adjustments as $adj)
                <tr class="hover:bg-gray-50/80 transition duration-150">
                    <td class="px-6 py-4">{{ $adj->created_at->format('d M Y H:i:s') }}</td>
                    <td class="px-6 py-4">
                        @if($adj->product)
                            <span class="font-medium text-gray-800">{{ $adj->product->name }}</span>
                        @else
                            <span class="text-gray-400 italic">Produk Dihapus</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 font-medium">{{ $adj->user ? $adj->user->name : 'Sistem' }}</td>
                    <td class="px-6 py-4">
                        @if($adj->action === 'created')
                            <span class="px-2.5 py-1 bg-emerald-100 text-emerald-700 text-[11px] font-bold rounded-md uppercase tracking-wider">Dibuat</span>
                        @elseif($adj->action === 'updated')
                            <span class="px-2.5 py-1 bg-blue-100 text-blue-700 text-[11px] font-bold rounded-md uppercase tracking-wider">Diedit</span>
                        @elseif($adj->action === 'order')
                            <span class="px-2.5 py-1 bg-amber-100 text-amber-700 text-[11px] font-bold rounded-md uppercase tracking-wider">Terjual</span>
                        @elseif($adj->action === 'deleted')
                            <span class="px-2.5 py-1 bg-red-100 text-red-700 text-[11px] font-bold rounded-md uppercase tracking-wider">Dihapus</span>
                        @else
                            <span class="px-2.5 py-1 bg-gray-100 text-gray-700 text-[11px] font-bold rounded-md uppercase tracking-wider">Penyesuaian</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center font-mono">
                        @if($adj->stok > 0)
                            <span class="inline-block px-2.5 py-1 bg-emerald-50 text-emerald-700 font-bold rounded-lg text-sm">+{{ $adj->stok }}</span>
                        @else
                            <span class="text-gray-300">—</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center font-mono">
                        @if($adj->stok < 0)
                            <span class="inline-block px-2.5 py-1 bg-red-50 text-red-600 font-bold rounded-lg text-sm">{{ abs($adj->stok) }}</span>
                        @else
                            <span class="text-black">—</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center font-mono">
                        @if($adj->action === 'deleted')
                            <span class="text-gray-400">—</span>
                        @else
                            <span class="inline-block px-2.5 py-1 bg-indigo-50 text-indigo-700 font-bold rounded-lg text-sm">{{ $adj->stok_total }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-gray-600">{{ $adj->note ?: '-' }}</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center text-gray-500">Kosong</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($adjustments->hasPages())
    <div class="p-6 border-t border-gray-100 bg-gray-50/30">
        {{ $adjustments->links() }}
    </div>
    @endif
</div>

<div id="editProductModal" class="fixed inset-0 z-[100] hidden">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity" onclick="closeEditModal()"></div>

    <!-- Modal Panel -->
    <div class="fixed inset-0 flex items-center justify-center p-4 z-10">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md flex flex-col transform transition-all">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/80 rounded-t-2xl">
                <h3 class="text-lg font-bold text-gray-800">Pilih Produk untuk Diedit</h3>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 p-1 rounded-lg hover:bg-gray-100 focus:outline-none transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="p-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Produk</label>
                <select id="selectedProductId" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 outline-none transition bg-white">
                    <option value="">-- Silakan pilih produk --</option>
                    @foreach($productsList as $prod)
                        <option value="{{ $prod->id }}">{{ $prod->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-3 bg-gray-50/80 rounded-b-2xl">
                <button onclick="closeEditModal()" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 focus:outline-none transition cursor-pointer">Batal</button>
                <button onclick="proceedToEdit()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 focus:outline-none transition cursor-pointer shadow-sm flex items-center gap-2">
                    Lanjutkan ke Edit <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function openEditModal() {
        document.getElementById('editProductModal').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('editProductModal').classList.add('hidden');
    }

    function proceedToEdit() {
        const select = document.getElementById('selectedProductId');
        const id = select.value;
        if (id) {
            window.location.href = '/admin/inventory-adjustments/product/' + id + '/edit';
        } else {
            Swal.fire({
                icon: 'warning',
                title: 'Produk belum dipilih',
                text: 'Silakan pilih produk terlebih dahulu!',
                confirmButtonText: 'OK',
                confirmButtonColor: '#4f46e5',
            });
            select.focus();
        }
    }
</script>
@endsection
