@extends('layouts.app')
@section('title', 'Detail Verifikasi')
@section('page-title', 'Detail Verifikasi Dokumen')

@section('content')
    <div class="row g-4">
        {{-- Info Pendaftaran Premium --}}
        <div class="col-md-4">
            <div class="card premium-card">
                <div class="card-header premium-card-header">
                    <div class="d-flex align-items-center gap-2">
                        <div class="header-icon primary-icon">
                            <i class="bi bi-person-circle"></i>
                        </div>
                        <h6 class="fw-semibold mb-0">Info Pendaftar</h6>
                    </div>
                    <span class="status-badge status-{{ $pendaftaran->warna_badge }}">
                        <i class="bi {{ $pendaftaran->status_icon ?? 'bi-circle' }}"></i>
                        {{ $pendaftaran->label_status }}
                    </span>
                    @if ($pendaftaran->is_kip)
                        <span class="status-badge" style="background:#fef3c7;color:#92400e;">
                            <i class="bi bi-award-fill"></i> KIP
                        </span>
                    @endif
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
                            <div class="info-icon"><i class="bi bi-building"></i></div>
                            <div class="info-content">
                                <div class="info-label">Program Studi</div>
                                <div class="info-value">{{ $pendaftaran->programStudi->nama }}</div>
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
                            <div class="info-icon"><i class="bi bi-whatsapp"></i></div>
                            <div class="info-content">
                                <div class="info-label">No. HP</div>
                                <div class="info-value">{{ $pendaftaran->no_hp }}</div>
                            </div>
                        </div>
                        @if ($pendaftaran->is_kip)
                            <div class="info-item">
                                <div class="info-icon">
                                    <i class="bi bi-award-fill" style="color:#f59e0b;"></i>
                                </div>
                                <div class="info-content">
                                    <div class="info-label">Status KIP</div>
                                    <div class="info-value" style="color:#92400e;">
                                        Penerima KIP Kuliah
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
                            <div class="info-item">
                                <div class="info-icon"><i class="bi bi-building"></i></div>
                                <div class="info-content">
                                    <div class="info-label">Akreditasi Prodi</div>
                                    <div class="info-value">
                                        {{ $pendaftaran->programStudi->akreditasiData?->nama ?? '-' }}
                                    </div>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-icon"><i class="bi bi-cash"></i></div>
                                <div class="info-content">
                                    <div class="info-label">Nominal UKT KIP</div>
                                    <div class="info-value text-success">
                                        {{ $pendaftaran->programStudi->nominal_kip_format ?? '-' }}
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Data Ekonomi Premium --}}
            @if ($pendaftaran->dataEkonomi)
                <div class="card premium-card mt-4">
                    <div class="card-header premium-card-header">
                        <div class="d-flex align-items-center gap-2">
                            <div class="header-icon success-icon">
                                <i class="bi bi-cash-stack"></i>
                            </div>
                            <h6 class="fw-semibold mb-0">Data Ekonomi</h6>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="ekonomi-grid">
                            <div class="ekonomi-item">
                                <div class="ekonomi-label">Penghasilan Ayah</div>
                                <div class="ekonomi-value text-success">
                                    Rp
                                    {{ $pendaftaran->dataEkonomi->penghasilan_ayah > 0 ? 'Rp ' . number_format($pendaftaran->dataEkonomi->penghasilan_ayah, 0, ',', '.') . '/bln' : $pendaftaran->dataEkonomi->subC1?->label ?? '-' }}
                                </div>
                            </div>
                            <div class="ekonomi-item">
                                <div class="ekonomi-label">Penghasilan Ibu</div>
                                <div class="ekonomi-value text-success">
                                    Rp
                                    {{ $pendaftaran->dataEkonomi->penghasilan_ibu > 0 ? 'Rp ' . number_format($pendaftaran->dataEkonomi->penghasilan_ibu, 0, ',', '.') . '/bln' : $pendaftaran->dataEkonomi->subC2?->label ?? '-' }}
                                </div>
                            </div>

                            @php
                                $kriteria = \App\Models\Kriteria::where('periode_id', $pendaftaran->periode_id)
                                    ->with('subKriteria')
                                    ->orderByRaw('CAST(SUBSTRING(kode, 2) AS INTEGER)')
                                    ->get()
                                    ->filter(fn($k) => !in_array($k->kode, ['C1', 'C2']));
                            @endphp

                            @foreach ($kriteria as $k)
                                @php
                                    $field = 'sub_' . strtolower($k->kode) . '_id';
                                    $subId = $pendaftaran->dataEkonomi->$field ?? null;
                                    $sub = $subId ? \App\Models\SubKriteria::find($subId) : null;
                                @endphp
                                <div class="ekonomi-item">
                                    <div class="ekonomi-label">{{ $k->nama }}</div>
                                    <div class="ekonomi-value">
                                        {{ $sub?->label ?? '-' }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            {{-- Riwayat Verifikasi Premium --}}
            @if ($pendaftaran->verifikasi->count() > 0)
                <div class="card premium-card mt-4">
                    <div class="card-header premium-card-header">
                        <div class="d-flex align-items-center gap-2">
                            <div class="header-icon info-icon">
                                <i class="bi bi-clock-history"></i>
                            </div>
                            <h6 class="fw-semibold mb-0">Riwayat Verifikasi</h6>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @foreach ($pendaftaran->verifikasi->sortByDesc('verified_at') as $v)
                            <div class="timeline-item">
                                <div class="timeline-marker {{ $v->warna_badge }}"></div>
                                <div class="timeline-content">
                                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                        <div>
                                            <span class="status-badge status-{{ $v->warna_badge }}">
                                                <i class="bi {{ $v->icon ?? 'bi-check-circle' }}"></i>
                                                {{ $v->label_hasil }}
                                            </span>
                                            <div class="timeline-reviewer">
                                                <i class="bi bi-person-circle"></i>
                                                {{ $v->reviewer->name }}
                                            </div>
                                            @if ($v->komentar)
                                                <div class="timeline-comment">
                                                    <i class="bi bi-chat-quote"></i>
                                                    {{ $v->komentar }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="timeline-time">
                                            <i class="bi bi-calendar3"></i>
                                            {{ $v->verified_at->translatedFormat('d M Y H:i') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- Dokumen & Aksi Premium --}}
        <div class="col-md-8">
            {{-- Dokumen Premium --}}
            <div class="card premium-card mb-4">
                <div class="card-header premium-card-header">
                    <div class="d-flex align-items-center gap-2">
                        <div class="header-icon warning-icon">
                            <i class="bi bi-folder2-open"></i>
                        </div>
                        <h6 class="fw-semibold mb-0">
                            Dokumen Pendukung
                            <span class="dokumen-count">{{ $pendaftaran->dokumen->count() }}</span>
                        </h6>
                    </div>
                </div>
                <div class="card-body p-4">
                    @if ($pendaftaran->dokumen->count() > 0)
                        <div class="row g-3">
                            @foreach ($pendaftaran->dokumen as $dok)
                                <div class="col-md-6">
                                    <div class="dokumen-card">
                                        <div class="dokumen-header">
                                            <div class="dokumen-icon">
                                                <i class="bi bi-file-earmark-text-fill"></i>
                                            </div>
                                            <div class="dokumen-info">
                                                <div class="dokumen-jenis">
                                                    {{ $dok->jenisDokumen?->nama ?? ($dok->jenis ?? '-') }}
                                                    @if ($dok->jenisDokumen?->wajib)
                                                        <span class="badge bg-danger ms-1"
                                                            style="font-size:0.55rem;">Wajib</span>
                                                    @endif
                                                </div>
                                                <div class="dokumen-nama">{{ $dok->nama_file }}</div>
                                            </div>
                                            <span class="status-badge status-{{ $dok->warna_badge }}">
                                                <i class="bi {{ $dok->status_icon ?? 'bi-clock' }}"></i>
                                                {{ $dok->label_status }}
                                            </span>
                                        </div>
                                        <div class="dokumen-footer">
                                            <div class="dokumen-ukuran">
                                                <i class="bi bi-hdd-stack"></i>
                                                {{ number_format($dok->ukuran / 1024, 1) }} KB
                                            </div>
                                            <a href="{{ route('dokumen.preview', $dok) }}" target="_blank"
                                                class="btn-preview">
                                                <i class="bi bi-eye"></i> Preview
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-dokumen">
                            <div class="empty-icon-small">
                                <i class="bi bi-folder-x"></i>
                            </div>
                            <p class="mb-0">Belum ada dokumen yang diupload</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Aksi Verifikasi Premium --}}
            @if ($pendaftaran->status === 'menunggu_verifikasi')
                <div class="row g-4">
                    {{-- Approve Card --}}
                    <div class="col-md-6">
                        <div class="card action-card approve-card">
                            <div class="card-header action-card-header approve-header">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="action-icon success">
                                        <i class="bi bi-check-circle-fill"></i>
                                    </div>
                                    <h6 class="fw-semibold mb-0">Setujui Dokumen</h6>
                                </div>
                            </div>
                            <div class="card-body">
                                <p class="action-description">
                                    <i class="bi bi-info-circle"></i>
                                    @if ($pendaftaran->is_kip)
                                        Dokumen valid. Mahasiswa KIP akan langsung mendapat UKT
                                        <strong>{{ $pendaftaran->programStudi->nominal_kip_format }}</strong>
                                        berdasarkan akreditasi
                                        <strong>{{ $pendaftaran->programStudi->akreditasiData?->nama }}</strong>.
                                        Proses SAW tidak akan dijalankan.
                                    @else
                                        Dokumen valid dan data sesuai. Perhitungan SAW akan dijalankan otomatis setelah
                                        disetujui.
                                    @endif
                                </p>
                                <form action="{{ route('reviewer.verifikasi.approve', $pendaftaran) }}" method="POST">
                                    @csrf
                                    <div class="form-group mb-3">
                                        <label class="form-label-sm">
                                            <i class="bi bi-chat-text"></i> Komentar (opsional)
                                        </label>
                                        <textarea name="komentar" class="form-control-modern-sm" rows="3"
                                            placeholder="Tambahkan catatan jika perlu..."></textarea>
                                    </div>
                                    <button type="submit" class="btn-approve" onclick="return confirmApprove(event)">
                                        <i class="bi bi-check-circle"></i> Setujui Dokumen
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- Reject Card --}}
                    <div class="col-md-6">
                        <div class="card action-card reject-card">
                            <div class="card-header action-card-header reject-header">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="action-icon danger">
                                        <i class="bi bi-x-circle-fill"></i>
                                    </div>
                                    <h6 class="fw-semibold mb-0">Tolak Dokumen</h6>
                                </div>
                            </div>
                            <div class="card-body">
                                <p class="action-description">
                                    <i class="bi bi-info-circle"></i>
                                    Dokumen tidak valid atau data tidak sesuai. Mahasiswa akan diberi tahu untuk upload
                                    ulang.
                                </p>
                                <form action="{{ route('reviewer.verifikasi.reject', $pendaftaran) }}" method="POST"
                                    id="rejectForm">
                                    @csrf
                                    <div class="form-group mb-3">
                                        <label class="form-label-sm">
                                            <i class="bi bi-chat-text"></i> Alasan Penolakan <span
                                                class="text-danger">*</span>
                                        </label>
                                        <textarea name="komentar" class="form-control-modern-sm @error('komentar') is-invalid @enderror" rows="3"
                                            placeholder="Jelaskan alasan penolakan..." id="rejectReason" required></textarea>
                                        @error('komentar')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <button type="submit" class="btn-reject" onclick="return confirmReject(event)">
                                        <i class="bi bi-x-circle"></i> Tolak Dokumen
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="info-alert {{ $pendaftaran->status === 'diverifikasi' ? 'info-success' : 'info-warning' }}">
                    <i
                        class="bi {{ $pendaftaran->status === 'diverifikasi' ? 'bi-check-circle-fill' : 'bi-info-circle-fill' }}"></i>
                    <div>
                        @if ($pendaftaran->status === 'diverifikasi')
                            ✅ Dokumen sudah diverifikasi. Perhitungan SAW sedang/sudah dijalankan.
                        @elseif($pendaftaran->status === 'ditolak_reviewer')
                            ⚠️ Dokumen sudah ditolak. Menunggu mahasiswa upload ulang.
                        @else
                            ℹ️ Status: {{ $pendaftaran->label_status }}
                        @endif
                    </div>
                </div>
            @endif

            <div class="mt-3">
                <a href="{{ route('reviewer.verifikasi.index') }}" class="btn-back">
                    <i class="bi bi-arrow-left"></i> Kembali ke Daftar Verifikasi
                </a>
            </div>
        </div>
    </div>

    <style>
        /* Premium Card */
        .premium-card {
            border: none;
            border-radius: 20px;
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
            width: 32px;
            height: 32px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }

        .primary-icon {
            background: #eef2ff;
            color: #3b82f6;
        }

        .success-icon {
            background: #d1fae5;
            color: #10b981;
        }

        .info-icon {
            background: #cffafe;
            color: #06b6d4;
        }

        .warning-icon {
            background: #fed7aa;
            color: #f59e0b;
        }

        /* Status Badge */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
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

        /* Info List */
        .info-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .info-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .info-icon {
            width: 32px;
            height: 32px;
            background: #f8fafc;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #3b82f6;
            font-size: 0.9rem;
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

        /* Ekonomi Grid */
        .ekonomi-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        .ekonomi-item {
            background: #f8fafc;
            border-radius: 12px;
            padding: 10px 12px;
        }

        .ekonomi-label {
            font-size: 0.6rem;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 4px;
        }

        .ekonomi-value {
            font-size: 0.75rem;
            font-weight: 600;
            color: #1e293b;
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

        .timeline-content {
            flex: 1;
        }

        .timeline-reviewer {
            font-size: 0.65rem;
            color: #64748b;
            margin-top: 4px;
        }

        .timeline-comment {
            font-size: 0.7rem;
            color: #475569;
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

        /* Dokumen Card */
        .dokumen-card {
            background: #f8fafc;
            border-radius: 16px;
            padding: 12px;
            transition: all 0.2s;
        }

        .dokumen-card:hover {
            background: #f1f5f9;
            transform: translateY(-2px);
        }

        .dokumen-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }

        .dokumen-icon {
            width: 36px;
            height: 36px;
            background: #eef2ff;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #3b82f6;
            font-size: 1rem;
        }

        .dokumen-info {
            flex: 1;
        }

        .dokumen-jenis {
            font-size: 0.7rem;
            font-weight: 600;
            color: #1e293b;
        }

        .dokumen-nama {
            font-size: 0.6rem;
            color: #64748b;
        }

        .dokumen-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1px solid #e2e8f0;
        }

        .dokumen-ukuran {
            font-size: 0.6rem;
            color: #94a3b8;
        }

        .btn-preview {
            background: transparent;
            border: 1px solid #3b82f6;
            color: #3b82f6;
            padding: 4px 12px;
            border-radius: 40px;
            font-size: 0.65rem;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-preview:hover {
            background: #3b82f6;
            color: white;
        }

        .dokumen-count {
            background: #e2e8f0;
            padding: 2px 8px;
            border-radius: 40px;
            font-size: 0.65rem;
            margin-left: 6px;
        }

        /* Action Cards */
        .action-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
            overflow: hidden;
            height: 100%;
        }

        .action-card-header {
            border-bottom: none;
            padding: 14px 18px;
        }

        .approve-header {
            background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        }

        .reject-header {
            background: linear-gradient(135deg, #fee2e2, #fecaca);
        }

        .action-icon {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .action-icon.success {
            background: #10b981;
            color: white;
        }

        .action-icon.danger {
            background: #dc2626;
            color: white;
        }

        .action-description {
            font-size: 0.7rem;
            color: #64748b;
            margin-bottom: 16px;
            padding: 8px 12px;
            background: #f8fafc;
            border-radius: 10px;
        }

        .form-label-sm {
            font-size: 0.7rem;
            font-weight: 600;
            margin-bottom: 4px;
            display: block;
        }

        .form-control-modern-sm {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            font-size: 0.75rem;
            transition: all 0.2s;
        }

        .form-control-modern-sm:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .btn-approve,
        .btn-reject {
            width: 100%;
            padding: 8px 16px;
            border-radius: 40px;
            font-weight: 600;
            font-size: 0.75rem;
            border: none;
            transition: all 0.2s;
        }

        .btn-approve {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }

        .btn-approve:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .btn-reject {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
        }

        .btn-reject:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }

        /* Info Alert */
        .info-alert {
            padding: 12px 16px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.75rem;
        }

        .info-success {
            background: #d1fae5;
            color: #065f46;
        }

        .info-warning {
            background: #fef3c7;
            color: #92400e;
        }

        /* Back Button */
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #f1f5f9;
            color: #475569;
            padding: 8px 20px;
            border-radius: 40px;
            font-size: 0.8rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-back:hover {
            background: #e2e8f0;
            transform: translateX(-4px);
            color: #1e293b;
        }

        /* Empty State */
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
        @media (max-width: 992px) {
            .ekonomi-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .premium-card-header {
                flex-direction: column;
                text-align: center;
            }

            .timeline-item {
                flex-direction: column;
            }

            .timeline-marker {
                display: none;
            }

            .timeline-time {
                white-space: normal;
            }

            .dokumen-header {
                flex-wrap: wrap;
            }
        }

        @media (max-width: 480px) {
            .info-item {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .info-icon {
                margin: 0 auto;
            }

            .ekonomi-grid {
                gap: 8px;
            }

            .action-card-header {
                justify-content: center;
            }
        }
    </style>

    <script>
        function confirmApprove(event) {
            event.preventDefault();
            swalConfirm(
                    '✅ Apakah Anda yakin ingin menyetujui dokumen ini?\n\nPerhitungan SAW akan dijalankan secara otomatis.')
                .then(function(ok) {
                    if (!ok) return;
                    event.target.closest('form').submit();
                });
            return false;
        }

        function confirmReject(event) {
            event.preventDefault();
            const komentar = document.getElementById('rejectReason').value;
            if (!komentar) {
                alert('⚠️ Alasan penolakan harus diisi!');
                return false;
            }
            swalConfirm(
                '⚠️ Apakah Anda yakin ingin menolak dokumen ini?\n\nMahasiswa akan diberi notifikasi untuk upload ulang.'
                ).then(function(ok) {
                if (!ok) return;
                document.getElementById('rejectForm').submit();
            });
            return false;
        }
    </script>
@endsection
