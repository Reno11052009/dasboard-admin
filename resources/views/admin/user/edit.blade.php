@extends('admin.components.layout')

@section('header', 'Edit User')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('users') }}" class="text-sm text-indigo-600 hover:underline flex items-center gap-1">
            ← Kembali ke Daftar User
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-8">
            <form action="{{ route('users.update', $user->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap</label>
                    <input type="text" name="name" required value="{{ old('name', $user->name) }}"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition"
                        placeholder="Contoh: John Doe">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" required value="{{ old('email', $user->email) }}"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition"
                        placeholder="Contoh: john@example.com">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Password Baru</label>
                    <input type="password" name="password"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition"
                        placeholder="Kosongkan jika tidak ingin更改 password">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition"
                        placeholder="Masukkan password yang sama">
                </div>

                <div class="pt-4 flex items-center gap-3">
                    <button type="submit"
                        class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-xl transition shadow-lg shadow-indigo-200">
                        Update User
                    </button>
                    <a href="{{ route('users') }}"
                        class="px-6 py-3 bg-gray-100 text-gray-600 font-semibold rounded-xl hover:bg-gray-200 transition">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
