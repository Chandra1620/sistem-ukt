@extends('layouts.app')
@section('title', 'Edit Pendaftaran')
@section('page-title', 'Edit Pendaftaran')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card premium-card">
                <div class="card-header premium-card-header">
                    <div class="d-flex align-items-center gap-2">
                        <div class="header-icon warning-icon">
                            <i class="bi bi-pencil-square"></i>
                        </div>
                        <h6 class="fw-semibold mb-0">Edit Pendaftaran</h6>
                    </div>
                    <div class="no-pendaftaran-badge">
                        <i class="bi bi-hash"></i>
                        {{ $pendaftaran->no_pendaftaran }}
                    </div>
                </div>
                <div class="card-body p-4">
                    <!-- Alert Warning Premium -->
                    <div class="alert-premium alert-warning-premium mb-4">
                        <div class="alert-icon">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                        </div>
                        <div class="alert-content">
                            <div class="alert-title">Perhatian!</div>
                            <div class="alert-message">
                                Anda hanya dapat mengedit pendaftaran yang berstatus
                                <strong>Draft</strong> atau <strong>Ditolak Reviewer</strong>.
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('mahasiswa.pendaftaran.update', $pendaftaran) }}" method="POST" id="editForm">
                        @csrf
                        @method('PUT')

                        <!-- Nama Lengkap -->
                        <div class="form-group-modern mb-4">
                            <label class="form-label-modern">
                                <i class="bi bi-person"></i>
                                Nama Lengkap
                                <span class="text-danger">*</span>
                            </label>
                            <div class="input-group-modern">
                                <input type="text" name="nama_lengkap"
                                    class="form-control-modern @error('nama_lengkap') is-invalid @enderror"
                                    value="{{ old('nama_lengkap', $pendaftaran->nama_lengkap) }}"
                                    placeholder="Sesuai dengan KTP" id="namaLengkap">
                                <div class="input-focus-border"></div>
                            </div>
                            <div class="form-hint">
                                <i class="bi bi-info-circle"></i>
                                Pastikan nama sesuai dengan dokumen
                            </div>
                            @error('nama_lengkap')
                                <div class="invalid-feedback-modern">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- NIK -->
                        <div class="form-group-modern mb-4">
                            <label class="form-label-modern">
                                <i class="bi bi-credit-card"></i>
                                NIK (Nomor Induk Kependudukan)
                                <span class="text-danger">*</span>
                            </label>
                            <div class="input-group-modern">
                                <input type="text" name="nik" id="nikInput"
                                    class="form-control-modern @error('nik') is-invalid @enderror"
                                    value="{{ old('nik', $pendaftaran->nik) }}" placeholder="16 digit NIK" maxlength="16">
                                <div class="input-focus-border"></div>
                            </div>
                            <div id="nikFeedback" class="form-hint">
                                <i class="bi bi-info-circle"></i>
                                Masukkan 16 digit NIK sesuai KTP
                            </div>
                            @error('nik')
                                <div class="invalid-feedback-modern">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- No HP -->
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
                                    value="{{ old('no_hp', $pendaftaran->no_hp) }}" placeholder="8123456789">
                                <div class="input-focus-border"></div>
                            </div>
                            <div id="noHpFeedback" class="form-hint">
                                <i class="bi bi-info-circle"></i>
                                Nomor aktif untuk komunikasi penting
                            </div>
                            @error('no_hp')
                                <div class="invalid-feedback-modern">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Program Studi -->
                        <div class="form-group-modern mb-4">
                            <label class="form-label-modern">
                                <i class="bi bi-building"></i>
                                Program Studi
                                <span class="text-danger">*</span>
                            </label>
                            <div class="input-group-modern">
                                <select name="program_studi_id"
                                    class="form-control-modern @error('program_studi_id') is-invalid @enderror"
                                    id="programStudi">
                                    <option value="">-- Pilih Program Studi --</option>
                                    @foreach ($programStudi->groupBy('jenjang') as $jenjang => $list)
                                        <optgroup label="{{ $jenjang }}">
                                            @foreach ($list as $prodi)
                                                <option value="{{ $prodi->id }}"
                                                    {{ old('program_studi_id', $pendaftaran->program_studi_id) == $prodi->id ? 'selected' : '' }}>
                                                    {{ $prodi->nama }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                                <div class="input-focus-border"></div>
                            </div>
                            <div class="form-hint">
                                <i class="bi bi-info-circle"></i>
                                Pilih program studi yang Anda ambil
                            </div>
                            @error('program_studi_id')
                                <div class="invalid-feedback-modern">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Asal Daerah & Provinsi -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label-modern">
                                    <i class="bi bi-geo-alt"></i>
                                    Asal Daerah
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group-modern">
                                    <input type="text" name="asal_daerah"
                                        class="form-control-modern @error('asal_daerah') is-invalid @enderror"
                                        value="{{ old('asal_daerah', $pendaftaran->asal_daerah) }}"
                                        placeholder="Contoh: Bandung" id="asalDaerah">
                                    <div class="input-focus-border"></div>
                                </div>
                                @error('asal_daerah')
                                    <div class="invalid-feedback-modern">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-modern">
                                    <i class="bi bi-map"></i>
                                    Provinsi
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group-modern">
                                    <input type="text" name="provinsi"
                                        class="form-control-modern @error('provinsi') is-invalid @enderror"
                                        value="{{ old('provinsi', $pendaftaran->provinsi) }}"
                                        placeholder="Contoh: Jawa Barat" id="provinsi">
                                    <div class="input-focus-border"></div>
                                </div>
                                @error('provinsi')
                                    <div class="invalid-feedback-modern">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Status KIP -->
                        <div class="kip-card mb-4">
                            <div class="kip-header">
                                <i class="bi bi-award"></i>
                                <span class="fw-semibold">Status KIP (Kartu Indonesia Pintar)</span>
                            </div>
                            <div class="kip-body">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="is_kip" value="1"
                                        id="isKip" {{ old('is_kip', $pendaftaran->is_kip) ? 'checked' : '' }}
                                        onchange="toggleKip(this)">
                                    <label class="form-check-label fw-semibold" for="isKip">
                                        Saya adalah penerima KIP Kuliah
                                    </label>
                                </div>
                                <div id="kipSection"
                                    style="display: {{ old('is_kip', $pendaftaran->is_kip) ? 'block' : 'none' }};">
                                    <div class="alert-kip">
                                        <i class="bi bi-info-circle-fill me-2"></i>
                                        Mahasiswa KIP tidak melalui proses perhitungan SAW. Nominal UKT ditentukan
                                        berdasarkan akreditasi program studi.
                                    </div>
                                    <div class="form-group-modern mt-3">
                                        <label class="form-label-modern small">
                                            <i class="bi bi-card-text"></i>
                                            Nomor KIP
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group-modern">
                                            <input type="text" name="no_kip" id="noKip"
                                                class="form-control-modern @error('no_kip') is-invalid @enderror"
                                                value="{{ old('no_kip', $pendaftaran->no_kip) }}"
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

                        <!-- Action Buttons -->
                        <div class="action-buttons">
                            <button type="submit" class="btn-submit" id="submitBtn">
                                <i class="bi bi-save"></i>
                                <span>Update Pendaftaran</span>
                            </button>
                            <a href="{{ route('mahasiswa.pendaftaran.show', $pendaftaran) }}" class="btn-cancel">
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

        .no-pendaftaran-badge {
            background: #f1f5f9;
            padding: 6px 14px;
            border-radius: 40px;
            font-size: 0.75rem;
            font-weight: 600;
            color: #475569;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-family: monospace;
        }

        /* Alert Premium */
        .alert-premium {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            padding: 16px 20px;
            border-radius: 20px;
            background: white;
            transition: all 0.3s ease;
        }

        .alert-warning-premium {
            background: linear-gradient(135deg, #fffbeb, #fef3c7);
            border-left: 4px solid #f59e0b;
        }

        .alert-icon {
            width: 40px;
            height: 40px;
            background: rgba(0, 0, 0, 0.05);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .alert-warning-premium .alert-icon {
            color: #f59e0b;
        }

        .alert-content {
            flex: 1;
        }

        .alert-title {
            font-weight: 700;
            font-size: 0.85rem;
            color: #92400e;
            margin-bottom: 4px;
        }

        .alert-message {
            font-size: 0.75rem;
            color: #64748b;
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

        .input-group-prefix {
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            border-right: none;
            padding: 12px 16px;
            border-radius: 16px 0 0 16px;
            color: #64748b;
            font-weight: 600;
        }

        .form-control-modern.with-prefix {
            border-radius: 0 16px 16px 0;
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

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 12px;
            margin-top: 16px;
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

            .action-buttons {
                flex-direction: column;
            }

            .alert-premium {
                flex-direction: column;
                text-align: center;
            }

            .alert-icon {
                margin: 0 auto;
            }
        }

        @media (max-width: 480px) {
            .form-control-modern {
                padding: 10px 14px;
                font-size: 0.85rem;
            }

            .input-group-prefix {
                padding: 10px 14px;
            }

            .no-pendaftaran-badge {
                font-size: 0.7rem;
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

        /* Additional styles */
        .text-success {
            color: #10b981 !important;
        }

        .text-warning {
            color: #f59e0b !important;
        }

        .text-danger {
            color: #ef4444 !important;
        }

        .text-muted {
            color: #94a3b8 !important;
        }

        .fw-semibold {
            font-weight: 600;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const pendaftaranId = {{ $pendaftaran->id }};

            // NIK validation with duplicate check
            const nikInput = document.getElementById('nikInput');
            const nikFeedback = document.getElementById('nikFeedback');
            let nikCheckTimeout;
            let originalNik = nikInput?.value || '';

            if (nikInput) {
                nikInput.addEventListener('input', function() {
                    let value = this.value.replace(/[^0-9]/g, '');
                    this.value = value.slice(0, 16);

                    clearTimeout(nikCheckTimeout);

                    if (!/^[0-9]{16}$/.test(this.value)) {
                        if (this.value.length > 0 && this.value.length < 16) {
                            nikFeedback.innerHTML =
                                `<i class="bi bi-info-circle"></i> NIK harus 16 digit (${this.value.length}/16)`;
                            nikFeedback.className = 'form-hint text-warning';
                        } else if (this.value.length === 0) {
                            nikFeedback.innerHTML =
                                '<i class="bi bi-info-circle"></i> Masukkan 16 digit NIK sesuai KTP';
                            nikFeedback.className = 'form-hint';
                        } else {
                            nikFeedback.innerHTML =
                                '<i class="bi bi-exclamation-triangle"></i> NIK harus 16 digit angka';
                            nikFeedback.className = 'form-hint text-danger';
                        }
                        nikInput.classList.remove('is-invalid');
                        return;
                    }

                    // Skip check if value is same as original
                    if (this.value === originalNik) {
                        nikFeedback.innerHTML =
                            '<i class="bi bi-check-circle"></i> NIK tersedia (tidak berubah).';
                        nikFeedback.className = 'form-hint text-success';
                        nikInput.classList.remove('is-invalid');
                        return;
                    }

                    nikFeedback.innerHTML =
                        '<i class="bi bi-hourglass-split"></i> Mengecek ketersediaan NIK...';
                    nikFeedback.className = 'form-hint text-muted';

                    nikCheckTimeout = setTimeout(async () => {
                        try {
                            const res = await fetch(
                                `/check/nik?nik=${this.value}&ignore_id=${pendaftaranId}`);
                            const data = await res.json();

                            if (data.exists) {
                                nikFeedback.innerHTML =
                                    '<i class="bi bi-x-circle"></i> NIK sudah terpakai oleh mahasiswa lain.';
                                nikFeedback.className = 'form-hint text-danger fw-semibold';
                                nikInput.classList.add('is-invalid');
                            } else {
                                nikFeedback.innerHTML =
                                    '<i class="bi bi-check-circle"></i> NIK tersedia.';
                                nikFeedback.className = form - hint text - success ';
                                nikInput.classList.remove('is-invalid');
                            }
                        } catch (error) {
                            console.error('Error checking NIK:', error);
                            nikFeedback.innerHTML =
                                '<i class="bi bi-info-circle"></i> Gagal mengecek NIK, coba lagi.';
                            nikFeedback.className = 'form-hint text-warning';
                        }
                    }, 500);
                });
            }

            // No HP validation with duplicate check
            const noHpInput = document.getElementById('noHpInput');
            const noHpFeedback = document.getElementById('noHpFeedback');
            let noHpCheckTimeout;
            let originalNoHp = noHpInput?.value || '';

            if (noHpInput) {
                noHpInput.addEventListener('input', function() {
                    let value = this.value.replace(/[^0-9]/g, '');
                    if (value.startsWith('0')) value = value.substring(1);
                    this.value = value;

                    clearTimeout(noHpCheckTimeout);

                    if (value.length < 8) {
                        if (value.length > 0) {
                            noHpFeedback.innerHTML =
                                `<i class="bi bi-info-circle"></i> Minimal 8 digit (${value.length}/8)`;
                            noHpFeedback.className = 'form-hint text-warning';
                        } else {
                            noHpFeedback.innerHTML =
                                '<i class="bi bi-info-circle"></i> Nomor aktif untuk komunikasi penting';
                            noHpFeedback.className = 'form-hint';
                        }
                        noHpInput.classList.remove('is-invalid');
                        return;
                    }

                    // Skip check if value is same as original
                    if (value === originalNoHp) {
                        noHpFeedback.innerHTML =
                            '<i class="bi bi-check-circle"></i> Nomor telepon tersedia (tidak berubah).';
                        noHpFeedback.className = 'form-hint text-success';
                        noHpInput.classList.remove('is-invalid');
                        return;
                    }

                    noHpFeedback.innerHTML =
                        '<i class="bi bi-hourglass-split"></i> Mengecek ketersediaan nomor...';
                    noHpFeedback.className = 'form-hint text-muted';

                    noHpCheckTimeout = setTimeout(async () => {
                        try {
                            const res = await fetch(
                                `/check/no-hp?no_hp=${encodeURIComponent(value)}&ignore_id=${pendaftaranId}`
                                );
                            const data = await res.json();

                            if (data.exists) {
                                noHpFeedback.innerHTML =
                                    '<i class="bi bi-x-circle"></i> Nomor telepon sudah terpakai oleh mahasiswa lain.';
                                noHpFeedback.className = 'form-hint text-danger fw-semibold';
                                noHpInput.classList.add('is-invalid');
                            } else {
                                noHpFeedback.innerHTML =
                                    '<i class="bi bi-check-circle"></i> Nomor telepon tersedia.';
                                noHpFeedback.className = 'form-hint text-success';
                                noHpInput.classList.remove('is-invalid');
                            }
                        } catch (error) {
                            console.error('Error checking No HP:', error);
                            noHpFeedback.innerHTML =
                                '<i class="bi bi-info-circle"></i> Gagal mengecek nomor, coba lagi.';
                            noHpFeedback.className = 'form-hint text-warning';
                        }
                    }, 500);
                });
            }

            // Form validation before submit
            const form = document.getElementById('editForm');
            const submitBtn = document.getElementById('submitBtn');

            if (form && submitBtn) {
                form.addEventListener('submit', function(e) {
                    const nama = document.getElementById('namaLengkap')?.value.trim();
                    const nik = nikInput?.value;
                    const noHp = noHpInput?.value;
                    const prodi = document.getElementById('programStudi')?.value;
                    const asalDaerah = document.getElementById('asalDaerah')?.value.trim();
                    const provinsi = document.getElementById('provinsi')?.value.trim();
                    const isKipChecked = document.getElementById('isKip')?.checked;
                    const noKip = document.getElementById('noKip')?.value.trim();

                    const isNikDuplicate = nik !== originalNik && nikFeedback?.textContent.includes(
                        'sudah terpakai');
                    const isNoHpDuplicate = noHp !== originalNoHp && noHpFeedback?.textContent.includes(
                        'sudah terpakai');

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
                        alert('⚠️ NIK sudah terdaftar! Gunakan NIK lain.');
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
                        alert('⚠️ Nomor HP sudah terdaftar! Gunakan nomor lain.');
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
                    submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> <span>Menyimpan...</span>';
                    return true;
                });
            }
        });

        function toggleKip(checkbox) {
            const kipSection = document.getElementById('kipSection');
            const noKip = document.getElementById('noKip');
            if (checkbox.checked) {
                kipSection.style.display = 'block';
                if (noKip) noKip.required = true;
            } else {
                kipSection.style.display = 'none';
                if (noKip) {
                    noKip.required = false;
                    noKip.value = '';
                }
            }
        }
    </script>
@endsection
