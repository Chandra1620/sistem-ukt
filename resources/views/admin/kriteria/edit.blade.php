@extends('layouts.app')
@section('title', 'Edit Kriteria SAW')
@section('page-title', 'Edit Kriteria SAW')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card premium-card">
                <div class="card-header premium-card-header">
                    <div class="d-flex align-items-center gap-2">
                        <div class="header-icon warning-icon">
                            <i class="bi bi-sliders2"></i>
                        </div>
                        <h6 class="fw-semibold mb-0">Edit Kriteria: {{ $kriteria->nama }}</h6>
                    </div>
                    <div class="kriteria-status-badge">
                        <i class="bi bi-info-circle"></i>
                        {{ $kriteria->subKriteria->count() }} Sub Kriteria
                    </div>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.kriteria.update', $kriteria) }}" method="POST" id="kriteriaForm">
                        @csrf @method('PUT')

                        <!-- Kode Kriteria -->
                        <div class="form-group-modern mb-4">
                            <label class="form-label-modern">
                                <i class="bi bi-hash"></i>
                                Kode Kriteria
                                <span class="text-danger">*</span>
                            </label>
                            <div class="input-group-modern">
                                <input type="text" name="kode"
                                    class="form-control-modern @error('kode') is-invalid @enderror"
                                    value="{{ old('kode', $kriteria->kode) }}" placeholder="Contoh: C1, C2, C3"
                                    id="kodeKriteria">
                                <div class="input-focus-border"></div>
                            </div>
                            <div class="form-hint">
                                <i class="bi bi-info-circle"></i>
                                Kode kriteria bersifat unik dan tidak boleh sama dengan kriteria lain
                            </div>
                            @error('kode')
                                <div class="invalid-feedback-modern">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Nama Kriteria -->
                        <div class="form-group-modern mb-4">
                            <label class="form-label-modern">
                                <i class="bi bi-textarea"></i>
                                Nama Kriteria
                                <span class="text-danger">*</span>
                            </label>
                            <div class="input-group-modern">
                                <input type="text" name="nama"
                                    class="form-control-modern @error('nama') is-invalid @enderror"
                                    value="{{ old('nama', $kriteria->nama) }}" placeholder="Contoh: Penghasilan Orang Tua"
                                    id="namaKriteria">
                                <div class="input-focus-border"></div>
                            </div>
                            <div class="form-hint">
                                <i class="bi bi-info-circle"></i>
                                Nama kriteria harus deskriptif dan mudah dipahami
                            </div>
                            @error('nama')
                                <div class="invalid-feedback-modern">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Bobot -->
                        <div class="form-group-modern mb-4">
                            <label class="form-label-modern">
                                <i class="bi bi-percent"></i>
                                Bobot (%)
                                <span class="text-danger">*</span>
                            </label>
                            <div class="input-group-modern input-group-bobot">
                                <input type="number" name="bobot"
                                    class="form-control-modern @error('bobot') is-invalid @enderror"
                                    value="{{ old('bobot', $kriteria->bobot) }}" min="0" max="100"
                                    step="0.1" id="bobotKriteria">
                                <span class="input-group-text-modern">%</span>
                                <div class="input-focus-border"></div>
                            </div>
                            <div class="bobot-slider-container">
                                <input type="range" id="bobotSlider" min="0" max="100" step="0.1"
                                    value="{{ old('bobot', $kriteria->bobot) }}">
                                <div class="bobot-preset">
                                    <span class="preset-btn" data-value="5">5%</span>
                                    <span class="preset-btn" data-value="10">10%</span>
                                    <span class="preset-btn" data-value="15">15%</span>
                                    <span class="preset-btn" data-value="20">20%</span>
                                    <span class="preset-btn" data-value="25">25%</span>
                                    <span class="preset-btn" data-value="30">30%</span>
                                </div>
                            </div>
                            <div class="form-hint" id="bobotHint">
                                <i class="bi bi-info-circle"></i>
                                Total semua bobot harus <strong>100%</strong>
                            </div>
                            @error('bobot')
                                <div class="invalid-feedback-modern">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Tipe Kriteria -->
                        <div class="form-group-modern mb-4">
                            <label class="form-label-modern">
                                <i class="bi bi-arrow-left-right"></i>
                                Tipe Kriteria
                                <span class="text-danger">*</span>
                            </label>
                            <div class="tipe-options">
                                <label class="tipe-option">
                                    <input type="radio" name="tipe" value="cost"
                                        {{ old('tipe', $kriteria->tipe) === 'cost' ? 'checked' : '' }}>
                                    <div class="tipe-card tipe-cost">
                                        <i class="bi bi-arrow-down-circle-fill"></i>
                                        <div class="tipe-title">Cost</div>
                                        <div class="tipe-desc">Semakin kecil semakin baik</div>
                                        <div class="tipe-example">Contoh: Penghasilan, PBB, Tagihan</div>
                                    </div>
                                </label>
                                <label class="tipe-option">
                                    <input type="radio" name="tipe" value="benefit"
                                        {{ old('tipe', $kriteria->tipe) === 'benefit' ? 'checked' : '' }}>
                                    <div class="tipe-card tipe-benefit">
                                        <i class="bi bi-arrow-up-circle-fill"></i>
                                        <div class="tipe-title">Benefit</div>
                                        <div class="tipe-desc">Semakin besar semakin baik</div>
                                        <div class="tipe-example">Contoh: Tanggungan, Aset, Prestasi</div>
                                    </div>
                                </label>
                            </div>
                            @error('tipe')
                                <div class="invalid-feedback-modern">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Warning if has sub criteria -->
                        @if ($kriteria->subKriteria->count() > 0)
                            <div class="info-warning mb-4">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                                <div>
                                    <strong>Perhatian!</strong> Kriteria ini memiliki
                                    {{ $kriteria->subKriteria->count() }} sub kriteria.
                                    Perubahan tipe kriteria dapat mempengaruhi data sub kriteria yang sudah ada.
                                </div>
                            </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="action-buttons">
                            <button type="submit" class="btn-submit" id="submitBtn">
                                <i class="bi bi-save"></i>
                                <span>Update Kriteria</span>
                            </button>
                            <a href="{{ route('admin.kriteria.index') }}" class="btn-cancel">
                                <i class="bi bi-arrow-left"></i>
                                <span>Kembali</span>
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        .premium-card {
            border: none;
            border-radius: 28px;
            box-shadow: 0 8px 28px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .premium-card:hover {
            box-shadow: 0 12px 36px rgba(0, 0, 0, 0.12);
        }

        /* Card Header */
        .premium-card-header {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border-bottom: 1px solid #e2e8f0;
            padding: 20px 28px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }

        .header-icon {
            width: 40px;
            height: 40px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
        }

        .warning-icon {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            color: #f59e0b;
        }

        .kriteria-status-badge {
            background: #f1f5f9;
            padding: 6px 14px;
            border-radius: 40px;
            font-size: 0.7rem;
            font-weight: 600;
            color: #475569;
            display: inline-flex;
            align-items: center;
            gap: 6px;
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
            color: #f59e0b;
            font-size: 0.9rem;
        }

        .input-group-modern {
            position: relative;
            display: flex;
            align-items: center;
        }

        .form-control-modern {
            width: 100%;
            padding: 12px 16px;
            font-size: 0.9rem;
            border: 2px solid #e2e8f0;
            border-radius: 16px;
            transition: all 0.2s ease;
            background: white;
        }

        .form-control-modern:focus {
            outline: none;
            border-color: #f59e0b;
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
        }

        .form-control-modern.is-invalid {
            border-color: #ef4444;
        }

        .input-group-text-modern {
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            border-left: none;
            padding: 12px 16px;
            border-radius: 0 16px 16px 0;
            color: #64748b;
            font-weight: 600;
        }

        .input-focus-border {
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: #f59e0b;
            transition: all 0.3s ease;
        }

        .form-control-modern:focus~.input-focus-border {
            width: 100%;
            left: 0;
        }

        .form-hint {
            font-size: 0.7rem;
            color: #94a3b8;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .invalid-feedback-modern {
            font-size: 0.7rem;
            color: #ef4444;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .invalid-feedback-modern::before {
            content: '⚠️';
            font-size: 0.65rem;
        }

        /* Bobot Slider */
        .bobot-slider-container {
            margin-top: 12px;
        }

        input[type="range"] {
            width: 100%;
            height: 4px;
            -webkit-appearance: none;
            background: #e2e8f0;
            border-radius: 5px;
            outline: none;
        }

        input[type="range"]::-webkit-slider-thumb {
            -webkit-appearance: none;
            width: 16px;
            height: 16px;
            background: #f59e0b;
            border-radius: 50%;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(245, 158, 11, 0.4);
        }

        .bobot-preset {
            display: flex;
            gap: 8px;
            margin-top: 12px;
            flex-wrap: wrap;
        }

        .preset-btn {
            background: #f1f5f9;
            padding: 4px 12px;
            border-radius: 40px;
            font-size: 0.7rem;
            font-weight: 600;
            color: #475569;
            cursor: pointer;
            transition: all 0.2s;
        }

        .preset-btn:hover {
            background: #f59e0b;
            color: white;
        }

        /* Tipe Options */
        .tipe-options {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }

        .tipe-option {
            cursor: pointer;
        }

        .tipe-option input {
            display: none;
        }

        .tipe-card {
            padding: 16px;
            border-radius: 20px;
            text-align: center;
            transition: all 0.2s ease;
            border: 2px solid #e2e8f0;
            cursor: pointer;
        }

        .tipe-option input:checked+.tipe-card {
            border-color: currentColor;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transform: scale(1.02);
        }

        .tipe-card i {
            font-size: 1.8rem;
            margin-bottom: 8px;
        }

        .tipe-cost {
            color: #dc2626;
        }

        .tipe-cost i {
            color: #dc2626;
        }

        .tipe-benefit {
            color: #10b981;
        }

        .tipe-benefit i {
            color: #10b981;
        }

        .tipe-title {
            font-weight: 700;
            font-size: 1rem;
            margin-bottom: 4px;
        }

        .tipe-desc {
            font-size: 0.7rem;
            color: #64748b;
            margin-bottom: 6px;
        }

        .tipe-example {
            font-size: 0.65rem;
            color: #94a3b8;
            background: #f8fafc;
            padding: 4px 8px;
            border-radius: 8px;
            margin-top: 8px;
        }

        /* Info Warning */
        .info-warning {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 14px 18px;
            border-radius: 16px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            font-size: 0.8rem;
            color: #92400e;
        }

        .info-warning i {
            font-size: 1.2rem;
            margin-top: 2px;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 12px;
            margin-top: 32px;
            padding-top: 16px;
            border-top: 1px solid #e2e8f0;
        }

        .btn-submit {
            flex: 1;
            background: linear-gradient(135deg, #f59e0b, #d97706);
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

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(245, 158, 11, 0.4);
        }

        .btn-cancel {
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

        .btn-cancel:hover {
            background: #e2e8f0;
            transform: translateY(-2px);
            color: #1e293b;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .premium-card-header {
                padding: 16px 20px;
                flex-direction: column;
                text-align: center;
            }

            .card-body {
                padding: 20px !important;
            }

            .tipe-options {
                grid-template-columns: 1fr;
                gap: 12px;
            }

            .action-buttons {
                flex-direction: column;
            }

            .bobot-preset {
                justify-content: center;
            }

            .info-warning {
                flex-direction: column;
                text-align: center;
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

        .premium-card {
            animation: fadeInUp 0.4s ease;
        }

        /* Loading State */
        .btn-submit.loading {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .btn-submit.loading i {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get current total bobot from other kriteria (excluding this one)
            const currentTotalBobot = {{ $currentTotalBobot ?? 0 }};
            const currentBobot = {{ $kriteria->bobot }};

            // Bobot slider synchronization
            const bobotInput = document.getElementById('bobotKriteria');
            const bobotSlider = document.getElementById('bobotSlider');
            const bobotHint = document.getElementById('bobotHint');

            function updateBobot(value) {
                bobotInput.value = value;
                bobotSlider.value = value;

                // Calculate new total (other kriteria total + new value)
                const newTotal = (currentTotalBobot - currentBobot) + parseFloat(value);
                const remaining = 100 - newTotal;

                if (newTotal > 100) {
                    bobotHint.innerHTML =
                        '<i class="bi bi-exclamation-triangle-fill text-danger"></i> <strong class="text-danger">Perhatian!</strong> Total bobot akan melebihi 100% (' +
                        newTotal + '%)!';
                    bobotHint.style.color = '#dc2626';
                    document.querySelector('.btn-submit').disabled = false;
                } else if (newTotal === 100) {
                    bobotHint.innerHTML =
                        '<i class="bi bi-check-circle-fill text-success"></i> <strong class="text-success">Sempurna!</strong> Total bobot akan menjadi 100%';
                    bobotHint.style.color = '#10b981';
                } else {
                    bobotHint.innerHTML =
                        `<i class="bi bi-info-circle"></i> Total bobot akan menjadi <strong>${newTotal}%</strong> (butuh ${100 - newTotal}% lagi untuk mencapai 100%)`;
                    bobotHint.style.color = '#94a3b8';
                }
            }

            bobotInput.addEventListener('input', function() {
                updateBobot(this.value);
            });

            bobotSlider.addEventListener('input', function() {
                updateBobot(this.value);
            });

            // Preset buttons
            document.querySelectorAll('.preset-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const value = this.getAttribute('data-value');
                    updateBobot(value);
                });
            });

            // Form validation before submit
            const form = document.getElementById('kriteriaForm');
            const submitBtn = document.getElementById('submitBtn');

            form.addEventListener('submit', function(e) {
                const bobot = parseFloat(bobotInput.value);
                const newTotal = (currentTotalBobot - currentBobot) + bobot;

                if (newTotal > 100) {
                    e.preventDefault();
                    alert('⚠️ Total bobot tidak boleh melebihi 100%!\n\nTotal setelah update: ' + newTotal +
                        '%');
                    return false;
                }

                // Add loading state
                submitBtn.classList.add('loading');
                submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> <span>Menyimpan...</span>';
                return true;
            });

            // Character counter for nama
            const namaInput = document.getElementById('namaKriteria');
            namaInput.addEventListener('input', function() {
                const maxLength = 100;
                const currentLength = this.value.length;
                if (currentLength > maxLength) {
                    this.value = this.value.substring(0, maxLength);
                }
            });

            // Initial hint update
            updateBobot(bobotInput.value);
        });
    </script>
@endsection
