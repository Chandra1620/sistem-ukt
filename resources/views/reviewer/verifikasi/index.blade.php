@extends('layouts.app')
@section('title', 'Verifikasi Dokumen')
@section('page-title', 'Verifikasi Dokumen')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h5 class="fw-bold mb-1">
                <i class="bi bi-shield-check text-primary me-2"></i>
                Verifikasi Dokumen
            </h5>
            <p class="text-muted small mb-0">Periksa dan verifikasi dokumen pendaftar</p>
        </div>
        <div class="d-flex gap-2">
            <div class="search-wrapper">
                <i class="bi bi-search"></i>
                <input type="text" id="searchInput" class="search-input" placeholder="Cari nama / no. pendaftaran...">
            </div>
        </div>
    </div>

    {{-- Filter Premium --}}
    <div class="card filter-card mb-4">
        <div class="card-body py-3">
            <form method="GET" class="row g-2 align-items-end" id="filterForm">
                <div class="col-md-3">
                    <div class="search-input-wrapper">
                        <i class="bi bi-search"></i>
                        <input type="text" name="search" class="search-input-filter"
                            placeholder="Cari nama / no. pendaftaran..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="prodi" class="form-select-modern">
                        <option value="">Semua Program Studi</option>
                        @foreach ($programStudi as $prodi)
                            <option value="{{ $prodi->id }}" {{ request('prodi') == $prodi->id ? 'selected' : '' }}>
                                {{ $prodi->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select-modern">
                        <option value="">Semua Status</option>
                        <option value="menunggu_verifikasi"
                            {{ request('status') === 'menunggu_verifikasi' ? 'selected' : '' }}>
                            Menunggu Verifikasi
                        </option>
                        <option value="diverifikasi" {{ request('status') === 'diverifikasi' ? 'selected' : '' }}>
                            Diverifikasi
                        </option>
                        <option value="ditolak_reviewer" {{ request('status') === 'ditolak_reviewer' ? 'selected' : '' }}>
                            Ditolak
                        </option>
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn-search-filter w-100">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
                <div class="col-md-1">
                    <a href="{{ route('reviewer.verifikasi.index') }}" class="btn-reset-filter w-100">
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
                            <th><i class="bi bi-file-earmark-text"></i> Dokumen</th>
                            <th><i class="bi bi-info-circle"></i> Status</th>
                            <th><i class="bi bi-calendar3"></i> Tanggal Submit</th>
                            <th><i class="bi bi-gear"></i> Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendaftaran as $p)
                            <tr class="verifikasi-row {{ $p->status === 'menunggu_verifikasi' ? 'priority-row' : '' }}">
                                <td data-label="No. Pendaftaran">
                                    <span class="no-pendaftaran">{{ $p->no_pendaftaran }}</span>
                                </td>
                                <td data-label="Nama Mahasiswa">
                                    <div class="user-cell">
                                        <div class="user-avatar-mini"
                                            style="background: linear-gradient(135deg, #3b82f6, #8b5cf6)">
                                            {{ strtoupper(substr($p->nama_lengkap, 0, 1)) }}
                                        </div>
                                        <div class="user-name">{{ $p->nama_lengkap }}</div>
                                    </div>
                                </td>
                                <td data-label="Program Studi" class="small">{{ $p->programStudi->nama }}</td>
                                <td data-label="Dokumen">
                                    <span class="dokumen-badge">
                                        <i class="bi bi-file-earmark-text"></i>
                                        {{ $p->dokumen->count() }} dokumen
                                    </span>
                                </td>
                                <td data-label="Status">
                                    <span class="status-badge status-{{ $p->warna_badge }}">
                                        <i class="bi {{ $p->status_icon ?? 'bi-circle' }}"></i>
                                        {{ $p->label_status }}
                                    </span>
                                </td>
                                <td data-label="Tanggal Submit">
                                    <div class="date-info">
                                        <i class="bi bi-calendar3"></i>
                                        <span>{{ $p->updated_at->format('d M Y') }}</span>
                                    </div>
                                    <div class="time-ago">{{ $p->updated_at->diffForHumans() }}</div>
                                </td>
                                <td data-label="Aksi">
                                    <a href="{{ route('reviewer.verifikasi.show', $p) }}" class="btn-verify">
                                        <i class="bi bi-eye"></i> Periksa
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="empty-state">
                                    <div class="empty-state-content">
                                        <div class="empty-icon">
                                            <i class="bi bi-inbox"></i>
                                        </div>
                                        <h6 class="mt-3 mb-1">Tidak Ada Data</h6>
                                        <p class="text-muted small mb-0">Tidak ada pendaftaran yang perlu diverifikasi</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($pendaftaran->hasPages())
                <div class="pagination-wrapper">
                    {{ $pendaftaran->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>

    <style>
        /* Search Wrapper */
        .search-wrapper {
            position: relative;
            min-width: 250px;
        }

        .search-wrapper i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 0.9rem;
        }

        .search-input {
            padding: 8px 16px 8px 38px;
            border: 1px solid #e2e8f0;
            border-radius: 40px;
            font-size: 0.85rem;
            width: 100%;
            transition: all 0.2s;
        }

        .search-input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
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
            min-width: 800px;
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

        .premium-table tbody tr.priority-row {
            background: linear-gradient(90deg, #fffbeb 0%, transparent 100%);
            border-left: 3px solid #f59e0b;
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

        /* Dokumen Badge */
        .dokumen-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #eef2ff;
            color: #3b82f6;
            padding: 4px 12px;
            border-radius: 40px;
            font-size: 0.7rem;
            font-weight: 600;
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

        .status-secondary {
            background: #f1f5f9;
            color: #475569;
        }

        .status-danger {
            background: #fee2e2;
            color: #991b1b;
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

        /* Tombol Verifikasi */
        .btn-verify {
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

        .btn-verify:hover {
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
        .verifikasi-row {
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

        .verifikasi-row:nth-child(1) {
            animation-delay: 0.03s;
        }

        .verifikasi-row:nth-child(2) {
            animation-delay: 0.06s;
        }

        .verifikasi-row:nth-child(3) {
            animation-delay: 0.09s;
        }

        .verifikasi-row:nth-child(4) {
            animation-delay: 0.12s;
        }

        .verifikasi-row:nth-child(5) {
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

            .search-wrapper {
                width: 100%;
            }

            .filter-card .row {
                display: flex;
                flex-direction: column;
                gap: 10px;
            }

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

            .btn-verify {
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

            .status-badge {
                font-size: 0.65rem;
                padding: 3px 8px;
            }

            .dokumen-badge {
                font-size: 0.65rem;
                padding: 3px 8px;
            }

            .no-pendaftaran {
                font-size: 0.7rem;
                padding: 3px 8px;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Search functionality
            const searchInput = document.getElementById('searchInput');
            const filterForm = document.getElementById('filterForm');

            if (searchInput) {
                let debounceTimer;
                searchInput.addEventListener('input', function() {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(() => {
                        const searchField = filterForm.querySelector('input[name="search"]');
                        if (searchField) {
                            searchField.value = this.value;
                            filterForm.submit();
                        }
                    }, 500);
                });
            }
        });
    </script>
@endsection
