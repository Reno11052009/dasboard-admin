<header class="bg-white shadow-sm py-4 px-8 flex justify-between items-center z-10 sticky top-0">
    <div class="flex items-center">
        <button id="mobileMenuBtn" class="md:hidden mr-4 text-gray-600 hover:text-indigo-600 focus:outline-none cursor-pointer">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
        </button>
        <h1 class="text-xl font-semibold text-gray-700">@yield('header', 'Dashboard')</h1>
    </div>
    <div class="flex items-center gap-2 relative">
        <!-- Notification Button -->
        <button id="notificationBtn" class="relative p-2 text-gray-400 hover:text-indigo-600 focus:outline-none transition-colors cursor-pointer rounded-full hover:bg-gray-100">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <!-- Badge -->
            <span class="absolute top-1 right-1 flex items-center justify-center w-4 h-4 text-[10px] font-bold text-white bg-red-500 rounded-full border-2 border-white">3</span>
        </button>

        <!-- Notification Dropdown -->
        <div id="notificationDropdown" class="hidden absolute top-full right-0 mt-2 w-80 bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden z-50 transform origin-top-right transition-all">
            <div class="px-4 py-3 border-b border-gray-50 flex justify-between items-center bg-gray-50/80 backdrop-blur-sm">
                <h3 class="text-sm font-bold text-gray-800">Notifikasi</h3>
                <span class="text-xs text-indigo-600 hover:text-indigo-800 cursor-pointer font-medium transition-colors">Tandai semua dibaca</span>
            </div>
            <div class="max-h-80 overflow-y-auto divide-y divide-gray-50">
                <!-- Item 1 (Unread) -->
                <a href="#" class="block px-4 py-3 hover:bg-gray-50 transition bg-indigo-50/30">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-800 font-semibold">Pesanan Baru #ORD-092</p>
                            <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">Budi Santoso baru saja membuat pesanan baru sebesar Rp 250.000.</p>
                            <p class="text-[10px] font-medium text-indigo-500 mt-1.5">2 menit yang lalu</p>
                        </div>
                        <div class="w-2 h-2 bg-indigo-500 rounded-full mt-1.5 flex-shrink-0"></div>
                    </div>
                </a>
                <!-- Item 2 -->
                <a href="#" class="block px-4 py-3 hover:bg-gray-50 transition">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-orange-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-800 font-medium">Stok Menipis</p>
                            <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">Produk "Kemeja Flanel" tersisa 3 item di gudang.</p>
                            <p class="text-[10px] font-medium text-gray-400 mt-1.5">1 jam yang lalu</p>
                        </div>
                    </div>
                </a>
                <!-- Item 3 -->
                <a href="#" class="block px-4 py-3 hover:bg-gray-50 transition">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-800 font-medium">Pengguna Baru</p>
                            <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">Siti Aminah baru saja mendaftar sebagai pelanggan.</p>
                            <p class="text-[10px] font-medium text-gray-400 mt-1.5">Kemarin</p>
                        </div>
                    </div>
                </a>
            </div>
            <div class="px-4 py-3 border-t border-gray-50 text-center bg-gray-50/50 hover:bg-gray-100 transition cursor-pointer">
                <a href="#" class="text-xs font-bold text-indigo-600">Lihat Semua Notifikasi</a>
            </div>
        </div>

        <span class="text-sm font-medium text-gray-600 ml-2 pl-2 border-l border-gray-200">Hi, {{ explode(' ', Auth::user()->name)[0] }}</span>
    </div>
</header>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const notifBtn = document.getElementById('notificationBtn');
        const notifDropdown = document.getElementById('notificationDropdown');
        
        if(notifBtn && notifDropdown) {
            notifBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                notifDropdown.classList.toggle('hidden');
            });
            
            document.addEventListener('click', function(e) {
                if (!notifDropdown.contains(e.target) && !notifBtn.contains(e.target)) {
                    notifDropdown.classList.add('hidden');
                }
            });
        }
    });
</script>
