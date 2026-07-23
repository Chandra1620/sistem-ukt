@extends('layouts.app')
@section('title', 'Edit Jenis Dokumen')
@section('page-title', 'Edit Jenis Dokumen')

@section('content')
    <div class="container-fluid px-2 px-sm-3 px-lg-4">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8 col-xl-7">
                <!-- Header -->
                <div class="mb-4">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <div class="rounded-3 d-flex align-items-center justify-content-center"
                            style="background: #eef2ff; width: 48px; height: 48px;">
                            <i class="bi bi-file-earmark-text fs-3 text-primary"></i>
                        </div>
                        <div>
                            <h1 class="h4 fw-bold mb-0" style="color: #1a1a2e;">Edit Jenis Dokumen</h1>
                            <p class="text-muted small mb-0 mt-1">Mengedit data: <span
                                    class="fw-semibold text-primary">{{ $jenisDokumen->nama }}</span></p>
                        </div>
                    </div>
                </div>

                <!-- Warning Alert if document has uploads -->
                @php
                    $uploadCount = $jenisDokumen->dokumen()->count();
                @endphp
                @if ($uploadCount > 0)
                    <div class="alert border-0 mb-4 p-3 rounded-3"
                        style="background: #fef3c7; border-left: 4px solid #f59e0b;">
                        <div class="d-flex gap-2">
                            <i class="bi bi-exclamation-triangle-fill text-warning"></i>
                            <div class="small text-dark">
                                <strong class="fw-semibold">Perhatian!</strong><br>
                                Jenis dokumen ini memiliki <strong>{{ $uploadCount }} file upload</strong> dari mahasiswa.
                                Perubahan pada dokumen ini akan mempengaruhi data upload yang terkait.
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Form Card -->
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4 p-sm-5">
                        <form action="{{ route('admin.jenis-dokumen.update', $jenisDokumen) }}" method="POST"
                            id="formEditJenisDokumen">
                            @csrf
                            @method('PUT')

                            <!-- Nama Dokumen -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold small mb-2">
                                    <i class="bi bi-file-earmark-text text-primary me-1"></i> Nama Dokumen
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="nama" id="namaDokumen"
                                    class="form-control rounded-3 @error('nama') is-invalid @enderror"
                                    value="{{ old('nama', $jenisDokumen->nama) }}"
                                    placeholder="Contoh: Kartu Keluarga, Ijazah, Transkrip Nilai"
                                    style="padding: 11px 16px; border-color: #e2e8f0;">
                                <div class="small text-muted mt-1">
                                    <i class="bi bi-info-circle me-1"></i>Nama dokumen yang jelas akan memudahkan mahasiswa
                                </div>
                                @error('nama')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Keterangan -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold small mb-2">
                                    <i class="bi bi-chat-text text-primary me-1"></i> Keterangan
                                    <span class="text-muted fw-normal ms-1">(Opsional)</span>
                                </label>
                                <textarea name="keterangan" id="keterangan" class="form-control rounded-3 @error('keterangan') is-invalid @enderror"
                                    rows="3" placeholder="Contoh: File PDF maksimal 2MB, Surat keterangan aktif kuliah"
                                    style="padding: 11px 16px; border-color: #e2e8f0;">{{ old('keterangan', $jenisDokumen->keterangan) }}</textarea>
                                <div class="small text-muted mt-1">
                                    <i class="bi bi-lightbulb me-1 text-warning"></i>Berikan petunjuk teknis untuk mahasiswa
                                </div>
                                @error('keterangan')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Urutan -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold small mb-2">
                                    <i class="bi bi-sort-numeric-down text-primary me-1"></i> Urutan Tampil
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="number" name="urutan" id="urutan"
                                    class="form-control rounded-3 @error('urutan') is-invalid @enderror"
                                    value="{{ old('urutan', $jenisDokumen->urutan) }}" min="0" step="1"
                                    style="padding: 11px 16px; border-color: #e2e8f0;">
                                <div class="small text-muted mt-1">
                                    <i class="bi bi-arrow-down-short text-success me-1"></i>
                                    Urutan terkecil akan tampil pertama di form mahasiswa
                                </div>
                                @error('urutan')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Status Wajib -->
                            <div class="mb-4 p-4 rounded-4" style="background: #fef2f2;">
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                    <div>
                                        <label class="form-label fw-semibold mb-0">
                                            <i class="bi bi-star-fill text-danger me-1"></i> Status Dokumen
                                        </label>
                                        <div class="small text-muted mt-0">Tentukan apakah dokumen ini wajib atau opsional
                                        </div>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="wajib" value="1"
                                            id="wajibSwitch" style="width: 3rem; height: 1.5rem; cursor: pointer;"
                                            {{ old('wajib', $jenisDokumen->wajib) ? 'checked' : '' }}>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <span id="wajibText"
                                        class="badge {{ old('wajib', $jenisDokumen->wajib) ? 'bg-danger' : 'bg-secondary' }} rounded-pill px-3 py-2">
                                        <i
                                            class="bi {{ old('wajib', $jenisDokumen->wajib) ? 'bi-star-fill' : 'bi-star' }} me-1"></i>
                                        {{ old('wajib', $jenisDokumen->wajib) ? 'Dokumen Wajib' : 'Dokumen Opsional' }}
                                    </span>
                                    <small class="text-muted ms-2">
                                        <i class="bi bi-info-circle"></i> Dokumen wajib harus diunggah sebelum pendaftaran
                                        disubmit
                                    </small>
                                </div>
                            </div>

                            <!-- Status Aktif -->
                            <div class="mb-4 p-4 rounded-4" style="background: #f8fafc;">
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                    <div>
                                        <label class="form-label fw-semibold mb-0">
                                            <i class="bi bi-check-circle text-success me-1"></i> Status Tampil
                                        </label>
                                        <div class="small text-muted mt-0">Tentukan apakah jenis dokumen ini aktif atau
                                            tidak</div>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="aktif" value="1"
                                            id="aktifSwitch" style="width: 3rem; height: 1.5rem; cursor: pointer;"
                                            {{ old('aktif', $jenisDokumen->aktif) ? 'checked' : '' }}>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <span id="aktifText"
                                        class="badge {{ old('aktif', $jenisDokumen->aktif) ? 'bg-success' : 'bg-secondary' }} rounded-pill px-3 py-2">
                                        <i
                                            class="bi {{ old('aktif', $jenisDokumen->aktif) ? 'bi-check-circle-fill' : 'bi-eye-slash-fill' }} me-1"></i>
                                        {{ old('aktif', $jenisDokumen->aktif) ? 'Tampil di Form' : 'Tidak Tampil' }}
                                    </span>
                                    <small class="text-muted ms-2">
                                        <i class="bi bi-info-circle"></i> Dokumen nonaktif tidak akan muncul di form upload
                                        mahasiswa
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
                                            class="fw-semibold ms-2 text-dark">{{ $jenisDokumen->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                    <div class="col-6">
                                        <span class="text-muted">Terakhir update:</span>
                                        <span
                                            class="fw-semibold ms-2 text-dark">{{ $jenisDokumen->updated_at->diffForHumans() }}</span>
                                    </div>
                                    @if ($uploadCount > 0)
                                        <div class="col-12 mt-1">
                                            <span class="text-muted">Total Upload:</span>
                                            <span class="fw-semibold ms-2 text-warning">{{ $uploadCount }} file</span>
                                        </div>
                                    @endif
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
                                        {{ $jenisDokumen->nama }}</div>

                                    <div class="col-4 col-sm-3 text-muted">Keterangan:</div>
                                    <div class="col-8 col-sm-9 text-muted" id="previewKeterangan">
                                        {{ Str::limit($jenisDokumen->keterangan, 50) ?: '—' }}</div>

                                    <div class="col-4 col-sm-3 text-muted">Urutan:</div>
                                    <div class="col-8 col-sm-9 fw-semibold text-dark" id="previewUrutan">
                                        {{ $jenisDokumen->urutan }}</div>

                                    <div class="col-4 col-sm-3 text-muted">Tipe:</div>
                                    <div class="col-8 col-sm-9 fw-semibold text-dark" id="previewWajib">
                                        {{ $jenisDokumen->wajib ? 'Wajib' : 'Opsional' }}</div>

                                    <div class="col-4 col-sm-3 text-muted">Status:</div>
                                    <div class="col-8 col-sm-9 fw-semibold text-dark" id="previewAktif">
                                        {{ $jenisDokumen->aktif ? 'Tampil di Form' : 'Tidak Tampil' }}</div>
                                </div>
                            </div>

                            <!-- Info Alert -->
                            <div class="alert border-0 rounded-3 mb-4 p-3" style="background: #eef2ff;">
                                <div class="d-flex gap-2">
                                    <i class="bi bi-info-circle-fill text-primary"></i>
                                    <div class="small text-dark">
                                        <strong class="fw-semibold">Informasi Penting</strong><br>
                                        Jenis dokumen yang sudah memiliki file upload dari mahasiswa tidak dapat dihapus.
                                        Perubahan nama/keterangan akan langsung mempengaruhi tampilan di form mahasiswa.
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex gap-3 mt-4 pt-2">
                                <a href="{{ route('admin.jenis-dokumen.index') }}"
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
        .form-control {
            transition: all 0.2s ease;
        }

        .form-control:focus {
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

        /* Highlight for changed values */
        .text-changed {
            color: #f59e0b !important;
            font-weight: bold;
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
            // Store original values for change detection
            const originalData = {
                nama: "{{ addslashes($jenisDokumen->nama) }}",
                keterangan: "{{ addslashes($jenisDokumen->keterangan) }}",
                urutan: "{{ $jenisDokumen->urutan }}",
                wajib: {{ $jenisDokumen->wajib ? 'true' : 'false' }},
                aktif: {{ $jenisDokumen->aktif ? 'true' : 'false' }}
            };

            // Get form elements
            const namaInput = document.querySelector('#namaDokumen');
            const keteranganInput = document.querySelector('#keterangan');
            const urutanInput = document.querySelector('#urutan');
            const wajibSwitch = document.querySelector('#wajibSwitch');
            const aktifSwitch = document.querySelector('#aktifSwitch');

            // Text badges
            const wajibText = document.querySelector('#wajibText');
            const aktifText = document.querySelector('#aktifText');

            // Preview elements
            const previewNama = document.querySelector('#previewNama');
            const previewKeterangan = document.querySelector('#previewKeterangan');
            const previewUrutan = document.querySelector('#previewUrutan');
            const previewWajib = document.querySelector('#previewWajib');
            const previewAktif = document.querySelector('#previewAktif');

            // Helper function to update preview
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
                    updatePreview(previewNama, this.value || '—', this.value !== originalData.nama);
                });
            }

            // Update keterangan preview with truncate
            if (keteranganInput && previewKeterangan) {
                keteranganInput.addEventListener('input', function() {
                    let value = this.value || '—';
                    const truncated = value.length > 50 ? value.substring(0, 50) + '...' : value;
                    updatePreview(previewKeterangan, truncated, this.value !== originalData.keterangan);
                });
            }

            // Update urutan preview
            if (urutanInput && previewUrutan) {
                urutanInput.addEventListener('input', function() {
                    updatePreview(previewUrutan, this.value || '—', this.value != originalData.urutan);
                });
            }

            // Update wajib preview and badge
            if (wajibSwitch && wajibText && previewWajib) {
                function updateWajibDisplay() {
                    const isWajib = wajibSwitch.checked;
                    const label = isWajib ? 'Wajib' : 'Opsional';
                    const icon = isWajib ? 'bi-star-fill' : 'bi-star';
                    const bgClass = isWajib ? 'bg-danger' : 'bg-secondary';
                    const isChanged = isWajib !== originalData.wajib;

                    wajibText.className = `badge ${bgClass} rounded-pill px-3 py-2`;
                    wajibText.innerHTML = `<i class="${icon} me-1"></i> Dokumen ${label}`;
                    updatePreview(previewWajib, label, isChanged);
                }

                wajibSwitch.addEventListener('change', updateWajibDisplay);
            }

            // Update aktif preview and badge
            if (aktifSwitch && aktifText && previewAktif) {
                function updateAktifDisplay() {
                    const isAktif = aktifSwitch.checked;
                    const label = isAktif ? 'Tampil di Form' : 'Tidak Tampil';
                    const icon = isAktif ? 'bi-check-circle-fill' : 'bi-eye-slash-fill';
                    const bgClass = isAktif ? 'bg-success' : 'bg-secondary';
                    const isChanged = isAktif !== originalData.aktif;

                    aktifText.className = `badge ${bgClass} rounded-pill px-3 py-2`;
                    aktifText.innerHTML = `<i class="${icon} me-1"></i> ${label}`;
                    updatePreview(previewAktif, label, isChanged);
                }

                aktifSwitch.addEventListener('change', updateAktifDisplay);
            }

            // Auto capitalize nama field on blur
            if (namaInput) {
                namaInput.addEventListener('blur', function() {
                    let words = this.value.toLowerCase().split(' ');
                    let capitalized = words.map(word =>
                        word.charAt(0).toUpperCase() + word.slice(1)
                    ).join(' ');
                    this.value = capitalized;
                    updatePreview(previewNama, this.value, this.value !== originalData.nama);
                });
            }

            // Form validation
            const form = document.getElementById('formEditJenisDokumen');
            const submitBtn = document.getElementById('submitBtn');

            if (form) {
                form.addEventListener('submit', function(e) {
                    const currentNama = namaInput?.value.trim() || '';
                    const currentUrutan = urutanInput?.value;
                    const hasChanges = (currentNama !== originalData.nama) ||
                        (keteranganInput?.value !== originalData.keterangan) ||
                        (currentUrutan != originalData.urutan) ||
                        (wajibSwitch?.checked !== originalData.wajib) ||
                        (aktifSwitch?.checked !== originalData.aktif);

                    if (!currentNama) {
                        e.preventDefault();
                        alert('Nama dokumen wajib diisi!');
                        namaInput?.focus();
                        return false;
                    }

                    if (currentUrutan === '' || currentUrutan === null) {
                        e.preventDefault();
                        alert('Urutan wajib diisi!');
                        urutanInput?.focus();
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
