@extends('layouts.app')
@section('title', 'Daftar UKT')
@section('page-title', 'Pendaftaran UKT Baru')

@section('content')
    <div class="container-fluid px-4 py-4">
        @if (!$periode)
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card border-0 shadow-lg rounded-4 text-center">
                        <div class="card-body p-5">
                            <div class="empty-state-icon">
                                <i class="bi bi-calendar-x"></i>
                            </div>
                            <h5 class="fw-bold mb-2">Periode Tidak Aktif</h5>
                            <p class="text-muted mb-4">
                                Saat ini tidak ada periode seleksi UKT yang sedang aktif.
                                Silakan tunggu pengumuman dari pihak kampus.
                            </p>
                            <a href="{{ route('mahasiswa.dashboard') }}"
                                class="btn-gradient-primary rounded-pill px-4 py-2 text-decoration-none">
                                <i class="bi bi-arrow-left me-2"></i> Kembali ke Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @else
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
                            <li class="breadcrumb-item active text-primary">
                                <i class="bi bi-file-earmark-text me-1"></i> Pendaftaran UKT
                            </li>
                        </ol>
                    </nav>

                    <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                        {{-- Card Header --}}
                        <div class="card-header bg-white py-4 border-0">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                                        <i class="bi bi-file-earmark-plus-fill fs-4 text-primary"></i>
                                    </div>
                                    <div>
                                        <h4 class="fw-bold mb-0 text-dark">Form Pendaftaran UKT</h4>
                                        <p class="text-muted small mb-0">Isi data diri dengan lengkap dan benar</p>
                                    </div>
                                </div>
                                <div class="periode-badge-custom">
                                    <i class="bi bi-calendar-check me-1"></i>
                                    {{ $periode->nama }}
                                </div>
                            </div>
                        </div>

                        <div class="card-body p-4 p-lg-5">
                            <form action="{{ route('mahasiswa.pendaftaran.store') }}" method="POST" id="pendaftaranForm">
                                @csrf

                                {{-- Progress Steps --}}
                                <div class="progress-steps mb-5">
                                    <div class="step-item active">
                                        <div class="step-circle">1</div>
                                        <div class="step-text">Data Diri</div>
                                    </div>
                                    <div class="step-line"></div>
                                    <div class="step-item">
                                        <div class="step-circle">2</div>
                                        <div class="step-text">Data Ekonomi</div>
                                    </div>
                                    <div class="step-line"></div>
                                    <div class="step-item">
                                        <div class="step-circle">3</div>
                                        <div class="step-text">Upload Dokumen</div>
                                    </div>
                                </div>

                                {{-- Form 2 Kolom --}}
                                <div class="row g-4">
                                    {{-- Kolom Kiri --}}
                                    <div class="col-md-6">
                                        {{-- Nama Lengkap --}}
                                        <div class="form-group-modern mb-4">
                                            <label class="form-label-modern">
                                                <i class="bi bi-person"></i>
                                                Nama Lengkap
                                                <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group-modern">
                                                <input type="text" name="nama_lengkap" id="namaLengkap"
                                                    class="form-control-modern @error('nama_lengkap') is-invalid @enderror"
                                                    value="{{ old('nama_lengkap', auth()->user()->name) }}"
                                                    placeholder="Sesuai dengan KTP">
                                                <div class="input-focus-border"></div>
                                            </div>
                                            <div class="form-hint">
                                                <i class="bi bi-info-circle me-1"></i> Pastikan nama sesuai dengan
                                                KTP/Dokumen resmi
                                            </div>
                                            @error('nama_lengkap')
                                                <div class="invalid-feedback-modern">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        {{-- NIK --}}
                                        <div class="form-group-modern mb-4">
                                            <label class="form-label-modern">
                                                <i class="bi bi-credit-card"></i>
                                                NIK (Nomor Induk Kependudukan)
                                                <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group-modern">
                                                <input type="text" name="nik" id="nikInput"
                                                    class="form-control-modern @error('nik') is-invalid @enderror"
                                                    value="{{ old('nik') }}" placeholder="16 digit NIK" maxlength="16"
                                                    inputmode="numeric" pattern="[0-9]*">
                                                <div class="input-focus-border"></div>
                                            </div>
                                            <div id="nikFeedback" class="form-hint">
                                                <i class="bi bi-info-circle me-1"></i> Masukkan 16 digit NIK sesuai KTP
                                            </div>
                                            @error('nik')
                                                <div class="invalid-feedback-modern">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        {{-- No HP --}}
                                        <div class="form-group-modern mb-4">
                                            <label class="form-label-modern">
                                                <i class="bi bi-whatsapp"></i>
                                                Nomor Handphone
                                                <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group-modern">
                                                <span class="input-group-prefix">+62</span>
                                                <input type="text" name="no_hp" id="noHpInput"
                                                    class="form-control-modern with-prefix @error('no_hp') is-invalid @enderror"
                                                    value="{{ old('no_hp') }}" placeholder="8123456789" inputmode="numeric"
                                                    pattern="[0-9]*" maxlength="15">
                                                <div class="input-focus-border"></div>
                                            </div>
                                            <div id="noHpFeedback" class="form-hint">
                                                <i class="bi bi-info-circle me-1"></i> Nomor aktif untuk komunikasi penting
                                            </div>
                                            @error('no_hp')
                                                <div class="invalid-feedback-modern">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Kolom Kanan --}}
                                    <div class="col-md-6">
                                        {{-- Program Studi --}}
                                        <div class="form-group-modern mb-4">
                                            <label class="form-label-modern">
                                                <i class="bi bi-building"></i>
                                                Program Studi
                                                <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group-modern">
                                                <select name="program_studi_id" id="programStudi"
                                                    class="form-control-modern @error('program_studi_id') is-invalid @enderror">
                                                    <option value="">-- Pilih Program Studi --</option>
                                                    @foreach ($programStudi->groupBy('jenjang') as $jenjang => $list)
                                                        <optgroup label="{{ $jenjang }}">
                                                            @foreach ($list as $prodi)
                                                                <option value="{{ $prodi->id }}"
                                                                    {{ old('program_studi_id') == $prodi->id ? 'selected' : '' }}>
                                                                    {{ $prodi->nama }}
                                                                </option>
                                                            @endforeach
                                                        </optgroup>
                                                    @endforeach
                                                </select>
                                                <div class="input-focus-border"></div>
                                            </div>
                                            <div class="form-hint">
                                                <i class="bi bi-info-circle me-1"></i> Pilih program studi yang Anda ambil
                                            </div>
                                            @error('program_studi_id')
                                                <div class="invalid-feedback-modern">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        {{-- Asal Daerah & Provinsi --}}
                                        <div class="row g-3 mb-4">
                                            <div class="col-md-6">
                                                <div class="form-group-modern">
                                                    <label class="form-label-modern">
                                                        <i class="bi bi-geo-alt"></i>
                                                        Asal Daerah
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <div class="input-group-modern">
                                                        <input type="text" name="asal_daerah" id="asalDaerah"
                                                            class="form-control-modern @error('asal_daerah') is-invalid @enderror"
                                                            value="{{ old('asal_daerah') }}"
                                                            placeholder="Contoh: Bandung">
                                                        <div class="input-focus-border"></div>
                                                    </div>
                                                    @error('asal_daerah')
                                                        <div class="invalid-feedback-modern">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group-modern">
                                                    <label class="form-label-modern">
                                                        <i class="bi bi-map"></i>
                                                        Provinsi
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <div class="input-group-modern">
                                                        <input type="text" name="provinsi" id="provinsi"
                                                            class="form-control-modern @error('provinsi') is-invalid @enderror"
                                                            value="{{ old('provinsi') }}"
                                                            placeholder="Contoh: Jawa Barat">
                                                        <div class="input-focus-border"></div>
                                                    </div>
                                                    @error('provinsi')
                                                        <div class="invalid-feedback-modern">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Status KIP --}}
                                        <div class="kip-card mb-4">
                                            <div class="kip-header">
                                                <span class="fw-semibold">Status KIP (Kartu Indonesia Pintar)</span>
                                            </div>
                                            <div class="kip-body">
                                                <div class="form-check form-switch mb-3">
                                                    <input class="form-check-input" type="checkbox" name="is_kip"
                                                        value="1" id="isKip"
                                                        {{ old('is_kip') ? 'checked' : '' }} onchange="toggleKip(this)">
                                                    <label class="form-check-label fw-semibold" for="isKip">
                                                        Saya adalah penerima KIP Kuliah
                                                    </label>
                                                </div>
                                                <div id="kipSection"
                                                    style="display: {{ old('is_kip') ? 'block' : 'none' }};">
                                                    <div class="alert-kip">
                                                        <i class="bi bi-info-circle-fill me-2"></i>
                                                        Mahasiswa KIP tidak melalui proses perhitungan SAW. Nominal UKT
                                                        ditentukan
                                                        berdasarkan akreditasi program studi.
                                                    </div>
                                                    <div class="form-group-modern mt-3">
                                                        <label class="form-label-modern small">
                                                            Nomor KIP
                                                            <span class="text-danger">*</span>
                                                        </label>
                                                        <div class="input-group-modern">
                                                            <input type="text" name="no_kip" id="noKip"
                                                                class="form-control-modern @error('no_kip') is-invalid @enderror"
                                                                value="{{ old('no_kip') }}"
                                                                placeholder="Masukkan nomor KIP Anda">
                                                            <div class="input-focus-border"></div>
                                                        </div>
                                                        @error('no_kip')
                                                            <div class="invalid-feedback-modern">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Info Alert --}}
                                <div class="info-alert-premium mb-4">
                                    <div class="info-alert-icon">
                                        <i class="bi bi-info-circle-fill"></i>
                                    </div>
                                    <div class="info-alert-content">
                                        <div class="info-alert-title">Informasi Pendaftaran</div>
                                        <div class="info-alert-text">
                                            Setelah mengisi data ini, Anda akan diarahkan untuk mengisi
                                            <strong>data ekonomi</strong> dan <strong>upload dokumen pendukung</strong>.
                                        </div>
                                    </div>
                                </div>

                                {{-- Action Buttons --}}
                                <div class="action-buttons-group">
                                    <button type="submit" class="btn-submit-premium" id="submitBtn">
                                        <i class="bi bi-arrow-right-circle"></i>
                                        <span>Lanjut ke Data Ekonomi</span>
                                    </button>
                                    <a href="{{ route('mahasiswa.dashboard') }}" class="btn-cancel-premium">
                                        <i class="bi bi-arrow-left"></i>
                                        <span>Kembali</span>
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <style>
        /* Button Gradient */
        .btn-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            transition: all 0.3s ease;
            display: inline-block;
        }

        .btn-gradient-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
            color: white;
        }

        /* Empty State */
        .empty-state-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            border-radius: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 2.5rem;
            color: #ef4444;
        }

        /* Period Badge */
        .periode-badge-custom {
            background: #eef2ff;
            padding: 8px 18px;
            border-radius: 40px;
            font-size: 0.8rem;
            font-weight: 600;
            color: #3b82f6;
            display: inline-flex;
            align-items: center;
            gap: 6px;
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
        }

        .step-item.active .step-circle {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        .step-text {
            font-size: 0.7rem;
            font-weight: 600;
            color: #94a3b8;
        }

        .step-item.active .step-text {
            color: #667eea;
        }

        .step-line {
            width: 60px;
            height: 2px;
            background: #e2e8f0;
            flex-shrink: 0;
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
            color: #667eea;
            font-size: 0.9rem;
        }

        .input-group-modern {
            position: relative;
            display: flex;
            align-items: stretch;
            width: 100%;
        }

        .input-group-prefix {
            display: flex;
            align-items: center;
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            border-right: none;
            padding: 0 16px;
            border-radius: 12px 0 0 12px;
            color: #64748b;
            font-weight: 600;
            font-size: 0.9rem;
            min-width: 55px;
            white-space: nowrap;
            height: 48px;
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
        }

        .form-control-modern:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-control-modern.is-invalid {
            border-color: #ef4444;
        }

        .form-control-modern.is-invalid:focus {
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        }

        .form-control-modern.with-prefix {
            border-radius: 0 12px 12px 0;
            border-left: none;
            padding-left: 12px;
        }

        /* Hover states */
        .input-group-modern:hover .input-group-prefix {
            border-color: #94a3b8;
        }

        .input-group-modern:hover .form-control-modern.with-prefix {
            border-color: #94a3b8;
        }

        /* Focus states */
        .input-group-modern:focus-within .input-group-prefix {
            border-color: #667eea;
            background: #f1f4ff;
        }

        .form-control-modern:focus~.input-focus-border {
            width: 100%;
            left: 0;
        }

        .input-focus-border {
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: #667eea;
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

        .form-hint.text-success {
            color: #22c55e;
        }

        .form-hint.text-danger {
            color: #ef4444;
        }

        .form-hint.text-warning {
            color: #f59e0b;
        }

        .invalid-feedback-modern {
            font-size: 0.7rem;
            color: #ef4444;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* KIP Card */
        .kip-card {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border-radius: 16px;
            overflow: hidden;
        }

        .kip-header {
            background: rgba(0, 0, 0, 0.05);
            padding: 12px 20px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.85rem;
            color: #92400e;
        }

        .kip-body {
            padding: 20px;
        }

        .alert-kip {
            background: rgba(255, 255, 255, 0.8);
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 0.75rem;
            color: #92400e;
            display: flex;
            align-items: center;
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
            margin-top: 16px;
        }

        .btn-submit-premium {
            flex: 1;
            background: linear-gradient(135deg, #667eea, #764ba2);
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
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
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

        /* Loading State */
        .btn-submit-premium.loading {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .btn-submit-premium.loading i {
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

        /* Card Animation */
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

        /* Responsive */
        @media (max-width: 992px) {
            .container-fluid {
                padding-left: 20px !important;
                padding-right: 20px !important;
            }

            .card-body {
                padding: 1.5rem !important;
            }
        }

        @media (max-width: 768px) {
            .progress-steps {
                display: none;
            }

            .action-buttons-group {
                flex-direction: column;
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

            .periode-badge-custom {
                display: inline-flex;
            }

            .step-line {
                width: 40px;
            }

            .container-fluid {
                padding-left: 15px !important;
                padding-right: 15px !important;
            }

            .card-body {
                padding: 1.25rem !important;
            }

            .input-group-prefix {
                height: 44px;
                min-width: 50px;
                padding: 0 12px;
                font-size: 0.85rem;
            }

            .form-control-modern {
                height: 44px;
            }
        }

        @media (max-width: 480px) {
            .card-body {
                padding: 1rem !important;
            }

            .form-control-modern {
                padding: 10px 14px;
                font-size: 0.85rem;
                height: 42px;
            }

            .input-group-prefix {
                padding: 0 10px;
                min-width: 45px;
                font-size: 0.8rem;
                height: 42px;
            }

            .container-fluid {
                padding-left: 10px !important;
                padding-right: 10px !important;
            }

            .row.g-4 {
                --bs-gutter-y: 1rem;
                --bs-gutter-x: 1rem;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // NIK validation
            const nikInput = document.getElementById('nikInput');
            const nikFeedback = document.getElementById('nikFeedback');
            let nikCheckTimeout;

            if (nikInput) {
                nikInput.addEventListener('input', function() {
                    let value = this.value.replace(/[^0-9]/g, '');
                    this.value = value.slice(0, 16);

                    clearTimeout(nikCheckTimeout);

                    if (value.length !== 16) {
                        if (value.length > 0 && value.length < 16) {
                            nikFeedback.innerHTML =
                                `<i class="bi bi-info-circle me-1"></i> NIK harus 16 digit (${value.length}/16)`;
                            nikFeedback.className = 'form-hint text-warning';
                        } else {
                            nikFeedback.innerHTML =
                                '<i class="bi bi-info-circle me-1"></i> Masukkan 16 digit NIK sesuai KTP';
                            nikFeedback.className = 'form-hint';
                        }
                        nikInput.classList.remove('is-invalid');
                        return;
                    }

                    nikFeedback.innerHTML =
                        '<i class="bi bi-hourglass-split me-1"></i> Mengecek ketersediaan NIK...';
                    nikFeedback.className = 'form-hint text-muted';

                    nikCheckTimeout = setTimeout(async () => {
                        try {
                            const res = await fetch(`/check/nik?nik=${value}&ignore_id=`);
                            const data = await res.json();

                            if (data.exists) {
                                nikFeedback.innerHTML =
                                    '<i class="bi bi-x-circle me-1"></i> NIK sudah terpakai.';
                                nikFeedback.className = 'form-hint text-danger fw-semibold';
                                nikInput.classList.add('is-invalid');
                            } else {
                                nikFeedback.innerHTML =
                                    '<i class="bi bi-check-circle me-1"></i> NIK tersedia.';
                                nikFeedback.className = 'form-hint text-success';
                                nikInput.classList.remove('is-invalid');
                            }
                        } catch (error) {
                            nikFeedback.innerHTML =
                                '<i class="bi bi-info-circle me-1"></i> Gagal mengecek NIK, coba lagi.';
                            nikFeedback.className = 'form-hint text-warning';
                        }
                    }, 500);
                });

                // Blur validation for NIK
                nikInput.addEventListener('blur', function() {
                    const value = this.value;
                    if (value.length > 0 && value.length < 16) {
                        this.classList.add('is-invalid');
                        nikFeedback.innerHTML =
                            `<i class="bi bi-exclamation-triangle me-1"></i> NIK harus 16 digit (${value.length}/16)`;
                        nikFeedback.className = 'form-hint text-danger';
                    }
                });
            }

            // No HP validation - Improved
            const noHpInput = document.getElementById('noHpInput');
            const noHpFeedback = document.getElementById('noHpFeedback');
            let noHpCheckTimeout;

            if (noHpInput) {
                noHpInput.addEventListener('input', function() {
                    let value = this.value.replace(/[^0-9]/g, '');
                    if (value.startsWith('0')) value = value.substring(1);
                    this.value = value;

                    if (this.value.length > 15) {
                        this.value = this.value.slice(0, 15);
                    }

                    clearTimeout(noHpCheckTimeout);

                    const currentLength = this.value.length;

                    if (currentLength < 8) {
                        if (currentLength > 0) {
                            noHpFeedback.innerHTML =
                                `<i class="bi bi-info-circle me-1"></i> Minimal 8 digit (${currentLength}/8)`;
                            noHpFeedback.className = 'form-hint text-warning';
                        } else {
                            noHpFeedback.innerHTML =
                                '<i class="bi bi-info-circle me-1"></i> Nomor aktif untuk komunikasi penting';
                            noHpFeedback.className = 'form-hint';
                        }
                        noHpInput.classList.remove('is-invalid');
                        return;
                    }

                    noHpFeedback.innerHTML =
                        '<i class="bi bi-hourglass-split me-1"></i> Mengecek ketersediaan nomor...';
                    noHpFeedback.className = 'form-hint text-muted';

                    noHpCheckTimeout = setTimeout(async () => {
                        try {
                            const res = await fetch(
                                `/check/no-hp?no_hp=${encodeURIComponent(this.value)}&ignore_id=`
                            );
                            const data = await res.json();

                            if (data.exists) {
                                noHpFeedback.innerHTML =
                                    '<i class="bi bi-x-circle me-1"></i> Nomor telepon sudah terpakai.';
                                noHpFeedback.className = 'form-hint text-danger fw-semibold';
                                noHpInput.classList.add('is-invalid');
                            } else {
                                noHpFeedback.innerHTML =
                                    '<i class="bi bi-check-circle me-1"></i> Nomor telepon tersedia.';
                                noHpFeedback.className = 'form-hint text-success';
                                noHpInput.classList.remove('is-invalid');
                            }
                        } catch (error) {
                            noHpFeedback.innerHTML =
                                '<i class="bi bi-info-circle me-1"></i> Gagal mengecek nomor, coba lagi.';
                            noHpFeedback.className = 'form-hint text-warning';
                        }
                    }, 500);
                });

                noHpInput.addEventListener('blur', function() {
                    const value = this.value;
                    if (value.length > 0 && value.length < 8) {
                        this.classList.add('is-invalid');
                        noHpFeedback.innerHTML =
                            `<i class="bi bi-exclamation-triangle me-1"></i> Minimal 8 digit angka`;
                        noHpFeedback.className = 'form-hint text-danger';
                    }
                });
            }

            // Form validation
            const form = document.getElementById('pendaftaranForm');
            const submitBtn = document.getElementById('submitBtn');

            if (form && submitBtn) {
                form.addEventListener('submit', function(e) {
                    const nama = document.getElementById('namaLengkap')?.value.trim();
                    const nik = document.getElementById('nikInput')?.value;
                    const noHp = document.getElementById('noHpInput')?.value;
                    const prodi = document.getElementById('programStudi')?.value;
                    const asalDaerah = document.getElementById('asalDaerah')?.value.trim();
                    const provinsi = document.getElementById('provinsi')?.value.trim();
                    const isKipChecked = document.getElementById('isKip')?.checked;
                    const noKip = document.getElementById('noKip')?.value.trim();

                    const nikFeedbackEl = document.getElementById('nikFeedback');
                    const noHpFeedbackEl = document.getElementById('noHpFeedback');

                    const isNikDuplicate = nikFeedbackEl?.textContent.includes('sudah terpakai');
                    const isNoHpDuplicate = noHpFeedbackEl?.textContent.includes('sudah terpakai');

                    if (!nama) {
                        e.preventDefault();
                        alert('⚠️ Nama lengkap harus diisi!');
                        return false;
                    }
                    if (!nik) {
                        e.preventDefault();
                        alert('⚠️ NIK harus diisi!');
                        return false;
                    }
                    if (nik.length !== 16) {
                        e.preventDefault();
                        alert('⚠️ NIK harus 16 digit!');
                        return false;
                    }
                    if (isNikDuplicate) {
                        e.preventDefault();
                        alert('⚠️ NIK sudah terdaftar!');
                        return false;
                    }
                    if (!noHp) {
                        e.preventDefault();
                        alert('⚠️ Nomor Handphone harus diisi!');
                        return false;
                    }
                    if (noHp.length < 8) {
                        e.preventDefault();
                        alert('⚠️ Nomor Handphone minimal 8 digit!');
                        return false;
                    }
                    if (isNoHpDuplicate) {
                        e.preventDefault();
                        alert('⚠️ Nomor HP sudah terdaftar!');
                        return false;
                    }
                    if (!prodi) {
                        e.preventDefault();
                        alert('⚠️ Program Studi harus dipilih!');
                        return false;
                    }
                    if (!asalDaerah) {
                        e.preventDefault();
                        alert('⚠️ Asal daerah harus diisi!');
                        return false;
                    }
                    if (!provinsi) {
                        e.preventDefault();
                        alert('⚠️ Provinsi harus diisi!');
                        return false;
                    }
                    if (isKipChecked && !noKip) {
                        e.preventDefault();
                        alert('⚠️ Nomor KIP harus diisi!');
                        return false;
                    }

                    submitBtn.classList.add('loading');
                    submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> <span>Memproses...</span>';
                    return true;
                });
            }
        });

        function toggleKip(checkbox) {
            const kipSection = document.getElementById('kipSection');
            const noKip = document.getElementById('noKip');
            if (checkbox.checked) {
                kipSection.style.display = 'block';
                noKip.required = true;
            } else {
                kipSection.style.display = 'none';
                noKip.required = false;
                noKip.value = '';
            }
        }
    </script>
@endsection
