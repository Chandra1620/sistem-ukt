@extends('layouts.app')
@section('title', 'Periode Seleksi')
@section('page-title', 'Kelola Periode Seleksi')

@section('content')
    <div class="page-header-wrapper">
        <div class="header-content">
            <div class="header-left">
                <div class="header-icon">
                    <i class="bi bi-calendar-range"></i>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.periode.riwayat') }}" class="btn btn-outline-history">
                    <i class="bi bi-clock-history"></i>
                    <span>Riwayat</span>
                </a>
                <a href="{{ route('admin.periode.create') }}" class="btn btn-primary btn-create">
                    <i class="bi bi-plus-lg"></i>
                    <span>Tambah Periode</span>
                </a>
            </div>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon active">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <div class="stat-info">
                <span class="stat-value">{{ $periode->where('status', 'aktif')->count() }}</span>
                <span class="stat-label">Periode Aktif</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon total">
                <i class="bi bi-calendar3"></i>
            </div>
            <div class="stat-info">
                <span class="stat-value">{{ $periode->count() }}</span>
                <span class="stat-label">Total Periode</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon pendaftar">
                <i class="bi bi-people"></i>
            </div>
            <div class="stat-info">
                <span class="stat-value">{{ $periode->sum('pendaftaran_count') }}</span>
                <span class="stat-label">Total Pendaftar</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon selesai">
                <i class="bi bi-check2-circle"></i>
            </div>
            <div class="stat-info">
                <span class="stat-value">{{ $periode->where('status', 'selesai')->count() }}</span>
                <span class="stat-label">Selesai</span>
            </div>
        </div>
    </div>

    <div class="table-container">
        <div class="table-header">
            <div class="table-header-left">
                <i class="bi bi-table"></i>
                <span>Daftar Periode Seleksi</span>
                <span class="table-count">{{ $periode->total() }} periode</span>
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
                        <th class="col-count">
                            <div class="th-content">
                                <i class="bi bi-people"></i>
                                <span>Pendaftar</span>
                            </div>
                        </th>
                        <th class="col-status">
                            <div class="th-content">
                                <i class="bi bi-circle-fill"></i>
                                <span>Status</span>
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
                <tbody>
                    @forelse($periode as $p)
                        <tr class="period-row" data-status="{{ $p->status }}">
                            <td class="col-name" data-label="Nama Periode">
                                <div class="period-info">
                                    <div class="period-icon {{ $p->status === 'aktif' ? 'active' : '' }}">
                                        <i class="bi bi-calendar3"></i>
                                    </div>
                                    <div class="period-detail">
                                        <span class="period-name">{{ $p->nama }}</span>
                                        <span class="period-meta">
                                            <span class="meta-year">{{ $p->tahun }}</span>
                                            <span class="meta-divider">•</span>
                                            <span class="meta-pendaftar">
                                                <i class="bi bi-person"></i>
                                                {{ $p->pendaftaran_count }}
                                            </span>
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="col-year" data-label="Tahun">
                                <span class="year-badge">{{ $p->tahun }}</span>
                            </td>
                            <td class="col-start" data-label="Mulai">
                                <span class="date-text">{{ $p->tanggal_mulai->format('d M Y') }}</span>
                            </td>
                            <td class="col-end" data-label="Selesai">
                                <span class="date-text">{{ $p->tanggal_selesai->format('d M Y') }}</span>
                                @if ($p->status === 'aktif' && now()->greaterThan($p->tanggal_selesai))
                                    <span class="expired-badge">Lewat</span>
                                @endif
                            </td>
                            <td class="col-count" data-label="Pendaftar">
                                <span class="pendaftar-count">{{ $p->pendaftaran_count }}</span>
                            </td>
                            <td class="col-status" data-label="Status">
                                @if ($p->status === 'aktif')
                                    <span class="status-badge status-active">
                                        <span class="status-dot"></span>
                                        Aktif
                                    </span>
                                @elseif($p->status === 'nonaktif')
                                    <span class="status-badge status-inactive">
                                        <span class="status-dot"></span>
                                        Nonaktif
                                    </span>
                                @else
                                    <span class="status-badge status-done">
                                        <span class="status-dot"></span>
                                        Selesai
                                    </span>
                                @endif
                            </td>
                            <td class="col-action" data-label="Aksi">
                                <div class="action-group">
                                    <a href="{{ route('admin.periode.show', $p) }}" class="action-btn view-btn"
                                        title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.periode.edit', $p) }}" class="action-btn edit-btn"
                                        title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @if ($p->status !== 'aktif')
                                        <form action="{{ route('admin.periode.destroy', $p) }}" method="POST"
                                            class="d-inline delete-form" onsubmit="return confirmDelete(event)">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="action-btn delete-btn" title="Hapus">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty-state">
                                <div class="empty-state-content">
                                    <div class="empty-icon-wrapper">
                                        <i class="bi bi-calendar-x"></i>
                                    </div>
                                    <h5>Belum Ada Periode Seleksi</h5>
                                    <p>Mulai dengan membuat periode seleksi baru untuk penerimaan mahasiswa</p>
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

        @if ($periode->hasPages())
            <div class="table-footer">
                <div class="footer-info">
                    <span>Menampilkan {{ $periode->firstItem() }} - {{ $periode->lastItem() }} dari
                        {{ $periode->total() }} periode</span>
                </div>
                <div class="footer-pagination">
                    {{ $periode->links() }}
                </div>
            </div>
        @endif
    </div>

    <style>
        /* ============================================ */
        /* GLOBAL RESET & BASE */
        /* ============================================ */
        * {
            box-sizing: border-box;
        }

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
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
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
            flex-wrap: wrap;
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
            border-color: #3b82f6;
            color: #3b82f6;
            background: #f8faff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.1);
        }

        .btn-create {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 24px;
            border-radius: 40px;
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            font-weight: 600;
            font-size: 14px;
            border: none;
            transition: all 0.3s ease;
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .btn-create:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
            color: white;
        }

        .btn-create i {
            font-size: 16px;
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

        .stat-icon.active {
            background: #d1fae5;
            color: #059669;
        }

        .stat-icon.total {
            background: #e0f2fe;
            color: #0284c7;
        }

        .stat-icon.pendaftar {
            background: #eef2ff;
            color: #4f46e5;
        }

        .stat-icon.selesai {
            background: #fef3c7;
            color: #d97706;
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
            color: #3b82f6;
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
            border-color: #3b82f6;
            background: white;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
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
            min-width: 700px;
            font-size: 14px;
        }

        .premium-table thead {
            background: #f8fafc;
        }

        .premium-table thead th {
            padding: 14px 20px;
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

        .premium-table tbody tr[data-status="aktif"] {
            background: linear-gradient(90deg, #f0fdf4 0%, transparent 60%);
            border-left: 3px solid #10b981;
        }

        .premium-table tbody tr[data-status="aktif"]:hover {
            background: linear-gradient(90deg, #e6f7ed 0%, #fafcff 60%);
        }

        .premium-table tbody td {
            padding: 16px 20px;
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
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #64748b;
            font-size: 18px;
            flex-shrink: 0;
            transition: all 0.3s ease;
        }

        .period-icon.active {
            background: #d1fae5;
            color: #059669;
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

        .expired-badge {
            display: inline-block;
            margin-left: 6px;
            padding: 2px 10px;
            background: #fee2e2;
            color: #dc2626;
            border-radius: 40px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
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
            background: #eef2ff;
            color: #4f46e5;
            border-radius: 40px;
            font-size: 13px;
            font-weight: 600;
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
            animation: pulse-dot 2s ease-in-out infinite;
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

        .status-active {
            background: #d1fae5;
            color: #065f46;
        }

        .status-active .status-dot {
            background: #059669;
        }

        .status-inactive {
            background: #f1f5f9;
            color: #475569;
        }

        .status-inactive .status-dot {
            background: #94a3b8;
            animation: none;
        }

        .status-done {
            background: #e2e8f0;
            color: #1e293b;
        }

        .status-done .status-dot {
            background: #64748b;
            animation: none;
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
            color: #3b82f6;
        }

        .view-btn:hover {
            background: #3b82f6;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
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
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            font-weight: 600;
            border: none;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .btn-empty:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
            color: white;
        }

        /* ============================================ */
        /* TABLE FOOTER */
        /* ============================================ */
        .table-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 24px;
            border-top: 1px solid #f1f5f9;
            flex-wrap: wrap;
            gap: 12px;
            background: #fafcff;
        }

        .footer-info {
            font-size: 13px;
            color: #64748b;
        }

        .footer-pagination nav {
            display: flex;
            justify-content: center;
        }

        .footer-pagination .pagination {
            gap: 4px;
            margin: 0;
            flex-wrap: wrap;
        }

        .footer-pagination .page-link {
            border-radius: 40px !important;
            border: none;
            padding: 6px 14px;
            color: #475569;
            font-weight: 500;
            font-size: 13px;
            transition: all 0.2s ease;
            background: transparent;
        }

        .footer-pagination .page-link:hover {
            background: #eef2ff;
            color: #3b82f6;
        }

        .footer-pagination .active .page-link {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
        }

        .footer-pagination .disabled .page-link {
            color: #94a3b8;
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
        }

        /* Mobile */
        @media (max-width: 768px) {
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
                justify-content: stretch;
            }

            .btn-outline-history,
            .btn-create {
                flex: 1;
                justify-content: center;
                padding: 10px 16px;
                font-size: 13px;
            }

            .btn-outline-history span,
            .btn-create span {
                display: inline;
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

            .premium-table tbody tr[data-status="aktif"] {
                border-left: 3px solid #10b981;
                background: #f0fdf4;
            }

            .premium-table tbody tr[data-status="aktif"]:hover {
                background: #f0fdf4;
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
                width: 36px;
                height: 36px;
                font-size: 16px;
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

            .pendaftar-count {
                font-size: 12px;
                padding: 2px 10px;
            }

            .status-badge {
                font-size: 11px;
                padding: 4px 12px;
            }

            .action-group {
                gap: 4px;
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

            .table-footer {
                flex-direction: column;
                text-align: center;
                padding: 12px 16px;
            }

            .footer-info {
                font-size: 12px;
            }

            .footer-pagination .page-link {
                padding: 4px 10px;
                font-size: 12px;
            }
        }

        /* Small Mobile */
        @media (max-width: 480px) {
            .header-icon {
                width: 40px;
                height: 40px;
                font-size: 16px;
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

            .action-group {
                flex-wrap: wrap;
                justify-content: flex-start;
            }

            .action-btn {
                width: 30px;
                height: 30px;
                font-size: 12px;
            }

            .btn-outline-history span,
            .btn-create span {
                display: none;
            }

            .btn-outline-history,
            .btn-create {
                padding: 10px 14px;
            }

            .empty-state-content h5 {
                font-size: 16px;
            }

            .empty-state-content p {
                font-size: 13px;
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
        function confirmDelete(event) {
            event.preventDefault();
            const form = event.target.closest('form');

            swalConfirm(
                '⚠️ Apakah Anda yakin ingin menghapus periode ini?\n\nData pendaftaran yang terkait tidak akan dihapus, tetapi periode tidak akan bisa direstore kembali.'
                ).then(function(ok) {
                if (!ok) return;
                form.submit();
            });
            return false;
        }

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
                        const status = row.getAttribute('data-status') || '';

                        const match = name.includes(query) || year.includes(query) || status
                            .includes(query);
                        row.style.display = match ? '' : 'none';
                    });
                });
            }

            // Tooltip functionality for action buttons
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
