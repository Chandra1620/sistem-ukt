@extends('layouts.app')
@section('title', 'Kelola Golongan UKT')
@section('page-title', 'Kelola Golongan UKT')

@section('content')
    <div class="container-fluid px-2 px-sm-3 px-lg-4">
        {{-- Header Section --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-block d-sm-flex justify-content-between align-items-center">
                    <div class="mb-3 mb-sm-0">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <div class="rounded-3 d-flex align-items-center justify-content-center"
                                style="background: #eef2ff; width: 52px; height: 52px;">
                                <i class="bi bi-tag fs-3 text-primary"></i>
                            </div>
                            <div>
                                <h1 class="h3 fw-bold mb-0" style="color: #1a1a2e;">Kelola Golongan UKT</h1>
                                <p class="text-muted small mb-0 mt-1">Kelola data golongan dan nominal UKT</p>
                            </div>
                        </div>
                    </div>
                    <div>
                        <a href="{{ route('admin.golongan-ukt.create') }}"
                            class="btn btn-primary px-4 py-2 rounded-3 shadow-sm-hover"
                            style="border-radius: 12px!important;">
                            <i class="bi bi-plus-lg me-2"></i>Tambah Golongan
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Alert Messages --}}
        {{-- @if (session('success'))
            <div class="alert alert-success border-0 mb-4 p-3 rounded-3"
                style="background: #e8f5e9; border-left: 4px solid #4caf50;">
                <div class="d-flex align-items-center gap-3">
                    <i class="bi bi-check-circle-fill fs-4 text-success"></i>
                    <div class="flex-grow-1 text-dark">{{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger border-0 mb-4 p-3 rounded-3"
                style="background: #ffebee; border-left: 4px solid #f44336;">
                <div class="d-flex align-items-center gap-3">
                    <i class="bi bi-exclamation-triangle-fill fs-4 text-danger"></i>
                    <div class="flex-grow-1 text-dark">{{ session('error') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif --}}

        {{-- Stats Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-6 col-lg-3">
                <div class="stat-card p-3 rounded-4 bg-white shadow-sm h-100 border"
                    style="border-color: #e0e7ff !important;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small text-uppercase fw-semibold">Total Golongan</span>
                            <h2 class="fw-bold mb-0 mt-1" style="color: #1a1a2e;">{{ $golongan->count() }}</h2>
                        </div>
                        <div class="stat-icon rounded-circle d-flex align-items-center justify-content-center"
                            style="background: #eef2ff; width: 48px; height: 48px;">
                            <i class="bi bi-tag fs-5 text-primary"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <span class="small text-muted">Seluruh golongan UKT</span>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="stat-card p-3 rounded-4 bg-white shadow-sm h-100 border"
                    style="border-color: #d1fae5 !important;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small text-uppercase fw-semibold">Total Mahasiswa</span>
                            <h2 class="fw-bold mb-0 mt-1" style="color: #1a1a2e;">
                                {{ number_format($golongan->sum(fn($g) => $g->hasilUkt->count())) }}</h2>
                        </div>
                        <div class="stat-icon rounded-circle d-flex align-items-center justify-content-center"
                            style="background: #d1fae5; width: 48px; height: 48px;">
                            <i class="bi bi-people fs-5 text-success"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <span class="small text-muted">Mahasiswa terdaftar</span>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="stat-card p-3 rounded-4 bg-white shadow-sm h-100 border"
                    style="border-color: #ffe4e2 !important;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small text-uppercase fw-semibold">Nominal Tertinggi</span>
                            <h2 class="fw-bold mb-0 mt-1" style="color: #1a1a2e; font-size: 1.3rem;">Rp
                                {{ number_format($golongan->max('nominal') ?? 0, 0, ',', '.') }}</h2>
                        </div>
                        <div class="stat-icon rounded-circle d-flex align-items-center justify-content-center"
                            style="background: #ffe4e2; width: 48px; height: 48px;">
                            <i class="bi bi-cash-stack fs-5 text-danger"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <span class="small text-muted">Nominal UKT tertinggi</span>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="stat-card p-3 rounded-4 bg-white shadow-sm h-100 border"
                    style="border-color: #fed7aa !important;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small text-uppercase fw-semibold">Range Skor Aktif</span>
                            <h2 class="fw-bold mb-0 mt-1" style="color: #1a1a2e;">
                                {{ $golongan->whereNotNull('skor_min')->count() }}</h2>
                        </div>
                        <div class="stat-icon rounded-circle d-flex align-items-center justify-content-center"
                            style="background: #fed7aa; width: 48px; height: 48px;">
                            <i class="bi bi-graph-up fs-5 text-warning"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <span class="small text-muted">Golongan dengan skor</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Table Card --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white py-3 px-4 border-bottom">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-grid-3x3-gap-fill text-primary"></i>
                        <span class="fw-semibold">Daftar Golongan UKT</span>
                        <span class="badge bg-light text-dark rounded-pill">{{ $golongan->count() }} Golongan</span>
                    </div>
                    <div class="text-muted small">
                        <i class="bi bi-info-circle me-1"></i>
                        Golongan yang memiliki mahasiswa tidak dapat dihapus
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-borderless align-middle mb-0">
                        <thead class="bg-light-50">
                            <tr class="small text-muted" style="border-bottom: 1px solid #e9ecef;">
                                <th class="ps-4 py-3 fw-semibold">GOLONGAN</th>
                                <th class="py-3 fw-semibold">NOMINAL UKT</th>
                                <th class="py-3 fw-semibold">SKOR MIN</th>
                                <th class="py-3 fw-semibold">SKOR MAX</th>
                                <th class="py-3 fw-semibold">MAHASISWA</th>
                                <th class="pe-4 py-3 text-end fw-semibold">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($golongan->sortBy('golongan') as $g)
                                <tr class="data-row" style="border-bottom: 1px solid #f0f2f5;">
                                    <td class="ps-4 py-3">
                                        <span class="px-3 py-1 rounded-pill fw-semibold"
                                            style="background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%); color: #667eea;">
                                            Golongan {{ $g->golongan }}
                                        </span>
                                    </td>
                                    <td class="py-3">
                                        <span class="fw-semibold text-success">
                                            <i class="bi bi-cash me-1"></i> Rp
                                            {{ number_format($g->nominal, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td class="py-3">
                                        @if ($g->skor_min)
                                            <code class="px-2 py-1 rounded"
                                                style="background: #f1f5f9; font-size: 0.75rem;">{{ number_format($g->skor_min, 2) }}</code>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="py-3">
                                        @if ($g->skor_max)
                                            <code class="px-2 py-1 rounded"
                                                style="background: #f1f5f9; font-size: 0.75rem;">{{ number_format($g->skor_max, 2) }}</code>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="py-3">
                                        <span class="px-3 py-1 rounded-pill small bg-light text-dark">
                                            <i class="bi bi-person-check me-1"></i>
                                            {{ number_format($g->hasilUkt->count()) }} Mahasiswa
                                        </span>
                                    </td>
                                    <td class="pe-4 py-3 text-end">
                                        <div class="d-flex gap-2 justify-content-end">
                                            <a href="{{ route('admin.golongan-ukt.edit', $g) }}" class="btn-action edit"
                                                data-bs-toggle="tooltip" title="Edit Golongan">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            @if ($g->hasilUkt->count() === 0)
                                                <form action="{{ route('admin.golongan-ukt.destroy', $g) }}"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirmDelete(event, 'Golongan {{ $g->golongan }}')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn-action delete"
                                                        data-bs-toggle="tooltip" title="Hapus Golongan">
                                                        <i class="bi bi-trash3"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <span class="btn-action disabled" data-bs-toggle="tooltip"
                                                    title="Tidak dapat dihapus karena memiliki {{ $g->hasilUkt->count() }} mahasiswa">
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
                                            <h6 class="fw-semibold text-muted mb-1">Belum Ada Data Golongan UKT</h6>
                                            <p class="small text-muted mb-3">Silakan tambah golongan pertama Anda</p>
                                            <a href="{{ route('admin.golongan-ukt.create') }}"
                                                class="btn btn-primary btn-sm px-4 rounded-pill">
                                                <i class="bi bi-plus-lg me-2"></i>Tambah Golongan
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
                        Total {{ $golongan->count() }} golongan terdaftar
                    </div>
                    <div class="small text-muted">
                        <i class="bi bi-shield-check me-1"></i>
                        Data terenkripsi & aman
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Elegant Stat Cards */
        .stat-card {
            transition: all 0.25s ease;
            cursor: default;
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

        /* Table Styles */
        .bg-light-50 {
            background: #fafbfc;
        }

        .data-row {
            transition: background 0.2s ease;
        }

        .data-row:hover {
            background: #fafbff;
        }

        /* Action Buttons */
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

        /* Button Primary */
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

        /* Code styling */
        code {
            font-family: 'SF Mono', 'Fira Code', monospace;
            font-size: 0.75rem;
            background: #f1f5f9;
            padding: 4px 8px;
            border-radius: 8px;
            color: #475569;
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
            .stat-card .text-muted.small {
                font-size: 0.6rem;
            }

            code {
                font-size: 0.65rem;
            }

            .btn-action {
                width: 28px;
                height: 28px;
                font-size: 0.8rem;
            }
        }
    </style>

    <script>
        function confirmDelete(event, golonganName) {
            event.preventDefault();
            const form = event.target.closest('form');

            swalConfirm(`⚠️ Hapus ${golonganName}?\n\nData golongan akan dihapus secara permanen.`).then(function(ok) {
                if (!ok) return;
                form.submit();
            });
            return false;
        }

        // Initialize tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.forEach(function(el) {
                new bootstrap.Tooltip(el);
            });

            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                document.querySelectorAll('.alert-success, .alert-danger').forEach(function(alert) {
                    alert.style.transition = 'opacity 0.5s ease';
                    alert.style.opacity = '0';
                    setTimeout(function() {
                        alert.remove();
                    }, 500);
                });
            }, 5000);
        });
    </script>
@endsection
