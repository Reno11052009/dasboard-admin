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
        @include('components.sidebar')

        <div class="flex-1 flex flex-col overflow-y-auto">
            @include('components.header')

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
            @include('components.footer')
        </div>
    </div>
</body>
</html>
