@extends('layouts.app')
@section('title', 'Kelola Jenis Dokumen')
@section('page-title', 'Kelola Jenis Dokumen')

@section('content')
    <div class="container-fluid px-4">
        {{-- Header Section --}}
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4 gap-3">
            <div>
                <h4 class="fw-bold mb-1 text-dark">
                    <i class="bi bi-file-earmark-text me-2 text-primary"></i>Kelola Jenis Dokumen
                </h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Jenis Dokumen</li>
                    </ol>
                </nav>
            </div>
            <div>
                <a href="{{ route('admin.jenis-dokumen.create') }}" class="btn btn-primary rounded-pill shadow-sm px-4">
                    <i class="bi bi-plus-circle me-2"></i> Tambah Jenis Dokumen
                </a>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="stat-card p-3 rounded-4 bg-white shadow-sm h-100 border"
                    style="border-color: #e0e7ff !important;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small text-uppercase fw-semibold">Total Dokumen</span>
                            <h2 class="fw-bold mb-0 mt-1" style="color: #1a1a2e;">{{ $jenis->count() }}</h2>
                        </div>
                        <div class="stat-icon rounded-circle d-flex align-items-center justify-content-center"
                            style="background: #eef2ff; width: 48px; height: 48px;">
                            <i class="bi bi-file-earmark fs-5 text-primary"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <span class="small text-muted">Seluruh jenis dokumen</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="stat-card p-3 rounded-4 bg-white shadow-sm h-100 border"
                    style="border-color: #d1fae5 !important;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small text-uppercase fw-semibold">Dokumen Aktif</span>
                            <h2 class="fw-bold mb-0 mt-1" style="color: #1a1a2e;">
                                {{ $jenis->where('aktif', true)->count() }}</h2>
                        </div>
                        <div class="stat-icon rounded-circle d-flex align-items-center justify-content-center"
                            style="background: #d1fae5; width: 48px; height: 48px;">
                            <i class="bi bi-check-lg fs-5 text-success"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <span class="small text-muted">Sedang aktif digunakan</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="stat-card p-3 rounded-4 bg-white shadow-sm h-100 border"
                    style="border-color: #ffe4e2 !important;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small text-uppercase fw-semibold">Dokumen Wajib</span>
                            <h2 class="fw-bold mb-0 mt-1" style="color: #1a1a2e;">
                                {{ $jenis->where('wajib', true)->count() }}</h2>
                        </div>
                        <div class="stat-icon rounded-circle d-flex align-items-center justify-content-center"
                            style="background: #ffe4e2; width: 48px; height: 48px;">
                            <i class="bi bi-star-fill fs-5 text-danger"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <span class="small text-muted">Harus diunggah mahasiswa</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="stat-card p-3 rounded-4 bg-white shadow-sm h-100 border"
                    style="border-color: #fed7aa !important;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small text-uppercase fw-semibold">Urutan Terakhir</span>
                            <h2 class="fw-bold mb-0 mt-1" style="color: #1a1a2e;">{{ $jenis->max('urutan') ?? 0 }}</h2>
                        </div>
                        <div class="stat-icon rounded-circle d-flex align-items-center justify-content-center"
                            style="background: #fed7aa; width: 48px; height: 48px;">
                            <i class="bi bi-sort-numeric-down fs-5 text-warning"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <span class="small text-muted">Urutan terakhir</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Table Card --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white py-3 border-0">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-table me-2 text-primary"></i> Daftar Jenis Dokumen
                    </h5>
                    <div class="text-muted small">
                        <i class="bi bi-info-circle"></i>
                        Dokumen wajib harus diunggah oleh mahasiswa saat pendaftaran
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr class="text-uppercase small text-muted">
                                <th class="ps-4 py-3 text-center" style="width: 80px;">Urutan</th>
                                <th class="py-3">Nama Dokumen</th>
                                <th class="py-3">Keterangan</th>
                                <th class="py-3 text-center" style="width: 100px;">Wajib</th>
                                <th class="py-3 text-center" style="width: 100px;">Status</th>
                                <th class="pe-4 py-3 text-end" style="width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($jenis->sortBy('urutan') as $j)
                                <tr>
                                    <td class="ps-4 text-center">
                                        <span
                                            class="badge rounded-pill bg-secondary bg-opacity-10 text-secondary px-3 py-2 font-monospace">
                                            <i class="bi bi-sort-numeric-up me-1"></i> {{ $j->urutan }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-primary bg-opacity-10 p-2 me-2">
                                                <i class="bi bi-file-earmark-text text-primary"></i>
                                            </div>
                                            <span class="fw-semibold">{{ $j->nama }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        @if ($j->keterangan)
                                            <span class="text-muted small">
                                                <i class="bi bi-chat-text me-1"></i> {{ Str::limit($j->keterangan, 50) }}
                                            </span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($j->wajib)
                                            <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger px-3 py-2">
                                                <i class="bi bi-star-fill me-1"></i> Wajib
                                            </span>
                                        @else
                                            <span
                                                class="badge rounded-pill bg-secondary bg-opacity-10 text-secondary px-3 py-2">
                                                <i class="bi bi-star me-1"></i> Opsional
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($j->aktif)
                                            <span
                                                class="badge rounded-pill bg-success bg-opacity-10 text-success px-3 py-2">
                                                <i class="bi bi-check-circle-fill me-1"></i> Aktif
                                            </span>
                                        @else
                                            <span
                                                class="badge rounded-pill bg-secondary bg-opacity-10 text-secondary px-3 py-2">
                                                <i class="bi bi-x-circle-fill me-1"></i> Nonaktif
                                            </span>
                                        @endif
                                    </td>
                                    <td class="pe-4 text-end">
                                        <div class="d-flex gap-2 justify-content-end">
                                            <a href="{{ route('admin.jenis-dokumen.edit', $j) }}"
                                                class="btn btn-sm rounded-pill btn-outline-warning px-3"
                                                data-bs-toggle="tooltip" title="Edit Jenis Dokumen">
                                                <i class="bi bi-pencil"></i> Edit
                                            </a>
                                            <form action="{{ route('admin.jenis-dokumen.destroy', $j) }}" method="POST"
                                                class="d-inline"
                                                data-confirm="⚠️ Apakah Anda yakin ingin menghapus jenis dokumen {{ $j->nama }}? Data yang terkait akan terpengaruh."
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                    class="btn btn-sm rounded-pill btn-outline-danger px-3"
                                                    data-bs-toggle="tooltip" title="Hapus Jenis Dokumen">
                                                    <i class="bi bi-trash"></i> Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="empty-state">
                                            <i class="bi bi-inbox fs-1 text-muted"></i>
                                            <h6 class="mt-2 text-muted">Belum ada data jenis dokumen</h6>
                                            <p class="small text-muted">Silakan tambah jenis dokumen pertama Anda</p>
                                            <a href="{{ route('admin.jenis-dokumen.create') }}"
                                                class="btn btn-primary btn-sm rounded-pill mt-2">
                                                <i class="bi bi-plus-circle"></i> Tambah Sekarang
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white border-0 py-3 text-muted small">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <i class="bi bi-database"></i>
                        Total {{ $jenis->count() }} jenis dokumen,
                        {{ $jenis->where('aktif', true)->count() }} aktif,
                        {{ $jenis->where('wajib', true)->count() }} wajib
                    </div>
                    <div>
                        <i class="bi bi-sort-numeric-down me-1"></i>
                        Urutan:
                        @foreach ($jenis->sortBy('urutan')->take(5) as $idx => $j)
                            <span class="badge bg-light text-dark me-1">{{ $j->urutan }}.{{ $j->nama }}</span>
                        @endforeach
                        @if ($jenis->count() > 5)
                            <span class="badge bg-secondary">+{{ $jenis->count() - 5 }} lainnya</span>
                        @endif
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

        /* Custom Styles untuk bagian lain (tetap seperti semula) */
        .bg-gradient-primary {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .bg-gradient-success {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }

        .bg-gradient-danger {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        }

        .bg-gradient-info {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
        }

        .table tbody tr {
            transition: background-color 0.2s ease;
        }

        .table tbody tr:hover {
            background-color: #f8f9ff;
        }

        .btn {
            transition: all 0.2s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .empty-state {
            padding: 2rem;
        }

        /* Badge styles */
        .badge {
            font-weight: 500;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .btn-sm {
                padding: 0.25rem 0.75rem;
                font-size: 0.75rem;
            }

            .badge {
                font-size: 0.7rem;
            }

            .table td,
            .table th {
                padding: 0.75rem 0.5rem;
            }

            .stat-card {
                padding: 0.75rem !important;
            }

            .stat-icon {
                width: 38px !important;
                height: 38px !important;
            }

            .stat-icon i {
                font-size: 1rem;
            }

            .stat-card h2 {
                font-size: 1.25rem;
            }
        }

        @media (max-width: 576px) {
            .stat-card .text-muted.small {
                font-size: 0.6rem;
            }
        }

        /* Animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .table tbody tr {
            animation: fadeInUp 0.3s ease-out;
        }
    </style>

    <script>
        // Initialize tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
@endsection
