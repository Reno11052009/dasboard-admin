<aside class="w-64 bg-slate-800 text-white flex-shrink-0 hidden md:flex flex-col">
    <div class="p-6 text-2xl font-bold text-center border-b border-slate-700">
        DASBOARD ADMIN
    </div>
    <nav class="flex-1 mt-4 px-4 space-y-2">
        <p class="text-xs uppercase text-slate-400 font-semibold leading-6">main</p>
        <a href="{{ route('index') }}" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-slate-700">Dashboard</a>
        <a href="{{ route('users') }}" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-slate-700">Users</a>
        <a href="{{ route('products') }}" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-slate-700">Products</a>
        <a href="{{ route('orders') }}" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-slate-700">Orders</a>
        <p class="text-xs uppercase text-slate-400 font-semibold leading-6">settings</p>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="w-full text-left py-2.5 px-4 rounded transition duration-200 hover:bg-slate-700 cursor-pointer">Logout</button>
        </form>
    </nav>
</aside>
