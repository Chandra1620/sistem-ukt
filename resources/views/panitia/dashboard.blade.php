@extends('layouts.app')
@section('title', 'Dashboard Panitia')
@section('page-title', 'Dashboard Panitia')

@section('content')
    {{-- Stat Cards Premium --}}
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card stat-card-primary">
                <div class="stat-icon-wrapper">
                    <i class="bi bi-calculator-fill"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Menunggu Finalisasi</div>
                    <div class="stat-value">{{ $stats['hasil_awal'] }}</div>
                </div>
                <div class="stat-trend">
                    <i class="bi bi-hourglass-split"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card stat-card-success">
                <div class="stat-icon-wrapper">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Sudah Final</div>
                    <div class="stat-value">{{ $stats['final_ukt'] }}</div>
                </div>
                <div class="stat-trend">
                    <i class="bi bi-trophy"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card stat-card-warning">
                <div class="stat-icon-wrapper">
                    <i class="bi bi-chat-square-text-fill"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Banding Aktif</div>
                    <div class="stat-value">{{ $stats['banding_aktif'] }}</div>
                </div>
                <div class="stat-trend">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card stat-card-danger">
                <div class="stat-icon-wrapper">
                    <i class="bi bi-arrow-repeat"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Total Override</div>
                    <div class="stat-value">{{ $stats['total_override'] }}</div>
                </div>
                <div class="stat-trend">
                    <i class="bi bi-gear-fill"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Menunggu Finalisasi Premium --}}
        <div class="col-lg-8">
            <div class="card premium-card">
                <div class="card-header premium-card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <div class="header-icon primary-icon">
                                <i class="bi bi-hourglass-split"></i>
                            </div>
                            <h6 class="fw-semibold mb-0">Menunggu Finalisasi</h6>
                            @if ($menungguFinalisasi->count() > 0)
                                <span class="badge-queue primary-badge">{{ $menungguFinalisasi->count() }} Menunggu</span>
                            @endif
                        </div>
                        <a href="{{ route('panitia.hasil-saw.index') }}" class="btn-view-all">
                            <i class="bi bi-arrow-right-circle"></i> Lihat Semua
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if ($menungguFinalisasi->count() > 0)
                        <div class="table-responsive">
                            <table class="table premium-table">
                                <thead>
                                    <tr>
                                        <th><i class="bi bi-hash"></i> No. Pendaftaran</th>
                                        <th><i class="bi bi-person"></i> Nama Mahasiswa</th>
                                        <th><i class="bi bi-building"></i> Program Studi</th>
                                        <th><i class="bi bi-graph-up"></i> Skor SAW</th>
                                        <th><i class="bi bi-star"></i> Gol. Rekomendasi</th>
                                        <th><i class="bi bi-gear"></i> Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($menungguFinalisasi as $p)
                                        <tr class="finalization-row">
                                            <td>
                                                <span class="badge-id">{{ $p->no_pendaftaran }}</span>
                                            </td>
                                            <td class="fw-semibold">{{ $p->nama_lengkap }}</td>
                                            <td class="small">{{ $p->programStudi->nama }}</td>
                                            <td>
                                                @if ($p->hasilSaw)
                                                    <div class="score-badge">
                                                        <i class="bi bi-calculator-fill"></i>
                                                        {{ number_format($p->hasilSaw->skor_akhir, 2) }}
                                                    </div>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($p->hasilUkt)
                                                    @if ($p->hasilUkt->golongan_rekomendasi == 0)
                                                        <span class="golongan-rekomendasi badge-kip">
                                                            KIP
                                                        </span>
                                                    @else
                                                        <span class="golongan-rekomendasi">
                                                            Gol. {{ $p->hasilUkt->golongan_rekomendasi }}
                                                        </span>
                                                    @endif
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('panitia.hasil-saw.show', $p) }}" class="btn-action">
                                                    <i class="bi bi-eye"></i> Review
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state-finalization">
                            <div class="empty-icon success-icon">
                                <i class="bi bi-check-all"></i>
                            </div>
                            <h6 class="mt-3">Semua Telah Difinalisasi!</h6>
                            <p class="text-muted small">Tidak ada data yang menunggu finalisasi</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Banding Aktif Premium --}}
        <div class="col-lg-4">
            <div class="card banding-premium-card">
                <div class="card-header banding-card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <div class="header-icon warning-icon">
                                <i class="bi bi-chat-square-text-fill"></i>
                            </div>
                            <h6 class="fw-semibold mb-0">Banding Aktif</h6>
                            @if ($bandingTerbaru->count() > 0)
                                <span class="badge-queue warning-badge">{{ $bandingTerbaru->count() }} Aktif</span>
                            @endif
                        </div>
                        <a href="{{ route('panitia.banding.index') }}" class="btn-view-all warning-link">
                            <i class="bi bi-arrow-right-circle"></i> Lihat Semua
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if ($bandingTerbaru->count() > 0)
                        <div class="banding-list">
                            @foreach ($bandingTerbaru as $b)
                                <div class="banding-item">
                                    <div class="banding-status {{ $b->warna_badge }}">
                                        <div class="status-dot"></div>
                                        <span>{{ $b->label_status }}</span>
                                    </div>
                                    <div class="banding-info">
                                        <div class="banding-name">
                                            <i class="bi bi-person-circle"></i>
                                            {{ $b->pendaftaran->nama_lengkap }}
                                        </div>
                                        <div class="banding-number">
                                            {{ $b->pendaftaran->no_pendaftaran }}
                                        </div>
                                        <div class="banding-ukt-change">
                                            <div class="ukt-old">
                                                <i class="bi bi-arrow-left-right"></i>
                                                Gol. {{ $b->hasilUkt->golongan_final }}
                                            </div>
                                            <div class="ukt-arrow">
                                                <i class="bi bi-arrow-right-short"></i>
                                            </div>
                                            <div class="ukt-new">
                                                {{ $b->hasilUkt->nominal_format }}
                                            </div>
                                        </div>
                                        @if ($b->alasan)
                                            <div class="banding-reason">
                                                <i class="bi bi-chat-quote"></i>
                                                {{ Str::limit($b->alasan, 60) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="banding-action">
                                        <a href="{{ route('panitia.banding.show', $b) }}" class="btn-process">
                                            <i class="bi bi-arrow-right-circle"></i> Proses
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state-banding">
                            <div class="empty-icon-small">
                                <i class="bi bi-inbox"></i>
                            </div>
                            <p class="small text-muted mt-2 mb-0">Tidak ada banding aktif</p>
                            <p class="text-muted" style="font-size: 0.65rem;">Semua banding telah diproses</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Premium Stat Cards */
        .stat-card {
            background: white;
            border-radius: 24px;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, currentColor, transparent);
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
        }

        .stat-card-primary {
            color: #3b82f6;
        }

        .stat-card-primary::before {
            background: linear-gradient(90deg, #3b82f6, #60a5fa);
        }

        .stat-card-success {
            color: #10b981;
        }

        .stat-card-success::before {
            background: linear-gradient(90deg, #10b981, #34d399);
        }

        .stat-card-warning {
            color: #f59e0b;
        }

        .stat-card-warning::before {
            background: linear-gradient(90deg, #f59e0b, #fbbf24);
        }

        .stat-card-danger {
            color: #ef4444;
        }

        .stat-card-danger::before {
            background: linear-gradient(90deg, #ef4444, #f87171);
        }

        .stat-icon-wrapper {
            width: 56px;
            height: 56px;
            background: rgba(0, 0, 0, 0.04);
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            transition: all 0.3s;
        }

        .stat-card:hover .stat-icon-wrapper {
            transform: scale(1.05);
            background: rgba(0, 0, 0, 0.08);
        }

        .stat-info {
            flex: 1;
            text-align: center;
        }

        .stat-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            font-weight: 600;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            color: #1e293b;
            line-height: 1.2;
        }

        .stat-trend {
            width: 32px;
            height: 32px;
            background: rgba(0, 0, 0, 0.04);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            transition: all 0.3s;
        }

        /* Premium Card */
        .premium-card {
            border: none;
            border-radius: 24px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
            overflow: hidden;
            transition: all 0.3s;
            height: 100%;
        }

        .premium-card:hover {
            box-shadow: 0 8px 28px rgba(0, 0, 0, 0.08);
        }

        .premium-card-header {
            background: white;
            border-bottom: 1px solid #f0f2f5;
            padding: 18px 24px;
        }

        .header-icon {
            width: 36px;
            height: 36px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .primary-icon {
            background: linear-gradient(135deg, #dbeafe, #bfdbfe);
            color: #3b82f6;
        }

        .warning-icon {
            background: linear-gradient(135deg, #fed7aa, #fdba74);
            color: #f59e0b;
        }

        .badge-queue {
            padding: 4px 12px;
            border-radius: 40px;
            font-size: 0.7rem;
            font-weight: 600;
            margin-left: 8px;
        }

        .primary-badge {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
        }

        .warning-badge {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
        }

        .btn-view-all {
            background: transparent;
            border: none;
            color: #3b82f6;
            font-size: 0.8rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-view-all:hover {
            color: #2563eb;
            transform: translateX(4px);
        }

        .warning-link {
            color: #f59e0b;
        }

        .warning-link:hover {
            color: #d97706;
        }

        /* Premium Table */
        .premium-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .premium-table thead th {
            background: #f8fafc;
            padding: 14px 16px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            border-bottom: 2px solid #e2e8f0;
        }

        .premium-table tbody tr {
            transition: all 0.2s;
            border-bottom: 1px solid #f1f5f9;
        }

        .premium-table tbody tr:hover {
            background: #fafcff;
        }

        .premium-table tbody td {
            padding: 14px 16px;
            vertical-align: middle;
        }

        .badge-id {
            background: #f1f5f9;
            color: #1e293b;
            padding: 4px 10px;
            border-radius: 40px;
            font-size: 0.75rem;
            font-weight: 600;
            font-family: monospace;
        }

        .score-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: #eef2ff;
            color: #3b82f6;
            padding: 4px 10px;
            border-radius: 40px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .golongan-rekomendasi {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            color: #92400e;
            padding: 4px 10px;
            border-radius: 40px;
            font-size: 0.7rem;
            font-weight: 700;
        }

        .btn-action {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            padding: 6px 14px;
            border-radius: 40px;
            font-size: 0.7rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .btn-action:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
            color: white;
        }

        /* Banding Premium Card */
        .banding-premium-card {
            border: none;
            border-radius: 24px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
            overflow: hidden;
            height: 100%;
        }

        .banding-card-header {
            background: white;
            border-bottom: 1px solid #f0f2f5;
            padding: 18px 24px;
        }

        .banding-list {
            max-height: 520px;
            overflow-y: auto;
        }

        .banding-item {
            padding: 18px 20px;
            border-bottom: 1px solid #f0f2f5;
            transition: all 0.2s;
            position: relative;
        }

        .banding-item:hover {
            background: #fefaf5;
        }

        .banding-status {
            position: absolute;
            top: 18px;
            right: 20px;
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.65rem;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 40px;
        }

        .banding-status.primary {
            background: #dbeafe;
            color: #1e40af;
        }

        .banding-status.warning {
            background: #fed7aa;
            color: #9b3412;
        }

        .banding-status.success {
            background: #d1fae5;
            color: #065f46;
        }

        .status-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: currentColor;
        }

        .banding-info {
            margin-bottom: 12px;
            padding-right: 80px;
        }

        .banding-name {
            font-weight: 700;
            font-size: 0.9rem;
            color: #1e293b;
            margin-bottom: 4px;
        }

        .banding-name i {
            margin-right: 6px;
            font-size: 0.8rem;
            color: #64748b;
        }

        .banding-number {
            font-size: 0.7rem;
            color: #94a3b8;
            font-family: monospace;
            margin-bottom: 8px;
        }

        .banding-ukt-change {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #f8fafc;
            padding: 6px 12px;
            border-radius: 12px;
            margin-top: 8px;
        }

        .ukt-old {
            font-size: 0.7rem;
            color: #64748b;
            text-decoration: line-through;
        }

        .ukt-arrow {
            color: #f59e0b;
            font-size: 1rem;
        }

        .ukt-new {
            font-size: 0.8rem;
            font-weight: 700;
            color: #10b981;
        }

        .banding-reason {
            font-size: 0.7rem;
            color: #64748b;
            margin-top: 8px;
            background: #fefaf5;
            padding: 6px 10px;
            border-radius: 10px;
            border-left: 3px solid #f59e0b;
        }

        .banding-reason i {
            margin-right: 4px;
            color: #f59e0b;
        }

        .banding-action {
            margin-top: 12px;
        }

        .btn-process {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            background: transparent;
            border: 1px solid #f59e0b;
            color: #f59e0b;
            padding: 6px 14px;
            border-radius: 40px;
            font-size: 0.7rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
            width: 100%;
        }

        .btn-process:hover {
            background: #f59e0b;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
        }

        /* Empty States */
        .empty-state-finalization {
            text-align: center;
            padding: 48px 24px;
        }

        .empty-icon {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            font-size: 2rem;
        }

        .success-icon {
            background: linear-gradient(135deg, #d1fae5, #a7f3d0);
            color: #10b981;
        }

        .empty-state-banding {
            text-align: center;
            padding: 48px 24px;
        }

        .empty-icon-small {
            width: 48px;
            height: 48px;
            background: #f1f5f9;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            font-size: 1.5rem;
            color: #94a3b8;
        }

        /* Animations */
        .finalization-row {
            animation: fadeInUp 0.3s ease forwards;
            opacity: 0;
            transform: translateY(10px);
        }

        .banding-item {
            animation: fadeInLeft 0.3s ease forwards;
            opacity: 0;
            transform: translateX(-10px);
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInLeft {
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .finalization-row:nth-child(1) {
            animation-delay: 0.05s;
        }

        .finalization-row:nth-child(2) {
            animation-delay: 0.1s;
        }

        .finalization-row:nth-child(3) {
            animation-delay: 0.15s;
        }

        .finalization-row:nth-child(4) {
            animation-delay: 0.2s;
        }

        .banding-item:nth-child(1) {
            animation-delay: 0.05s;
        }

        .banding-item:nth-child(2) {
            animation-delay: 0.1s;
        }

        .banding-item:nth-child(3) {
            animation-delay: 0.15s;
        }

        /* Scrollbar */
        .banding-list::-webkit-scrollbar {
            width: 4px;
        }

        .banding-list::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 4px;
        }

        .banding-list::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .stat-card {
                padding: 16px;
            }

            .stat-value {
                font-size: 1.5rem;
            }

            .stat-icon-wrapper {
                width: 44px;
                height: 44px;
                font-size: 1.3rem;
            }

            .premium-table thead th:nth-child(3),
            .premium-table tbody td:nth-child(3) {
                display: none;
            }

            .banding-item {
                padding: 14px 16px;
            }

            .banding-info {
                padding-right: 0;
            }

            .banding-status {
                position: relative;
                top: auto;
                right: auto;
                display: inline-flex;
                margin-bottom: 10px;
            }
        }
    </style>
@endsection
