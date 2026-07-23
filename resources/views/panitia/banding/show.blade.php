@extends('layouts.app')
@section('title', 'Detail Banding')
@section('page-title', 'Detail Pengajuan Banding')

@section('content')
    <div class="row g-4">
        {{-- Info Banding Premium --}}
        <div class="col-md-4">
            <div class="card premium-card">
                <div class="card-header premium-card-header">
                    <div class="d-flex align-items-center gap-2">
                        <div class="header-icon warning-icon">
                            <i class="bi bi-chat-square-text-fill"></i>
                        </div>
                        <h6 class="fw-semibold mb-0">Info Banding</h6>
                    </div>
                    <span class="status-badge status-{{ $banding->warna_badge }}">
                        <i class="bi {{ $banding->status_icon ?? 'bi-circle' }}"></i>
                        {{ $banding->label_status }}
                    </span>
                </div>
                <div class="card-body p-4">
                    <div class="info-list">
                        <div class="info-item">
                            <div class="info-icon"><i class="bi bi-hash"></i></div>
                            <div class="info-content">
                                <div class="info-label">No. Pendaftaran</div>
                                <div class="info-value no-pendaftaran">{{ $banding->pendaftaran->no_pendaftaran }}</div>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon"><i class="bi bi-person"></i></div>
                            <div class="info-content">
                                <div class="info-label">Nama Mahasiswa</div>
                                <div class="info-value">{{ $banding->pendaftaran->nama_lengkap }}</div>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon"><i class="bi bi-building"></i></div>
                            <div class="info-content">
                                <div class="info-label">Program Studi</div>
                                <div class="info-value">{{ $banding->pendaftaran->programStudi->nama }}</div>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon"><i class="bi bi-trophy"></i></div>
                            <div class="info-content">
                                <div class="info-label">Golongan UKT Saat Ini</div>
                                <div class="info-value">
                                    <span class="golongan-badge gol-{{ $banding->hasilUkt->golongan_final }}">
                                        Gol. {{ $banding->hasilUkt->golongan_final }}
                                    </span>
                                    <div class="nominal-value">{{ $banding->hasilUkt->nominal_format }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon"><i class="bi bi-star"></i></div>
                            <div class="info-content">
                                <div class="info-label">Golongan Rekomendasi Sistem</div>
                                <div class="info-value rekomendasi-value">
                                    Gol. {{ $banding->hasilUkt->golongan_rekomendasi }}
                                </div>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon"><i class="bi bi-calendar3"></i></div>
                            <div class="info-content">
                                <div class="info-label">Tanggal Pengajuan</div>
                                <div class="info-value">{{ $banding->created_at->translatedFormat('d F Y H:i') }} WIB</div>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon"><i class="bi bi-chat-quote"></i></div>
                            <div class="info-content">
                                <div class="info-label">Alasan Banding</div>
                                <div class="alasan-box">
                                    <i class="bi bi-quote"></i>
                                    {{ $banding->alasan }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Hasil Keputusan Premium --}}
            @if (in_array($banding->status, ['diterima', 'ditolak']))
                <div class="card premium-card mt-4 decision-card decision-{{ $banding->status }}">
                    <div class="card-header decision-header">
                        <div class="d-flex align-items-center gap-2">
                            <div class="decision-icon">
                                <i
                                    class="bi bi-{{ $banding->status === 'diterima' ? 'check-circle-fill' : 'x-circle-fill' }}"></i>
                            </div>
                            <h6 class="fw-semibold mb-0">Keputusan Banding</h6>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="decision-item">
                            <div class="decision-label">Status</div>
                            <div class="decision-value">
                                <span class="status-badge status-{{ $banding->warna_badge }}">
                                    <i class="bi {{ $banding->status_icon }}"></i>
                                    {{ $banding->label_status }}
                                </span>
                            </div>
                        </div>
                        <div class="decision-item">
                            <div class="decision-label">Keputusan Panitia</div>
                            <div class="decision-value keputusan-text">
                                <i class="bi bi-chat-left-text"></i>
                                {{ $banding->keputusan_panitia }}
                            </div>
                        </div>
                        @if ($banding->golongan_baru)
                            <div class="decision-item">
                                <div class="decision-label">Golongan Baru</div>
                                <div class="decision-value">
                                    <span class="golongan-badge gol-{{ $banding->golongan_baru }}">
                                        Gol. {{ $banding->golongan_baru }}
                                    </span>
                                </div>
                            </div>
                        @endif
                        @if ($banding->diprosesOleh)
                            <div class="decision-item">
                                <div class="decision-label">Diproses Oleh</div>
                                <div class="decision-value">
                                    <i class="bi bi-person-circle"></i>
                                    {{ $banding->diprosesOleh->name }}
                                </div>
                            </div>
                            <div class="decision-item">
                                <div class="decision-label">Tanggal Diproses</div>
                                <div class="decision-value">
                                    <i class="bi bi-calendar3"></i>
                                    {{ $banding->diproses_at->translatedFormat('d F Y H:i') }} WIB
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <div class="col-md-8">
            {{-- Data Ekonomi Premium --}}
            @if ($banding->pendaftaran->dataEkonomi)
                <div class="card premium-card mb-4">
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
                            <div class="ekonomi-card">
                                <div class="ekonomi-label">Total Penghasilan</div>
                                <div class="ekonomi-value total">Rp
                                    {{ number_format($banding->pendaftaran->dataEkonomi->penghasilan_total, 0, ',', '.') }}
                                </div>
                            </div>
                            <div class="ekonomi-card">
                                <div class="ekonomi-label">Jumlah Tanggungan</div>
                                <div class="ekonomi-value">{{ $banding->pendaftaran->dataEkonomi->jumlah_tanggungan }}
                                    <span class="unit">orang</span>
                                </div>
                            </div>
                            <div class="ekonomi-card">
                                <div class="ekonomi-label">Kepemilikan Rumah</div>
                                <div class="ekonomi-value">
                                    {{ ucfirst(str_replace('_', ' ', $banding->pendaftaran->dataEkonomi->kepemilikan_rumah)) }}
                                </div>
                            </div>
                            <div class="ekonomi-card">
                                <div class="ekonomi-label">Kendaraan</div>
                                <div class="ekonomi-value">
                                    {{ ucfirst(str_replace('_', ' ', $banding->pendaftaran->dataEkonomi->kepemilikan_kendaraan)) }}
                                </div>
                            </div>
                            <div class="ekonomi-card">
                                <div class="ekonomi-label">Tagihan Listrik & Air</div>
                                <div class="ekonomi-value">Rp
                                    {{ number_format($banding->pendaftaran->dataEkonomi->tagihan_listrik + $banding->pendaftaran->dataEkonomi->tagihan_air, 0, ',', '.') }}
                                </div>
                            </div>
                            <div class="ekonomi-card">
                                <div class="ekonomi-label">Hutang/Cicilan</div>
                                <div class="ekonomi-value">Rp
                                    {{ number_format($banding->pendaftaran->dataEkonomi->hutang_cicilan, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Detail SAW Premium --}}
            @if ($banding->pendaftaran->hasilSaw)
                <div class="card premium-card mb-4">
                    <div class="card-header premium-card-header">
                        <div class="d-flex align-items-center gap-2">
                            <div class="header-icon primary-icon">
                                <i class="bi bi-calculator"></i>
                            </div>
                            <h6 class="fw-semibold mb-0">Detail Perhitungan SAW</h6>
                        </div>
                        <div class="score-badge">
                            <i class="bi bi-graph-up"></i>
                            Skor: {{ number_format($banding->pendaftaran->hasilSaw->skor_akhir, 2) }}
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table saw-table">
                                <thead>
                                    <tr>
                                        <th>Kode</th>
                                        <th>Kriteria</th>
                                        <th>Bobot</th>
                                        <th>Nilai Asli</th>
                                        <th>Normalisasi</th>
                                        <th>Terbobot</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($banding->pendaftaran->hasilSaw->details as $detail)
                                        <tr class="saw-detail-row">
                                            <td>
                                                <span class="kode-badge">{{ $detail->kriteria->kode }}</span>
                                            </td>
                                            <td class="kriteria-name">{{ $detail->kriteria->nama }}</td>
                                            <td class="bobot-cell">{{ $detail->kriteria->bobot }}%</td>
                                            <td class="nilai-cell">{{ number_format($detail->nilai_asli, 2) }}</td>
                                            <td class="normalisasi-cell">
                                                {{ number_format($detail->nilai_normalisasi ?? 0, 2) }}
                                            <td class="terbobot-cell">{{ number_format($detail->nilai_terbobot ?? 0, 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Form Proses Banding Premium --}}
            @if (in_array($banding->status, ['diajukan', 'diproses']))
                <div class="card premium-card">
                    <div class="card-header premium-card-header">
                        <div class="d-flex align-items-center gap-2">
                            <div class="header-icon primary-icon">
                                <i class="bi bi-gavel"></i>
                            </div>
                            <h6 class="fw-semibold mb-0">Keputusan Banding</h6>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('panitia.banding.proses', $banding) }}" method="POST" id="bandingForm">
                            @csrf

                            <div class="form-group mb-4">
                                <label class="form-label-modern">
                                    <i class="bi bi-check-circle"></i> Keputusan <span class="text-danger">*</span>
                                </label>
                                <div class="decision-options">
                                    <label class="decision-option">
                                        <input type="radio" name="keputusan" value="diterima" id="diterima"
                                            onchange="toggleGolongan(true)">
                                        <div class="decision-card decision-accept">
                                            <i class="bi bi-check-circle-fill"></i>
                                            <div class="decision-title">Diterima</div>
                                            <div class="decision-desc">Menerima banding dan mengubah golongan UKT</div>
                                        </div>
                                    </label>
                                    <label class="decision-option">
                                        <input type="radio" name="keputusan" value="ditolak" id="ditolak"
                                            onchange="toggleGolongan(false)">
                                        <div class="decision-card decision-reject">
                                            <i class="bi bi-x-circle-fill"></i>
                                            <div class="decision-title">Ditolak</div>
                                            <div class="decision-desc">Menolak banding, golongan UKT tetap</div>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div class="form-group mb-4" id="golonganBaru" style="display: none;">
                                <label class="form-label-modern">
                                    <i class="bi bi-star"></i> Golongan Baru <span class="text-danger">*</span>
                                </label>
                                <div class="golongan-selector">
                                    @foreach ($golongan as $g)
                                        <label class="golongan-option">
                                            <input type="radio" name="golongan_baru" value="{{ $g->golongan }}">
                                            <span class="golongan-radio gol-{{ $g->golongan }}">
                                                Gol. {{ $g->golongan }}
                                                <span class="golongan-nominal">Rp
                                                    {{ number_format($g->nominal, 0, ',', '.') }}</span>
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div class="form-group mb-4">
                                <label class="form-label-modern">
                                    <i class="bi bi-chat-text"></i> Keputusan Panitia <span class="text-danger">*</span>
                                </label>
                                <textarea name="keputusan_panitia" class="form-control-modern @error('keputusan_panitia') is-invalid @enderror"
                                    rows="4" placeholder="Jelaskan keputusan dan alasannya (minimal 10 karakter)..." id="keputusanText"
                                    required></textarea>
                                <div class="form-hint">
                                    <i class="bi bi-info-circle"></i>
                                    Minimal 10 karakter
                                </div>
                                @error('keputusan_panitia')
                                    <div class="invalid-feedback-modern">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="action-buttons">
                                <button type="submit" class="btn-submit" onclick="return confirmProses(event)">
                                    <i class="bi bi-gavel"></i> Proses Banding
                                </button>
                                <a href="{{ route('panitia.banding.index') }}" class="btn-cancel">
                                    <i class="bi bi-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            @else
                <div class="mt-3">
                    <a href="{{ route('panitia.banding.index') }}" class="btn-back">
                        <i class="bi bi-arrow-left"></i> Kembali ke Daftar Banding
                    </a>
                </div>
            @endif
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

        .warning-icon {
            background: #fed7aa;
            color: #f59e0b;
        }

        .success-icon {
            background: #d1fae5;
            color: #10b981;
        }

        .primary-icon {
            background: #eef2ff;
            color: #3b82f6;
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
            color: #f59e0b;
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

        .no-pendaftaran {
            font-family: monospace;
            background: #f1f5f9;
            padding: 2px 8px;
            border-radius: 20px;
            display: inline-block;
        }

        .golongan-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 40px;
            font-size: 0.7rem;
            font-weight: 700;
        }

        .gol-1 {
            background: #fef3c7;
            color: #92400e;
        }

        .gol-2 {
            background: #fed7aa;
            color: #9b3412;
        }

        .gol-3 {
            background: #fee2e2;
            color: #991b1b;
        }

        .gol-4 {
            background: #d1fae5;
            color: #065f46;
        }

        .gol-5 {
            background: #dbeafe;
            color: #1e40af;
        }

        .gol-6 {
            background: #cffafe;
            color: #0e7490;
        }

        .gol-7 {
            background: #e2e8f0;
            color: #475569;
        }

        .gol-8 {
            background: #f1f5f9;
            color: #1e293b;
        }

        .nominal-value {
            font-size: 0.75rem;
            font-weight: 600;
            color: #10b981;
            margin-top: 4px;
        }

        .rekomendasi-value {
            color: #64748b;
        }

        .alasan-box {
            background: #f8fafc;
            border-radius: 12px;
            padding: 10px 12px;
            font-size: 0.8rem;
            color: #475569;
            border-left: 3px solid #f59e0b;
        }

        .alasan-box i {
            color: #f59e0b;
            margin-right: 6px;
        }

        /* Decision Card */
        .decision-card {
            border: none;
            border-radius: 20px;
            overflow: hidden;
        }

        .decision-accept .decision-header {
            background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        }

        .decision-reject .decision-header {
            background: linear-gradient(135deg, #fee2e2, #fecaca);
        }

        .decision-icon {
            width: 32px;
            height: 32px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .decision-accept .decision-icon {
            background: #10b981;
            color: white;
        }

        .decision-reject .decision-icon {
            background: #dc2626;
            color: white;
        }

        .decision-item {
            margin-bottom: 14px;
            padding-bottom: 12px;
            border-bottom: 1px solid #f0f2f5;
        }

        .decision-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .decision-label {
            font-size: 0.65rem;
            text-transform: uppercase;
            color: #94a3b8;
            margin-bottom: 4px;
        }

        .keputusan-text {
            background: #f8fafc;
            padding: 8px 12px;
            border-radius: 12px;
            font-size: 0.75rem;
        }

        /* Ekonomi Grid */
        .ekonomi-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        .ekonomi-card {
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

        .ekonomi-value.total {
            color: #10b981;
        }

        .unit {
            font-size: 0.6rem;
            font-weight: normal;
        }

        /* SAW Table */
        .saw-table {
            width: 100%;
            border-collapse: collapse;
        }

        .saw-table thead th {
            background: #f8fafc;
            padding: 12px 16px;
            font-size: 0.65rem;
            font-weight: 600;
            text-transform: uppercase;
            color: #64748b;
            border-bottom: 2px solid #e2e8f0;
        }

        .saw-table tbody td {
            padding: 10px 16px;
            border-bottom: 1px solid #f1f5f9;
        }

        .kode-badge {
            background: #e2e8f0;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 0.65rem;
            font-weight: 700;
            font-family: monospace;
        }

        .kriteria-name {
            font-size: 0.75rem;
        }

        .bobot-cell,
        .nilai-cell,
        .normalisasi-cell,
        .terbobot-cell {
            font-size: 0.7rem;
        }

        .terbobot-cell {
            font-weight: 700;
            color: #3b82f6;
        }

        .score-badge {
            background: #dbeafe;
            color: #1e40af;
            padding: 4px 12px;
            border-radius: 40px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        /* Decision Options */
        .decision-options {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }

        .decision-option {
            cursor: pointer;
        }

        .decision-option input {
            display: none;
        }

        .decision-card {
            padding: 16px;
            border-radius: 16px;
            text-align: center;
            transition: all 0.2s ease;
            border: 2px solid #e2e8f0;
            background: white;
        }

        .decision-option input:checked+.decision-card {
            border-color: currentColor;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transform: scale(1.02);
        }

        .decision-accept {
            color: #10b981;
        }

        .decision-reject {
            color: #dc2626;
        }

        .decision-card i {
            font-size: 1.5rem;
            margin-bottom: 8px;
        }

        .decision-title {
            font-weight: 700;
            font-size: 0.85rem;
            margin-bottom: 4px;
        }

        .decision-desc {
            font-size: 0.6rem;
            color: #64748b;
        }

        /* Golongan Selector */
        .golongan-selector {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .golongan-option {
            cursor: pointer;
        }

        .golongan-option input {
            display: none;
        }

        .golongan-radio {
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            padding: 8px 12px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
            background: #f1f5f9;
            color: #475569;
            transition: all 0.2s;
        }

        .golongan-option input:checked+.golongan-radio {
            background: #3b82f6;
            color: white;
        }

        .golongan-nominal {
            font-size: 0.6rem;
            font-weight: normal;
        }

        /* Form */
        .form-label-modern {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .form-control-modern {
            width: 100%;
            padding: 10px 14px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 0.8rem;
            transition: all 0.2s;
        }

        .form-control-modern:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-hint {
            font-size: 0.65rem;
            color: #94a3b8;
            margin-top: 4px;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 12px;
            margin-top: 24px;
        }

        .btn-submit {
            flex: 1;
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            border: none;
            padding: 10px 20px;
            border-radius: 40px;
            color: white;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .btn-cancel,
        .btn-back {
            flex: 1;
            background: #f1f5f9;
            border: none;
            padding: 10px 20px;
            border-radius: 40px;
            color: #475569;
            font-weight: 600;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s;
        }

        .btn-cancel:hover,
        .btn-back:hover {
            background: #e2e8f0;
            transform: translateY(-2px);
            color: #1e293b;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .ekonomi-grid {
                grid-template-columns: 1fr;
            }

            .decision-options {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .premium-card-header {
                flex-direction: column;
                text-align: center;
            }

            .info-item {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .info-icon {
                margin: 0 auto;
            }

            .action-buttons {
                flex-direction: column;
            }

            .golongan-selector {
                justify-content: center;
            }
        }

        @media (max-width: 480px) {
            .info-value {
                font-size: 0.8rem;
            }

            .decision-card {
                padding: 12px;
            }

            .decision-title {
                font-size: 0.75rem;
            }

            .golongan-radio {
                padding: 6px 10px;
                font-size: 0.65rem;
            }
        }
    </style>

    <script>
        function toggleGolongan(show) {
            document.getElementById('golonganBaru').style.display = show ? 'block' : 'none';
        }

        function confirmProses(event) {
            event.preventDefault();
            const keputusan = document.querySelector('input[name="keputusan"]:checked');
            const keputusanText = document.getElementById('keputusanText').value;

            if (!keputusan) {
                alert('⚠️ Silakan pilih keputusan banding!');
                return false;
            }

            if (!keputusanText || keputusanText.length < 10) {
                alert('⚠️ Keputusan panitia minimal 10 karakter!');
                return false;
            }

            if (keputusan.value === 'diterima') {
                const golonganBaru = document.querySelector('input[name="golongan_baru"]:checked');
                if (!golonganBaru) {
                    alert('⚠️ Silakan pilih golongan baru!');
                    return false;
                }
            }

            swalConfirm('⚖️ Apakah Anda yakin dengan keputusan ini?\n\nKeputusan akan dikirimkan ke mahasiswa.').then(
                function(ok) {
                    if (!ok) return;
                    document.getElementById('bandingForm').submit();
                });
            return false;
        }
    </script>
@endsection
