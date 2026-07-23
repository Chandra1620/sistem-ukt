<nav class="nav flex-column px-3 py-2">
    {{-- Bagian Menu Utama --}}
    <div class="sidebar-section">
        <span class="section-dot"></span>
        <span class="section-label">Menu Utama</span>
    </div>

    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <div class="nav-icon-wrapper">
            <i class="bi bi-speedometer2"></i>
        </div>
        <span class="nav-text">Dashboard</span>
        @if (request()->routeIs('admin.dashboard'))
            <div class="nav-active-indicator"></div>
        @endif
    </a>

    {{-- Bagian Manajemen User --}}
    <div class="sidebar-section">
        <span class="section-dot"></span>
        <span class="section-label">Manajemen User</span>
    </div>

    <a href="{{ route('admin.users.index') }}"
        class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
        <div class="nav-icon-wrapper">
            <i class="bi bi-people-fill"></i>
        </div>
        <span class="nav-text">Kelola User</span>
        @php
            $totalUsers = \App\Models\User::count();
        @endphp
        @if ($totalUsers > 0)
            <span class="nav-badge badge-user" title="Total pengguna terdaftar">
                {{ $totalUsers }}
            </span>
        @endif
        @if (request()->routeIs('admin.users.*'))
            <div class="nav-active-indicator"></div>
        @endif
    </a>

    {{-- Bagian Pengaturan Akademik --}}
    <div class="sidebar-section">
        <span class="section-dot"></span>
        <span class="section-label">Pengaturan Akademik</span>
    </div>

    <a href="{{ route('admin.program-studi.index') }}"
        class="nav-link {{ request()->routeIs('admin.program-studi.*') ? 'active' : '' }}">
        <div class="nav-icon-wrapper">
            <i class="bi bi-mortarboard"></i>
        </div>
        <span class="nav-text">Program Studi</span>
        @php
            $totalProdi = \App\Models\ProgramStudi::count();
        @endphp
        @if ($totalProdi > 0)
            <span class="nav-badge badge-prodi" title="Total program studi">
                {{ $totalProdi }}
            </span>
        @endif
        @if (request()->routeIs('admin.program-studi.*'))
            <div class="nav-active-indicator"></div>
        @endif
    </a>

    <a href="{{ route('admin.jenis-dokumen.index') }}"
        class="nav-link {{ request()->routeIs('admin.jenis-dokumen.*') ? 'active' : '' }}">
        <div class="nav-icon-wrapper">
            <i class="bi bi-file-earmark-text"></i>
        </div>
        <span class="nav-text">Jenis Dokumen</span>
        @php
            $totalDokumen = \App\Models\JenisDokumen::count();
        @endphp
        @if ($totalDokumen > 0)
            <span class="nav-badge badge-dokumen" title="Total jenis dokumen">
                {{ $totalDokumen }}
            </span>
        @endif
        @if (request()->routeIs('admin.jenis-dokumen.*'))
            <div class="nav-active-indicator"></div>
        @endif
    </a>

    <a href="{{ route('admin.akreditasi.index') }}"
        class="nav-link {{ request()->routeIs('admin.akreditasi.*') ? 'active' : '' }}">
        <div class="nav-icon-wrapper">
            <i class="bi bi-award"></i>
        </div>
        <span class="nav-text">Akreditasi</span>
        @php
            $totalAkreditasi = \App\Models\Akreditasi::count();
        @endphp
        @if ($totalAkreditasi > 0)
            <span class="nav-badge badge-akreditasi" title="Total data akreditasi">
                {{ $totalAkreditasi }}
            </span>
        @endif
        @if (request()->routeIs('admin.akreditasi.*'))
            <div class="nav-active-indicator"></div>
        @endif
    </a>

    {{-- Bagian Konfigurasi SPK --}}
    <div class="sidebar-section">
        <span class="section-dot"></span>
        <span class="section-label">Konfigurasi SPK</span>
    </div>

    <a href="{{ route('admin.periode.index') }}"
        class="nav-link {{ request()->routeIs('admin.periode.*') ? 'active' : '' }}">
        <div class="nav-icon-wrapper">
            <i class="bi bi-calendar-range"></i>
        </div>
        <span class="nav-text">Periode Seleksi</span>
        @php
            $periodeAktif = \App\Models\PeriodeSeleksi::where('status', 'aktif')->count();
        @endphp
        @if ($periodeAktif > 0)
            <span class="nav-badge badge-active" title="Periode seleksi aktif">
                <i class="bi bi-circle-fill"></i>
                Aktif
            </span>
        @endif
        @if (request()->routeIs('admin.periode.*'))
            <div class="nav-active-indicator"></div>
        @endif
    </a>

    <a href="{{ route('admin.kriteria.index') }}"
        class="nav-link {{ request()->routeIs('admin.kriteria.*') ? 'active' : '' }}">
        <div class="nav-icon-wrapper">
            <i class="bi bi-sliders2"></i>
        </div>
        <span class="nav-text">Kriteria & Bobot</span>
        @php
            $periodeAktifIdKriteria = \App\Models\PeriodeSeleksi::where('status', 'aktif')->value('id');
            $totalKriteria = $periodeAktifIdKriteria
                ? \App\Models\Kriteria::where('periode_id', $periodeAktifIdKriteria)->count()
                : 0;
        @endphp
        @if ($totalKriteria > 0)
            <span class="nav-badge badge-info" title="Jumlah kriteria">
                {{ $totalKriteria }}
            </span>
        @endif
        @if (request()->routeIs('admin.kriteria.*'))
            <div class="nav-active-indicator"></div>
        @endif
    </a>

    <a href="{{ route('admin.sub-kriteria.index') }}"
        class="nav-link {{ request()->routeIs('admin.sub-kriteria.*') ? 'active' : '' }}">
        <div class="nav-icon-wrapper">
            <i class="bi bi-diagram-3"></i>
        </div>
        <span class="nav-text">Sub Kriteria</span>
        @php
            $periodeAktifIdSub = \App\Models\PeriodeSeleksi::where('status', 'aktif')->value('id');
            $totalSubKriteria = $periodeAktifIdSub
                ? \App\Models\SubKriteria::whereHas(
                    'kriteria',
                    fn($q) => $q->where('periode_id', $periodeAktifIdSub),
                )->count()
                : 0;
        @endphp
        @if ($totalSubKriteria > 0)
            <span class="nav-badge badge-info" title="Jumlah sub kriteria">
                {{ $totalSubKriteria }}
            </span>
        @endif
        @if (request()->routeIs('admin.sub-kriteria.*'))
            <div class="nav-active-indicator"></div>
        @endif
    </a>

    {{-- Bagian UKT --}}
    <div class="sidebar-section">
        <span class="section-dot"></span>
        <span class="section-label">Pengaturan UKT</span>
    </div>

    <a href="{{ route('admin.golongan-ukt.index') }}"
        class="nav-link {{ request()->routeIs('admin.golongan-ukt.*') ? 'active' : '' }}">
        <div class="nav-icon-wrapper">
            <i class="bi bi-cash-coin"></i>
        </div>
        <span class="nav-text">Golongan UKT</span>
        @php
            $totalGolongan = \App\Models\GolonganUkt::count();
        @endphp
        @if ($totalGolongan > 0)
            <span class="nav-badge badge-golongan" title="Jumlah golongan UKT">
                {{ $totalGolongan }}
            </span>
        @endif
        @if (request()->routeIs('admin.golongan-ukt.*'))
            <div class="nav-active-indicator"></div>
        @endif
    </a>

    {{-- Bagian Data & Integrasi --}}
    <div class="sidebar-section">
        <span class="section-dot"></span>
        <span class="section-label">Data & Integrasi</span>
    </div>

    <a href="{{ route('admin.import-etl.index') }}"
        class="nav-link etl-link {{ request()->routeIs('admin.import-etl.*') ? 'active' : '' }}">
        <div class="nav-icon-wrapper">
            <i class="bi bi-cloud-upload"></i>
        </div>
        <span class="nav-text">Import & ETL</span>
        @if (request()->routeIs('admin.import-etl.*'))
            <div class="nav-active-indicator"></div>
        @endif
    </a>

    {{-- Bagian Laporan --}}
    <div class="sidebar-section">
        <span class="section-dot"></span>
        <span class="section-label">Laporan</span>
    </div>

    <a href="{{ route('admin.audit-log.index') }}"
        class="nav-link {{ request()->routeIs('admin.audit-log.*') ? 'active' : '' }}">
        <div class="nav-icon-wrapper">
            <i class="bi bi-journal-text"></i>
        </div>
        <span class="nav-text">Audit Log</span>
        @php
            $totalLogs = \App\Models\AuditLog::whereDate('created_at', today())->count();
        @endphp
        @if ($totalLogs > 0)
            <span class="nav-badge badge-log" title="Log aktivitas hari ini">
                {{ $totalLogs }}
            </span>
        @endif
        @if (request()->routeIs('admin.audit-log.*'))
            <div class="nav-active-indicator"></div>
        @endif
    </a>

    <a href="{{ route('admin.export.index') }}"
        class="nav-link {{ request()->routeIs('admin.export.*') ? 'active' : '' }}">
        <div class="nav-icon-wrapper">
            <i class="bi bi-download"></i>
        </div>
        <span class="nav-text">Export Data</span>
        @if (request()->routeIs('admin.export.*'))
            <div class="nav-active-indicator"></div>
        @endif
    </a>
