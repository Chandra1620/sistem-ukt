@extends('layouts.app')
@section('title', 'Pendaftaran Saya')
@section('page-title', 'Pendaftaran Saya')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h5 class="fw-bold mb-1">
                <i class="bi bi-file-earmark-text text-primary me-2"></i>
                Pendaftaran Saya
            </h5>
            <p class="text-muted small mb-0">Riwayat semua pendaftaran UKT Anda</p>
        </div>
        <a href="{{ route('mahasiswa.pendaftaran.create') }}" class="btn btn-primary btn-premium">
            <i class="bi bi-plus-circle me-1"></i> Daftar Baru
        </a>
    </div>

    <div class="card premium-card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table premium-table">
                    <thead>
                        <tr>
                            <th><i class="bi bi-hash"></i> No. Pendaftaran</th>
                            <th><i class="bi bi-calendar3"></i> Periode</th>
                            <th><i class="bi bi-building"></i> Program Studi</th>
                            <th><i class="bi bi-info-circle"></i> Status</th>
                            <th><i class="bi bi-trophy"></i> Golongan UKT</th>
                            <th><i class="bi bi-calendar-plus"></i> Tanggal Daftar</th>
                            <th><i class="bi bi-gear"></i> Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendaftaran as $p)
                            <tr class="pendaftaran-row">
                                <td data-label="No. Pendaftaran">
                                    <span class="no-pendaftaran">{{ $p->no_pendaftaran }}</span>
                                </td>
                                <td data-label="Periode">
                                    <span class="periode-name">{{ $p->periode->nama ?? '-' }}</span>
                                </td>
                                <td data-label="Program Studi">
                                    <div class="prodi-info">
                                        <i class="bi bi-mortarboard-fill text-muted"></i>
                                        <span>{{ $p->programStudi->nama }}</span>
                                    </div>
                                </td>
                                <td data-label="Status">
                                    <span class="status-badge status-{{ $p->warna_badge }}">
                                        <i class="bi {{ $p->status_icon ?? 'bi-circle' }}"></i>
                                        {{ $p->label_status }}
                                    </span>
                                </td>
                                <td data-label="Golongan UKT">
                                    @if ($p->hasilUkt && $p->hasilBolehDilihat())
                                        <div class="ukt-info">
                                            <span class="golongan-badge">
                                                Gol. {{ $p->hasilUkt->golongan_final }}
                                            </span>
                                            <div class="nominal-text">
                                                {{ $p->hasilUkt->nominal_format }}
                                            </div>
                                        </div>
                                    @elseif (in_array($p->status, ['sedang_dihitung', 'hasil_awal']))
                                        <span class="badge bg-warning-subtle text-warning-emphasis">
                                            <i class="bi bi-hourglass-split me-1"></i>Menunggu
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td data-label="Tanggal Daftar">
                                    <div class="date-info">
                                        <i class="bi bi-calendar3"></i>
                                        <span>{{ $p->created_at->format('d M Y') }}</span>
                                    </div>
                                    <div class="time-ago">{{ $p->created_at->diffForHumans() }}</div>
                                </td>
                                <td data-label="Aksi">
                                    <div class="action-buttons">
                                        <a href="{{ route('mahasiswa.pendaftaran.show', $p) }}" class="btn-action view"
                                            title="Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if ($p->bisaDiedit())
                                            <a href="{{ route('mahasiswa.pendaftaran.edit', $p) }}" class="btn-action edit"
                                                title="Edit">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                        @endif
                                        @if ($p->hasilBolehDilihat())
                                            <a href="{{ route('mahasiswa.hasil-ukt.show', $p) }}" class="btn-action hasil"
                                                title="Lihat Hasil UKT">
                                                <i class="bi bi-award"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                </table>
            @empty
                <tr>
                    <td colspan="7" class="empty-state">
                        <div class="empty-state-content">
                            <div class="empty-icon">
                                <i class="bi bi-file-earmark-x"></i>
                            </div>
                            <h6 class="mt-3 mb-1">Belum Ada Pendaftaran</h6>
                            <p class="text-muted small mb-3">Mulai dengan melakukan pendaftaran UKT pertama Anda</p>
                            <a href="{{ route('mahasiswa.pendaftaran.create') }}" class="btn btn-primary btn-sm">
                                <i class="bi bi-plus-circle"></i> Daftar Sekarang
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
                </tbody>
                </table>
            </div>
            @if ($pendaftaran->hasPages())
                <div class="pagination-wrapper">
                    {{ $pendaftaran->links() }}
                </div>
            @endif
        </div>
    </div>

    <style>
        /* Premium Button */
        .btn-premium {
            border-radius: 40px;
            padding: 8px 24px;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.2);
        }

        .btn-premium:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(59, 130, 246, 0.3);
        }

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

        /* Premium Table */
        .premium-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px;
        }

        .premium-table thead th {
            background: #f8fafc;
            padding: 16px 20px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            border-bottom: 2px solid #e2e8f0;
        }

        .premium-table thead th i {
            margin-right: 6px;
            font-size: 0.75rem;
        }

        .premium-table tbody tr {
            transition: all 0.2s ease;
            border-bottom: 1px solid #f1f5f9;
        }

        .premium-table tbody tr:hover {
            background: #fafcff;
        }

        .premium-table tbody td {
            padding: 16px 20px;
            vertical-align: middle;
        }

        /* No Pendaftaran */
        .no-pendaftaran {
            font-family: monospace;
            font-weight: 700;
            font-size: 0.85rem;
            background: #f1f5f9;
            padding: 4px 10px;
            border-radius: 40px;
            display: inline-block;
        }

        /* Periode Name */
        .periode-name {
            font-weight: 500;
            color: #1e293b;
        }

        /* Prodi Info */
        .prodi-info {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .prodi-info i {
            font-size: 0.9rem;
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

        .status-secondary {
            background: #f1f5f9;
            color: #475569;
        }

        .status-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        /* UKT Info */
        .ukt-info {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .golongan-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            padding: 3px 10px;
            border-radius: 40px;
            font-size: 0.7rem;
            font-weight: 700;
            color: #92400e;
            width: fit-content;
        }

        .nominal-text {
            font-size: 0.7rem;
            font-weight: 600;
            color: #10b981;
        }

        /* Date Info */
        .date-info {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.75rem;
            color: #1e293b;
        }

        .time-ago {
            font-size: 0.65rem;
            color: #94a3b8;
            margin-top: 2px;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn-action {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            font-size: 0.9rem;
            transition: all 0.2s ease;
            text-decoration: none;
            border: none;
            cursor: pointer;
        }

        .btn-action.view {
            background: #eef2ff;
            color: #3b82f6;
        }

        .btn-action.view:hover {
            background: #3b82f6;
            color: white;
            transform: translateY(-2px);
        }

        .btn-action.edit {
            background: #fef3c7;
            color: #f59e0b;
        }

        .btn-action.edit:hover {
            background: #f59e0b;
            color: white;
            transform: translateY(-2px);
        }

        .btn-action.hasil {
            background: #d1fae5;
            color: #10b981;
        }

        .btn-action.hasil:hover {
            background: #10b981;
            color: white;
            transform: translateY(-2px);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 48px 24px !important;
        }

        .empty-state-content {
            text-align: center;
        }

        .empty-icon {
            width: 72px;
            height: 72px;
            background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            font-size: 2rem;
            color: #94a3b8;
        }

        /* Pagination */
        .pagination-wrapper {
            padding: 16px 20px;
            border-top: 1px solid #f0f2f5;
            background: white;
        }

        .pagination-wrapper nav {
            display: flex;
            justify-content: center;
        }

        .pagination-wrapper .pagination {
            gap: 4px;
            flex-wrap: wrap;
            margin: 0;
        }

        .pagination-wrapper .page-link {
            border-radius: 40px !important;
            border: none;
            padding: 6px 12px;
            color: #475569;
            font-weight: 500;
            transition: all 0.2s;
        }

        .pagination-wrapper .page-link:hover {
            background: #eef2ff;
            color: #3b82f6;
        }

        .pagination-wrapper .active .page-link {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
        }

        /* Animations */
        .pendaftaran-row {
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

        .pendaftaran-row:nth-child(1) {
            animation-delay: 0.03s;
        }

        .pendaftaran-row:nth-child(2) {
            animation-delay: 0.06s;
        }

        .pendaftaran-row:nth-child(3) {
            animation-delay: 0.09s;
        }

        .pendaftaran-row:nth-child(4) {
            animation-delay: 0.12s;
        }

        .pendaftaran-row:nth-child(5) {
            animation-delay: 0.15s;
        }

        /* Tooltip */
        .custom-tooltip {
            position: fixed;
            background: #1e293b;
            color: white;
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 0.7rem;
            z-index: 1000;
            pointer-events: none;
            white-space: nowrap;
        }

        /* ============================================ */
        /* RESPONSIVE STYLES */
        /* ============================================ */

        /* Tablet (max 992px) */
        @media (max-width: 992px) {
            .premium-table thead th {
                padding: 12px 16px;
                font-size: 0.65rem;
            }

            .premium-table tbody td {
                padding: 12px 16px;
            }
        }

        /* Mobile (max 768px) */
        @media (max-width: 768px) {
            .d-flex.justify-content-between {
                flex-direction: column;
                align-items: stretch !important;
            }

            .btn-premium {
                text-align: center;
                width: 100%;
            }

            /* Table menjadi card style */
            .premium-table thead {
                display: none;
            }

            .premium-table tbody tr {
                display: block;
                margin-bottom: 16px;
                border-radius: 16px;
                border: 1px solid #e2e8f0;
                background: white;
            }

            .premium-table tbody td {
                display: flex;
                align-items: flex-start;
                gap: 12px;
                padding: 12px 16px;
                border-bottom: 1px solid #f1f5f9;
            }

            .premium-table tbody td:last-child {
                border-bottom: none;
            }

            .premium-table tbody td::before {
                content: attr(data-label);
                font-weight: 600;
                font-size: 0.7rem;
                text-transform: uppercase;
                color: #64748b;
                min-width: 100px;
                flex-shrink: 0;
            }

            .no-pendaftaran {
                font-size: 0.75rem;
            }

            .action-buttons {
                justify-content: flex-start;
            }

            .btn-action {
                width: 36px;
                height: 36px;
            }

            .ukt-info {
                width: 100%;
            }
        }

        /* Small Mobile (max 480px) */
        @media (max-width: 480px) {
            .premium-table tbody td {
                flex-direction: column;
                gap: 8px;
            }

            .premium-table tbody td::before {
                min-width: auto;
            }

            .prodi-info {
                flex-wrap: wrap;
            }

            .date-info {
                flex-wrap: wrap;
            }

            .action-buttons {
                flex-wrap: wrap;
            }

            .btn-action {
                width: 32px;
                height: 32px;
                font-size: 0.8rem;
            }

            .empty-icon {
                width: 56px;
                height: 56px;
                font-size: 1.5rem;
            }
        }

        /* Extra Small (max 360px) */
        @media (max-width: 360px) {
            .premium-table tbody td {
                padding: 10px 12px;
            }

            .no-pendaftaran {
                font-size: 0.7rem;
                padding: 3px 8px;
            }

            .status-badge {
                font-size: 0.6rem;
                padding: 3px 8px;
            }

            .golongan-badge {
                font-size: 0.6rem;
                padding: 2px 8px;
            }

            .nominal-text {
                font-size: 0.65rem;
            }
        }
    </style>

    <script>
        // Tooltips
        document.querySelectorAll('.btn-action').forEach(btn => {
            let tooltipTimeout;
            let tooltipElement;

            btn.addEventListener('mouseenter', function(e) {
                const title = this.getAttribute('title');
                if (title && window.innerWidth > 768) {
                    tooltipTimeout = setTimeout(() => {
                        tooltipElement = document.createElement('div');
                        tooltipElement.className = 'custom-tooltip';
                        tooltipElement.textContent = title;

                        const rect = this.getBoundingClientRect();
                        let top = rect.top - 35;
                        let left = rect.left + (rect.width / 2) - 30;

                        if (top < 10) top = rect.bottom + 5;
                        if (left < 10) left = 10;
                        if (left + 60 > window.innerWidth) left = window.innerWidth - 70;

                        tooltipElement.style.top = top + 'px';
                        tooltipElement.style.left = left + 'px';

                        document.body.appendChild(tooltipElement);
                    }, 300);
                }
            });

            btn.addEventListener('mouseleave', function() {
                if (tooltipTimeout) {
                    clearTimeout(tooltipTimeout);
                }
                if (tooltipElement) {
                    tooltipElement.remove();
                    tooltipElement = null;
                }
            });
        });
    </script>
@endsection
