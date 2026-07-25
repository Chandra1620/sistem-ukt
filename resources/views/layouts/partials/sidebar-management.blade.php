<nav class="nav flex-column px-3 py-2">
    {{-- Bagian Menu Utama --}}
    <div class="sidebar-section d-flex align-items-center gap-2 mb-2 mt-2">
        <div class="section-indicator"></div>
        <span>Menu Utama</span>
    </div>

    <a href="{{ route('management.dashboard') }}"
        class="nav-link {{ request()->routeIs('management.dashboard') ? 'active' : '' }}">
        <div class="nav-icon-wrapper">
            <i class="bi bi-speedometer2"></i>
        </div>
        <span class="nav-text">Dashboard</span>
        @if (request()->routeIs('management.dashboard'))
            <div class="nav-active-indicator"></div>
        @endif
    </a>

    {{-- Bagian Analitik --}}
    <div class="sidebar-section d-flex align-items-center gap-2 mt-3 mb-2">
        <div class="section-indicator"></div>
        <span>Analitik</span>
    </div>

    <a href="{{ route('management.analitik.index') }}"
        class="nav-link analitik-link {{ request()->routeIs('management.analitik.*') ? 'active' : '' }}">
        <div class="nav-icon-wrapper">
            <i class="bi bi-graph-up"></i>
        </div>
        <span class="nav-text nav-text-multiline">
            <span class="nav-line">Analitik</span>
            <span class="nav-line">Lengkap</span>
        </span>
        @php
            $totalData = \App\Models\Pendaftaran::count();
        @endphp
        @if ($totalData > 0)
            <div class="nav-badge badge-analitik">
                <i class="bi bi-bar-chart-steps"></i>
                {{ $totalData }}
            </div>
        @endif
        @if (request()->routeIs('management.analitik.*'))
            <div class="nav-active-indicator"></div>
        @endif
    </a>

    {{-- Bagian Informasi --}}
    <div class="sidebar-section d-flex align-items-center gap-2 mt-3 mb-2">
        <div class="section-indicator"></div>
        <span>Informasi</span>
    </div>

    <a href="{{ route('management.distribusi-ukt.index') }}"
        class="nav-link distribusi-link {{ request()->routeIs('management.distribusi-ukt.*') ? 'active' : '' }}">
        <div class="nav-icon-wrapper">
            <i class="bi bi-pie-chart-fill"></i>
        </div>
        <span class="nav-text nav-text-multiline">
            <span class="nav-line">Distribusi</span>
            <span class="nav-line">UKT</span>
        </span>
        @if (request()->routeIs('management.distribusi-ukt.*'))
            <div class="nav-active-indicator"></div>
        @endif
    </a>

    <a href="{{ route('management.statistik-prodi.index') }}"
        class="nav-link statistik-link {{ request()->routeIs('management.statistik-prodi.*') ? 'active' : '' }}">
        <div class="nav-icon-wrapper">
            <i class="bi bi-bar-chart-steps"></i>
        </div>
        <span class="nav-text nav-text-multiline">
            <span class="nav-line">Statistik</span>
            <span class="nav-line">Prodi</span>
        </span>
        @if (request()->routeIs('management.statistik-prodi.*'))
            <div class="nav-active-indicator"></div>
        @endif
    </a>

    <a href="{{ route('management.trend-tahunan.index') }}"
        class="nav-link trend-link {{ request()->routeIs('management.trend-tahunan.*') ? 'active' : '' }}">
        <div class="nav-icon-wrapper">
            <i class="bi bi-graph-up-arrow"></i>
        </div>
        <span class="nav-text nav-text-multiline">
            <span class="nav-line">Trend</span>
            <span class="nav-line">Tahunan</span>
        </span>
        @if (request()->routeIs('management.trend-tahunan.*'))
            <div class="nav-active-indicator"></div>
        @endif
    </a>

    <div class="sidebar-section d-flex align-items-center gap-2 mt-3 mb-2">
        <div class="section-indicator"></div>
        <span>Laporan</span>
    </div>

    <a href="{{ route('management.proyeksi-pendapatan.index') }}"
        class="nav-link proyeksi-link {{ request()->routeIs('management.proyeksi-pendapatan.*') ? 'active' : '' }}">
        <div class="nav-icon-wrapper">
            <i class="bi bi-cash-stack"></i>
        </div>
        <span class="nav-text nav-text-multiline">
            <span class="nav-line">Proyeksi</span>
            <span class="nav-line">Pendapatan</span>
        </span>
        @if (request()->routeIs('management.proyeksi-pendapatan.*'))
            <div class="nav-active-indicator"></div>
        @endif
    </a>

    <a href="{{ route('management.analisis-keadilan.index') }}"
        class="nav-link analisis-keadilan-link {{ request()->routeIs('management.analisis-keadilan.*') ? 'active' : '' }}">
        <div class="nav-icon-wrapper">
            <i class="bi bi-shield-check"></i>
        </div>
        <span class="nav-text nav-text-multiline">
            <span class="nav-line">Analisis</span>
            <span class="nav-line">Keadilan</span>
        </span>
        @if (request()->routeIs('management.analisis-keadilan.*'))
            <div class="nav-active-indicator"></div>
        @endif
    </a>

    <a href="{{ route('management.sebaran-geografis.index') }}"
        class="nav-link sebaran-geografis-link {{ request()->routeIs('management.sebaran-geografis.*') ? 'active' : '' }}">
        <div class="nav-icon-wrapper">
            <i class="bi bi-geo-alt"></i>
        </div>
        <span class="nav-text nav-text-multiline">
            <span class="nav-line">Sebaran</span>
            <span class="nav-line">Geografis</span>
        </span>
        @if (request()->routeIs('management.sebaran-geografis.*'))
            <div class="nav-active-indicator"></div>
        @endif
    </a>

    <a href="{{ route('management.export.index') }}"
        class="nav-link {{ request()->routeIs('management.export.*') ? 'active' : '' }}">
        <div class="nav-icon-wrapper">
            <i class="bi bi-download"></i>
        </div>
        <span class="nav-text">Export Data</span>
    </a>

    {{-- Bagian Akun --}}
    <div class="sidebar-section d-flex align-items-center gap-2 mt-3 mb-2">
        <div class="section-indicator"></div>
        <span>Akun</span>
    </div>

    <a href="{{ route('profile.index') }}"
        class="nav-link profile-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
        <div class="nav-icon-wrapper">
            <i class="bi bi-person-circle"></i>
        </div>
        <span class="nav-text">Profil Saya</span>
        @if (request()->routeIs('profile.*'))
            <div class="nav-active-indicator"></div>
        @endif
    </a>