</nav>

<style>
    /* ============================================ */
    /* SIDEBAR NAVIGATION STYLES */
    /* ============================================ */
    .nav {
        gap: 2px;
        padding: 8px 12px !important;
    }

    /* ============================================ */
    /* SECTION HEADER */
    /* ============================================ */
    .sidebar-section {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 12px 6px 12px;
        margin-top: 6px;
        position: relative;
        transition: all 0.3s ease;
        user-select: none;
    }

    .sidebar-section:first-of-type {
        margin-top: 0;
    }

    .section-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: linear-gradient(135deg, #3b82f6, #8b5cf6);
        display: inline-block;
        transition: all 0.3s ease;
        flex-shrink: 0;
        box-shadow: 0 0 8px rgba(59, 130, 246, 0.3);
    }

    .sidebar-section:hover .section-dot {
        transform: scale(1.4);
        box-shadow: 0 0 16px rgba(59, 130, 246, 0.6);
    }

    .section-label {
        color: rgba(255, 255, 255, 0.4);
        font-size: 0.65rem;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        font-weight: 700;
        transition: color 0.3s ease;
    }

    .sidebar-section:hover .section-label {
        color: rgba(255, 255, 255, 0.7);
    }

    /* ============================================ */
    /* NAV LINK */
    /* ============================================ */
    .nav-link {
        display: flex;
        align-items: center;
        gap: 12px;
        color: rgba(255, 255, 255, 0.65);
        border-radius: 12px;
        padding: 10px 14px;
        margin: 1px 0;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        font-weight: 500;
        font-size: 0.85rem;
        position: relative;
        text-decoration: none;
        cursor: pointer;
        border: none;
        background: transparent;
        width: 100%;
        text-align: left;
        min-height: 44px;
    }

    .nav-link::before {
        content: '';
        position: absolute;
        inset: 0;
        border-radius: 12px;
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.15), rgba(139, 92, 246, 0.05));
        opacity: 0;
        transition: opacity 0.3s ease;
        pointer-events: none;
    }

    .nav-link:hover::before {
        opacity: 1;
    }

    .nav-link:hover {
        color: #ffffff;
        transform: translateX(4px);
    }

    .nav-link:active {
        transform: scale(0.98);
    }

    .nav-link.active {
        color: #ffffff;
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.2), rgba(139, 92, 246, 0.1));
        box-shadow: 0 2px 12px rgba(59, 130, 246, 0.1);
        border-left: 3px solid #3b82f6;
    }

    .nav-link.active::before {
        opacity: 0;
    }

    /* ============================================ */
    /* NAV ICON */
    /* ============================================ */
    .nav-icon-wrapper {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.06);
        transition: all 0.25s ease;
        flex-shrink: 0;
        position: relative;
        z-index: 1;
    }

    .nav-link:hover .nav-icon-wrapper {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.25), rgba(139, 92, 246, 0.15));
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
    }

    .nav-link.active .nav-icon-wrapper {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.3), rgba(139, 92, 246, 0.2));
        box-shadow: 0 4px 16px rgba(59, 130, 246, 0.2);
    }

    .nav-icon-wrapper i {
        font-size: 1.1rem;
        transition: transform 0.2s ease;
        color: rgba(255, 255, 255, 0.7);
    }

    .nav-link:hover .nav-icon-wrapper i {
        transform: scale(1.1);
        color: #ffffff;
    }

    .nav-link.active .nav-icon-wrapper i {
        color: #60a5fa;
    }

    /* ============================================ */
    /* NAV TEXT */
    /* ============================================ */
    .nav-text {
        position: relative;
        z-index: 1;
        flex: 1;
        font-weight: 500;
        transition: font-weight 0.2s ease;
    }

    .nav-link.active .nav-text {
        font-weight: 600;
    }

    /* ============================================ */
    /* ACTIVE INDICATOR */
    /* ============================================ */
    .nav-active-indicator {
        width: 3px;
        height: 20px;
        background: linear-gradient(180deg, #3b82f6, #60a5fa);
        border-radius: 4px;
        flex-shrink: 0;
        animation: slideIn 0.3s ease;
        box-shadow: 0 0 12px rgba(59, 130, 246, 0.4);
    }

    @keyframes slideIn {
        from {
            height: 0;
            opacity: 0;
            transform: scaleY(0);
        }

        to {
            height: 20px;
            opacity: 1;
            transform: scaleY(1);
        }
    }

    /* ============================================ */
    /* NAV BADGE */
    /* ============================================ */
    .nav-badge {
        position: relative;
        z-index: 1;
        font-size: 0.6rem;
        padding: 3px 10px;
        border-radius: 40px;
        font-weight: 600;
        letter-spacing: 0.3px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        white-space: nowrap;
        transition: all 0.3s ease;
        animation: badgeSlideIn 0.4s ease;
        flex-shrink: 0;
        min-width: 24px;
        justify-content: center;
        cursor: help;
    }

    @keyframes badgeSlideIn {
        from {
            opacity: 0;
            transform: translateX(8px) scale(0.8);
        }

        to {
            opacity: 1;
            transform: translateX(0) scale(1);
        }
    }

    .nav-badge i {
        font-size: 0.5rem;
    }

    .nav-badge:hover {
        transform: scale(1.05);
        filter: brightness(1.15);
    }

    /* Badge Colors */
    .badge-user {
        background: linear-gradient(135deg, #3b82f6, #6366f1);
        color: white;
        box-shadow: 0 2px 8px rgba(59, 130, 246, 0.35);
    }

    .badge-prodi {
        background: linear-gradient(135deg, #06b6d4, #0891b2);
        color: white;
        box-shadow: 0 2px 8px rgba(6, 182, 212, 0.35);
    }

    .badge-dokumen {
        background: linear-gradient(135deg, #ec489a, #db2777);
        color: white;
        box-shadow: 0 2px 8px rgba(236, 72, 154, 0.35);
    }

    .badge-akreditasi {
        background: linear-gradient(135deg, #8b5cf6, #6d28d9);
        color: white;
        box-shadow: 0 2px 8px rgba(139, 92, 246, 0.35);
    }

    .badge-active {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.35);
        animation: pulseGreen 2s ease-in-out infinite;
        font-size: 0.55rem;
        padding: 3px 10px 3px 8px;
    }

    .badge-active i {
        font-size: 0.45rem;
        animation: pulseDot 1.5s ease-in-out infinite;
    }

    @keyframes pulseGreen {

        0%,
        100% {
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.35);
        }

        50% {
            box-shadow: 0 2px 16px rgba(16, 185, 129, 0.6);
        }
    }

    @keyframes pulseDot {

        0%,
        100% {
            opacity: 1;
            transform: scale(1);
        }

        50% {
            opacity: 0.5;
            transform: scale(0.7);
        }
    }

    .badge-info {
        background: linear-gradient(135deg, #8b5cf6, #6d28d9);
        color: white;
        box-shadow: 0 2px 8px rgba(139, 92, 246, 0.35);
    }

    .badge-golongan {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
        box-shadow: 0 2px 8px rgba(245, 158, 11, 0.35);
    }

    .badge-log {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
        box-shadow: 0 2px 8px rgba(245, 158, 11, 0.35);
    }

    /* ============================================ */
    /* ETL LINK SPECIAL */
    /* ============================================ */
    .etl-link .nav-icon-wrapper i {
        animation: gentlePulse 2.5s ease-in-out infinite;
    }

    @keyframes gentlePulse {

        0%,
        100% {
            opacity: 1;
            transform: scale(1);
        }

        50% {
            opacity: 0.6;
            transform: scale(0.95);
        }
    }

    /* ============================================ */
    /* RIPPLE EFFECT */
    /* ============================================ */
    .nav-link:active::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.15);
        transform: translate(-50%, -50%);
        animation: ripple 0.5s ease-out;
        pointer-events: none;
    }

    @keyframes ripple {
        0% {
            width: 0;
            height: 0;
            opacity: 0.6;
        }

        100% {
            width: 200px;
            height: 200px;
            opacity: 0;
        }
    }

    /* ============================================ */
    /* RESPONSIVE */
    /* ============================================ */
    @media (max-width: 768px) {
        .nav-link {
            padding: 8px 12px;
            font-size: 0.8rem;
            min-height: 40px;
        }

        .nav-text {
            font-size: 0.8rem;
        }

        .nav-badge {
            font-size: 0.5rem;
            padding: 2px 8px;
            min-width: 20px;
        }

        .nav-icon-wrapper {
            width: 28px;
            height: 28px;
            border-radius: 8px;
        }

        .nav-icon-wrapper i {
            font-size: 0.95rem;
        }

        .nav-active-indicator {
            height: 16px;
        }

        @keyframes slideIn {
            to {
                height: 16px;
            }
        }

        .sidebar-section {
            padding: 6px 10px 4px 10px;
        }

        .section-label {
            font-size: 0.55rem;
            letter-spacing: 1.2px;
        }

        .section-dot {
            width: 5px;
            height: 5px;
        }

        .badge-active {
            font-size: 0.45rem;
            padding: 2px 8px 2px 6px;
        }

        .badge-active i {
            font-size: 0.4rem;
        }
    }

    @media (max-width: 480px) {
        .nav {
            padding: 4px 8px !important;
        }

        .nav-link {
            padding: 6px 10px;
            font-size: 0.75rem;
            min-height: 36px;
            gap: 10px;
            border-radius: 10px;
        }

        .nav-text {
            font-size: 0.75rem;
        }

        .nav-icon-wrapper {
            width: 24px;
            height: 24px;
            border-radius: 6px;
        }

        .nav-icon-wrapper i {
            font-size: 0.8rem;
        }

        .nav-badge {
            font-size: 0.45rem;
            padding: 2px 6px;
            min-width: 16px;
        }

        .nav-active-indicator {
            height: 14px;
            width: 2px;
        }

        @keyframes slideIn {
            to {
                height: 14px;
            }
        }

        .sidebar-section {
            padding: 4px 8px 2px 8px;
            gap: 6px;
        }

        .section-label {
            font-size: 0.5rem;
            letter-spacing: 1px;
        }

        .section-dot {
            width: 4px;
            height: 4px;
        }

        .badge-active {
            font-size: 0.4rem;
            padding: 1px 6px 1px 4px;
        }

        .badge-active i {
            font-size: 0.35rem;
        }
    }

    /* ============================================ */
    /* SCROLLBAR STYLING */
    /* ============================================ */
    .nav::-webkit-scrollbar {
        width: 3px;
    }

    .nav::-webkit-scrollbar-track {
        background: transparent;
    }

    .nav::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.2);
        border-radius: 10px;
    }

    .nav::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.3);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Animasi fade-in untuk setiap nav link
        const navLinks = document.querySelectorAll('.nav-link:not(.etl-link)');
        navLinks.forEach((link, index) => {
            link.style.opacity = '0';
            link.style.transform = 'translateX(-8px)';
            link.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
            setTimeout(() => {
                link.style.opacity = '1';
                link.style.transform = 'translateX(0)';
            }, 50 + (index * 30));
        });

        // Keyboard navigation support
        document.querySelectorAll('.nav-link').forEach((link) => {
            link.setAttribute('role', 'menuitem');
            link.setAttribute('tabindex', '0');

            link.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    this.click();
                }
            });
        });

        // Haptic feedback untuk mobile
        if ('vibrate' in navigator) {
            document.querySelectorAll('.nav-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    if (window.innerWidth <= 768) {
                        navigator.vibrate(8);
                    }
                });
            });
        }

        // Badge count animation on update
        const badges = document.querySelectorAll('.nav-badge');
        badges.forEach(badge => {
            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
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

        // Add badge-update animation if not exists
        const styleSheet = document.createElement('style');
        styleSheet.textContent = `
            @keyframes badgeUpdate {
                0% { transform: scale(1); }
                50% { transform: scale(1.15); }
                100% { transform: scale(1); }
            }
            .badge-update {
                animation: badgeUpdate 0.3s ease;
            }
        `;
        document.head.appendChild(styleSheet);
    });

    // Fungsi untuk update badge secara realtime (opsional)
    window.updateAdminBadges = async function() {
        try {
            // Update badge user
            const userResponse = await fetch('/api/admin/users/count');
            if (userResponse.ok) {
                const userData = await userResponse.json();
                const userBadge = document.querySelector('.badge-user');
                if (userBadge && userData.count !== undefined) {
                    const currentCount = parseInt(userBadge.textContent.match(/\d+/)?.[0] || 0);
                    if (currentCount !== userData.count) {
                        userBadge.textContent = userData.count;
                        userBadge.classList.add('badge-update');
                        setTimeout(() => userBadge.classList.remove('badge-update'), 300);
                    }
                }
            }

            // Update badge log
            const logResponse = await fetch('/api/admin/audit-log/today-count');
            if (logResponse.ok) {
                const logData = await logResponse.json();
                const logBadge = document.querySelector('.badge-log');
                if (logBadge && logData.count !== undefined) {
                    const currentCount = parseInt(logBadge.textContent.match(/\d+/)?.[0] || 0);
                    if (currentCount !== logData.count) {
                        logBadge.textContent = logData.count;
                        logBadge.classList.add('badge-update');
                        setTimeout(() => logBadge.classList.remove('badge-update'), 300);
                    }
                }
            }
        } catch (error) {
            // Silent fail untuk avoid console clutter
            if (window.DEV_MODE) {
                console.log('Gagal memperbarui badge admin:', error);
            }
        }
    };

    // Auto update badges every 30 seconds jika fungsi tersedia
    if (typeof window.updateAdminBadges === 'function') {
        setInterval(window.updateAdminBadges, 30000);
    }
</script>
