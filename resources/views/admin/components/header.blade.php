<header class="bg-white shadow-sm py-4 px-8 flex justify-between items-center z-10 sticky top-0">
    <div class="flex items-center">
        <button id="mobileMenuBtn" class="md:hidden mr-4 text-gray-600 hover:text-indigo-600 focus:outline-none cursor-pointer">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
        </button>
        <h1 class="text-xl font-semibold text-gray-700">@yield('header', 'Dashboard')</h1>
    </div>
    <div class="flex items-center gap-2 relative">
        @php
            $notifications = auth()->user()->unreadNotifications;
            $unreadCount = $notifications->count();
        @endphp
        <!-- Notification Button -->
        <button id="notificationBtn" class="relative p-2 text-gray-400 hover:text-indigo-600 focus:outline-none transition-colors cursor-pointer rounded-full hover:bg-gray-100">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <!-- Badge -->
            @if($unreadCount > 0)
            <span id="notifBadge" class="absolute top-1 right-1 flex items-center justify-center w-4 h-4 text-[10px] font-bold text-white bg-red-500 rounded-full border-2 border-white">{{ $unreadCount }}</span>
            @endif
        </button>

        <!-- Notification Dropdown -->
        <div id="notificationDropdown" class="hidden absolute top-full right-0 mt-2 w-80 bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden z-50 transform origin-top-right transition-all">
            <div class="px-4 py-3 border-b border-gray-50 flex justify-between items-center bg-gray-50/80 backdrop-blur-sm">
                <h3 class="text-sm font-bold text-gray-800">Notifikasi</h3>
                <button id="markAllReadHeaderBtn" onclick="markAllAsRead()" class="text-xs text-indigo-600 hover:text-indigo-800 cursor-pointer font-medium transition-colors focus:outline-none {{ $unreadCount > 0 ? '' : 'hidden' }}">Tandai semua dibaca</button>
            </div>
            <div class="max-h-80 overflow-y-auto divide-y divide-gray-50">
                @forelse($notifications as $notification)
                <a href="{{ $notification->data['url'] ?? '#' }}" class="block px-4 py-3 hover:bg-gray-50 transition bg-indigo-50/30">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-800 font-semibold">{{ $notification->data['type'] === 'product_approval' ? 'Persetujuan Produk' : ($notification->data['type'] === 'product_status_updated' ? 'Status Produk' : 'Notifikasi') }}</p>
                            <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">{{ $notification->data['message'] ?? 'Ada aktivitas baru.' }}</p>
                            <p class="text-[10px] font-medium text-indigo-500 mt-1.5">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>
                        <div class="w-2 h-2 bg-indigo-500 rounded-full mt-1.5 flex-shrink-0"></div>
                    </div>
                </a>
                @empty
                <div class="px-4 py-6 text-center text-sm text-gray-500">
                    Tidak ada notifikasi baru
                </div>
                @endforelse
            </div>
            <div id="detailNotifBtn" class="px-4 py-3 border-t border-gray-50 text-center bg-gray-50/50 hover:bg-gray-100 transition cursor-pointer {{ $unreadCount > 0 ? '' : 'hidden' }}" onclick="openNotifModal()">
                <button class="text-xs font-bold text-indigo-600 focus:outline-none">Lihat Detail Notif</button>
            </div>
        </div>

        <span class="text-sm font-medium text-gray-600 ml-2 pl-2 border-l border-gray-200">Hi, {{ explode(' ', Auth::user()->name)[0] }}</span>
    </div>
</header>

