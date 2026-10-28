@extends('layouts.app')
@section('title', 'Export Data')
@section('page-title', 'Export Data')

@section('content')
    <div class="container-fluid px-2 px-sm-3 px-lg-4">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10">

                <!-- Header -->
                <div class="mb-4">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <div class="rounded-3 d-flex align-items-center justify-content-center"
                            style="background: #eef2ff; width: 48px; height: 48px;">
                            <i class="bi bi-database-down fs-3 text-primary"></i>
                        </div>
                        <div>
                            <h1 class="h4 fw-bold mb-0" style="color: #1a1a2e;">Export Data UKT</h1>
                            <p class="text-muted small mb-0 mt-1">Filter data sesuai kebutuhan sebelum melakukan export</p>
                        </div>
                    </div>
                </div>

                {{-- Success Alert --}}
                {{-- @if (session('success'))
                    <div class="alert alert-success border-0 mb-4 p-3 rounded-3"
                        style="background: #e8f5e9; border-left: 4px solid #4caf50;">
                        <div class="d-flex align-items-center gap-3">
                            <i class="bi bi-check-circle-fill fs-4 text-success"></i>
                            <div class="flex-grow-1 text-dark">{{ session('success') }}</div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    </div>
                @endif --}}

                {{-- Filter Card --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                    <div class="card-header bg-white py-3 px-4 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-funnel text-primary"></i>
                            <span class="fw-semibold">Filter Data</span>
                        </div>
                    </div>
                    <div class="card-body p-4 p-sm-5">
                        <form id="exportForm">
                            <div class="row g-4">
                                {{-- Periode --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small mb-2">
                                        <i class="bi bi-calendar-event text-primary me-1"></i> Periode Seleksi
                                    </label>
                                    <select name="periode_id" class="form-select rounded-3"
                                        style="padding: 11px 16px; border-color: #e2e8f0;">
                                        <option value="">-- Semua Periode --</option>
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
                                </div>

                                {{-- Program Studi --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small mb-2">
                                        <i class="bi bi-building text-primary me-1"></i> Program Studi
                                    </label>
                                    <select name="program_studi_id" class="form-select rounded-3"
                                        style="padding: 11px 16px; border-color: #e2e8f0;">
                                        <option value="">-- Semua Program Studi --</option>
                                        @foreach ($programStudi->groupBy('jenjang') as $jenjang => $list)
                                            <optgroup label="{{ $jenjang }}">
                                                @foreach ($list as $p)
                                                    <option value="{{ $p->id }}">{{ $p->nama }}</option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Status Mahasiswa --}}
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small mb-2">
                                        <i class="bi bi-flag text-primary me-1"></i> Status Mahasiswa
                                    </label>
                                    <select name="status" class="form-select rounded-3"
                                        style="padding: 11px 16px; border-color: #e2e8f0;">
                                        <option value="">-- Semua Status --</option>
                                        @foreach ($statusList as $val => $label)
                                            <option value="{{ $val }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Golongan UKT --}}
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small mb-2">
                                        <i class="bi bi-cash-stack text-primary me-1"></i> Golongan UKT
                                    </label>
                                    <select name="golongan" class="form-select rounded-3"
                                        style="padding: 11px 16px; border-color: #e2e8f0;">
                                        <option value="">-- Semua Golongan --</option>
                                        @foreach ($golongan as $g)
                                            <option value="{{ $g->golongan }}">
                                                Golongan {{ $g->golongan }}
                                                (Rp {{ number_format($g->nominal, 0, ',', '.') }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Tahun --}}
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small mb-2">
                                        <i class="bi bi-calendar text-primary me-1"></i> Tahun Pendaftaran
                                    </label>
                                    <select name="tahun" class="form-select rounded-3"
                                        style="padding: 11px 16px; border-color: #e2e8f0;"
                                        onchange="filterPeriodeByTahun(this.value)">
                                        <option value="">-- Semua Tahun --</option>
                                        @foreach ($periode->pluck('tahun')->unique()->sortDesc() as $tahun)
                                            <option value="{{ $tahun }}">{{ $tahun }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Rentang Tanggal --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small mb-2">
                                        <i class="bi bi-calendar-range text-primary me-1"></i> Tanggal Daftar (Dari)
                                    </label>
                                    <input type="date" name="tanggal_mulai" class="form-control rounded-3"
                                        style="padding: 11px 16px; border-color: #e2e8f0;">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small mb-2">
                                        <i class="bi bi-calendar-range text-primary me-1"></i> Tanggal Daftar (Sampai)
                                    </label>
                                    <input type="date" name="tanggal_selesai" class="form-control rounded-3"
                                        style="padding: 11px 16px; border-color: #e2e8f0;">
                                </div>
                            </div>

                            {{-- Preview Box --}}
                            <div class="mt-4 p-4 rounded-3" id="previewBox" style="background: #f0f7ff;">
                                <div class="d-flex align-items-center gap-3 flex-wrap">
                                    <div class="rounded-2 d-flex align-items-center justify-content-center"
                                        style="background: #eef2ff; width: 44px; height: 44px;">
                                        <i class="bi bi-info-circle-fill text-primary fs-4"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold" id="previewText">Klik "Cek Data" untuk melihat jumlah data
                                            sesuai filter</div>
                                        <div class="small text-muted" id="previewSub"></div>
                                    </div>
                                    <button type="button" class="btn btn-primary rounded-pill px-4 py-2"
                                        onclick="previewData()">
                                        <i class="bi bi-search me-2"></i> Cek Data
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Export Options Card --}}
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header bg-white py-3 px-4 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-download text-primary"></i>
                            <span class="fw-semibold">Pilih Format Export</span>
                        </div>
                    </div>
                    <div class="card-body p-4 p-sm-5">
                        <div class="row g-4">
                            {{-- Excel Card --}}
                            <div class="col-md-4">
                                <div class="export-card p-4 rounded-3 text-center h-100 border"
                                    style="border-color: #d1fae5 !important;">
                                    <div class="rounded-2 d-flex align-items-center justify-content-center mx-auto mb-3"
                                        style="background: #d1fae5; width: 60px; height: 60px;">
                                        <i class="bi bi-file-earmark-excel-fill fs-2 text-success"></i>
                                    </div>
                                    <h6 class="fw-bold mb-1">Excel (.xlsx)</h6>
                                    <p class="small text-muted mb-2">Format spreadsheet dengan styling</p>
                                    <div class="small text-muted mb-3">
                                        <span class="badge bg-light text-dark me-1">Terformat</span>
                                        <span class="badge bg-light text-dark">Bisa difilter</span>
                                    </div>
                                    <button onclick="doExport('excel')" class="btn btn-success w-100 rounded-pill py-2">
                                        <i class="bi bi-download me-2"></i> Export Excel
                                    </button>
                                </div>
                            </div>

                            {{-- PDF Card --}}
                            <div class="col-md-4">
                                <div class="export-card p-4 rounded-3 text-center h-100 border"
                                    style="border-color: #ffe4e2 !important;">
                                    <div class="rounded-2 d-flex align-items-center justify-content-center mx-auto mb-3"
                                        style="background: #ffe4e2; width: 60px; height: 60px;">
                                        <i class="bi bi-file-earmark-pdf-fill fs-2 text-danger"></i>
                                    </div>
                                    <h6 class="fw-bold mb-1">PDF</h6>
                                    <p class="small text-muted mb-2">Format laporan siap cetak</p>
                                    <div class="small text-muted mb-3">
                                        <span class="badge bg-light text-danger border me-1">Maks. 300 data</span>
                                        <span class="badge bg-light text-dark">Siap cetak</span>
                                    </div>
                                    <button onclick="doExport('pdf')" class="btn btn-danger w-100 rounded-pill py-2">
                                        <i class="bi bi-download me-2"></i> Export PDF
                                    </button>
                                </div>
                            </div>

                            {{-- CSV Card --}}
                            <div class="col-md-4">
                                <div class="export-card p-4 rounded-3 text-center h-100 border"
                                    style="border-color: #e0e7ff !important;">
                                    <div class="rounded-2 d-flex align-items-center justify-content-center mx-auto mb-3"
                                        style="background: #eef2ff; width: 60px; height: 60px;">
                                        <i class="bi bi-file-earmark-text-fill fs-2 text-primary"></i>
                                    </div>
                                    <h6 class="fw-bold mb-1">CSV</h6>
                                    <p class="small text-muted mb-2">Format universal untuk berbagai sistem</p>
                                    <div class="small text-muted mb-3">
                                        <span class="badge bg-light text-dark me-1">Universal</span>
                                        <span class="badge bg-light text-dark">Ringan</span>
                                    </div>
                                    <button onclick="doExport('csv')" class="btn btn-primary w-100 rounded-pill py-2">
                                        <i class="bi bi-download me-2"></i> Export CSV
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Tips Alert --}}
                        <div class="alert border-0 rounded-3 mt-4 p-3" style="background: #eef2ff;">
                            <div class="d-flex gap-2">
                                <i class="bi bi-lightbulb text-warning"></i>
                                <div class="small text-dark">
                                    <strong class="fw-semibold">Tips:</strong> Klik "Cek Data" terlebih dahulu untuk
                                    memastikan filter sudah benar sebelum melakukan export. Filter kosong akan mengexport
                                    semua data.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <style>
        /* Custom Styles */
        .form-select,
        .form-control {
            transition: all 0.2s ease;
        }

        .form-select:focus,
        .form-control:focus {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
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

        .btn-success {
            background: #10b981;
            border: none;
            transition: all 0.2s ease;
        }

        .btn-success:hover {
            background: #059669;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .btn-danger {
            background: #ef4444;
            border: none;
            transition: all 0.2s ease;
        }

        .btn-danger:hover {
            background: #dc2626;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }

        .export-card {
            transition: all 0.25s ease;
        }

        .export-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08) !important;
        }

        .badge.bg-light {
            background: #f1f5f9 !important;
            color: #475569 !important;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .card-body {
                padding: 1.5rem !important;
            }

            .export-card {
                margin-bottom: 0;
            }

            #previewBox .d-flex {
                flex-direction: column;
                text-align: center;
            }

            #previewBox button {
                width: 100%;
                margin-top: 12px;
            }

            .btn-primary,
            .btn-success,
            .btn-danger {
                padding: 0.5rem;
                font-size: 0.85rem;
            }
        }

        @media (max-width: 576px) {
            .export-card {
                margin-bottom: 1rem;
            }

            .export-card:last-child {
                margin-bottom: 0;
            }
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

        .card {
            animation: fadeSlideUp 0.3s ease;
        }

        .export-card {
            animation: fadeSlideUp 0.3s ease backwards;
        }

        .export-card:nth-child(1) {
            animation-delay: 0.02s;
        }

        .export-card:nth-child(2) {
            animation-delay: 0.04s;
        }

        .export-card:nth-child(3) {
            animation-delay: 0.06s;
        }
    </style>

    <script>
        function getFilterParams() {
            const form = document.getElementById('exportForm');
            const data = new FormData(form);
            const params = new URLSearchParams();
            for (const [key, value] of data.entries()) {
                if (value) params.append(key, value);
            }
            return params.toString();
        }

        async function previewData() {
            const previewText = document.getElementById('previewText');
            const previewSub = document.getElementById('previewSub');
            const previewBox = document.getElementById('previewBox');

            previewText.textContent = 'Mengecek data...';
            previewBox.style.background = '#f0f7ff';

            try {
                const params = getFilterParams();
                const res = await fetch(`/admin/export/preview?${params}`);
                const data = await res.json();
                window.lastCheckedTotal = data.total;

                if (data.total > 0) {
                    previewText.innerHTML =
                        `<span class="text-success fw-bold">✓ ${data.total.toLocaleString()} data</span> ditemukan sesuai filter`;
                    if (data.total > 300) {
                        previewSub.innerHTML = '<span class="text-danger fw-semibold">Data melebihi 300 baris.</span> Siap diexport ke Excel/CSV. Untuk PDF, silakan persempit filter.';
                    } else {
                        previewSub.textContent = 'Data siap diexport. Pilih format export di bawah.';
                    }
                    previewBox.style.background = '#f0fdf4';
                } else {
                    previewText.innerHTML = `<span class="text-warning fw-bold">⚠ Tidak ada data</span> sesuai filter`;
                    previewSub.textContent = 'Coba ubah filter untuk mendapatkan data.';
                    previewBox.style.background = '#fef9e7';
                }
            } catch (e) {
                previewText.textContent = 'Gagal mengecek data. Coba lagi.';
                previewBox.style.background = '#fff5f5';
            }
        }

        function doExport(format) {
            if (format === 'pdf' && window.lastCheckedTotal !== undefined && window.lastCheckedTotal > 300) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Data Melebihi Batas PDF',
                        text: `Data yang dipilih (${window.lastCheckedTotal.toLocaleString()} baris) melebihi batas 300 baris untuk PDF guna mencegah kehabisan memori server. Silakan persempit filter (misal pilih Program Studi) atau gunakan format Excel / CSV.`,
                        confirmButtonColor: '#3b82f6',
                    });
                    return;
                }
            }
            const params = getFilterParams();
            window.location.href = `/admin/export/${format}?${params}`;
        }

        function filterPeriodeByTahun(tahun) {
            const periodeSelect = document.querySelector('select[name="periode_id"]');
            const options = periodeSelect.querySelectorAll('option');

            options.forEach(opt => {
                if (!opt.value) return;
                if (!tahun) {
                    opt.style.display = '';
                } else {
                    opt.style.display = opt.textContent.includes(tahun) ? '' : 'none';
                }
            });

            const selected = periodeSelect.options[periodeSelect.selectedIndex];
            if (selected && selected.style.display === 'none') {
                periodeSelect.value = '';
            }
        }
    </script>
@endsection
