@extends('layouts.app')
@section('title', 'Riwayat Kriteria per Periode')
@section('page-title', 'Riwayat Kriteria per Periode')

@section('content')
    <div class="page-header-wrapper">
        <div class="header-content">
            <div class="header-left">
                <div class="header-icon">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div class="header-text">
                    <h1 class="header-title">Riwayat Kriteria</h1>
                    <p class="header-subtitle">Ringkasan konfigurasi kriteria SAW per periode seleksi</p>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.periode.bandingkan') }}" class="btn btn-outline-compare">
                    <i class="bi bi-arrow-left-right"></i>
                    <span>Bandingkan</span>
                </a>
                <a href="{{ route('admin.periode.index') }}" class="btn btn-outline-back">
                    <i class="bi bi-arrow-left"></i>
                    <span>Kembali</span>
                </a>
            </div>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon total">
                <i class="bi bi-calendar3"></i>
            </div>
            <div class="stat-info">
                <span class="stat-value">{{ count($riwayat) }}</span>
                <span class="stat-label">Total Periode</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon kriteria">
                <i class="bi bi-list-check"></i>
            </div>
            <div class="stat-info">
                <span class="stat-value">{{ collect($riwayat)->sum('jumlah_kriteria') }}</span>
                <span class="stat-label">Total Kriteria</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon sub">
                <i class="bi bi-diagram-3"></i>
            </div>
            <div class="stat-info">
                <span class="stat-value">{{ collect($riwayat)->sum('jumlah_sub') }}</span>
                <span class="stat-label">Total Sub Kriteria</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon valid">
                <i class="bi bi-check-circle"></i>
            </div>
            <div class="stat-info">
                @php
                    $validCount = collect($riwayat)
                        ->filter(function ($r) {
                            return (float) $r['total_bobot'] == 100;
                        })
                        ->count();
                @endphp
                <span class="stat-value">{{ $validCount }}</span>
                <span class="stat-label">Bobot 100%</span>
            </div>
        </div>
    </div>

    <div class="table-container">
        <div class="table-header">
            <div class="table-header-left">
                <i class="bi bi-table"></i>
                <span>Daftar Riwayat Periode</span>
                <span class="table-count">{{ count($riwayat) }} periode</span>
            </div>
            <div class="table-header-right">
                <div class="search-wrapper">
                    <i class="bi bi-search"></i>
                    <input type="text" id="tableSearch" placeholder="Cari periode..." class="search-input">
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="premium-table">
                <thead>
                    <tr>
                        <th class="col-name">
                            <div class="th-content">
                                <i class="bi bi-tag"></i>
                                <span>Nama Periode</span>
                            </div>
                        </th>
                        <th class="col-year">
                            <div class="th-content">
                                <i class="bi bi-calendar-year"></i>
                                <span>Tahun</span>
                            </div>
                        </th>
                        <th class="col-start">
                            <div class="th-content">
                                <i class="bi bi-calendar-plus"></i>
                                <span>Mulai</span>
                            </div>
                        </th>
                        <th class="col-end">
                            <div class="th-content">
                                <i class="bi bi-calendar-minus"></i>
                                <span>Selesai</span>
                            </div>
                        </th>
                        <th class="col-status">
                            <div class="th-content">
                                <i class="bi bi-circle-fill"></i>
                                <span>Status</span>
                            </div>
                        </th>
                        <th class="col-kriteria">
                            <div class="th-content">
                                <i class="bi bi-list-check"></i>
                                <span>Kriteria</span>
                            </div>
                        </th>
                        <th class="col-sub">
                            <div class="th-content">
                                <i class="bi bi-diagram-3"></i>
                                <span>Sub</span>
                            </div>
                        </th>
                        <th class="col-bobot">
                            <div class="th-content">
                                <i class="bi bi-percent"></i>
                                <span>Bobot</span>
                            </div>
                        </th>
                        <th class="col-pendaftar">
                            <div class="th-content">
                                <i class="bi bi-people"></i>
                                <span>Pendaftar</span>
                            </div>
                        </th>
                        <th class="col-action">
                            <div class="th-content">
                                <i class="bi bi-eye"></i>
                                <span>Aksi</span>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayat as $r)
                        <tr class="period-row" data-bobot="{{ (float) $r['total_bobot'] }}">
                            <td class="col-name" data-label="Nama Periode">
                                <div class="period-info">
                                    <div class="period-icon">
                                        <i class="bi bi-calendar3"></i>
                                    </div>
                                    <div class="period-detail">
                                        <span class="period-name">{{ $r['nama'] }}</span>
                                        <span class="period-meta">
                                            <span class="meta-year">{{ $r['tahun'] }}</span>
                                            <span class="meta-divider">•</span>
                                            <span class="meta-pendaftar">
                                                <i class="bi bi-person"></i>
                                                {{ $r['jumlah_pendaftar'] }} pendaftar
                                            </span>
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="col-year" data-label="Tahun">
                                <span class="year-badge">{{ $r['tahun'] }}</span>
                            </td>
                            <td class="col-start" data-label="Mulai">
                                <span
                                    class="date-text">{{ \Carbon\Carbon::parse($r['tanggal_mulai'])->format('d M Y') }}</span>
                            </td>
                            <td class="col-end" data-label="Selesai">
                                <span
                                    class="date-text">{{ \Carbon\Carbon::parse($r['tanggal_selesai'])->format('d M Y') }}</span>
                            </td>
                            <td class="col-status" data-label="Status">
                                @if ($r['status'] === 'aktif')
                                    <span class="status-badge status-active">
                                        <span class="status-dot"></span>
                                        Aktif
                                    </span>
                                @elseif($r['status'] === 'selesai')
                                    <span class="status-badge status-done">
                                        <span class="status-dot"></span>
                                        Selesai
                                    </span>
                                @else
                                    <span class="status-badge status-inactive">
                                        <span class="status-dot"></span>
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="col-kriteria" data-label="Kriteria">
                                <span class="count-badge kriteria-badge">
                                    <i class="bi bi-list-check"></i>
                                    {{ $r['jumlah_kriteria'] }}
                                </span>
                            </td>
                            <td class="col-sub" data-label="Sub">
                                <span class="count-badge sub-badge">
                                    <i class="bi bi-diagram-3"></i>
                                    {{ $r['jumlah_sub'] }}
                                </span>
                            </td>
                            <td class="col-bobot" data-label="Bobot">
                                @php $bobot = (float) $r['total_bobot']; @endphp
                                <span class="bobot-badge {{ $bobot == 100 ? 'bobot-valid' : 'bobot-invalid' }}">
                                    @if ($bobot == 100)
                                        <i class="bi bi-check-circle-fill"></i>
                                    @else
                                        <i class="bi bi-exclamation-triangle-fill"></i>
                                    @endif
                                    {{ number_format($bobot, 0) }}%
                                </span>
                            </td>
                            <td class="col-pendaftar" data-label="Pendaftar">
                                <span class="pendaftar-count">{{ $r['jumlah_pendaftar'] }}</span>
                            </td>
                            <td class="col-action" data-label="Aksi">
                                <a href="{{ route('admin.periode.show', $r['id']) }}" class="action-btn view-btn"
                                    title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="empty-state">
                                <div class="empty-state-content">
                                    <div class="empty-icon-wrapper">
                                        <i class="bi bi-clock-history"></i>
                                    </div>
                                    <h5>Belum Ada Riwayat</h5>
                                    <p>Belum ada data riwayat periode seleksi yang tersedia</p>
                                    <a href="{{ route('admin.periode.create') }}" class="btn btn-primary btn-empty">
                                        <i class="bi bi-plus-lg"></i>
                                        Buat Periode Pertama
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="info-alert-bottom">
            <div class="info-alert">
                <div class="info-alert-icon">
                    <i class="bi bi-info-circle-fill"></i>
                </div>
                <div class="info-alert-content">
                    <span class="info-alert-title">Informasi Bobot</span>
                    <span class="info-alert-text">
                        <strong>Total Bobot</strong> idealnya = 100%. Apabila tidak 100%, hasil perhitungan SAW
                        tidak akan mencapai skala penuh (0–100). Pastikan jumlah bobot semua kriteria pada
                        setiap periode sudah tepat 100%.
                        <span class="bobot-legend">
                            <span class="legend-item">
                                <span class="legend-dot valid-dot"></span> Valid (100%)
                            </span>
                            <span class="legend-item">
                                <span class="legend-dot invalid-dot"></span> Tidak Valid (&ne; 100%)
                            </span>
                        </span>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* ============================================ */
        /* PAGE HEADER */
        /* ============================================ */
        .page-header-wrapper {
            margin-bottom: 32px;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .header-icon {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
            flex-shrink: 0;
        }

        .header-text {
            display: flex;
            flex-direction: column;
        }

        .header-title {
            font-size: 24px;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
            line-height: 1.2;
            letter-spacing: -0.02em;
        }

        .header-subtitle {
            font-size: 14px;
            color: #64748b;
            margin: 4px 0 0 0;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn-outline-compare {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 40px;
            border: 1.5px solid #8b5cf6;
            background: white;
            color: #7c3aed;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn-outline-compare:hover {
            background: #8b5cf6;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
        }

        .btn-outline-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 40px;
            border: 1.5px solid #e2e8f0;
            background: white;
            color: #475569;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn-outline-back:hover {
            border-color: #8b5cf6;
            color: #7c3aed;
            background: #f5f3ff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.1);
        }

        /* ============================================ */
        /* STATS GRID */
        /* ============================================ */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: white;
            padding: 20px 24px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            gap: 16px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            border: 1px solid #f1f5f9;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }

        .stat-icon.total {
            background: #e0f2fe;
            color: #0284c7;
        }

        .stat-icon.kriteria {
            background: #eef2ff;
            color: #4f46e5;
        }

        .stat-icon.sub {
            background: #fce7f3;
            color: #db2777;
        }

        .stat-icon.valid {
            background: #d1fae5;
            color: #059669;
        }

        .stat-info {
            display: flex;
            flex-direction: column;
        }

        .stat-value {
            font-size: 24px;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.2;
        }

        .stat-label {
            font-size: 13px;
            color: #64748b;
            font-weight: 500;
        }

        /* ============================================ */
        /* TABLE CONTAINER */
        /* ============================================ */
        .table-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            border: 1px solid #f1f5f9;
            overflow: hidden;
        }

        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 24px;
            border-bottom: 1px solid #f1f5f9;
            flex-wrap: wrap;
            gap: 12px;
        }

        .table-header-left {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
            color: #0f172a;
            font-size: 15px;
        }

        .table-header-left i {
            color: #8b5cf6;
            font-size: 18px;
        }

        .table-count {
            background: #f1f5f9;
            padding: 2px 12px;
            border-radius: 40px;
            font-size: 12px;
            font-weight: 500;
            color: #64748b;
        }

        .table-header-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .search-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .search-wrapper i {
            position: absolute;
            left: 12px;
            color: #94a3b8;
            font-size: 14px;
        }

        .search-input {
            padding: 8px 14px 8px 38px;
            border: 1.5px solid #e2e8f0;
            border-radius: 40px;
            font-size: 13px;
            background: #f8fafc;
            color: #0f172a;
            transition: all 0.3s ease;
            width: 220px;
            outline: none;
        }

        .search-input:focus {
            border-color: #8b5cf6;
            background: white;
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
            width: 280px;
        }

        .search-input::placeholder {
            color: #94a3b8;
        }

        /* ============================================ */
        /* PREMIUM TABLE */
        /* ============================================ */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .premium-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 900px;
            font-size: 14px;
        }

        .premium-table thead {
            background: #f8fafc;
        }

        .premium-table thead th {
            padding: 14px 16px;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            border-bottom: 2px solid #e2e8f0;
            text-align: left;
            white-space: nowrap;
            position: sticky;
            top: 0;
            background: #f8fafc;
            z-index: 5;
        }

        .th-content {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .th-content i {
            font-size: 13px;
        }

        .premium-table tbody tr {
            border-bottom: 1px solid #f1f5f9;
            transition: all 0.2s ease;
            animation: fadeSlideIn 0.4s ease forwards;
            opacity: 0;
            transform: translateY(8px);
        }

        .premium-table tbody tr:nth-child(1) {
            animation-delay: 0.05s;
        }

        .premium-table tbody tr:nth-child(2) {
            animation-delay: 0.10s;
        }

        .premium-table tbody tr:nth-child(3) {
            animation-delay: 0.15s;
        }

        .premium-table tbody tr:nth-child(4) {
            animation-delay: 0.20s;
        }

        .premium-table tbody tr:nth-child(5) {
            animation-delay: 0.25s;
        }

        .premium-table tbody tr:nth-child(6) {
            animation-delay: 0.30s;
        }

        .premium-table tbody tr:nth-child(7) {
            animation-delay: 0.35s;
        }

        .premium-table tbody tr:nth-child(8) {
            animation-delay: 0.40s;
        }

        .premium-table tbody tr:nth-child(9) {
            animation-delay: 0.45s;
        }

        .premium-table tbody tr:nth-child(10) {
            animation-delay: 0.50s;
        }

        @keyframes fadeSlideIn {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .premium-table tbody tr:hover {
            background: #fafcff;
        }

        .premium-table tbody tr[data-bobot="100"] {
            border-left: 3px solid #10b981;
        }

        .premium-table tbody tr[data-bobot!="100"] {
            border-left: 3px solid #ef4444;
        }

        .premium-table tbody td {
            padding: 14px 16px;
            vertical-align: middle;
        }

        /* ============================================ */
        /* PERIOD INFO */
        /* ============================================ */
        .period-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .period-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #64748b;
            font-size: 16px;
            flex-shrink: 0;
        }

        .period-detail {
            display: flex;
            flex-direction: column;
        }

        .period-name {
            font-weight: 600;
            color: #0f172a;
            font-size: 14px;
            line-height: 1.3;
        }

        .period-meta {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: #94a3b8;
            margin-top: 2px;
        }

        .meta-divider {
            color: #cbd5e1;
        }

        .meta-pendaftar {
            display: inline-flex;
            align-items: center;
            gap: 3px;
        }

        .meta-pendaftar i {
            font-size: 11px;
        }

        /* ============================================ */
        /* YEAR BADGE */
        /* ============================================ */
        .year-badge {
            display: inline-block;
            padding: 4px 14px;
            background: #f1f5f9;
            border-radius: 40px;
            font-size: 13px;
            font-weight: 600;
            color: #475569;
            font-family: 'Inter', monospace;
            letter-spacing: 0.3px;
        }

        /* ============================================ */
        /* DATE TEXT */
        /* ============================================ */
        .date-text {
            font-size: 13px;
            color: #334155;
            font-weight: 500;
            white-space: nowrap;
        }

        /* ============================================ */
        /* STATUS BADGE */
        /* ============================================ */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 14px;
            border-radius: 40px;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
        }

        .status-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            display: inline-block;
        }

        .status-active {
            background: #d1fae5;
            color: #065f46;
        }

        .status-active .status-dot {
            background: #059669;
            animation: pulse-dot 2s ease-in-out infinite;
        }

        .status-inactive {
            background: #f1f5f9;
            color: #475569;
        }

        .status-inactive .status-dot {
            background: #94a3b8;
        }

        .status-done {
            background: #e2e8f0;
            color: #1e293b;
        }

        .status-done .status-dot {
            background: #64748b;
        }

        @keyframes pulse-dot {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: 0.5;
                transform: scale(0.8);
            }
        }

        /* ============================================ */
        /* COUNT BADGE */
        /* ============================================ */
        .count-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 12px;
            border-radius: 40px;
            font-size: 13px;
            font-weight: 600;
        }

        .kriteria-badge {
            background: #eef2ff;
            color: #4f46e5;
        }

        .kriteria-badge i {
            font-size: 12px;
        }

        .sub-badge {
            background: #fce7f3;
            color: #db2777;
        }

        .sub-badge i {
            font-size: 12px;
        }

        /* ============================================ */
        /* BOBOT BADGE */
        /* ============================================ */
        .bobot-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 14px;
            border-radius: 40px;
            font-size: 13px;
            font-weight: 600;
        }

        .bobot-valid {
            background: #d1fae5;
            color: #065f46;
        }

        .bobot-valid i {
            color: #059669;
            font-size: 13px;
        }

        .bobot-invalid {
            background: #fee2e2;
            color: #991b1b;
        }

        .bobot-invalid i {
            color: #dc2626;
            font-size: 13px;
        }

        /* ============================================ */
        /* PENDAFTAR COUNT */
        /* ============================================ */
        .pendaftar-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 32px;
            padding: 2px 12px;
            background: #f1f5f9;
            color: #475569;
            border-radius: 40px;
            font-size: 13px;
            font-weight: 600;
        }

        /* ============================================ */
        /* ACTION BUTTON */
        /* ============================================ */
        .action-btn {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
            cursor: pointer;
            transition: all 0.25s ease;
            text-decoration: none;
            font-size: 14px;
            background: transparent;
        }

        .view-btn {
            background: #eef2ff;
            color: #8b5cf6;
        }

        .view-btn:hover {
            background: #8b5cf6;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
        }

        /* ============================================ */
        /* EMPTY STATE */
        /* ============================================ */
        .empty-state {
            text-align: center;
            padding: 60px 24px !important;
        }

        .empty-state-content {
            max-width: 400px;
            margin: 0 auto;
        }

        .empty-icon-wrapper {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 36px;
            color: #94a3b8;
        }

        .empty-state-content h5 {
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 8px;
        }

        .empty-state-content p {
            color: #64748b;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .btn-empty {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 24px;
            border-radius: 40px;
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
            color: white;
            font-weight: 600;
            border: none;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
        }

        .btn-empty:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(139, 92, 246, 0.4);
            color: white;
        }

        /* ============================================ */
        /* INFO ALERT BOTTOM */
        /* ============================================ */
        .info-alert-bottom {
            padding: 16px 24px;
            border-top: 1px solid #f1f5f9;
            background: #fafcff;
        }

        .info-alert {
            display: flex;
            gap: 14px;
            padding: 14px 18px;
            background: #eff6ff;
            border-radius: 12px;
            border: 1px solid #bfdbfe;
        }

        .info-alert-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: #bfdbfe;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #2563eb;
            font-size: 16px;
            flex-shrink: 0;
        }

        .info-alert-content {
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .info-alert-title {
            font-weight: 600;
            color: #1e3a8a;
            font-size: 13px;
            margin-bottom: 2px;
        }

        .info-alert-text {
            font-size: 13px;
            color: #1e40af;
            line-height: 1.6;
        }

        .info-alert-text strong {
            color: #1e3a8a;
        }

        .bobot-legend {
            display: inline-flex;
            align-items: center;
            gap: 16px;
            margin-left: 8px;
        }

        .legend-item {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 12px;
            color: #475569;
        }

        .legend-dot {
            width: 10px;
            height: 10px;
            border-radius: 3px;
            display: inline-block;
        }

        .valid-dot {
            background: #10b981;
        }

        .invalid-dot {
            background: #ef4444;
        }

        /* ============================================ */
        /* RESPONSIVE */
        /* ============================================ */

        /* Tablet */
        @media (max-width: 992px) {
            .header-title {
                font-size: 20px;
            }

            .header-icon {
                width: 48px;
                height: 48px;
                font-size: 20px;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .search-input {
                width: 160px;
            }

            .search-input:focus {
                width: 200px;
            }

            .premium-table {
                min-width: 800px;
            }
        }

        /* Mobile */
        @media (max-width: 768px) {
            .page-header-wrapper {
                margin-bottom: 24px;
            }

            .header-content {
                flex-direction: column;
                align-items: stretch;
            }

            .header-left {
                gap: 12px;
            }

            .header-icon {
                width: 44px;
                height: 44px;
                font-size: 18px;
                border-radius: 12px;
            }

            .header-title {
                font-size: 18px;
            }

            .header-subtitle {
                font-size: 13px;
            }

            .header-actions {
                width: 100%;
                flex-direction: column;
            }

            .header-actions .btn {
                width: 100%;
                justify-content: center;
                padding: 10px 16px;
                font-size: 13px;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
            }

            .stat-card {
                padding: 16px;
                gap: 12px;
            }

            .stat-icon {
                width: 40px;
                height: 40px;
                font-size: 16px;
            }

            .stat-value {
                font-size: 20px;
            }

            .stat-label {
                font-size: 12px;
            }

            .table-header {
                padding: 12px 16px;
                flex-direction: column;
                align-items: stretch;
            }

            .table-header-left {
                font-size: 14px;
            }

            .table-header-right {
                width: 100%;
            }

            .search-wrapper {
                width: 100%;
            }

            .search-input {
                width: 100%;
                padding: 10px 14px 10px 38px;
                font-size: 14px;
            }

            .search-input:focus {
                width: 100%;
            }

            .premium-table {
                min-width: 100%;
                font-size: 13px;
            }

            .premium-table thead {
                display: none;
            }

            .premium-table tbody tr {
                display: block;
                margin: 12px 12px;
                border-radius: 14px;
                border: 1px solid #e2e8f0;
                background: white;
                box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04);
            }

            .premium-table tbody tr[data-bobot="100"] {
                border-left: 3px solid #10b981;
            }

            .premium-table tbody tr[data-bobot!="100"] {
                border-left: 3px solid #ef4444;
            }

            .premium-table tbody td {
                display: flex;
                align-items: flex-start;
                gap: 8px;
                padding: 10px 14px;
                border-bottom: 1px solid #f1f5f9;
                min-height: 44px;
            }

            .premium-table tbody td:last-child {
                border-bottom: none;
            }

            .premium-table tbody td::before {
                content: attr(data-label);
                font-weight: 600;
                font-size: 11px;
                text-transform: uppercase;
                color: #94a3b8;
                min-width: 70px;
                flex-shrink: 0;
                letter-spacing: 0.3px;
            }

            .premium-table tbody td.col-name::before {
                display: none;
            }

            .period-info {
                flex: 1;
            }

            .period-icon {
                width: 32px;
                height: 32px;
                font-size: 14px;
            }

            .period-name {
                font-size: 14px;
            }

            .year-badge {
                font-size: 12px;
                padding: 2px 12px;
            }

            .date-text {
                font-size: 12px;
                white-space: normal;
            }

            .count-badge {
                font-size: 12px;
                padding: 3px 10px;
            }

            .bobot-badge {
                font-size: 12px;
                padding: 3px 10px;
            }

            .pendaftar-count {
                font-size: 12px;
                padding: 2px 10px;
            }

            .status-badge {
                font-size: 11px;
                padding: 4px 12px;
            }

            .action-btn {
                width: 32px;
                height: 32px;
                font-size: 13px;
                border-radius: 8px;
            }

            .empty-state {
                padding: 40px 16px !important;
            }

            .empty-icon-wrapper {
                width: 64px;
                height: 64px;
                font-size: 28px;
            }

            .info-alert-bottom {
                padding: 12px 16px;
            }

            .info-alert {
                flex-direction: column;
                gap: 10px;
                padding: 12px 14px;
            }

            .info-alert-icon {
                width: 28px;
                height: 28px;
                font-size: 14px;
            }

            .info-alert-title {
                font-size: 12px;
            }

            .info-alert-text {
                font-size: 12px;
            }

            .bobot-legend {
                display: flex;
                flex-wrap: wrap;
                gap: 12px;
                margin-left: 0;
                margin-top: 4px;
            }

            .legend-item {
                font-size: 11px;
            }
        }

        /* Small Mobile */
        @media (max-width: 480px) {
            .header-icon {
                width: 40px;
                height: 40px;
                font-size: 16px;
                border-radius: 10px;
            }

            .header-title {
                font-size: 16px;
            }

            .header-subtitle {
                font-size: 12px;
            }

            .stats-grid {
                grid-template-columns: 1fr 1fr;
                gap: 8px;
            }

            .stat-card {
                padding: 12px 14px;
                gap: 10px;
            }

            .stat-icon {
                width: 34px;
                height: 34px;
                font-size: 14px;
            }

            .stat-value {
                font-size: 18px;
            }

            .stat-label {
                font-size: 11px;
            }

            .premium-table tbody td {
                padding: 8px 12px;
                flex-direction: column;
                gap: 4px;
            }

            .premium-table tbody td::before {
                min-width: auto;
                font-size: 10px;
            }

            .premium-table tbody td.col-name::before {
                display: none;
            }

            .period-info {
                flex-wrap: wrap;
            }

            .period-meta {
                font-size: 11px;
                flex-wrap: wrap;
            }

            .action-btn {
                width: 30px;
                height: 30px;
                font-size: 12px;
            }

            .empty-state-content h5 {
                font-size: 16px;
            }

            .empty-state-content p {
                font-size: 13px;
            }

            .bobot-legend {
                flex-direction: column;
                gap: 4px;
            }
        }

        /* Very Small */
        @media (max-width: 360px) {
            .stats-grid {
                grid-template-columns: 1fr 1fr;
                gap: 6px;
            }

            .stat-card {
                padding: 10px 12px;
                gap: 8px;
            }

            .stat-icon {
                width: 30px;
                height: 30px;
                font-size: 12px;
                border-radius: 8px;
            }

            .stat-value {
                font-size: 16px;
            }

            .premium-table tbody tr {
                margin: 8px 8px;
            }

            .premium-table tbody td {
                padding: 6px 10px;
            }

            .period-name {
                font-size: 13px;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Search functionality
            const searchInput = document.getElementById('tableSearch');
            const rows = document.querySelectorAll('.period-row');

            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const query = this.value.toLowerCase().trim();

                    rows.forEach(row => {
                        const name = row.querySelector('.period-name')?.textContent
                        ?.toLowerCase() || '';
                        const year = row.querySelector('.year-badge')?.textContent || '';
                        const status = row.querySelector('.status-badge')?.textContent
                        ?.toLowerCase() || '';

                        const match = name.includes(query) || year.includes(query) || status
                            .includes(query);
                        row.style.display = match ? '' : 'none';
                    });
                });
            }

            // Tooltip functionality
            const actionBtns = document.querySelectorAll('.action-btn');
            let tooltipTimer;
            let tooltipEl;

            actionBtns.forEach(btn => {
                btn.addEventListener('mouseenter', function(e) {
                    const title = this.getAttribute('title');
                    if (title && window.innerWidth > 768) {
                        tooltipTimer = setTimeout(() => {
                            tooltipEl = document.createElement('div');
                            tooltipEl.className = 'custom-tooltip';
                            tooltipEl.textContent = title;
                            tooltipEl.style.cssText = `
                                position: fixed;
                                background: #0f172a;
                                color: white;
                                padding: 4px 12px;
                                border-radius: 8px;
                                font-size: 12px;
                                font-weight: 500;
                                z-index: 1000;
                                pointer-events: none;
                                white-space: nowrap;
                                box-shadow: 0 4px 12px rgba(0,0,0,0.2);
                                transform: translateX(-50%);
                            `;

                            const rect = this.getBoundingClientRect();
                            let top = rect.top - 32;
                            let left = rect.left + (rect.width / 2);

                            if (top < 10) top = rect.bottom + 8;
                            if (left < 20) left = 20;
                            if (left + 60 > window.innerWidth) left = window.innerWidth -
                            60;

                            tooltipEl.style.top = top + 'px';
                            tooltipEl.style.left = left + 'px';
                            document.body.appendChild(tooltipEl);
                        }, 400);
                    }
                });

                btn.addEventListener('mouseleave', function() {
                    clearTimeout(tooltipTimer);
                    if (tooltipEl) {
                        tooltipEl.remove();
                        tooltipEl = null;
                    }
                });
            });
        });
    </script>

    <!-- Custom tooltip style -->
    <style>
        .custom-tooltip {
            position: fixed;
            background: #0f172a;
            color: white;
            padding: 4px 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 500;
            z-index: 1000;
            pointer-events: none;
            white-space: nowrap;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            transform: translateX(-50%);
        }
    </style>
@endsection
