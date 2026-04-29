@extends('admin.components.layout')

@section('header', 'Penyesuaian Stok')

@section('content')
<div class="max-w-lg mx-auto">
    <div class="mb-6">
        <a href="{{ route('inventory.index') }}" class="text-sm text-indigo-600 hover:underline flex items-center gap-1">
            ← Kembali ke Inventory Adjustments
        </a>
    </div>

    {{-- Info Produk --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-4">
        <div class="flex items-center gap-4">
            @if($product->image)
                <img src="{{ asset('storage/' . $product->image) }}" class="w-14 h-14 rounded-xl object-cover border border-gray-100">
            @else
                <div class="w-14 h-14 rounded-xl bg-indigo-50 flex items-center justify-center">
                    <svg class="w-7 h-7 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                    </svg>
                </div>
            @endif
            <div>
                <h2 class="text-lg font-bold text-gray-800">{{ $product->name }}</h2>
                <p class="text-sm text-gray-400">{{ $product->category ?: 'Tanpa kategori' }}</p>
            </div>
            <div class="ml-auto text-right">
                <p class="text-xs text-gray-400 mb-0.5">Stok saat ini</p>
                <span class="text-3xl font-extrabold text-indigo-600">{{ $product->stok }}</span>
            </div>
        </div>
    </div>

    {{-- Form Penyesuaian --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6">
            @if ($errors->any())
                <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-6 text-sm">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('inventory.product.update', $product->id) }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')

                {{-- Hidden inputs --}}
                <input type="hidden" name="tipe" id="inputTipe" value="tambah">
                <input type="hidden" name="jumlah" id="inputJumlah" value="1">

                {{-- Stepper --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">Penyesuaian Stok</label>
                    <div class="flex items-center justify-between gap-4 bg-gray-50 rounded-2xl px-6 py-5">
                        {{-- Tombol Kurang --}}
                        <button type="button" id="btnKurang"
                            onclick="ubahStok(-1)"
                            class="w-12 h-12 rounded-xl bg-red-100 text-red-600 hover:bg-red-200 transition flex items-center justify-center font-bold text-2xl leading-none cursor-pointer select-none">
                            −
                        </button>

                        {{-- Tampilan nilai delta (editable) --}}
                        <div class="text-center flex-1">
                            <div id="labelDelta" class="text-xs font-semibold text-emerald-600 uppercase tracking-widest mb-1">Tambah</div>
                            <input type="number" id="displayJumlah" value="1"
                                oninput="onManualInput()"
                                onblur="onBlurInput()"
                                class="w-full text-center text-4xl font-extrabold text-gray-800 bg-transparent border-none outline-none focus:ring-0 appearance-none">
                        </div>

                        {{-- Tombol Tambah --}}
                        <button type="button" id="btnTambah"
                            onclick="ubahStok(1)"
                            class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 hover:bg-emerald-200 transition flex items-center justify-center font-bold text-2xl leading-none cursor-pointer select-none">
                            +
                        </button>
                    </div>
                </div>

                {{-- Preview hasil --}}
                <div class="bg-gray-50 rounded-xl px-5 py-4 flex items-center justify-between">
                    <span class="text-sm text-gray-500 font-medium">Stok setelah penyesuaian:</span>
                    <span id="previewStok" class="text-2xl font-extrabold text-gray-800">{{ $product->stok + 1 }}</span>
                </div>

                {{-- Catatan --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Catatan / Alasan <span class="text-red-400">*</span></label>
                    <textarea name="edit_reason" rows="2" required
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 outline-none transition"
                        placeholder="Contoh: Stok masuk dari supplier, koreksi stok rusak, dll...">{{ old('edit_reason') }}</textarea>
                </div>

                <div class="pt-2 flex items-center gap-3">
                    <button type="submit"
                        class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-xl transition shadow-lg shadow-indigo-200 cursor-pointer">
                        Simpan Penyesuaian
                    </button>
                    <a href="{{ route('inventory.index') }}"
                        class="px-6 py-3 bg-gray-100 text-gray-600 font-semibold rounded-xl hover:bg-gray-200 transition text-center cursor-pointer">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const stokSaatIni = {{ $product->stok }};
    let delta = 1;

    function syncUI(updateInput = true) {
        const inputDisplay = document.getElementById('displayJumlah');
        const label        = document.getElementById('labelDelta');
        const preview      = document.getElementById('previewStok');
        const inputTipe    = document.getElementById('inputTipe');
        const inputJml     = document.getElementById('inputJumlah');

        const abs   = Math.abs(delta);
        const hasil = stokSaatIni + delta;

        if (updateInput) inputDisplay.value = delta; // tampilkan nilai asli (bisa negatif)

        if (delta >= 0) {
            label.textContent = 'Tambah';
            label.className   = 'text-xs font-semibold text-emerald-600 uppercase tracking-widest mb-1';
            inputTipe.value   = 'tambah';
        } else {
            label.textContent = 'Kurang';
            label.className   = 'text-xs font-semibold text-red-500 uppercase tracking-widest mb-1';
            inputTipe.value   = 'kurang';
        }

        inputJml.value = abs;

        preview.textContent = hasil;
        preview.className   = hasil < 0
            ? 'text-2xl font-extrabold text-red-500'
            : hasil === 0
                ? 'text-2xl font-extrabold text-amber-500'
                : 'text-2xl font-extrabold text-gray-800';
    }

    function ubahStok(arah) {
        delta += arah;
        if (delta === 0) delta = arah > 0 ? 1 : -1;
        syncUI();
    }

    function onManualInput() {
        const raw = parseInt(document.getElementById('displayJumlah').value);
        if (isNaN(raw) || raw === 0) return; // biarkan user mengetik dulu
        delta = raw; // langsung pakai nilai mentah (bisa negatif)
        syncUI(false);
    }

    function onBlurInput() {
        const raw = parseInt(document.getElementById('displayJumlah').value);
        if (isNaN(raw) || raw === 0) {
            delta = delta >= 0 ? 1 : -1;
        } else {
            delta = raw;
        }
        syncUI(true);
    }
</script>
@endsection
