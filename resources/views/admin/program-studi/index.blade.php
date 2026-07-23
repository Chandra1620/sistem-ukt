@extends('layouts.app')
@section('title', 'Kelola Program Studi')
@section('page-title', 'Kelola Program Studi')

@section('content')
    <div class="container-fluid px-4">
        {{-- Header Section --}}
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4 gap-3">
            <div>
                <h4 class="fw-bold mb-1 text-dark">
                    <i class="bi bi-building me-2 text-primary"></i>Kelola Program Studi
                </h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Program Studi</li>
                    </ol>
                </nav>
            </div>
            <div>
                <a href="{{ route('admin.program-studi.create') }}" class="btn btn-primary rounded-pill shadow-sm px-4">
                    <i class="bi bi-plus-circle me-2"></i> Tambah Program Studi
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
                            <span class="text-muted small text-uppercase fw-semibold">Total Program Studi</span>
                            <h2 class="fw-bold mb-0 mt-1" style="color: #1a1a2e;">{{ $programStudi->count() }}</h2>
                        </div>
                        <div class="stat-icon rounded-circle d-flex align-items-center justify-content-center"
                            style="background: #eef2ff; width: 48px; height: 48px;">
                            <i class="bi bi-mortarboard fs-5 text-primary"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <span class="small text-muted">Seluruh program studi</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="stat-card p-3 rounded-4 bg-white shadow-sm h-100 border"
                    style="border-color: #d1fae5 !important;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small text-uppercase fw-semibold">Program Studi Aktif</span>
                            <h2 class="fw-bold mb-0 mt-1" style="color: #1a1a2e;">
                                {{ $programStudi->where('aktif', true)->count() }}</h2>
                        </div>
                        <div class="stat-icon rounded-circle d-flex align-items-center justify-content-center"
                            style="background: #d1fae5; width: 48px; height: 48px;">
                            <i class="bi bi-check-lg fs-5 text-success"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <span class="small text-muted">Program studi aktif</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="stat-card p-3 rounded-4 bg-white shadow-sm h-100 border"
                    style="border-color: #ffe4e2 !important;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small text-uppercase fw-semibold">Total Pendaftar</span>
                            <h2 class="fw-bold mb-0 mt-1" style="color: #1a1a2e;">
                                {{ number_format($programStudi->sum(fn($p) => $p->pendaftaran()->count())) }}</h2>
                        </div>
                        <div class="stat-icon rounded-circle d-flex align-items-center justify-content-center"
                            style="background: #ffe4e2; width: 48px; height: 48px;">
                            <i class="bi bi-people fs-5 text-danger"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <span class="small text-muted">Total pendaftar terdaftar</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="stat-card p-3 rounded-4 bg-white shadow-sm h-100 border"
                    style="border-color: #fed7aa !important;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small text-uppercase fw-semibold">Jenjang Pendidikan</span>
                            <h2 class="fw-bold mb-0 mt-1" style="color: #1a1a2e;">
                                {{ $programStudi->groupBy('jenjang')->count() }}</h2>
                        </div>
                        <div class="stat-icon rounded-circle d-flex align-items-center justify-content-center"
                            style="background: #fed7aa; width: 48px; height: 48px;">
                            <i class="bi bi-layers fs-5 text-warning"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <span class="small text-muted">Jenjang pendidikan</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Table Card --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white py-3 border-0">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-table me-2 text-primary"></i> Daftar Program Studi
                    </h5>
                    <div class="text-muted small">
                        <i class="bi bi-info-circle"></i> Program Studi yang memiliki pendaftar tidak dapat dihapus
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr class="text-uppercase small text-muted">
                                <th class="ps-4 py-3">Nama Program Studi</th>
                                <th class="py-3">Jenjang</th>
                                <th class="py-3">Akreditasi</th>
                                <th class="py-3">Nominal KIP</th>
                                <th class="py-3">Status</th>
                                <th class="py-3">Pendaftar</th>
                                <th class="pe-4 py-3 text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($programStudi->groupBy('jenjang') as $jenjang => $list)
                                {{-- Group Header --}}
                                <tr class="bg-gradient-soft">
                                    <td colspan="6" class="py-2 px-4">
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-primary bg-opacity-10 p-1 me-2">
                                                <i class="bi bi-mortarboard text-primary small"></i>
                                            </div>
                                            <span class="fw-semibold text-primary">
                                                {{ $jenjang }}
                                            </span>
                                            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill ms-2">
                                                {{ $list->count() }} Program
                                            </span>
                                        </div>
                                    </td>
                                </tr>

                                @foreach ($list as $p)
                                    <tr>
                                        <td class="ps-4 fw-semibold">
                                            <i class="bi bi-book me-2 text-primary"></i>{{ $p->nama }}
                                        </td>
                                        <td>
                                            <span class="badge rounded-pill px-3 py-2"
                                                style="background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);">
                                                {{ $p->jenjang }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($p->akreditasiData)
                                                <span
                                                    class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill">
                                                    {{ $p->akreditasiData->nama }}
                                                </span>
                                            @else
                                                <span class="text-muted small">-</span>
                                            @endif
                                        </td>
                                        <td class="text-success fw-semibold small">
                                            {{ $p->akreditasiData?->nominal_kip_format ?? '-' }}
                                        </td>
                                        <td>
                                            @if ($p->aktif)
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
                                        <td>
                                            <span class="badge rounded-pill bg-info bg-opacity-10 text-info px-3 py-2">
                                                <i class="bi bi-people me-1"></i>
                                                {{ $p->pendaftaran()->count() }} Pendaftar
                                            </span>
                                        </td>
                                        <td class="pe-4 text-end">
                                            <div class="d-flex gap-2 justify-content-end">
                                                <a href="{{ route('admin.program-studi.edit', $p) }}"
                                                    class="btn btn-sm rounded-pill btn-outline-warning px-3">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </a>
                                                @if ($p->pendaftaran()->count() === 0)
                                                    <form action="{{ route('admin.program-studi.destroy', $p) }}"
                                                        method="POST" class="d-inline"
                                                        data-confirm="Hapus Program Studi {{ $p->nama }}?"
                                                        @csrf @method('DELETE')
                                                        <button type="submit"
                                                            class="btn btn-sm rounded-pill btn-outline-danger px-3">
                                                            <i class="bi bi-trash"></i> Hapus
                                                        </button>
                                                    </form>
                                                @else
                                                    <button class="btn btn-sm rounded-pill btn-outline-secondary px-3"
                                                        disabled>
                                                        <i class="bi bi-lock"></i> Terkunci
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="empty-state">
                                            <i class="bi bi-inbox fs-1 text-muted"></i>
                                            <h6 class="mt-2 text-muted">Belum ada data program studi</h6>
                                            <p class="small text-muted">Silakan tambah program studi pertama Anda</p>
                                            <a href="{{ route('admin.program-studi.create') }}"
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
                        Total {{ $programStudi->count() }} program studi,
                        {{ $programStudi->where('aktif', true)->count() }} aktif
                    </div>
                    <div>
                        <i class="bi bi-building me-1"></i>
                        Jenjang:
                        @foreach ($programStudi->groupBy('jenjang') as $jenjang => $list)
                            <span class="badge bg-light text-dark me-1">{{ $jenjang }} ({{ $list->count() }})</span>
                        @endforeach
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

        .bg-gradient-info {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .bg-gradient-warning {
            background: linear-gradient(135deg, #f6d365 0%, #fda085 100%);
        }

        .bg-gradient-soft {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
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

        .table tbody tr:not(.bg-gradient-soft):hover {
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

        /* Badge hover effect */
        .badge {
            transition: all 0.2s ease;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .btn-sm {
                padding: 0.25rem 0.75rem;
                font-size: 0.75rem;
            }

            .badge {
                font-size: 0.75rem;
            }

            .table td,
            .table th {
                padding: 0.75rem 0.5rem;
            }
        }

        /* Animation for new items */
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
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
@endsection
