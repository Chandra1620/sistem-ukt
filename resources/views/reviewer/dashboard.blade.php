@extends('layouts.app')
@section('title', 'Dashboard Reviewer')
@section('page-title', 'Dashboard Reviewer')

@section('content')
    {{-- Stat Cards Premium --}}
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card stat-card-warning">
                <div class="stat-icon-wrapper">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Menunggu Verifikasi</div>
                    <div class="stat-value">{{ $stats['menunggu_verifikasi'] }}</div>
                </div>
                <div class="stat-trend">
                    <i class="bi bi-arrow-right"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card stat-card-success">
                <div class="stat-icon-wrapper">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Sudah Diverifikasi</div>
                    <div class="stat-value">{{ $stats['diverifikasi'] }}</div>
                </div>
                <div class="stat-trend">
                    <i class="bi bi-check-lg"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card stat-card-danger">
                <div class="stat-icon-wrapper">
                    <i class="bi bi-x-circle"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Ditolak</div>
                    <div class="stat-value">{{ $stats['ditolak'] }}</div>
                </div>
                <div class="stat-trend">
                    <i class="bi bi-x-lg"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card stat-card-primary">
                <div class="stat-icon-wrapper">
                    <i class="bi bi-person-check"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Total Saya Verifikasi</div>
                    <div class="stat-value">{{ $stats['total_diverifikasi'] }}</div>
                </div>
                <div class="stat-trend">
                    <i class="bi bi-trophy"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Antrian Verifikasi Premium --}}
        <div class="col-lg-8">
            <div class="card premium-card">
                <div class="card-header premium-card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <div class="header-icon">
                                <i class="bi bi-list-task"></i>
                            </div>
                            <h6 class="fw-semibold mb-0">Antrian Verifikasi</h6>
                            @if ($antrian->count() > 0)
                                <span class="badge-queue">{{ $antrian->count() }} Menunggu</span>
                            @endif
                        </div>
                        <a href="{{ route('reviewer.verifikasi.index') }}" class="btn-view-all">
                            <i class="bi bi-arrow-right-circle"></i> Lihat Semua
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if ($antrian->count() > 0)
                        <div class="table-responsive">
                            <table class="table premium-table">
                                <thead>
                                    <tr>
                                        <th><i class="bi bi-hash"></i> No. Pendaftaran</th>
                                        <th><i class="bi bi-person"></i> Nama Mahasiswa</th>
                                        <th><i class="bi bi-building"></i> Program Studi</th>
                                        <th><i class="bi bi-calendar"></i> Tanggal Submit</th>
                                        <th><i class="bi bi-gear"></i> Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($antrian as $p)
                                        <tr class="queue-row">
                                            <td>
                                                <span class="badge-id">{{ $p->no_pendaftaran }}</span>
                                            </td>
                                            <td class="fw-semibold">{{ $p->nama_lengkap }}</td>
                                            <td>{{ $p->programStudi->nama }}</td>
                                            <td class="text-muted small">
                                                <i class="bi bi-clock"></i>
                                                {{ $p->updated_at->diffForHumans() }}
                                                <div class="date-tooltip">{{ $p->updated_at->format('d M Y H:i') }}</div>
                                            </td>
                                            <td>
                                                <a href="{{ route('reviewer.verifikasi.show', $p) }}" class="btn-verify">
                                                    <i class="bi bi-eye"></i> Periksa
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state-queue">
                            <div class="empty-icon">
                                <i class="bi bi-check-all"></i>
                            </div>
                            <h6 class="mt-3">Semua Beres!</h6>
                            <p class="text-muted small">Tidak ada antrian verifikasi saat ini</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Riwayat Verifikasi Saya Premium --}}
        <div class="col-lg-4">
            <div class="card history-premium-card">
                <div class="card-header history-card-header">
                    <div class="d-flex align-items-center gap-2">
                        <div class="header-icon small-icon">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <h6 class="fw-semibold mb-0">Riwayat Verifikasi Saya</h6>
                    </div>
                    <div class="history-badge">
                        <i class="bi bi-journal-bookmark-fill"></i>
                        {{ $riwayatVerifikasi->count() }}
                    </div>
                </div>
                <div class="card-body p-0">
                    @if ($riwayatVerifikasi->count() > 0)
                        <div class="timeline-wrapper">
                            @foreach ($riwayatVerifikasi as $v)
                                <div class="timeline-item">
                                    <div class="timeline-marker {{ $v->warna_badge }}"></div>
                                    <div class="timeline-content">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <div class="timeline-title">{{ $v->pendaftaran->nama_lengkap }}</div>
                                                <div class="timeline-subtitle">
                                                    {{ $v->pendaftaran->no_pendaftaran }}
                                                </div>
                                            </div>
                                            <span class="status-badge status-{{ $v->warna_badge }}">
                                                <i class="bi {{ $v->icon_status }}"></i>
                                                {{ $v->label_hasil }}
                                            </span>
                                        </div>
                                        @if ($v->komentar)
                                            <div class="timeline-comment">
                                                <i class="bi bi-chat-quote"></i>
                                                {{ Str::limit($v->komentar, 60) }}
                                            </div>
                                        @endif
                                        <div class="timeline-time">
                                            <i class="bi bi-calendar3"></i>
                                            {{ $v->verified_at->format('d M Y') }}
                                            <span class="time-detail">{{ $v->verified_at->format('H:i') }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state-history">
                            <div class="empty-icon-small">
                                <i class="bi bi-inbox"></i>
                            </div>
                            <p class="small text-muted mt-2 mb-0">Belum ada riwayat verifikasi</p>
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

        .stat-card-warning {
            color: #f59e0b;
        }

        .stat-card-warning::before {
            background: linear-gradient(90deg, #f59e0b, #fbbf24);
        }

        .stat-card-success {
            color: #10b981;
        }

        .stat-card-success::before {
            background: linear-gradient(90deg, #10b981, #34d399);
        }

        .stat-card-danger {
            color: #ef4444;
        }

        .stat-card-danger::before {
            background: linear-gradient(90deg, #ef4444, #f87171);
        }

        .stat-card-primary {
            color: #3b82f6;
        }

        .stat-card-primary::before {
            background: linear-gradient(90deg, #3b82f6, #60a5fa);
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
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #f59e0b;
            font-size: 1.2rem;
        }

        .badge-queue {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
            padding: 4px 12px;
            border-radius: 40px;
            font-size: 0.7rem;
            font-weight: 600;
            margin-left: 8px;
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

        .btn-verify {
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

        .btn-verify:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
            color: white;
        }

        /* Timeline for History */
        .history-premium-card {
            border: none;
            border-radius: 24px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
            overflow: hidden;
            height: 100%;
        }

        .history-card-header {
            background: white;
            border-bottom: 1px solid #f0f2f5;
            padding: 18px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .small-icon {
            width: 32px;
            height: 32px;
            background: #eef2ff;
            border-radius: 10px;
            color: #3b82f6;
        }

        .history-badge {
            background: #f1f5f9;
            padding: 4px 12px;
            border-radius: 40px;
            font-size: 0.7rem;
            font-weight: 600;
            color: #1e293b;
        }

        .timeline-wrapper {
            max-height: 480px;
            overflow-y: auto;
            padding: 8px 0;
        }

        .timeline-item {
            display: flex;
            gap: 16px;
            padding: 16px 20px;
            border-bottom: 1px solid #f0f2f5;
            transition: all 0.2s;
        }

        .timeline-item:hover {
            background: #fafcff;
        }

        .timeline-marker {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-top: 6px;
            position: relative;
        }

        .timeline-marker::before {
            content: '';
            position: absolute;
            top: 10px;
            left: 4px;
            width: 2px;
            height: calc(100% + 20px);
            background: #e2e8f0;
        }

        .timeline-item:last-child .timeline-marker::before {
            display: none;
        }

        .timeline-marker.primary {
            background: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }

        .timeline-marker.success {
            background: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);
        }

        .timeline-marker.danger {
            background: #ef4444;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.2);
        }

        .timeline-marker.warning {
            background: #f59e0b;
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.2);
        }

        .timeline-content {
            flex: 1;
        }

        .timeline-title {
            font-weight: 600;
            font-size: 0.85rem;
            color: #1e293b;
        }

        .timeline-subtitle {
            font-size: 0.7rem;
            color: #94a3b8;
            font-family: monospace;
            margin-top: 2px;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 10px;
            border-radius: 40px;
            font-size: 0.65rem;
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

        .status-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-warning {
            background: #fed7aa;
            color: #9b3412;
        }

        .timeline-comment {
            font-size: 0.7rem;
            color: #64748b;
            margin-top: 6px;
            background: #f8fafc;
            padding: 6px 10px;
            border-radius: 12px;
            display: inline-block;
        }

        .timeline-comment i {
            margin-right: 4px;
            font-size: 0.6rem;
            color: #94a3b8;
        }

        .timeline-time {
            font-size: 0.6rem;
            color: #94a3b8;
            margin-top: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .time-detail {
            background: #f1f5f9;
            padding: 2px 6px;
            border-radius: 20px;
        }

        /* Empty States */
        .empty-state-queue {
            text-align: center;
            padding: 48px 24px;
        }

        .empty-icon {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, #d1fae5, #a7f3d0);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            font-size: 2rem;
            color: #10b981;
        }

        .empty-state-history {
            text-align: center;
            padding: 40px 24px;
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

        /* Tooltip for date */
        .date-tooltip {
            display: none;
            position: absolute;
            background: #1e293b;
            color: white;
            padding: 4px 8px;
            border-radius: 8px;
            font-size: 0.65rem;
            white-space: nowrap;
            z-index: 100;
        }

        td .text-muted {
            position: relative;
            cursor: help;
        }

        td .text-muted:hover .date-tooltip {
            display: block;
            bottom: 100%;
            left: 0;
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

            .timeline-item {
                padding: 12px 16px;
            }
        }

        /* Scrollbar */
        .timeline-wrapper::-webkit-scrollbar {
            width: 4px;
        }

        .timeline-wrapper::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 4px;
        }

        .timeline-wrapper::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        /* Animation for queue items */
        .queue-row {
            animation: fadeInUp 0.3s ease forwards;
            opacity: 0;
            transform: translateY(10px);
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .queue-row:nth-child(1) {
            animation-delay: 0.05s;
        }

        .queue-row:nth-child(2) {
            animation-delay: 0.1s;
        }

        .queue-row:nth-child(3) {
            animation-delay: 0.15s;
        }

        .queue-row:nth-child(4) {
            animation-delay: 0.2s;
        }

        .queue-row:nth-child(5) {
            animation-delay: 0.25s;
        }
    </style>
@endsection
