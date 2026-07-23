@extends('layouts.app')
@section('title', 'Kelola Kriteria SAW')
@section('page-title', 'Kriteria & Bobot SAW')

@section('content')
    <div class="page-header-wrapper">
        <div class="header-content">
            <div class="header-left">
                <div class="header-icon">
                    <i class="bi bi-sliders2"></i>
                </div>
                <div class="header-text">
                    <h1 class="header-title">Kriteria SAW</h1>
                    <p class="header-subtitle">Kelola kriteria dan bobot perhitungan UKT untuk seleksi</p>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.kriteria.riwayat') }}" class="btn btn-outline-history">
                    <i class="bi bi-clock-history"></i>
                    <span>Riwayat</span>
                </a>
                <a href="{{ route('admin.kriteria.create') }}" class="btn btn-primary btn-create">
                    <i class="bi bi-plus-lg"></i>
                    <span>Tambah Kriteria</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Filter Periode --}}
    <div class="filter-card">
        <div class="filter-card-body">
            <form method="GET" action="{{ route('admin.kriteria.index') }}" class="filter-form">
                <div class="filter-group">
                    <label class="filter-label">
                        <i class="bi bi-calendar3"></i>
                        Pilih Periode
                    </label>
                    <div class="select-wrapper">
                        <i class="bi bi-chevron-down select-icon"></i>
                        <select name="periode_id" class="filter-select" onchange="this.form.submit()">
                            <option value="">-- Pilih Periode --</option>
                            @foreach ($semua_periode as $p)
                                <option value="{{ $p->id }}"
                                    {{ $periodeAktif && $periodeAktif->id == $p->id ? 'selected' : '' }}>
                                    {{ $p->nama }} ({{ $p->tahun }})
                                    @if ($p->status === 'aktif')
                                        - Aktif
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="filter-info">
                    @if ($periodeAktif)
                        <span class="filter-badge">
                            <i class="bi bi-info-circle-fill"></i>
                            Menampilkan: <strong>{{ $periodeAktif->nama }}</strong>
                            <span class="badge-status {{ $periodeAktif->status }}">
                                {{ ucfirst($periodeAktif->status) }}
                            </span>
                        </span>
                    @else
                        <span class="filter-badge muted">
                            <i class="bi bi-info-circle-fill"></i>
                            Silakan pilih periode untuk melihat kriteria
                        </span>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Total Bobot Alert Premium --}}
    @if ($periodeAktif)
        @if ($totalBobot != 100)
            <div class="alert-premium alert-warning-premium">
                <div class="alert-icon">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <div class="alert-content">
                    <div class="alert-title">Total Bobot Belum 100%</div>
                    <div class="alert-message">
                        Total bobot saat ini <strong>{{ $totalBobot }}%</strong>.
                        Total bobot harus <strong>100%</strong> agar perhitungan SAW valid.
                        @php $kekurangan = 100 - $totalBobot; @endphp
                        @if ($kekurangan > 0)
                            <span class="alert-suggestion">Kurang <strong>{{ $kekurangan }}%</strong> lagi.</span>
                        @endif
                    </div>
                </div>
                <div class="alert-progress">
                    <div class="progress-circle warning" data-progress="{{ $totalBobot }}">
                        <span>{{ $totalBobot }}%</span>
                    </div>
                </div>
            </div>
        @else
            <div class="alert-premium alert-success-premium">
                <div class="alert-icon">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div class="alert-content">
                    <div class="alert-title">Bobot Sempurna!</div>
                    <div class="alert-message">
                        Total bobot sudah <strong>100%</strong>. Kriteria SAW siap digunakan untuk perhitungan.
                    </div>
                </div>
                <div class="alert-progress">
                    <div class="progress-circle success" data-progress="100">
                        <span><i class="bi bi-check-lg"></i></span>
                    </div>
                </div>
            </div>
        @endif
    @endif

    <div class="table-container">
        <div class="table-header">
            <div class="table-header-left">
                <i class="bi bi-table"></i>
                <span>Daftar Kriteria</span>
                <span class="table-count">{{ $kriteria->count() }} kriteria</span>
            </div>
            <div class="table-header-right">
                <div class="search-wrapper">
                    <i class="bi bi-search"></i>
                    <input type="text" id="searchKriteria" placeholder="Cari kriteria..." class="search-input">
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="premium-table" id="kriteriaTable">
                <thead>
                    <tr>
                        <th class="col-drag">
                            <div class="th-content">
                                <i class="bi bi-grip-vertical"></i>
                                <span>Urut</span>
                            </div>
                        </th>
                        <th class="col-kode">
                            <div class="th-content">
                                <i class="bi bi-hash"></i>
                                <span>Kode</span>
                            </div>
                        </th>
                        <th class="col-nama">
                            <div class="th-content">
                                <i class="bi bi-textarea"></i>
                                <span>Nama Kriteria</span>
                            </div>
                        </th>
                        <th class="col-bobot">
                            <div class="th-content">
                                <i class="bi bi-percent"></i>
                                <span>Bobot</span>
                            </div>
                        </th>
                        <th class="col-tipe">
                            <div class="th-content">
                                <i class="bi bi-arrow-left-right"></i>
                                <span>Tipe</span>
                            </div>
                        </th>
                        <th class="col-sub">
                            <div class="th-content">
                                <i class="bi bi-diagram-3"></i>
                                <span>Sub Kriteria</span>
                            </div>
                        </th>
                        <th class="col-action">
                            <div class="th-content">
                                <i class="bi bi-gear"></i>
                                <span>Aksi</span>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody id="kriteriaBody">
                    @forelse($kriteria as $index => $k)
                        <tr class="kriteria-row" data-id="{{ $k->id }}" data-name="{{ $k->nama }}">
                            <td class="col-drag" data-label="Urut">
                                <div class="drag-handle-wrapper">
                                    <i class="bi bi-grip-vertical drag-handle"></i>
                                    <span class="drag-number">{{ $index + 1 }}</span>
                                </div>
                            </td>
                            <td class="col-kode" data-label="Kode">
                                <span class="kode-badge">{{ $k->kode }}</span>
                            </td>
                            <td class="col-nama" data-label="Nama Kriteria">
                                <span class="kriteria-name">{{ $k->nama }}</span>
                            </td>
                            <td class="col-bobot" data-label="Bobot">
                                <div class="bobot-wrapper">
                                    <div class="progress-modern">
                                        <div class="progress-bar-modern" style="width: {{ $k->bobot }}%"></div>
                                    </div>
                                    <span class="bobot-value">{{ $k->bobot }}%</span>
                                </div>
                            </td>
                            <td class="col-tipe" data-label="Tipe">
                                @if ($k->tipe === 'cost')
                                    <span class="tipe-badge tipe-cost">
                                        <i class="bi bi-arrow-down-short"></i> Cost
                                    </span>
                                @else
                                    <span class="tipe-badge tipe-benefit">
                                        <i class="bi bi-arrow-up-short"></i> Benefit
                                    </span>
                                @endif
                            </td>
                            <td class="col-sub" data-label="Sub Kriteria">
                                <a href="{{ route('admin.sub-kriteria.index', ['kriteria_id' => $k->id]) }}"
                                    class="subkriteria-badge" title="Kelola Sub Kriteria">
                                    <i class="bi bi-diagram-3"></i>
                                    <span class="subkriteria-count">{{ $k->subKriteria->count() }}</span>
                                    <span class="subkriteria-text">sub</span>
                                </a>
                            </td>
                            <td class="col-action" data-label="Aksi">
                                <div class="action-group">
                                    <a href="{{ route('admin.kriteria.show', $k) }}" class="action-btn view-btn"
                                        title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.kriteria.edit', $k) }}" class="action-btn edit-btn"
                                        title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.kriteria.destroy', $k) }}" method="POST"
                                        class="d-inline delete-form" onsubmit="return confirmDelete(event)">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="action-btn delete-btn" title="Hapus">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty-state">
                                <div class="empty-state-content">
                                    <div class="empty-icon-wrapper">
                                        <i class="bi bi-sliders2"></i>
                                    </div>
                                    <h5>Belum Ada Kriteria</h5>
                                    <p>Mulai dengan menambahkan kriteria SAW pertama untuk periode ini</p>
                                    <a href="{{ route('admin.kriteria.create') }}" class="btn btn-primary btn-empty">
                                        <i class="bi bi-plus-lg"></i>
                                        Tambah Kriteria
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if ($kriteria->count() > 0 && $periodeAktif)
                    <tfoot>
                        <tr>
                            <td colspan="3" class="footer-label">
                                <strong>Total Keseluruhan</strong>
                            </td>
                            <td colspan="4">
                                <div class="total-bobot {{ $totalBobot == 100 ? 'valid' : 'invalid' }}">
                                    <div class="total-progress">
                                        <div class="total-progress-bar" style="width: {{ $totalBobot }}%"></div>
                                    </div>
                                    <div class="total-info">
                                        <span class="total-value">{{ $totalBobot }}%</span>
                                        @if ($totalBobot == 100)
                                            <i class="bi bi-check-circle-fill total-icon valid"></i>
                                            <span class="total-message valid">Lengkap ✓</span>
                                        @else
                                            <i class="bi bi-exclamation-triangle-fill total-icon invalid"></i>
                                            <span class="total-message invalid">Butuh {{ 100 - $totalBobot }}% lagi</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>

    <style>
        /* ============================================ */
        /* PAGE HEADER */
        /* ============================================ */
        .page-header-wrapper {
            margin-bottom: 28px;
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

        .btn-outline-history {
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

        .btn-outline-history:hover {
            border-color: #8b5cf6;
            color: #7c3aed;
            background: #f5f3ff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.1);
        }

        .btn-create {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 24px;
            border-radius: 40px;
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
            color: white;
            font-weight: 600;
            font-size: 14px;
            border: none;
            transition: all 0.3s ease;
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
        }

        .btn-create:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(139, 92, 246, 0.4);
            color: white;
        }

        .btn-create i {
            font-size: 16px;
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
            margin-bottom: 24px;
        }

        .filter-card-body {
            padding: 16px 24px;
        }

        .filter-form {
            display: flex;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .filter-group {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .filter-label {
            font-weight: 600;
            font-size: 13px;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
        }

        .filter-label i {
            color: #8b5cf6;
            font-size: 14px;
        }

        .select-wrapper {
            position: relative;
            min-width: 250px;
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
            padding: 9px 14px;
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

        .filter-info {
            flex: 1;
        }

        .filter-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            background: #f1f5f9;
            border-radius: 40px;
            font-size: 13px;
            color: #475569;
        }

        .filter-badge i {
            color: #8b5cf6;
        }

        .filter-badge.muted {
            background: #f8fafc;
            color: #94a3b8;
        }

        .filter-badge.muted i {
            color: #94a3b8;
        }

        .badge-status {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 40px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .badge-status.aktif {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-status.nonaktif {
            background: #f1f5f9;
            color: #475569;
        }

        .badge-status.selesai {
            background: #e2e8f0;
            color: #1e293b;
        }

        /* ============================================ */
        /* ALERT PREMIUM */
        /* ============================================ */
        .alert-premium {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 16px 24px;
            border-radius: 16px;
            margin-bottom: 24px;
            background: white;
            border: 1px solid #f1f5f9;
            transition: all 0.3s ease;
        }

        .alert-warning-premium {
            background: linear-gradient(135deg, #fffbeb, #fef3c7);
            border-left: 4px solid #f59e0b;
        }

        .alert-success-premium {
            background: linear-gradient(135deg, #f0fdf4, #dcfce7);
            border-left: 4px solid #10b981;
        }

        .alert-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }

        .alert-warning-premium .alert-icon {
            background: #fef3c7;
            color: #f59e0b;
        }

        .alert-success-premium .alert-icon {
            background: #d1fae5;
            color: #10b981;
        }

        .alert-content {
            flex: 1;
        }

        .alert-title {
            font-weight: 700;
            font-size: 14px;
            margin-bottom: 2px;
        }

        .alert-warning-premium .alert-title {
            color: #92400e;
        }

        .alert-success-premium .alert-title {
            color: #065f46;
        }

        .alert-message {
            font-size: 13px;
            color: #64748b;
        }

        .alert-message strong {
            color: #0f172a;
        }

        .alert-suggestion {
            display: inline-block;
            margin-left: 6px;
            padding: 2px 10px;
            background: #fef3c7;
            border-radius: 20px;
            color: #d97706;
            font-size: 12px;
        }

        .alert-progress {
            width: 60px;
            flex-shrink: 0;
        }

        .progress-circle {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            margin: 0 auto;
        }

        .progress-circle.warning {
            background: conic-gradient(#f59e0b 0deg 0deg, #e2e8f0 0deg 360deg);
        }

        .progress-circle.warning::before {
            content: '';
            position: absolute;
            width: 40px;
            height: 40px;
            background: white;
            border-radius: 50%;
        }

        .progress-circle.warning span {
            position: relative;
            z-index: 1;
            font-size: 12px;
            font-weight: 700;
            color: #f59e0b;
        }

        .progress-circle.success {
            background: #10b981;
        }

        .progress-circle.success::before {
            content: '';
            position: absolute;
            width: 40px;
            height: 40px;
            background: #10b981;
            border-radius: 50%;
        }

        .progress-circle.success span {
            position: relative;
            z-index: 1;
            color: white;
            font-size: 18px;
        }

        .progress-circle.success span i {
            display: block;
            line-height: 1;
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
            padding: 14px 24px;
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
            min-width: 800px;
            font-size: 14px;
        }

        .premium-table thead {
            background: #f8fafc;
        }

        .premium-table thead th {
            padding: 12px 16px;
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
            gap: 6px;
        }

        .th-content i {
            font-size: 12px;
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

        .premium-table tbody tr.dragging {
            opacity: 0.4;
            background: #f5f3ff !important;
        }

        .premium-table tbody td {
            padding: 12px 16px;
            vertical-align: middle;
        }

        .premium-table tfoot td {
            padding: 12px 16px;
            background: #fafcff;
            border-top: 2px solid #e2e8f0;
        }

        .premium-table tfoot .footer-label {
            font-weight: 600;
            color: #0f172a;
        }

        /* ============================================ */
        /* DRAG HANDLE */
        /* ============================================ */
        .drag-handle-wrapper {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .drag-handle {
            cursor: grab;
            color: #94a3b8;
            font-size: 16px;
            transition: color 0.2s;
        }

        .drag-handle:hover {
            color: #8b5cf6;
        }

        .drag-handle:active {
            cursor: grabbing;
        }

        .drag-number {
            font-size: 12px;
            color: #94a3b8;
            min-width: 20px;
            text-align: center;
        }

        /* ============================================ */
        /* KODE BADGE */
        /* ============================================ */
        .kode-badge {
            display: inline-block;
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
            color: white;
            padding: 4px 12px;
            border-radius: 40px;
            font-size: 12px;
            font-weight: 700;
            font-family: monospace;
            box-shadow: 0 2px 6px rgba(139, 92, 246, 0.3);
            white-space: nowrap;
        }

        /* ============================================ */
        /* KRITERIA NAME */
        /* ============================================ */
        .kriteria-name {
            font-weight: 500;
            color: #0f172a;
            word-break: break-word;
        }

        /* ============================================ */
        /* BOBOT PROGRESS */
        /* ============================================ */
        .bobot-wrapper {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 130px;
        }

        .progress-modern {
            flex: 1;
            height: 6px;
            background: #e2e8f0;
            border-radius: 10px;
            overflow: hidden;
            min-width: 50px;
        }

        .progress-bar-modern {
            height: 100%;
            background: linear-gradient(90deg, #8b5cf6, #a78bfa);
            border-radius: 10px;
            transition: width 0.3s ease;
        }

        .bobot-value {
            font-size: 13px;
            font-weight: 700;
            color: #1e293b;
            min-width: 40px;
        }

        /* ============================================ */
        /* TIPE BADGE */
        /* ============================================ */
        .tipe-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 12px;
            border-radius: 40px;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
        }

        .tipe-cost {
            background: #fee2e2;
            color: #dc2626;
        }

        .tipe-benefit {
            background: #d1fae5;
            color: #059669;
        }

        /* ============================================ */
        /* SUBKRITERIA BADGE */
        /* ============================================ */
        .subkriteria-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #eef2ff;
            color: #4f46e5;
            padding: 4px 12px;
            border-radius: 40px;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
            white-space: nowrap;
        }

        .subkriteria-badge:hover {
            background: #4f46e5;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }

        .subkriteria-count {
            font-weight: 700;
        }

        .subkriteria-text {
            font-weight: 400;
            font-size: 11px;
            color: #6b7280;
        }

        .subkriteria-badge:hover .subkriteria-text {
            color: rgba(255, 255, 255, 0.8);
        }

        /* ============================================ */
        /* ACTION GROUP */
        /* ============================================ */
        .action-group {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .action-btn {
            width: 32px;
            height: 32px;
            border-radius: 8px;
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
            color: #4f46e5;
        }

        .view-btn:hover {
            background: #4f46e5;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }

        .edit-btn {
            background: #fef3c7;
            color: #f59e0b;
        }

        .edit-btn:hover {
            background: #f59e0b;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
        }

        .delete-btn {
            background: #fee2e2;
            color: #ef4444;
        }

        .delete-btn:hover {
            background: #ef4444;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }

        .delete-form {
            display: inline-block;
        }

        /* ============================================ */
        /* TOTAL BOBOT FOOTER */
        /* ============================================ */
        .total-bobot {
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
        }

        .total-progress {
            flex: 1;
            height: 8px;
            background: #e2e8f0;
            border-radius: 10px;
            overflow: hidden;
            max-width: 200px;
        }

        .total-progress-bar {
            height: 100%;
            border-radius: 10px;
            transition: width 0.3s ease;
        }

        .total-bobot.valid .total-progress-bar {
            background: linear-gradient(90deg, #10b981, #34d399);
        }

        .total-bobot.invalid .total-progress-bar {
            background: linear-gradient(90deg, #ef4444, #f87171);
        }

        .total-info {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .total-value {
            font-size: 16px;
            font-weight: 800;
        }

        .total-bobot.valid .total-value {
            color: #10b981;
        }

        .total-bobot.invalid .total-value {
            color: #ef4444;
        }

        .total-icon {
            font-size: 16px;
        }

        .total-icon.valid {
            color: #10b981;
        }

        .total-icon.invalid {
            color: #ef4444;
        }

        .total-message {
            font-size: 12px;
            font-weight: 500;
        }

        .total-message.valid {
            color: #065f46;
        }

        .total-message.invalid {
            color: #991b1b;
        }

        /* ============================================ */
        /* EMPTY STATE */
        /* ============================================ */
        .empty-state {
            text-align: center;
            padding: 48px 24px !important;
        }

        .empty-state-content {
            max-width: 400px;
            margin: 0 auto;
        }

        .empty-icon-wrapper {
            width: 72px;
            height: 72px;
            background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            font-size: 32px;
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
        /* HIGHLIGHT FOR SEARCH */
        /* ============================================ */
        .kriteria-row.highlight {
            background: #fef3c7 !important;
            animation: highlightPulse 0.5s ease;
        }

        @keyframes highlightPulse {

            0%,
            100% {
                background: #fef3c7;
            }

            50% {
                background: #fde68a;
            }
        }

        .kriteria-row.hidden {
            display: none;
        }

        /* ============================================ */
        /* TOAST NOTIFICATION */
        /* ============================================ */
        .toast-notification {
            position: fixed;
            bottom: 24px;
            right: 24px;
            padding: 12px 20px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            z-index: 10000;
            animation: slideIn 0.3s ease;
            font-weight: 500;
        }

        .toast-success {
            background: #10b981;
            color: white;
        }

        .toast-error {
            background: #ef4444;
            color: white;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }

            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }

        /* ============================================ */
        /* REORDER LOADING OVERLAY */
        /* ============================================ */
        .reorder-loading {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(4px);
        }

        .reorder-loading-content {
            background: white;
            padding: 16px 24px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
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

            .filter-form {
                flex-direction: column;
                align-items: stretch;
                gap: 12px;
            }

            .filter-group {
                width: 100%;
            }

            .select-wrapper {
                min-width: auto;
                flex: 1;
            }

            .search-input {
                width: 160px;
            }

            .search-input:focus {
                width: 200px;
            }

            .premium-table {
                min-width: 700px;
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
            }

            .filter-card-body {
                padding: 12px 16px;
            }

            .filter-group {
                flex-direction: column;
                align-items: stretch;
                gap: 4px;
            }

            .filter-info {
                width: 100%;
            }

            .filter-badge {
                width: 100%;
                justify-content: center;
            }

            .alert-premium {
                flex-direction: column;
                text-align: center;
                padding: 16px;
            }

            .alert-icon {
                margin-bottom: 4px;
            }

            .alert-progress {
                margin-top: 8px;
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
                min-width: 80px;
                flex-shrink: 0;
                letter-spacing: 0.3px;
            }

            .premium-table tbody td.col-drag::before {
                display: none;
            }

            .premium-table tfoot td {
                display: block;
                padding: 12px 16px;
            }

            .premium-table tfoot .footer-label {
                display: none;
            }

            .drag-handle-wrapper {
                flex: 1;
            }

            .bobot-wrapper {
                min-width: auto;
                flex: 1;
                gap: 8px;
            }

            .progress-modern {
                min-width: 60px;
            }

            .action-group {
                flex-wrap: wrap;
            }

            .subkriteria-text {
                display: none;
            }

            .total-bobot {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .total-progress {
                max-width: 100%;
                width: 100%;
            }

            .empty-state {
                padding: 32px 16px !important;
            }

            .empty-icon-wrapper {
                width: 60px;
                height: 60px;
                font-size: 24px;
            }

            .empty-state-content h5 {
                font-size: 16px;
            }

            .empty-state-content p {
                font-size: 13px;
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
                padding: 10px 12px;
            }

            .filter-select {
                font-size: 13px;
                padding: 8px 12px;
            }

            .alert-premium {
                padding: 12px 14px;
            }

            .alert-title {
                font-size: 13px;
            }

            .alert-message {
                font-size: 12px;
            }

            .progress-circle {
                width: 44px;
                height: 44px;
            }

            .progress-circle.warning::before,
            .progress-circle.success::before {
                width: 34px;
                height: 34px;
            }

            .progress-circle.warning span {
                font-size: 10px;
            }

            .progress-circle.success span {
                font-size: 14px;
            }

            .premium-table tbody tr {
                margin: 8px 8px;
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

            .premium-table tbody td.col-drag::before {
                display: none;
            }

            .kode-badge {
                font-size: 11px;
                padding: 3px 10px;
            }

            .bobot-value {
                font-size: 12px;
                min-width: 35px;
            }

            .tipe-badge {
                font-size: 11px;
                padding: 3px 10px;
            }

            .subkriteria-badge {
                font-size: 11px;
                padding: 3px 10px;
            }

            .action-btn {
                width: 28px;
                height: 28px;
                font-size: 12px;
                border-radius: 6px;
            }

            .drag-handle {
                font-size: 14px;
            }

            .drag-number {
                font-size: 11px;
            }

            .btn-empty {
                font-size: 13px;
                padding: 8px 20px;
            }

            .toast-notification {
                bottom: 12px;
                right: 12px;
                padding: 10px 16px;
                font-size: 13px;
            }
        }

        /* Very Small */
        @media (max-width: 360px) {
            .premium-table tbody td {
                padding: 6px 10px;
            }

            .kode-badge {
                font-size: 10px;
                padding: 2px 8px;
            }

            .kriteria-name {
                font-size: 13px;
            }

            .bobot-value {
                font-size: 11px;
                min-width: 30px;
            }

            .action-btn {
                width: 24px;
                height: 24px;
                font-size: 10px;
            }

            .drag-handle {
                font-size: 12px;
            }

            .drag-number {
                font-size: 10px;
                min-width: 16px;
            }
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Update progress circle
            const progressCircles = document.querySelectorAll('.progress-circle.warning');
            progressCircles.forEach(circle => {
                const progress = circle.getAttribute('data-progress');
                if (progress) {
                    const angle = (progress / 100) * 360;
                    circle.style.background =
                        `conic-gradient(#f59e0b 0deg ${angle}deg, #e2e8f0 ${angle}deg 360deg)`;
                }
            });

            // Search functionality
            const searchInput = document.getElementById('searchKriteria');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase().trim();
                    const rows = document.querySelectorAll('#kriteriaBody .kriteria-row');
                    let visibleCount = 0;

                    rows.forEach(row => {
                        const name = row.getAttribute('data-name') || '';
                        if (searchTerm === '' || name.toLowerCase().includes(searchTerm)) {
                            row.classList.remove('hidden');
                            visibleCount++;
                            if (searchTerm !== '' && name.toLowerCase().includes(searchTerm)) {
                                row.classList.add('highlight');
                                setTimeout(() => row.classList.remove('highlight'), 1000);
                            }
                        } else {
                            row.classList.add('hidden');
                        }
                    });

                    let emptyMsg = document.querySelector('#kriteriaBody .search-empty-message');
                    if (visibleCount === 0 && searchTerm !== '') {
                        if (!emptyMsg) {
                            emptyMsg = document.createElement('tr');
                            emptyMsg.className = 'search-empty-message';
                            emptyMsg.innerHTML = `
                                <td colspan="7" class="empty-state">
                                    <div class="empty-state-content">
                                        <div class="empty-icon-wrapper">
                                            <i class="bi bi-search"></i>
                                        </div>
                                        <h5>Tidak Ditemukan</h5>
                                        <p>Kriteria "${searchTerm}" tidak ditemukan</p>
                                    </div>
                                </td>
                            `;
                            document.querySelector('#kriteriaBody').appendChild(emptyMsg);
                        }
                    } else if (emptyMsg) {
                        emptyMsg.remove();
                    }
                });
            }

            // ============================================
            // DRAG AND DROP FUNCTIONALITY
            // ============================================
            const tbody = document.getElementById('kriteriaBody');
            const rows = tbody ? tbody.querySelectorAll('.kriteria-row') : [];

            if (tbody && rows.length > 1) {
                new Sortable(tbody, {
                    handle: '.drag-handle',
                    animation: 300,
                    ghostClass: 'dragging',
                    onEnd: function() {
                        saveOrder();
                        updateDragNumbers();
                    }
                });
            }

            function saveOrder() {
                const rows = document.querySelectorAll('#kriteriaBody .kriteria-row:not(.hidden)');
                const order = [];

                rows.forEach(row => {
                    const id = row.getAttribute('data-id');
                    if (id) order.push(id);
                });

                if (order.length === 0) return;

                // Show loading
                const loading = document.createElement('div');
                loading.id = 'reorderLoading';
                loading.className = 'reorder-loading';
                loading.innerHTML = `
                    <div class="reorder-loading-content">
                        <div class="spinner-border text-primary spinner-border-sm" role="status"></div>
                        <span>Menyimpan urutan...</span>
                    </div>
                `;
                document.body.appendChild(loading);

                fetch('{{ route('admin.kriteria.reorder') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            order: order
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showToast('Urutan berhasil disimpan', 'success');
                        } else {
                            showToast(data.message || 'Gagal menyimpan urutan', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast('Terjadi kesalahan pada server', 'error');
                    })
                    .finally(() => {
                        const loadingEl = document.getElementById('reorderLoading');
                        if (loadingEl) loadingEl.remove();
                    });
            }

            function updateDragNumbers() {
                const rows = document.querySelectorAll('#kriteriaBody .kriteria-row:not(.hidden)');
                rows.forEach((row, index) => {
                    const dragNumber = row.querySelector('.drag-number');
                    if (dragNumber) {
                        dragNumber.textContent = index + 1;
                    }
                });
            }

            function showToast(message, type = 'success') {
                const toast = document.createElement('div');
                toast.className = `toast-notification toast-${type}`;
                toast.innerHTML = `
                    <i class="bi ${type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill'}"></i>
                    <span>${message}</span>
                `;
                document.body.appendChild(toast);

                setTimeout(() => {
                    toast.style.animation = 'slideOut 0.3s ease';
                    setTimeout(() => toast.remove(), 300);
                }, 3000);
            }
        });

        function confirmDelete(event) {
            event.preventDefault();
            const form = event.target.closest('form');

            swalConfirm(
                '⚠️ Apakah Anda yakin ingin menghapus kriteria ini?\n\nSub kriteria yang terkait juga akan ikut terhapus.'
                ).then(function(ok) {
                if (!ok) return;
                form.submit();
            });
            return false;
        }

        // Tooltips
        document.querySelectorAll('.action-btn, .subkriteria-badge').forEach(btn => {
            let tooltipTimeout;
            let tooltipElement;

            btn.addEventListener('mouseenter', function(e) {
                const title = this.getAttribute('title');
                if (title && window.innerWidth > 768) {
                    tooltipTimeout = setTimeout(() => {
                        tooltipElement = document.createElement('div');
                        tooltipElement.className = 'custom-tooltip';
                        tooltipElement.textContent = title;

                        const rect = this.getBoundingClientRect();
                        let top = rect.top - 35;
                        let left = rect.left + (rect.width / 2) - 30;

                        if (top < 10) top = rect.bottom + 5;
                        if (left < 10) left = 10;
                        if (left + 60 > window.innerWidth) left = window.innerWidth - 70;

                        tooltipElement.style.top = top + 'px';
                        tooltipElement.style.left = left + 'px';

                        document.body.appendChild(tooltipElement);
                    }, 300);
                }
            });

            btn.addEventListener('mouseleave', function() {
                if (tooltipTimeout) {
                    clearTimeout(tooltipTimeout);
                }
                if (tooltipElement) {
                    tooltipElement.remove();
                    tooltipElement = null;
                }
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
