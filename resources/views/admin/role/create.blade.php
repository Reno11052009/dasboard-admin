@extends('admin.components.layout')

@section('header', 'Tambah Role Baru')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('roles') }}" class="text-sm text-indigo-600 hover:underline flex items-center gap-1">
            ← Kembali ke Daftar Role
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-8">
            <form action="{{ route('roles.store') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Role</label>
                    <input type="text" name="name" required value="{{ old('name') }}"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition">
                </div>

                <div class="pt-4 border-t border-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <label class="block text-lg font-semibold text-gray-800">Pengaturan Hak Akses (Permissions)</label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" id="selectAll" class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                            <span class="text-sm font-semibold text-gray-700">Pilih Semua Akses</span>
                        </label>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @php
                            $rolePerms = [];
                            if (session()->hasOldInput()) {
                                $oldPerms = old('permissions', []);
                                $rolePerms = is_array($oldPerms) ? $oldPerms : [];
                            }
                        @endphp
                        @foreach($availablePermissions as $group => $perms)
                        <div class="bg-gray-50 rounded-xl p-5 border border-gray-100">
                            <h3 class="font-bold text-gray-700 capitalize mb-3 border-b border-gray-200 pb-2">Manajemen {{ $group }}</h3>
                            <div class="space-y-3">
                                @foreach($perms as $perm)
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="checkbox" name="permissions[]" value="{{ $perm }}" {{ in_array($perm, $rolePerms) ? 'checked' : '' }} class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                    <span class="text-gray-600 group-hover:text-gray-900 transition">{{ $perm }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="pt-6 flex items-center gap-3">
                    <button type="submit"
                        class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-xl transition shadow-lg shadow-indigo-200 cursor-pointer">
                        Simpan Role
                    </button>
                    <a href="{{ route('roles') }}"
                        class="px-6 py-3 bg-gray-100 text-gray-600 font-semibold rounded-xl hover:bg-gray-200 transition">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAllCheckbox = document.getElementById('selectAll');
        const permissionCheckboxes = document.querySelectorAll('input[name="permissions[]"]');

        selectAllCheckbox.addEventListener('change', function() {
            permissionCheckboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });
        });

        permissionCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const allChecked = Array.from(permissionCheckboxes).every(cb => cb.checked);
                selectAllCheckbox.checked = allChecked;
            });
        });
    });
</script>
@endsection
