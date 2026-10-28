@extends('layouts.app')
@section('title', 'Dashboard Management')
@section('page-title', 'Dashboard Analitik')

@section('content')
    {{-- Toolbar: Pemilih Periode --}}
    <div class="d-flex justify-content-end mb-3">
        @include('management.partials.periode-selector')
    </div>
    <script>
        window.SELECTED_PERIODE = @json((string) ($selectedPeriode ?? 'all'));
        function withPeriode(url) {
            const sep = url.includes('?') ? '&' : '?';
            return url + sep + 'periode=' + encodeURIComponent(window.SELECTED_PERIODE);
        }
    </script>

    {{-- Stat Cards Utama --}}
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stat-card stat-card-primary">
                <div class="stat-icon-wrapper">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Total Pendaftar</div>
                    <div class="stat-value">{{ number_format($stats['total_pendaftar'] ?? 0) }}</div>
                </div>
                <div class="stat-trend">
                    <i class="bi bi-graph-up-arrow"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stat-card stat-card-success">
                <div class="stat-icon-wrapper">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Sudah Final</div>
                    <div class="stat-value">{{ number_format($stats['sudah_final'] ?? 0) }}</div>
                </div>
                <div class="stat-trend">
                    <i class="bi bi-trophy-fill"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stat-card stat-card-warning">
                <div class="stat-icon-wrapper">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Menunggu Verifikasi</div>
                    <div class="stat-value">{{ number_format($stats['menunggu_verifikasi'] ?? 0) }}</div>
                </div>
                <div class="stat-trend">
                    <i class="bi bi-clock-history"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stat-card stat-card-danger">
                <div class="stat-icon-wrapper">
                    <i class="bi bi-chat-square-text-fill"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Banding Aktif</div>
                    <div class="stat-value">{{ number_format($stats['banding_aktif'] ?? 0) }}</div>
                </div>
                <div class="stat-trend">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Row 1 --}}
    <div class="row g-4 mb-4">
        <div class="col-xl-5 col-lg-6" id="distribusi">
            <div class="card chart-card h-100">
                <div class="card-header chart-card-header">
                    <div class="d-flex align-items-center gap-3">
                        <div class="header-icon purple-icon">
                            <i class="bi bi-pie-chart-fill"></i>
                        </div>
                        <div>
                            <h6 class="fw-semibold mb-0">Distribusi Golongan UKT</h6>
                            <small class="text-muted">Total {{ $stats['total_pendaftar'] ?? 0 }} Pendaftar</small>
                        </div>
                    </div>
                    <span class="chart-badge">
                        <i class="bi bi-info-circle"></i> Update Real-time
                    </span>
                </div>
                <div class="card-body p-3">
                    <div id="chart-distribusi-ukt" class="chart-container"></div>
                </div>
            </div>
        </div>

        <div class="col-xl-7 col-lg-6" id="prodi">
            <div class="card chart-card h-100">
                <div class="card-header chart-card-header">
                    <div class="d-flex align-items-center gap-3">
                        <div class="header-icon green-icon">
                            <i class="bi bi-bar-chart-steps"></i>
                        </div>
                        <div>
                            <h6 class="fw-semibold mb-0">Pendaftar per Program Studi</h6>
                            <small class="text-muted">{{ $dataProdiCount ?? 0 }} Program Studi</small>
                        </div>
                    </div>
                    <span class="chart-badge">
                        <i class="bi bi-building"></i> Distribusi
                    </span>
                </div>
                <div class="card-body p-3">
                    <div id="chart-per-prodi" class="chart-container"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Row 2 --}}
    <div class="row g-4 mb-4">
        <div class="col-xl-5 col-lg-6">
            <div class="card chart-card h-100">
                <div class="card-header chart-card-header">
                    <div class="d-flex align-items-center gap-3">
                        <div class="header-icon kip-icon">
                            <i class="bi bi-award-fill"></i>
                        </div>
                        <div>
                            <h6 class="fw-semibold mb-0">KIP vs Non-KIP</h6>
                            <small class="text-muted">Sumber Pembiayaan</small>
                        </div>
                    </div>
                    <span class="chart-badge">
                        <i class="bi bi-people"></i> Perbandingan
                    </span>
                </div>
                <div class="card-body p-3">
                    <div id="chart-kip-nonkip" class="chart-container"></div>
                </div>
            </div>
        </div>

        <div class="col-xl-7 col-lg-6">
            <div class="card chart-card h-100">
                <div class="card-header chart-card-header">
                    <div class="d-flex align-items-center gap-3">
                        <div class="header-icon blue-icon">
                            <i class="bi bi-bar-chart-fill"></i>
                        </div>
                        <div>
                            <h6 class="fw-semibold mb-0">KIP per Program Studi</h6>
                            <small class="text-muted">Distribusi KIP & Non-KIP</small>
                        </div>
                    </div>
                    <span class="chart-badge">
                        <i class="bi bi-building"></i> Perbandingan
                    </span>
                </div>
                <div class="card-body p-3">
                    <div id="chart-kip-prodi" class="chart-container"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Row 3 --}}
    <div class="row g-4 mb-4">
        <div class="col-xl-8 col-lg-7">
            <div class="card chart-card">
                <div class="card-header chart-card-header">
                    <div class="d-flex align-items-center gap-3">
                        <div class="header-icon info-icon">
                            <i class="bi bi-graph-up"></i>
                        </div>
                        <div>
                            <h6 class="fw-semibold mb-0">Trend Pendaftar per Periode</h6>
                            <small class="text-muted">Data Tahunan</small>
                        </div>
                    </div>
                    <span class="chart-badge">
                        <i class="bi bi-calendar-range"></i> 2021-2026
                    </span>
                </div>
                <div class="card-body p-3">
                    <div id="chart-trend" class="chart-container"></div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-5">
            <div class="card economy-card h-100">
                <div class="card-header economy-card-header">
                    <div class="d-flex align-items-center gap-3">
                        <div class="header-icon orange-icon">
                            <i class="bi bi-cash-stack"></i>
                        </div>
                        <div>
                            <h6 class="fw-semibold mb-0">Statistik Ekonomi</h6>
                            <small class="text-muted">Penghasilan Orang Tua</small>
                        </div>
                    </div>
                    <span class="chart-badge">
                        <i class="bi bi-currency-dollar"></i> Rupiah
                    </span>
                </div>
                <div class="card-body p-3">
                    @if ($statsEkonomi)
                        <div class="economy-stats">
                            <div class="economy-item">
                                <div class="economy-icon average">
                                    <i class="bi bi-graph-up-arrow"></i>
                                </div>
                                <div class="economy-info">
                                    <div class="economy-label">Rata-rata Penghasilan</div>
                                    <div class="economy-value primary-value">
                                        Rp {{ number_format($statsEkonomi->rata_rata ?? 0, 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                            <div class="economy-item">
                                <div class="economy-icon minimum">
                                    <i class="bi bi-arrow-down-circle"></i>
                                </div>
                                <div class="economy-info">
                                    <div class="economy-label">Penghasilan Minimum</div>
                                    <div class="economy-value success-value">
                                        Rp {{ number_format($statsEkonomi->minimum ?? 0, 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                            <div class="economy-item">
                                <div class="economy-icon maximum">
                                    <i class="bi bi-arrow-up-circle"></i>
                                </div>
                                <div class="economy-info">
                                    <div class="economy-label">Penghasilan Maximum</div>
                                    <div class="economy-value danger-value">
                                        Rp {{ number_format($statsEkonomi->maximum ?? 0, 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="empty-economy">
                            <div class="empty-icon-small">
                                <i class="bi bi-inbox"></i>
                            </div>
                            <p class="small text-muted mt-2 mb-0">Belum ada data ekonomi</p>
                        </div>
                    @endif
                    <a href="{{ route('management.analitik.index') }}" class="btn-analytics">
                        <i class="bi bi-graph-up"></i>
                        <span>Analitik Lengkap</span>
                        <i class="bi bi-arrow-right-short"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Stat Cards KIP, Non-KIP, SAW, dan Persentase KIP (Paling Bawah) --}}
    <div class="row g-4">
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stat-card stat-card-kip">
                <div class="stat-icon-wrapper">
                    <i class="bi bi-award-fill"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Mahasiswa KIP</div>
                    <div class="stat-value">{{ number_format($stats['total_kip'] ?? 0) }}</div>
                </div>
                <div class="stat-trend">
                    <i class="bi bi-bank2"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stat-card stat-card-nonkip">
                <div class="stat-icon-wrapper">
                    <i class="bi bi-wallet2"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Non-KIP (Mandiri)</div>
                    <div class="stat-value">{{ number_format($stats['total_non_kip'] ?? 0) }}</div>
                </div>
                <div class="stat-trend">
                    <i class="bi bi-cash-stack"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stat-card stat-card-info">
                <div class="stat-icon-wrapper">
                    <i class="bi bi-calculator-fill"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Sudah Dihitung SAW</div>
                    <div class="stat-value">{{ number_format($stats['sudah_saw'] ?? 0) }}</div>
                </div>
                <div class="stat-trend">
                    <i class="bi bi-graph-up-arrow"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stat-card stat-card-purple">
                <div class="stat-icon-wrapper">
                    <i class="bi bi-percent"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">% Penerima KIP</div>
                    <div class="stat-value">
                        {{ isset($stats['total_pendaftar']) && $stats['total_pendaftar'] > 0
                            ? number_format(($stats['total_kip'] / $stats['total_pendaftar']) * 100, 1)
                            : 0 }}%
                    </div>
                </div>
                <div class="stat-trend">
                    <i class="bi bi-pie-chart-fill"></i>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* ============================================
                   PREMIUM STAT CARDS
                   ============================================ */
        .stat-card {
            background: #ffffff;
            border-radius: 20px;
            padding: 24px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04), 0 6px 16px rgba(0, 0, 0, 0.02);
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(0, 0, 0, 0.03);
            height: 100%;
            min-height: 100px;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, currentColor, transparent);
            opacity: 0.8;
        }

        .stat-card::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: currentColor;
            opacity: 0.02;
            pointer-events: none;
        }

        .stat-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.08);
            border-color: transparent;
        }

        .stat-card:hover .stat-icon-wrapper {
            transform: scale(1.08) rotate(-5deg);
        }

        .stat-card-primary {
            color: #3b82f6;
        }

        .stat-card-primary::before {
            background: linear-gradient(90deg, #3b82f6, #60a5fa);
        }

        .stat-card-primary .stat-icon-wrapper {
            background: rgba(59, 130, 246, 0.12);
            color: #3b82f6;
        }

        .stat-card-success {
            color: #10b981;
        }

        .stat-card-success::before {
            background: linear-gradient(90deg, #10b981, #34d399);
        }

        .stat-card-success .stat-icon-wrapper {
            background: rgba(16, 185, 129, 0.12);
            color: #10b981;
        }

        .stat-card-warning {
            color: #f59e0b;
        }

        .stat-card-warning::before {
            background: linear-gradient(90deg, #f59e0b, #fbbf24);
        }

        .stat-card-warning .stat-icon-wrapper {
            background: rgba(245, 158, 11, 0.12);
            color: #f59e0b;
        }

        .stat-card-danger {
            color: #ef4444;
        }

        .stat-card-danger::before {
            background: linear-gradient(90deg, #ef4444, #f87171);
        }

        .stat-card-danger .stat-icon-wrapper {
            background: rgba(239, 68, 68, 0.12);
            color: #ef4444;
        }

        .stat-card-kip {
            color: #f59e0b;
        }

        .stat-card-kip::before {
            background: linear-gradient(90deg, #f59e0b, #fbbf24);
        }

        .stat-card-kip .stat-icon-wrapper {
            background: rgba(245, 158, 11, 0.12);
            color: #f59e0b;
        }

        .stat-card-nonkip {
            color: #8b5cf6;
        }

        .stat-card-nonkip::before {
            background: linear-gradient(90deg, #8b5cf6, #a78bfa);
        }

        .stat-card-nonkip .stat-icon-wrapper {
            background: rgba(139, 92, 246, 0.12);
            color: #8b5cf6;
        }

        .stat-card-info {
            color: #06b6d4;
        }

        .stat-card-info::before {
            background: linear-gradient(90deg, #06b6d4, #22d3ee);
        }

        .stat-card-info .stat-icon-wrapper {
            background: rgba(6, 182, 212, 0.12);
            color: #06b6d4;
        }

        .stat-card-purple {
            color: #ec4899;
        }

        .stat-card-purple::before {
            background: linear-gradient(90deg, #ec4899, #f472b6);
        }

        .stat-card-purple .stat-icon-wrapper {
            background: rgba(236, 72, 153, 0.12);
            color: #ec4899;
        }

        .stat-icon-wrapper {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            flex-shrink: 0;
        }

        .stat-info {
            flex: 1;
            padding: 0 12px;
            min-width: 0;
        }

        .stat-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #94a3b8;
            font-weight: 600;
            margin-bottom: 2px;
        }

        .stat-value {
            font-size: 1.75rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.2;
            letter-spacing: -0.5px;
        }

        .stat-trend {
            width: 36px;
            height: 36px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            background: rgba(0, 0, 0, 0.03);
            transition: all 0.3s;
            flex-shrink: 0;
        }

        .stat-card:hover .stat-trend {
            background: rgba(0, 0, 0, 0.06);
        }

        /* ============================================
                   CHART CARDS
                   ============================================ */
        .chart-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04), 0 6px 16px rgba(0, 0, 0, 0.02);
            overflow: hidden;
            transition: all 0.35s ease;
            background: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.03);
            height: 100%;
        }

        .chart-card:hover {
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.06);
            transform: translateY(-2px);
        }

        .chart-card-header {
            background: transparent;
            border-bottom: 1px solid #f1f5f9;
            padding: 18px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }

        .header-icon {
            width: 42px;
            height: 42px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            flex-shrink: 0;
        }

        .purple-icon {
            background: linear-gradient(135deg, #ede9fe, #ddd6fe);
            color: #8b5cf6;
        }

        .green-icon {
            background: linear-gradient(135deg, #d1fae5, #a7f3d0);
            color: #10b981;
        }

        .blue-icon {
            background: linear-gradient(135deg, #dbeafe, #bfdbfe);
            color: #3b82f6;
        }

        .info-icon {
            background: linear-gradient(135deg, #cffafe, #bae6fd);
            color: #06b6d4;
        }

        .orange-icon {
            background: linear-gradient(135deg, #fed7aa, #fdba74);
            color: #f59e0b;
        }

        .kip-icon {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            color: #f59e0b;
        }

        .chart-badge {
            background: #f1f5f9;
            padding: 6px 14px;
            border-radius: 40px;
            font-size: 0.7rem;
            font-weight: 500;
            color: #475569;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .chart-container {
            min-height: 280px;
            position: relative;
        }

        /* ============================================
                   ECONOMY CARD
                   ============================================ */
        .economy-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04), 0 6px 16px rgba(0, 0, 0, 0.02);
            overflow: hidden;
            background: linear-gradient(145deg, #ffffff 0%, #fffbeb 100%);
            border: 1px solid rgba(0, 0, 0, 0.03);
            height: 100%;
        }

        .economy-card-header {
            background: transparent;
            border-bottom: 1px solid #f1f5f9;
            padding: 18px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }

        .economy-stats {
            padding: 4px 0;
        }

        .economy-item {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 14px 16px;
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            margin-bottom: 10px;
            transition: all 0.3s ease;
            border: 1px solid rgba(0, 0, 0, 0.02);
        }

        .economy-item:last-child {
            margin-bottom: 0;
        }

        .economy-item:hover {
            background: #ffffff;
            transform: translateX(4px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
        }

        .economy-icon {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .economy-icon.average {
            background: rgba(59, 130, 246, 0.12);
            color: #3b82f6;
        }

        .economy-icon.minimum {
            background: rgba(16, 185, 129, 0.12);
            color: #10b981;
        }

        .economy-icon.maximum {
            background: rgba(239, 68, 68, 0.12);
            color: #ef4444;
        }

        .economy-info {
            flex: 1;
            min-width: 0;
        }

        .economy-label {
            font-size: 0.65rem;
            text-transform: uppercase;
            color: #94a3b8;
            letter-spacing: 0.4px;
            font-weight: 600;
        }

        .economy-value {
            font-size: 1rem;
            font-weight: 700;
            color: #0f172a;
        }

        .primary-value {
            color: #3b82f6;
        }

        .success-value {
            color: #10b981;
        }

        .danger-value {
            color: #ef4444;
        }

        .btn-analytics {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            padding: 12px 24px;
            border-radius: 40px;
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            margin-top: 16px;
            border: none;
            width: 100%;
            letter-spacing: 0.3px;
        }

        .btn-analytics:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(59, 130, 246, 0.35);
            color: white;
        }

        .btn-analytics i:last-child {
            transition: transform 0.3s ease;
        }

        .btn-analytics:hover i:last-child {
            transform: translateX(6px);
        }

        /* ============================================
                   EMPTY STATES & LOADING
                   ============================================ */
        .empty-economy {
            text-align: center;
            padding: 20px 0;
        }

        .empty-icon-small {
            width: 48px;
            height: 48px;
            background: #f1f5f9;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            font-size: 1.5rem;
            color: #94a3b8;
        }

        .chart-loading {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 260px;
        }

        .loading-spinner {
            width: 36px;
            height: 36px;
            border: 3px solid #e2e8f0;
            border-top-color: #3b82f6;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* ============================================
                   ANIMATIONS
                   ============================================ */
        .stat-card {
            animation: fadeInUp 0.5s ease forwards;
            opacity: 0;
            transform: translateY(20px);
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .stat-card:nth-child(1) {
            animation-delay: 0.04s;
        }

        .stat-card:nth-child(2) {
            animation-delay: 0.08s;
        }

        .stat-card:nth-child(3) {
            animation-delay: 0.12s;
        }

        .stat-card:nth-child(4) {
            animation-delay: 0.16s;
        }

        /* Bottom stat cards animation delay */
        .row:last-child .stat-card:nth-child(1) {
            animation-delay: 0.20s;
        }

        .row:last-child .stat-card:nth-child(2) {
            animation-delay: 0.24s;
        }

        .row:last-child .stat-card:nth-child(3) {
            animation-delay: 0.28s;
        }

        .row:last-child .stat-card:nth-child(4) {
            animation-delay: 0.32s;
        }

        .chart-card {
            animation: fadeInUp 0.5s ease forwards;
            opacity: 0;
            transform: translateY(20px);
            animation-delay: 0.1s;
        }

        .economy-card {
            animation: fadeInUp 0.5s ease forwards;
            opacity: 0;
            transform: translateY(20px);
            animation-delay: 0.15s;
        }

        /* ============================================
                   RESPONSIVE
                   ============================================ */
        @media (max-width: 1399px) {
            .stat-value {
                font-size: 1.5rem;
            }

            .stat-icon-wrapper {
                width: 48px;
                height: 48px;
                font-size: 1.3rem;
            }
        }

        @media (max-width: 1199px) {
            .stat-card {
                padding: 18px 16px;
                min-height: 85px;
            }

            .stat-value {
                font-size: 1.4rem;
            }

            .stat-icon-wrapper {
                width: 44px;
                height: 44px;
                font-size: 1.2rem;
            }

            .chart-card-header {
                padding: 14px 18px;
            }

            .economy-card-header {
                padding: 14px 18px;
            }
        }

        @media (max-width: 991px) {
            .stat-card {
                min-height: 80px;
            }

            .stat-value {
                font-size: 1.25rem;
            }

            .stat-icon-wrapper {
                width: 40px;
                height: 40px;
                font-size: 1.1rem;
                border-radius: 12px;
            }

            .stat-trend {
                width: 30px;
                height: 30px;
                font-size: 0.9rem;
            }

            .chart-container {
                min-height: 240px;
            }
        }

        @media (max-width: 767px) {
            .stat-card {
                padding: 16px 14px;
                min-height: 70px;
                border-radius: 16px;
            }

            .stat-value {
                font-size: 1.1rem;
            }

            .stat-label {
                font-size: 0.6rem;
            }

            .stat-icon-wrapper {
                width: 36px;
                height: 36px;
                font-size: 1rem;
                border-radius: 10px;
            }

            .stat-trend {
                width: 26px;
                height: 26px;
                font-size: 0.8rem;
                border-radius: 8px;
            }

            .chart-card-header {
                padding: 12px 16px;
                flex-direction: column;
                align-items: flex-start;
            }

            .economy-card-header {
                padding: 12px 16px;
                flex-direction: column;
                align-items: flex-start;
            }

            .header-icon {
                width: 36px;
                height: 36px;
                font-size: 1rem;
                border-radius: 10px;
            }

            .chart-badge {
                font-size: 0.6rem;
                padding: 4px 10px;
            }

            .chart-container {
                min-height: 200px;
            }

            .economy-item {
                padding: 10px 12px;
                gap: 12px;
            }

            .economy-icon {
                width: 36px;
                height: 36px;
                font-size: 1rem;
            }

            .economy-value {
                font-size: 0.9rem;
            }

            .btn-analytics {
                padding: 10px 18px;
                font-size: 0.8rem;
            }

            .row.g-4 {
                --bs-gutter-y: 1rem;
                --bs-gutter-x: 1rem;
            }
        }

        @media (max-width: 575px) {
            .stat-card {
                padding: 12px 10px;
                min-height: 60px;
                border-radius: 12px;
            }

            .stat-value {
                font-size: 1rem;
            }

            .stat-label {
                font-size: 0.55rem;
                letter-spacing: 0.3px;
            }

            .stat-icon-wrapper {
                width: 32px;
                height: 32px;
                font-size: 0.9rem;
                border-radius: 8px;
            }

            .stat-trend {
                width: 22px;
                height: 22px;
                font-size: 0.7rem;
                border-radius: 6px;
            }

            .stat-info {
                padding: 0 8px;
            }

            .chart-card-header {
                padding: 10px 14px;
            }

            .chart-container {
                min-height: 180px;
            }

            .header-icon {
                width: 32px;
                height: 32px;
                font-size: 0.9rem;
                border-radius: 8px;
            }

            .chart-card-header h6 {
                font-size: 0.85rem;
            }

            .chart-badge {
                font-size: 0.55rem;
                padding: 3px 8px;
            }

            .economy-item {
                padding: 8px 10px;
                gap: 10px;
            }

            .economy-icon {
                width: 30px;
                height: 30px;
                font-size: 0.85rem;
                border-radius: 8px;
            }

            .economy-value {
                font-size: 0.8rem;
            }

            .economy-label {
                font-size: 0.55rem;
            }

            .btn-analytics {
                padding: 8px 14px;
                font-size: 0.75rem;
            }
        }

        /* ============================================
                   SCROLLBAR STYLING
                   ============================================ */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts" defer></script>
    <script>
        function formatGolongan(g) {
            if (g === null || g === undefined || g === '') return 'Unknown';
            const str = String(g).trim();
            const match = str.match(/(\d+)$/);
            const num = match ? match[1] : str;
            if (num === '0' || str.toUpperCase() === 'KIP' || str.toUpperCase() === 'GOLONGAN KIP') return 'KIP';
            return str.toLowerCase().startsWith('golongan') ? str : 'Golongan ' + str;
        }

        document.addEventListener('DOMContentLoaded', async function() {
            // Show loading state
            const chartIds = ['chart-distribusi-ukt', 'chart-per-prodi', 'chart-trend', 'chart-kip-nonkip',
                'chart-kip-prodi'
            ];
            chartIds.forEach(id => {
                const container = document.getElementById(id);
                if (container) {
                    container.innerHTML =
                        '<div class="chart-loading"><div class="loading-spinner"></div></div>';
                }
            });

            // Chart Distribusi UKT
            try {
                const resUkt = await fetch(withPeriode('{{ route('api.statistik.distribusi-ukt') }}'));
                const dataUkt = await resUkt.json();

                const uktContainer = document.querySelector('#chart-distribusi-ukt');
                if (uktContainer && dataUkt && dataUkt.length > 0) {
                    uktContainer.innerHTML = '';
                    const chart = new ApexCharts(uktContainer, {
                        series: dataUkt.map(d => d.total || 0),
                        labels: dataUkt.map(d => formatGolongan(d.golongan)),
                        chart: {
                            type: 'donut',
                            height: 280,
                            toolbar: {
                                show: false
                            }
                        },
                        colors: (function() {
                            const palette = ['#3b82f6', '#8b5cf6', '#06b6d4', '#10b981',
                                '#ef4444',
                                '#ec489a', '#6366f1'
                            ];
                            let idx = 0;
                            return dataUkt.map(d => formatGolongan(d.golongan) === 'KIP' ?
                                '#f59e0b' : palette[idx++ % palette.length]);
                        })(),
                        legend: {
                            position: 'bottom',
                            fontSize: '11px',
                            markers: {
                                width: 10,
                                height: 10,
                                radius: 10
                            }
                        },
                        plotOptions: {
                            pie: {
                                donut: {
                                    size: '65%',
                                    labels: {
                                        show: true,
                                        total: {
                                            show: true,
                                            label: 'Total',
                                            formatter: () => dataUkt.reduce((a, b) => a + (b.total ||
                                                0), 0)
                                        }
                                    }
                                }
                            }
                        },
                        dataLabels: {
                            enabled: false
                        },
                        tooltip: {
                            y: {
                                formatter: (val) => val + ' Mahasiswa'
                            }
                        },
                        responsive: [{
                            breakpoint: 768,
                            options: {
                                chart: {
                                    height: 220
                                },
                                legend: {
                                    fontSize: '10px'
                                }
                            }
                        }]
                    });
                    chart.render();
                } else if (uktContainer) {
                    uktContainer.innerHTML =
                        '<div class="text-center text-muted py-4"><i class="bi bi-pie-chart fs-1"></i><p class="mt-2 mb-0">Belum ada data</p></div>';
                }
            } catch (e) {
                console.error('Error UKT chart:', e);
                const container = document.querySelector('#chart-distribusi-ukt');
                if (container) {
                    container.innerHTML =
                        '<div class="text-center text-danger py-4"><i class="bi bi-exclamation-triangle fs-1"></i><p class="mt-2 mb-0">Gagal memuat data</p></div>';
                }
            }

            // Chart Per Prodi
            try {
                const resProdi = await fetch(withPeriode('{{ route('api.statistik.per-prodi') }}'));
                const dataProdi = await resProdi.json();

                const prodiContainer = document.querySelector('#chart-per-prodi');
                if (prodiContainer && dataProdi && dataProdi.length > 0) {
                    prodiContainer.innerHTML = '';
                    const chart = new ApexCharts(prodiContainer, {
                        series: [{
                            name: 'Pendaftar',
                            data: dataProdi.map(d => d.total || 0)
                        }],
                        chart: {
                            type: 'bar',
                            height: 280,
                            toolbar: {
                                show: false
                            }
                        },
                        xaxis: {
                            categories: dataProdi.map(d => d.prodi || 'Unknown'),
                            labels: {
                                rotate: -45,
                                style: {
                                    fontSize: '10px'
                                }
                            }
                        },
                        colors: ['#10b981'],
                        plotOptions: {
                            bar: {
                                borderRadius: 8,
                                horizontal: true,
                                barHeight: '70%'
                            }
                        },
                        dataLabels: {
                            enabled: true,
                            formatter: (val) => val,
                            style: {
                                fontSize: '10px',
                                fontWeight: 'bold'
                            }
                        },
                        tooltip: {
                            y: {
                                formatter: (val) => val + ' Pendaftar'
                            }
                        },
                        grid: {
                            borderColor: '#f1f5f9'
                        },
                        responsive: [{
                            breakpoint: 768,
                            options: {
                                chart: {
                                    height: 220
                                },
                                xaxis: {
                                    labels: {
                                        style: {
                                            fontSize: '8px'
                                        }
                                    }
                                }
                            }
                        }]
                    });
                    chart.render();
                } else if (prodiContainer) {
                    prodiContainer.innerHTML =
                        '<div class="text-center text-muted py-4"><i class="bi bi-bar-chart fs-1"></i><p class="mt-2 mb-0">Belum ada data</p></div>';
                }
            } catch (e) {
                console.error('Error Prodi chart:', e);
                const container = document.querySelector('#chart-per-prodi');
                if (container) {
                    container.innerHTML =
                        '<div class="text-center text-danger py-4"><i class="bi bi-exclamation-triangle fs-1"></i><p class="mt-2 mb-0">Gagal memuat data</p></div>';
                }
            }

            // Chart Trend
            try {
                const trendLabels = @json($trendTahunan->pluck('nama') ?? []);
                const trendData = @json($trendTahunan->pluck('pendaftaran_count') ?? []);

                const trendContainer = document.querySelector('#chart-trend');
                if (trendContainer && trendLabels && trendLabels.length > 0) {
                    trendContainer.innerHTML = '';
                    const chart = new ApexCharts(trendContainer, {
                        series: [{
                            name: 'Pendaftar',
                            data: trendData
                        }],
                        chart: {
                            type: 'area',
                            height: 280,
                            toolbar: {
                                show: false
                            }
                        },
                        xaxis: {
                            categories: trendLabels,
                            labels: {
                                style: {
                                    fontSize: '11px'
                                }
                            }
                        },
                        colors: ['#3b82f6'],
                        fill: {
                            type: 'gradient',
                            gradient: {
                                shadeIntensity: 1,
                                opacityFrom: 0.4,
                                opacityTo: 0.05
                            }
                        },
                        stroke: {
                            curve: 'smooth',
                            width: 3
                        },
                        dataLabels: {
                            enabled: false
                        },
                        tooltip: {
                            y: {
                                formatter: (val) => val + ' Pendaftar'
                            }
                        },
                        markers: {
                            size: 4,
                            colors: ['#3b82f6'],
                            strokeColors: '#fff',
                            strokeWidth: 2
                        },
                        grid: {
                            borderColor: '#f1f5f9'
                        },
                        responsive: [{
                            breakpoint: 768,
                            options: {
                                chart: {
                                    height: 220
                                }
                            }
                        }]
                    });
                    chart.render();
                } else if (trendContainer) {
                    trendContainer.innerHTML =
                        '<div class="text-center text-muted py-4"><i class="bi bi-graph-up fs-1"></i><p class="mt-2 mb-0">Belum ada data</p></div>';
                }
            } catch (e) {
                console.error('Error Trend chart:', e);
                const container = document.querySelector('#chart-trend');
                if (container) {
                    container.innerHTML =
                        '<div class="text-center text-danger py-4"><i class="bi bi-exclamation-triangle fs-1"></i><p class="mt-2 mb-0">Gagal memuat data</p></div>';
                }
            }

            // Chart KIP vs Non-KIP
            try {
                const kipData = [
                    {{ $stats['total_kip'] ?? 0 }},
                    {{ $stats['total_non_kip'] ?? 0 }}
                ];
                const kipContainer = document.querySelector('#chart-kip-nonkip');
                if (kipContainer) {
                    kipContainer.innerHTML = '';
                    const chart = new ApexCharts(kipContainer, {
                        series: kipData,
                        labels: ['KIP', 'Non-KIP'],
                        chart: {
                            type: 'donut',
                            height: 280,
                            toolbar: {
                                show: false
                            }
                        },
                        colors: ['#f59e0b', '#8b5cf6'],
                        legend: {
                            position: 'bottom',
                            fontSize: '12px'
                        },
                        plotOptions: {
                            pie: {
                                donut: {
                                    size: '65%',
                                    labels: {
                                        show: true,
                                        total: {
                                            show: true,
                                            label: 'Total',
                                            formatter: () => kipData.reduce((a, b) => a + b, 0)
                                        }
                                    }
                                }
                            }
                        },
                        dataLabels: {
                            enabled: true
                        },
                        tooltip: {
                            y: {
                                formatter: (val) => val + ' Mahasiswa'
                            }
                        },
                        responsive: [{
                            breakpoint: 768,
                            options: {
                                chart: {
                                    height: 220
                                }
                            }
                        }]
                    });
                    chart.render();
                }
            } catch (e) {
                console.error('Error KIP chart:', e);
            }

            // Chart KIP per Prodi
            try {
                const resKipProdi = await fetch(withPeriode('{{ route('api.statistik.kip-per-prodi') }}'));
                const dataKipProdi = await resKipProdi.json();
                const kipProdiContainer = document.querySelector('#chart-kip-prodi');

                if (kipProdiContainer && dataKipProdi && dataKipProdi.length > 0) {
                    kipProdiContainer.innerHTML = '';
                    const chart = new ApexCharts(kipProdiContainer, {
                        series: [{
                            name: 'KIP',
                            data: dataKipProdi.map(d => d.kip || 0)
                        }, {
                            name: 'Non-KIP',
                            data: dataKipProdi.map(d => d.non_kip || 0)
                        }],
                        chart: {
                            type: 'bar',
                            height: 280,
                            stacked: true,
                            toolbar: {
                                show: false
                            }
                        },
                        xaxis: {
                            categories: dataKipProdi.map(d => d.prodi || 'Unknown'),
                            labels: {
                                style: {
                                    fontSize: '10px'
                                }
                            }
                        },
                        colors: ['#f59e0b', '#8b5cf6'],
                        plotOptions: {
                            bar: {
                                borderRadius: 4,
                                horizontal: true,
                                barHeight: '70%'
                            }
                        },
                        legend: {
                            position: 'top'
                        },
                        tooltip: {
                            y: {
                                formatter: (val) => val + ' Mahasiswa'
                            }
                        },
                        grid: {
                            borderColor: '#f1f5f9'
                        },
                        responsive: [{
                            breakpoint: 768,
                            options: {
                                chart: {
                                    height: 220
                                },
                                xaxis: {
                                    labels: {
                                        style: {
                                            fontSize: '8px'
                                        }
                                    }
                                }
                            }
                        }]
                    });
                    chart.render();
                } else if (kipProdiContainer) {
                    kipProdiContainer.innerHTML =
                        '<div class="text-center text-muted py-4"><i class="bi bi-bar-chart fs-1"></i><p class="mt-2 mb-0">Belum ada data</p></div>';
                }
            } catch (e) {
                console.error('Error KIP Prodi chart:', e);
                const container = document.querySelector('#chart-kip-prodi');
                if (container) {
                    container.innerHTML =
                        '<div class="text-center text-danger py-4"><i class="bi bi-exclamation-triangle fs-1"></i><p class="mt-2 mb-0">Gagal memuat data</p></div>';
                }
            }
        });
    </script>
@endpush
