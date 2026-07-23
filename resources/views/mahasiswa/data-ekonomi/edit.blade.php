@extends('layouts.app')
@section('title', 'Edit Data Ekonomi')
@section('page-title', 'Edit Data Ekonomi')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">

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
                        <li class="breadcrumb-item">
                            <a href="{{ route('mahasiswa.pendaftaran.show', $pendaftaran) }}"
                                class="text-decoration-none text-muted">
                                <i class="bi bi-eye me-1"></i> Detail
                            </a>
                        </li>
                        <li class="breadcrumb-item active text-primary">
                            <i class="bi bi-pencil-square me-1"></i> Edit Data Ekonomi
                        </li>
                    </ol>
                </nav>

                {{-- Warning Alert if already verified --}}
                @if ($dataEkonomi && $dataEkonomi->status_verifikasi === 'verified')
                    <div class="alert alert-warning border-0 rounded-3 mb-4">
                        <div class="d-flex">
                            <i class="bi bi-exclamation-triangle-fill text-warning me-2 fs-5"></i>
                            <div class="small">
                                <strong>Perhatian!</strong> Data ekonomi Anda sudah <strong>diverifikasi</strong>.
                                Perubahan data memerlukan verifikasi ulang oleh reviewer.
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Main Card --}}
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="fw-bold mb-1">
                                    <i class="bi bi-pencil-square text-warning me-2"></i>
                                    Edit Data Ekonomi
                                </h5>
                                <p class="text-muted small mb-0">Perbarui data ekonomi keluarga Anda</p>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-light text-dark rounded-pill px-3 py-2">
                                    <i class="bi bi-file-text me-1"></i> {{ $pendaftaran->no_pendaftaran }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-4">

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

                        {{-- Progress Steps Sederhana --}}
                        <div class="d-flex justify-content-between mb-5 px-2">
                            <div class="text-center">
                                <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center mx-auto mb-1"
                                    style="width: 32px; height: 32px; font-size: 14px;">
                                    <i class="bi bi-check-lg fs-6"></i>
                                </div>
                                <small class="text-success fw-semibold">Data Diri</small>
                            </div>
                            <div class="flex-grow-1 mx-3 mt-2">
                                <div class="progress" style="height: 2px;">
                                    <div class="progress-bar bg-success" style="width: 50%;"></div>
                                </div>
                            </div>
                            <div class="text-center">
                                <div class="rounded-circle bg-warning text-white d-flex align-items-center justify-content-center mx-auto mb-1"
                                    style="width: 32px; height: 32px; font-size: 14px;">
                                    <i class="bi bi-pencil fs-6"></i>
                                </div>
                                <small class="text-warning fw-semibold">Data Ekonomi</small>
                            </div>
                            <div class="flex-grow-1 mx-3 mt-2">
                                <div class="progress" style="height: 2px;">
                                    <div class="progress-bar bg-secondary" style="width: 0%;"></div>
                                </div>
                            </div>
                            <div class="text-center">
                                <div class="rounded-circle bg-light text-muted d-flex align-items-center justify-content-center mx-auto mb-1"
                                    style="width: 32px; height: 32px; font-size: 14px;">
                                    3
                                </div>
                                <small class="text-muted">Upload Dokumen</small>
                            </div>
                        </div>

                        <form action="{{ route('mahasiswa.data-ekonomi.update', [$pendaftaran, $dataEkonomi]) }}"
                            method="POST" id="formDataEkonomi">
                            @csrf
                            @method('PUT')

                            {{-- Informasi Singkat --}}
                            <div class="alert alert-primary border-0 rounded-3 py-2 mb-4 bg-opacity-10">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-info-circle-fill text-primary me-2"></i>
                                    <small class="text-primary">Pilih opsi yang paling sesuai dengan kondisi ekonomi
                                        keluarga Anda.</small>
                                </div>
                            </div>

                            {{-- Last Updated Info --}}
                            @if ($dataEkonomi && $dataEkonomi->updated_at)
                                <div class="text-end mb-3">
                                    <small class="text-muted">
                                        <i class="bi bi-clock-history me-1"></i>
                                        Terakhir diperbarui: {{ $dataEkonomi->updated_at->format('d/m/Y H:i') }}
                                    </small>
                                </div>
                            @endif

                            {{-- Form Fields --}}
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
                                    $selectedLabel = '';
                                    foreach ($k->subKriteria as $sub) {
                                        if ($oldVal == $sub->id) {
                                            $selectedLabel = $sub->label;
                                            break;
                                        }
                                    }
                                @endphp

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="bi {{ $icon }} text-primary me-1"></i>
                                        {{ $k->nama }}
                                        <span class="text-muted small fw-normal">(Bobot {{ $k->bobot }}%)</span>
                                    </label>
                                    <select name="{{ $field }}"
                                        class="form-select @error($field) is-invalid @enderror select-kriteria" required>
                                        <option value="">-- Pilih {{ $k->nama }} --</option>
                                        @foreach ($k->subKriteria as $sub)
                                            <option value="{{ $sub->id }}"
                                                {{ $oldVal == $sub->id ? 'selected' : '' }}>
                                                {{ $sub->label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($selectedLabel)
                                        <div class="small text-muted mt-1">
                                            <i class="bi bi-dot"></i> Sebelumnya: {{ $selectedLabel }}
                                        </div>
                                    @endif
                                    @error($field)
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endforeach

                            {{-- Info Verifikasi Ulang --}}
                            <div class="alert alert-warning border-0 rounded-3 mt-4">
                                <div class="d-flex">
                                    <i class="bi bi-shield-exclamation fs-5 me-2"></i>
                                    <div>
                                        <strong class="small">Verifikasi Ulang</strong>
                                        <p class="mb-0 small text-muted">Setelah mengubah data, status verifikasi akan
                                            berubah menjadi <strong>pending</strong> dan perlu diverifikasi ulang.</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Buttons --}}
                            <div class="d-flex gap-2 mt-4 pt-2">
                                <a href="{{ route('mahasiswa.pendaftaran.show', $pendaftaran) }}"
                                    class="btn btn-outline-secondary rounded-pill px-4">
                                    <i class="bi bi-arrow-left me-1"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-warning rounded-pill px-5 ms-auto">
                                    <i class="bi bi-save me-1"></i> Update
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
        .form-select,
        .form-control {
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            padding: 10px 14px;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }

        .form-select:focus,
        .form-control:focus {
            border-color: #f59e0b;
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
        }

        .btn {
            font-weight: 500;
            padding: 8px 20px;
            transition: all 0.2s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .card {
            border-radius: 20px;
        }

        .alert {
            border-radius: 12px;
        }

        label {
            font-size: 0.85rem;
            margin-bottom: 6px;
            color: #334155;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('formDataEkonomi');

            if (form) {
                form.addEventListener('submit', function(e) {
                    const selects = document.querySelectorAll('.select-kriteria');
                    let allFilled = true;

                    selects.forEach(select => {
                        if (!select.value) {
                            allFilled = false;
                            select.classList.add('is-invalid');
                        } else {
                            select.classList.remove('is-invalid');
                        }
                    });

                    if (!allFilled) {
                        e.preventDefault();
                        alert('⚠️ Harap lengkapi semua data ekonomi sebelum melanjutkan!');
                    } else {
                        // Optional: confirm if changes were made
                        e.preventDefault();
                        swalConfirm('Apakah Anda yakin ingin menyimpan perubahan data ekonomi ini?').then(
                            function(ok) {
                                if (ok) e.target.submit();
                            });
                    }
                });
            }
        });
    </script>
@endsection
