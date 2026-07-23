@extends('layouts.app')
@section('title', 'Edit Golongan UKT')
@section('page-title', 'Edit Golongan UKT')

@section('content')
    <div class="container-fluid px-4">
        {{-- Breadcrumb Navigation --}}
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}" class="text-decoration-none">
                        <i class="bi bi-house-door me-1"></i> Dashboard
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.golongan-ukt.index') }}" class="text-decoration-none">
                        <i class="bi bi-tags me-1"></i> Kelola Golongan UKT
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">
                    <i class="bi bi-pencil-square me-1"></i> Edit Golongan
                </li>
            </ol>
        </nav>

        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-7">
                {{-- Warning Alert for Existing Students --}}
                @if ($golonganUkt->hasilUkt->count() > 0)
                    <div class="alert alert-warning border-start border-4 border-warning rounded-3 shadow-sm mb-4">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <i class="bi bi-exclamation-triangle-fill fs-4 text-warning"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="alert-heading fw-bold mb-1">Perhatian!</h6>
                                <p class="mb-0 small">
                                    Golongan ini memiliki <strong>{{ $golonganUkt->hasilUkt->count() }} mahasiswa</strong>
                                    terdaftar.
                                    Perubahan nominal atau range skor akan mempengaruhi data mahasiswa yang terkait.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Main Form Card --}}
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                    {{-- Card Header with Gradient --}}
                    <div class="card-header bg-gradient-warning text-white py-3 border-0">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-white bg-opacity-25 p-2 me-3">
                                <i class="bi bi-pencil-square fs-5"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0">Edit Golongan UKT</h5>
                                <p class="small mb-0 opacity-75">
                                    Mengedit data Golongan {{ $golonganUkt->golongan }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-4 p-lg-5">
                        <form action="{{ route('admin.golongan-ukt.update', $golonganUkt) }}" method="POST"
                            id="formEditGolongan">
                            @csrf
                            @method('PUT')

                            {{-- Golongan Number (Disabled - Read Only) --}}
                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-hash text-primary me-1"></i>
                                    Nomor Golongan
                                </label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-light">
                                        <i class="bi bi-tag-fill"></i>
                                    </span>
                                    <input type="text" class="form-control bg-light"
                                        value="Golongan {{ $golonganUkt->golongan }}" disabled>
                                    <span class="input-group-text bg-light">
                                        <i class="bi bi-lock-fill text-muted"></i>
                                    </span>
                                </div>
                                <div class="form-text">
                                    <i class="bi bi-info-circle-fill text-info small me-1"></i>
                                    Nomor golongan tidak dapat diubah karena telah terintegrasi dengan sistem penilaian.
                                </div>
                            </div>

                            {{-- Nominal UKT --}}
                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-cash-stack text-primary me-1"></i>
                                    Nominal UKT
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-light fw-semibold">Rp</span>
                                    <input type="number" name="nominal"
                                        class="form-control @error('nominal') is-invalid @enderror"
                                        value="{{ old('nominal', $golonganUkt->nominal) }}" min="0" step="50000"
                                        placeholder="0" required>
                                </div>
                                <div class="form-text">
                                    <i class="bi bi-currency-exchange me-1"></i>
                                    Format nominal dalam Rupiah, kelipatan Rp 50.000
                                </div>
                                @error('nominal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Score Range Section --}}
                            <div class="mb-4">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="bi bi-graph-up fs-5 text-primary me-2"></i>
                                    <h6 class="fw-bold mb-0">Range Skor Preferensi</h6>
                                    <div class="ms-2">
                                        <span class="badge bg-info bg-opacity-10 text-info rounded-pill">
                                            Opsional
                                        </span>
                                    </div>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small text-muted">
                                            <i class="bi bi-arrow-down-circle text-success me-1"></i>
                                            Skor Minimum
                                        </label>
                                        <input type="number" name="skor_min"
                                            class="form-control @error('skor_min') is-invalid @enderror"
                                            value="{{ old('skor_min', $golonganUkt->skor_min) }}" min="0"
                                            max="1" step="0.001" placeholder="0.000">
                                        @error('skor_min')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small text-muted">
                                            <i class="bi bi-arrow-up-circle text-danger me-1"></i>
                                            Skor Maksimum
                                        </label>
                                        <input type="number" name="skor_max"
                                            class="form-control @error('skor_max') is-invalid @enderror"
                                            value="{{ old('skor_max', $golonganUkt->skor_max) }}" min="0"
                                            max="1" step="0.001" placeholder="1.000">
                                        @error('skor_max')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-text mt-2">
                                    <i class="bi bi-sliders2 me-1"></i>
                                    Nilai skor antara 0 - 1 (contoh: 0.500). Biarkan kosong jika tidak menggunakan range
                                    otomatis.
                                </div>
                            </div>

                            {{-- Current Values Summary --}}
                            <div class="mb-4 p-3 bg-light rounded-3">
                                <div class="row text-center">
                                    <div class="col-6 border-end">
                                        <span class="text-muted small d-block mb-1">Nominal Saat Ini</span>
                                        <span class="fw-bold text-success fs-5" id="currentNominal">
                                            Rp {{ number_format($golonganUkt->nominal, 0, ',', '.') }}
                                        </span>
                                    </div>
                                    <div class="col-6">
                                        <span class="text-muted small d-block mb-1">Preview Perubahan</span>
                                        <span class="fw-bold text-primary fs-5" id="previewNominal">
                                            Rp {{ number_format($golonganUkt->nominal, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- Info Alert --}}
                            <div class="alert alert-info border-start border-4 border-primary rounded-3 shadow-sm mb-4">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <i class="bi bi-info-circle-fill fs-5 text-primary"></i>
                                    </div>
                                    <div class="ms-3">
                                        <h6 class="alert-heading fw-bold mb-1">Informasi Range Skor</h6>
                                        <p class="mb-0 small">
                                            Skor minimum dan maksimum digunakan untuk menentukan golongan UKT secara
                                            otomatis dari hasil perhitungan SAW.
                                            <br>
                                            <strong class="text-primary">Catatan:</strong> Skor tinggi = kurang mampu
                                            ekonomi = golongan lebih rendah (Golongan 1 tertinggi).
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {{-- Action Buttons --}}
                            <div class="d-flex flex-column flex-sm-row gap-2 justify-content-end mt-4 pt-3 border-top">
                                <a href="{{ route('admin.golongan-ukt.index') }}"
                                    class="btn btn-outline-secondary rounded-pill px-4 order-2 order-sm-1">
                                    <i class="bi bi-arrow-left me-1"></i> Batal
                                </a>
                                <button type="submit"
                                    class="btn btn-warning rounded-pill px-5 order-1 order-sm-2 shadow-sm">
                                    <i class="bi bi-save me-2"></i> Update Golongan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Additional Info Card --}}
                <div class="card border-0 shadow-sm rounded-3 mt-4 bg-gradient-light">
                    <div class="card-body p-3">
                        <div class="row align-items-center text-center text-md-start">
                            <div class="col-md-4 mb-2 mb-md-0">
                                <i class="bi bi-clock-history text-warning me-2"></i>
                                <span class="small">Terakhir update:
                                    {{ $golonganUkt->updated_at->diffForHumans() ?? '-' }}</span>
                            </div>
                            <div class="col-md-4 mb-2 mb-md-0">
                                <i class="bi bi-calendar-check text-primary me-2"></i>
                                <span class="small">Dibuat: {{ $golonganUkt->created_at->format('d/m/Y') ?? '-' }}</span>
                            </div>
                            <div class="col-md-4">
                                <i class="bi bi-shield-check-fill text-success me-2"></i>
                                <span class="small">Perubahan akan tersimpan dengan aman</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Custom Styles */
        .bg-gradient-warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .bg-gradient-light {
            background: linear-gradient(135deg, #f5f7fa 0%, #e8ecf1 100%);
        }

        .card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 1rem 2rem rgba(0, 0, 0, 0.1) !important;
        }

        .form-control,
        .input-group-text {
            transition: all 0.2s ease;
        }

        .form-control:focus,
        .input-group:focus-within {
            transform: scale(1.01);
            box-shadow: 0 0 0 0.2rem rgba(245, 87, 108, 0.25);
        }

        .btn {
            transition: all 0.2s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .btn:active {
            transform: translateY(0);
        }

        /* Disabled input styling */
        input:disabled {
            cursor: not-allowed;
            background-color: #e9ecef;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .card-body {
                padding: 1.5rem !important;
            }

            .btn {
                width: 100%;
                margin-bottom: 0.5rem;
            }

            .d-flex.gap-2 {
                gap: 0.5rem !important;
            }
        }

        /* Number input styling */
        input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button {
            opacity: 0.5;
        }

        /* Animation for preview */
        #previewNominal {
            transition: all 0.3s ease;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }

            100% {
                transform: scale(1);
            }
        }

        .preview-updated {
            animation: pulse 0.3s ease;
        }
    </style>

    <script>
        // Live preview for nominal format
        document.addEventListener('DOMContentLoaded', function() {
            const nominalInput = document.querySelector('input[name="nominal"]');
            const previewSpan = document.getElementById('previewNominal');
            const currentSpan = document.getElementById('currentNominal');

            function formatRupiah(angka) {
                let number_string = angka.toString().replace(/[^,\d]/g, '');
                let split = number_string.split(',');
                let sisa = split[0].length % 3;
                let rupiah = split[0].substr(0, sisa);
                let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

                if (ribuan) {
                    let separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }

                rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
                return rupiah ? 'Rp ' + rupiah : 'Rp 0';
            }

            function updatePreview() {
                let value = parseInt(nominalInput.value) || 0;
                let formatted = formatRupiah(value);
                previewSpan.textContent = formatted;

                // Add animation effect
                previewSpan.classList.add('preview-updated');
                setTimeout(() => {
                    previewSpan.classList.remove('preview-updated');
                }, 300);
            }

            if (nominalInput) {
                nominalInput.addEventListener('input', updatePreview);
            }

            // Validation for min < max on score fields
            const skorMin = document.querySelector('input[name="skor_min"]');
            const skorMax = document.querySelector('input[name="skor_max"]');

            function validateScoreRange() {
                // Clear previous custom validity
                if (skorMax) skorMax.setCustomValidity('');

                if (skorMin && skorMax && skorMin.value && skorMax.value) {
                    const min = parseFloat(skorMin.value);
                    const max = parseFloat(skorMax.value);

                    if (min >= max) {
                        skorMax.setCustomValidity('Skor maksimum harus lebih besar dari skor minimum');
                        if (skorMax.classList) skorMax.classList.add('is-invalid');
                    } else {
                        if (skorMax.classList) skorMax.classList.remove('is-invalid');
                    }
                }
            }

            if (skorMin && skorMax) {
                skorMin.addEventListener('input', validateScoreRange);
                skorMax.addEventListener('input', validateScoreRange);
                // Initial validation
                validateScoreRange();
            }

            // Form submission validation
            const form = document.getElementById('formEditGolongan');
            if (form) {
                form.addEventListener('submit', function(e) {
                    validateScoreRange();
                    if (skorMax && skorMax.getCustomValidity()) {
                        e.preventDefault();
                        alert('Skor maksimum harus lebih besar dari skor minimum');
                    }

                    // Confirmation for significant changes
                    const originalNominal = {{ $golonganUkt->nominal }};
                    const newNominal = parseInt(nominalInput.value) || 0;

                    if (Math.abs(newNominal - originalNominal) > 1000000) {
                        e.preventDefault();
                        swalConfirm('Perubahan nominal melebihi Rp 1.000.000. Lanjutkan?').then(function(
                        ok) {
                            if (ok) e.target.submit();
                        });
                    }
                });
            }
        });
    </script>
@endsection
