<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans">
    <div class="flex h-screen overflow-hidden">
        @include('admin.components.sidebar')

        <div class="flex-1 flex flex-col overflow-y-auto">
            @include('admin.components.header')

            @if(session('success'))
                <div id="toast" class="fixed top-4 right-4 bg-emerald-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
                    {{ session('success') }}
                </div>
                <script>
                    setTimeout(() => document.getElementById('toast')?.remove(), 3000);
                </script>
            @endif

            <main class="p-8 flex-1">
                @yield('content')
            </main>
            @include('admin.components.footer')
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(event, el) {
            event.preventDefault();
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    el.closest('form').submit();
                }
            })
        }
    </script>
</body>
</html>
