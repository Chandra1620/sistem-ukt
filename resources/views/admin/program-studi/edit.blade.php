@extends('layouts.app')
@section('title', 'Edit Program Studi')
@section('page-title', 'Edit Program Studi')

@section('content')
    <div class="container-fluid px-2 px-sm-3 px-lg-4">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8 col-xl-7">
                <!-- Header -->
                <div class="mb-4">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <div class="rounded-3 d-flex align-items-center justify-content-center"
                            style="background: #eef2ff; width: 48px; height: 48px;">
                            <i class="bi bi-building fs-3 text-primary"></i>
                        </div>
                        <div>
                            <h1 class="h4 fw-bold mb-0" style="color: #1a1a2e;">Edit Program Studi</h1>
                            <p class="text-muted small mb-0 mt-1">Mengedit data: <span
                                    class="fw-semibold text-primary">{{ $programStudi->nama }}</span></p>
                        </div>
                    </div>
                </div>

                <!-- Warning Alert for Existing Pendaftar -->
                @if ($programStudi->pendaftaran()->count() > 0)
                    <div class="alert border-0 mb-4 p-3 rounded-3"
                        style="background: #fef3c7; border-left: 4px solid #f59e0b;">
                        <div class="d-flex gap-2">
                            <i class="bi bi-exclamation-triangle-fill text-warning"></i>
                            <div class="small text-dark">
                                <strong class="fw-semibold">Perhatian!</strong><br>
                                Program studi ini memiliki <strong>{{ $programStudi->pendaftaran()->count() }}
                                    pendaftar</strong> terdaftar.
                                Perubahan pada program studi akan mempengaruhi data pendaftaran yang terkait.
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Form Card -->
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4 p-sm-5">
                        <form action="{{ route('admin.program-studi.update', $programStudi) }}" method="POST"
                            id="formEditProgramStudi">
                            @csrf
                            @method('PUT')

                            <!-- Nama Program Studi -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold small mb-2">
                                    <i class="bi bi-book text-primary me-1"></i> Nama Program Studi
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="nama" id="namaProdi"
                                    class="form-control rounded-3 @error('nama') is-invalid @enderror"
                                    value="{{ old('nama', $programStudi->nama) }}" placeholder="Contoh: Teknik Informatika"
                                    style="padding: 11px 16px; border-color: #e2e8f0;">
                                <div class="small text-muted mt-1">
                                    <i class="bi bi-info-circle me-1"></i>Nama lengkap program studi sesuai dengan izin
                                    operasional
                                </div>
                                @error('nama')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Kode Program Studi -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold small mb-2">
                                    <i class="bi bi-upc-scan text-primary me-1"></i> Kode Program Studi
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="kode" id="kodeProdi"
                                    class="form-control rounded-3 @error('kode') is-invalid @enderror"
                                    value="{{ old('kode', $programStudi->kode) }}" placeholder="Contoh: TI, IF"
                                    style="padding: 11px 16px; border-color: #e2e8f0; text-transform: uppercase;">
                                <div class="small text-muted mt-1">
                                    <i class="bi bi-exclamation-triangle me-1 text-warning"></i>Kode unik, akan otomatis
                                    diubah menjadi huruf besar
                                </div>
                                @error('kode')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Jenjang Pendidikan -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold small mb-2">
                                    <i class="bi bi-mortarboard text-primary me-1"></i> Jenjang Pendidikan
                                    <span class="text-danger">*</span>
                                </label>
                                <select name="jenjang" id="jenjangSelect"
                                    class="form-select rounded-3 @error('jenjang') is-invalid @enderror"
                                    style="padding: 11px 16px; border-color: #e2e8f0;">
                                    <option value="D3"
                                        {{ old('jenjang', $programStudi->jenjang) === 'D3' ? 'selected' : '' }}>D3 (Diploma
                                        3)</option>
                                    <option value="D4"
                                        {{ old('jenjang', $programStudi->jenjang) === 'D4' ? 'selected' : '' }}>D4 (Diploma
                                        4)</option>
                                </select>
                                <div class="small text-muted mt-1">
                                    <i class="bi bi-info-circle me-1"></i>Pilih jenjang pendidikan yang sesuai
                                </div>
                                @error('jenjang')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Akreditasi -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold small mb-2">
                                    <i class="bi bi-award text-primary me-1"></i> Akreditasi
                                </label>
                                <select name="akreditasi_id" id="akreditasiSelect"
                                    class="form-select rounded-3 @error('akreditasi_id') is-invalid @enderror"
                                    style="padding: 11px 16px; border-color: #e2e8f0;">
                                    <option value="">-- Pilih Akreditasi --</option>
                                    @foreach (\App\Models\Akreditasi::aktif()->orderBy('urutan')->get() as $a)
                                        <option value="{{ $a->id }}"
                                            {{ old('akreditasi_id', $programStudi->akreditasi_id) == $a->id ? 'selected' : '' }}>
                                            {{ $a->nama }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="small text-muted mt-1">
                                    <i class="bi bi-info-circle me-1"></i>Pilih akreditasi program studi
                                </div>
                                @error('akreditasi_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Status Aktif Toggle -->
                            <div class="mb-4 p-4 rounded-4" style="background: #f8fafc;">
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                    <div>
                                        <label class="form-label fw-semibold mb-0">
                                            <i class="bi bi-check-circle text-success me-1"></i> Status Program Studi
                                        </label>
                                        <div class="small text-muted mt-0">Tentukan apakah program studi ini aktif atau
                                            tidak</div>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="aktif" value="1"
                                            id="aktifSwitch" style="width: 3rem; height: 1.5rem; cursor: pointer;"
                                            {{ old('aktif', $programStudi->aktif) ? 'checked' : '' }}>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <span id="statusText"
                                        class="badge {{ old('aktif', $programStudi->aktif) ? 'bg-success' : 'bg-secondary' }} rounded-pill px-3 py-2">
                                        <i
                                            class="bi {{ old('aktif', $programStudi->aktif) ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }} me-1"></i>
                                        {{ old('aktif', $programStudi->aktif) ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                    <small class="text-muted ms-2">
                                        <i class="bi bi-info-circle"></i> Program studi aktif akan muncul di form
                                        pendaftaran
                                    </small>
                                </div>
                            </div>

                            <!-- Informasi Data -->
                            <div class="mb-4 p-3 rounded-3" style="background: #f1f5f9;">
                                <h6 class="fw-semibold mb-2 small">
                                    <i class="bi bi-clock-history me-1 text-warning"></i> Informasi Data
                                </h6>
                                <div class="row small g-2">
                                    <div class="col-6">
                                        <span class="text-muted">Dibuat:</span>
                                        <span
                                            class="fw-semibold ms-2 text-dark">{{ $programStudi->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                    <div class="col-6">
                                        <span class="text-muted">Terakhir update:</span>
                                        <span
                                            class="fw-semibold ms-2 text-dark">{{ $programStudi->updated_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Preview Card -->
                            <div class="mb-4 p-3 rounded-3" style="background: #f1f5f9;">
                                <h6 class="fw-semibold mb-2 small">
                                    <i class="bi bi-eye text-primary me-1"></i> Preview Perubahan
                                </h6>
                                <div class="row small g-2">
                                    <div class="col-4 col-sm-3 text-muted">Nama:</div>
                                    <div class="col-8 col-sm-9 fw-semibold text-dark" id="previewNama">
                                        {{ $programStudi->nama }}</div>

                                    <div class="col-4 col-sm-3 text-muted">Kode:</div>
                                    <div class="col-8 col-sm-9 fw-semibold text-dark" id="previewKode">
                                        {{ $programStudi->kode }}</div>

                                    <div class="col-4 col-sm-3 text-muted">Jenjang:</div>
                                    <div class="col-8 col-sm-9 fw-semibold text-dark" id="previewJenjang">
                                        {{ $programStudi->jenjang === 'D3' ? 'D3 (Diploma 3)' : 'D4 (Diploma 4)' }}
                                    </div>

                                    <div class="col-4 col-sm-3 text-muted">Akreditasi:</div>
                                    <div class="col-8 col-sm-9 fw-semibold text-dark" id="previewAkreditasi">
                                        @php
                                            $akreditasi = \App\Models\Akreditasi::find($programStudi->akreditasi_id);
                                        @endphp
                                        {{ $akreditasi ? $akreditasi->nama : '-' }}
                                    </div>

                                    <div class="col-4 col-sm-3 text-muted">Status:</div>
                                    <div class="col-8 col-sm-9 fw-semibold text-dark" id="previewStatus">
                                        {{ $programStudi->aktif ? 'Aktif' : 'Nonaktif' }}
                                    </div>
                                </div>
                            </div>

                            <!-- Info Alert -->
                            <div class="alert border-0 rounded-3 mb-4 p-3" style="background: #eef2ff;">
                                <div class="d-flex gap-2">
                                    <i class="bi bi-info-circle-fill text-primary"></i>
                                    <div class="small text-dark">
                                        <strong class="fw-semibold">Informasi Penting</strong><br>
                                        Program studi yang sudah memiliki pendaftar tidak dapat dihapus.
                                        Perubahan nama/kode akan langsung mempengaruhi data pendaftaran yang terkait.
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex gap-3 mt-4 pt-2">
                                <a href="{{ route('admin.program-studi.index') }}"
                                    class="btn btn-light flex-grow-1 rounded-3 py-2"
                                    style="background: #f1f5f9; border: none;">
                                    <i class="bi bi-arrow-left me-2"></i>Batal
                                </a>
                                <button type="submit" class="btn btn-primary flex-grow-1 rounded-3 py-2" id="submitBtn">
                                    <i class="bi bi-save me-2"></i>Update
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Custom Styles */
        .form-control,
        .form-select {
            transition: all 0.2s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
        }

        .form-check-input:checked {
            background-color: #10b981;
            border-color: #10b981;
        }

        .form-check-input:focus {
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
            border-color: #10b981;
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

        .btn-light:hover {
            background: #e2e8f0 !important;
            transform: translateY(-2px);
        }

        /* Preview Animation */
        @keyframes fadeSlide {
            from {
                opacity: 0;
                transform: translateY(-5px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .preview-updated {
            animation: fadeSlide 0.2s ease;
        }

        /* Highlight Changed */
        .text-changed {
            color: #f59e0b !important;
            font-weight: bold;
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

        /* Responsive */
        @media (max-width: 768px) {
            .card-body {
                padding: 1.5rem !important;
            }

            .btn-primary,
            .btn-light {
                padding: 0.5rem;
                font-size: 0.85rem;
            }
        }

        @media (max-width: 576px) {
            .card-body {
                padding: 1.25rem !important;
            }

            .d-flex.gap-3 {
                flex-direction: column;
            }

            .d-flex.gap-3 .btn {
                width: 100%;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get form elements
            const namaInput = document.querySelector('#namaProdi');
            const kodeInput = document.querySelector('#kodeProdi');
            const jenjangSelect = document.querySelector('#jenjangSelect');
            const akreditasiSelect = document.querySelector('#akreditasiSelect');
            const aktifSwitch = document.querySelector('#aktifSwitch');
            const statusText = document.querySelector('#statusText');

            // Preview elements
            const previewNama = document.querySelector('#previewNama');
            const previewKode = document.querySelector('#previewKode');
            const previewJenjang = document.querySelector('#previewJenjang');
            const previewAkreditasi = document.querySelector('#previewAkreditasi');
            const previewStatus = document.querySelector('#previewStatus');

            // Store original values
            const originalNama = namaInput?.value || '';
            const originalKode = kodeInput?.value || '';
            const originalJenjang = jenjangSelect?.value || '';
            const originalAkreditasi = akreditasiSelect?.value || '';
            const originalStatus = aktifSwitch?.checked || false;

            // Jenjang name mapping
            const jenjangNames = {
                'D3': 'D3 (Diploma 3)',
                'D4': 'D4 (Diploma 4)'
            };

            // Helper function to update preview with animation
            function updatePreview(element, value, isChanged = false) {
                if (element) {
                    element.textContent = value || '—';
                    if (isChanged) {
                        element.classList.add('text-changed');
                    } else {
                        element.classList.remove('text-changed');
                    }
                    element.classList.add('preview-updated');
                    setTimeout(() => element.classList.remove('preview-updated'), 200);
                }
            }

            // Update nama preview
            if (namaInput && previewNama) {
                namaInput.addEventListener('input', function() {
                    updatePreview(previewNama, this.value || '—', this.value !== originalNama);
                });
            }

            // Update kode preview with uppercase transformation
            if (kodeInput && previewKode) {
                kodeInput.addEventListener('input', function() {
                    this.value = this.value.toUpperCase();
                    updatePreview(previewKode, this.value, this.value !== originalKode);
                });
            }

            // Update jenjang preview
            if (jenjangSelect && previewJenjang) {
                jenjangSelect.addEventListener('change', function() {
                    const displayText = jenjangNames[this.value] || this.value || '—';
                    updatePreview(previewJenjang, displayText, this.value !== originalJenjang);
                });
            }

            // Update akreditasi preview
            if (akreditasiSelect && previewAkreditasi) {
                akreditasiSelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    const displayText = selectedOption?.text?.replace(/\(KIP:.*\)/, '').trim() || '—';
                    updatePreview(previewAkreditasi, displayText, this.value !== originalAkreditasi);
                });
            }

            // Update status preview and badge
            if (aktifSwitch && statusText && previewStatus) {
                function updateStatusDisplay() {
                    const isActive = aktifSwitch.checked;
                    const statusLabel = isActive ? 'Aktif' : 'Nonaktif';
                    const statusIcon = isActive ? 'bi-check-circle-fill' : 'bi-x-circle-fill';
                    const statusClass = isActive ? 'bg-success' : 'bg-secondary';

                    statusText.className = `badge ${statusClass} rounded-pill px-3 py-2`;
                    statusText.innerHTML = `<i class="${statusIcon} me-1"></i> ${statusLabel}`;
                    updatePreview(previewStatus, statusLabel, isActive !== originalStatus);
                }

                aktifSwitch.addEventListener('change', updateStatusDisplay);
            }

            // Auto capitalize nama field on blur
            if (namaInput) {
                namaInput.addEventListener('blur', function() {
                    let words = this.value.toLowerCase().split(' ');
                    let capitalized = words.map(word =>
                        word.charAt(0).toUpperCase() + word.slice(1)
                    ).join(' ');
                    this.value = capitalized;
                    updatePreview(previewNama, this.value, this.value !== originalNama);
                });
            }

            // Form validation
            const form = document.getElementById('formEditProgramStudi');
            const submitBtn = document.getElementById('submitBtn');

            if (form) {
                form.addEventListener('submit', function(e) {
                    const currentNama = namaInput?.value.trim() || '';
                    const currentKode = kodeInput?.value.trim() || '';
                    const currentJenjang = jenjangSelect?.value || '';
                    const currentAkreditasi = akreditasiSelect?.value || '';
                    const currentStatus = aktifSwitch?.checked || false;

                    const hasChanges = (currentNama !== originalNama) ||
                        (currentKode !== originalKode) ||
                        (currentJenjang !== originalJenjang) ||
                        (currentAkreditasi !== originalAkreditasi) ||
                        (currentStatus !== originalStatus);

                    if (!currentNama) {
                        e.preventDefault();
                        alert('⚠️ Nama program studi wajib diisi!');
                        namaInput?.focus();
                        return false;
                    }

                    if (!currentKode) {
                        e.preventDefault();
                        alert('⚠️ Kode program studi wajib diisi!');
                        kodeInput?.focus();
                        return false;
                    }

                    if (!currentJenjang) {
                        e.preventDefault();
                        alert('⚠️ Jenjang program studi wajib dipilih!');
                        jenjangSelect?.focus();
                        return false;
                    }

                    if (!hasChanges) {
                        e.preventDefault();
                        swalConfirm('Tidak ada perubahan data. Apakah Anda tetap ingin menyimpan?').then(
                            function(ok) {
                                if (ok) e.target.submit();
                            });
                    }

                    // Show loading state
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Menyimpan...';
                    return true;
                });
            }
        });
    </script>
@endsection
