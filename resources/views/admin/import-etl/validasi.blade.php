@extends('layouts.app')
@section('title', 'Hasil Validasi Import')
@section('page-title', 'Hasil Validasi Import ETL')

@section('content')
    <div class="container-fluid px-2 px-sm-3 px-lg-4">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-11">

                <!-- Header -->
                <div class="mb-4">
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                        <div>
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <div class="rounded-3 d-flex align-items-center justify-content-center"
                                    style="background: #eef2ff; width: 48px; height: 48px;">
                                    <i class="bi bi-file-check fs-3 text-primary"></i>
                                </div>
                                <div>
                                    <h1 class="h4 fw-bold mb-0" style="color: #1a1a2e;">Hasil Validasi Data</h1>
                                    <p class="text-muted small mb-0 mt-1">
                                        File: <span class="fw-medium text-dark">{{ basename($path) }}</span> &bull;
                                        Periode: <span class="fw-medium text-dark">{{ $periode->nama }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div>
                            <span
                                class="badge {{ $summary['total_error'] > 0 ? 'bg-danger' : 'bg-success' }} rounded-pill px-3 py-2">
                                {{ $summary['total_error'] > 0 ? 'Ada Kesalahan' : 'Valid' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-6 col-lg-3">
                        <div class="stat-card p-3 rounded-4 bg-white shadow-sm h-100 border"
                            style="border-color: #e0e7ff !important;">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="text-muted small text-uppercase fw-semibold">Total Baris</span>
                                    <h2 class="fw-bold mb-0 mt-1" style="color: #1a1a2e;">
                                        {{ number_format($summary['total_baris']) }}</h2>
                                </div>
                                <div class="stat-icon rounded-circle d-flex align-items-center justify-content-center"
                                    style="background: #eef2ff; width: 44px; height: 44px;">
                                    <i class="bi bi-database fs-5 text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="stat-card p-3 rounded-4 bg-white shadow-sm h-100 border"
                            style="border-color: #d1fae5 !important;">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="text-muted small text-uppercase fw-semibold">Data Valid</span>
                                    <h2 class="fw-bold mb-0 mt-1" style="color: #1a1a2e;">
                                        {{ number_format($summary['total_valid']) }}</h2>
                                </div>
                                <div class="stat-icon rounded-circle d-flex align-items-center justify-content-center"
                                    style="background: #d1fae5; width: 44px; height: 44px;">
                                    <i class="bi bi-check-lg fs-5 text-success"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="stat-card p-3 rounded-4 bg-white shadow-sm h-100 border"
                            style="border-color: #ffe4e2 !important;">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="text-muted small text-uppercase fw-semibold">Bermasalah</span>
                                    <h2 class="fw-bold mb-0 mt-1" style="color: #1a1a2e;">
                                        {{ number_format($summary['total_tidak_valid']) }}</h2>
                                </div>
                                <div class="stat-icon rounded-circle d-flex align-items-center justify-content-center"
                                    style="background: #ffe4e2; width: 44px; height: 44px;">
                                    <i class="bi bi-exclamation-triangle fs-5 text-danger"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="stat-card p-3 rounded-4 bg-white shadow-sm h-100 border"
                            style="border-color: #fed7aa !important;">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="text-muted small text-uppercase fw-semibold">Total Error</span>
                                    <h2 class="fw-bold mb-0 mt-1" style="color: #1a1a2e;">
                                        {{ number_format($summary['total_error']) }}</h2>
                                </div>
                                <div class="stat-icon rounded-circle d-flex align-items-center justify-content-center"
                                    style="background: #fed7aa; width: 44px; height: 44px;">
                                    <i class="bi bi-bug fs-5 text-warning"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($summary['total_error'] > 0)
                    <!-- Ada error -->
                    <div class="alert border-0 mb-4 p-3 rounded-3"
                        style="background: #fee2e2; border-left: 4px solid #dc2626;">
                        <div class="d-flex gap-2">
                            <i class="bi bi-exclamation-triangle-fill text-danger"></i>
                            <div class="small text-dark">
                                <strong class="fw-semibold">Kesalahan Ditemukan!</strong><br>
                                Ditemukan <strong>{{ $summary['total_error'] }} kesalahan</strong> pada
                                <strong>{{ $summary['total_tidak_valid'] }} baris</strong> data.
                                Perbaiki data pada file Excel, lalu upload ulang.
                            </div>
                        </div>
                    </div>

                    <!-- Tabel Error -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                        <div class="card-header bg-white py-3 px-4 border-bottom">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-exclamation-triangle text-danger"></i>
                                    <span class="fw-semibold">Daftar Kesalahan</span>
                                    <span
                                        class="badge bg-danger bg-opacity-10 text-danger rounded-pill">{{ count($errors) }}
                                        Error</span>
                                </div>
                                <form action="{{ route('admin.import-etl.error-report') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="path" value="{{ $path }}">
                                    <input type="hidden" name="periode_id" value="{{ $periode->id }}">
                                    <button type="submit" class="btn btn-sm rounded-pill px-3"
                                        style="background: #f1f5f9; border: none; color: #475569;">
                                        <i class="bi bi-download me-1"></i> Unduh
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive" style="max-height: 480px; overflow-y: auto;">
                                <table class="table table-borderless align-middle mb-0">
                                    <thead class="bg-light-50 sticky-header">
                                        <tr class="small text-muted" style="border-bottom: 1px solid #e9ecef;">
                                            <th class="ps-4 py-3 text-center fw-semibold" style="width: 70px;">BARIS</th>
                                            <th class="py-3 fw-semibold" style="width: 120px;">KOLOM</th>
                                            <th class="py-3 fw-semibold">NILAI</th>
                                            <th class="py-3 fw-semibold">KESALAHAN</th>
                                            <th class="pe-4 py-3 fw-semibold">SARAN</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($errors as $e)
                                            <tr class="data-row" style="border-bottom: 1px solid #f0f2f5;">
                                                <td class="ps-4 py-3 text-center">
                                                    <code class="px-2 py-1 rounded"
                                                        style="background: #fee2e2; color: #dc2626; font-size: 0.7rem;">#{{ $e['baris'] }}</code>
                                                </td>
                                                <td class="py-3 small fw-medium text-dark">{{ $e['kolom'] }}</td>
                                                <td class="py-3">
                                                    <code class="px-2 py-1 rounded"
                                                        style="background: #f1f5f9; font-size: 0.75rem;">{{ $e['nilai'] !== '' ? $e['nilai'] : '(kosong)' }}</code>
                                                </td>
                                                <td class="py-3">
                                                    <span class="small text-danger">{{ $e['kesalahan'] }}</span>
                                                </td>
                                                <td class="pe-4 py-3 small text-muted">{{ $e['saran'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-3">
                        <a href="{{ route('admin.import-etl.index') }}" class="btn btn-light rounded-3 py-2 px-4"
                            style="background: #f1f5f9; border: none;">
                            <i class="bi bi-arrow-left me-2"></i> Upload Ulang
                        </a>
                    </div>
                @else
                    <!-- Tidak ada error -->
                    <div class="alert border-0 mb-4 p-3 rounded-3"
                        style="background: #d1fae5; border-left: 4px solid #10b981;">
                        <div class="d-flex gap-2">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <div class="small text-dark">
                                <strong class="fw-semibold">Data Valid!</strong><br>
                                Seluruh <strong>{{ $summary['total_baris'] }} baris</strong> data valid dan siap untuk
                                diimport.
                            </div>
                        </div>
                    </div>

                    <!-- Preview Data -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                        <div class="card-header bg-white py-3 px-4 border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-eye text-primary"></i>
                                <span class="fw-semibold">Preview Data (5 Baris Pertama)</span>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-borderless align-middle mb-0">
                                    <thead class="bg-light-50">
                                        <tr class="small text-muted" style="border-bottom: 1px solid #e9ecef;">
                                            <th class="ps-4 py-3 text-center fw-semibold" style="width: 60px;">#</th>
                                            <th class="py-3 fw-semibold">NAMA</th>
                                            <th class="py-3 fw-semibold">NIK</th>
                                            <th class="py-3 fw-semibold">PROGRAM STUDI</th>
                                            <th class="pe-4 py-3 text-center fw-semibold" style="width: 100px;">KIP</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($preview as $i => $row)
                                            <tr class="data-row" style="border-bottom: 1px solid #f0f2f5;">
                                                <td class="ps-4 py-3 text-center text-muted small">{{ $i + 1 }}
                                                </td>
                                                <td class="py-3 fw-medium text-dark">{{ $row['nama_lengkap'] ?? '-' }}
                                                </td>
                                                <td class="py-3">
                                                    <code class="px-2 py-1 rounded"
                                                        style="background: #f1f5f9; font-size: 0.75rem;">{{ $row['nik'] ?? '-' }}</code>
                                                </td>
                                                <td class="py-3 text-dark">{{ $row['program_studi'] ?? '-' }}</td>
                                                <td class="pe-4 py-3 text-center">
                                                    @php
                                                        $isKip = in_array(strtolower((string) ($row['is_kip'] ?? '')), [
                                                            '1',
                                                            'ya',
                                                            'yes',
                                                            'true',
                                                        ]);
                                                    @endphp
                                                    @if ($isKip)
                                                        <span class="badge px-2 py-1 rounded-pill"
                                                            style="background: #fef3c7; color: #92400e;">KIP</span>
                                                    @else
                                                        <span class="badge px-2 py-1 rounded-pill"
                                                            style="background: #f1f5f9; color: #475569;">Non-KIP</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Action -->
                    <form action="{{ route('admin.import-etl.proses') }}" method="POST">
                        @csrf
                        <input type="hidden" name="path" value="{{ $path }}">
                        <input type="hidden" name="periode_id" value="{{ $periode->id }}">
                        <div class="d-flex gap-3">
                            <a href="{{ route('admin.import-etl.index') }}" class="btn btn-light rounded-3 py-2 px-4"
                                style="background: #f1f5f9; border: none;">
                                <i class="bi bi-arrow-left me-2"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary rounded-3 py-2 px-5"
                                data-confirm="Proses import {{ $summary['total_baris'] }} data sekarang?"
                                <i class="bi bi-play-circle me-2"></i> Proses Import ({{ $summary['total_baris'] }} Data)
                            </button>
                        </div>
                    </form>
                @endif

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

        .btn-light {
            background: #f1f5f9;
            border: none;
            transition: all 0.2s ease;
            color: #475569;
        }

        .btn-light:hover {
            background: #e2e8f0 !important;
            transform: translateY(-2px);
            color: #1e293b;
        }

        .data-row {
            transition: background 0.2s ease;
        }

        .data-row:hover {
            background: #fafbff;
        }

        code {
            font-family: 'SF Mono', 'Fira Code', monospace;
        }

        .sticky-header {
            position: sticky;
            top: 0;
            z-index: 10;
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

        .data-row:nth-child(6) {
            animation-delay: 0.12s;
        }

        .data-row:nth-child(7) {
            animation-delay: 0.14s;
        }

        .card,
        .stat-card,
        .alert {
            animation: fadeSlideUp 0.3s ease;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .stat-card {
                padding: 0.75rem !important;
            }

            .stat-icon {
                width: 36px;
                height: 36px;
            }

            .stat-icon i {
                font-size: 0.9rem;
            }

            .stat-card h2 {
                font-size: 1.1rem;
            }

            .d-flex.gap-3 {
                flex-direction: column;
            }

            .d-flex.gap-3 .btn {
                width: 100%;
            }

            .d-flex.gap-3 form {
                width: 100%;
            }

            .d-flex.gap-3 form .btn {
                width: 100%;
            }
        }

        @media (max-width: 576px) {
            code {
                font-size: 0.65rem;
            }

            .badge {
                font-size: 0.65rem;
            }
        }
    </style>
@endsection
