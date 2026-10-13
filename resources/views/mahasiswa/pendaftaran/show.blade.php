@extends('layouts.app')
@section('title', 'Detail Pendaftaran')
@section('page-title', 'Detail Pendaftaran')

@section('content')
    <div class="row g-4">
        {{-- Info Pendaftaran --}}
        <div class="col-md-4">
            <div class="card premium-card">
                <div class="card-header premium-card-header">
                    <div class="d-flex align-items-center gap-2">
                        <div class="header-icon primary-icon">
                            <i class="bi bi-file-earmark-text-fill"></i>
                        </div>
                        <h6 class="fw-semibold mb-0">Info Pendaftaran</h6>
                    </div>
                    <div class="d-flex gap-2">
                        <span class="status-badge status-{{ $pendaftaran->warna_badge ?? 'secondary' }}">
                            <i class="bi {{ $pendaftaran->status_icon ?? 'bi-circle' }}"></i>
                            {{ $pendaftaran->label_status ?? ucfirst($pendaftaran->status) }}
                        </span>
                        @if ($pendaftaran->is_kip)
                            <span class="status-badge" style="background:#fef3c7;color:#92400e;">
                                <i class="bi bi-award-fill"></i> KIP
                            </span>
                        @endif
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="info-list">
                        <div class="info-item">
                            <div class="info-icon"><i class="bi bi-hash"></i></div>
                            <div class="info-content">
                                <div class="info-label">No. Pendaftaran</div>
                                <div class="info-value no-pendaftaran">{{ $pendaftaran->no_pendaftaran }}</div>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon"><i class="bi bi-person"></i></div>
                            <div class="info-content">
                                <div class="info-label">Nama Lengkap</div>
                                <div class="info-value">{{ $pendaftaran->nama_lengkap }}</div>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon"><i class="bi bi-credit-card"></i></div>
                            <div class="info-content">
                                <div class="info-label">NIK</div>
                                <div class="info-value nik-value">{{ $pendaftaran->nik }}</div>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon"><i class="bi bi-whatsapp"></i></div>
                            <div class="info-content">
                                <div class="info-label">No. HP</div>
                                <div class="info-value">{{ $pendaftaran->no_hp }}</div>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon"><i class="bi bi-building"></i></div>
                            <div class="info-content">
                                <div class="info-label">Program Studi</div>
                                <div class="info-value">{{ $pendaftaran->programStudi->nama ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon"><i class="bi bi-geo-alt"></i></div>
                            <div class="info-content">
                                <div class="info-label">Asal Daerah</div>
                                <div class="info-value">{{ $pendaftaran->asal_daerah }}, {{ $pendaftaran->provinsi }}</div>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon"><i class="bi bi-calendar3"></i></div>
                            <div class="info-content">
                                <div class="info-label">Periode</div>
                                <div class="info-value">{{ $pendaftaran->periode->nama ?? '-' }}</div>
                            </div>
                        </div>
                        @if ($pendaftaran->is_kip)
                            <div class="info-item">
                                <div class="info-icon"><i class="bi bi-award"></i></div>
                                <div class="info-content">
                                    <div class="info-label">Status KIP</div>
                                    <div class="info-value">
                                        <span style="color:#92400e;font-weight:600;">
                                            Penerima KIP Kuliah
                                        </span>
                                    </div>
                                </div>
                            </div>
                            @if ($pendaftaran->no_kip)
                                <div class="info-item">
                                    <div class="info-icon"><i class="bi bi-card-text"></i></div>
                                    <div class="info-content">
                                        <div class="info-label">No. KIP</div>
                                        <div class="info-value font-monospace">{{ $pendaftaran->no_kip }}</div>
                                    </div>
                                </div>
                            @endif
                        @endif
                        <div class="info-item">
                            <div class="info-icon"><i class="bi bi-clock"></i></div>
                            <div class="info-content">
                                <div class="info-label">Tanggal Daftar</div>
                                <div class="info-value">{{ $pendaftaran->created_at->format('d M Y H:i') }} WIB</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top-0 pb-4">
                    <div class="action-footer">
                        @if ($pendaftaran->bisaDiedit())
                            <a href="{{ route('mahasiswa.pendaftaran.edit', $pendaftaran) }}" class="btn-action-edit">
                                <i class="bi bi-pencil-square"></i> Edit Pendaftaran
                            </a>
                        @endif
                        @if (in_array($pendaftaran->status, ['draft', 'ditolak_reviewer']))
                            <form action="{{ route('mahasiswa.pendaftaran.submit', $pendaftaran) }}" method="POST"
                                class="d-inline w-100" onsubmit="return confirmSubmit(event)">
                                @csrf
                                <button type="submit" class="btn-action-submit w-100">
                                    <i class="bi bi-send"></i> Submit Pendaftaran
                                </button>
                            </form>
                        @endif
                        @if ($pendaftaran->hasilBolehDilihat())
                            @if ($pendaftaran->is_kip)
                                <div class="alert border-0 mb-3 p-3 rounded-3" style="background:#fef3c7;">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-award-fill text-warning fs-5"></i>
                                        <div>
                                            <div class="fw-semibold small" style="color:#92400e;">
                                                Mahasiswa KIP Kuliah
                                            </div>
                                            <div class="small" style="color:#78350f;">
                                                UKT ditentukan berdasarkan akreditasi program studi.
                                                Sumber pembiayaan: Pemerintah (KIP).
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif
                        @if ($pendaftaran->hasilBolehDilihat())
                            <a href="{{ route('mahasiswa.hasil-ukt.show', $pendaftaran) }}" class="btn-action-hasil">
                                <i class="bi bi-award"></i> Lihat Hasil UKT
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Riwayat Status Premium --}}
            <div class="card premium-card mt-4">
                <div class="card-header premium-card-header">
                    <div class="d-flex align-items-center gap-2">
                        <div class="header-icon info-icon">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <h6 class="fw-semibold mb-0">Riwayat Status</h6>
                    </div>
                </div>
                <div class="card-body p-0">
                    @forelse($pendaftaran->riwayatStatus->sortByDesc('created_at') as $riwayat)
                        <div class="timeline-item">
                            <div class="timeline-marker {{ $riwayat->warna_badge ?? 'secondary' }}"></div>
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                    <div>
                                        <span class="status-badge status-{{ $riwayat->warna_badge ?? 'secondary' }}">
                                            <i class="bi {{ $riwayat->icon ?? 'bi-circle' }}"></i>
                                            {{ ucfirst(str_replace('_', ' ', $riwayat->status_baru)) }}
                                        </span>
                                        @if ($riwayat->keterangan)
                                            <div class="timeline-comment">
                                                <i class="bi bi-chat-quote"></i>
                                                {{ $riwayat->keterangan }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="timeline-time">
                                        <i class="bi bi-calendar3"></i>
                                        {{ $riwayat->created_at->format('d M Y H:i') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state-small">
                            <i class="bi bi-inbox"></i>
                            <p class="mt-2 mb-0">Belum ada riwayat</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Data Ekonomi & Dokumen --}}
        <div class="col-md-8">
            {{-- Notifikasi Ditolak Premium --}}
            @if ($pendaftaran->status === 'ditolak_reviewer')
                @php
                    $verifikasiTerakhir = $pendaftaran->verifikasi->sortByDesc('verified_at')->first();
                @endphp
                @if ($verifikasiTerakhir)
                    <div class="alert-reject mb-4">
                        <div class="alert-reject-icon">
                            <i class="bi bi-x-circle-fill"></i>
                        </div>
                        <div class="alert-reject-content">
                            <div class="alert-reject-title">Dokumen Ditolak</div>
                            <div class="alert-reject-message">{{ $verifikasiTerakhir->komentar }}</div>
                            <div class="alert-reject-meta">
                                <i class="bi bi-person-circle"></i>
                                {{ $verifikasiTerakhir->reviewer->name ?? 'Reviewer' }}
                                <i class="bi bi-clock ms-2"></i>
                                {{ $verifikasiTerakhir->verified_at->format('d M Y H:i') }}
                            </div>
                            <div class="alert-reject-hint">
                                <i class="bi bi-info-circle"></i> Silakan perbaiki data dan upload ulang dokumen, lalu
                                submit kembali.
                            </div>
                        </div>
                    </div>
                @endif
            @endif

            {{-- Data Ekonomi Premium --}}
            <div class="card premium-card mb-4">
                <div class="card-header premium-card-header">
                    <div class="d-flex align-items-center gap-2">
                        <div class="header-icon success-icon">
                            <i class="bi bi-cash-stack"></i>
                        </div>
                        <h6 class="fw-semibold mb-0">Data Ekonomi</h6>
                    </div>
                    @if ($pendaftaran->bisaDiedit())
                        <a href="{{ $pendaftaran->dataEkonomi ? route('mahasiswa.data-ekonomi.edit', $pendaftaran) : route('mahasiswa.data-ekonomi.create', $pendaftaran) }}"
                            class="btn-add-data">
                            <i class="bi bi-{{ $pendaftaran->dataEkonomi ? 'pencil' : 'plus-circle' }}"></i>
                            {{ $pendaftaran->dataEkonomi ? 'Edit Data' : 'Isi Data' }}
                        </a>
                    @endif
                </div>
                <div class="card-body p-4">
                    @if ($pendaftaran->dataEkonomi)
                        <div class="ekonomi-grid">
                            @php
                                $kriteria = \App\Models\Kriteria::where('periode_id', $pendaftaran->periode_id)
                                    ->with('subKriteria')
                                    ->orderByRaw('CAST(SUBSTRING(kode, 2) AS INTEGER)')
                                    ->get()
                                    ->filter(fn($k) => $k->subKriteria->count() > 0);
                            @endphp

                            @foreach ($kriteria as $k)
                                @php
                                    $field = 'sub_' . strtolower($k->kode) . '_id';
                                    $subId = $pendaftaran->dataEkonomi->$field ?? null;
                                    $sub = $subId ? \App\Models\SubKriteria::find($subId) : null;
                                @endphp
                                <div class="ekonomi-card {{ in_array($k->kode, ['C1', 'C2']) ? 'total' : '' }}">
                                    <div class="ekonomi-label">{{ $k->nama }}</div>
                                    <div class="ekonomi-value">
                                        {{ $sub?->label ?? '-' }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-ekonomi">
                            <div class="empty-icon-small">
                                <i class="bi bi-cash-stack"></i>
                            </div>
                            <p class="mb-2">Data ekonomi belum diisi</p>
                            @if ($pendaftaran->bisaDiedit())
                                <a href="{{ route('mahasiswa.data-ekonomi.create', $pendaftaran) }}"
                                    class="btn-add-ekonomi">
                                    <i class="bi bi-plus-circle"></i> Isi Data Ekonomi
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            {{-- Dokumen Pendukung --}}
            <div class="card premium-card mb-4">
                <div class="card-header premium-card-header">
                    <div class="d-flex align-items-center gap-2">
                        <div class="header-icon warning-icon">
                            <i class="bi bi-folder2-open"></i>
                        </div>
                        <h6 class="fw-semibold mb-0">Dokumen Pendukung</h6>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if ($pendaftaran->bisaDiedit())
                        <div class="upload-form p-4 border-bottom">
                            <form action="{{ route('mahasiswa.dokumen.store', $pendaftaran) }}" method="POST"
                                enctype="multipart/form-data" id="uploadForm">
                                @csrf
                                <div class="row g-2 align-items-end">
                                    <div class="col-md-4">
                                        <select name="jenis_dokumen_id" class="form-control-modern-sm"
                                            id="selectJenisDokumen">
                                            <option value="">-- Pilih Jenis Dokumen --</option>
                                            @foreach (\App\Models\JenisDokumen::whereRaw('"aktif" IS TRUE')->orderBy('urutan')->get() as $jd)
                                                <option value="{{ $jd->id }}"
                                                    data-keterangan="{{ $jd->keterangan }}"
                                                    data-wajib="{{ $jd->wajib ? 'true' : 'false' }}">
                                                    {{ $jd->nama }}
                                                    @if ($jd->wajib)
                                                        ★
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>

                                        <div id="keteranganDokumen" class="mt-2" style="display:none;">
                                            <div class="alert alert-info border-0 py-2 px-3 small">
                                                <i class="bi bi-info-circle me-1"></i>
                                                <span id="keteranganText"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="file-upload-wrapper">
                                            <input type="file" name="file" id="fileInput"
                                                accept=".pdf,.jpg,.jpeg,.png">
                                            <label for="fileInput" class="file-upload-label">
                                                <i class="bi bi-cloud-upload"></i> Pilih File
                                            </label>
                                            <span class="file-name" id="fileName">Belum ada file</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <button type="submit" class="btn-upload w-100">
                                            <i class="bi bi-upload"></i> Upload
                                        </button>
                                    </div>
                                </div>
                                <div class="form-text mt-2">
                                    <i class="bi bi-info-circle"></i> Format: PDF, JPG, PNG. Maks: 2MB
                                </div>
                            </form>
                        </div>
                    @endif

                    @if ($pendaftaran->dokumen->count() > 0)
                        <div class="table-responsive">
                            <table class="table dokumen-table">
                                <thead>
                                    <tr>
                                        <th><i class="bi bi-tag"></i> Jenis</th>
                                        <th><i class="bi bi-file-earmark"></i> Nama File</th>
                                        <th><i class="bi bi-check-circle"></i> Status</th>
                                        <th><i class="bi bi-gear"></i> Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pendaftaran->dokumen as $dok)
                                        <tr class="dokumen-row">
                                            <td data-label="Jenis">
                                                {{ $dok->jenisDokumen?->nama ?? ($dok->jenis ?? '-') }}
                                                @if ($dok->jenisDokumen?->wajib)
                                                    <span class="badge bg-danger ms-1"
                                                        style="font-size:0.6rem;">Wajib</span>
                                                @endif
                                            </td>
                                            <td data-label="Nama File" class="text-muted">{{ $dok->nama_file }}</td>
                                            <td data-label="Status">
                                                <span class="status-badge status-{{ $dok->warna_badge ?? 'secondary' }}">
                                                    <i class="bi {{ $dok->status_icon ?? 'bi-clock' }}"></i>
                                                    {{ $dok->label_status ?? ucfirst($dok->status) }}
                                                </span>
                                            </td>
                                            <td data-label="Aksi">
                                                <div class="action-buttons-small">
                                                    <a href="{{ route('dokumen.preview', $dok) }}" target="_blank"
                                                        class="btn-action-small preview" title="Lihat">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    @if ($pendaftaran->bisaDiedit())
                                                        <form action="{{ route('mahasiswa.dokumen.destroy', $dok) }}"
                                                            method="POST" class="d-inline"
                                                            onsubmit="return confirmDeleteDokumen(event)">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="btn-action-small delete"
                                                                title="Hapus">
                                                                <i class="bi bi-trash3"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-dokumen">
                            <div class="empty-icon-small">
                                <i class="bi bi-folder-x"></i>
                            </div>
                            <p class="mb-0">Belum ada dokumen diupload</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Banding Premium --}}
            @if ($pendaftaran->status === 'final_ukt')
                <div class="card premium-card">
                    <div class="card-header premium-card-header">
                        <div class="d-flex align-items-center gap-2">
                            <div class="header-icon danger-icon">
                                <i class="bi bi-chat-square-text-fill"></i>
                            </div>
                            <h6 class="fw-semibold mb-0">Pengajuan Banding</h6>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        @if ($pendaftaran->banding->where('status', 'diajukan')->count() > 0)
                            <div class="info-banding">
                                <i class="bi bi-info-circle-fill"></i>
                                <span>Banding Anda sedang diproses oleh panitia.</span>
                            </div>
                        @else
                            <form action="{{ route('mahasiswa.banding.store', $pendaftaran) }}" method="POST"
                                id="bandingForm">
                                @csrf
                                <div class="form-group-modern mb-3">
                                    <label class="form-label-modern">
                                        <i class="bi bi-chat-quote"></i>
                                        Alasan Banding
                                    </label>
                                    <textarea name="alasan" class="form-control-modern @error('alasan') is-invalid @enderror" rows="4"
                                        placeholder="Jelaskan alasan pengajuan banding Anda (minimal 20 karakter)..." id="alasanBanding">{{ old('alasan') }}</textarea>
                                    <div class="form-hint">
                                        <i class="bi bi-info-circle"></i>
                                        Minimal 20 karakter
                                    </div>
                                    @error('alasan')
                                        <div class="invalid-feedback-modern">{{ $message }}</div>
                                    @enderror
                                </div>
                                <button type="submit" class="btn-banding" onclick="return confirmBanding(event)">
                                    <i class="bi bi-send"></i> Ajukan Banding
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    <style>
        /* Premium Card */
        .premium-card {
            border: none;
            border-radius: 24px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .premium-card:hover {
            box-shadow: 0 8px 28px rgba(0, 0, 0, 0.08);
        }

        /* Card Header */
        .premium-card-header {
            background: white;
            border-bottom: 1px solid #f0f2f5;
            padding: 16px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }

        .header-icon {
            width: 36px;
            height: 36px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }

        .primary-icon {
            background: #eef2ff;
            color: #3b82f6;
        }

        .info-icon {
            background: #cffafe;
            color: #06b6d4;
        }

        .success-icon {
            background: #d1fae5;
            color: #10b981;
        }

        .warning-icon {
            background: #fed7aa;
            color: #f59e0b;
        }

        .danger-icon {
            background: #fee2e2;
            color: #dc2626;
        }

        /* Status Badge */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 40px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .status-primary {
            background: #dbeafe;
            color: #1e40af;
        }

        .status-success {
            background: #d1fae5;
            color: #065f46;
        }

        .status-warning {
            background: #fed7aa;
            color: #9b3412;
        }

        .status-info {
            background: #cffafe;
            color: #0e7490;
        }

        .status-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-secondary {
            background: #f1f5f9;
            color: #475569;
        }

        /* Info List */
        .info-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .info-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .info-item .info-icon {
            width: 32px;
            height: 32px;
            background: #f8fafc;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #3b82f6;
            font-size: 0.9rem;
            flex-shrink: 0;
        }

        .info-content {
            flex: 1;
        }

        .info-label {
            font-size: 0.65rem;
            text-transform: uppercase;
            color: #94a3b8;
            margin-bottom: 2px;
        }

        .info-value {
            font-size: 0.85rem;
            font-weight: 600;
            color: #1e293b;
        }

        .no-pendaftaran,
        .nik-value {
            font-family: monospace;
            background: #f1f5f9;
            padding: 2px 8px;
            border-radius: 20px;
            display: inline-block;
        }

        /* Action Footer */
        .action-footer {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn-action-edit,
        .btn-action-submit,
        .btn-action-hasil {
            flex: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 40px;
            font-size: 0.75rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-action-edit {
            background: #fef3c7;
            color: #f59e0b;
        }

        .btn-action-edit:hover {
            background: #f59e0b;
            color: white;
            transform: translateY(-2px);
        }

        .btn-action-submit {
            background: #3b82f6;
            color: white;
            border: none;
            cursor: pointer;
        }

        .btn-action-submit:hover {
            background: #2563eb;
            transform: translateY(-2px);
        }

        .btn-action-hasil {
            background: #d1fae5;
            color: #10b981;
        }

        .btn-action-hasil:hover {
            background: #10b981;
            color: white;
            transform: translateY(-2px);
        }

        /* Timeline */
        .timeline-item {
            display: flex;
            gap: 12px;
            padding: 14px 20px;
            border-bottom: 1px solid #f0f2f5;
        }

        .timeline-marker {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-top: 6px;
            position: relative;
            flex-shrink: 0;
        }

        .timeline-marker.primary {
            background: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }

        .timeline-marker.success {
            background: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);
        }

        .timeline-marker.warning {
            background: #f59e0b;
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.2);
        }

        .timeline-marker.danger {
            background: #ef4444;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.2);
        }

        .timeline-marker.info {
            background: #06b6d4;
            box-shadow: 0 0 0 3px rgba(6, 182, 212, 0.2);
        }

        .timeline-marker.secondary {
            background: #94a3b8;
            box-shadow: 0 0 0 3px rgba(148, 163, 184, 0.2);
        }

        .timeline-content {
            flex: 1;
        }

        .timeline-comment {
            font-size: 0.7rem;
            color: #64748b;
            margin-top: 6px;
            background: #f8fafc;
            padding: 6px 10px;
            border-radius: 10px;
        }

        .timeline-time {
            font-size: 0.65rem;
            color: #94a3b8;
            white-space: nowrap;
        }

        /* Alert Reject */
        .alert-reject {
            background: linear-gradient(135deg, #fee2e2, #fecaca);
            border-radius: 20px;
            padding: 16px 20px;
            display: flex;
            gap: 14px;
        }

        .alert-reject-icon {
            font-size: 1.5rem;
            color: #dc2626;
            flex-shrink: 0;
        }

        .alert-reject-content {
            flex: 1;
        }

        .alert-reject-title {
            font-weight: 700;
            font-size: 0.85rem;
            color: #991b1b;
            margin-bottom: 4px;
        }

        .alert-reject-message {
            font-size: 0.75rem;
            color: #7f1d1d;
            margin-bottom: 8px;
        }

        .alert-reject-meta {
            font-size: 0.65rem;
            color: #b91c1c;
            margin-bottom: 8px;
        }

        .alert-reject-hint {
            font-size: 0.7rem;
            background: rgba(0, 0, 0, 0.05);
            padding: 8px 12px;
            border-radius: 10px;
            color: #991b1b;
        }

        /* Ekonomi Grid */
        .ekonomi-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }

        .ekonomi-card {
            background: #f8fafc;
            border-radius: 16px;
            padding: 12px 16px;
            transition: all 0.2s;
        }

        .ekonomi-card:hover {
            background: #f1f5f9;
            transform: translateY(-2px);
        }

        .ekonomi-card.total {
            background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        }

        .ekonomi-label {
            font-size: 0.65rem;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 4px;
        }

        .ekonomi-value {
            font-size: 0.85rem;
            font-weight: 700;
            color: #1e293b;
        }

        /* Buttons */
        .btn-add-data {
            background: transparent;
            border: 1px solid #10b981;
            color: #10b981;
            padding: 5px 12px;
            border-radius: 40px;
            font-size: 0.7rem;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-add-data:hover {
            background: #10b981;
            color: white;
        }

        .btn-add-ekonomi {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #10b981;
            color: white;
            padding: 6px 14px;
            border-radius: 40px;
            font-size: 0.7rem;
            text-decoration: none;
        }

        /* Dokumen Table */
        .dokumen-table {
            width: 100%;
            border-collapse: collapse;
        }

        .dokumen-table thead th {
            background: #f8fafc;
            padding: 12px 16px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            color: #64748b;
            border-bottom: 2px solid #e2e8f0;
        }

        .dokumen-table tbody td {
            padding: 12px 16px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        .action-buttons-small {
            display: flex;
            gap: 6px;
        }

        .btn-action-small {
            width: 28px;
            height: 28px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            font-size: 0.8rem;
            transition: all 0.2s;
            text-decoration: none;
            border: none;
            cursor: pointer;
        }

        .btn-action-small.preview {
            background: #eef2ff;
            color: #3b82f6;
        }

        .btn-action-small.preview:hover {
            background: #3b82f6;
            color: white;
        }

        .btn-action-small.delete {
            background: #fee2e2;
            color: #ef4444;
        }

        .btn-action-small.delete:hover {
            background: #ef4444;
            color: white;
        }

        /* File Upload */
        .file-upload-wrapper {
            position: relative;
        }

        .file-upload-label {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #f1f5f9;
            padding: 8px 16px;
            border-radius: 40px;
            font-size: 0.7rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .file-upload-label:hover {
            background: #e2e8f0;
        }

        #fileInput {
            display: none;
        }

        .file-name {
            font-size: 0.7rem;
            color: #64748b;
            margin-left: 8px;
        }

        .btn-upload {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            border: none;
            padding: 8px 16px;
            border-radius: 40px;
            color: white;
            font-weight: 500;
            font-size: 0.7rem;
            transition: all 0.2s;
        }

        .btn-upload:hover {
            transform: translateY(-2px);
        }

        .btn-banding {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            border: none;
            padding: 10px 20px;
            border-radius: 40px;
            color: white;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }

        .btn-banding:hover {
            transform: translateY(-2px);
        }

        .info-banding {
            background: #cffafe;
            padding: 12px 16px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.75rem;
            color: #0e7490;
        }

        /* Form Controls */
        .form-control-modern-sm {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 40px;
            font-size: 0.75rem;
        }

        .form-control-modern {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 16px;
            font-size: 0.85rem;
        }

        .form-control-modern:focus {
            outline: none;
            border-color: #dc2626;
        }

        .form-label-modern {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .form-hint {
            font-size: 0.7rem;
            color: #94a3b8;
            margin-top: 6px;
        }

        .invalid-feedback-modern {
            font-size: 0.7rem;
            color: #ef4444;
            margin-top: 6px;
        }

        /* Empty States */
        .empty-state-small,
        .empty-ekonomi,
        .empty-dokumen {
            text-align: center;
            padding: 40px 20px;
        }

        .empty-icon-small {
            width: 48px;
            height: 48px;
            background: #f1f5f9;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
            font-size: 1.3rem;
            color: #94a3b8;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .ekonomi-grid {
                grid-template-columns: 1fr;
            }

            .action-footer {
                flex-direction: column;
            }

            .timeline-item {
                flex-direction: column;
            }

            .timeline-marker {
                display: none;
            }

            .alert-reject {
                flex-direction: column;
                text-align: center;
            }

            .dokumen-table thead {
                display: none;
            }

            .dokumen-table tbody tr {
                display: block;
                margin-bottom: 12px;
                border: 1px solid #e2e8f0;
                border-radius: 12px;
            }

            .dokumen-table tbody td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 10px 12px;
                border-bottom: 1px solid #f1f5f9;
            }

            .dokumen-table tbody td:last-child {
                border-bottom: none;
            }

            .dokumen-table tbody td::before {
                content: attr(data-label);
                font-weight: 600;
                font-size: 0.65rem;
                color: #64748b;
            }
        }

        @media (max-width: 480px) {
            .premium-card-header {
                flex-direction: column;
                text-align: center;
            }

            .info-item {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .info-item .info-icon {
                margin: 0 auto;
            }
        }
    </style>

    <script>
        function confirmSubmit(event) {
            event.preventDefault();
            swalConfirm('✅ Apakah Anda yakin ingin submit pendaftaran ini?\n\nData akan diverifikasi oleh reviewer.').then(
                function(ok) {
                    if (!ok) return;
                    event.target.submit();
                });
            return false;
        }

        function confirmDeleteDokumen(event) {
            event.preventDefault();
            swalConfirm('⚠️ Apakah Anda yakin ingin menghapus dokumen ini?').then(function(ok) {
                if (!ok) return;
                event.target.submit();
            });
            return false;
        }

        function confirmBanding(event) {
            event.preventDefault();
            const alasan = document.getElementById('alasanBanding');
            if (alasan && alasan.value.length < 20) {
                alert('⚠️ Alasan banding minimal 20 karakter!');
                return false;
            }
            swalConfirm('⚠️ Apakah Anda yakin ingin mengajukan banding?\n\nBanding akan diproses oleh panitia.').then(
                function(ok) {
                    if (!ok) return;
                    document.getElementById('bandingForm').submit();
                });
            return false;
        }

        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('fileInput');
            if (fileInput) {
                fileInput.addEventListener('change', function(e) {
                    const fileName = e.target.files[0]?.name || 'Belum ada file';
                    const fileNameSpan = document.getElementById('fileName');
                    if (fileNameSpan) fileNameSpan.textContent = fileName;
                });
            }

            const selectJenis = document.getElementById('selectJenisDokumen');
            const keteranganDiv = document.getElementById('keteranganDokumen');
            const keteranganText = document.getElementById('keteranganText');

            if (selectJenis && keteranganDiv && keteranganText) {
                selectJenis.addEventListener('change', function() {
                    const selected = this.options[this.selectedIndex];
                    const keterangan = selected.dataset.keterangan;

                    if (keterangan && keterangan !== 'null' && keterangan !== '') {
                        keteranganText.textContent = keterangan;
                        keteranganDiv.style.display = 'block';
                    } else {
                        keteranganDiv.style.display = 'none';
                    }
                });
            }
        });
    </script>
@endsection
