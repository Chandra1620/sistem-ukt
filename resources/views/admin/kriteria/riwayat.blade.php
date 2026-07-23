@extends('layouts.app')
@section('title', 'Riwayat Kriteria per Periode')
@section('page-title', 'Riwayat Kriteria & Bobot per Periode')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h5 class="fw-bold mb-1 d-flex align-items-center gap-2">
                <span class="bg-primary bg-opacity-10 p-2 rounded-3">
                    <i class="bi bi-clock-history text-primary"></i>
                </span>
                Riwayat Kriteria & Bobot
            </h5>
            <p class="text-muted small mb-0">
                <i class="bi bi-info-circle me-1"></i>
                Ringkasan konfigurasi kriteria SAW dari setiap periode seleksi
            </p>
        </div>
        <a href="{{ route('admin.kriteria.index') }}" class="btn btn-outline-secondary rounded-pill px-4 py-2">
            <i class="bi bi-arrow-left me-2"></i> Kembali
        </a>
    </div>

    <!-- Statistik Ringkas -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-3 me-3">
                        <i class="bi bi-calendar3 text-primary fs-5"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block">Total Periode</small>
                        <h5 class="fw-bold mb-0">{{ $riwayat->count() }}</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="bg-success bg-opacity-10 p-3 rounded-3 me-3">
                        <i class="bi bi-check-circle text-success fs-5"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block">Aktif</small>
                        <h5 class="fw-bold mb-0">{{ $riwayat->where('status', 'aktif')->count() }}</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="bg-warning bg-opacity-10 p-3 rounded-3 me-3">
                        <i class="bi bi-hourglass-split text-warning fs-5"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block">Nonaktif</small>
                        <h5 class="fw-bold mb-0">{{ $riwayat->where('status', 'nonaktif')->count() }}</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="bg-secondary bg-opacity-10 p-3 rounded-3 me-3">
                        <i class="bi bi-flag text-secondary fs-5"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block">Selesai</small>
                        <h5 class="fw-bold mb-0">{{ $riwayat->where('status', 'selesai')->count() }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <!-- Card Header dengan Search & Filter -->
        <div class="card-header bg-transparent border-0 px-4 py-3">
            <div class="row g-3 align-items-center">
                <div class="col-md-6">
                    <h6 class="fw-semibold mb-0">
                        <i class="bi bi-list-ul me-2 text-primary"></i>
                        Daftar Periode
                    </h6>
                </div>
                <div class="col-md-6">
                    <div class="d-flex gap-2 justify-content-md-end">
                        <div class="position-relative flex-grow-1 flex-md-grow-0">
                            <i
                                class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted small"></i>
                            <input type="text" id="searchPeriode" class="form-control form-control-sm rounded-pill ps-5"
                                placeholder="Cari periode..." style="max-width: 250px;">
                        </div>
                        <button class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="window.location.reload()">
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="periodeTable">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4 py-3" style="min-width: 150px;">
                                <i class="bi bi-tag me-1"></i> Periode
                            </th>
                            <th style="min-width: 100px;">Tahun</th>
                            <th style="min-width: 100px;">Status</th>
                            <th class="text-center" style="min-width: 80px;">Kriteria</th>
                            <th class="text-center" style="min-width: 80px;">Sub</th>
                            <th class="text-center" style="min-width: 100px;">Total Bobot</th>
                            <th style="min-width: 110px;">Dibuat</th>
                            <th style="min-width: 110px;">Diperbarui</th>
                            <th class="text-center" style="min-width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($riwayat as $r)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="bg-primary bg-opacity-10 p-2 rounded-3 d-none d-sm-block">
                                            <i class="bi bi-folder text-primary"></i>
                                        </div>
                                        <div>
                                            <span class="fw-semibold d-block">{{ $r['nama'] }}</span>
                                            <small class="text-muted d-block d-sm-none">Tahun: {{ $r['tahun'] }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark fw-normal">
                                        <i class="bi bi-calendar me-1"></i>
                                        {{ $r['tahun'] }}
                                    </span>
                                </td>
                                <td>
                                    @if ($r['status'] === 'aktif')
                                        <span class="badge bg-success rounded-pill px-3 py-2">
                                            <i class="bi bi-check-circle-fill me-1 small"></i> Aktif
                                        </span>
                                    @elseif($r['status'] === 'selesai')
                                        <span class="badge bg-secondary rounded-pill px-3 py-2">
                                            <i class="bi bi-check-circle me-1 small"></i> Selesai
                                        </span>
                                    @else
                                        <span class="badge bg-warning text-dark rounded-pill px-3 py-2">
                                            <i class="bi bi-pause-circle me-1 small"></i> Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary bg-opacity-10 text-primary fw-semibold px-3 py-2">
                                        <i class="bi bi-list-check me-1"></i>
                                        {{ $r['jumlah_kriteria'] }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info bg-opacity-10 text-info fw-semibold px-3 py-2">
                                        <i class="bi bi-grid me-1"></i>
                                        {{ $r['jumlah_sub'] }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @php $bobot = (float) $r['total_bobot']; @endphp
                                    @if ($bobot == 100)
                                        <span class="badge bg-success rounded-pill px-3 py-2">
                                            <i class="bi bi-check-circle-fill me-1"></i>
                                            {{ number_format($bobot, 0) }}%
                                        </span>
                                    @elseif($bobot > 100)
                                        <span class="badge bg-danger rounded-pill px-3 py-2">
                                            <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                            {{ number_format($bobot, 0) }}%
                                        </span>
                                    @else
                                        <span class="badge bg-warning text-dark rounded-pill px-3 py-2">
                                            <i class="bi bi-exclamation-circle me-1"></i>
                                            {{ number_format($bobot, 0) }}%
                                        </span>
                                    @endif
                                </td>
                                <td class="small text-muted">
                                    <i class="bi bi-calendar-plus me-1"></i>
                                    {{ $r['created_at'] ? \Carbon\Carbon::parse($r['created_at'])->format('d/m/Y') : '-' }}
                                </td>
                                <td class="small text-muted">
                                    <i class="bi bi-calendar-check me-1"></i>
                                    {{ $r['updated_at'] ? \Carbon\Carbon::parse($r['updated_at'])->format('d/m/Y') : '-' }}
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.kriteria.index', ['periode_id' => $r['id']]) }}"
                                        class="btn btn-sm btn-primary rounded-pill px-3 py-2 transition-hover">
                                        <i class="bi bi-eye me-1"></i>
                                        <span class="d-none d-sm-inline">Detail</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <div class="py-4">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                                            style="width: 64px; height: 64px;">
                                            <i class="bi bi-inbox fs-1 text-muted"></i>
                                        </div>
                                        <h6 class="fw-semibold mb-1">Belum Ada Data</h6>
                                        <p class="text-muted small mb-0">Belum ada data riwayat periode seleksi</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Card Footer dengan Total Data -->
        @if ($riwayat->count() > 0)
            <div class="card-footer bg-transparent border-0 px-4 py-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <small class="text-muted">
                        <i class="bi bi-database me-1"></i>
                        Menampilkan <strong>{{ $riwayat->count() }}</strong> periode
                    </small>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-success rounded-pill px-4 py-2" onclick="exportPDF()"
                            id="exportPdfBtn">
                            <i class="bi bi-filetype-pdf me-2"></i> Export PDF
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Alert Informasi -->
    <div class="alert alert-info border-0 rounded-4 mt-4 p-4 shadow-sm">
        <div class="d-flex gap-3 align-items-start">
            <div class="bg-white bg-opacity-25 p-2 rounded-3">
                <i class="bi bi-info-circle fs-5"></i>
            </div>
            <div class="small">
                <strong class="d-block mb-1">Info Total Bobot</strong>
                <p class="mb-0 text-muted">
                    Total bobot idealnya = <strong class="text-success">100%</strong>.
                    Jika tidak mencapai 100%, hasil perhitungan SAW tidak akan mencapai skala penuh (0–100).
                    Klik <strong>Detail</strong> untuk mengelola kriteria pada periode tertentu.
                </p>
            </div>
        </div>
    </div>

    <style>
        .transition-hover {
            transition: all 0.3s ease;
        }

        .transition-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.25);
        }

        .table tbody tr {
            transition: background-color 0.2s ease;
        }

        .table tbody tr:hover {
            background-color: rgba(13, 110, 253, 0.04);
        }

        .badge {
            font-weight: 500;
        }

        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
        }

        @media (max-width: 768px) {

            .table td,
            .table th {
                font-size: 0.85rem;
            }

            .btn {
                font-size: 0.8rem;
                padding: 0.35rem 0.7rem;
            }
        }

        @media (max-width: 576px) {
            .table td .d-block {
                font-size: 0.9rem;
            }

            .badge {
                font-size: 0.7rem;
                padding: 0.3rem 0.6rem;
            }
        }

        /* Loading overlay untuk PDF */
        #pdfLoadingOverlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }

        #pdfLoadingOverlay .loading-box {
            background: white;
            padding: 40px;
            border-radius: 16px;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        #pdfLoadingOverlay .spinner {
            width: 50px;
            height: 50px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #0d6efd;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>

    <!-- Loading Overlay -->
    <div id="pdfLoadingOverlay">
        <div class="loading-box">
            <div class="spinner"></div>
            <h6 class="fw-bold mb-2">Sedang Membuat PDF...</h6>
            <p class="text-muted small mb-0">Mohon tunggu sebentar</p>
        </div>
    </div>

    <!-- Script untuk Export PDF langsung download -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <script>
        // Live search functionality
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchPeriode');
            if (searchInput) {
                searchInput.addEventListener('keyup', function() {
                    const searchText = this.value.toLowerCase();
                    const rows = document.querySelectorAll('#periodeTable tbody tr');

                    rows.forEach(row => {
                        const text = row.textContent.toLowerCase();
                        row.style.display = text.includes(searchText) ? '' : 'none';
                    });
                });
            }
        });

        // Export PDF function - langsung download
        function exportPDF() {
            // Show loading overlay
            const overlay = document.getElementById('pdfLoadingOverlay');
            overlay.style.display = 'flex';

            // Get the button and change its state
            const btn = document.getElementById('exportPdfBtn');
            const originalHtml = btn.innerHTML;
            btn.innerHTML =
                '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Memproses...';
            btn.disabled = true;

            // Get the table
            const table = document.getElementById('periodeTable');

            // Create a clone of the table for PDF
            const cloneTable = table.cloneNode(true);

            // Remove action column (last column) from clone
            const rows = cloneTable.querySelectorAll('tr');
            rows.forEach(row => {
                const cells = row.querySelectorAll('th, td');
                if (cells.length > 0) {
                    // Remove last cell (action column)
                    cells[cells.length - 1].remove();
                }
            });

            // Get statistics
            const totalPeriode = {{ $riwayat->count() }};
            const aktif = {{ $riwayat->where('status', 'aktif')->count() }};
            const nonaktif = {{ $riwayat->where('status', 'nonaktif')->count() }};
            const selesai = {{ $riwayat->where('status', 'selesai')->count() }};

            // Create PDF content with full styling
            const element = document.createElement('div');
            element.innerHTML = `
                <div style="padding: 30px; font-family: 'Segoe UI', Arial, sans-serif; background: white;">
                    <!-- Header -->
                    <div style="text-align: center; margin-bottom: 30px; border-bottom: 3px solid #0d6efd; padding-bottom: 20px;">
                        <h1 style="color: #0d6efd; font-size: 28px; margin-bottom: 5px; font-weight: 700;">
                            📊 Riwayat Kriteria & Bobot
                        </h1>
                        <p style="color: #6c757d; font-size: 14px; margin: 5px 0;">
                            Ringkasan konfigurasi kriteria SAW dari setiap periode seleksi
                        </p>
                        <p style="color: #adb5bd; font-size: 12px; margin-top: 5px;">
                            Dicetak: ${new Date().toLocaleString('id-ID', { 
                                day: '2-digit', 
                                month: 'long', 
                                year: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit',
                                second: '2-digit'
                            })}
                        </p>
                    </div>

                    <!-- Summary Statistics -->
                    <div style="display: flex; gap: 30px; margin-bottom: 25px; padding: 15px 20px; background: #f8f9fa; border-radius: 8px; flex-wrap: wrap;">
                        <div>
                            <div style="font-size: 11px; color: #6c757d;">Total Periode</div>
                            <div style="font-size: 20px; font-weight: 700; color: #0d6efd;">${totalPeriode}</div>
                        </div>
                        <div>
                            <div style="font-size: 11px; color: #6c757d;">Aktif</div>
                            <div style="font-size: 20px; font-weight: 700; color: #28a745;">${aktif}</div>
                        </div>
                        <div>
                            <div style="font-size: 11px; color: #6c757d;">Nonaktif</div>
                            <div style="font-size: 20px; font-weight: 700; color: #ffc107;">${nonaktif}</div>
                        </div>
                        <div>
                            <div style="font-size: 11px; color: #6c757d;">Selesai</div>
                            <div style="font-size: 20px; font-weight: 700; color: #6c757d;">${selesai}</div>
                        </div>
                    </div>

                    <!-- Table -->
                    ${cloneTable.outerHTML}

                    <!-- Footer -->
                    <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #dee2e6; font-size: 11px; color: #6c757d;">
                        <div>© ${new Date().getFullYear()} - Sistem Pendukung Keputusan SAW</div>
                        <div style="margin-top: 3px;">Dicetak pada: ${new Date().toLocaleString('id-ID', { 
                            day: '2-digit', 
                            month: 'long', 
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        })}</div>
                    </div>
                </div>
            `;

            // Configure PDF options
            const opt = {
                margin: [10, 10, 10, 10],
                filename: 'riwayat_periode_' + new Date().toISOString().split('T')[0] + '.pdf',
                image: {
                    type: 'jpeg',
                    quality: 0.98
                },
                html2canvas: {
                    scale: 2,
                    useCORS: true,
                    letterRendering: true,
                },
                jsPDF: {
                    unit: 'mm',
                    format: 'a4',
                    orientation: 'landscape'
                },
                pagebreak: {
                    mode: ['avoid-all', 'css', 'legacy']
                }
            };

            // Generate PDF
            html2pdf()
                .set(opt)
                .from(element)
                .save()
                .then(() => {
                    // Hide loading overlay
                    overlay.style.display = 'none';
                    // Reset button
                    btn.innerHTML = originalHtml;
                    btn.disabled = false;
                })
                .catch((error) => {
                    console.error('Error generating PDF:', error);
                    overlay.style.display = 'none';
                    btn.innerHTML = originalHtml;
                    btn.disabled = false;
                    alert('Gagal membuat PDF. Silakan coba lagi.');
                });
        }
    </script>
@endsection
