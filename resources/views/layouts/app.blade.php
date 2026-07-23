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

    <!-- ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap"
        rel="stylesheet">

    @stack('styles')

    <style>
        /* ============================================
           RESET & BASE
           ============================================ */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #f1f4f9;
            color: #1a2634;
            overflow-x: hidden;
            min-height: 100vh;
        }

        /* ============================================
           MODERN SCROLLBAR
           ============================================ */
        ::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }

        ::-webkit-scrollbar-track {
            background: #e9ecef;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: #c1c9d6;
            border-radius: 10px;
            transition: background 0.2s;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* ============================================
           SIDEBAR
           ============================================ */
        .sidebar {
            width: 280px;
            min-height: 100vh;
            background: linear-gradient(165deg, #0f2b3d 0%, #091b27 100%);
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 1040;
            overflow-y: auto;
            overflow-x: hidden;
            transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 4px 0 40px rgba(0, 0, 0, 0.12);
            display: flex;
            flex-direction: column;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 20px 16px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
            margin-bottom: 8px;
            flex-shrink: 0;
        }

        .sidebar-brand .brand-text {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 800;
            font-size: 1.2rem;
            background: linear-gradient(135deg, #ffffff 0%, #a8c8ff 100%);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -0.3px;
        }

        .sidebar-brand .brand-text i {
            font-size: 1.4rem;
            background: none;
            -webkit-text-fill-color: #3b82f6;
            color: #3b82f6;
        }

        .sidebar-close-btn {
            display: none;
            background: rgba(255, 255, 255, 0.08);
            border: none;
            color: rgba(255, 255, 255, 0.6);
            width: 32px;
            height: 32px;
            border-radius: 10px;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .sidebar-close-btn:hover {
            background: rgba(255, 255, 255, 0.16);
            color: #fff;
            transform: rotate(90deg);
        }

        .sidebar-section {
            color: rgba(255, 255, 255, 0.35);
            font-size: 0.6rem;
            text-transform: uppercase;
            letter-spacing: 1.6px;
            padding: 16px 22px 6px;
            font-weight: 700;
        }

        .sidebar .nav {
            flex: 1;
            padding: 0 8px;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.65);
            border-radius: 12px;
            padding: 10px 16px;
            margin: 2px 0;
            transition: all 0.25s ease;
            font-weight: 500;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar .nav-link:hover {
            background: rgba(255, 255, 255, 0.08);
            color: #ffffff;
            transform: translateX(4px);
        }

        .sidebar .nav-link.active {
            background: rgba(59, 130, 246, 0.18);
            color: #ffffff;
            border-left: 3px solid #3b82f6;
        }

        .sidebar .nav-link i {
            font-size: 1.15rem;
            width: 22px;
            text-align: center;
            flex-shrink: 0;
        }

        /* Sidebar Footer */
        .sidebar-footer {
            padding: 12px 16px 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
            flex-shrink: 0;
        }

        .user-profile-card {
            background: rgba(255, 255, 255, 0.06);
            border-radius: 14px;
            padding: 12px 16px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-profile-card .avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: rgba(59, 130, 246, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .user-profile-card .avatar i {
            font-size: 1.2rem;
            color: #60a5fa;
        }

        .user-profile-card .user-name {
            color: #ffffff;
            font-weight: 600;
            font-size: 0.8rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-profile-card .user-role {
            color: rgba(255, 255, 255, 0.4);
            font-size: 0.6rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* ============================================
           MAIN CONTENT
           ============================================ */
        .main-content {
            margin-left: 280px;
            min-height: 100vh;
            background: #f1f4f9;
            transition: margin-left 0.35s ease;
            width: calc(100% - 280px);
        }

        /* ============================================
           NAVBAR TOP
           ============================================ */
        .navbar-top {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.04);
            padding: 10px 24px;
            position: sticky;
            top: 0;
            z-index: 99;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
        }

        .navbar-left {
            display: flex;
            align-items: center;
            gap: 14px;
            flex: 1;
            min-width: 0;
        }

        .navbar-right {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-shrink: 0;
        }

        .navbar-top .page-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Hamburger Button */
        .hamburger-btn {
            display: none;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 6px 12px;
            font-size: 1.15rem;
            cursor: pointer;
            transition: all 0.2s ease;
            flex-shrink: 0;
            color: #475569;
        }

        .hamburger-btn:hover {
            background: #ffffff;
            border-color: #cbd5e1;
            transform: scale(1.03);
        }

        /* Notification Button */
        .notif-btn {
            border-radius: 40px;
            padding: 6px 12px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            color: #475569;
        }

        .notif-btn:hover {
            background: #ffffff;
            border-color: #cbd5e1;
            transform: scale(1.03);
        }

        .notif-btn i {
            font-size: 1.1rem;
        }

        .notif-badge {
            position: absolute;
            top: -4px;
            right: -4px;
            background: #ef4444;
            color: #fff;
            font-size: 0.55rem;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 40px;
            min-width: 18px;
            text-align: center;
            display: none;
            line-height: 1.4;
            border: 2px solid #f8fafc;
        }

        /* Role Badge */
        .role-badge {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            padding: 5px 14px;
            border-radius: 40px;
            font-weight: 600;
            font-size: 0.7rem;
            color: #fff;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.25);
        }

        .role-badge i {
            font-size: 0.8rem;
        }

        /* ============================================
           SIDEBAR OVERLAY
           ============================================ */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.45);
            z-index: 1039;
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .sidebar-overlay.show {
            display: block;
            opacity: 1;
        }

        /* ============================================
           CARDS
           ============================================ */
        .card {
            border: none;
            border-radius: 20px;
            background: #ffffff;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.07);
            transform: translateY(-2px);
        }

        .card-header {
            background: transparent;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            border-radius: 20px 20px 0 0 !important;
            padding: 18px 22px;
            font-weight: 600;
            font-size: 0.95rem;
        }

        /* ============================================
           ALERTS
           ============================================ */
        .alert {
            border-radius: 16px;
            border: none;
            padding: 14px 20px;
            font-weight: 500;
            backdrop-filter: blur(4px);
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border-left: 4px solid #10b981;
        }

        .alert-danger {
            background: #fee2e2;
            color: #991b1b;
            border-left: 4px solid #ef4444;
        }

        .alert-info {
            background: #dbeafe;
            color: #1e40af;
            border-left: 4px solid #3b82f6;
        }

        .alert .btn-close {
            padding: 8px;
        }

        /* ============================================
           TABLES
           ============================================ */
        .table {
            border-collapse: separate;
            border-spacing: 0 6px;
        }

        .table thead th {
            border: none;
            background: transparent;
            font-weight: 600;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #94a3b8;
            padding: 8px 16px;
        }

        .table tbody tr {
            background: #ffffff;
            border-radius: 14px;
            transition: all 0.2s ease;
        }

        .table tbody tr:hover {
            transform: scale(1.01);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
        }

        .table tbody td {
            border: none;
            padding: 12px 16px;
            vertical-align: middle;
            font-size: 0.85rem;
        }

        /* ============================================
           FORM ELEMENTS
           ============================================ */
        .form-control,
        .form-select {
            border-radius: 12px;
            border: 1.5px solid #e2e8f0;
            padding: 10px 16px;
            font-size: 0.85rem;
            transition: all 0.2s ease;
            background: #ffffff;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.08);
            outline: none;
        }

        .form-control::placeholder {
            color: #94a3b8;
        }

        /* ============================================
           BUTTONS
           ============================================ */
        .btn {
            border-radius: 40px;
            padding: 8px 22px;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.25s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            border: none;
            color: #fff;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.35);
            color: #fff;
        }

        .btn-outline-light {
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(4px);
        }

        .btn-outline-light:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.3);
            color: #ffffff;
            transform: translateY(-1px);
        }

        /* ============================================
           DROPDOWN
           ============================================ */
        .dropdown-menu {
            border: none;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
            padding: 8px 0;
            overflow: hidden;
        }

        .dropdown-item {
            padding: 10px 20px;
            font-size: 0.85rem;
            transition: all 0.15s ease;
        }

        .dropdown-item:hover {
            background: #f1f5f9;
        }

        .dropdown-item:active {
            background: #e2e8f0;
        }

        /* ============================================
           ANIMATIONS
           ============================================ */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .main-content .p-4>* {
            animation: fadeInUp 0.4s ease-out forwards;
        }

        /* ============================================
           RESPONSIVE
           ============================================ */

        /* Tablet (max 992px) */
        @media (max-width: 992px) {
            .navbar-top .page-title {
                font-size: 0.95rem;
            }

            .role-badge span {
                display: none;
            }

            .role-badge i {
                margin-right: 0;
            }
        }

        /* Mobile Landscape (max 768px) */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                width: 280px;
            }

            .sidebar.show {
                transform: translateX(0);
                box-shadow: 4px 0 40px rgba(0, 0, 0, 0.25);
            }

            .main-content {
                margin-left: 0;
                width: 100%;
            }

            .navbar-top {
                padding: 8px 16px;
            }

            .navbar-left {
                gap: 10px;
            }

            .navbar-right {
                gap: 8px;
            }

            .navbar-top .page-title {
                font-size: 0.85rem;
            }

            .hamburger-btn {
                display: flex !important;
                align-items: center;
                justify-content: center;
            }

            .sidebar-close-btn {
                display: flex !important;
            }

            .notif-btn {
                padding: 5px 10px;
            }

            .notif-btn i {
                font-size: 1rem;
            }

            .role-badge {
                padding: 4px 10px;
                font-size: 0.65rem;
            }

            .role-badge span {
                display: none;
            }

            .card {
                border-radius: 16px;
            }

            .card-header {
                padding: 14px 18px;
                font-size: 0.85rem;
            }
        }

        /* Mobile Portrait (max 480px) */
        @media (max-width: 480px) {
            .navbar-top {
                padding: 6px 12px;
            }

            .navbar-left {
                gap: 8px;
            }

            .navbar-right {
                gap: 6px;
            }

            .navbar-top .page-title {
                font-size: 0.75rem;
                max-width: 140px;
            }

            .hamburger-btn {
                padding: 4px 10px;
                font-size: 1rem;
            }

            .notif-btn {
                padding: 4px 8px;
            }

            .notif-btn i {
                font-size: 0.9rem;
            }

            .role-badge {
                padding: 3px 8px;
                font-size: 0.6rem;
            }

            .role-badge i {
                font-size: 0.7rem;
            }

            .main-content .p-4 {
                padding: 12px !important;
            }

            .sidebar {
                width: 260px;
            }

            .sidebar-brand {
                padding: 16px 16px 12px;
            }

            .sidebar-brand .brand-text {
                font-size: 1rem;
            }

            .sidebar-brand .brand-text i {
                font-size: 1.2rem;
            }

            .sidebar .nav-link {
                padding: 8px 14px;
                font-size: 0.8rem;
            }

            .sidebar .nav-link i {
                font-size: 1rem;
                width: 20px;
            }

            .sidebar-section {
                padding: 12px 18px 4px;
                font-size: 0.55rem;
            }

            .user-profile-card {
                padding: 10px 12px;
            }

            .user-profile-card .user-name {
                font-size: 0.75rem;
            }

            .btn-outline-light {
                font-size: 0.7rem;
                padding: 6px 12px;
            }

            .dropdown-menu {
                width: calc(100vw - 32px) !important;
                min-width: unset !important;
                right: 16px !important;
                left: 16px !important;
            }
        }

        /* Extra Small (max 360px) */
        @media (max-width: 360px) {
            .navbar-top .page-title {
                font-size: 0.65rem;
                max-width: 100px;
            }

            .hamburger-btn {
                padding: 3px 8px;
                font-size: 0.9rem;
            }

            .notif-btn {
                padding: 3px 6px;
            }

            .notif-btn i {
                font-size: 0.8rem;
            }

            .role-badge {
                padding: 2px 6px;
                font-size: 0.55rem;
            }

            .sidebar {
                width: 240px;
            }

            .sidebar-brand .brand-text {
                font-size: 0.9rem;
            }

            .sidebar .nav-link {
                padding: 6px 12px;
                font-size: 0.75rem;
            }

            .sidebar .nav-link i {
                font-size: 0.9rem;
                width: 18px;
            }

            .user-profile-card .user-name {
                font-size: 0.65rem;
            }

            .main-content .p-4 {
                padding: 8px !important;
            }
        }

        /* Desktop: hamburger & close hidden */
        @media (min-width: 769px) {
            .hamburger-btn {
                display: none !important;
            }

            .sidebar-close-btn {
                display: none !important;
            }
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
                    <i class="bi bi-mortarboard-fill"></i>
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

            async function loadNotifikasi() {
                try {
                    const res = await fetch('/api/notifikasi');
                    const data = await res.json();

                    const unread = data.filter(function(n) {
                        return !n.dibaca;
                    });

                    // Update badge
                    if (unread.length > 0) {
                        notifBadge.textContent = unread.length;
                        notifBadge.style.display = 'block';
                    } else {
                        notifBadge.style.display = 'none';
                    }

                    if (!notifList) return;

                    if (!data || data.length === 0) {
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

                    data.forEach(function(n) {
                        const isUnread = !n.dibaca;
                        html += `
                            <li class="dropdown-item py-3 px-3"
                                style="background:${isUnread ? '#fef9e3' : '#fff'}; cursor:pointer; border-bottom:1px solid #f1f5f9;"
                                onmouseenter="this.style.backgroundColor='${isUnread ? '#fef3c7' : '#f8fafc'}'"
                                onmouseleave="this.style.backgroundColor='${isUnread ? '#fef9e3' : '#fff'}'"
                                onclick="window.bacaNotif(${n.id}, this)">
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
                                            ${new Date(n.created_at).toLocaleString('id-ID', {
                                                day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit'
                                            })}
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

            window.bacaNotif = async function(id, el) {
                try {
                    await fetch('/api/notifikasi/' + id + '/baca', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json'
                        }
                    });

                    // Update tampilan
                    el.style.backgroundColor = '#fff';
                    el.onmouseenter = null;
                    el.onmouseleave = null;

                    const dot = el.querySelector('.badge.bg-primary');
                    if (dot) dot.remove();

                    const judul = el.querySelector('strong');
                    if (judul) {
                        judul.style.color = '#6b7280';
                        judul.style.fontWeight = '400';
                    }

                    // Update badge
                    const current = parseInt(notifBadge.textContent) || 0;
                    if (current > 1) {
                        notifBadge.textContent = current - 1;
                    } else {
                        notifBadge.style.display = 'none';
                    }

                } catch (e) {
                    console.error('Gagal menandai notifikasi:', e);
                }
            };

            window.bacaSemuaNotif = async function() {
                try {
                    await fetch('/api/notifikasi/baca-semua', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json'
                        }
                    });

                    // Update semua item
                    document.querySelectorAll('#notif-list li.dropdown-item').forEach(function(item) {
                        // Skip header
                        if (item.querySelector('.fw-semibold')) return;

                        item.style.backgroundColor = '#fff';
                        item.onmouseenter = null;
                        item.onmouseleave = null;

                        const dot = item.querySelector('.badge.bg-primary');
                        if (dot) dot.remove();

                        const judul = item.querySelector('strong');
                        if (judul) {
                            judul.style.color = '#6b7280';
                            judul.style.fontWeight = '400';
                        }
                    });

                    notifBadge.style.display = 'none';

                } catch (e) {
                    console.error('Gagal menandai semua notifikasi:', e);
                }
            };

            // Load & interval
            loadNotifikasi();
            setInterval(loadNotifikasi, 30000);
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