</nav>

<style>
    /* Gaya Navigasi Modern - Management */
    .nav {
        gap: 4px;
    }

    /* Header Bagian */
    .sidebar-section {
        color: rgba(255, 255, 255, 0.45);
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 1.2px;
        font-weight: 700;
        padding: 8px 12px 6px 12px;
        position: relative;
        transition: all 0.3s ease;
    }

    .section-indicator {
        width: 4px;
        height: 4px;
        background: rgba(255, 255, 255, 0.35);
        border-radius: 50%;
        display: inline-block;
        transition: all 0.3s ease;
        flex-shrink: 0;
    }

    .sidebar-section:hover .section-indicator {
        background: linear-gradient(135deg, #3b82f6, #8b5cf6);
        transform: scale(1.3);
        box-shadow: 0 0 8px rgba(59, 130, 246, 0.5);
    }

    /* Link Navigasi */
    .nav-link {
        display: flex;
        align-items: center;
        gap: 12px;
        color: rgba(255, 255, 255, 0.7);
        border-radius: 14px;
        padding: 12px 16px;
        margin: 2px 0;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        font-weight: 500;
        font-size: 0.9rem;
        position: relative;
        overflow: hidden;
        backdrop-filter: blur(4px);
        text-decoration: none;
    }

    /* Teks multiline untuk semua menu yang perlu */
    .nav-text-multiline {
        display: flex;
        flex-direction: column;
        gap: 2px;
        line-height: 1.3;
    }

    .nav-line {
        display: block;
        white-space: nowrap;
    }

    .nav-link:hover .nav-text-multiline .nav-line {
        color: #fff;
    }

    .nav-link::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        width: 0;
        height: 100%;
        background: linear-gradient(90deg, rgba(59, 130, 246, 0.2) 0%, rgba(59, 130, 246, 0) 100%);
        transition: width 0.3s ease;
        z-index: 0;
    }

    .nav-link:hover::before {
        width: 100%;
    }

    .nav-link:hover {
        color: #fff;
        transform: translateX(4px);
        background: rgba(255, 255, 255, 0.08);
    }

    .nav-link.active {
        background: linear-gradient(95deg, rgba(59, 130, 246, 0.2) 0%, rgba(59, 130, 246, 0.05) 100%);
        color: #fff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    /* Pembungkus Ikon */
    .nav-icon-wrapper {
        width: 32px;
        height: 32px;
        min-width: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.08);
        transition: all 0.25s ease;
        position: relative;
        z-index: 1;
    }

    .nav-link:hover .nav-icon-wrapper {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.3) 0%, rgba(59, 130, 246, 0.15) 100%);
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
    }

    .nav-link.active .nav-icon-wrapper {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.4) 0%, rgba(59, 130, 246, 0.2) 100%);
        box-shadow: 0 0 12px rgba(59, 130, 246, 0.3);
    }

    .nav-icon-wrapper i {
        font-size: 1.2rem;
        transition: transform 0.2s ease;
    }

    .nav-link:hover .nav-icon-wrapper i {
        transform: scale(1.1);
    }

    /* Teks Navigasi Normal */
    .nav-text {
        position: relative;
        z-index: 1;
        flex: 1;
        font-weight: 500;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .nav-link.active .nav-text {
        font-weight: 600;
    }

    /* Indikator Aktif */
    .nav-active-indicator {
        width: 3px;
        height: 24px;
        background: linear-gradient(180deg, #3b82f6 0%, #60a5fa 100%);
        border-radius: 4px;
        position: absolute;
        right: 12px;
        animation: slideIn 0.3s ease;
        box-shadow: 0 0 8px rgba(59, 130, 246, 0.5);
    }

    @keyframes slideIn {
        from {
            height: 0;
            opacity: 0;
        }

        to {
            height: 24px;
            opacity: 1;
        }
    }

    /* Gaya Lencana Umum */
    .nav-badge {
        position: relative;
        z-index: 1;
        font-size: 0.65rem;
        padding: 4px 10px;
        border-radius: 40px;
        font-weight: 600;
        letter-spacing: 0.3px;
        display: flex;
        align-items: center;
        gap: 4px;
        white-space: nowrap;
        transition: all 0.3s ease;
        animation: badgeSlideIn 0.4s ease;
        flex-shrink: 0;
    }

    @keyframes badgeSlideIn {
        from {
            opacity: 0;
            transform: translateX(10px);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    /* Badge Analitik */
    .badge-analitik {
        background: linear-gradient(135deg, #3b82f6 0%, #06b6d4 100%);
        color: white;
        box-shadow: 0 2px 8px rgba(59, 130, 246, 0.4);
        animation: pulseBlue 2s infinite;
    }

    @keyframes pulseBlue {

        0%,
        100% {
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.4);
        }

        50% {
            box-shadow: 0 2px 16px rgba(59, 130, 246, 0.7);
        }
    }

    /* Badge Informasi Statistik - Hanya Icon */
    .badge-info-stats {
        background: linear-gradient(135deg, #8b5cf6 0%, #a855f7 100%);
        color: white;
        font-size: 0.6rem;
        padding: 3px 8px;
        transition: all 0.3s ease;
    }

    .badge-info-stats i {
        font-size: 0.7rem;
    }

    .nav-badge i {
        font-size: 0.7rem;
    }

    .nav-badge:hover {
        transform: scale(1.05);
        filter: brightness(1.1);
        cursor: pointer;
    }

    /* Gaya khusus untuk setiap link informasi */
    .distribusi-link:hover .nav-icon-wrapper {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.3) 0%, rgba(139, 92, 246, 0.15) 100%);
    }

    .statistik-link:hover .nav-icon-wrapper {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.3) 0%, rgba(59, 130, 246, 0.15) 100%);
    }

    .trend-link:hover .nav-icon-wrapper {
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.3) 0%, rgba(239, 68, 68, 0.15) 100%);
    }

    /* Gaya khusus untuk link Profil */
    .profile-link .nav-icon-wrapper i {
        transition: all 0.3s ease;
    }

    .profile-link:hover .nav-icon-wrapper i {
        animation: profileFloat 0.5s ease;
    }

    @keyframes profileFloat {

        0%,
        100% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(-3px);
        }
    }

    /* Efek riak saat diklik */
    .nav-link:active::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.3);
        transform: translate(-50%, -50%);
        animation: ripple 0.6s ease-out;
        pointer-events: none;
    }

    @keyframes ripple {
        0% {
            width: 0;
            height: 0;
            opacity: 0.5;
        }

        100% {
            width: 200px;
            height: 200px;
            opacity: 0;
        }
    }

    /* Garis tepi untuk item aktif */
    .nav-link.active {
        border-left: 3px solid #3b82f6;
        background: linear-gradient(95deg, rgba(59, 130, 246, 0.2) 0%, rgba(59, 130, 246, 0.02) 100%);
    }

    /* Tooltip untuk badge analitik saja */
    .badge-analitik {
        cursor: help;
        position: relative;
    }

    .badge-analitik::before {
        content: "Total data pendaftaran";
        position: absolute;
        bottom: 100%;
        right: 0;
        background: rgba(0, 0, 0, 0.9);
        color: white;
        padding: 4px 10px;
        border-radius: 8px;
        font-size: 0.7rem;
        white-space: nowrap;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.2s;
        margin-bottom: 8px;
        font-weight: normal;
        z-index: 1000;
    }

    .badge-analitik:hover::before {
        opacity: 1;
    }

    /* Animasi untuk badge dengan angka */
    @keyframes numberUpdate {
        0% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.2);
        }

        100% {
            transform: scale(1);
        }
    }

    .badge-update {
        animation: numberUpdate 0.3s ease;
    }

    /* Animasi khusus untuk link analitik */
    .analitik-link .nav-icon-wrapper i {
        animation: gentlePulse 2s ease infinite;
    }

    @keyframes gentlePulse {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: 0.7;
            transform: scale(1.05);
        }
    }

    /* Efek shine pada badge analitik */
    .badge-analitik {
        position: relative;
        overflow: hidden;
    }

    .badge-analitik::after {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: left 0.5s ease;
    }

    .badge-analitik:hover::after {
        left: 100%;
    }

    /* Efek hover tambahan untuk link informasi */
    .distribusi-link,
    .statistik-link,
    .trend-link {
        transition: all 0.3s ease;
    }

    /* Ikon animasi untuk link yang belum aktif */
    .distribusi-link .nav-icon-wrapper i,
    .statistik-link .nav-icon-wrapper i,
    .trend-link .nav-icon-wrapper i {
        transition: all 0.3s ease;
    }

    .distribusi-link:hover .nav-icon-wrapper i {
        animation: float 0.5s ease;
    }

    .statistik-link:hover .nav-icon-wrapper i {
        animation: bounce 0.5s ease;
    }

    .trend-link:hover .nav-icon-wrapper i {
        animation: rise 0.5s ease;
    }

    @keyframes float {

        0%,
        100% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(-3px);
        }
    }

    @keyframes bounce {

        0%,
        100% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.1);
        }
    }

    @keyframes rise {

        0%,
        100% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(-4px);
        }
    }

    /* Penyesuaian responsif */
    @media (max-width: 768px) {
        .nav-link {
            padding: 10px 14px;
        }

        .nav-text,
        .nav-text-multiline .nav-line {
            font-size: 0.85rem;
        }

        .nav-badge {
            font-size: 0.55rem;
            padding: 3px 8px;
        }

        .nav-icon-wrapper {
            width: 28px;
            height: 28px;
            min-width: 28px;
        }

        .nav-icon-wrapper i {
            font-size: 1rem;
        }

        .nav-active-indicator {
            height: 20px;
        }

        @keyframes slideIn {
            to {
                height: 20px;
            }
        }

        .badge-analitik::before {
            font-size: 0.6rem;
            white-space: normal;
            width: max-content;
            max-width: 180px;
        }
    }
</style>

<script>
    // Animasi saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function() {
        const navLinks = document.querySelectorAll('.nav-link');
        navLinks.forEach((link, index) => {
            link.style.animation = `fadeInSlide 0.3s ease forwards ${index * 0.03}s`;
            link.style.opacity = '0';
            link.style.transform = 'translateX(-10px)';
        });

        // Tambahan efek untuk semua badge
        const badges = document.querySelectorAll('.nav-badge');
        badges.forEach(badge => {
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'characterData' || mutation.type ===
                        'childList') {
                        badge.classList.add('badge-update');
                        setTimeout(() => {
                            badge.classList.remove('badge-update');
                        }, 300);
                    }
                });
            });

            observer.observe(badge, {
                childList: true,
                characterData: true,
                subtree: true
            });
        });
    });

    // Animasi fade in untuk menu
    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeInSlide {
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
    `;
    document.head.appendChild(style);

    // Efek umpan balik haptic untuk perangkat sentuh
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', function(e) {
            if (window.navigator && window.navigator.vibrate) {
                window.navigator.vibrate(10);
            }
        });
    });
</script>
