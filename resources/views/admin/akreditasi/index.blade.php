@extends('layouts.app')
@section('title', 'Kelola Akreditasi')
@section('page-title', 'Kelola Akreditasi')

@section('content')
    <div class="container-fluid px-2 px-sm-3 px-lg-4">
        <!-- Header -->
        <div class="mb-4">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                <div>
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <div class="rounded-3 d-flex align-items-center justify-content-center"
                            style="background: #eef2ff; width: 48px; height: 48px;">
                            <i class="bi bi-award fs-3 text-primary"></i>
                        </div>
                        <div>
                            <h1 class="h4 fw-bold mb-0" style="color: #1a1a2e;">Kelola Akreditasi</h1>
                            <p class="text-muted small mb-0 mt-1">Kelola data akreditasi program studi</p>
                        </div>
                    </div>
                </div>
                <div>
                    <a href="{{ route('admin.akreditasi.create') }}" class="btn btn-primary px-4 py-2 rounded-3">
                        <i class="bi bi-plus-lg me-2"></i>Tambah Akreditasi
                    </a>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="stat-card p-3 rounded-4 bg-white shadow-sm h-100 border"
                    style="border-color: #e0e7ff !important;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small text-uppercase fw-semibold">Total Akreditasi</span>
                            <h2 class="fw-bold mb-0 mt-1" style="color: #1a1a2e;">{{ $akreditasi->count() }}</h2>
                        </div>
                        <div class="stat-icon rounded-circle d-flex align-items-center justify-content-center"
                            style="background: #eef2ff; width: 48px; height: 48px;">
                            <i class="bi bi-award fs-5 text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="stat-card p-3 rounded-4 bg-white shadow-sm h-100 border"
                    style="border-color: #d1fae5 !important;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small text-uppercase fw-semibold">Akreditasi Aktif</span>
                            <h2 class="fw-bold mb-0 mt-1" style="color: #1a1a2e;">
                                {{ $akreditasi->where('aktif', true)->count() }}</h2>
                        </div>
                        <div class="stat-icon rounded-circle d-flex align-items-center justify-content-center"
                            style="background: #d1fae5; width: 48px; height: 48px;">
                            <i class="bi bi-check-circle fs-5 text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="stat-card p-3 rounded-4 bg-white shadow-sm h-100 border"
                    style="border-color: #fed7aa !important;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small text-uppercase fw-semibold">Total Prodi Terhubung</span>
                            <h2 class="fw-bold mb-0 mt-1" style="color: #1a1a2e;">
                                {{ $akreditasi->sum('program_studi_count') }}</h2>
                        </div>
                        <div class="stat-icon rounded-circle d-flex align-items-center justify-content-center"
                            style="background: #fed7aa; width: 48px; height: 48px;">
                            <i class="bi bi-building fs-5 text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Table Card -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white py-3 px-4 border-bottom">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-table text-primary"></i>
                        <span class="fw-semibold">Daftar Akreditasi</span>
                        <span class="badge bg-light text-dark rounded-pill">{{ $akreditasi->count() }} Data</span>
                    </div>
                    <div class="text-muted small">
                        <i class="bi bi-info-circle me-1"></i>
                        Akreditasi yang sudah memiliki program studi tidak dapat dihapus
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-borderless align-middle mb-0">
                        <thead class="bg-light-50">
                            <tr class="small text-muted" style="border-bottom: 1px solid #e9ecef;">
                                <th class="ps-4 py-3 text-center fw-semibold" style="width: 80px;">URUTAN</th>
                                <th class="py-3 fw-semibold">NAMA AKREDITASI</th>
                                <th class="py-3 fw-semibold">KETERANGAN</th>
                                <th class="py-3 text-center fw-semibold" style="width: 100px;">PRODI</th>
                                <th class="py-3 text-center fw-semibold" style="width: 100px;">STATUS</th>
                                <th class="pe-4 py-3 text-end fw-semibold" style="width: 120px;">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($akreditasi as $a)
                                <tr class="data-row" style="border-bottom: 1px solid #f0f2f5;">
                                    <td class="ps-4 py-3 text-center">
                                        <span class="px-2 py-1 rounded-pill small"
                                            style="background: #f1f5f9; color: #475569;">
                                            {{ $a->urutan }}
                                        </span>
                                    </td>
                                    <td class="py-3">
                                        <span class="fw-semibold text-dark">{{ $a->nama }}</span>
                                    </td>
                                    <td class="py-3">
                                        @if ($a->keterangan)
                                            <span class="small text-muted">{{ Str::limit($a->keterangan, 40) }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="py-3 text-center">
                                        <span class="px-2 py-1 rounded-pill small"
                                            style="background: #eef2ff; color: #3b82f6;">
                                            {{ $a->program_studi_count }} Prodi
                                        </span>
                                    </td>
                                    <td class="py-3 text-center">
                                        @if ($a->aktif)
                                            <span class="status-badge-active px-2 py-1 rounded-pill small">
                                                <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i> Aktif
                                            </span>
                                        @else
                                            <span class="status-badge-inactive px-2 py-1 rounded-pill small">
                                                <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i> Nonaktif
                                            </span>
                                        @endif
                                    </td>
                                    <td class="pe-4 py-3 text-end">
                                        <div class="d-flex gap-2 justify-content-end">
                                            <a href="{{ route('admin.akreditasi.edit', $a) }}" class="btn-action edit"
                                                data-bs-toggle="tooltip" title="Edit Akreditasi">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            @if ($a->program_studi_count == 0)
                                                <form action="{{ route('admin.akreditasi.destroy', $a) }}" method="POST"
                                                    class="d-inline"
                                                    data-confirm="Hapus akreditasi {{ $a->nama }}? Tindakan ini tidak dapat dibatalkan."
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn-action delete"
                                                        data-bs-toggle="tooltip" title="Hapus Akreditasi">
                                                        <i class="bi bi-trash3"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <span class="btn-action disabled" data-bs-toggle="tooltip"
                                                    title="Tidak dapat dihapus karena memiliki {{ $a->program_studi_count }} program studi">
                                                    <i class="bi bi-lock"></i>
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="empty-state">
                                            <div class="mb-3">
                                                <i class="bi bi-inbox fs-1 text-muted"></i>
                                            </div>
                                            <h6 class="fw-semibold text-muted mb-1">Belum Ada Data Akreditasi</h6>
                                            <p class="small text-muted mb-3">Silakan tambah akreditasi pertama Anda</p>
                                            <a href="{{ route('admin.akreditasi.create') }}"
                                                class="btn btn-primary btn-sm px-4 rounded-pill">
                                                <i class="bi bi-plus-lg me-2"></i>Tambah Akreditasi
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card-footer bg-white py-3 px-4 border-top">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="small text-muted">
                        <i class="bi bi-database me-1"></i>
                        Total {{ $akreditasi->count() }} akreditasi, {{ $akreditasi->where('aktif', true)->count() }}
                        aktif
                    </div>
                    <div class="small text-muted">
                        <i class="bi bi-building me-1"></i>
                        Total program studi terhubung: <span
                            class="fw-semibold">{{ $akreditasi->sum('program_studi_count') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Custom Styles */
        .bg-light-50 {
            background: #fafbfc;
        }

        .stat-card {
            transition: all 0.25s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08) !important;
        }

        .stat-icon {
            transition: all 0.25s ease;
        }

        .stat-card:hover .stat-icon {
            transform: scale(1.05);
        }

        .btn-primary {
            background: #1e293b;
            border: none;
            transition: all 0.2s ease;
        }

        .btn-primary:hover {
            background: #0f172a;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .btn-action {
            width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            background: transparent;
            border: none;
            transition: all 0.2s ease;
            cursor: pointer;
            font-size: 1rem;
            color: #94a3b8;
        }

        .btn-action.edit:hover {
            background: #eef2ff;
            color: #4f46e5;
            transform: translateY(-2px);
        }

        .btn-action.delete {
            color: #f87171;
        }

        .btn-action.delete:hover {
            background: #fef2f2;
            color: #dc2626;
            transform: translateY(-2px);
        }

        .btn-action.disabled {
            cursor: not-allowed;
            opacity: 0.5;
            color: #94a3b8;
        }

        .status-badge-active {
            background: #d1fae5;
            color: #059669;
            font-weight: 500;
        }

        .status-badge-inactive {
            background: #f1f5f9;
            color: #64748b;
            font-weight: 500;
        }

        .data-row {
            transition: background 0.2s ease;
        }

        .data-row:hover {
            background: #fafbff;
        }

        .empty-state {
            padding: 2rem;
        }

        /* Animations */
        @keyframes fadeSlideUp {
            from {
                opacity: 0;
                transform: translateY(15px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .data-row {
            animation: fadeSlideUp 0.3s ease backwards;
        }

        .data-row:nth-child(1) {
            animation-delay: 0.02s;
        }

        .data-row:nth-child(2) {
            animation-delay: 0.04s;
        }

        .data-row:nth-child(3) {
            animation-delay: 0.06s;
        }

        .data-row:nth-child(4) {
            animation-delay: 0.08s;
        }

        .data-row:nth-child(5) {
            animation-delay: 0.10s;
        }

        .card,
        .stat-card {
            animation: fadeSlideUp 0.3s ease;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .stat-card {
                padding: 0.75rem !important;
            }

            .stat-icon {
                width: 38px;
                height: 38px;
            }

            .stat-icon i {
                font-size: 1rem;
            }

            .stat-card h2 {
                font-size: 1.25rem;
            }

            .btn-action {
                width: 30px;
                height: 30px;
                font-size: 0.85rem;
            }

            .table td,
            .table th {
                padding: 0.75rem 0.5rem;
            }
        }

        @media (max-width: 576px) {

            .status-badge-active,
            .status-badge-inactive {
                font-size: 0.7rem;
                padding: 0.25rem 0.6rem;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.forEach(function(el) {
                new bootstrap.Tooltip(el);
            });
        });
    </script>
@endsection
