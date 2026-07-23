@extends('layouts.app')
@section('title', 'Preview Import')
@section('page-title', 'Preview Import')

@section('content')
    <div class="container-fluid px-2 px-sm-3 px-lg-4">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10">

                <!-- Header -->
                <div class="mb-4">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <div class="rounded-3 d-flex align-items-center justify-content-center"
                            style="background: #eef2ff; width: 48px; height: 48px;">
                            <i class="bi bi-eye fs-3 text-primary"></i>
                        </div>
                        <div>
                            <h1 class="h4 fw-bold mb-0" style="color: #1a1a2e;">Preview Import</h1>
                            <p class="text-muted small mb-0 mt-1">Review data sebelum diproses ke database</p>
                        </div>
                    </div>
                </div>

                <!-- Alert Kolom Tidak Lengkap -->
                @if (!empty($missing))
                    <div class="alert border-0 mb-4 p-3 rounded-3 alert-dismissible fade show"
                        style="background: #fee2e2; border-left: 4px solid #dc2626;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex gap-2">
                                <i class="bi bi-exclamation-triangle-fill text-danger"></i>
                                <div class="small text-dark">
                                    <strong class="fw-semibold">Kolom wajib tidak ditemukan!</strong><br>
                                    Kolom yang hilang: <strong>{{ implode(', ', $missing) }}</strong>
                                    <div class="text-muted mt-1">Pastikan file menggunakan template yang benar.</div>
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    </div>
                @endif

                <!-- Info Summary Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-6 col-lg-3">
                        <div class="stat-card p-3 rounded-4 bg-white shadow-sm h-100 border"
                            style="border-color: #e0e7ff !important;">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="text-muted small text-uppercase fw-semibold">Total Baris</span>
                                    <h2 class="fw-bold mb-0 mt-1" style="color: #1a1a2e;">{{ number_format($total) }}</h2>
                                </div>
                                <div class="stat-icon rounded-circle d-flex align-items-center justify-content-center"
                                    style="background: #eef2ff; width: 48px; height: 48px;">
                                    <i class="bi bi-database fs-5 text-primary"></i>
                                </div>
                            </div>
                            <div class="mt-2">
                                <span class="small text-muted">Total baris data</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="stat-card p-3 rounded-4 bg-white shadow-sm h-100 border"
                            style="border-color: #d1fae5 !important;">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="text-muted small text-uppercase fw-semibold">Jumlah Kolom</span>
                                    <h2 class="fw-bold mb-0 mt-1" style="color: #1a1a2e;">
                                        {{ number_format(count($headers)) }}</h2>
                                </div>
                                <div class="stat-icon rounded-circle d-flex align-items-center justify-content-center"
                                    style="background: #d1fae5; width: 48px; height: 48px;">
                                    <i class="bi bi-grid-3x3-gap-fill fs-5 text-success"></i>
                                </div>
                            </div>
                            <div class="mt-2">
                                <span class="small text-muted">Jumlah kolom</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="stat-card p-3 rounded-4 bg-white shadow-sm h-100 border"
                            style="border-color: #ffe4e2 !important;">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="text-muted small text-uppercase fw-semibold">Kolom Tidak Valid</span>
                                    <h2 class="fw-bold mb-0 mt-1" style="color: #1a1a2e;">
                                        {{ number_format(count($missing)) }}</h2>
                                </div>
                                <div class="stat-icon rounded-circle d-flex align-items-center justify-content-center"
                                    style="background: #ffe4e2; width: 48px; height: 48px;">
                                    <i class="bi bi-exclamation-triangle fs-5 text-danger"></i>
                                </div>
                            </div>
                            <div class="mt-2">
                                <span class="small text-muted">Kolom tidak valid</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="stat-card p-3 rounded-4 bg-white shadow-sm h-100 border"
                            style="border-color: #fed7aa !important;">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="text-muted small text-uppercase fw-semibold">Periode</span>
                                    <h2 class="fw-bold mb-0 mt-1" style="color: #1a1a2e; font-size: 1.1rem;">
                                        {{ Str::limit($periode->nama, 20) }}</h2>
                                </div>
                                <div class="stat-icon rounded-circle d-flex align-items-center justify-content-center"
                                    style="background: #fed7aa; width: 48px; height: 48px;">
                                    <i class="bi bi-calendar-event fs-5 text-warning"></i>
                                </div>
                            </div>
                            <div class="mt-2">
                                <span class="small text-muted">Periode seleksi</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Preview Data Card -->
                <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                    <div class="card-header bg-white py-3 px-4 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-table text-primary"></i>
                            <span class="fw-semibold">Preview Data</span>
                            <span class="badge bg-light text-dark rounded-pill">5 baris pertama</span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-borderless align-middle mb-0">
                                <thead class="bg-light-50">
                                    <tr class="small text-muted" style="border-bottom: 1px solid #e9ecef;">
                                        <th class="ps-4 py-3 text-center fw-semibold" style="width: 50px;">#</th>
                                        @foreach ($headers as $h)
                                            <th
                                                class="py-3 fw-semibold {{ in_array($h, $requiredColumns ?? ['nama_lengkap', 'nik', 'program_studi']) ? 'text-primary' : '' }}">
                                                {{ $h }}
                                                @if (in_array($h, $requiredColumns ?? ['nama_lengkap', 'nik', 'program_studi']))
                                                    <span class="badge bg-primary bg-opacity-10 text-primary ms-1"
                                                        style="font-size: 0.6rem;">Wajib</span>
                                                @endif
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($preview as $i => $row)
                                        <tr class="data-row" style="border-bottom: 1px solid #f0f2f5;">
                                            <td class="ps-4 py-3 text-center text-muted small">{{ $i + 1 }}</td>
                                            @foreach ($row as $cell)
                                                <td class="py-3 small {{ empty($cell) ? 'text-muted' : 'text-dark' }}">
                                                    {{ $cell ?: '—' }}
                                                </td>
                                            @endforeach
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ count($headers) + 1 }}" class="text-center py-5">
                                                <div class="empty-state">
                                                    <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                                                        style="width: 64px; height: 64px; background: #f1f5f9;">
                                                        <i class="bi bi-inbox fs-2 text-muted"></i>
                                                    </div>
                                                    <h6 class="fw-semibold text-muted mb-1">Tidak Ada Data</h6>
                                                    <p class="small text-muted">Tidak ada data untuk ditampilkan</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if ($total > 5)
                            <div class="p-3 text-center border-top bg-light-50">
                                <small class="text-muted">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Menampilkan 5 dari {{ number_format($total) }} baris data
                                </small>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Kolom yang Ditemukan Card -->
                <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                    <div class="card-header bg-white py-3 px-4 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle text-success"></i>
                            <span class="fw-semibold">Kolom yang Ditemukan</span>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex flex-wrap gap-2">
                            @foreach ($headers as $h)
                                <span
                                    class="column-badge px-3 py-1 rounded-pill small {{ in_array($h, $requiredColumns ?? ['nama_lengkap', 'nik', 'program_studi']) ? 'column-required' : 'column-optional' }}">
                                    <i
                                        class="bi {{ in_array($h, $requiredColumns ?? ['nama_lengkap', 'nik', 'program_studi']) ? 'bi-check-circle-fill' : 'bi-dot' }} me-1"></i>
                                    {{ $h }}
                                </span>
                            @endforeach
                        </div>
                        <div class="d-flex gap-3 mt-3 pt-1">
                            <span class="legend-item d-flex align-items-center gap-1">
                                <span class="legend-badge bg-success rounded-circle"
                                    style="width: 10px; height: 10px;"></span>
                                <span class="small text-muted">Kolom Wajib</span>
                            </span>
                            <span class="legend-item d-flex align-items-center gap-1">
                                <span class="legend-badge bg-secondary rounded-circle"
                                    style="width: 10px; height: 10px;"></span>
                                <span class="small text-muted">Kolom Opsional</span>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-body p-4">
                        <div class="d-flex flex-column flex-sm-row gap-3 justify-content-between align-items-center">
                            <a href="{{ route('admin.import-etl.index') }}" class="btn btn-light rounded-3 py-2 px-4"
                                style="background: #f1f5f9; border: none;">
                                <i class="bi bi-arrow-left me-2"></i> Kembali
                            </a>
                            @if (empty($missing))
                                <form action="{{ route('admin.import-etl.proses') }}" method="POST" id="processForm">
                                    @csrf
                                    <input type="hidden" name="path" value="{{ $path }}">
                                    <input type="hidden" name="periode_id" value="{{ $periode->id }}">
                                    <button type="submit" class="btn btn-primary rounded-3 py-2 px-5">
                                        <i class="bi bi-play-circle me-2"></i> Proses ETL ({{ number_format($total) }}
                                        data)
                                    </button>
                                </form>
                            @else
                                <button class="btn btn-secondary rounded-3 py-2 px-5" disabled
                                    style="opacity: 0.6; cursor: not-allowed;">
                                    <i class="bi bi-play-circle me-2"></i> Proses ETL (Ada kolom tidak valid)
                                </button>
                            @endif
                        </div>
                        @if (!empty($missing))
                            <div class="alert border-0 mt-3 mb-0 p-3 rounded-3" style="background: #fef3c7;">
                                <div class="d-flex gap-2">
                                    <i class="bi bi-info-circle-fill text-warning"></i>
                                    <small class="text-warning">Silakan perbaiki file dengan menambahkan kolom yang hilang,
                                        lalu upload ulang.</small>
                                </div>
                            </div>
                        @endif
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

        .btn-secondary {
            background: #94a3b8;
            border: none;
        }

        .column-required {
            background: #d1fae5;
            color: #065f46;
        }

        .column-optional {
            background: #f1f5f9;
            color: #475569;
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

            .table td,
            .table th {
                padding: 0.75rem 0.5rem;
            }

            .column-badge {
                font-size: 0.7rem;
                padding: 0.2rem 0.6rem;
            }
        }

        @media (max-width: 576px) {
            .stat-card .text-muted.small {
                font-size: 0.6rem;
            }

            .d-flex.flex-column-sm-row {
                flex-direction: column;
                gap: 0.75rem;
            }

            .d-flex.flex-column-sm-row .btn,
            .d-flex.flex-column-sm-row form {
                width: 100%;
            }

            .d-flex.flex-column-sm-row button {
                width: 100%;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add data-label for responsive table (optional, for better mobile view)
            const tableHeaders = document.querySelectorAll('.table thead th');
            const tableRows = document.querySelectorAll('.table tbody tr');

            tableRows.forEach(row => {
                const cells = row.querySelectorAll('td');
                cells.forEach((cell, index) => {
                    if (tableHeaders[index] && window.innerWidth < 768) {
                        const headerText = tableHeaders[index].textContent.trim().replace('Wajib',
                            '').replace('#', '').trim();
                        if (headerText && index > 0) {
                            cell.setAttribute('data-label', headerText);
                        }
                    }
                });
            });

            // Confirm process function
            const processForm = document.getElementById('processForm');
            if (processForm) {
                processForm.addEventListener('submit', function(e) {
                    const total = '{{ number_format($total) }}';
                    const periode = '{{ $periode->nama }}';

                    e.preventDefault();
                    swalConfirm(`⚠️ Konfirmasi Proses ETL\n\n` +
                        `Total data: ${total} baris\n` +
                        `Periode: ${periode}\n\n` +
                        `Proses ini akan:\n` +
                        `✓ Validasi data mahasiswa\n` +
                        `✓ Hitung skor SAW otomatis\n` +
                        `✓ Tentukan golongan UKT\n` +
                        `✓ Simpan ke database\n\n` +
                        `Lanjutkan proses import?`).then(function(ok) {
                        if (ok) e.target.submit();
                    });
                });
            }
        });
    </script>
@endsection
