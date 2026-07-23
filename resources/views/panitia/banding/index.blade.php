@extends('layouts.app')
@section('title', 'Proses Banding')
@section('page-title', 'Proses Banding')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h5 class="fw-bold mb-1">
                <i class="bi bi-chat-square-text-fill text-primary me-2"></i>
                Proses Banding
            </h5>
            <p class="text-muted small mb-0">Kelola pengajuan banding dari mahasiswa</p>
        </div>
        <div class="stats-banding">
            <div class="stat-banding-item">
                <span class="stat-banding-label">Menunggu</span>
                <span class="stat-banding-value warning">{{ $banding->where('status', 'diajukan')->count() }}</span>
            </div>
            <div class="stat-banding-item">
                <span class="stat-banding-label">Diproses</span>
                <span class="stat-banding-value info">{{ $banding->where('status', 'diproses')->count() }}</span>
            </div>
            <div class="stat-banding-item">
                <span class="stat-banding-label">Selesai</span>
                <span
                    class="stat-banding-value success">{{ $banding->whereIn('status', ['diterima', 'ditolak'])->count() }}</span>
            </div>
        </div>
    </div>

    {{-- Filter Premium --}}
    <div class="card filter-card mb-4">
        <div class="card-body py-3">
            <form method="GET" class="row g-2 align-items-end" id="filterForm">
                <div class="col-md-4">
                    <div class="search-input-wrapper">
                        <i class="bi bi-search"></i>
                        <input type="text" name="search" class="search-input-filter"
                            placeholder="Cari nama / no. pendaftaran..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select-modern">
                        <option value="">Semua Status</option>
                        <option value="diajukan" {{ request('status') === 'diajukan' ? 'selected' : '' }}>
                            <i class="bi bi-clock-history"></i> Diajukan
                        </option>
                        <option value="diproses" {{ request('status') === 'diproses' ? 'selected' : '' }}>
                            <i class="bi bi-gear"></i> Diproses
                        </option>
                        <option value="diterima" {{ request('status') === 'diterima' ? 'selected' : '' }}>
                            <i class="bi bi-check-circle"></i> Diterima
                        </option>
                        <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>
                            <i class="bi bi-x-circle"></i> Ditolak
                        </option>
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn-search-filter w-100">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
                <div class="col-md-1">
                    <a href="{{ route('panitia.banding.index') }}" class="btn-reset-filter w-100">
                        <i class="bi bi-arrow-repeat"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card premium-card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table premium-table">
                    <thead>
                        <tr>
                            <th><i class="bi bi-hash"></i> No. Pendaftaran</th>
                            <th><i class="bi bi-person"></i> Nama Mahasiswa</th>
                            <th><i class="bi bi-building"></i> Program Studi</th>
                            <th><i class="bi bi-trophy"></i> Golongan Saat Ini</th>
                            <th><i class="bi bi-info-circle"></i> Status Banding</th>
                            <th><i class="bi bi-calendar3"></i> Tanggal Ajuan</th>
                            <th><i class="bi bi-gear"></i> Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($banding as $b)
                            <tr class="banding-row priority-{{ $b->status }}">
                                <td data-label="No. Pendaftaran">
                                    <span class="no-pendaftaran">{{ $b->pendaftaran->no_pendaftaran }}</span>
                                </td>
                                <td data-label="Nama Mahasiswa">
                                    <div class="user-cell">
                                        <div class="user-avatar-mini"
                                            style="background: linear-gradient(135deg, #3b82f6, #8b5cf6)">
                                            {{ strtoupper(substr($b->pendaftaran->nama_lengkap, 0, 1)) }}
                                        </div>
                                        <div class="user-name">{{ $b->pendaftaran->nama_lengkap }}</div>
                                    </div>
                                </td>
                                <td data-label="Program Studi" class="small">{{ $b->pendaftaran->programStudi->nama }}
                                </td>
                                <td data-label="Golongan Saat Ini">
                                    @if ($b->hasilUkt)
                                        <div class="golongan-info">
                                            <span class="golongan-badge gol-{{ $b->hasilUkt->golongan_final }}">
                                                Gol. {{ $b->hasilUkt->golongan_final }}
                                            </span>
                                            <div class="nominal-small">{{ $b->hasilUkt->nominal_format }}</div>
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td data-label="Status Banding">
                                    <span class="status-badge status-{{ $b->warna_badge }}">
                                        <i class="bi {{ $b->status_icon ?? 'bi-circle' }}"></i>
                                        {{ $b->label_status }}
                                    </span>
                                    @if ($b->status === 'diajukan')
                                        <span class="urgent-badge">
                                            <i class="bi bi-exclamation-circle"></i> Perlu Tindakan
                                        </span>
                                    @endif
                                </td>
                                <td data-label="Tanggal Ajuan">
                                    <div class="date-info">
                                        <i class="bi bi-calendar3"></i>
                                        <span>{{ $b->created_at->translatedFormat('d M Y') }}</span>
                                    </div>
                                    <div class="time-ago">{{ $b->created_at->diffForHumans() }}</div>
                                </td>
                                <td data-label="Aksi">
                                    <a href="{{ route('panitia.banding.show', $b) }}" class="btn-process">
                                        <i class="bi bi-eye"></i> Proses
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="empty-state">
                                    <div class="empty-state-content">
                                        <div class="empty-icon">
                                            <i class="bi bi-chat-square-x"></i>
                                        </div>
                                        <h6 class="mt-3 mb-1">Tidak Ada Pengajuan Banding</h6>
                                        <p class="text-muted small mb-0">Belum ada mahasiswa yang mengajukan banding</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($banding->hasPages())
                <div class="pagination-wrapper">
                    {{ $banding->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>

    <style>
        /* Stats Banding */
        .stats-banding {
            display: flex;
            gap: 12px;
            background: white;
            padding: 8px 16px;
            border-radius: 60px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }

        .stat-banding-item {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .stat-banding-label {
            font-size: 0.7rem;
            color: #64748b;
        }

        .stat-banding-value {
            font-weight: 700;
            font-size: 0.9rem;
        }

        .stat-banding-value.warning {
            color: #f59e0b;
        }

        .stat-banding-value.info {
            color: #06b6d4;
        }

        .stat-banding-value.success {
            color: #10b981;
        }

        /* Filter Card */
        .filter-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
            overflow: hidden;
        }

        .search-input-wrapper {
            position: relative;
        }

        .search-input-wrapper i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 0.9rem;
        }

        .search-input-filter {
            width: 100%;
            padding: 10px 16px 10px 38px;
            border: 1px solid #e2e8f0;
            border-radius: 40px;
            font-size: 0.85rem;
            transition: all 0.2s;
        }

        .search-input-filter:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-select-modern {
            width: 100%;
            padding: 10px 16px;
            border: 1px solid #e2e8f0;
            border-radius: 40px;
            font-size: 0.85rem;
            background: white;
            cursor: pointer;
            transition: all 0.2s;
        }

        .form-select-modern:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .btn-search-filter {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            border: none;
            border-radius: 40px;
            padding: 10px;
            color: white;
            transition: all 0.2s;
        }

        .btn-search-filter:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .btn-reset-filter {
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f1f5f9;
            border: none;
            border-radius: 40px;
            padding: 10px;
            color: #475569;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-reset-filter:hover {
            background: #e2e8f0;
            transform: translateY(-2px);
            color: #1e293b;
        }

        /* Premium Card */
        .premium-card {
            border: none;
            border-radius: 24px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }

        /* Premium Table */
        .premium-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 900px;
        }

        .premium-table thead th {
            background: #f8fafc;
            padding: 16px 20px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            border-bottom: 2px solid #e2e8f0;
        }

        .premium-table thead th i {
            margin-right: 6px;
            font-size: 0.75rem;
        }

        .premium-table tbody tr {
            transition: all 0.2s ease;
            border-bottom: 1px solid #f1f5f9;
        }

        .premium-table tbody tr:hover {
            background: #fafcff;
        }

        .premium-table tbody tr.priority-diajukan {
            background: linear-gradient(90deg, #fffbeb 0%, transparent 100%);
            border-left: 3px solid #f59e0b;
        }

        .premium-table tbody tr.priority-diproses {
            background: linear-gradient(90deg, #eff6ff 0%, transparent 100%);
            border-left: 3px solid #3b82f6;
        }

        .premium-table tbody td {
            padding: 16px 20px;
            vertical-align: middle;
        }

        /* No Pendaftaran */
        .no-pendaftaran {
            font-family: monospace;
            font-weight: 700;
            font-size: 0.8rem;
            background: #f1f5f9;
            padding: 4px 10px;
            border-radius: 40px;
            display: inline-block;
        }

        /* User Cell */
        .user-cell {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar-mini {
            width: 32px;
            height: 32px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 0.8rem;
        }

        .user-name {
            font-weight: 600;
            color: #1e293b;
        }

        /* Golongan Info */
        .golongan-info {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .golongan-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 40px;
            font-size: 0.7rem;
            font-weight: 700;
            width: fit-content;
        }

        .gol-1 {
            background: #fef3c7;
            color: #92400e;
        }

        .gol-2 {
            background: #fed7aa;
            color: #9b3412;
        }

        .gol-3 {
            background: #fee2e2;
            color: #991b1b;
        }

        .gol-4 {
            background: #d1fae5;
            color: #065f46;
        }

        .gol-5 {
            background: #dbeafe;
            color: #1e40af;
        }

        .gol-6 {
            background: #cffafe;
            color: #0e7490;
        }

        .gol-7 {
            background: #e2e8f0;
            color: #475569;
        }

        .gol-8 {
            background: #f1f5f9;
            color: #1e293b;
        }

        .nominal-small {
            font-size: 0.65rem;
            font-weight: 600;
            color: #10b981;
        }

        /* Status Badge */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 12px;
            border-radius: 40px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .status-primary {
            background: #dbeafe;
            color: #1e40af;
        }

        .status-success {
            background: #d1fae5;
            color: #065f46;
        }

        .status-warning {
            background: #fed7aa;
            color: #9b3412;
        }

        .status-info {
            background: #cffafe;
            color: #0e7490;
        }

        .status-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        /* Urgent Badge */
        .urgent-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: #fee2e2;
            color: #dc2626;
            padding: 2px 8px;
            border-radius: 40px;
            font-size: 0.6rem;
            font-weight: 600;
            margin-left: 6px;
            animation: pulse-urgent 1.5s infinite;
        }

        @keyframes pulse-urgent {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.7;
            }
        }

        /* Date Info */
        .date-info {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.75rem;
            color: #1e293b;
        }

        .time-ago {
            font-size: 0.65rem;
            color: #94a3b8;
            margin-top: 2px;
        }

        /* Tombol Proses */
        .btn-process {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            padding: 6px 14px;
            border-radius: 40px;
            font-size: 0.7rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-process:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
            color: white;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 48px 24px !important;
        }

        .empty-state-content {
            text-align: center;
        }

        .empty-icon {
            width: 72px;
            height: 72px;
            background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            font-size: 2rem;
            color: #94a3b8;
        }

        /* Pagination */
        .pagination-wrapper {
            padding: 16px 20px;
            border-top: 1px solid #f0f2f5;
            background: white;
        }

        .pagination-wrapper nav {
            display: flex;
            justify-content: center;
        }

        .pagination-wrapper .pagination {
            gap: 4px;
            flex-wrap: wrap;
            margin: 0;
        }

        .pagination-wrapper .page-link {
            border-radius: 40px !important;
            border: none;
            padding: 6px 12px;
            color: #475569;
            font-weight: 500;
            transition: all 0.2s;
        }

        .pagination-wrapper .page-link:hover {
            background: #eef2ff;
            color: #3b82f6;
        }

        .pagination-wrapper .active .page-link {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
        }

        /* Animations */
        .banding-row {
            animation: fadeInUp 0.3s ease forwards;
            opacity: 0;
            transform: translateY(10px);
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .banding-row:nth-child(1) {
            animation-delay: 0.03s;
        }

        .banding-row:nth-child(2) {
            animation-delay: 0.06s;
        }

        .banding-row:nth-child(3) {
            animation-delay: 0.09s;
        }

        .banding-row:nth-child(4) {
            animation-delay: 0.12s;
        }

        .banding-row:nth-child(5) {
            animation-delay: 0.15s;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .premium-table thead th {
                padding: 12px 16px;
                font-size: 0.65rem;
            }

            .premium-table tbody td {
                padding: 12px 16px;
            }
        }

        @media (max-width: 768px) {
            .d-flex.justify-content-between {
                flex-direction: column;
                align-items: stretch !important;
            }

            .stats-banding {
                justify-content: center;
            }

            .filter-card .row {
                display: flex;
                flex-direction: column;
                gap: 10px;
            }

            .filter-card .col-md-4,
            .filter-card .col-md-3,
            .filter-card .col-md-1 {
                width: 100%;
            }

            .premium-table thead {
                display: none;
            }

            .premium-table tbody tr {
                display: block;
                margin-bottom: 16px;
                border-radius: 16px;
                border: 1px solid #e2e8f0;
                background: white;
            }

            .premium-table tbody td {
                display: flex;
                align-items: flex-start;
                gap: 12px;
                padding: 12px 16px;
                border-bottom: 1px solid #f1f5f9;
            }

            .premium-table tbody td:last-child {
                border-bottom: none;
            }

            .premium-table tbody td::before {
                content: attr(data-label);
                font-weight: 600;
                font-size: 0.7rem;
                text-transform: uppercase;
                color: #64748b;
                min-width: 100px;
                flex-shrink: 0;
            }

            .user-cell {
                flex-direction: column;
                align-items: flex-start;
            }

            .golongan-info {
                align-items: flex-start;
            }

            .urgent-badge {
                margin-left: 0;
                margin-top: 4px;
                display: inline-flex;
            }

            .btn-process {
                width: 100%;
                justify-content: center;
            }
        }

        @media (max-width: 480px) {
            .premium-table tbody td {
                flex-direction: column;
                gap: 8px;
            }

            .premium-table tbody td::before {
                min-width: auto;
            }

            .user-cell {
                flex-direction: row;
                align-items: center;
            }

            .user-name {
                font-size: 0.85rem;
            }

            .stats-banding {
                flex-wrap: wrap;
            }

            .no-pendaftaran {
                font-size: 0.7rem;
                padding: 3px 8px;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto refresh untuk status baru (opsional)
            // setInterval(function() {
            //     location.reload();
            // }, 30000);
        });
    </script>
@endsection