<!-- Notification Details Modal -->
<div id="notifModal" class="fixed inset-0 z-[100] hidden">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity" onclick="closeNotifModal()"></div>

    <!-- Modal Panel -->
    <div class="fixed inset-0 flex items-center justify-center p-4 z-10">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[85vh] flex flex-col transform transition-all">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/80 rounded-t-2xl">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800">Detail Notifikasi</h3>
                </div>
                <button onclick="closeNotifModal()" class="text-gray-400 hover:text-gray-600 p-1 rounded-lg hover:bg-gray-100 focus:outline-none transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="px-6 py-4 overflow-y-auto flex-1">
                @if($unreadCount > 0)
                    <div class="space-y-4">
                        @foreach($notifications as $notification)
                        <div class="p-5 border border-gray-100 rounded-xl bg-gray-50/50 hover:bg-white hover:shadow-sm transition duration-200">
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                </div>
                                <div class="flex-1">
                                    <div class="flex justify-between items-start mb-1.5">
                                        <h4 class="text-sm font-bold text-gray-800">{{ $notification->data['type'] === 'product_approval' ? 'Persetujuan Produk' : ($notification->data['type'] === 'product_status_updated' ? 'Status Produk' : 'Pemberitahuan Sistem') }}</h4>
                                        <span class="text-[11px] font-medium text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-full">{{ $notification->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-sm text-gray-600 leading-relaxed">{{ $notification->data['message'] ?? 'Ada aktivitas baru yang memerlukan perhatian Anda.' }}</p>
                                    @if(isset($notification->data['url']))
                                    <div class="mt-4">
                                        <a href="{{ $notification->data['url'] }}" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition-colors">
                                            Tindak Lanjuti <svg class="w-3.5 h-3.5 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                        </a>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="py-12 text-center">
                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
                        </div>
                        <p class="text-gray-500 font-medium text-sm">Tidak ada notifikasi baru saat ini.</p>
                    </div>
                @endif
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-3 bg-gray-50/80 rounded-b-2xl">
                <button onclick="closeNotifModal()" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 focus:outline-none transition cursor-pointer">Tutup</button>
                <button id="markAllReadFooterBtn" onclick="markAllAsRead()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 focus:outline-none transition cursor-pointer shadow-sm {{ $unreadCount > 0 ? '' : 'hidden' }}">Tandai Semua Dibaca</button>
            </div>
        </div>
    </div>
</div>

<script>
    function openNotifModal() {
        const modal = document.getElementById('notifModal');
        if (modal) {
            modal.classList.remove('hidden');
            const dropdown = document.getElementById('notificationDropdown');
            if (dropdown) dropdown.classList.add('hidden');
        }
    }

    function closeNotifModal() {
        const modal = document.getElementById('notifModal');
        if (modal) {
            modal.classList.add('hidden');
        }
    }

    function markAllAsRead() {
        fetch('{{ route("notifications.markRead") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        }).then(response => response.json())
          .then(data => {
              if (data.success) {
                  const badge = document.getElementById('notifBadge');
                  if (badge) badge.remove();
                  window.location.reload();
              }
          });
    }

    function fetchNotifications() {
        fetch('{{ route("notifications.unread") }}', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            updateNotificationUI(data.count, data.notifications);
        })
        .catch(error => console.error('Error fetching notifications:', error));
    }

    function updateNotificationUI(count, notifications) {
        let badge = document.getElementById('notifBadge');
        if (count > 0) {
            if (!badge) {
                const notifBtn = document.getElementById('notificationBtn');
                if (notifBtn) {
                    badge = document.createElement('span');
                    badge.id = 'notifBadge';
                    badge.className = 'absolute top-1 right-1 flex items-center justify-center w-4 h-4 text-[10px] font-bold text-white bg-red-500 rounded-full border-2 border-white';
                    notifBtn.appendChild(badge);
                }
            }
            if (badge) badge.innerText = count;
        } else {
            if (badge) badge.remove();
        }

        const dropdownList = document.querySelector('#notificationDropdown .max-h-80');
        if (dropdownList) {
            if (count > 0) {
                dropdownList.innerHTML = notifications.map(notif => `
                    <a href="${notif.data.url ?? '#'}" class="block px-4 py-3 hover:bg-gray-50 transition bg-indigo-50/30">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-800 font-semibold">${notif.data.type === 'product_approval' ? 'Persetujuan Produk' : (notif.data.type === 'product_status_updated' ? 'Status Produk' : 'Notifikasi')}</p>
                                <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">${notif.data.message ?? 'Ada aktivitas baru.'}</p>
                                <p class="text-[10px] font-medium text-indigo-500 mt-1.5">${notif.created_at}</p>
                            </div>
                            <div class="w-2 h-2 bg-indigo-500 rounded-full mt-1.5 flex-shrink-0"></div>
                        </div>
                    </a>
                `).join('');
            } else {
                dropdownList.innerHTML = `
                    <div class="px-4 py-6 text-center text-sm text-gray-500">
                        Tidak ada notifikasi baru
                    </div>
                `;
            }
        }

        const modalList = document.querySelector('#notifModal .overflow-y-auto');
        if (modalList) {
            if (count > 0) {
                modalList.innerHTML = `
                    <div class="space-y-4">
                        ${notifications.map(notif => `
                        <div class="p-5 border border-gray-100 rounded-xl bg-gray-50/50 hover:bg-white hover:shadow-sm transition duration-200">
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                </div>
                                <div class="flex-1">
                                    <div class="flex justify-between items-start mb-1.5">
                                        <h4 class="text-sm font-bold text-gray-800">${notif.data.type === 'product_approval' ? 'Persetujuan Produk' : (notif.data.type === 'product_status_updated' ? 'Status Produk' : 'Pemberitahuan Sistem')}</h4>
                                        <span class="text-[11px] font-medium text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-full">${notif.created_at}</span>
                                    </div>
                                    <p class="text-sm text-gray-600 leading-relaxed">${notif.data.message ?? 'Ada aktivitas baru yang memerlukan perhatian Anda.'}</p>
                                    ${notif.data.url ? `
                                    <div class="mt-4">
                                        <a href="${notif.data.url}" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition-colors">
                                            Tindak Lanjuti <svg class="w-3.5 h-3.5 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                        </a>
                                    </div>
                                    ` : ''}
                                </div>
                            </div>
                        </div>
                        `).join('')}
                    </div>
                `;
            } else {
                modalList.innerHTML = `
                    <div class="py-12 text-center">
                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
                        </div>
                        <p class="text-gray-500 font-medium text-sm">Tidak ada notifikasi baru saat ini.</p>
                    </div>
                `;
            }
        }

        const headerBtn = document.getElementById('markAllReadHeaderBtn');
        const detailBtn = document.getElementById('detailNotifBtn');
        const footerBtn = document.getElementById('markAllReadFooterBtn');

        if (count > 0) {
            if (headerBtn) headerBtn.classList.remove('hidden');
            if (detailBtn) detailBtn.classList.remove('hidden');
            if (footerBtn) footerBtn.classList.remove('hidden');
        } else {
            if (headerBtn) headerBtn.classList.add('hidden');
            if (detailBtn) detailBtn.classList.add('hidden');
            if (footerBtn) footerBtn.classList.add('hidden');
        }
    }

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

        // Fetch notifications every 5 seconds
        setInterval(fetchNotifications, 5000);
    });
</script>
