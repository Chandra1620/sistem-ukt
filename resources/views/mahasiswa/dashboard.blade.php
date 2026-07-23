@extends('layouts.app')
@section('title', 'Dashboard Mahasiswa')
@section('page-title', 'Dashboard')

@section('content')
    <div class="row g-4 mb-4">
        {{-- Status Pendaftaran - Premium Card --}}
        <div class="col-12">
            @if ($pendaftaran)
                <div class="card premium-card border-0 shadow-lg overflow-hidden">
                    <div class="card-gradient-bg"></div>
                    <div class="card-body p-4 position-relative">
                        <div class="row align-items-start">
                            <div class="col-md-8">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="avatar-circle">
                                        <i class="bi bi-person-fill fs-3"></i>
                                    </div>
                                    <div>
                                        <h4 class="fw-bold mb-1">{{ $pendaftaran->nama_lengkap }}</h4>
                                        <div class="d-flex flex-wrap gap-3">
                                            <span class="text-muted small">
                                                <i class="bi bi-hash"></i> {{ $pendaftaran->no_pendaftaran }}
                                            </span>
                                            <span class="text-muted small">
                                                <i class="bi bi-book"></i> {{ $pendaftaran->programStudi->nama }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <span class="badge-status-custom bg-{{ $pendaftaran->warna_badge }}">
                                        <i class="bi {{ $pendaftaran->status_icon }} me-1"></i>
                                        {{ $pendaftaran->label_status }}
                                    </span>
                                </div>
                            </div>

                            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                @if ($pendaftaran->hasilUkt)
                                    @if ($pendaftaran->is_kip)
                                        <div class="ukt-card kip-card">
                                            <div class="text-uppercase small fw-semibold text-muted mb-1">
                                                Mahasiswa KIP
                                            </div>
                                            <div class="badge-nominal-kip">
                                                <i class="bi bi-cash-stack"></i>
                                                {{ $pendaftaran->hasilUkt->nominal_format }}
                                            </div>
                                            <div class="kip-source-label">
                                                <i class="bi bi-bank"></i> Dibiayai Pemerintah
                                            </div>
                                        </div>
                                    @else
                                        <div class="ukt-card">
                                            <div class="text-uppercase small fw-semibold text-muted mb-1">
                                                Golongan UKT
                                            </div>
                                            <div class="display-4 fw-bold text-gradient-primary">
                                                {{ $pendaftaran->hasilUkt->golongan_final }}
                                            </div>
                                            <div class="badge-nominal">
                                                <i class="bi bi-cash-stack"></i>
                                                {{ $pendaftaran->hasilUkt->nominal_format }}
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>

                        {{-- Progress Status Premium --}}
                        <div class="mt-4 pt-2">
                            @php
                                $steps = [
                                    'draft' => ['Draft', 1, 'bi bi-file-text'],
                                    'menunggu_verifikasi' => ['Menunggu Verifikasi', 2, 'bi bi-clock-history'],
                                    'diverifikasi' => ['Diverifikasi', 3, 'bi bi-check-circle'],
                                    'sedang_dihitung' => ['Dihitung', 4, 'bi bi-calculator'],
                                    'hasil_awal' => ['Hasil Awal', 5, 'bi bi-bar-chart'],
                                    'final_ukt' => ['Final UKT', 6, 'bi bi-award'],
                                    'selesai' => ['Selesai', 7, 'bi bi-flag-checkered'],
                                ];
                                $currentStep = $steps[$pendaftaran->status][1] ?? 1;
                                $totalSteps = 7;
                                $progress = ($currentStep / $totalSteps) * 100;
                            @endphp
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="small fw-semibold text-muted">
                                    <i class="bi bi-graph-up"></i> Progress Pendaftaran
                                </span>
                                <span class="small fw-bold text-primary">{{ round($progress) }}%</span>
                            </div>
                            <div class="progress premium-progress mb-3">
                                <div class="progress-bar progress-bar-animated" style="width: {{ $progress }}%;"></div>
                            </div>
                            <div class="step-indicators">
                                @foreach ($steps as $status => [$label, $step, $icon])
                                    <div class="step-item {{ $step <= $currentStep ? 'active' : '' }}">
                                        <div class="step-icon {{ $step == $currentStep ? 'current' : '' }}">
                                            <i class="{{ $icon }}"></i>
                                        </div>
                                        <div class="step-label">{{ $label }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Action Buttons Premium --}}
                        <div class="mt-4 d-flex flex-wrap gap-2">
                            <a href="{{ route('mahasiswa.pendaftaran.show', $pendaftaran) }}"
                                class="btn btn-primary btn-premium">
                                <i class="bi bi-eye"></i> Lihat Detail
                            </a>
                            @if ($pendaftaran->bisaDiedit())
                                <a href="{{ route('mahasiswa.pendaftaran.edit', $pendaftaran) }}"
                                    class="btn btn-outline-secondary btn-premium">
                                    <i class="bi bi-pencil-square"></i> Edit Data
                                </a>
                            @endif
                            @if (in_array($pendaftaran->status, ['hasil_awal', 'final_ukt', 'selesai']))
                                <a href="{{ route('mahasiswa.hasil-ukt.show', $pendaftaran) }}"
                                    class="btn btn-success btn-premium">
                                    <i class="bi bi-award"></i> Lihat Hasil UKT
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @else
                <div class="card empty-state-card border-0 shadow-lg">
                    <div class="card-body p-5 text-center">
                        <div class="empty-state-icon">
                            <i class="bi bi-file-earmark-plus"></i>
                        </div>
                        <h5 class="mt-4 fw-bold">Belum Ada Pendaftaran</h5>
                        <p class="text-muted mb-4">
                            @if ($periode)
                                <i class="bi bi-calendar-check"></i> Periode <strong>{{ $periode->nama }}</strong> sedang
                                aktif.<br>
                                Segera daftarkan diri Anda untuk mengikuti seleksi UKT.
                            @else
                                <i class="bi bi-calendar-x"></i> Belum ada periode seleksi yang aktif saat ini.
                            @endif
                        </p>
                        @if ($periode)
                            <a href="{{ route('mahasiswa.pendaftaran.create') }}" class="btn btn-primary btn-lg px-4">
                                <i class="bi bi-plus-circle"></i> Daftar Sekarang
                            </a>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        {{-- Info Periode & Statistik --}}
        <div class="col-md-4">
            @if ($periode)
                <div class="card info-card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h6 class="fw-semibold mb-0">
                                <i class="bi bi-calendar-event text-primary"></i> Periode Aktif
                            </h6>
                            <div class="pulse-dot"></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">
                                <i class="bi bi-tag"></i> Nama Periode
                            </div>
                            <div class="info-value fw-semibold">{{ $periode->nama }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">
                                <i class="bi bi-calendar-plus"></i> Tanggal Mulai
                            </div>
                            <div class="info-value">{{ $periode->tanggal_mulai->translatedFormat('d F Y') }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">
                                <i class="bi bi-calendar-minus"></i> Tanggal Selesai
                            </div>
                            <div class="info-value">{{ $periode->tanggal_selesai->translatedFormat('d F Y') }}</div>
                        </div>
                        @php
                            // Hitung hari tersisa dengan benar
                            $tanggalMulai = $periode->tanggal_mulai->startOfDay();
                            $tanggalSelesai = $periode->tanggal_selesai->startOfDay();
                            $sekarang = now()->startOfDay();

                            if ($sekarang < $tanggalMulai) {
                                $hariTersisa = $sekarang->diffInDays($tanggalMulai);
                                $status = 'belum_mulai';
                                $statusText = 'Mulai dalam';
                                $alertClass = 'alert-info';
                                $icon = 'bi-calendar-check';
                            } elseif ($sekarang <= $tanggalSelesai) {
                                $hariTersisa = $sekarang->diffInDays($tanggalSelesai) + 1;
                                $status = 'berlangsung';
                                $statusText = 'Tersisa';
                                $alertClass = 'alert-warning';
                                $icon = 'bi-hourglass-split';
                            } else {
                                $hariTersisa = 0;
                                $status = 'berakhir';
                                $statusText = 'Telah berakhir';
                                $alertClass = 'alert-secondary';
                                $icon = 'bi-calendar-x';
                            }
                        @endphp

                        @if ($status === 'berlangsung' && $hariTersisa > 0 && $hariTersisa <= 7)
                            <div class="deadline-warning">
                                <i class="bi {{ $icon }}"></i>
                                {{ $statusText }} <strong>{{ $hariTersisa }}</strong> hari lagi!
                            </div>
                        @elseif ($status === 'belum_mulai' && $hariTersisa <= 7)
                            <div class="alert alert-info mt-3 mb-0 py-2" style="background: #cffafe; border-radius: 10px;">
                                <i class="bi {{ $icon }}"></i>
                                {{ $statusText }} <strong>{{ $hariTersisa }}</strong> hari lagi!
                            </div>
                        @elseif ($status === 'berakhir')
                            <div class="alert alert-secondary mt-3 mb-0 py-2"
                                style="background: #e2e8f0; border-radius: 10px;">
                                <i class="bi {{ $icon }}"></i>
                                {{ $statusText }}
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        {{-- Riwayat Pendaftaran Premium --}}
        <div class="col-md-8">
            <div class="card history-card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="fw-semibold mb-0">
                            <i class="bi bi-clock-history text-primary"></i> Riwayat Pendaftaran
                        </h6>
                        <span class="badge bg-light text-dark">{{ $riwayat->count() }} Data</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if ($riwayat->count() > 0)
                        <div class="table-responsive">
                            <table class="table premium-table mb-0">
                                <thead>
                                    <tr>
                                        <th><i class="bi bi-hash"></i> No. Pendaftaran</th>
                                        <th><i class="bi bi-book"></i> Program Studi</th>
                                        <th><i class="bi bi-info-circle"></i> Status</th>
                                        <th><i class="bi bi-trophy"></i> Golongan</th>
                                        <th><i class="bi bi-calendar"></i> Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($riwayat as $r)
                                        <tr class="clickable-row"
                                            data-href="{{ route('mahasiswa.pendaftaran.show', $r) }}">
                                            <td data-label="No. Pendaftaran">
                                                <span class="badge bg-light text-dark">{{ $r->no_pendaftaran }}</span>
                                            </td>
                                            <td data-label="Program Studi" class="small">{{ $r->programStudi->nama }}
                                            </td>
                                            <td data-label="Status">
                                                <span class="status-badge status-{{ $r->warna_badge }}">
                                                    <i class="bi {{ $r->status_icon ?? 'bi-circle' }} me-1"></i>
                                                    {{ $r->label_status }}
                                                </span>
                                            </td>
                                            <td data-label="Golongan">
                                                @if ($r->hasilUkt)
                                                    @if ($r->is_kip)
                                                        <span class="kip-badge-small">
                                                            <i class="bi bi-award-fill"></i>
                                                            KIP
                                                        </span>
                                                    @else
                                                        <span class="golongan-badge">
                                                            <i class="bi bi-star-fill"></i>
                                                            Gol. {{ $r->hasilUkt->golongan_final }}
                                                        </span>
                                                    @endif
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td data-label="Tanggal" class="small text-muted">
                                                <i class="bi bi-calendar3"></i>
                                                {{ $r->created_at->format('d/m/Y') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state-small text-center py-5">
                            <i class="bi bi-inbox fs-1 text-muted"></i>
                            <p class="text-muted mt-2 mb-0">Belum ada riwayat pendaftaran</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Premium Card Styles */
        .premium-card {
            position: relative;
            overflow: hidden;
            border-radius: 28px !important;
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            transition: all 0.3s ease;
        }

        .premium-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 35px -12px rgba(0, 0, 0, 0.2);
        }

        .card-gradient-bg {
            position: absolute;
            top: -50%;
            right: -30%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.08) 0%, rgba(59, 130, 246, 0) 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .avatar-circle {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            box-shadow: 0 8px 20px rgba(59, 130, 246, 0.3);
            transition: all 0.3s ease;
        }

        .avatar-circle:hover {
            transform: scale(1.05);
        }

        .text-gradient-primary {
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            background-clip: text;
            -webkit-background-clip: text;
            color: transparent;
        }

        .badge-status-custom {
            display: inline-flex;
            align-items: center;
            padding: 6px 14px;
            border-radius: 40px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .ukt-card {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            padding: 16px 20px;
            border-radius: 24px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .ukt-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .badge-nominal {
            display: inline-block;
            background: rgba(0, 0, 0, 0.1);
            padding: 4px 12px;
            border-radius: 40px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-top: 8px;
        }

        /* Premium Progress */
        .premium-progress {
            height: 10px;
            border-radius: 10px;
            background: #e2e8f0;
        }

        .progress-bar {
            background: linear-gradient(90deg, #3b82f6 0%, #8b5cf6 100%);
            border-radius: 10px;
            position: relative;
            overflow: hidden;
        }

        .progress-bar::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            animation: shimmer 2s infinite;
        }

        @keyframes shimmer {
            0% {
                transform: translateX(-100%);
            }

            100% {
                transform: translateX(100%);
            }
        }

        /* Step Indicators */
        .step-indicators {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 8px;
        }

        .step-item {
            text-align: center;
            flex: 1;
            min-width: 60px;
        }

        .step-icon {
            width: 32px;
            height: 32px;
            background: #e2e8f0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 6px;
            transition: all 0.3s;
            font-size: 0.9rem;
            color: #64748b;
        }

        .step-item.active .step-icon {
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            color: white;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
        }

        .step-item.active .step-icon.current {
            transform: scale(1.1);
            animation: pulse 1s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.4);
            }

            50% {
                box-shadow: 0 0 0 8px rgba(59, 130, 246, 0);
            }
        }

        .step-label {
            font-size: 0.6rem;
            color: #94a3b8;
            font-weight: 500;
        }

        .step-item.active .step-label {
            color: #3b82f6;
            font-weight: 600;
        }

        /* Buttons */
        .btn-premium {
            border-radius: 40px;
            padding: 8px 20px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-premium:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        /* Empty State */
        .empty-state-card {
            border-radius: 28px !important;
            background: linear-gradient(135deg, #ffffff 0%, #fef9e3 100%);
            transition: all 0.3s ease;
        }

        .empty-state-card:hover {
            transform: translateY(-4px);
        }

        .empty-state-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            border-radius: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            color: white;
            font-size: 2.5rem;
            box-shadow: 0 12px 24px rgba(59, 130, 246, 0.3);
        }

        /* Info Card */
        .info-card {
            border-radius: 20px !important;
            transition: all 0.3s;
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        }

        .info-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.1);
        }

        .info-item {
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 1px solid #f0f0f0;
        }

        .info-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .info-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            color: #94a3b8;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .info-value {
            font-size: 1rem;
            color: #1e293b;
        }

        .pulse-dot {
            width: 10px;
            height: 10px;
            background: #10b981;
            border-radius: 50%;
            animation: pulse-green 1.5s infinite;
        }

        @keyframes pulse-green {

            0%,
            100% {
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4);
            }

            50% {
                box-shadow: 0 0 0 6px rgba(16, 185, 129, 0);
            }
        }

        .deadline-warning {
            background: #fef3c7;
            color: #d97706;
            padding: 6px 10px;
            border-radius: 10px;
            font-size: 0.7rem;
            font-weight: 600;
            margin-top: 10px;
            display: flex;
            align-items: center;
            gap: 6px;
            animation: pulse-warning 1.5s infinite;
        }

        @keyframes pulse-warning {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.8;
            }
        }

        /* Table Premium */
        .premium-table {
            width: 100%;
            border-collapse: collapse;
        }

        .premium-table thead th {
            background: #f8fafc;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            color: #64748b;
            padding: 14px 16px;
            border-bottom: 2px solid #e2e8f0;
        }

        .premium-table thead th i {
            margin-right: 4px;
        }

        .premium-table tbody tr {
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .premium-table tbody tr:hover {
            background: #f8fafc;
            transform: scale(1.01);
        }

        .premium-table tbody td {
            padding: 14px 16px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
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
            background: #e2e8f0;
            color: #475569;
        }

        .golongan-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            padding: 4px 10px;
            border-radius: 40px;
            font-size: 0.7rem;
            font-weight: 700;
            color: #92400e;
        }

        .kip-card {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        }

        .kip-icon-large {
            font-size: 2.5rem;
            color: #d97706;
            margin: 8px 0;
        }

        .badge-nominal-kip {
            display: inline-block;
            background: rgba(217, 119, 6, 0.15);
            color: #92400e;
            padding: 6px 14px;
            border-radius: 40px;
            font-size: 0.95rem;
            font-weight: 700;
            margin-top: 4px;
        }

        .kip-source-label {
            font-size: 0.65rem;
            color: #78350f;
            margin-top: 8px;
            font-weight: 500;
        }

        .kip-badge-small {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            padding: 4px 10px;
            border-radius: 40px;
            font-size: 0.7rem;
            font-weight: 700;
            color: #92400e;
        }

        .clickable-row {
            cursor: pointer;
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .step-label {
                font-size: 0.5rem;
            }

            .step-icon {
                width: 28px;
                height: 28px;
                font-size: 0.8rem;
            }
        }

        @media (max-width: 992px) {
            .step-indicators {
                gap: 4px;
            }

            .step-label {
                font-size: 0.45rem;
            }

            .step-icon {
                width: 24px;
                height: 24px;
                font-size: 0.7rem;
            }
        }

        @media (max-width: 768px) {
            .step-indicators {
                display: none;
            }

            .premium-table thead {
                display: none;
            }

            .premium-table tbody tr {
                display: block;
                margin-bottom: 12px;
                border: 1px solid #e2e8f0;
                border-radius: 12px;
            }

            .premium-table tbody td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 10px 12px;
                border-bottom: 1px solid #f1f5f9;
            }

            .premium-table tbody td:last-child {
                border-bottom: none;
            }

            .premium-table tbody td::before {
                content: attr(data-label);
                font-weight: 600;
                font-size: 0.65rem;
                color: #64748b;
            }

            .avatar-circle {
                width: 50px;
                height: 50px;
                font-size: 1.5rem;
            }

            .ukt-card {
                padding: 12px 16px;
            }

            .display-4 {
                font-size: 2rem;
            }
        }

        @media (max-width: 576px) {
            .premium-card .card-body {
                padding: 20px !important;
            }

            .avatar-circle {
                width: 45px;
                height: 45px;
            }

            .avatar-circle i {
                font-size: 1.2rem;
            }

            .ukt-card {
                margin-top: 16px;
            }

            .empty-state-icon {
                width: 60px;
                height: 60px;
                font-size: 1.8rem;
            }

            .info-value {
                font-size: 0.85rem;
            }

            .badge-status-custom {
                font-size: 0.7rem;
                padding: 4px 10px;
            }
        }

        @media (max-width: 380px) {
            .premium-table tbody td {
                flex-direction: column;
                gap: 8px;
                align-items: flex-start;
            }

            .premium-table tbody td::before {
                margin-bottom: 4px;
            }

            .status-badge {
                font-size: 0.6rem;
                padding: 3px 8px;
            }

            .golongan-badge {
                font-size: 0.6rem;
                padding: 3px 8px;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Make table rows clickable
            document.querySelectorAll('.clickable-row').forEach(row => {
                row.addEventListener('click', function() {
                    window.location.href = this.dataset.href;
                });
            });
        });
    </script>
@endsection
