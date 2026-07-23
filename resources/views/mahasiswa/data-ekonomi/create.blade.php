@extends('layouts.app')
@section('title', 'Isi Data Ekonomi')
@section('page-title', 'Isi Data Ekonomi')

@section('content')
    <div class="container-fluid px-4 py-4">
        <div class="row">
            <div class="col-12">

                {{-- Breadcrumb --}}
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb bg-transparent p-0 mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('mahasiswa.dashboard') }}" class="text-decoration-none text-muted">
                                <i class="bi bi-house-door me-1"></i> Dashboard
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('mahasiswa.pendaftaran.index') }}" class="text-decoration-none text-muted">
                                <i class="bi bi-file-text me-1"></i> Pendaftaran
                            </a>
                        </li>
                        <li class="breadcrumb-item active text-primary">
                            <i class="bi bi-cash-stack me-1"></i> Data Ekonomi
                        </li>
                    </ol>
                </nav>

                {{-- Main Card --}}
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <div>
                                <h5 class="fw-bold mb-1">
                                    <i class="bi bi-cash-stack text-success me-2"></i>
                                    Isi Data Ekonomi
                                </h5>
                                <p class="text-muted small mb-0">Lengkapi data ekonomi keluarga Anda</p>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-light text-dark rounded-pill px-3 py-2">
                                    <i class="bi bi-file-text me-1"></i> {{ $pendaftaran->no_pendaftaran }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-4 p-lg-5">

                        {{-- Error Alert --}}
                        @if ($errors->any())
                            <div class="alert alert-danger border-0 rounded-3 mb-4">
                                <div class="d-flex">
                                    <i class="bi bi-exclamation-circle-fill me-2"></i>
                                    <div>
                                        <strong>Data tidak lengkap</strong>
                                        <ul class="mb-0 mt-1 ps-3">
                                            @foreach ($errors->all() as $error)
                                                <li class="small">{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Progress Steps --}}
                        <div class="progress-steps mb-5">
                            <div class="step-item completed">
                                <div class="step-circle">
                                    <i class="bi bi-check-lg"></i>
                                </div>
                                <div class="step-text">Data Diri</div>
                            </div>
                            <div class="step-line completed"></div>
                            <div class="step-item active">
                                <div class="step-circle">2</div>
                                <div class="step-text">Data Ekonomi</div>
                            </div>
                            <div class="step-line"></div>
                            <div class="step-item">
                                <div class="step-circle">3</div>
                                <div class="step-text">Upload Dokumen</div>
                            </div>
                        </div>

                        <form action="{{ route('mahasiswa.data-ekonomi.store', $pendaftaran) }}" method="POST"
                            id="formDataEkonomi">
                            @csrf

                            {{-- Informasi Singkat --}}
                            <div class="info-alert-premium mb-4">
                                <div class="info-alert-icon">
                                    <i class="bi bi-info-circle-fill"></i>
                                </div>
                                <div class="info-alert-content">
                                    <div class="info-alert-title">Informasi Data Ekonomi</div>
                                    <div class="info-alert-text">
                                        Pilih opsi yang sesuai dengan kondisi ekonomi keluarga Anda.
                                        Data ini akan digunakan untuk menentukan nominal UKT.
                                    </div>
                                </div>
                            </div>

                            {{-- Form Fields dalam Grid 2 Kolom --}}
                            <div class="row g-4">
                                @foreach ($kriteria as $kode => $k)
                                    @if ($k->subKriteria->count() === 0)
                                        @continue
                                    @endif

                                    @php
                                        $icons = [
                                            'C1' => 'bi-wallet2',
                                            'C2' => 'bi-wallet',
                                            'C3' => 'bi-people',
                                            'C4' => 'bi-lightbulb',
                                            'C5' => 'bi-briefcase',
                                            'C6' => 'bi-person-badge',
                                            'C7' => 'bi-house',
                                            'C8' => 'bi-car-front',
                                            'C9' => 'bi-map',
                                            'C10' => 'bi-receipt',
                                            'C11' => 'bi-credit-card',
                                            'C12' => 'bi-droplet',
                                        ];
                                        $icon = $icons[$kode] ?? 'bi-clipboard';
                                        $field = 'sub_' . strtolower($kode) . '_id';
                                        $oldVal = old($field, $dataEkonomi->$field ?? '');
                                    @endphp

                                    <div class="col-md-6">
                                        <div class="form-group-modern mb-3">
                                            <label class="form-label-modern">
                                                <i class="bi {{ $icon }} text-primary me-1"></i>
                                                {{ $k->nama }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group-modern">
                                                <select name="{{ $field }}"
                                                    class="form-control-modern select-kriteria @error($field) is-invalid @enderror"
                                                    required>
                                                    <option value="">-- Pilih {{ $k->nama }} --</option>
                                                    @foreach ($k->subKriteria as $sub)
                                                        <option value="{{ $sub->id }}"
                                                            data-bobot="{{ $sub->bobot ?? 0 }}"
                                                            {{ $oldVal == $sub->id ? 'selected' : '' }}>
                                                            {{ $sub->label }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div class="input-focus-border"></div>
                                            </div>
                                            @error($field)
                                                <div class="invalid-feedback-modern">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Info Penting --}}
                            <div class="info-alert-premium info-alert-warning mt-4">
                                <div class="info-alert-icon">
                                    <i class="bi bi-shield-check"></i>
                                </div>
                                <div class="info-alert-content">
                                    <div class="info-alert-title">Verifikasi Data</div>
                                    <div class="info-alert-text">
                                        Data Anda akan diverifikasi oleh reviewer. Pastikan data yang diisi sesuai dengan
                                        dokumen pendukung yang akan diupload pada tahap selanjutnya.
                                    </div>
                                </div>
                            </div>

                            {{-- Buttons --}}
                            <div class="action-buttons-group mt-4 pt-2">
                                <a href="{{ route('mahasiswa.pendaftaran.show', $pendaftaran) }}"
                                    class="btn-cancel-premium">
                                    <i class="bi bi-arrow-left me-1"></i> Kembali
                                </a>
                                <button type="submit" class="btn-submit-premium">
                                    <i class="bi bi-save me-1"></i> Simpan & Lanjutkan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Info Tambahan --}}
                <div class="text-center mt-3">
                    <small class="text-muted">
                        <i class="bi bi-lock-fill me-1"></i> Data aman dan terenkripsi
                    </small>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Container Full Width */
        .container-fluid {
            width: 100%;
            padding-left: 1.5rem;
            padding-right: 1.5rem;
        }

        /* Progress Steps */
        .progress-steps {
            display: flex;
            align-items: center;
            justify-content: center;
            max-width: 500px;
            margin: 0 auto;
        }

        .step-item {
            text-align: center;
            flex: 1;
        }

        .step-circle {
            width: 40px;
            height: 40px;
            background: #f1f5f9;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 8px;
            font-weight: 700;
            color: #94a3b8;
            transition: all 0.3s;
            font-size: 14px;
        }

        .step-item.active .step-circle {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .step-item.completed .step-circle {
            background: #10b981;
            color: white;
        }

        .step-text {
            font-size: 0.7rem;
            font-weight: 600;
            color: #94a3b8;
        }

        .step-item.active .step-text {
            color: #059669;
        }

        .step-item.completed .step-text {
            color: #10b981;
        }

        .step-line {
            width: 60px;
            height: 2px;
            background: #e2e8f0;
            flex-shrink: 0;
        }

        .step-line.completed {
            background: #10b981;
        }

        /* Form Groups */
        .form-group-modern {
            position: relative;
        }

        .form-label-modern {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.8rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 8px;
        }

        .form-label-modern i {
            color: #059669;
            font-size: 0.9rem;
        }

        .input-group-modern {
            position: relative;
            display: flex;
            align-items: stretch;
            width: 100%;
        }

        .form-control-modern {
            width: 100%;
            padding: 12px 16px;
            font-size: 0.9rem;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            transition: all 0.2s ease;
            background: white;
            height: 48px;
            appearance: auto;
        }

        .form-control-modern:focus {
            outline: none;
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }

        .form-control-modern.is-invalid {
            border-color: #ef4444;
        }

        .form-control-modern.is-invalid:focus {
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        }

        .input-focus-border {
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: #10b981;
            transition: all 0.3s ease;
        }

        .form-control-modern:focus~.input-focus-border {
            width: 100%;
            left: 0;
        }

        .invalid-feedback-modern {
            font-size: 0.7rem;
            color: #ef4444;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* Info Alert Premium */
        .info-alert-premium {
            background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
            border-radius: 16px;
            padding: 16px 20px;
            display: flex;
            align-items: flex-start;
            gap: 14px;
        }

        .info-alert-premium.info-alert-warning {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        }

        .info-alert-premium.info-alert-warning .info-alert-icon {
            color: #d97706;
        }

        .info-alert-premium.info-alert-warning .info-alert-title {
            color: #92400e;
        }

        .info-alert-icon {
            width: 42px;
            height: 42px;
            background: white;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #3b82f6;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .info-alert-content {
            flex: 1;
        }

        .info-alert-title {
            font-weight: 700;
            font-size: 0.85rem;
            color: #1e40af;
            margin-bottom: 4px;
        }

        .info-alert-text {
            font-size: 0.75rem;
            color: #475569;
        }

        /* Action Buttons */
        .action-buttons-group {
            display: flex;
            gap: 12px;
        }

        .btn-submit-premium {
            flex: 1;
            background: linear-gradient(135deg, #10b981, #059669);
            border: none;
            padding: 12px 24px;
            border-radius: 40px;
            color: white;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .btn-submit-premium:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.4);
            color: white;
        }

        .btn-cancel-premium {
            flex: 1;
            background: #f1f5f9;
            border: none;
            padding: 12px 24px;
            border-radius: 40px;
            color: #475569;
            font-weight: 600;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .btn-cancel-premium:hover {
            background: #e2e8f0;
            transform: translateY(-2px);
            color: #1e293b;
        }

        /* Card */
        .card {
            border-radius: 20px;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .container-fluid {
                padding-left: 1.25rem !important;
                padding-right: 1.25rem !important;
            }

            .card-body {
                padding: 1.5rem !important;
            }
        }

        @media (max-width: 768px) {
            .container-fluid {
                padding-left: 1rem !important;
                padding-right: 1rem !important;
            }

            .progress-steps {
                display: none;
            }

            .action-buttons-group {
                flex-direction: column;
            }

            .action-buttons-group .btn {
                width: 100%;
            }

            .card-body {
                padding: 1.25rem !important;
            }

            .info-alert-premium {
                flex-direction: column;
                text-align: center;
            }

            .info-alert-icon {
                margin: 0 auto;
            }

            .card-header .d-flex {
                flex-direction: column;
                text-align: center;
            }

            .form-control-modern {
                height: 44px;
                font-size: 0.85rem;
            }
        }

        @media (max-width: 576px) {
            .container-fluid {
                padding-left: 0.75rem !important;
                padding-right: 0.75rem !important;
            }

            .card-body {
                padding: 1rem !important;
            }

            .form-control-modern {
                padding: 10px 12px;
                font-size: 0.8rem;
                height: 42px;
            }

            .form-label-modern {
                font-size: 0.75rem;
            }

            .row.g-4 {
                --bs-gutter-y: 0.75rem;
                --bs-gutter-x: 0.75rem;
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

        .card {
            animation: fadeInUp 0.4s ease;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('formDataEkonomi');
            const selects = document.querySelectorAll('.select-kriteria');

            // Real-time validation
            selects.forEach(select => {
                select.addEventListener('change', function() {
                    if (this.value) {
                        this.classList.remove('is-invalid');
                    }
                });
            });

            // Form submission
            if (form) {
                form.addEventListener('submit', function(e) {
                    let allFilled = true;
                    let firstEmpty = null;

                    selects.forEach(select => {
                        if (!select.value) {
                            allFilled = false;
                            select.classList.add('is-invalid');
                            if (!firstEmpty) {
                                firstEmpty = select;
                            }
                        } else {
                            select.classList.remove('is-invalid');
                        }
                    });

                    if (!allFilled) {
                        e.preventDefault();
                        // Scroll to first empty field
                        if (firstEmpty) {
                            firstEmpty.focus();
                            firstEmpty.scrollIntoView({
                                behavior: 'smooth',
                                block: 'center'
                            });
                        }

                        // Show sweet alert or custom alert
                        const alertDiv = document.createElement('div');
                        alertDiv.className = 'alert alert-danger border-0 rounded-3 mb-4';
                        alertDiv.innerHTML = `
                            <div class="d-flex">
                                <i class="bi bi-exclamation-circle-fill me-2"></i>
                                <div>
                                    <strong>Data tidak lengkap!</strong>
                                    <p class="mb-0 small">Harap lengkapi semua data ekonomi sebelum melanjutkan.</p>
                                </div>
                            </div>
                        `;

                        const formBody = document.querySelector('.card-body');
                        const existingAlert = formBody.querySelector('.alert-danger');
                        if (existingAlert) {
                            existingAlert.remove();
                        }
                        formBody.insertBefore(alertDiv, formBody.firstChild);

                        return false;
                    }
                });
            }
        });
    </script>
@endsection
