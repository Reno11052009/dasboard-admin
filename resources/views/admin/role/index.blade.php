@extends('admin.components.layout')

@section('header', 'Role Management')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-gray-800">Daftar Role</h1>
    <a href="{{ route('roles.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-xl transition shadow-sm">
        + Tambah Role Baru
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full whitespace-nowrap">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Role</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Hak Akses (Permissions)</th>
                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($roles as $role)
                <tr class="hover:bg-gray-50 transition duration-150">
                    <td class="px-6 py-4">
                        <div class="font-medium text-gray-900">{{ $role->name }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-wrap gap-1 max-w-md">
                            @if(is_array($role->permissions) && count($role->permissions) > 0)
                                @if($role->name === 'Super Admin')
                                    <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-lg">Semua Akses</span>
                                @else
                                    @foreach(array_slice($role->permissions, 0, 5) as $perm)
                                        <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-700 rounded-lg">{{ $perm }}</span>
                                    @endforeach
                                    @if(count($role->permissions) > 5)
                                        <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-lg">+{{ count($role->permissions) - 5 }} lainnya</span>
                                    @endif
                                @endif
                            @else
                                <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-500 rounded-lg">Tidak ada akses</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <a href="{{ route('roles.edit', $role->id) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-indigo-600 hover:bg-indigo-50 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                        </a>
                        @if($role->name !== 'Super Admin')
                        <form action="{{ route('roles.delete', $role->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus role ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-red-600 hover:bg-red-50 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    @if($roles->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $roles->links() }}
    </div>
    @endif
</div>
@endsection
