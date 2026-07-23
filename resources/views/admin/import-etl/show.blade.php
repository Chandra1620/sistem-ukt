@extends('layouts.app')
@section('title', 'Detail Import Log')
@section('page-title', 'Detail Import Log')

@section('content')
    <div class="container-fluid px-2 px-sm-3 px-lg-4">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8">

                <!-- Header -->
                <div class="mb-4">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <div class="rounded-3 d-flex align-items-center justify-content-center"
                            style="background: #eef2ff; width: 48px; height: 48px;">
                            <i class="bi bi-file-text fs-3 text-primary"></i>
                        </div>
                        <div>
                            <h1 class="h4 fw-bold mb-0" style="color: #1a1a2e;">Detail Import Log</h1>
                            <p class="text-muted small mb-0 mt-1">Detail proses import data</p>
                        </div>
                    </div>
                </div>

                <!-- Summary Card -->
                <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                    <div class="card-header bg-white py-3 px-4 border-bottom">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-info-circle text-primary"></i>
                                <span class="fw-semibold">Ringkasan Import</span>
                            </div>
                            <span
                                class="status-badge status-{{ $importLog->status }} px-3 py-1 rounded-pill small fw-semibold">
                                <i
                                    class="bi {{ $importLog->status === 'success' ? 'bi-check-circle-fill' : ($importLog->status === 'partial' ? 'bi-exclamation-triangle-fill' : 'bi-x-circle-fill') }} me-1"></i>
                                {{ $importLog->label_status }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-4 p-sm-5">
                        <div class="row g-4">
                            <!-- File & Periode -->
                            <div class="col-md-6">
                                <div class="info-detail p-3 rounded-3 d-flex align-items-start gap-3"
                                    style="background: #f8fafc;">
                                    <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                                        style="background: #eef2ff; width: 44px; height: 44px;">
                                        <i class="bi bi-file-earmark-excel-fill text-success fs-5"></i>
                                    </div>
                                    <div class="flex-grow-1" style="min-width: 0;">
                                        <div class="small text-muted mb-1">Nama File</div>
                                        <div class="fw-semibold text-dark text-break word-break-all"
                                            style="word-break: break-all; overflow-wrap: break-word;">
                                            {{ $importLog->nama_file }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-detail p-3 rounded-3 d-flex align-items-center gap-3"
                                    style="background: #f8fafc;">
                                    <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                                        style="background: #fed7aa; width: 44px; height: 44px;">
                                        <i class="bi bi-calendar-event text-warning fs-5"></i>
                                    </div>
                                    <div class="flex-grow-1" style="min-width: 0;">
                                        <div class="small text-muted mb-1">Periode Seleksi</div>
                                        <div class="fw-semibold text-dark text-break">{{ $importLog->periode_nama ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- User & Waktu -->
                            <div class="col-md-6">
                                <div class="info-detail p-3 rounded-3 d-flex align-items-center gap-3"
                                    style="background: #f8fafc;">
                                    <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                                        style="background: #d1fae5; width: 44px; height: 44px;">
                                        <i class="bi bi-person-badge text-success fs-5"></i>
                                    </div>
                                    <div class="flex-grow-1" style="min-width: 0;">
                                        <div class="small text-muted mb-1">Diproses Oleh</div>
                                        <div class="fw-semibold text-dark text-break">{{ $importLog->user->name ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-detail p-3 rounded-3 d-flex align-items-center gap-3"
                                    style="background: #f8fafc;">
                                    <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                                        style="background: #e0e7ff; width: 44px; height: 44px;">
                                        <i class="bi bi-clock-history text-primary fs-5"></i>
                                    </div>
                                    <div class="flex-grow-1" style="min-width: 0;">
                                        <div class="small text-muted mb-1">Waktu Proses</div>
                                        <div class="fw-semibold text-dark">
                                            {{ $importLog->created_at->format('d/m/Y H:i:s') }}</div>
                                    </div>
                                </div>
                            </div>
                            @if ($importLog->selesai_at)
                                <div class="col-12">
                                    <div class="info-detail p-3 rounded-3 d-flex align-items-center gap-3"
                                        style="background: #f8fafc;">
                                        <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                                            style="background: #d1fae5; width: 44px; height: 44px;">
                                            <i class="bi bi-check-circle text-success fs-5"></i>
                                        </div>
                                        <div class="flex-grow-1" style="min-width: 0;">
                                            <div class="small text-muted mb-1">Selesai Pada</div>
                                            <div class="fw-semibold text-dark">
                                                {{ $importLog->selesai_at->format('d/m/Y H:i:s') }}
                                                <span
                                                    class="text-muted small ms-2">({{ $importLog->selesai_at->diffForHumans() }})</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Statistik Cards (sama seperti sebelumnya) -->
                        <div class="row g-3 mt-4">
                            <div class="col-6 col-lg-3">
                                <div class="stat-card p-3 rounded-3 bg-white shadow-sm h-100 border"
                                    style="border-color: #e0e7ff !important;">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <span class="text-muted small text-uppercase fw-semibold">Total Data</span>
                                            <h3 class="fw-bold mb-0 mt-1" style="color: #1a1a2e;">
                                                {{ number_format($importLog->total_baris) }}</h3>
                                        </div>
                                        <div class="stat-icon rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                            style="background: #eef2ff; width: 44px; height: 44px;">
                                            <i class="bi bi-database fs-5 text-primary"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-lg-3">
                                <div class="stat-card p-3 rounded-3 bg-white shadow-sm h-100 border"
                                    style="border-color: #d1fae5 !important;">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <span class="text-muted small text-uppercase fw-semibold">Berhasil</span>
                                            <h3 class="fw-bold mb-0 mt-1" style="color: #1a1a2e;">
                                                {{ number_format($importLog->berhasil) }}</h3>
                                        </div>
                                        <div class="stat-icon rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                            style="background: #d1fae5; width: 44px; height: 44px;">
                                            <i class="bi bi-check-circle fs-5 text-success"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-lg-3">
                                <div class="stat-card p-3 rounded-3 bg-white shadow-sm h-100 border"
                                    style="border-color: #ffe4e2 !important;">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <span class="text-muted small text-uppercase fw-semibold">Gagal</span>
                                            <h3 class="fw-bold mb-0 mt-1" style="color: #1a1a2e;">
                                                {{ number_format($importLog->gagal) }}</h3>
                                        </div>
                                        <div class="stat-icon rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                            style="background: #ffe4e2; width: 44px; height: 44px;">
                                            <i class="bi bi-x-circle fs-5 text-danger"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-lg-3">
                                <div class="stat-card p-3 rounded-3 bg-white shadow-sm h-100 border"
                                    style="border-color: #fed7aa !important;">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <span class="text-muted small text-uppercase fw-semibold">Duplikat</span>
                                            <h3 class="fw-bold mb-0 mt-1" style="color: #1a1a2e;">
                                                {{ number_format($importLog->duplikat) }}</h3>
                                        </div>
                                        <div class="stat-icon rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                            style="background: #fed7aa; width: 44px; height: 44px;">
                                            <i class="bi bi-files fs-5 text-warning"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Error Log Card dengan overflow handling -->
                @if (!empty($importLog->error_log) && is_array($importLog->error_log) && count($importLog->error_log) > 0)
                    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                        <div class="card-header bg-white py-3 px-4 border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-exclamation-triangle text-danger"></i>
                                <span class="fw-semibold">Log Error</span>
                                <span
                                    class="badge bg-danger bg-opacity-10 text-danger rounded-pill">{{ count($importLog->error_log) }}
                                    Error</span>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="error-log-container p-3" style="background: #1e293b;">
                                @foreach ($importLog->error_log as $error)
                                    <div class="error-item d-flex align-items-start gap-2 p-2 mb-2 rounded-2"
                                        style="background: #334155; cursor: pointer;" onclick="copyError(this)">
                                        <i class="bi bi-x-circle-fill text-danger mt-1 flex-shrink-0"></i>
                                        <span class="error-message small flex-grow-1 text-break"
                                            style="color: #f87171; word-break: break-word; overflow-wrap: break-word;">{{ $error }}</span>
                                        <i class="bi bi-clipboard text-muted flex-shrink-0"
                                            style="font-size: 0.75rem;"></i>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Action Buttons -->
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-body p-4">
                        <div class="d-flex flex-column flex-sm-row gap-3 justify-content-between align-items-center">
                            <a href="{{ route('admin.import-etl.index') }}" class="btn btn-light rounded-3 py-2 px-4"
                                style="background: #f1f5f9; border: none;">
                                <i class="bi bi-arrow-left me-2"></i> Kembali
                            </a>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-secondary rounded-3 py-2 px-4"
                                    onclick="window.print()" style="border-color: #e2e8f0; color: #475569;">
                                    <i class="bi bi-printer me-2"></i> Cetak
                                </button>
                                <form action="{{ route('admin.import-etl.destroy', $importLog) }}" method="POST"
                                    data-confirm="⚠️ Hapus log ini? Data tidak dapat dikembalikan."
                                    class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger rounded-3 py-2 px-4">
                                        <i class="bi bi-trash me-2"></i> Hapus Log
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <style>
        /* Custom Styles */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .status-success {
            background: #d1fae5;
            color: #065f46;
        }

        .status-partial {
            background: #fed7aa;
            color: #9b3412;
        }

        .status-failed {
            background: #fee2e2;
            color: #991b1b;
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

        .info-detail {
            transition: all 0.2s ease;
        }

        .info-detail:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        /* Text overflow handling */
        .text-break {
            word-break: break-word !important;
            overflow-wrap: break-word !important;
        }

        .word-break-all {
            word-break: break-all !important;
        }

        .flex-shrink-0 {
            flex-shrink: 0 !important;
        }

        /* Error log container */
        .error-log-container {
            max-height: 400px;
            overflow-y: auto;
        }

        /* Scrollbar styling */
        .error-log-container::-webkit-scrollbar {
            width: 6px;
        }

        .error-log-container::-webkit-scrollbar-track {
            background: #1e293b;
            border-radius: 3px;
        }

        .error-log-container::-webkit-scrollbar-thumb {
            background: #475569;
            border-radius: 3px;
        }

        .error-log-container::-webkit-scrollbar-thumb:hover {
            background: #64748b;
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

        .btn-outline-secondary {
            transition: all 0.2s ease;
        }

        .btn-outline-secondary:hover {
            background: #1e293b;
            border-color: #1e293b;
            color: white;
            transform: translateY(-2px);
        }

        .btn-danger {
            background: #dc2626;
            border: none;
            transition: all 0.2s ease;
        }

        .btn-danger:hover {
            background: #b91c1c;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
        }

        .error-item {
            transition: all 0.2s ease;
        }

        .error-item:hover {
            background: #475569 !important;
            transform: translateX(4px);
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

        .card,
        .stat-card,
        .info-detail {
            animation: fadeSlideUp 0.3s ease;
        }

        /* Print Styles */
        @media print {

            .btn,
            .breadcrumb,
            .nav-link,
            .btn-close,
            .alert,
            .action-buttons,
            .btn-outline-secondary,
            .btn-danger,
            .error-item [class*="clipboard"] {
                display: none !important;
            }

            .card {
                box-shadow: none !important;
                border: 1px solid #ddd !important;
            }

            body {
                padding: 0 !important;
                margin: 0 !important;
            }

            .container-fluid {
                padding: 0 !important;
            }

            .error-log-container {
                background: white !important;
            }

            .error-item {
                background: #f1f5f9 !important;
                border: 1px solid #e2e8f0;
            }

            .error-message {
                color: #dc2626 !important;
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .card-body {
                padding: 1.5rem !important;
            }

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

            .stat-card h3 {
                font-size: 1.25rem;
            }

            .info-detail {
                padding: 0.75rem !important;
            }

            .info-detail .rounded-2 {
                width: 36px !important;
                height: 36px !important;
            }

            .info-detail .fs-5 {
                font-size: 1rem !important;
            }
        }

        @media (max-width: 576px) {
            .d-flex.gap-2 {
                flex-direction: column;
                width: 100%;
            }

            .d-flex.gap-2 .btn,
            .d-flex.gap-2 form {
                width: 100%;
            }

            .status-badge {
                font-size: 0.7rem;
                padding: 0.25rem 0.6rem;
            }
        }
    </style>

    <script>
        function copyError(element) {
            const errorText = element.querySelector('.error-message')?.textContent;
            if (errorText) {
                navigator.clipboard.writeText(errorText).then(() => {
                    const originalBg = element.style.background;
                    element.style.background = '#10b981';
                    setTimeout(() => {
                        element.style.background = originalBg;
                    }, 500);
                });
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.forEach(function(el) {
                new bootstrap.Tooltip(el);
            });
        });
    </script>
@endsection
