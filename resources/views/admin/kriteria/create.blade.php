@extends('layouts.app')
@section('title', 'Tambah Kriteria SAW')
@section('page-title', 'Tambah Kriteria SAW')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card premium-card">
                <div class="card-header premium-card-header">
                    <div class="d-flex align-items-center gap-2">
                        <div class="header-icon primary-icon">
                            <i class="bi bi-sliders2"></i>
                        </div>
                        <h6 class="fw-semibold mb-0">Form Tambah Kriteria SAW</h6>
                    </div>
                    <div class="step-indicator">
                        <span class="step active">1</span>
                        <span class="step-line"></span>
                        <span class="step">2</span>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.kriteria.store') }}" method="POST" id="kriteriaForm">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Periode <span class="text-danger">*</span>
                            </label>
                            <select name="periode_id" id="periodeSelect" class="form-select @error('periode_id') is-invalid @enderror">
                                <option value="">-- Pilih Periode --</option>
                                @foreach ($periode as $p)
                                    <option value="{{ $p->id }}" {{ old('periode_id', $periodeAktif->id ?? '') == $p->id ? 'selected' : '' }}>
                                        {{ $p->nama }} ({{ $p->tahun }})
                                    </option>
                                @endforeach
                            </select>
                            @error('periode_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Card Info Bobot Periode Terpilih -->
                        <div class="card bg-light border-0 rounded-3 mb-4 p-3" id="bobotInfoCard">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <div class="text-muted small fw-medium">
                                        <i class="bi bi-pie-chart-fill text-primary me-1"></i> Status Alokasi Bobot Periode
                                    </div>
                                    <div class="fw-bold mt-1">
                                        Terpakai: <span id="bobotTerpakaiText">0</span>% / 100%
                                    </div>
                                </div>
                                <div class="text-end">
                                    <span class="badge" id="sisaBobotBadge" style="font-size: 0.85rem;">
                                        Sisa: <span id="sisaBobotText">100</span>%
                                    </span>
                                </div>
                            </div>
                            <div class="progress mt-2" style="height: 6px;">
                                <div class="progress-bar" id="bobotProgressBar" role="progressbar" style="width: 0%;"></div>
                            </div>
                            <div class="small text-danger mt-2" id="bobotFullWarning" style="display: none;">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                Total bobot periode ini sudah mencapai 100%. Tidak dapat menambah kriteria baru dengan bobot > 0 kecuali kriteria lain dikurangi terlebih dahulu.
                            </div>
                        </div>

                        <!-- Periode Info -->
                        <div class="info-banner mb-4">
                            <i class="bi bi-calendar-check-fill"></i>
                            <div>
                                <span class="info-label">Periode Aktif</span>
                                @php $periodeAktif = $periode->firstWhere('status', 'aktif'); @endphp
                                @if ($periodeAktif)
                                    <span class="info-value">{{ $periodeAktif->nama }} ({{ $periodeAktif->tahun }})</span>
                                @endif
                            </div>
                        </div>

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
                                    value="{{ old('kode') }}" placeholder="Contoh: C1, C2, C3" id="kodeKriteria"
                                    autocomplete="off">
                                <div class="input-focus-border"></div>
                            </div>
                            <div class="form-hint">
                                <i class="bi bi-info-circle"></i>
                                Gunakan kode yang unik dan mudah diingat, seperti C1, C2, C3
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
                                    value="{{ old('nama') }}" placeholder="Contoh: Penghasilan Orang Tua"
                                    id="namaKriteria">
                                <div class="input-focus-border"></div>
                            </div>
                            <div class="form-hint">
                                <i class="bi bi-info-circle"></i>
                                Beri nama yang jelas dan deskriptif untuk kriteria
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
                                    value="{{ old('bobot') }}" min="0" max="100" step="0.1"
                                    placeholder="0" id="bobotKriteria">
                                <span class="input-group-text-modern">%</span>
                                <div class="input-focus-border"></div>
                            </div>
                            <div class="bobot-slider-container">
                                <input type="range" id="bobotSlider" min="0" max="100" step="0.1"
                                    value="0">
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
                                        {{ old('tipe') === 'cost' ? 'checked' : '' }}>
                                    <div class="tipe-card tipe-cost">
                                        <i class="bi bi-arrow-down-circle-fill"></i>
                                        <div class="tipe-title">Cost</div>
                                        <div class="tipe-desc">Semakin kecil semakin baik</div>
                                        <div class="tipe-example">Contoh: Penghasilan, PBB, Tagihan</div>
                                    </div>
                                </label>
                                <label class="tipe-option">
                                    <input type="radio" name="tipe" value="benefit"
                                        {{ old('tipe') === 'benefit' ? 'checked' : '' }}>
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

                        <!-- Action Buttons -->
                        <div class="action-buttons">
                            <button type="submit" class="btn-submit" id="submitBtn">
                                <i class="bi bi-save"></i>
                                <span>Simpan Kriteria</span>
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
        /* Premium Card */
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

        .primary-icon {
            background: linear-gradient(135deg, #dbeafe, #bfdbfe);
            color: #3b82f6;
        }

        /* Step Indicator */
        .step-indicator {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .step {
            width: 28px;
            height: 28px;
            background: #e2e8f0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 700;
            color: #64748b;
            transition: all 0.3s;
        }

        .step.active {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.4);
        }

        .step-line {
            width: 30px;
            height: 2px;
            background: #e2e8f0;
        }

        /* Info Banner */
        .info-banner {
            background: linear-gradient(135deg, #eef2ff, #e0e7ff);
            border-radius: 16px;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            color: #3b82f6;
        }

        .info-banner i {
            font-size: 1.2rem;
        }

        .info-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            color: #64748b;
            display: block;
        }

        .info-value {
            font-weight: 700;
            font-size: 0.85rem;
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
            color: #3b82f6;
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
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
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
            background: #3b82f6;
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
            background: #3b82f6;
            border-radius: 50%;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.4);
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
            background: #3b82f6;
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
            background: linear-gradient(135deg, #3b82f6, #2563eb);
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
            box-shadow: 0 8px 20px rgba(59, 130, 246, 0.4);
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
                gap: 12px;
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

            .step-indicator {
                display: none;
            }

            .bobot-preset {
                justify-content: center;
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
            // Bobot slider synchronization
            const bobotInput = document.getElementById('bobotKriteria');
            const bobotSlider = document.getElementById('bobotSlider');
            const bobotHint = document.getElementById('bobotHint');

            function updateBobot(value) {
                bobotInput.value = value;
                bobotSlider.value = value;

            const bobotPerPeriode = @json($bobotPerPeriode ?? []);
            const periodeSelect = document.getElementById('periodeSelect');

            function getSelectedPeriodeTotal() {
                const pId = periodeSelect ? periodeSelect.value : '';
                return pId && bobotPerPeriode[pId] !== undefined ? parseFloat(bobotPerPeriode[pId]) : 0;
            }

            function updateBobotInfo() {
                const pId = periodeSelect ? periodeSelect.value : '';
                const card = document.getElementById('bobotInfoCard');
                if (!card) return;

                if (!pId) {
                    card.style.display = 'none';
                    return;
                }
                card.style.display = 'block';

                const terpakai = getSelectedPeriodeTotal();
                const sisa = Math.max(0, Math.round((100 - terpakai) * 100) / 100);

                document.getElementById('bobotTerpakaiText').textContent = terpakai.toFixed(1);
                document.getElementById('sisaBobotText').textContent = sisa.toFixed(1);

                const progressBar = document.getElementById('bobotProgressBar');
                progressBar.style.width = Math.min(100, terpakai) + '%';

                const badge = document.getElementById('sisaBobotBadge');
                const warning = document.getElementById('bobotFullWarning');

                if (terpakai >= 100) {
                    badge.className = 'badge bg-danger text-white';
                    progressBar.className = 'progress-bar bg-danger';
                    warning.style.display = 'block';
                } else if (terpakai >= 80) {
                    badge.className = 'badge bg-warning text-dark';
                    progressBar.className = 'progress-bar bg-warning';
                    warning.style.display = 'none';
                } else {
                    badge.className = 'badge bg-success text-white';
                    progressBar.className = 'progress-bar bg-success';
                    warning.style.display = 'none';
                }

                updateBobot(bobotInput.value || 0);
            }

            if (periodeSelect) {
                periodeSelect.addEventListener('change', updateBobotInfo);
            }

            function updateBobot(value) {
                bobotInput.value = value;
                bobotSlider.value = value;

                const baseTotal = getSelectedPeriodeTotal();
                const currentTotal = Math.round((baseTotal + parseFloat(value || 0)) * 100) / 100;
                const remaining = Math.max(0, Math.round((100 - baseTotal) * 100) / 100);

                if (currentTotal > 100) {
                    bobotHint.innerHTML =
                        `<i class="bi bi-exclamation-triangle-fill text-danger"></i> <strong class="text-danger">Perhatian!</strong> Total bobot akan melebihi 100% (${currentTotal}%). Sisa yang tersedia: <strong>${remaining}%</strong>`;
                    bobotHint.style.color = '#dc2626';
                } else if (currentTotal === 100) {
                    bobotHint.innerHTML =
                        '<i class="bi bi-check-circle-fill text-success"></i> <strong class="text-success">Sempurna!</strong> Total bobot periode akan tepat 100.00%';
                    bobotHint.style.color = '#10b981';
                } else {
                    bobotHint.innerHTML =
                        `<i class="bi bi-info-circle"></i> Total bobot periode akan menjadi <strong>${currentTotal}%</strong> (sisa yang tersedia: <strong>${(100 - currentTotal).toFixed(1)}%</strong>)`;
                    bobotHint.style.color = '#64748b';
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

            // Auto-generate kode suggestion
            const kodeInput = document.getElementById('kodeKriteria');
            const namaInput = document.getElementById('namaKriteria');

            // Form validation before submit
            const form = document.getElementById('kriteriaForm');
            const submitBtn = document.getElementById('submitBtn');

            form.addEventListener('submit', function(e) {
                const bobot = parseFloat(bobotInput.value || 0);
                const baseTotal = getSelectedPeriodeTotal();
                const currentTotal = Math.round((bobot + baseTotal) * 100) / 100;

                if (currentTotal > 100) {
                    e.preventDefault();
                    alert(`⚠️ Total bobot tidak boleh melebihi 100%!\n\nTotal saat ini: ${baseTotal}%\nInput: ${bobot}%\nTotal setelah ditambah: ${currentTotal}%\nSisa tersedia: ${Math.max(0, 100 - baseTotal)}%`);
                    return false;
                }

                // Add loading state
                submitBtn.classList.add('loading');
                submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> <span>Menyimpan...</span>';
                return true;
            });

            // Trigger initial check
            updateBobotInfo();

            // Character counter for nama
            namaInput.addEventListener('input', function() {
                const maxLength = 100;
                const currentLength = this.value.length;
                if (currentLength > maxLength) {
                    this.value = this.value.substring(0, maxLength);
                }
            });
        });
    </script>
@endsection
