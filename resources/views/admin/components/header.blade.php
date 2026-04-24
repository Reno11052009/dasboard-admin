<header class="bg-white shadow-sm py-4 px-8 flex justify-between items-center z-10 sticky top-0">
    <div class="flex items-center">
        <button id="mobileMenuBtn" class="md:hidden mr-4 text-gray-600 hover:text-indigo-600 focus:outline-none cursor-pointer">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
        </button>
        <h1 class="text-xl font-semibold text-gray-700">@yield('header', 'Dashboard')</h1>
    </div>
    <div class="flex items-center gap-4">
        <span class="text-sm text-gray-500">Welcome, {{ Auth::user()->name }}</span>
    </div>
</header>
