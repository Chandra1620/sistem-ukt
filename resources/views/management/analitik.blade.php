@extends('layouts.app')
@section('title', 'Analitik Lengkap')
@section('page-title', 'Analitik Lengkap')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h5 class="fw-bold mb-1">
                <i class="bi bi-graph-up-arrow text-primary me-2"></i>
                Analitik Lengkap
            </h5>
            <p class="text-muted small mb-0">Data statistik dan analisis mendalam sistem UKT</p>
        </div>
        <div class="stats-ringkasan">
            <div class="ringkasan-item">
                <span class="ringkasan-label">Total Pendaftar</span>
                <span class="ringkasan-value">{{ $statsEkonomi['total_pendaftar'] ?? 0 }}</span>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        {{-- Statistik Ekonomi Premium --}}
        <div class="col-md-3">
            <div class="stat-card stat-card-primary">
                <div class="stat-icon-wrapper">
                    <i class="bi bi-graph-up"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Rata-rata Penghasilan</div>
                    <div class="stat-value">Rp {{ number_format($statsEkonomi['rata_rata'], 0, ',', '.') }}</div>
                </div>
                <div class="stat-trend">
                    <i class="bi bi-calculator"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card stat-card-success">
                <div class="stat-icon-wrapper">
                    <i class="bi bi-bar-chart"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Median Penghasilan</div>
                    <div class="stat-value">Rp {{ number_format($statsEkonomi['median'], 0, ',', '.') }}</div>
                </div>
                <div class="stat-trend">
                    <i class="bi bi-arrow-left-right"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card stat-card-warning">
                <div class="stat-icon-wrapper">
                    <i class="bi bi-arrow-down-circle"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Penghasilan Minimum</div>
                    <div class="stat-value">Rp {{ number_format($statsEkonomi['minimum'], 0, ',', '.') }}</div>
                </div>
                <div class="stat-trend">
                    <i class="bi bi-arrow-down"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card stat-card-danger">
                <div class="stat-icon-wrapper">
                    <i class="bi bi-arrow-up-circle"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Penghasilan Maximum</div>
                    <div class="stat-value">Rp {{ number_format($statsEkonomi['maximum'], 0, ',', '.') }}</div>
                </div>
                <div class="stat-trend">
                    <i class="bi bi-arrow-up"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        {{-- Distribusi UKT Premium --}}
        <div class="col-md-5">
            <div class="card chart-card h-100">
                <div class="card-header chart-card-header">
                    <div class="d-flex align-items-center gap-2">
                        <div class="header-icon purple-icon">
                            <i class="bi bi-pie-chart-fill"></i>
                        </div>
                        <h6 class="fw-semibold mb-0">Distribusi Golongan UKT</h6>
                    </div>
                    <div class="chart-badge">
                        <i class="bi bi-trophy"></i> {{ $distribusiUkt->sum('total') }} Mahasiswa
                    </div>
                </div>
                <div class="card-body">
                    <div id="chart-distribusi-ukt" class="chart-container"></div>
                    <div class="distribusi-list mt-3">
                        @foreach ($distribusiUkt as $d)
                            <div class="distribusi-item">
                                <div class="distribusi-label">
                                    <span class="golongan-dot"
                                        style="background: {{ $d['is_kip'] ? '#f59e0b' : '' }};
    {{ !$d['is_kip'] ? '' : '' }}">
                                    </span>
                                    <span class="small">{{ $d['golongan'] }}</span>
                                </div>
                                <div class="distribusi-value">
                                    <span class="badge-golongan">{{ $d['total'] }} mahasiswa</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Heatmap Wilayah Premium --}}
        <div class="col-md-7">
            <div class="card chart-card h-100">
                <div class="card-header chart-card-header">
                    <div class="d-flex align-items-center gap-2">
                        <div class="header-icon danger-icon">
                            <i class="bi bi-geo-alt-fill"></i>
                        </div>
                        <h6 class="fw-semibold mb-0">Sebaran Wilayah Pendaftar</h6>
                    </div>
                    <div class="chart-badge">
                        <i class="bi bi-map"></i> Top 10 Provinsi
                    </div>
                </div>
                <div class="card-body p-0">
                    @php $totalWilayah = $heatmapWilayah->sum('total'); @endphp
                    <div class="wilayah-list" id="wilayah-list">
                        @foreach ($heatmapWilayah->take(10) as $i => $w)
                            <div class="wilayah-group">
                                <div class="wilayah-item" role="button" data-provinsi="{{ $w->provinsi }}"
                                    onclick="toggleWilayah(this)">
                                    <div class="wilayah-rank">{{ $i + 1 }}</div>
                                    <div class="wilayah-info">
                                        <div class="wilayah-name">
                                            {{ $w->provinsi ?: 'Tidak diketahui' }}
                                            <i class="bi bi-chevron-down wilayah-caret"></i>
                                        </div>
                                        <div class="wilayah-progress">
                                            <div class="progress-bar-modern">
                                                <div class="progress-fill"
                                                    style="width: {{ $totalWilayah > 0 ? round(($w->total / $totalWilayah) * 100, 1) : 0 }}%">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="wilayah-total">
                                        <span class="wilayah-count">{{ $w->total }}</span>
                                        <span
                                            class="wilayah-persen">{{ $totalWilayah > 0 ? round(($w->total / $totalWilayah) * 100, 1) : 0 }}%</span>
                                    </div>
                                </div>
                                <div class="wilayah-detail" style="display:none;"></div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        {{-- Distribusi Per Prodi Premium --}}
        <div class="col-md-7">
            <div class="card chart-card h-100">
                <div class="card-header chart-card-header">
                    <div class="d-flex align-items-center gap-2">
                        <div class="header-icon success-icon">
                            <i class="bi bi-bar-chart-steps"></i>
                        </div>
                        <h6 class="fw-semibold mb-0">Distribusi per Program Studi</h6>
                    </div>
                    <div class="chart-badge">
                        <i class="bi bi-building"></i> {{ $dataProdi->count() }} Prodi
                    </div>
                </div>
                <div class="card-body">
                    <div id="chart-per-prodi" class="chart-container"></div>
                </div>
            </div>
        </div>

        {{-- Distribusi Pekerjaan Premium --}}
        <div class="col-md-5">
            <div class="card chart-card h-100">
                <div class="card-header chart-card-header">
                    <div class="d-flex align-items-center gap-2">
                        <div class="header-icon orange-icon">
                            <i class="bi bi-briefcase-fill"></i>
                        </div>
                        <h6 class="fw-semibold mb-0">Pekerjaan Orang Tua</h6>
                    </div>
                    <div class="chart-badge">
                        <i class="bi bi-person"></i> Pekerjaan Ayah
                    </div>
                </div>
                <div class="card-body p-0">
                    @if ($distribusiPekerjaan->count() > 0)
                        <div class="pekerjaan-list">
                            @foreach ($distribusiPekerjaan as $p)
                                <div class="pekerjaan-item">
                                    <div class="pekerjaan-icon">
                                        <i class="bi bi-person-badge"></i>
                                    </div>
                                    <div class="pekerjaan-info">
                                        <div class="pekerjaan-name">
                                            {{ ucfirst(str_replace('_', ' ', $p->pekerjaan_ayah)) }}</div>
                                        <div class="pekerjaan-progress">
                                            <div class="progress-bar-small">
                                                <div class="progress-fill-small"
                                                    style="width: {{ $distribusiPekerjaan->max('total') > 0 ? round(($p->total / $distribusiPekerjaan->max('total')) * 100, 1) : 0 }}%">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="pekerjaan-total">
                                        <span class="pekerjaan-count">{{ $p->total }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state-small text-center py-5">
                            <i class="bi bi-inbox fs-1 text-muted"></i>
                            <p class="text-muted mt-2 mb-0">Belum ada data pekerjaan</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Trend Tahunan Premium --}}
    <div class="card chart-card" id="trend">
        <div class="card-header chart-card-header">
            <div class="d-flex align-items-center gap-2">
                <div class="header-icon info-icon">
                    <i class="bi bi-graph-up"></i>
                </div>
                <h6 class="fw-semibold mb-0">Trend Pendaftar per Periode</h6>
            </div>
            <div class="chart-badge">
                <i class="bi bi-calendar-range"></i> {{ $trendTahunan->count() }} Periode
            </div>
        </div>
        <div class="card-body">
            <div id="chart-trend-tahunan" class="chart-container"></div>
            <div class="table-responsive mt-4">
                <table class="table trend-table">
                    <thead>
                        <tr>
                            <th><i class="bi bi-calendar3"></i> Periode</th>
                            <th><i class="bi bi-calendar-year"></i> Tahun</th>
                            <th><i class="bi bi-people"></i> Total Pendaftar</th>
                            <th><i class="bi bi-circle-fill"></i> Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($trendTahunan as $t)
                            <tr class="trend-row">
                                <td data-label="Periode" class="fw-semibold">{{ $t->nama }}</td>
                                <td data-label="Tahun">{{ $t->tahun }}</td>
                                <td data-label="Total Pendaftar">
                                    <span class="pendaftar-badge-sm">
                                        <i class="bi bi-person"></i>
                                        {{ $t->pendaftaran_count }}
                                    </span>
                                </td>
                                <td data-label="Status">
                                    @if ($t->status === 'aktif')
                                        <span class="status-badge-sm status-active">
                                            <i class="bi bi-check-circle-fill"></i> Aktif
                                        </span>
                                    @elseif($t->status === 'selesai')
                                        <span class="status-badge-sm status-done">
                                            <i class="bi bi-flag-checkered"></i> Selesai
                                        </span>
                                    @else
                                        <span class="status-badge-sm status-inactive">
                                            <i class="bi bi-pause-circle"></i> Nonaktif
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="empty-state-small text-center py-4">
                                    <i class="bi bi-inbox fs-3 text-muted"></i>
                                    <p class="text-muted mt-2 mb-0">Belum ada data periode</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <style>
        /* Stats Ringkasan */
        .stats-ringkasan {
            background: white;
            padding: 8px 20px;
            border-radius: 60px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }

        .ringkasan-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .ringkasan-label {
            font-size: 0.7rem;
            color: #64748b;
        }

        .ringkasan-value {
            font-weight: 800;
            font-size: 1rem;
            color: #1e293b;
            background: #f1f5f9;
            padding: 2px 10px;
            border-radius: 40px;
        }

        /* Stat Cards */
        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            position: relative;
            overflow: hidden;
            height: 100%;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, currentColor, transparent);
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
        }

        .stat-card-primary {
            color: #3b82f6;
        }

        .stat-card-success {
            color: #10b981;
        }

        .stat-card-warning {
            color: #f59e0b;
        }

        .stat-card-danger {
            color: #ef4444;
        }

        .stat-icon-wrapper {
            width: 48px;
            height: 48px;
            background: rgba(0, 0, 0, 0.04);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            transition: all 0.3s;
        }

        .stat-card:hover .stat-icon-wrapper {
            transform: scale(1.05);
            background: rgba(0, 0, 0, 0.08);
        }

        .stat-info {
            flex: 1;
            text-align: center;
            padding: 0 8px;
        }

        .stat-label {
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            font-weight: 600;
        }

        .stat-value {
            font-size: 0.9rem;
            font-weight: 800;
            color: #1e293b;
            line-height: 1.2;
        }

        .stat-trend {
            width: 28px;
            height: 28px;
            background: rgba(0, 0, 0, 0.04);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            transition: all 0.3s;
        }

        /* Chart Cards */
        .chart-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .chart-card:hover {
            box-shadow: 0 8px 28px rgba(0, 0, 0, 0.08);
        }

        .chart-card-header {
            background: white;
            border-bottom: 1px solid #f0f2f5;
            padding: 14px 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .header-icon {
            width: 32px;
            height: 32px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }

        .purple-icon {
            background: #ede9fe;
            color: #8b5cf6;
        }

        .danger-icon {
            background: #fee2e2;
            color: #ef4444;
        }

        .success-icon {
            background: #d1fae5;
            color: #10b981;
        }

        .orange-icon {
            background: #fed7aa;
            color: #f59e0b;
        }

        .info-icon {
            background: #cffafe;
            color: #06b6d4;
        }

        .chart-badge {
            background: #f1f5f9;
            padding: 4px 12px;
            border-radius: 40px;
            font-size: 0.65rem;
            font-weight: 500;
            color: #475569;
        }

        .chart-container {
            min-height: 250px;
        }

        /* Distribusi List */
        .distribusi-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .distribusi-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .distribusi-label {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .golongan-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }

        .gol-1 {
            background: #fef3c7;
        }

        .gol-2 {
            background: #fed7aa;
        }

        .gol-3 {
            background: #fee2e2;
        }

        .gol-4 {
            background: #d1fae5;
        }

        .gol-5 {
            background: #dbeafe;
        }

        .gol-6 {
            background: #cffafe;
        }

        .gol-7 {
            background: #e2e8f0;
        }

        .gol-8 {
            background: #f1f5f9;
        }

        .badge-golongan {
            background: #eef2ff;
            color: #3b82f6;
            padding: 2px 10px;
            border-radius: 40px;
            font-size: 0.65rem;
            font-weight: 600;
        }

        /* Wilayah List */
        .wilayah-list {
            padding: 8px 0;
        }

        .wilayah-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 16px;
            border-bottom: 1px solid #f1f5f9;
            transition: all 0.2s;
        }

        .wilayah-item:hover {
            background: #f8fafc;
        }

        .wilayah-rank {
            width: 28px;
            height: 28px;
            background: #f1f5f9;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.7rem;
            color: #64748b;
        }

        .wilayah-info {
            flex: 1;
        }

        .wilayah-name {
            font-weight: 600;
            font-size: 0.8rem;
            color: #1e293b;
            margin-bottom: 4px;
        }

        .wilayah-progress {
            width: 100%;
        }

        .progress-bar-modern {
            height: 6px;
            background: #e2e8f0;
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #ef4444, #f87171);
            border-radius: 10px;
            transition: width 0.3s ease;
        }

        .wilayah-total {
            text-align: right;
            min-width: 70px;
        }

        .wilayah-count {
            font-weight: 700;
            font-size: 0.8rem;
            color: #1e293b;
        }

        .wilayah-persen {
            font-size: 0.6rem;
            color: #94a3b8;
            margin-left: 4px;
        }

        /* Pekerjaan List */
        .pekerjaan-list {
            padding: 8px 0;
        }

        .pekerjaan-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 16px;
            border-bottom: 1px solid #f1f5f9;
            transition: all 0.2s;
        }

        .pekerjaan-item:hover {
            background: #f8fafc;
        }

        .pekerjaan-icon {
            width: 32px;
            height: 32px;
            background: #fef3c7;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #f59e0b;
            font-size: 0.9rem;
        }

        .pekerjaan-info {
            flex: 1;
        }

        .pekerjaan-name {
            font-weight: 600;
            font-size: 0.75rem;
            color: #1e293b;
            margin-bottom: 4px;
        }

        .progress-bar-small {
            height: 4px;
            background: #e2e8f0;
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-fill-small {
            height: 100%;
            background: linear-gradient(90deg, #f59e0b, #fbbf24);
            border-radius: 10px;
            transition: width 0.3s ease;
        }

        .pekerjaan-count {
            font-weight: 700;
            font-size: 0.8rem;
            color: #1e293b;
            background: #f1f5f9;
            padding: 2px 8px;
            border-radius: 40px;
        }

        /* Trend Table */
        .trend-table {
            width: 100%;
            border-collapse: collapse;
        }

        .trend-table thead th {
            background: #f8fafc;
            padding: 12px 16px;
            font-size: 0.65rem;
            font-weight: 600;
            text-transform: uppercase;
            color: #64748b;
            border-bottom: 2px solid #e2e8f0;
        }

        .trend-table tbody td {
            padding: 10px 16px;
            border-bottom: 1px solid #f1f5f9;
        }

        .pendaftar-badge-sm {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: #eef2ff;
            color: #3b82f6;
            padding: 2px 10px;
            border-radius: 40px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .status-badge-sm {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 10px;
            border-radius: 40px;
            font-size: 0.65rem;
            font-weight: 600;
        }

        .status-active {
            background: #d1fae5;
            color: #065f46;
        }

        .status-done {
            background: #e2e8f0;
            color: #1e293b;
        }

        .status-inactive {
            background: #f1f5f9;
            color: #475569;
        }

        /* Empty State */
        .empty-state-small {
            text-align: center;
            padding: 40px 20px;
        }

        .gol-0 {
            background: #fbbf24;
            /* warna beda supaya KIP kelihatan menonjol */
        }

        .wilayah-item {
            cursor: pointer;
        }

        .wilayah-caret {
            font-size: 0.7rem;
            margin-left: 6px;
            color: #94a3b8;
        }

        .wilayah-detail {
            padding: 8px 16px 12px 56px;
            background: #f8fafc;
        }

        .daerah-item {
            display: flex;
            justify-content: space-between;
            padding: 6px 10px;
            font-size: 0.75rem;
            color: #475569;
            border-bottom: 1px dashed #e2e8f0;
        }

        .daerah-item:last-child {
            border-bottom: none;
        }

        .daerah-count {
            font-weight: 600;
            color: #1e293b;
        }

        .daerah-header {
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            border-radius: 4px 4px 0 0;
        }

        .daerah-list {
            padding: 4px 0;
        }

        .daerah-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 6px 12px;
            font-size: 12px;
            border-bottom: 1px solid #f1f5f9;
        }

        .daerah-item:last-child {
            border-bottom: none;
        }

        .daerah-rank {
            min-width: 18px;
            height: 18px;
            background: #e0e7ff;
            color: #4f46e5;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: 600;
        }

        .daerah-name {
            flex: 1;
            color: #374151;
            font-weight: 500;
        }

        .daerah-bar {
            width: 80px;
            height: 4px;
            background: #e2e8f0;
            border-radius: 2px;
            overflow: hidden;
        }

        .daerah-fill {
            height: 100%;
            background: #3b82f6;
            border-radius: 2px;
            transition: width 0.3s ease;
        }

        .daerah-count {
            min-width: 28px;
            text-align: right;
            color: #1f2937;
            font-weight: 600;
        }

        .daerah-persen {
            min-width: 38px;
            text-align: right;
            color: #6b7280;
            font-size: 11px;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .stat-value {
                font-size: 0.8rem;
            }

            .stat-icon-wrapper {
                width: 40px;
                height: 40px;
                font-size: 1.2rem;
            }
        }

        @media (max-width: 768px) {
            .d-flex.justify-content-between {
                flex-direction: column;
                align-items: stretch !important;
            }

            .stats-ringkasan {
                justify-content: center;
            }

            .stat-card {
                padding: 12px;
            }

            .stat-value {
                font-size: 0.7rem;
            }

            .chart-card-header {
                flex-direction: column;
                text-align: center;
            }

            .trend-table thead {
                display: none;
            }

            .trend-table tbody tr {
                display: block;
                margin-bottom: 12px;
                border: 1px solid #e2e8f0;
                border-radius: 12px;
            }

            .trend-table tbody td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 10px 12px;
                border-bottom: 1px solid #f1f5f9;
            }

            .trend-table tbody td:last-child {
                border-bottom: none;
            }

            .trend-table tbody td::before {
                content: attr(data-label);
                font-weight: 600;
                font-size: 0.65rem;
                color: #64748b;
            }
        }

        @media (max-width: 480px) {
            .stat-card {
                flex-direction: column;
                text-align: center;
                gap: 8px;
            }

            .stat-icon-wrapper {
                width: 36px;
                height: 36px;
                font-size: 1rem;
            }

            .stat-value {
                font-size: 0.65rem;
            }

            .wilayah-item {
                flex-wrap: wrap;
            }

            .wilayah-total {
                width: 100%;
                text-align: left;
                margin-left: 40px;
            }
        }
    </style>
@endsection

@push('scripts')
    <script>
        function formatGolongan(g) {
            if (g === null || g === undefined || g === '') return 'Unknown';
            const str = String(g).trim();
            if (str === '0' || str.toLowerCase() === 'kip') return 'KIP';
            return str.toLowerCase().startsWith('golongan') ? str : 'Golongan ' + str;
        }
        document.addEventListener('DOMContentLoaded', async function() {
            // Show loading state
            const charts = ['chart-distribusi-ukt', 'chart-per-prodi', 'chart-trend-tahunan'];
            charts.forEach(id => {
                const container = document.getElementById(id);
                if (container) {
                    container.innerHTML =
                        '<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>';
                }
            });

            // Chart Distribusi UKT
            try {
                const resUkt = await fetch('/api/statistik/distribusi-ukt');
                const dataUkt = await resUkt.json();

                const uktContainer = document.querySelector('#chart-distribusi-ukt');
                if (dataUkt.length > 0) {
                    uktContainer.innerHTML = '';
                    new ApexCharts(uktContainer, {
                        series: dataUkt.map(d => d.total),
                        labels: dataUkt.map(d => formatGolongan(d.golongan)),
                        chart: {
                            type: 'donut',
                            height: 260,
                            toolbar: {
                                show: false
                            }
                        },
                        colors: (function() {
                            const palette = ['#3b82f6', '#8b5cf6', '#06b6d4', '#10b981',
                                '#ef4444', '#ec489a', '#6366f1'
                            ];
                            let idx = 0;
                            return dataUkt.map(d =>
                                formatGolongan(d.golongan) === 'KIP' ?
                                '#f59e0b' :
                                palette[idx++ % palette.length]
                            );
                        })(),
                        legend: {
                            position: 'bottom',
                            fontSize: '10px',
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
                                            formatter: () => dataUkt.reduce((a, b) => a + b.total, 0)
                                        }
                                    }
                                }
                            }
                        },
                        dataLabels: {
                            enabled: false
                        }
                    }).render();
                } else {
                    uktContainer.innerHTML =
                        '<div class="text-center text-muted py-5">Belum ada data distribusi UKT</div>';
                }
            } catch (e) {
                console.error('Error loading UKT chart:', e);
            }

            // Chart Per Prodi
            try {
                const resProdi = await fetch('/api/statistik/per-prodi');
                const dataProdi = await resProdi.json();

                const prodiContainer = document.querySelector('#chart-per-prodi');
                if (dataProdi.length > 0) {
                    prodiContainer.innerHTML = '';
                    new ApexCharts(prodiContainer, {
                        series: [{
                            name: 'Pendaftar',
                            data: dataProdi.map(d => d.total)
                        }],
                        chart: {
                            type: 'bar',
                            height: 280,
                            toolbar: {
                                show: false
                            }
                        },
                        xaxis: {
                            categories: dataProdi.map(d => d.prodi),
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
                        }
                    }).render();
                } else {
                    prodiContainer.innerHTML =
                        '<div class="text-center text-muted py-5">Belum ada data per program studi</div>';
                }
            } catch (e) {
                console.error('Error loading Prodi chart:', e);
            }

            // Chart Trend Tahunan
            try {
                const trendLabels = @json($trendTahunan->pluck('nama'));
                const trendData = @json($trendTahunan->pluck('pendaftaran_count'));

                const trendContainer = document.querySelector('#chart-trend-tahunan');
                if (trendLabels.length > 0) {
                    trendContainer.innerHTML = '';
                    new ApexCharts(trendContainer, {
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
                        markers: {
                            size: 4,
                            colors: ['#3b82f6'],
                            strokeColors: '#fff',
                            strokeWidth: 2
                        }
                    }).render();
                } else {
                    trendContainer.innerHTML =
                        '<div class="text-center text-muted py-5">Belum ada data trend pendaftaran</div>';
                }
            } catch (e) {
                console.error('Error loading Trend chart:', e);
            }
        });
        async function toggleWilayah(el) {
            const group = el.closest('.wilayah-group');
            const detail = group.querySelector('.wilayah-detail');
            const caret = el.querySelector('.wilayah-caret');
            const isOpen = detail.style.display !== 'none';

            if (isOpen) {
                detail.style.display = 'none';
                caret.classList.remove('bi-chevron-up');
                caret.classList.add('bi-chevron-down');
                return;
            }

            document.querySelectorAll('.wilayah-detail').forEach(d => d.style.display = 'none');
            document.querySelectorAll('.wilayah-caret').forEach(c => {
                c.classList.remove('bi-chevron-up');
                c.classList.add('bi-chevron-down');
            });

            detail.style.display = 'block';
            caret.classList.remove('bi-chevron-down');
            caret.classList.add('bi-chevron-up');

            if (detail.dataset.loaded === 'true') return;

            detail.innerHTML =
                '<div class="text-center py-3"><div class="spinner-border spinner-border-sm text-primary"></div></div>';

            const provinsi = el.dataset.provinsi;
            try {
                const res = await fetch(
                    `{{ route('api.statistik.wilayah-detail') }}?provinsi=${encodeURIComponent(provinsi)}`);
                const data = await res.json();

                if (data && data.length > 0) {
                    const totalProvinsi = data.reduce((sum, d) => sum + d.total, 0);
                    detail.innerHTML = `
        <div class="daerah-header px-3 py-2 d-flex justify-content-between align-items-center">
            <span class="small fw-semibold text-muted">${provinsi}</span>
            <span class="small text-muted">${data.length} daerah · ${totalProvinsi} pendaftar</span>
        </div>
        <div class="daerah-list">
            ${data.map((d, i) => `
                        <div class="daerah-item">
                            <span class="daerah-rank">${i + 1}</span>
                            <span class="daerah-name">${d.daerah || 'Tidak diketahui'}</span>
                            <div class="daerah-bar">
                                <div class="daerah-fill"
                                    style="width:${totalProvinsi > 0 ? ((d.total / totalProvinsi) * 100).toFixed(1) : 0}%">
                                </div>
                            </div>
                            <span class="daerah-count">${d.total}</span>
                            <span class="daerah-persen">${totalProvinsi > 0 ? ((d.total / totalProvinsi) * 100).toFixed(1) : 0}%</span>
                        </div>
                    `).join('')}
        </div>`;
                } else {
                    detail.innerHTML = '<div class="text-center text-muted py-2 small">Belum ada rincian daerah</div>';
                }

                detail.dataset.loaded = 'true';
            } catch (e) {
                console.error('Error loading wilayah detail:', e);
                detail.innerHTML = '<div class="text-center text-danger py-2 small">Gagal memuat data daerah</div>';
            }
        }
    </script>
@endpush
