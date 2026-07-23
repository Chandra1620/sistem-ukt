@extends('layouts.app')
@section('title', 'Bandingkan Periode')
@section('page-title', 'Bandingkan Kriteria Antar Periode')

@section('content')
    <div class="page-header-wrapper">
        <div class="header-content">
            <div class="header-left">
                <div class="header-icon">
                    <i class="bi bi-arrow-left-right"></i>
                </div>
                <div class="header-text">
                    <h1 class="header-title">Bandingkan Periode</h1>
                    <p class="header-subtitle">Pilih dua periode untuk melihat perbedaan konfigurasi kriteria SAW</p>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.periode.riwayat') }}" class="btn btn-outline-back">
                    <i class="bi bi-arrow-left"></i>
                    <span>Kembali ke Riwayat</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Form pilih periode --}}
    <div class="filter-card">
        <div class="filter-card-body">
            <form method="GET" action="{{ route('admin.periode.bandingkan') }}" id="compareForm">
                <div class="filter-grid">
                    <div class="filter-group">
                        <label class="filter-label">
                            <i class="bi bi-calendar3 text-primary"></i>
                            Periode A
                        </label>
                        <div class="select-wrapper">
                            <i class="bi bi-chevron-down select-icon"></i>
                            <select name="periode_a" class="filter-select" id="periodeA">
                                <option value="">-- Pilih Periode A --</option>
                                @foreach ($semua as $p)
                                    <option value="{{ $p->id }}"
                                        {{ request('periode_a') == $p->id ? 'selected' : '' }}>
                                        {{ $p->nama }} ({{ $p->tahun }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="filter-divider">
                        <i class="bi bi-arrow-left-right"></i>
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">
                            <i class="bi bi-calendar3 text-warning"></i>
                            Periode B
                        </label>
                        <div class="select-wrapper">
                            <i class="bi bi-chevron-down select-icon"></i>
                            <select name="periode_b" class="filter-select" id="periodeB">
                                <option value="">-- Pilih Periode B --</option>
                                @foreach ($semua as $p)
                                    <option value="{{ $p->id }}"
                                        {{ request('periode_b') == $p->id ? 'selected' : '' }}>
                                        {{ $p->nama }} ({{ $p->tahun }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="filter-action">
                        <button type="submit" class="btn btn-compare" id="compareBtn">
                            <i class="bi bi-arrow-left-right"></i>
                            <span>Bandingkan</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if ($periodeA && $periodeB)
        {{-- Header info kedua periode --}}
        <div class="period-compare-grid">
            <div class="period-card period-card-a">
                <div class="period-card-header">
                    <div class="period-card-icon">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                    <div class="period-card-info">
                        <span class="period-card-label">Periode A</span>
                        <h4 class="period-card-title">{{ $periodeA->nama }}</h4>
                    </div>
                    <span class="period-card-badge badge-primary">Kriteria {{ $periodeA->kriteria->count() }}</span>
                </div>
                <div class="period-card-body">
                    <div class="period-detail-item">
                        <i class="bi bi-calendar-year"></i>
                        <span>{{ $periodeA->tahun }}</span>
                    </div>
                    <div class="period-detail-item">
                        <i class="bi bi-calendar-range"></i>
                        <span>{{ $periodeA->tanggal_mulai->format('d M Y') }} –
                            {{ $periodeA->tanggal_selesai->format('d M Y') }}</span>
                    </div>
                    <div class="period-detail-item">
                        <i class="bi bi-list-check"></i>
                        <span>{{ $periodeA->kriteria->count() }} Kriteria</span>
                    </div>
                    <div class="period-detail-item">
                        <i class="bi bi-percent"></i>
                        <span>Bobot: <strong>{{ number_format($periodeA->kriteria->sum('bobot'), 0) }}%</strong></span>
                    </div>
                </div>
                <div class="period-card-footer">
                    <span class="status-badge status-{{ $periodeA->status }}">
                        <span class="status-dot"></span>
                        {{ ucfirst($periodeA->status) }}
                    </span>
                </div>
            </div>

            <div class="period-compare-vs">
                <div class="vs-circle">
                    <span>VS</span>
                </div>
                <span class="vs-label">Perbandingan</span>
            </div>

            <div class="period-card period-card-b">
                <div class="period-card-header">
                    <div class="period-card-icon">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                    <div class="period-card-info">
                        <span class="period-card-label">Periode B</span>
                        <h4 class="period-card-title">{{ $periodeB->nama }}</h4>
                    </div>
                    <span class="period-card-badge badge-warning">Kriteria {{ $periodeB->kriteria->count() }}</span>
                </div>
                <div class="period-card-body">
                    <div class="period-detail-item">
                        <i class="bi bi-calendar-year"></i>
                        <span>{{ $periodeB->tahun }}</span>
                    </div>
                    <div class="period-detail-item">
                        <i class="bi bi-calendar-range"></i>
                        <span>{{ $periodeB->tanggal_mulai->format('d M Y') }} –
                            {{ $periodeB->tanggal_selesai->format('d M Y') }}</span>
                    </div>
                    <div class="period-detail-item">
                        <i class="bi bi-list-check"></i>
                        <span>{{ $periodeB->kriteria->count() }} Kriteria</span>
                    </div>
                    <div class="period-detail-item">
                        <i class="bi bi-percent"></i>
                        <span>Bobot: <strong>{{ number_format($periodeB->kriteria->sum('bobot'), 0) }}%</strong></span>
                    </div>
                </div>
                <div class="period-card-footer">
                    <span class="status-badge status-{{ $periodeB->status }}">
                        <span class="status-dot"></span>
                        {{ ucfirst($periodeB->status) }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Tabel perbandingan --}}
        <div class="table-container">
            <div class="table-header">
                <div class="table-header-left">
                    <i class="bi bi-table"></i>
                    <span>Perbandingan Kriteria</span>
                    <span class="table-count">{{ count($diff) }} kriteria</span>
                </div>
                <div class="table-header-right">
                    <div class="legend-group">
                        <span class="legend-item">
                            <span class="legend-dot changed-dot"></span>
                            Berbeda
                        </span>
                        <span class="legend-item">
                            <span class="legend-dot same-dot"></span>
                            Sama
                        </span>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="premium-table">
                    <thead>
                        <tr>
                            <th class="col-kode">
                                <div class="th-content">
                                    <i class="bi bi-hash"></i>
                                    <span>Kode</span>
                                </div>
                            </th>
                            <th class="col-nama-a">
                                <div class="th-content text-primary">
                                    <i class="bi bi-tag"></i>
                                    <span>Nama (A)</span>
                                </div>
                            </th>
                            <th class="col-bobot-a">
                                <div class="th-content text-primary">
                                    <i class="bi bi-percent"></i>
                                    <span>Bobot (A)</span>
                                </div>
                            </th>
                            <th class="col-tipe-a">
                                <div class="th-content text-primary">
                                    <i class="bi bi-type"></i>
                                    <span>Tipe (A)</span>
                                </div>
                            </th>
                            <th class="col-sub-a">
                                <div class="th-content text-primary">
                                    <i class="bi bi-diagram-3"></i>
                                    <span>Sub (A)</span>
                                </div>
                            </th>
                            <th class="col-vs">
                                <div class="th-content text-center">
                                    <i class="bi bi-arrow-left-right"></i>
                                    <span>VS</span>
                                </div>
                            </th>
                            <th class="col-nama-b">
                                <div class="th-content text-warning">
                                    <i class="bi bi-tag"></i>
                                    <span>Nama (B)</span>
                                </div>
                            </th>
                            <th class="col-bobot-b">
                                <div class="th-content text-warning">
                                    <i class="bi bi-percent"></i>
                                    <span>Bobot (B)</span>
                                </div>
                            </th>
                            <th class="col-tipe-b">
                                <div class="th-content text-warning">
                                    <i class="bi bi-type"></i>
                                    <span>Tipe (B)</span>
                                </div>
                            </th>
                            <th class="col-sub-b">
                                <div class="th-content text-warning">
                                    <i class="bi bi-diagram-3"></i>
                                    <span>Sub (B)</span>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($diff as $kode => $d)
                            <tr class="compare-row {{ $d['berubah'] ? 'row-changed' : 'row-same' }}">
                                <td class="col-kode" data-label="Kode">
                                    <span class="kode-badge">{{ $d['kode'] }}</span>
                                </td>
                                <td class="col-nama-a" data-label="Nama (A)">
                                    <span class="text-primary">{{ $d['a_nama'] }}</span>
                                </td>
                                <td class="col-bobot-a" data-label="Bobot (A)">
                                    <span class="bobot-value {{ $d['a_bobot'] != $d['b_bobot'] ? 'bobot-diff' : '' }}">
                                        {{ $d['a_bobot'] }}%
                                    </span>
                                </td>
                                <td class="col-tipe-a" data-label="Tipe (A)">
                                    <span class="tipe-value {{ $d['a_tipe'] != $d['b_tipe'] ? 'tipe-diff' : '' }}">
                                        {{ $d['a_tipe'] }}
                                    </span>
                                </td>
                                <td class="col-sub-a" data-label="Sub (A)">
                                    <span class="sub-count">{{ $d['a_sub'] }}</span>
                                </td>
                                <td class="col-vs" data-label="VS">
                                    <div class="vs-indicator {{ $d['berubah'] ? 'vs-changed' : 'vs-same' }}">
                                        @if ($d['berubah'])
                                            <i class="bi bi-arrow-left-right"></i>
                                        @else
                                            <i class="bi bi-check-lg"></i>
                                        @endif
                                    </div>
                                </td>
                                <td class="col-nama-b" data-label="Nama (B)">
                                    <span class="text-warning">{{ $d['b_nama'] }}</span>
                                </td>
                                <td class="col-bobot-b" data-label="Bobot (B)">
                                    <span class="bobot-value {{ $d['a_bobot'] != $d['b_bobot'] ? 'bobot-diff' : '' }}">
                                        {{ $d['b_bobot'] }}%
                                    </span>
                                </td>
                                <td class="col-tipe-b" data-label="Tipe (B)">
                                    <span class="tipe-value {{ $d['a_tipe'] != $d['b_tipe'] ? 'tipe-diff' : '' }}">
                                        {{ $d['b_tipe'] }}
                                    </span>
                                </td>
                                <td class="col-sub-b" data-label="Sub (B)">
                                    <span class="sub-count">{{ $d['b_sub'] }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @php
                $jumlahBerubah = collect($diff)->where('berubah', true)->count();
            @endphp
            <div class="table-footer">
                <div class="footer-summary">
                    <div class="summary-icon {{ $jumlahBerubah > 0 ? 'summary-warning' : 'summary-success' }}">
                        @if ($jumlahBerubah > 0)
                            <i class="bi bi-exclamation-triangle-fill"></i>
                        @else
                            <i class="bi bi-check-circle-fill"></i>
                        @endif
                    </div>
                    <div class="summary-text">
                        @if ($jumlahBerubah > 0)
                            Ditemukan <strong>{{ $jumlahBerubah }} kriteria</strong> yang berbeda antara
                            Periode A dan Periode B (nama, bobot, atau tipe berubah).
                        @else
                            Seluruh kriteria identik antara Periode A dan Periode B.
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

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
        /* FILTER CARD */
        /* ============================================ */
        .filter-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            border: 1px solid #f1f5f9;
            overflow: hidden;
            margin-bottom: 32px;
        }

        .filter-card-body {
            padding: 24px 28px;
        }

        .filter-grid {
            display: grid;
            grid-template-columns: 1fr auto 1fr auto;
            gap: 16px;
            align-items: end;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .filter-label {
            font-weight: 600;
            font-size: 13px;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .filter-label i {
            font-size: 14px;
        }

        .select-wrapper {
            position: relative;
        }

        .select-icon {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            pointer-events: none;
            font-size: 12px;
        }

        .filter-select {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            font-size: 14px;
            color: #0f172a;
            background: #fafcff;
            transition: all 0.3s ease;
            outline: none;
            appearance: none;
            cursor: pointer;
            font-family: inherit;
        }

        .filter-select:focus {
            border-color: #8b5cf6;
            background: white;
            box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.08);
        }

        .filter-select:hover {
            border-color: #c4b5fd;
        }

        .filter-divider {
            display: flex;
            align-items: center;
            justify-content: center;
            color: #94a3b8;
            font-size: 20px;
            padding-bottom: 2px;
        }

        .filter-action {
            display: flex;
            align-items: end;
        }

        .btn-compare {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 28px;
            border-radius: 40px;
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
            color: white;
            font-weight: 600;
            font-size: 14px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
            white-space: nowrap;
        }

        .btn-compare:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(139, 92, 246, 0.4);
        }

        .btn-compare:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none !important;
        }

        /* ============================================ */
        /* PERIOD COMPARE GRID */
        /* ============================================ */
        .period-compare-grid {
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            gap: 24px;
            margin-bottom: 32px;
            align-items: stretch;
        }

        .period-card {
            background: white;
            border-radius: 16px;
            border: 1px solid #f1f5f9;
            overflow: hidden;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        .period-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
        }

        .period-card-a {
            border-top: 4px solid #3b82f6;
        }

        .period-card-b {
            border-top: 4px solid #f59e0b;
        }

        .period-card-header {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px 20px;
            background: #fafcff;
            border-bottom: 1px solid #f1f5f9;
        }

        .period-card-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 16px;
            flex-shrink: 0;
        }

        .period-card-a .period-card-icon {
            background: #3b82f6;
        }

        .period-card-b .period-card-icon {
            background: #f59e0b;
        }

        .period-card-info {
            flex: 1;
            min-width: 0;
        }

        .period-card-label {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #94a3b8;
        }

        .period-card-title {
            font-size: 15px;
            font-weight: 600;
            color: #0f172a;
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .period-card-badge {
            padding: 4px 12px;
            border-radius: 40px;
            font-size: 11px;
            font-weight: 600;
            white-space: nowrap;
        }

        .badge-primary {
            background: #eef2ff;
            color: #4f46e5;
        }

        .badge-warning {
            background: #fef3c7;
            color: #d97706;
        }

        .period-card-body {
            padding: 16px 20px;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .period-detail-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: #475569;
        }

        .period-detail-item i {
            color: #94a3b8;
            font-size: 14px;
            width: 18px;
            flex-shrink: 0;
        }

        .period-detail-item strong {
            color: #0f172a;
        }

        .period-card-footer {
            padding: 12px 20px;
            border-top: 1px solid #f1f5f9;
            background: #fafcff;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 40px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            display: inline-block;
        }

        .status-aktif {
            background: #d1fae5;
            color: #065f46;
        }

        .status-aktif .status-dot {
            background: #059669;
            animation: pulse-dot 2s ease-in-out infinite;
        }

        .status-nonaktif {
            background: #f1f5f9;
            color: #475569;
        }

        .status-nonaktif .status-dot {
            background: #94a3b8;
        }

        .status-selesai {
            background: #e2e8f0;
            color: #1e293b;
        }

        .status-selesai .status-dot {
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
        /* VS CIRCLE */
        /* ============================================ */
        .period-compare-vs {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 4px;
        }

        .vs-circle {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 16px;
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
            flex-shrink: 0;
        }

        .vs-label {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #94a3b8;
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

        .legend-group {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 12px;
            color: #64748b;
        }

        .legend-dot {
            width: 12px;
            height: 12px;
            border-radius: 4px;
            display: inline-block;
        }

        .changed-dot {
            background: #fef3c7;
            border: 1px solid #f59e0b;
        }

        .same-dot {
            background: #d1fae5;
            border: 1px solid #10b981;
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
            min-width: 1000px;
            font-size: 14px;
        }

        .premium-table thead {
            background: #f8fafc;
        }

        .premium-table thead th {
            padding: 12px 14px;
            font-weight: 600;
            font-size: 11px;
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
            gap: 4px;
        }

        .th-content i {
            font-size: 12px;
        }

        .th-content.text-primary {
            color: #3b82f6;
        }

        .th-content.text-warning {
            color: #f59e0b;
        }

        .th-content.text-center {
            justify-content: center;
        }

        .premium-table tbody tr {
            border-bottom: 1px solid #f1f5f9;
            transition: all 0.2s ease;
            animation: fadeSlideIn 0.3s ease forwards;
            opacity: 0;
            transform: translateY(6px);
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

        .premium-table tbody tr.row-changed {
            background: #fffbeb;
        }

        .premium-table tbody tr.row-changed:hover {
            background: #fef3c7;
        }

        .premium-table tbody tr.row-same {
            background: #f0fdf4;
        }

        .premium-table tbody tr.row-same:hover {
            background: #d1fae5;
        }

        .premium-table tbody td {
            padding: 12px 14px;
            vertical-align: middle;
        }

        /* ============================================ */
        /* TABLE CELL STYLES */
        /* ============================================ */
        .kode-badge {
            display: inline-block;
            padding: 2px 10px;
            background: #f1f5f9;
            border-radius: 40px;
            font-size: 12px;
            font-weight: 600;
            color: #475569;
            font-family: monospace;
        }

        .bobot-value {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 40px;
            font-size: 13px;
            font-weight: 600;
            background: #f1f5f9;
            color: #475569;
        }

        .bobot-diff {
            background: #fee2e2;
            color: #dc2626;
        }

        .tipe-value {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 40px;
            font-size: 12px;
            font-weight: 500;
            background: #f1f5f9;
            color: #475569;
        }

        .tipe-diff {
            background: #fee2e2;
            color: #dc2626;
        }

        .sub-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 28px;
            padding: 2px 8px;
            background: #f1f5f9;
            border-radius: 40px;
            font-size: 12px;
            font-weight: 600;
            color: #475569;
        }

        .vs-indicator {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            font-size: 14px;
        }

        .vs-same {
            background: #d1fae5;
            color: #059669;
        }

        .vs-changed {
            background: #fef3c7;
            color: #d97706;
        }

        /* ============================================ */
        /* TABLE FOOTER */
        /* ============================================ */
        .table-footer {
            padding: 16px 24px;
            border-top: 1px solid #f1f5f9;
            background: #fafcff;
        }

        .footer-summary {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .summary-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
        }

        .summary-success {
            background: #d1fae5;
            color: #059669;
        }

        .summary-warning {
            background: #fef3c7;
            color: #d97706;
        }

        .summary-text {
            font-size: 13px;
            color: #475569;
            line-height: 1.5;
        }

        .summary-text strong {
            color: #0f172a;
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

            .filter-grid {
                grid-template-columns: 1fr 1fr;
                gap: 12px;
            }

            .filter-divider {
                display: none;
            }

            .filter-action {
                grid-column: 1 / -1;
            }

            .btn-compare {
                width: 100%;
                justify-content: center;
            }

            .period-compare-grid {
                grid-template-columns: 1fr;
                gap: 16px;
            }

            .period-compare-vs {
                flex-direction: row;
                gap: 12px;
                padding: 8px 0;
            }

            .vs-circle {
                width: 40px;
                height: 40px;
                font-size: 12px;
            }

            .premium-table {
                min-width: 900px;
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
            }

            .btn-outline-back {
                width: 100%;
                justify-content: center;
                padding: 10px 16px;
                font-size: 13px;
            }

            .filter-card-body {
                padding: 16px;
            }

            .filter-grid {
                grid-template-columns: 1fr;
                gap: 12px;
            }

            .filter-divider {
                display: none;
            }

            .filter-action {
                grid-column: auto;
            }

            .btn-compare {
                width: 100%;
                justify-content: center;
                padding: 10px 16px;
                font-size: 13px;
            }

            .period-card-header {
                padding: 12px 16px;
            }

            .period-card-title {
                font-size: 14px;
                white-space: normal;
            }

            .period-card-body {
                padding: 12px 16px;
            }

            .period-card-footer {
                padding: 10px 16px;
            }

            .period-compare-vs {
                flex-direction: row;
                gap: 12px;
                padding: 8px 0;
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

            .legend-group {
                justify-content: flex-start;
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
                box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04);
            }

            .premium-table tbody tr.row-changed {
                border-left: 3px solid #f59e0b;
            }

            .premium-table tbody tr.row-same {
                border-left: 3px solid #10b981;
            }

            .premium-table tbody td {
                display: flex;
                align-items: flex-start;
                gap: 8px;
                padding: 10px 14px;
                border-bottom: 1px solid #f1f5f9;
                min-height: 42px;
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
                min-width: 80px;
                flex-shrink: 0;
                letter-spacing: 0.3px;
            }

            .vs-indicator {
                width: 28px;
                height: 28px;
                font-size: 12px;
            }

            .table-footer {
                padding: 12px 16px;
            }

            .footer-summary {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }

            .summary-text {
                font-size: 12px;
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

            .filter-card-body {
                padding: 12px;
            }

            .filter-select {
                font-size: 13px;
                padding: 8px 12px;
            }

            .period-card {
                border-radius: 12px;
            }

            .period-card-header {
                padding: 10px 12px;
                flex-wrap: wrap;
            }

            .period-card-title {
                font-size: 13px;
            }

            .period-card-body {
                padding: 10px 12px;
                gap: 6px;
            }

            .period-detail-item {
                font-size: 12px;
            }

            .period-card-badge {
                font-size: 10px;
                padding: 3px 8px;
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

            .vs-circle {
                width: 36px;
                height: 36px;
                font-size: 11px;
            }

            .vs-label {
                font-size: 10px;
            }

            .btn-compare {
                font-size: 13px;
                padding: 8px 14px;
            }

            .btn-compare span {
                display: inline;
            }

            .summary-text {
                font-size: 11px;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const periodeA = document.getElementById('periodeA');
            const periodeB = document.getElementById('periodeB');
            const compareBtn = document.getElementById('compareBtn');
            const form = document.getElementById('compareForm');

            // Disable compare button if both periods are not selected
            function validateSelection() {
                const aVal = periodeA.value;
                const bVal = periodeB.value;

                if (aVal && bVal && aVal !== bVal) {
                    compareBtn.disabled = false;
                } else {
                    compareBtn.disabled = true;
                }
            }

            // Prevent selecting same period
            periodeA.addEventListener('change', function() {
                const val = this.value;
                if (val && periodeB.value === val) {
                    periodeB.value = '';
                }
                validateSelection();
            });

            periodeB.addEventListener('change', function() {
                const val = this.value;
                if (val && periodeA.value === val) {
                    periodeA.value = '';
                }
                validateSelection();
            });

            // Initial validation
            validateSelection();

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

        /* Override for mobile table */
        @media (max-width: 768px) {

            .premium-table tbody td.col-kode::before,
            .premium-table tbody td.col-vs::before {
                display: none;
            }
        }
    </style>
@endsection
