@extends('layouts.app')
@section('title', 'Tambah Sub Kriteria')
@section('page-title', 'Tambah Sub Kriteria')

@section('content')
    <div class="container-fluid px-4">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-7">
                <!-- Header Section -->
                <div class="mb-4">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-2">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.sub-kriteria.index') }}" class="text-decoration-none">
                                    <i class="bi bi-table me-1"></i>Sub Kriteria
                                </a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Tambah Baru</li>
                        </ol>
                    </nav>
                    <h4 class="fw-bold mb-1">Tambah Sub Kriteria</h4>
                    <p class="text-muted small">Isi form berikut untuk menambahkan sub kriteria baru</p>
                </div>

                <!-- Form Card -->
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                    <!-- Card Header Gradient -->
                    <div class="card-header bg-gradient-primary text-white py-3 px-4 border-0">
                        <div class="d-flex align-items-center gap-2">
                            <div class="bg-white bg-opacity-25 rounded-circle p-2">
                                <i class="bi bi-diagram-3 fs-5"></i>
                            </div>
                            <div>
                                <h6 class="fw-semibold mb-0 text-white">Form Tambah Sub Kriteria</h6>
                                <small class="text-white-50">Lengkapi data di bawah ini dengan benar</small>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-4 p-lg-5">
                        <form action="{{ route('admin.sub-kriteria.store') }}" method="POST" id="subKriteriaForm">
                            @csrf

                            <!-- Kriteria Select -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold mb-2">
                                    <i class="bi bi-bookmark-check text-primary me-1"></i>
                                    Kriteria <span class="text-danger">*</span>
                                </label>
                                <select name="kriteria_id"
                                    class="form-select form-select-lg @error('kriteria_id') is-invalid @enderror"
                                    style="border-radius: 12px; padding: 12px 16px;">
                                    <option value="">📌 -- Pilih Kriteria --</option>
                                    @foreach ($kriteria as $k)
                                        <option value="{{ $k->id }}"
                                            {{ old('kriteria_id', request('kriteria_id')) == $k->id ? 'selected' : '' }}
                                            data-tipe="{{ $k->tipe }}" data-bobot="{{ $k->bobot }}">
                                            <span class="fw-semibold">{{ $k->kode }}</span> - {{ $k->nama }}
                                            <span class="text-muted small">({{ ucfirst($k->tipe) }} | Bobot:
                                                {{ $k->bobot }}%)</span>
                                        </option>
                                    @endforeach
                                </select>
                                @error('kriteria_id')
                                    <div class="invalid-feedback d-block mt-1">
                                        <i class="bi bi-exclamation-triangle-fill me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                                <div class="form-text mt-2">
                                    <i class="bi bi-info-circle text-info me-1"></i>
                                    Pilih kriteria yang akan ditambahkan sub kriterianya
                                </div>
                            </div>

                            <!-- Label -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold mb-2">
                                    <i class="bi bi-tag text-primary me-1"></i>
                                    Label <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="label"
                                    class="form-control form-control-lg @error('label') is-invalid @enderror"
                                    style="border-radius: 12px; padding: 12px 16px;" value="{{ old('label') }}"
                                    placeholder="Contoh: Kurang dari 1 Juta">
                                @error('label')
                                    <div class="invalid-feedback d-block mt-1">
                                        <i class="bi bi-exclamation-triangle-fill me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                                <div class="form-text mt-2">
                                    <i class="bi bi-lightbulb text-warning me-1"></i>
                                    Beri label yang deskriptif dan mudah dipahami
                                </div>
                            </div>

                            <!-- Range Inputs -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold mb-3">
                                    <i class="bi bi-sliders2 text-primary me-1"></i>
                                    Range Nilai
                                </label>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="input-group" style="border-radius: 12px; overflow: hidden;">
                                            <span class="input-group-text bg-light border-0">
                                                <i class="bi bi-arrow-right text-primary"></i>
                                            </span>
                                            <input type="text" name="range_min"
                                                class="form-control @error('range_min') is-invalid @enderror"
                                                style="padding: 12px 16px;" value="{{ old('range_min') }}"
                                                placeholder="Minimal">
                                        </div>
                                        @error('range_min')
                                            <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group" style="border-radius: 12px; overflow: hidden;">
                                            <span class="input-group-text bg-light border-0">
                                                <i class="bi bi-arrow-left text-primary"></i>
                                            </span>
                                            <input type="text" name="range_max"
                                                class="form-control @error('range_max') is-invalid @enderror"
                                                style="padding: 12px 16px;" value="{{ old('range_max') }}"
                                                placeholder="Maksimal">
                                        </div>
                                        @error('range_max')
                                            <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-text mt-2">
                                    <i class="bi bi-question-circle text-info me-1"></i>
                                    Kosongkan jika tidak menggunakan range (untuk data non-angka)
                                </div>
                            </div>

                            <!-- Nilai -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold mb-2">
                                    <i class="bi bi-star-fill text-primary me-1"></i>
                                    Nilai <span class="text-danger">*</span>
                                </label>
                                <div class="position-relative">
                                    <input type="number" name="nilai"
                                        class="form-control form-control-lg @error('nilai') is-invalid @enderror"
                                        style="border-radius: 12px; padding: 12px 16px;" value="{{ old('nilai') }}"
                                        min="0" max="10" step="0.01" id="nilaiInput"
                                        placeholder="Contoh: 5">
                                    <div class="position-absolute top-50 end-0 translate-middle-y me-3">
                                        <span class="badge bg-primary rounded-pill px-3 py-2" id="nilaiPreview">0</span>
                                    </div>
                                </div>
                                @error('nilai')
                                    <div class="invalid-feedback d-block mt-1">
                                        <i class="bi bi-exclamation-triangle-fill me-1"></i>{{ $message }}
                                    </div>
                                @enderror

                                <!-- Range Slider -->
                                <div class="mt-3">
                                    <input type="range" class="form-range" min="0" max="10"
                                        step="0.01" value="{{ old('nilai', 0) }}" id="nilaiSlider">
                                    <div class="d-flex justify-content-between mt-2">
                                        <span class="text-muted small">0 (Kurang Baik)</span>
                                        <span class="text-muted small">5 (Cukup)</span>
                                        <span class="text-muted small">10 (Sangat Baik)</span>
                                    </div>
                                </div>

                                <div class="form-text mt-2">
                                    <i class="bi bi-info-circle text-info me-1"></i>
                                    Nilai antara 0 - 10 (semakin tinggi, semakin baik untuk benefit)
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex flex-column flex-sm-row gap-3 mt-4 pt-2">
                                <button type="submit"
                                    class="btn btn-gradient-primary rounded-pill px-5 py-2 shadow-lg transition-all"
                                    style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                                    <i class="bi bi-save me-2"></i> Simpan Sub Kriteria
                                </button>
                                <a href="{{ route('admin.sub-kriteria.index') }}"
                                    class="btn btn-outline-secondary rounded-pill px-4 py-2 transition-all">
                                    <i class="bi bi-arrow-left me-1"></i> Kembali
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Informasi Tambahan -->
                <div class="row mt-4 g-3">
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-3">
                            <div class="card-body text-center p-3">
                                <i class="bi bi-info-circle-fill text-primary fs-4"></i>
                                <p class="small text-muted mt-2 mb-0">
                                    <strong>Tips:</strong> Gunakan label yang jelas
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-3">
                            <div class="card-body text-center p-3">
                                <i class="bi bi-bar-chart-fill text-success fs-4"></i>
                                <p class="small text-muted mt-2 mb-0">
                                    <strong>Range:</strong> Skala 0-10 untuk nilai
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-3">
                            <div class="card-body text-center p-3">
                                <i class="bi bi-check-circle-fill text-info fs-4"></i>
                                <p class="small text-muted mt-2 mb-0">
                                    <strong>Benefit/Cost:</strong> Sesuaikan dengan kriteria
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Custom Styles */
        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .btn-gradient-primary {
            transition: all 0.3s ease;
            border: none;
        }

        .btn-gradient-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
            background: linear-gradient(135deg, #5a67d8 0%, #6b46a0 100%);
        }

        .transition-all {
            transition: all 0.3s ease;
        }

        .btn-outline-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
        }

        /* Form Control Focus Effect */
        .form-control:focus,
        .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        /* Range Slider Styling */
        input[type="range"] {
            -webkit-appearance: none;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            height: 6px;
            border-radius: 10px;
        }

        input[type="range"]::-webkit-slider-thumb {
            -webkit-appearance: none;
            width: 20px;
            height: 20px;
            background: #764ba2;
            border-radius: 50%;
            cursor: pointer;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        input[type="range"]::-webkit-slider-thumb:hover {
            transform: scale(1.2);
            background: #667eea;
        }

        /* Card Animation */
        .card {
            animation: slideInUp 0.5s ease-out;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .card-body {
                padding: 1.5rem !important;
            }

            .btn-gradient-primary,
            .btn-outline-secondary {
                width: 100%;
            }

            .form-control-lg,
            .form-select-lg {
                font-size: 0.95rem;
            }
        }

        /* Custom Number Input */
        input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button {
            opacity: 0.5;
        }

        /* Loading State */
        button[type="submit"]:active {
            transform: scale(0.98);
        }
    </style>

    <script>
        // Live preview for nilai
        const nilaiInput = document.getElementById('nilaiInput');
        const nilaiSlider = document.getElementById('nilaiSlider');
        const nilaiPreview = document.getElementById('nilaiPreview');

        function updateNilai(value) {
            const numValue = parseFloat(value);
            if (!isNaN(numValue)) {
                nilaiPreview.textContent = numValue.toFixed(2);
                // Change badge color based on value
                if (numValue >= 8) {
                    nilaiPreview.className = 'badge bg-success rounded-pill px-3 py-2';
                } else if (numValue >= 5) {
                    nilaiPreview.className = 'badge bg-warning rounded-pill px-3 py-2';
                } else {
                    nilaiPreview.className = 'badge bg-danger rounded-pill px-3 py-2';
                }
            } else {
                nilaiPreview.textContent = '0';
            }
        }

        if (nilaiInput) {
            nilaiInput.addEventListener('input', function(e) {
                nilaiSlider.value = this.value;
                updateNilai(this.value);
            });
        }

        if (nilaiSlider) {
            nilaiSlider.addEventListener('input', function(e) {
                nilaiInput.value = this.value;
                updateNilai(this.value);
            });
        }

        // Show kriteria info on select
        const kriteriaSelect = document.querySelector('select[name="kriteria_id"]');
        if (kriteriaSelect) {
            kriteriaSelect.addEventListener('change', function(e) {
                const selectedOption = this.options[this.selectedIndex];
                const tipe = selectedOption.getAttribute('data-tipe');
                const bobot = selectedOption.getAttribute('data-bobot');

                if (tipe) {
                    const infoDiv = document.querySelector('.form-text.mt-2');
                    if (infoDiv) {
                        const tipeText = tipe === 'cost' ?
                            '<i class="bi bi-arrow-down-circle-fill text-danger me-1"></i> <strong class="text-danger">Cost</strong> (semakin kecil nilai, semakin baik)' :
                            '<i class="bi bi-arrow-up-circle-fill text-success me-1"></i> <strong class="text-success">Benefit</strong> (semakin besar nilai, semakin baik)';
                        infoDiv.innerHTML = `${tipeText} | Bobot: ${bobot}%`;
                    }
                }
            });
        }
    </script>
@endsection
