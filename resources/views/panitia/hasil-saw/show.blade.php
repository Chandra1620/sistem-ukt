@extends('layouts.app')
@section('title', 'Detail Hasil SAW')
@section('page-title', 'Detail Hasil SAW')

@section('content')
    <div class="row g-4">
        {{-- Info Pendaftar Premium --}}
        @if ($pendaftaran->is_kip)
            <div class="alert border-0 mb-4 p-3 rounded-3" style="background:#fef3c7;">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-award-fill text-warning fs-4"></i>
                    <div>
                        <div class="fw-semibold" style="color:#92400e;">Mahasiswa KIP Kuliah</div>
                        <div class="small" style="color:#78350f;">
                            No. KIP: {{ $pendaftaran->no_kip ?? '-' }} |
                            Akreditasi: {{ $pendaftaran->programStudi->akreditasiData?->nama ?? '-' }}
                            | Nominal KIP: {{ $pendaftaran->programStudi->nominal_kip_format ?? '-' }}
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="col-md-4">
            <div class="card premium-card">
                <div class="card-header premium-card-header">
                    <div class="d-flex align-items-center gap-2">
                        <div class="header-icon primary-icon">
                            <i class="bi bi-person-circle"></i>
                        </div>
                        <h6 class="fw-semibold mb-0">Info Pendaftar</h6>
                    </div>
                    <span class="status-badge status-{{ $pendaftaran->warna_badge ?? 'secondary' }}">
                        <i class="bi {{ $pendaftaran->status_icon ?? 'bi-circle' }}"></i>
                        {{ $pendaftaran->label_status ?? ucfirst($pendaftaran->status) }}
                    </span>
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
                    </div>
                </div>
            </div>

            {{-- Hasil UKT Premium --}}
            @if ($pendaftaran->hasilUkt)
                <div class="mt-2">
                    <span
                        class="badge {{ $pendaftaran->hasilUkt->sumber_pembiayaan === 'KIP' ? 'bg-warning text-dark' : 'bg-success' }}">
                        <i
                            class="bi {{ $pendaftaran->hasilUkt->sumber_pembiayaan === 'KIP' ? 'bi-award-fill' : 'bi-wallet2' }} me-1"></i>
                        {{ $pendaftaran->hasilUkt->sumber_pembiayaan === 'KIP' ? 'KIP - Pemerintah' : 'Non-KIP - Mandiri' }}
                    </span>
                </div>

                <div class="card premium-card mt-4">
                    <div class="card-header premium-card-header">
                        <div class="d-flex align-items-center gap-2">
                            <div class="header-icon warning-icon">
                                <i class="bi bi-award"></i>
                            </div>
                            <h6 class="fw-semibold mb-0">Hasil UKT</h6>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="ukt-result">
                            <div class="rekomendasi-box">
                                <div class="rekomendasi-label">
                                    <i class="bi bi-star"></i> Golongan Rekomendasi Sistem
                                </div>
                                <div class="rekomendasi-value">
                                    {{ $pendaftaran->hasilUkt->golongan_rekomendasi ?? '-' }}
                                </div>
                            </div>
                            <div class="final-box">
                                <div class="final-label">
                                    <i class="bi bi-trophy"></i> Golongan Final
                                </div>
                                <div class="final-value gol-{{ $pendaftaran->hasilUkt->golongan_final ?? '1' }}">
                                    {{ $pendaftaran->hasilUkt->golongan_final ?? '-' }}
                                </div>
                                <div class="nominal-value">
                                    <i class="bi bi-cash-stack"></i>
                                    {{ $pendaftaran->hasilUkt->nominal_format ?? 'Rp 0' }}
                                </div>
                                @if ($pendaftaran->hasilUkt->is_override ?? false)
                                    <div class="override-badge-large">
                                        <i class="bi bi-arrow-repeat"></i> Telah Dioverride
                                    </div>
                                @endif
                                @if ($pendaftaran->hasilUkt->dasar_penetapan)
                                    <div class="mt-2 p-2 rounded-3 small"
                                        style="background:{{ $pendaftaran->hasilUkt->sumber_pembiayaan === 'KIP' ? '#fef3c7' : '#f0fdf4' }};
                                    color:{{ $pendaftaran->hasilUkt->sumber_pembiayaan === 'KIP' ? '#92400e' : '#065f46' }}">
                                        <i class="bi bi-info-circle me-1"></i>
                                        {{ $pendaftaran->hasilUkt->dasar_penetapan }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Aksi Finalisasi & Override Premium --}}
            @if ($pendaftaran->status === 'hasil_awal')
                {{-- Finalisasi Card --}}
                <div class="card action-card finalisasi-card mt-4">
                    <div class="card-header action-card-header finalisasi-header">
                        <div class="d-flex align-items-center gap-2">
                            <div class="action-icon success">
                                <i class="bi bi-check2-circle"></i>
                            </div>
                            <h6 class="fw-semibold mb-0">Finalisasi UKT</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <p class="action-description">
                            <i class="bi bi-info-circle"></i>
                            Finalisasi akan mengunci hasil UKT dan mengirim notifikasi ke mahasiswa.
                        </p>
                        <form action="{{ route('panitia.finalisasi', $pendaftaran) }}" method="POST" id="finalisasiForm">
                            @csrf
                            <button type="submit" class="btn-finalisasi" onclick="return confirmFinalisasi(event)">
                                <i class="bi bi-check-circle"></i> Finalisasi UKT
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Override Card --}}
                <div class="card action-card override-card mt-4">
                    <div class="card-header action-card-header override-header">
                        <div class="d-flex align-items-center gap-2">
                            <div class="action-icon warning">
                                <i class="bi bi-arrow-repeat"></i>
                            </div>
                            <h6 class="fw-semibold mb-0 text-warning">Override UKT</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <p class="action-description">
                            <i class="bi bi-exclamation-triangle"></i>
                            Override golongan UKT jika ada pertimbangan khusus. Wajib mengisi alasan.
                        </p>
                        <form action="{{ route('panitia.override', $pendaftaran) }}" method="POST" id="overrideForm">
                            @csrf
                            <div class="form-group mb-3">
                                <label class="form-label-sm">
                                    <i class="bi bi-star"></i> Golongan Baru <span class="text-danger">*</span>
                                </label>
                                <div class="golongan-selector">
                                    @for ($i = 1; $i <= 8; $i++)
                                        <label class="golongan-option">
                                            <input type="radio" name="golongan_baru" value="{{ $i }}"
                                                {{ $pendaftaran->hasilUkt && $pendaftaran->hasilUkt->golongan_final == $i ? 'checked' : '' }}>
                                            <span class="golongan-radio gol-{{ $i }}">
                                                Gol. {{ $i }}
                                            </span>
                                        </label>
                                    @endfor
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label-sm">
                                    <i class="bi bi-chat-text"></i> Alasan Override <span class="text-danger">*</span>
                                </label>
                                <textarea name="alasan" class="form-control-modern-sm" rows="3"
                                    placeholder="Jelaskan alasan override (minimal 20 karakter)..." id="alasanOverride" required></textarea>
                            </div>
                            <button type="submit" class="btn-override" onclick="return confirmOverride(event)">
                                <i class="bi bi-arrow-repeat"></i> Override UKT
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>

        {{-- Detail SAW Premium --}}
        <div class="col-md-8">
            {{-- Skor SAW --}}
            @if ($pendaftaran->is_kip)
                <div class="card premium-card mb-4">
                    <div class="card-body p-4 text-center">
                        <i class="bi bi-award-fill text-warning fs-1 d-block mb-3"></i>
                        <h6 class="fw-bold">Mahasiswa KIP Kuliah</h6>
                        <p class="text-muted small mb-3">
                            Mahasiswa ini adalah penerima KIP. UKT ditentukan berdasarkan
                            akreditasi program studi, bukan melalui perhitungan SAW.
                        </p>
                        <div class="p-3 rounded-3" style="background:#fef3c7;">
                            <div class="fw-semibold" style="color:#92400e;">
                                <i class="bi bi-award me-1"></i>
                                Akreditasi {{ $pendaftaran->programStudi->akreditasiData?->nama ?? '-' }}
                                → {{ $pendaftaran->programStudi->nominal_kip_format ?? '-' }}
                            </div>
                            <div class="small mt-1" style="color:#78350f;">
                                Sumber Pembiayaan: Pemerintah (KIP)
                            </div>
                        </div>
                    </div>
                </div>
            @elseif ($pendaftaran->hasilSaw)
                <div class="card premium-card mb-4">
                    <div class="card-header premium-card-header">
                        <div class="d-flex align-items-center gap-2">
                            <div class="header-icon primary-icon">
                                <i class="bi bi-calculator"></i>
                            </div>
                            <h6 class="fw-semibold mb-0">Detail Perhitungan SAW</h6>
                        </div>
                        <div class="score-summary">
                            <div class="score-badge">
                                <i class="bi bi-graph-up"></i>
                                Skor: {{ number_format($pendaftaran->hasilSaw->skor_akhir ?? 0, 2) }}
                            </div>
                            @if ($pendaftaran->hasilSaw->ranking ?? false)
                                <div class="rank-badge">
                                    <i class="bi bi-trophy"></i>
                                    Ranking #{{ $pendaftaran->hasilSaw->ranking }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table saw-detail-table">
                                <thead>
                                    <tr>
                                        <th>Kode</th>
                                        <th>Kriteria</th>
                                        <th>Tipe</th>
                                        <th>Bobot</th>
                                        <th>Nilai Asli</th>
                                        <th>Normalisasi</th>
                                        <th>Terbobot</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pendaftaran->hasilSaw->details ?? [] as $detail)
                                        <tr class="saw-detail-row">
                                            <td>
                                                <span class="kode-badge">{{ $detail->kriteria->kode ?? '-' }}</span>
                                            </td>
                                            <td class="kriteria-name">{{ $detail->kriteria->nama ?? '-' }}</td>
                                            <td>
                                                @if (($detail->kriteria->tipe ?? '') === 'cost')
                                                    <span class="tipe-badge tipe-cost">Cost</span>
                                                @else
                                                    <span class="tipe-badge tipe-benefit">Benefit</span>
                                                @endif
                                            </td>
                                            <td class="bobot-cell">{{ $detail->kriteria->bobot ?? 0 }}%</td>
                                            <td class="nilai-cell">{{ number_format($detail->nilai_asli ?? 0, 2) }}</td>
                                            <td class="normalisasi-cell">
                                                {{ number_format($detail->nilai_normalisasi ?? 0, 2) }}
                                            <td class="terbobot-cell">{{ number_format($detail->nilai_terbobot ?? 0, 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="6" class="text-end fw-bold">Total Skor Akhir:</td>
                                        <td class="total-skor">
                                            {{ number_format($pendaftaran->hasilSaw->skor_akhir ?? 0, 2) }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            @else
                <div class="card premium-card mb-4">
                    <div class="card-body p-4 text-center text-muted">
                        <i class="bi bi-info-circle fs-3 d-block mb-2"></i>
                        Data perhitungan SAW belum tersedia untuk pendaftar ini.
                    </div>
                </div>
            @endif

            {{-- Data Ekonomi Premium --}}
            @if ($pendaftaran->dataEkonomi)
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
                            @php
                                $kriteria = \App\Models\Kriteria::where('periode_id', $pendaftaran->periode_id ?? 0)
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
                    </div>
                </div>
            @endif

            {{-- Riwayat Verifikasi Premium --}}
            @if ($pendaftaran->verifikasi && $pendaftaran->verifikasi->count() > 0)
                <div class="card premium-card">
                    <div class="card-header premium-card-header">
                        <div class="d-flex align-items-center gap-2">
                            <div class="header-icon info-icon">
                                <i class="bi bi-shield-check"></i>
                            </div>
                            <h6 class="fw-semibold mb-0">Riwayat Verifikasi</h6>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @foreach ($pendaftaran->verifikasi->sortByDesc('verified_at') as $v)
                            <div class="timeline-item">
                                <div class="timeline-marker {{ $v->warna_badge ?? 'secondary' }}"></div>
                                <div class="timeline-content">
                                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                        <div>
                                            <span class="status-badge status-{{ $v->warna_badge ?? 'secondary' }}">
                                                <i class="bi {{ $v->icon ?? 'bi-check-circle' }}"></i>
                                                {{ $v->label_hasil ?? ucfirst($v->hasil) }}
                                            </span>
                                            <div class="timeline-reviewer">
                                                <i class="bi bi-person-circle"></i>
                                                {{ $v->reviewer->name ?? '-' }}
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
                                            {{ $v->verified_at ? $v->verified_at->translatedFormat('d M Y H:i') : '-' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="mt-3">
                <a href="{{ route('panitia.hasil-saw.index') }}" class="btn-back">
                    <i class="bi bi-arrow-left"></i> Kembali ke Daftar Hasil SAW
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

        .status-secondary {
            background: #f1f5f9;
            color: #475569;
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

        .no-pendaftaran {
            font-family: monospace;
            background: #f1f5f9;
            padding: 2px 8px;
            border-radius: 20px;
            display: inline-block;
        }

        /* UKT Result */
        .ukt-result {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .rekomendasi-box,
        .final-box {
            background: #f8fafc;
            border-radius: 16px;
            padding: 16px;
            text-align: center;
        }

        .rekomendasi-label,
        .final-label {
            font-size: 0.65rem;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 8px;
        }

        .rekomendasi-value {
            font-size: 2rem;
            font-weight: 800;
            color: #64748b;
        }

        .final-value {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 8px;
        }

        .gol-1 {
            color: #92400e;
        }

        .gol-2 {
            color: #9b3412;
        }

        .gol-3 {
            color: #991b1b;
        }

        .gol-4 {
            color: #065f46;
        }

        .gol-5 {
            color: #1e40af;
        }

        .gol-6 {
            color: #0e7490;
        }

        .gol-7 {
            color: #475569;
        }

        .gol-8 {
            color: #1e293b;
        }

        .nominal-value {
            font-size: 0.9rem;
            font-weight: 700;
            color: #10b981;
        }

        .override-badge-large {
            display: inline-block;
            background: #fef3c7;
            color: #f59e0b;
            padding: 4px 12px;
            border-radius: 40px;
            font-size: 0.7rem;
            font-weight: 600;
            margin-top: 12px;
        }

        /* Action Cards */
        .action-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
            overflow: hidden;
        }

        .action-card-header {
            border-bottom: none;
            padding: 14px 18px;
        }

        .finalisasi-header {
            background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        }

        .override-header {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
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

        .action-icon.warning {
            background: #f59e0b;
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

        .btn-finalisasi,
        .btn-override {
            width: 100%;
            padding: 10px 16px;
            border-radius: 40px;
            font-weight: 600;
            font-size: 0.75rem;
            border: none;
            transition: all 0.2s;
        }

        .btn-finalisasi {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }

        .btn-finalisasi:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .btn-override {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
        }

        .btn-override:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
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
            display: inline-block;
            padding: 4px 12px;
            border-radius: 40px;
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

        /* Score Summary */
        .score-summary {
            display: flex;
            gap: 8px;
        }

        .score-badge,
        .rank-badge {
            padding: 4px 12px;
            border-radius: 40px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .score-badge {
            background: #dbeafe;
            color: #1e40af;
        }

        .rank-badge {
            background: #fef3c7;
            color: #92400e;
        }

        /* SAW Detail Table */
        .saw-detail-table {
            width: 100%;
            border-collapse: collapse;
        }

        .saw-detail-table thead th {
            background: #f8fafc;
            padding: 12px 16px;
            font-size: 0.65rem;
            font-weight: 600;
            text-transform: uppercase;
            color: #64748b;
            border-bottom: 2px solid #e2e8f0;
        }

        .saw-detail-table tbody td {
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

        .tipe-badge {
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 0.6rem;
            font-weight: 600;
        }

        .tipe-cost {
            background: #fee2e2;
            color: #dc2626;
        }

        .tipe-benefit {
            background: #d1fae5;
            color: #10b981;
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

        .total-skor {
            font-size: 0.9rem;
            font-weight: 800;
            color: #3b82f6;
        }

        /* Ekonomi Grid */
        .ekonomi-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
        }

        .ekonomi-card {
            background: #f8fafc;
            border-radius: 12px;
            padding: 10px 12px;
        }

        .ekonomi-card.total {
            background: linear-gradient(135deg, #d1fae5, #a7f3d0);
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
        }

        .timeline-marker.success {
            background: #10b981;
        }

        .timeline-marker.warning {
            background: #f59e0b;
        }

        .timeline-marker.danger {
            background: #ef4444;
        }

        .timeline-marker.secondary {
            background: #94a3b8;
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

        /* Form Controls */
        .form-label-sm {
            font-size: 0.7rem;
            font-weight: 600;
            margin-bottom: 6px;
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
            border-color: #f59e0b;
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
        }

        /* Responsive */
        @media (max-width: 992px) {
            .ekonomi-grid {
                grid-template-columns: repeat(2, 1fr);
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

            .ekonomi-grid {
                grid-template-columns: 1fr;
            }

            .golongan-selector {
                justify-content: center;
            }

            .score-summary {
                justify-content: center;
            }
        }

        @media (max-width: 480px) {
            .info-item {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .info-item .info-icon {
                margin: 0 auto;
            }

            .rekomendasi-value {
                font-size: 1.5rem;
            }

            .final-value {
                font-size: 2rem;
            }
        }
    </style>

    <script>
        function confirmFinalisasi(event) {
            event.preventDefault();
            swalConfirm(
                '✅ Apakah Anda yakin ingin memfinalisasi UKT mahasiswa ini?\n\nHasil akan dikunci dan notifikasi akan dikirim ke mahasiswa.'
            ).then(function(ok) {
                if (!ok) return;
                document.getElementById('finalisasiForm').submit();
            });
            return false;
        }

        function confirmOverride(event) {
            event.preventDefault();
            const alasan = document.getElementById('alasanOverride');
            const golongan = document.querySelector('input[name="golongan_baru"]:checked');

            if (!golongan) {
                alert('⚠️ Silakan pilih golongan baru!');
                return false;
            }

            if (!alasan || alasan.value.trim().length < 20) {
                alert('⚠️ Alasan override minimal 20 karakter!');
                return false;
            }

            swalConfirm(
                    '⚠️ Apakah Anda yakin ingin mengoverride UKT mahasiswa ini?\n\nPerubahan akan dicatat dalam sistem.')
                .then(function(ok) {
                    if (!ok) return;
                    document.getElementById('overrideForm').submit();
                });
            return false;
        }
    </script>
@endsection
