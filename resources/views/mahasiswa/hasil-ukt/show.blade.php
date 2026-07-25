@extends('layouts.app')
@section('title', 'Hasil UKT')
@section('page-title', 'Hasil UKT Anda')

@section('content')
    @php
        $hasBandingArea =
            ($pendaftaran->status === 'final_ukt' && !$pendaftaran->is_kip) ||
            ($pendaftaran->banding && $pendaftaran->banding->count() > 0);
    @endphp
    <div class="row g-4 justify-content-center">
        {{-- Kolom Kiri: Kartu Hasil UKT --}}
        <div class="{{ $hasBandingArea ? 'col-lg-5' : 'col-lg-7 col-xl-6' }}">
            {{-- Kartu Hasil UKT --}}
            <div class="card result-card">
                <div class="card-body p-4 p-xl-5">
                    <div class="text-center">
                        @if ($pendaftaran->is_kip)
                            {{-- Tampilan KIP --}}
                            <h5 class="result-title">Penerima KIP Kuliah</h5>
                            <div class="kip-badge">
                                <i class="bi bi-bank2 me-1"></i> Dibiayai Pemerintah
                            </div>
                            <div class="nominal-wrapper kip-wrapper">
                                <span class="nominal-label">Nominal UKT KIP</span>
                                <span class="nominal-value">{{ $pendaftaran->hasilUkt->nominal_format ?? 'Rp 0' }}</span>
                            </div>
                            <div class="akreditation-info">
                                <i class="bi bi-award me-1"></i>
                                Akreditasi {{ $pendaftaran->programStudi->akreditasiData?->nama ?? '-' }}
                            </div>
                        @else
                            {{-- Tampilan Non-KIP --}}
                            <h5 class="result-title">Golongan UKT</h5>
                            <div class="golongan-display">
                                {{ $pendaftaran->hasilUkt->golongan_final ?? '-' }}
                            </div>
                            <div class="nominal-wrapper">
                                <span class="nominal-label">Nominal UKT</span>
                                <span class="nominal-value">{{ $pendaftaran->hasilUkt->nominal_format ?? 'Rp 0' }}</span>
                            </div>
                        @endif

                        {{-- Status --}}
                        @if ($pendaftaran->hasilUkt)
                            <div class="status-banner {{ $pendaftaran->is_kip ? 'status-kip' : 'status-nonkip' }}">
                                <i class="bi {{ $pendaftaran->is_kip ? 'bi-bank2' : 'bi-wallet2' }} me-2"></i>
                                {{ $pendaftaran->is_kip ? 'KIP - Dibiayai Pemerintah' : 'Non-KIP - Pembayaran Mandiri' }}
                            </div>
                        @endif

                        {{-- Override Badge --}}
                        @if ($pendaftaran->hasilUkt && $pendaftaran->hasilUkt->is_override)
                            <div class="override-badge">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                UKT ini telah dioverride oleh panitia
                            </div>
                        @endif

                        {{-- Footer Info --}}
                        <div class="result-footer">
                            <div class="footer-item">
                                <span class="footer-label">No. Pendaftaran</span>
                                <span class="footer-value">{{ $pendaftaran->no_pendaftaran }}</span>
                            </div>
                            <div class="footer-item">
                                <span class="footer-label">Program Studi</span>
                                <span class="footer-value">{{ $pendaftaran->programStudi->nama ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Info Finalisasi --}}
            @if ($pendaftaran->hasilUkt && $pendaftaran->hasilUkt->finalisasiOleh)
                <div class="card info-card mt-4">
                    <div class="card-body">
                        <div class="info-header">
                            <span class="info-icon success">
                                <i class="bi bi-person-check"></i>
                            </span>
                            <h6 class="info-title">Info Finalisasi</h6>
                        </div>
                        <div class="info-grid">
                            <div class="info-row">
                                <span class="info-label">Difinalisasi Oleh</span>
                                <span class="info-value">{{ $pendaftaran->hasilUkt->finalisasiOleh->name ?? '-' }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Tanggal Finalisasi</span>
                                <span class="info-value">
                                    {{ $pendaftaran->hasilUkt->finalisasi_at ? $pendaftaran->hasilUkt->finalisasi_at->translatedFormat('d F Y H:i') . ' WIB' : '-' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Info Override --}}
            @if (
                $pendaftaran->hasilUkt &&
                    $pendaftaran->hasilUkt->is_override &&
                    $pendaftaran->hasilUkt->overrideOleh &&
                    !$pendaftaran->is_kip)
                <div class="card override-card mt-4">
                    <div class="card-body">
                        <div class="info-header">
                            <span class="info-icon warning">
                                <i class="bi bi-arrow-repeat"></i>
                            </span>
                            <h6 class="info-title text-warning">Info Override</h6>
                        </div>
                        <div class="info-grid">
                            <div class="info-row">
                                <span class="info-label">Golongan Rekomendasi</span>
                                <span class="info-value">Golongan
                                    {{ $pendaftaran->hasilUkt->golongan_rekomendasi ?? '-' }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Dioverride Oleh</span>
                                <span class="info-value">{{ $pendaftaran->hasilUkt->overrideOleh->name ?? '-' }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Alasan Override</span>
                                <span
                                    class="info-value reason-text">{{ $pendaftaran->hasilUkt->alasan_override ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Kolom Kanan: Banding --}}
        @if ($hasBandingArea)
        <div class="col-lg-7">
            {{-- Ajukan Banding --}}
            @if ($pendaftaran->status === 'final_ukt' && !$pendaftaran->is_kip)
                <div class="card banding-card">
                    <div class="card-header banding-header">
                        <div class="header-left">
                            <span class="header-icon danger">
                                <i class="bi bi-chat-square-text-fill"></i>
                            </span>
                            <h6 class="header-title">Ajukan Banding</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        @if ($pendaftaran->banding && $pendaftaran->banding->whereIn('status', ['diajukan', 'diproses'])->count() > 0)
                            <div class="alert-process">
                                <i class="bi bi-info-circle-fill"></i>
                                Banding Anda sedang diproses oleh panitia.
                            </div>
                        @else
                            <p class="banding-desc">
                                <i class="bi bi-info-circle me-1"></i>
                                Jika Anda merasa hasil UKT tidak sesuai dengan kondisi ekonomi Anda,
                                silakan ajukan banding dengan mengisi alasan di bawah ini.
                            </p>
                            <form action="{{ route('mahasiswa.banding.store', $pendaftaran) }}" method="POST"
                                id="bandingForm">
                                @csrf
                                <div class="form-group">
                                    <label class="form-label">
                                        <i class="bi bi-chat-quote me-1"></i>
                                        Alasan Banding
                                    </label>
                                    <textarea name="alasan" class="form-control @error('alasan') is-invalid @enderror" rows="4"
                                        placeholder="Jelaskan alasan pengajuan banding Anda (minimal 20 karakter)..." id="alasanBanding">{{ old('alasan') }}</textarea>
                                    <small class="form-hint">
                                        <i class="bi bi-info-circle"></i> Minimal 20 karakter
                                    </small>
                                    @error('alasan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-banding" onclick="return confirmBanding(event)">
                                    <i class="bi bi-send me-1"></i> Ajukan Banding
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Riwayat Banding --}}
            @if ($pendaftaran->banding && $pendaftaran->banding->count() > 0)
                <div class="card history-card mt-4">
                    <div class="card-header history-header">
                        <div class="header-left">
                            <span class="header-icon warning">
                                <i class="bi bi-clock-history"></i>
                            </span>
                            <h6 class="header-title">Riwayat Banding</h6>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @foreach ($pendaftaran->banding->sortByDesc('created_at') as $banding)
                            <div class="history-item">
                                <div class="history-top">
                                    <span
                                        class="badge-status 
                                        @if ($banding->status === 'diajukan') status-warning
                                        @elseif($banding->status === 'diproses') status-info
                                        @elseif($banding->status === 'diterima') status-success
                                        @elseif($banding->status === 'ditolak') status-danger
                                        @else status-default @endif">
                                        @if ($banding->status === 'diajukan')
                                            <i class="bi bi-hourglass-split"></i> Diajukan
                                        @elseif($banding->status === 'diproses')
                                            <i class="bi bi-arrow-repeat"></i> Diproses
                                        @elseif($banding->status === 'diterima')
                                            <i class="bi bi-check-circle"></i> Diterima
                                        @elseif($banding->status === 'ditolak')
                                            <i class="bi bi-x-circle"></i> Ditolak
                                        @else
                                            {{ ucfirst($banding->status) }}
                                        @endif
                                    </span>
                                    <span class="history-date">
                                        <i class="bi bi-calendar3 me-1"></i>
                                        {{ $banding->created_at->translatedFormat('d F Y H:i') }}
                                    </span>
                                </div>
                                <div class="history-alasan">
                                    <i class="bi bi-chat-quote me-1"></i>
                                    {{ $banding->alasan }}
                                </div>
                                @if ($banding->keputusan)
                                    <div class="history-keputusan">
                                        <i class="bi bi-chat-dots me-1"></i>
                                        {{ $banding->keputusan }}
                                    </div>
                                @endif
                                @if ($banding->golongan_baru && $banding->status === 'diterima')
                                    <div class="history-golongan">
                                        <i class="bi bi-star-fill me-1"></i>
                                        Golongan Baru: {{ $banding->golongan_baru }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
        @endif
    </div>

    {{-- Baris Aksi: Terima & Kembali (selalu di tengah) --}}
    <div class="row justify-content-center mt-1">
        <div class="{{ $hasBandingArea ? 'col-lg-12' : 'col-lg-7 col-xl-6 text-center' }}">
            {{-- Tombol Terima Keputusan --}}
            @if ($pendaftaran->status === 'final_ukt')
                <div class="mt-4">
                    <form action="{{ route('mahasiswa.hasil-ukt.terima', $pendaftaran) }}" method="POST"
                        data-confirm="Dengan menerima, Anda tidak dapat mengajukan banding lagi. Lanjutkan?" @csrf <button
                        type="submit" class="btn btn-accept">
                        <i class="bi bi-check2-circle me-1"></i> Terima Keputusan UKT
                        </button>
                    </form>
                </div>
            @endif

            {{-- Tombol Kembali --}}
            <div class="mt-3">
                <a href="{{ route('mahasiswa.pendaftaran.show', $pendaftaran) }}" class="btn-back">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Detail Pendaftaran
                </a>
            </div>
        </div>
    </div>

    <style>
        /* ===== RESULT CARD ===== */
        .result-card {
            background: linear-gradient(145deg, #0f2b4a 0%, #1a4a7a 100%);
            color: #fff;
            border: none;
            border-radius: 24px;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            position: relative;
        }

        .result-card::before {
            content: '';
            position: absolute;
            top: -40%;
            right: -20%;
            width: 250px;
            height: 250px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.06) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .result-icon {
            width: 72px;
            height: 72px;
            background: rgba(255, 215, 0, 0.12);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
        }

        .result-icon i {
            font-size: 2.4rem;
            color: #fbbf24;
        }

        .result-icon.kip-icon {
            background: rgba(251, 191, 36, 0.15);
        }

        .result-icon.kip-icon i {
            color: #f59e0b;
        }

        .result-title {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: rgba(255, 255, 255, 0.6);
            margin-bottom: 4px;
            font-weight: 600;
        }

        .kip-badge {
            display: inline-block;
            background: rgba(251, 191, 36, 0.15);
            color: #fcd34d;
            padding: 4px 16px;
            border-radius: 40px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 12px;
        }

        .golongan-display {
            font-size: 4.5rem;
            font-weight: 900;
            line-height: 1;
            margin-bottom: 8px;
            text-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            letter-spacing: -2px;
        }

        .nominal-wrapper {
            background: rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            padding: 10px 20px;
            margin: 12px 0;
        }

        .nominal-wrapper.kip-wrapper {
            background: rgba(251, 191, 36, 0.10);
        }

        .nominal-label {
            display: block;
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255, 255, 255, 0.5);
        }

        .nominal-value {
            display: block;
            font-size: 1.2rem;
            font-weight: 700;
        }

        .akreditation-info {
            font-size: 0.7rem;
            color: rgba(255, 255, 255, 0.5);
            margin-top: 4px;
        }

        .status-banner {
            padding: 8px 16px;
            border-radius: 40px;
            font-size: 0.75rem;
            font-weight: 600;
            margin: 12px 0;
        }

        .status-banner.status-kip {
            background: rgba(251, 191, 36, 0.15);
            color: #fcd34d;
        }

        .status-banner.status-nonkip {
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
        }

        .override-badge {
            background: rgba(251, 191, 36, 0.12);
            color: #fbbf24;
            padding: 4px 14px;
            border-radius: 40px;
            font-size: 0.7rem;
            display: inline-block;
            margin-bottom: 12px;
        }

        .result-footer {
            display: flex;
            justify-content: space-between;
            padding-top: 16px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            gap: 16px;
        }

        .footer-item {
            text-align: left;
        }

        .footer-label {
            display: block;
            font-size: 0.55rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: rgba(255, 255, 255, 0.4);
        }

        .footer-value {
            display: block;
            font-size: 0.75rem;
            font-weight: 600;
            margin-top: 2px;
        }

        /* ===== INFO CARD ===== */
        .info-card,
        .override-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
            overflow: hidden;
        }

        .override-card {
            border-left: 4px solid #f59e0b;
        }

        .info-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 12px;
        }

        .info-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            flex-shrink: 0;
        }

        .info-icon.success {
            background: #d1fae5;
            color: #10b981;
        }

        .info-icon.warning {
            background: #fef3c7;
            color: #f59e0b;
        }

        .info-title {
            font-weight: 600;
            margin: 0;
            font-size: 0.9rem;
        }

        .info-grid {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            flex-wrap: wrap;
            gap: 6px;
        }

        .info-label {
            font-size: 0.7rem;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .info-value {
            font-size: 0.8rem;
            font-weight: 600;
            color: #1e293b;
        }

        .reason-text {
            background: #fef3c7;
            padding: 4px 12px;
            border-radius: 12px;
            width: 100%;
            font-weight: 400;
        }

        /* ===== BANDING CARD ===== */
        .banding-card,
        .history-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
            overflow: hidden;
        }

        .banding-header,
        .history-header {
            background: #fff;
            border-bottom: 1px solid #f1f5f9;
            padding: 16px 20px;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .header-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            flex-shrink: 0;
        }

        .header-icon.danger {
            background: #fee2e2;
            color: #dc2626;
        }

        .header-icon.warning {
            background: #fef3c7;
            color: #f59e0b;
        }

        .header-title {
            font-weight: 600;
            margin: 0;
            font-size: 0.9rem;
        }

        .banding-desc {
            font-size: 0.8rem;
            color: #64748b;
            margin-bottom: 16px;
        }

        .alert-process {
            background: #cffafe;
            padding: 12px 16px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.8rem;
            color: #0e7490;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-label {
            font-weight: 600;
            font-size: 0.8rem;
            color: #1e293b;
            margin-bottom: 6px;
            display: block;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 0.85rem;
            transition: all 0.2s;
        }

        .form-control:focus {
            outline: none;
            border-color: #dc2626;
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.08);
        }

        .form-control.is-invalid {
            border-color: #dc2626;
        }

        .form-hint {
            display: block;
            font-size: 0.7rem;
            color: #94a3b8;
            margin-top: 4px;
        }

        .invalid-feedback {
            color: #dc2626;
            font-size: 0.75rem;
            margin-top: 4px;
        }

        .btn-banding {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            border: none;
            padding: 10px 28px;
            border-radius: 40px;
            color: #fff;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.2s;
            cursor: pointer;
        }

        .btn-banding:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(220, 38, 38, 0.30);
            color: #fff;
        }

        /* ===== HISTORY ===== */
        .history-item {
            padding: 16px 20px;
            border-bottom: 1px solid #f1f5f9;
        }

        .history-item:last-child {
            border-bottom: none;
        }

        .history-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 8px;
        }

        .badge-status {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 12px;
            border-radius: 40px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .badge-status.status-warning {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-status.status-info {
            background: #cffafe;
            color: #0e7490;
        }

        .badge-status.status-success {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-status.status-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-status.status-default {
            background: #f1f5f9;
            color: #475569;
        }

        .history-date {
            font-size: 0.7rem;
            color: #94a3b8;
        }

        .history-alasan {
            font-size: 0.8rem;
            color: #475569;
            background: #f8fafc;
            padding: 8px 14px;
            border-radius: 12px;
            margin-bottom: 6px;
        }

        .history-keputusan {
            font-size: 0.8rem;
            color: #3b82f6;
            background: #eff6ff;
            padding: 8px 14px;
            border-radius: 12px;
            margin-bottom: 6px;
        }

        .history-golongan {
            font-size: 0.8rem;
            color: #10b981;
            background: #d1fae5;
            padding: 8px 14px;
            border-radius: 12px;
        }

        /* ===== BUTTONS ===== */
        .btn-accept {
            background: linear-gradient(135deg, #10b981, #059669);
            border: none;
            padding: 10px 28px;
            border-radius: 40px;
            color: #fff;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.2s;
            cursor: pointer;
        }

        .btn-accept:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.30);
            color: #fff;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            padding: 8px 20px;
            background: #f1f5f9;
            color: #475569;
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

        /* ===== RESPONSIVE ===== */
        @media (max-width: 992px) {
            .golongan-display {
                font-size: 3.5rem;
            }

            .result-footer {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .footer-item {
                text-align: center;
            }
        }

        @media (max-width: 576px) {
            .result-card .card-body {
                padding: 24px !important;
            }

            .golongan-display {
                font-size: 3rem;
            }

            .result-icon {
                width: 60px;
                height: 60px;
            }

            .result-icon i {
                font-size: 2rem;
            }

            .history-top {
                flex-direction: column;
                align-items: flex-start;
            }

            .info-row {
                flex-direction: column;
            }

            .reason-text {
                width: auto;
            }
        }

        /* ===== ANIMATION ===== */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(24px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .result-card,
        .info-card,
        .override-card,
        .banding-card,
        .history-card {
            animation: fadeInUp 0.5s ease forwards;
            opacity: 0;
        }

        .result-card {
            animation-delay: 0.05s;
        }

        .info-card {
            animation-delay: 0.10s;
        }

        .override-card {
            animation-delay: 0.15s;
        }

        .banding-card {
            animation-delay: 0.10s;
        }

        .history-card {
            animation-delay: 0.15s;
        }
    </style>

    <script>
        function confirmBanding(event) {
            event.preventDefault();
            const alasan = document.getElementById('alasanBanding');
            if (alasan && alasan.value.trim().length < 20) {
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
    </script>
@endsection
