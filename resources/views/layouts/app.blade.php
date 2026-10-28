<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem UKT')</title>

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap"
        rel="stylesheet">

    @stack('styles')

    <!-- Vite Styles -->
    @vite(['resources/css/layout.css'])

    {{-- Variabel dinamis Blade yang di-inject terpisah --}}
    <style>
        :root {
            --sidebar-bg-image: url('{{ asset('images/login-bg.png') }}');
        }
    </style>
</head>

<body>
    <!-- ============================================
    SIDEBAR OVERLAY (Mobile)
    ============================================ -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="d-flex">
        <!-- ============================================
        SIDEBAR
        ============================================ -->
        <aside class="sidebar" id="sidebar">
            <!-- Brand -->
            <div class="sidebar-brand">
                <div class="brand-text">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo Politeknik Negeri Indramayu" class="brand-logo-img">
                    <span>Sistem UKT</span>
                </div>
                <button class="sidebar-close-btn" id="sidebarCloseBtn" aria-label="Tutup sidebar">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <!-- Navigation -->
            @include('layouts.partials.sidebar-' . auth()->user()->getRoleNames()->first())

            <!-- Footer -->
            <div class="sidebar-footer">
                <div class="user-profile-card">
                    <div class="avatar">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <div class="flex-grow-1" style="min-width:0;">
                        <div class="user-name">{{ auth()->user()->name }}</div>
                        <div class="user-role">{{ auth()->user()->getRoleNames()->first() }}</div>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-outline-light w-100"
                        style="border-radius:40px; padding:7px; font-size:0.75rem;">
                        <i class="bi bi-box-arrow-left me-1"></i> Keluar
                    </button>
                </form>
            </div>
        </aside>

        <!-- ============================================
        MAIN CONTENT
        ============================================ -->
        <main class="main-content">
            <!-- Navbar -->
            <header class="navbar-top">
                <div class="navbar-left">
                    <button class="hamburger-btn" id="hamburgerBtn" aria-label="Buka sidebar">
                        <i class="bi bi-list"></i>
                    </button>
                    <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
                </div>
                <div class="navbar-right">
                    <!-- Notifikasi -->
                    <div class="dropdown">
                        <button class="notif-btn" data-bs-toggle="dropdown" aria-label="Notifikasi">
                            <i class="bi bi-bell"></i>
                            <span class="notif-badge" id="notif-count"></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg" id="notif-list"
                            style="width:380px; max-height:480px; overflow-y:auto; border:none; border-radius:16px; padding:0;">
                            <li class="dropdown-item text-muted text-center py-4" style="font-size:0.85rem;">
                                <i class="bi bi-bell-slash me-2"></i> Tidak ada notifikasi
                            </li>
                        </ul>
                    </div>

                    <!-- Role Badge -->
                    <div class="role-badge">
                        <i class="bi bi-shield-check"></i>
                        <span>{{ ucfirst(auth()->user()->getRoleNames()->first()) }}</span>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <div class="p-4">
                <!-- Flash Messages (SweetAlert2 Toast) -->
                @php($__flash = ['success' => session('success'), 'error' => session('error'), 'info' => session('info')])
                @if ($__flash['success'] || $__flash['error'] || $__flash['info'])
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 4000,
                                timerProgressBar: true,
                                didOpen: function(el) {
                                    el.addEventListener('mouseenter', Swal.stopTimer);
                                    el.addEventListener('mouseleave', Swal.resumeTimer);
                                }
                            });
                            @if ($__flash['success'])
                                Toast.fire({
                                    icon: 'success',
                                    title: @json($__flash['success'])
                                });
                            @endif
                            @if ($__flash['error'])
                                Toast.fire({
                                    icon: 'error',
                                    title: @json($__flash['error'])
                                });
                            @endif
                            @if ($__flash['info'])
                                Toast.fire({
                                    icon: 'info',
                                    title: @json($__flash['info'])
                                });
                            @endif
                        });
                    </script>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    <!-- ============================================
    SCRIPTS
    ============================================ -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- ===== Global SweetAlert2 confirm helper ===== -->
    <script>
        window.swalConfirm = function(message, opts) {
            opts = opts || {};
            return Swal.fire({
                title: opts.title || 'Konfirmasi',
                html: String(message).replace(/\n/g, '<br>'),
                icon: opts.icon || 'question',
                showCancelButton: true,
                confirmButtonText: opts.confirmButtonText || 'Ya, lanjutkan',
                cancelButtonText: opts.cancelButtonText || 'Batal',
                confirmButtonColor: opts.confirmButtonColor || '#0d6efd',
                cancelButtonColor: '#6c757d',
                reverseButtons: true
            }).then(function(r) {
                return r.isConfirmed;
            });
        };
        // Ganti confirm() bawaan browser: form/tombol dengan [data-confirm]
        document.addEventListener('submit', function(e) {
            var form = e.target;
            if (!form || form.tagName !== 'FORM') return;
            var msg = (e.submitter && e.submitter.getAttribute('data-confirm')) || form.getAttribute(
            'data-confirm');
            if (!msg) return;
            e.preventDefault();
            window.swalConfirm(msg).then(function(ok) {
                if (ok) form.submit();
            });
        }, true);
    </script>

    <script>
        // ============================================
        // SIDEBAR TOGGLE
        // ============================================
        (function() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const hamburgerBtn = document.getElementById('hamburgerBtn');
            const closeBtn = document.getElementById('sidebarCloseBtn');

            function openSidebar() {
                sidebar.classList.add('show');
                overlay.classList.add('show');
                document.body.style.overflow = 'hidden';
            }

            function closeSidebar() {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
                document.body.style.overflow = '';
            }

            if (hamburgerBtn) hamburgerBtn.addEventListener('click', openSidebar);
            if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
            if (overlay) overlay.addEventListener('click', closeSidebar);

            // Tutup sidebar saat klik link di mobile
            document.querySelectorAll('.sidebar .nav-link').forEach(function(link) {
                link.addEventListener('click', function() {
                    if (window.innerWidth <= 768) closeSidebar();
                });
            });

            // Tutup saat resize ke desktop
            window.addEventListener('resize', function() {
                if (window.innerWidth > 768) {
                    closeSidebar();
                    document.body.style.overflow = '';
                }
            });
        })();

        // ============================================
        // NOTIFICATION SYSTEM
        // ============================================
        (function() {
            const notifList = document.getElementById('notif-list');
            const notifBadge = document.getElementById('notif-count');
            const csrf = document.querySelector('meta[name="csrf-token"]');

            // Set badge SELALU dari angka yang dihitung server (sinkron DB).
            function setBadge(count) {
                count = parseInt(count) || 0;
                if (!notifBadge) return;
                if (count > 0) {
                    notifBadge.textContent = count > 99 ? '99+' : count;
                    notifBadge.style.display = 'block';
                } else {
                    notifBadge.textContent = '';
                    notifBadge.style.display = 'none';
                }
            }

            async function loadNotifikasi() {
                try {
                    const res = await fetch('/api/notifikasi', { headers: { 'Accept': 'application/json' } });
                    const data = await res.json();
                    const list = Array.isArray(data) ? data : (data.notifikasi || []);

                    // Badge dari server (bukan tebakan sisi klien).
                    const serverCount = Array.isArray(data)
                        ? list.filter(n => !n.dibaca).length
                        : (data.jumlah_belum_dibaca || 0);
                    setBadge(serverCount);

                    if (!notifList) return;

                    if (!list || list.length === 0) {
                        notifList.innerHTML =
                            '<li class="dropdown-item text-muted text-center py-4" style="font-size:0.85rem;"><i class="bi bi-bell-slash me-2"></i> Tidak ada notifikasi</li>';
                        return;
                    }

                    let html = `
                        <li class="px-3 py-2 border-bottom d-flex justify-content-between align-items-center">
                            <span class="fw-semibold small">
                                <i class="bi bi-bell-fill me-1"></i> Notifikasi Terbaru
                            </span>
                            <button onclick="window.bacaSemuaNotif()"
                                class="btn btn-link btn-sm p-0 text-primary text-decoration-none small fw-semibold">
                                Tandai semua dibaca
                            </button>
                        </li>
                    `;

                    list.forEach(function(n) {
                        const isUnread = !n.dibaca;
                        html += `
                            <li class="dropdown-item py-3 px-3"
                                style="background:${isUnread ? '#fef9e3' : '#fff'}; cursor:pointer; border-bottom:1px solid #f1f5f9;"
                                onmouseenter="this.style.backgroundColor='${isUnread ? '#fef3c7' : '#f8fafc'}'"
                                onmouseleave="this.style.backgroundColor='${isUnread ? '#fef9e3' : '#fff'}'"
                                onclick="window.bacaNotif(${n.id})">
                                <div class="d-flex align-items-start gap-2">
                                    <div class="flex-shrink-0 mt-1">
                                        <i class="${n.ikon || 'bi bi-bell'}"
                                            style="color:${isUnread ? '#f59e0b' : '#94a3b8'}; font-size:14px;"></i>
                                    </div>
                                    <div class="flex-grow-1" style="min-width:0;">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <strong class="small" style="color:${isUnread ? '#1f2937' : '#6b7280'};
                                                font-weight:${isUnread ? '600' : '400'};">
                                                ${n.judul}
                                            </strong>
                                            ${isUnread ? '<span class="badge bg-primary rounded-pill flex-shrink-0" style="width:8px;height:8px;padding:0;"></span>' : ''}
                                        </div>
                                        <p class="small mb-1 mt-1" style="color:#6b7280; line-height:1.4; word-break:break-word;">
                                            ${n.pesan}
                                        </p>
                                        <small style="color:#9ca3af; font-size:10px;">
                                            ${n.created_at ? new Date(n.created_at).toLocaleString('id-ID', {
                                                day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit'
                                            }) : ''}
                                        </small>
                                    </div>
                                </div>
                            </li>
                        `;
                    });

                    notifList.innerHTML = html;
                } catch (e) {
                    console.warn('Notifikasi error:', e);
                }
            }

            // Sinkron badge ringan tanpa render ulang daftar (polling murah).
            async function refreshBadge() {
                try {
                    const res = await fetch('/api/notifikasi/jumlah', { headers: { 'Accept': 'application/json' } });
                    const data = await res.json();
                    setBadge(data.jumlah_belum_dibaca || 0);
                } catch (e) { /* diamkan */ }
            }

            window.bacaNotif = async function(id) {
                try {
                    const res = await fetch('/api/notifikasi/' + id + '/baca', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrf ? csrf.content : '',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    });
                    const data = await res.json().catch(() => ({}));
                    // Badge dari DB + render ulang daftar agar status konsisten.
                    if (data && typeof data.jumlah_belum_dibaca !== 'undefined') setBadge(data.jumlah_belum_dibaca);
                    await loadNotifikasi();
                } catch (e) {
                    console.error('Gagal menandai notifikasi:', e);
                }
            };

            window.bacaSemuaNotif = async function() {
                try {
                    const res = await fetch('/api/notifikasi/baca-semua', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrf ? csrf.content : '',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    });
                    const data = await res.json().catch(() => ({}));
                    setBadge(data && typeof data.jumlah_belum_dibaca !== 'undefined' ? data.jumlah_belum_dibaca : 0);
                    await loadNotifikasi();
                } catch (e) {
                    console.error('Gagal menandai semua notifikasi:', e);
                }
            };

            // Polling interval 60 detik saat tab aktif, hentikan saat tab background
            let pollingTimer = null;

            function startPolling() {
                stopPolling();
                pollingTimer = setInterval(function() {
                    if (document.visibilityState === 'visible') {
                        refreshBadge();
                    }
                }, 60000);
            }

            function stopPolling() {
                if (pollingTimer) {
                    clearInterval(pollingTimer);
                    pollingTimer = null;
                }
            }

            // Muat awal saat halaman dibuka
            loadNotifikasi();
            startPolling();

            // Tangani status visibilitas tab (Page Visibility API)
            document.addEventListener('visibilitychange', function() {
                if (document.visibilityState === 'visible') {
                    refreshBadge();
                    startPolling();
                } else {
                    stopPolling();
                }
            });

            const notifBtn = document.querySelector('.notif-btn');
            if (notifBtn) notifBtn.addEventListener('click', loadNotifikasi);
        })();

        // ============================================
        // PAGE LOAD ANIMATION
        // ============================================
        document.addEventListener('DOMContentLoaded', function() {
            const content = document.querySelector('.main-content .p-4');
            if (content) {
                content.style.opacity = '0';
                setTimeout(function() {
                    content.style.transition = 'opacity 0.4s ease';
                    content.style.opacity = '1';
                }, 50);
            }
        });
    </script>

    @stack('scripts')
</body>

</html>
