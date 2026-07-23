<nav class="nav flex-column px-3 py-2">
    {{-- Bagian Menu Utama --}}
    <div class="sidebar-section d-flex align-items-center gap-2 mb-2 mt-2">
        <div class="section-indicator"></div>
        <span>Menu Utama</span>
    </div>

    <a href="{{ route('reviewer.dashboard') }}"
        class="nav-link {{ request()->routeIs('reviewer.dashboard') ? 'active' : '' }}">
        <div class="nav-icon-wrapper">
            <i class="bi bi-speedometer2"></i>
        </div>
        <span class="nav-text">Dashboard</span>
        @if (request()->routeIs('reviewer.dashboard'))
            <div class="nav-active-indicator"></div>
        @endif
    </a>

    {{-- Bagian Verifikasi --}}
    <div class="sidebar-section d-flex align-items-center gap-2 mt-3 mb-2">
        <div class="section-indicator"></div>
        <span>Verifikasi</span>
    </div>

    <a href="{{ route('reviewer.verifikasi.index') }}"
        class="nav-link verifikasi-link {{ request()->routeIs('reviewer.verifikasi.*') ? 'active' : '' }}">
        <div class="nav-icon-wrapper">
            <i class="bi bi-shield-check"></i>
        </div>
        <span class="nav-text">Verifikasi Dokumen</span>
        @php
            $antrian = \App\Models\Pendaftaran::where('status', 'menunggu_verifikasi')->count();
        @endphp
        @if ($antrian > 0)
            <div class="nav-badge badge-antrian">
                {{ $antrian }}
            </div>
        @endif
        @if (request()->routeIs('reviewer.verifikasi.*'))
            <div class="nav-active-indicator"></div>
        @endif
    </a>

    <div class="sidebar-section d-flex align-items-center gap-2 mt-3 mb-2">
        <div class="section-indicator"></div>
        <span>Laporan</span>
    </div>

    <a href="{{ route('reviewer.export.index') }}"
        class="nav-link {{ request()->routeIs('reviewer.export.*') ? 'active' : '' }}">
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
    /* Gaya Navigasi Modern - Reviewer */
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

    /* Teks Navigasi */
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

    /* Lencana Antrian - Premium (Tanpa Ikon) */
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
        justify-content: center;
        gap: 4px;
        white-space: nowrap;
        transition: all 0.3s ease;
        animation: badgeSlideIn 0.4s ease;
        flex-shrink: 0;
        min-width: 28px;
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

    .badge-antrian {
        background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.4);
        animation: badgePulse 0.5s ease, badgeGlow 2s infinite;
    }

    @keyframes badgePulse {
        0% {
            transform: scale(0.8);
            opacity: 0;
        }

        50% {
            transform: scale(1.1);
        }

        100% {
            transform: scale(1);
            opacity: 1;
        }
    }

    @keyframes badgeGlow {

        0%,
        100% {
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.4);
        }

        50% {
            box-shadow: 0 4px 20px rgba(245, 158, 11, 0.8);
        }
    }

    .nav-badge:hover {
        transform: scale(1.05);
        filter: brightness(1.1);
        cursor: help;
    }

    /* Gaya khusus untuk link Verifikasi */
    .verifikasi-link {
        position: relative;
    }

    /* Efek peringatan ringan untuk verifikasi */
    .verifikasi-link:not(.active) .nav-icon-wrapper i {
        animation: gentleShake 3s ease infinite;
    }

    @keyframes gentleShake {

        0%,
        100% {
            transform: translateX(0);
        }

        95% {
            transform: translateX(0);
        }

        96% {
            transform: translateX(-2px);
        }

        98% {
            transform: translateX(2px);
        }

        100% {
            transform: translateX(0);
        }
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

    /* Tooltip untuk badge */
    .badge-antrian {
        cursor: help;
        position: relative;
    }

    .badge-antrian::before {
        content: "Menunggu verifikasi dokumen";
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

    .badge-antrian:hover::before {
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

    /* Penyesuaian responsif */
    @media (max-width: 768px) {
        .nav-link {
            padding: 10px 14px;
        }

        .nav-text {
            font-size: 0.85rem;
        }

        .nav-badge {
            font-size: 0.55rem;
            padding: 3px 8px;
            min-width: 22px;
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

        .badge-antrian::before {
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

        // Tambahan efek untuk badge antrian
        const badge = document.querySelector('.badge-antrian');
        if (badge) {
            // Observer untuk mendeteksi perubahan jumlah
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'characterData' || mutation.type === 'childList') {
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
        }
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
    if ('vibrate' in navigator) {
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function() {
                navigator.vibrate(10);
            });
        });
    }

    // Fungsi untuk memperbarui badge antrian secara real-time (opsional)
    window.updateAntrianBadge = async function() {
        try {
            const response = await fetch('/api/verifikasi/antrian-count');
            const data = await response.json();
            const badge = document.querySelector('.badge-antrian');
            const antrianCount = data.count || 0;

            if (badge && antrianCount > 0) {
                const currentCount = parseInt(badge.textContent.match(/\d+/)?.[0] || 0);
                if (currentCount !== antrianCount) {
                    badge.textContent = antrianCount;
                    badge.classList.add('badge-update');
                    setTimeout(() => badge.classList.remove('badge-update'), 300);
                }
            } else if (badge && antrianCount === 0) {
                badge.style.display = 'none';
            } else if (!badge && antrianCount > 0) {
                location.reload();
            }
        } catch (error) {
            console.log('Gagal memperbarui antrian');
        }
    };
</script>
