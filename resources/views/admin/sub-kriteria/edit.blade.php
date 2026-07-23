@extends('layouts.app')
@section('title', 'Edit Sub Kriteria')
@section('page-title', 'Edit Sub Kriteria')

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
                            <li class="breadcrumb-item active" aria-current="page">Edit Data</li>
                        </ol>
                    </nav>
                    <h4 class="fw-bold mb-1">✏️ Edit Sub Kriteria</h4>
                    <p class="text-muted small">Perbarui informasi sub kriteria yang dipilih</p>
                </div>

                <!-- Form Card -->
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                    <!-- Card Header Gradient -->
                    <div class="card-header bg-gradient-warning text-white py-3 px-4 border-0">
                        <div class="d-flex align-items-center gap-2">
                            <div class="bg-white bg-opacity-25 rounded-circle p-2">
                                <i class="bi bi-pencil-square fs-5"></i>
                            </div>
                            <div>
                                <h6 class="fw-semibold mb-0 text-white">Form Edit Sub Kriteria</h6>
                                <small class="text-white-50">Perbarui data dengan benar</small>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-4 p-lg-5">
                        <form action="{{ route('admin.sub-kriteria.update', $subKriteria) }}" method="POST"
                            id="editSubKriteriaForm">
                            @csrf
                            @method('PUT')

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
                                            {{ old('kriteria_id', $subKriteria->kriteria_id) == $k->id ? 'selected' : '' }}
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
                                <div class="form-text mt-2" id="kriteriaInfo">
                                    <i class="bi bi-info-circle text-info me-1"></i>
                                    Pilih kriteria yang akan diperbarui sub kriterianya
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
                                    style="border-radius: 12px; padding: 12px 16px;"
                                    value="{{ old('label', $subKriteria->label) }}"
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
                                                style="padding: 12px 16px;"
                                                value="{{ old('range_min', $subKriteria->range_min) }}"
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
                                                style="padding: 12px 16px;"
                                                value="{{ old('range_max', $subKriteria->range_max) }}"
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
                                        style="border-radius: 12px; padding: 12px 16px;"
                                        value="{{ old('nilai', $subKriteria->nilai) }}" min="0" max="10"
                                        step="0.01" id="nilaiInput" placeholder="Contoh: 5">
                                    <div class="position-absolute top-50 end-0 translate-middle-y me-3">
                                        <span class="badge rounded-pill px-3 py-2"
                                            id="nilaiPreview">{{ number_format(old('nilai', $subKriteria->nilai), 2) }}</span>
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
                                        step="0.01" value="{{ old('nilai', $subKriteria->nilai) }}"
                                        id="nilaiSlider">
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

                            <!-- Info Box -->
                            <div class="alert alert-info border-0 rounded-3 mb-4"
                                style="background: linear-gradient(135deg, #e0e7ff 0%, #e9d5ff 100%);">
                                <div class="d-flex gap-3 align-items-center">
                                    <i class="bi bi-calendar-check fs-4 text-primary"></i>
                                    <div>
                                        <small class="text-primary fw-semibold">Informasi Data</small><br>
                                        <small class="text-muted">
                                            Dibuat: {{ $subKriteria->created_at->format('d/m/Y H:i') }} |
                                            Terakhir diperbarui: {{ $subKriteria->updated_at->diffForHumans() }}
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex flex-column flex-sm-row gap-3 mt-4 pt-2">
                                <button type="submit"
                                    class="btn btn-gradient-warning rounded-pill px-5 py-2 shadow-lg transition-all">
                                    <i class="bi bi-pencil-square me-2"></i> Update Sub Kriteria
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
                                    <strong>Tips:</strong> Periksa kembali data sebelum update
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
        .bg-gradient-warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .btn-gradient-warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            transition: all 0.3s ease;
            border: none;
            color: white;
        }

        .btn-gradient-warning:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(240, 147, 251, 0.3);
            background: linear-gradient(135deg, #e07ce6 0%, #e04860 100%);
            color: white;
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
            border-color: #f5576c;
            box-shadow: 0 0 0 0.2rem rgba(245, 87, 108, 0.25);
        }

        /* Range Slider Styling */
        input[type="range"] {
            -webkit-appearance: none;
            background: linear-gradient(90deg, #f093fb 0%, #f5576c 100%);
            height: 6px;
            border-radius: 10px;
        }

        input[type="range"]::-webkit-slider-thumb {
            -webkit-appearance: none;
            width: 20px;
            height: 20px;
            background: #f5576c;
            border-radius: 50%;
            cursor: pointer;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        input[type="range"]::-webkit-slider-thumb:hover {
            transform: scale(1.2);
            background: #f093fb;
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

            .btn-gradient-warning,
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
                nilaiPreview.className = 'badge bg-danger rounded-pill px-3 py-2';
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
        const kriteriaInfo = document.getElementById('kriteriaInfo');

        if (kriteriaSelect) {
            function updateKriteriaInfo() {
                const selectedOption = kriteriaSelect.options[kriteriaSelect.selectedIndex];
                const tipe = selectedOption.getAttribute('data-tipe');
                const bobot = selectedOption.getAttribute('data-bobot');

                if (tipe && kriteriaInfo) {
                    const tipeText = tipe === 'cost' ?
                        '<i class="bi bi-arrow-down-circle-fill text-danger me-1"></i> <strong class="text-danger">Cost</strong> (semakin kecil nilai, semakin baik)' :
                        '<i class="bi bi-arrow-up-circle-fill text-success me-1"></i> <strong class="text-success">Benefit</strong> (semakin besar nilai, semakin baik)';
                    kriteriaInfo.innerHTML = `${tipeText} | Bobot: ${bobot}%`;
                }
            }

            kriteriaSelect.addEventListener('change', updateKriteriaInfo);
            // Trigger on load
            updateKriteriaInfo();
        }

        // Initialize preview on load
        if (nilaiInput) {
            updateNilai(nilaiInput.value);
        }
    </script>
@endsection
