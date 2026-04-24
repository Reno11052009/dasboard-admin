<aside id="sidebar" class="w-64 bg-slate-800 text-white flex-shrink-0 flex flex-col fixed inset-y-0 left-0 transform -translate-x-full md:relative md:translate-x-0 transition-transform duration-200 ease-in-out z-50 h-screen">
    <div class="p-6 flex items-center justify-between border-b border-slate-700">
        <a href="/admin" class="text-xl font-bold">DASBOARD ADMIN</a>
        <button id="closeSidebarBtn" class="md:hidden text-slate-300 hover:text-white cursor-pointer">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>
    <nav class="flex-1 mt-4 px-4 space-y-2">
        <p class="text-xs uppercase text-slate-400 font-semibold leading-6">main</p>
        <a href="{{ route('index') }}" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-slate-700">Dashboard</a>
        @auth
        @if(Auth::user()->role === 'super_admin')
        <a href="{{ route('users') }}" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-slate-700">Users</a>
        @endif
        @endauth
        <a href="{{ route('products') }}" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-slate-700">Products</a>
        <a href="{{ route('orders') }}" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-slate-700">Orders</a>
        <p class="text-xs uppercase text-slate-400 font-semibold leading-6">settings</p>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="w-full text-left py-2.5 px-4 rounded transition duration-200 hover:bg-slate-700 cursor-pointer">Logout</button>
        </form>
    </nav>
</aside>
