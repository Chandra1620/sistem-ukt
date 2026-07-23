@extends('layouts.app')
@section('title', 'Import & ETL')
@section('page-title', 'Import & ETL')

@section('content')
    <div class="container-fluid px-2 px-sm-3 px-lg-4">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10">

                <!-- Header -->
                <div class="mb-4">
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                        <div>
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <div class="rounded-3 d-flex align-items-center justify-content-center"
                                    style="background: #eef2ff; width: 48px; height: 48px;">
                                    <i class="bi bi-cloud-upload fs-3 text-primary"></i>
                                </div>
                                <div>
                                    <h1 class="h4 fw-bold mb-0" style="color: #1a1a2e;">Import & ETL Dataset</h1>
                                    <p class="text-muted small mb-0 mt-1">Upload dataset Excel/CSV untuk import massal data
                                        mahasiswa</p>
                                </div>
                            </div>
                        </div>
                        <div>
                            <a href="{{ route('admin.import-etl.download-template') }}"
                                class="btn btn-outline-primary rounded-3 px-4 py-2"
                                style="border-color: #e2e8f0; color: #475569;">
                                <i class="bi bi-download me-2"></i> Download Template
                            </a>
                        </div>
                    </div>
                </div>

                <!-- ETL Steps -->
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="step-card p-3 rounded-4 bg-white shadow-sm h-100 border"
                            style="border-color: #e0e7ff !important;">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-2 d-flex align-items-center justify-content-center"
                                    style="background: #eef2ff; width: 48px; height: 48px;">
                                    <i class="bi bi-cloud-upload fs-4 text-primary"></i>
                                </div>
                                <div>
                                    <div class="small text-muted mb-1">Langkah 1</div>
                                    <h6 class="fw-bold mb-0" style="color: #1a1a2e;">Extract</h6>
                                    <p class="small text-muted mb-0">Upload file dataset</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="step-card p-3 rounded-4 bg-white shadow-sm h-100 border"
                            style="border-color: #fed7aa !important;">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-2 d-flex align-items-center justify-content-center"
                                    style="background: #fed7aa; width: 48px; height: 48px;">
                                    <i class="bi bi-gear-wide-connected fs-4 text-warning"></i>
                                </div>
                                <div>
                                    <div class="small text-muted mb-1">Langkah 2</div>
                                    <h6 class="fw-bold mb-0" style="color: #1a1a2e;">Transform</h6>
                                    <p class="small text-muted mb-0">Validasi & hitung SAW</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="step-card p-3 rounded-4 bg-white shadow-sm h-100 border"
                            style="border-color: #d1fae5 !important;">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-2 d-flex align-items-center justify-content-center"
                                    style="background: #d1fae5; width: 48px; height: 48px;">
                                    <i class="bi bi-database-add fs-4 text-success"></i>
                                </div>
                                <div>
                                    <div class="small text-muted mb-1">Langkah 3</div>
                                    <h6 class="fw-bold mb-0" style="color: #1a1a2e;">Load</h6>
                                    <p class="small text-muted mb-0">Simpan ke database</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Upload Form Card -->
                <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                    <div class="card-header bg-white py-3 px-4 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-cloud-upload text-primary"></i>
                            <span class="fw-semibold">Upload Dataset</span>
                        </div>
                    </div>
                    <div class="card-body p-4 p-sm-5">
                        <form action="{{ route('admin.import-etl.preview') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small mb-2">
                                        <i class="bi bi-calendar-event text-primary me-1"></i> Periode Seleksi
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select name="periode_id"
                                        class="form-select rounded-3 @error('periode_id') is-invalid @enderror"
                                        style="padding: 11px 16px; border-color: #e2e8f0;" required>
                                        <option value="">-- Pilih Periode --</option>
                                        @foreach ($periode as $p)
                                            <option value="{{ $p->id }}">
                                                {{ $p->nama }} ({{ $p->tahun }})
                                                @if ($p->status === 'aktif')
                                                    <span
                                                        class="badge bg-success bg-opacity-10 text-success ms-1">Aktif</span>
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('periode_id')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small mb-2">
                                        <i class="bi bi-file-earmark-excel text-primary me-1"></i> File Excel/CSV
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="file-upload-wrapper">
                                        <input type="file" name="file" id="fileInput" class="file-upload-input"
                                            accept=".xlsx,.xls,.csv" required>
                                        <label for="fileInput" class="file-upload-label rounded-3"
                                            style="border: 2px dashed #e2e8f0; background: #f8fafc; padding: 24px; text-align: center; cursor: pointer;">
                                            <i class="bi bi-cloud-arrow-up fs-3 text-muted"></i>
                                            <div class="small mt-2 text-muted">Klik atau drag file ke sini</div>
                                            <div class="small text-muted">.xlsx, .xls, .csv (Max 10MB)</div>
                                        </label>
                                        <div id="fileNameDisplay" class="small text-success mt-2 text-center"
                                            style="display: none;"></div>
                                    </div>
                                    @error('file')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="alert border-0 rounded-3 mt-4 p-3" style="background: #eef2ff;">
                                <div class="d-flex gap-2">
                                    <i class="bi bi-info-circle-fill text-primary"></i>
                                    <div class="small text-dark">
                                        <strong class="fw-semibold">Petunjuk:</strong> Pastikan file menggunakan format
                                        kolom sesuai template.
                                        Download template terlebih dahulu jika belum punya.
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex gap-3 mt-4 pt-2">
                                <a href="{{ route('admin.import-etl.download-template') }}"
                                    class="btn btn-light flex-grow-1 rounded-3 py-2"
                                    style="background: #f1f5f9; border: none;">
                                    <i class="bi bi-download me-2"></i> Download Template
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-clipboard-check me-2"></i> Validasi Data
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Riwayat Import Card -->
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header bg-white py-3 px-4 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-clock-history text-primary"></i>
                            <span class="fw-semibold">Riwayat Import</span>
                            <span class="badge bg-light text-dark rounded-pill">{{ $logs->total() }} Log</span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-borderless align-middle mb-0">
                                <thead class="bg-light-50">
                                    <tr class="small text-muted" style="border-bottom: 1px solid #e9ecef;">
                                        <th class="ps-4 py-3 fw-semibold">WAKTU</th>
                                        <th class="py-3 fw-semibold">FILE</th>
                                        <th class="py-3 fw-semibold">PERIODE</th>
                                        <th class="py-3 fw-semibold">TOTAL</th>
                                        <th class="py-3 fw-semibold">BERHASIL</th>
                                        <th class="py-3 fw-semibold">GAGAL</th>
                                        <th class="py-3 fw-semibold">DUPLIKAT</th>
                                        <th class="py-3 fw-semibold">STATUS</th>
                                        <th class="pe-4 py-3 text-end fw-semibold">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($logs as $log)
                                        <tr class="data-row" style="border-bottom: 1px solid #f0f2f5;">
                                            <td class="ps-4 py-3">
                                                <div class="small">
                                                    <div class="text-dark">{{ $log->created_at->format('d/m/Y') }}</div>
                                                    <div class="text-muted" style="font-size: 0.65rem;">
                                                        {{ $log->created_at->format('H:i:s') }}</div>
                                                </div>
                                            </td>
                                            <td class="py-3">
                                                <div class="d-flex align-items-center gap-2">
                                                    <i class="bi bi-file-earmark-excel text-success"></i>
                                                    <span
                                                        class="small text-dark">{{ Str::limit($log->nama_file, 30) }}</span>
                                                </div>
                                            </td>
                                            <td class="py-3">
                                                <span
                                                    class="badge bg-light text-dark rounded-pill">{{ $log->periode_nama ?? '-' }}</span>
                                            </td>
                                            <td class="py-3">
                                                <span
                                                    class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill">{{ number_format($log->total_baris) }}</span>
                                            </td>
                                            <td class="py-3">
                                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill">
                                                    <i class="bi bi-check-circle-fill me-1"></i>
                                                    {{ number_format($log->berhasil) }}
                                                </span>
                                            </td>
                                            <td class="py-3">
                                                <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill">
                                                    <i class="bi bi-x-circle-fill me-1"></i>
                                                    {{ number_format($log->gagal) }}
                                                </span>
                                            </td>
                                            <td class="py-3">
                                                <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill">
                                                    <i class="bi bi-files me-1"></i> {{ number_format($log->duplikat) }}
                                                </span>
                                            </td>
                                            <td class="py-3">
                                                <span
                                                    class="status-badge status-{{ $log->status }} px-3 py-1 rounded-pill small">
                                                    {{ $log->label_status }}
                                                </span>
                                            </td>
                                            <td class="pe-4 py-3 text-end">
                                                <div class="d-flex gap-2 justify-content-end">
                                                    <a href="{{ route('admin.import-etl.show', $log) }}"
                                                        class="btn-action view" data-bs-toggle="tooltip"
                                                        title="Detail Log">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <form action="{{ route('admin.import-etl.destroy', $log) }}"
                                                        method="POST" class="d-inline"
                                                        data-confirm="Hapus log ini?"
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn-action delete"
                                                            data-bs-toggle="tooltip" title="Hapus Log">
                                                            <i class="bi bi-trash3"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-5">
                                                <div class="empty-state">
                                                    <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                                                        style="width: 64px; height: 64px; background: #f1f5f9;">
                                                        <i class="bi bi-inbox fs-2 text-muted"></i>
                                                    </div>
                                                    <h6 class="fw-semibold text-muted mb-1">Belum Ada Riwayat Import</h6>
                                                    <p class="small text-muted">Silakan upload file untuk memulai import
                                                        data</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if ($logs->hasPages())
                            <div class="border-top py-3 px-4">
                                {{ $logs->links() }}
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

        .step-card {
            transition: all 0.25s ease;
        }

        .step-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08) !important;
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

        .btn-outline-primary {
            border-color: #e2e8f0;
            color: #475569;
            transition: all 0.2s ease;
        }

        .btn-outline-primary:hover {
            background: #1e293b;
            border-color: #1e293b;
            color: white;
            transform: translateY(-2px);
        }

        .form-select,
        .form-control {
            transition: all 0.2s ease;
        }

        .form-select:focus,
        .form-control:focus {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
        }

        .file-upload-label {
            transition: all 0.2s ease;
        }

        .file-upload-label:hover {
            border-color: #3b82f6 !important;
            background: #f0f7ff !important;
        }

        /* Status Badges */
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

        .status-processing {
            background: #dbeafe;
            color: #1e40af;
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

        .btn-action.view {
            color: #3b82f6;
        }

        .btn-action.view:hover {
            background: #eef2ff;
            color: #2563eb;
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

        .data-row {
            transition: background 0.2s ease;
        }

        .data-row:hover {
            background: #fafbff;
        }

        /* Pagination */
        .pagination {
            gap: 4px;
            margin: 0;
            justify-content: center;
        }

        .page-item .page-link {
            border-radius: 40px !important;
            border: none;
            padding: 6px 12px;
            color: #475569;
            font-size: 0.8rem;
            transition: all 0.2s;
        }

        .page-item .page-link:hover {
            background: #eef2ff;
            color: #3b82f6;
        }

        .page-item.active .page-link {
            background: #1e293b;
            color: white;
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

        .card {
            animation: fadeSlideUp 0.3s ease;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .card-body {
                padding: 1.5rem !important;
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

            .step-card {
                margin-bottom: 0;
            }
        }

        @media (max-width: 576px) {
            .status-badge {
                font-size: 0.65rem;
            }

            .badge {
                font-size: 0.65rem;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tooltips initialization
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.forEach(function(el) {
                new bootstrap.Tooltip(el);
            });

            // File input preview
            const fileInput = document.getElementById('fileInput');
            const fileNameDisplay = document.getElementById('fileNameDisplay');

            if (fileInput) {
                fileInput.addEventListener('change', function() {
                    if (this.files && this.files[0]) {
                        const fileName = this.files[0].name;
                        const fileSize = (this.files[0].size / 1024).toFixed(2);
                        fileNameDisplay.innerHTML =
                            `<i class="bi bi-check-circle-fill me-1 text-success"></i> ${fileName} (${fileSize} KB)`;
                        fileNameDisplay.style.display = 'block';
                    } else {
                        fileNameDisplay.style.display = 'none';
                    }
                });
            }
        });
    </script>
@endsection
