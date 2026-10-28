@extends('layouts.app')
@section('title', 'Audit Log')
@section('page-title', 'Audit Log Sistem')

@section('content')
    <div class="mb-4">
        <h5 class="fw-bold mb-1">
            <i class="bi bi-journal-text text-primary me-2"></i>
            Audit Log
        </h5>
        <p class="text-muted small mb-0">Riwayat semua aktivitas penting dalam sistem</p>
    </div>

    {{-- Filter --}}
    <div class="card filter-card mb-4">
        <div class="card-body py-3">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <div class="search-input-wrapper">
                        <i class="bi bi-search"></i>
                        <input type="text" name="search" class="search-input-filter"
                            placeholder="Cari aksi atau user..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <select name="aksi" class="form-select-modern">
                        <option value="">Semua Aksi</option>
                        @foreach ($aksiList as $aksi)
                            <option value="{{ $aksi }}" {{ request('aksi') === $aksi ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $aksi)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="tanggal_mulai" class="date-input-modern"
                        value="{{ request('tanggal_mulai') }}" placeholder="Dari tanggal">
                </div>
                <div class="col-md-2">
                    <input type="date" name="tanggal_selesai" class="date-input-modern"
                        value="{{ request('tanggal_selesai') }}" placeholder="Sampai tanggal">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn-search-filter w-100" aria-label="Search">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
                <div class="col-md-1">
                    <a href="{{ route('admin.audit-log.index') }}" class="btn-reset-filter w-100" aria-label="Reset Filter">
                        <i class="bi bi-arrow-repeat"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card premium-card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table premium-table">
                    <thead>
                        <tr>
                            <th><i class="bi bi-clock"></i> Waktu</th>
                            <th><i class="bi bi-person"></i> User</th>
                            <th><i class="bi bi-tag"></i> Aksi</th>
                            <th><i class="bi bi-database"></i> Model</th>
                            <th><i class="bi bi-arrow-left-right"></i> Data Lama</th>
                            <th><i class="bi bi-arrow-left-right"></i> Data Baru</th>
                            <th><i class="bi bi-wifi"></i> IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr class="log-row">
                                <td data-label="Waktu">
                                    <div class="time-cell">
                                        <div class="time-main">{{ $log->created_at?->format('d M Y H:i') ?? '-' }}</div>
                                        <div class="time-relative">{{ $log->created_at?->diffForHumans() ?? '-' }}</div>
                                    </div>
                                </td>
                                <td data-label="User">
                                    @if ($log->user)
                                        <div class="user-cell">
                                            <div class="user-avatar-mini"
                                                style="background: linear-gradient(135deg, {{ $log->user->avatar_color ?? '#3b82f6' }}, {{ $log->user->avatar_color_secondary ?? '#2563eb' }})">
                                                {{ strtoupper(substr($log->user->name, 0, 1)) }}
                                            </div>
                                            <div class="user-info">
                                                <div class="user-name">{{ $log->user->name }}</div>
                                                <div class="user-email">{{ $log->user->email }}</div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="system-cell">
                                            <i class="bi bi-robot"></i>
                                            <span>System</span>
                                        </div>
                                    @endif
                                </td>
                                <td data-label="Aksi">
                                    @php
                                        $actionClass = match ($log->aksi) {
                                            'tambah', 'create', 'created', 'add', 'added' => 'action-create',
                                            'ubah', 'update', 'updated', 'edit', 'edited' => 'action-update',
                                            'hapus', 'delete', 'deleted', 'remove', 'removed' => 'action-delete',
                                            'override', 'overridden', 'force' => 'action-delete',
                                            'login', 'logout', 'auth' => 'action-login',
                                            default => 'action-default',
                                        };
                                    @endphp
                                    <span class="action-badge {{ $actionClass }}">
                                        <i class="bi {{ $log->action_icon ?? 'bi-info-circle' }}"></i>
                                        {{ $log->label_aksi ?? ucfirst(str_replace('_', ' ', $log->aksi)) }}
                                    </span>
                                </td>
                                <td data-label="Model">
                                    <div class="model-cell">
                                        <i class="bi bi-table"></i>
                                        <span>{{ $log->model ?? '-' }}</span>
                                        @if ($log->model_id)
                                            <span class="model-id">#{{ $log->model_id }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td data-label="Data Lama">
                                    @if ($log->data_lama)
                                        <button class="btn-data-view" data-bs-toggle="modal" data-bs-target="#dataModal"
                                            data-title="Data Lama"
                                            data-content="{{ json_encode($log->data_lama, JSON_PRETTY_PRINT) }}">
                                            <i class="bi bi-eye"></i> Lihat
                                        </button>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td data-label="Data Baru">
                                    @if ($log->data_baru)
                                        <button class="btn-data-view primary" data-bs-toggle="modal"
                                            data-bs-target="#dataModal" data-title="Data Baru"
                                            data-content="{{ json_encode($log->data_baru, JSON_PRETTY_PRINT) }}">
                                            <i class="bi bi-eye"></i> Lihat
                                        </button>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td data-label="IP Address">
                                    <div class="ip-cell">
                                        <i class="bi bi-router"></i>
                                        <span>{{ $log->ip_address ?? '-' }}</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="empty-state">
                                    <div class="empty-state-content">
                                        <div class="empty-icon">
                                            <i class="bi bi-journal-x"></i>
                                        </div>
                                        <h6 class="mt-3 mb-1">Belum Ada Log Aktivitas</h6>
                                        <p class="text-muted small mb-0">Log aktivitas akan muncul di sini</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination Compact Modern --}}
            @if ($logs->hasPages())
                <div class="pagination-wrapper">
                    <div class="pagination-modern">
                        {{-- Previous --}}
                        @if ($logs->onFirstPage())
                            <span class="page-nav disabled">
                                <i class="bi bi-chevron-left"></i>
                            </span>
                        @else
                            <a href="{{ $logs->previousPageUrl() }}" class="page-nav">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        @endif

                        {{-- Pages --}}
                        <div class="page-numbers">
                            @php
                                $start = max(1, $logs->currentPage() - 2);
                                $end = min($logs->lastPage(), $logs->currentPage() + 2);

                                if ($start > 1) {
                                    echo '<a href="' . $logs->url(1) . '" class="page-num">1</a>';
                                    if ($start > 2) {
                                        echo '<span class="page-dots">…</span>';
                                    }
                                }

                                for ($i = $start; $i <= $end; $i++) {
                                    if ($i == $logs->currentPage()) {
                                        echo '<span class="page-num active">' . $i . '</span>';
                                    } else {
                                        echo '<a href="' . $logs->url($i) . '" class="page-num">' . $i . '</a>';
                                    }
                                }

                                if ($end < $logs->lastPage()) {
                                    if ($end < $logs->lastPage() - 1) {
                                        echo '<span class="page-dots">…</span>';
                                    }
                                    echo '<a href="' .
                                        $logs->url($logs->lastPage()) .
                                        '" class="page-num">' .
                                        $logs->lastPage() .
                                        '</a>';
                                }
                            @endphp
                        </div>

                        {{-- Next --}}
                        @if ($logs->hasMorePages())
                            <a href="{{ $logs->nextPageUrl() }}" class="page-nav">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        @else
                            <span class="page-nav disabled">
                                <i class="bi bi-chevron-right"></i>
                            </span>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Modal Data --}}
    <div class="modal fade" id="dataModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content premium-modal-content">
                <div class="modal-header premium-modal-header">
                    <div class="d-flex align-items-center gap-2">
                        <div class="modal-icon">
                            <i class="bi bi-database"></i>
                        </div>
                        <h6 class="modal-title fw-semibold" id="modalTitle">Detail Data</h6>
                    </div>
                    <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="data-content-wrapper">
                        <pre id="modalContent" class="data-preview"></pre>
                    </div>
                </div>
                <div class="modal-footer premium-modal-footer">
                    <button type="button" class="btn-copy-data" onclick="copyToClipboard()">
                        <i class="bi bi-clipboard"></i> Salin
                    </button>
                    <button type="button" class="btn-close-data" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Filter Card */
        .filter-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
            overflow: hidden;
        }

        .search-input-wrapper {
            position: relative;
        }

        .search-input-wrapper i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 0.9rem;
        }

        .search-input-filter {
            width: 100%;
            padding: 10px 16px 10px 38px;
            border: 1px solid #e2e8f0;
            border-radius: 40px;
            font-size: 0.85rem;
            transition: all 0.2s;
            background: white;
        }

        .search-input-filter:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-select-modern {
            width: 100%;
            padding: 10px 16px;
            border: 1px solid #e2e8f0;
            border-radius: 40px;
            font-size: 0.85rem;
            background: white;
            cursor: pointer;
            transition: all 0.2s;
            appearance: auto;
        }

        .form-select-modern:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .date-input-modern {
            width: 100%;
            padding: 10px 16px;
            border: 1px solid #e2e8f0;
            border-radius: 40px;
            font-size: 0.85rem;
            transition: all 0.2s;
            background: white;
        }

        .date-input-modern:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .btn-search-filter {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            border: none;
            border-radius: 40px;
            padding: 10px;
            color: white;
            transition: all 0.2s;
            cursor: pointer;
        }

        .btn-search-filter:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .btn-reset-filter {
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f1f5f9;
            border: none;
            border-radius: 40px;
            padding: 10px;
            color: #475569;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-reset-filter:hover {
            background: #e2e8f0;
            transform: translateY(-2px);
            color: #1e293b;
        }

        /* Premium Card */
        .premium-card {
            border: none;
            border-radius: 24px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }

        /* Premium Table */
        .premium-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 900px;
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

        .premium-table thead th i {
            margin-right: 6px;
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

        /* Time Cell */
        .time-cell {
            display: flex;
            flex-direction: column;
        }

        .time-main {
            font-size: 0.75rem;
            font-weight: 500;
            color: #1e293b;
        }

        .time-relative {
            font-size: 0.65rem;
            color: #94a3b8;
        }

        /* User Cell */
        .user-cell {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar-mini {
            width: 32px;
            height: 32px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 0.8rem;
            flex-shrink: 0;
        }

        .user-info {
            flex: 1;
            min-width: 0;
        }

        .user-name {
            font-size: 0.8rem;
            font-weight: 600;
            color: #1e293b;
        }

        .user-email {
            font-size: 0.65rem;
            color: #94a3b8;
            word-break: break-all;
        }

        .system-cell {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #f1f5f9;
            padding: 4px 12px;
            border-radius: 40px;
            font-size: 0.7rem;
            color: #475569;
        }

        /* Action Badge */
        .action-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 12px;
            border-radius: 40px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .action-create {
            background: #d1fae5;
            color: #065f46;
        }

        .action-update {
            background: #dbeafe;
            color: #1e40af;
        }

        .action-delete {
            background: #fee2e2;
            color: #991b1b;
        }

        .action-login {
            background: #cffafe;
            color: #0e7490;
        }

        .action-default {
            background: #f1f5f9;
            color: #475569;
        }

        /* Model Cell */
        .model-cell {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.75rem;
            flex-wrap: wrap;
        }

        .model-id {
            background: #f1f5f9;
            padding: 2px 6px;
            border-radius: 20px;
            font-size: 0.65rem;
            font-family: monospace;
        }

        /* IP Cell */
        .ip-cell {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #f8fafc;
            padding: 4px 10px;
            border-radius: 40px;
            font-size: 0.7rem;
            font-family: monospace;
        }

        /* Data View Button */
        .btn-data-view {
            background: #f1f5f9;
            border: none;
            padding: 4px 12px;
            border-radius: 40px;
            font-size: 0.7rem;
            font-weight: 500;
            color: #475569;
            transition: all 0.2s;
            cursor: pointer;
        }

        .btn-data-view:hover {
            background: #3b82f6;
            color: white;
        }

        .btn-data-view.primary {
            background: #eef2ff;
            color: #3b82f6;
        }

        .btn-data-view.primary:hover {
            background: #3b82f6;
            color: white;
        }

        /* Modal Premium */
        .premium-modal-content {
            border: none;
            border-radius: 24px;
            overflow: hidden;
        }

        .premium-modal-header {
            background: linear-gradient(135deg, #ffffff, #f8fafc);
            border-bottom: 1px solid #e2e8f0;
            padding: 16px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-icon {
            width: 32px;
            height: 32px;
            background: #eef2ff;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #3b82f6;
            flex-shrink: 0;
        }

        .btn-close-modal {
            background: #f1f5f9;
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            cursor: pointer;
        }

        .btn-close-modal:hover {
            background: #e2e8f0;
            transform: rotate(90deg);
        }

        .data-content-wrapper {
            max-height: 500px;
            overflow-y: auto;
        }

        .data-preview {
            background: #1e293b;
            color: #e2e8f0;
            padding: 16px;
            border-radius: 16px;
            font-size: 0.75rem;
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
            overflow-x: auto;
            margin: 0;
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        .premium-modal-footer {
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 12px 20px;
            display: flex;
            gap: 12px;
            justify-content: flex-end;
        }

        .btn-copy-data {
            background: #eef2ff;
            border: none;
            padding: 6px 16px;
            border-radius: 40px;
            font-size: 0.7rem;
            font-weight: 500;
            color: #3b82f6;
            transition: all 0.2s;
            cursor: pointer;
        }

        .btn-copy-data:hover {
            background: #3b82f6;
            color: white;
        }

        .btn-close-data {
            background: #f1f5f9;
            border: none;
            padding: 6px 16px;
            border-radius: 40px;
            font-size: 0.7rem;
            font-weight: 500;
            color: #475569;
            transition: all 0.2s;
            cursor: pointer;
        }

        .btn-close-data:hover {
            background: #e2e8f0;
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
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            font-size: 1.8rem;
            color: #94a3b8;
        }

        /* Pagination Wrapper */
        .pagination-wrapper {
            padding: 12px 20px;
            border-top: 1px solid #f0f2f5;
            background: white;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Pagination Modern Compact */
        .pagination-modern {
            display: flex;
            align-items: center;
            gap: 6px;
            background: #f8fafc;
            padding: 6px 12px;
            border-radius: 40px;
            border: 1px solid #e2e8f0;
        }

        .page-numbers {
            display: flex;
            align-items: center;
            gap: 2px;
        }

        .page-nav,
        .page-num {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 28px;
            height: 28px;
            padding: 0 6px;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 500;
            color: #475569;
            text-decoration: none;
            transition: all 0.2s;
            cursor: pointer;
            border: none;
            background: transparent;
        }

        .page-nav:hover,
        .page-num:hover {
            background: #eef2ff;
            color: #3b82f6;
            text-decoration: none;
        }

        .page-num.active {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            font-weight: 600;
        }

        .page-nav.disabled {
            opacity: 0.3;
            cursor: not-allowed;
            pointer-events: none;
        }

        .page-dots {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 20px;
            height: 28px;
            font-size: 0.75rem;
            color: #94a3b8;
        }

        /* Animations */
        .log-row {
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

        .log-row:nth-child(1) {
            animation-delay: 0.03s;
        }

        .log-row:nth-child(2) {
            animation-delay: 0.06s;
        }

        .log-row:nth-child(3) {
            animation-delay: 0.09s;
        }

        .log-row:nth-child(4) {
            animation-delay: 0.12s;
        }

        .log-row:nth-child(5) {
            animation-delay: 0.15s;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .premium-table thead th {
                padding: 10px 12px;
                font-size: 0.65rem;
            }

            .premium-table tbody td {
                padding: 10px 12px;
            }
        }

        @media (max-width: 768px) {
            .filter-card .row {
                display: flex;
                flex-direction: column;
                gap: 10px;
            }

            .filter-card .col-md-3,
            .filter-card .col-md-2,
            .filter-card .col-md-1 {
                width: 100%;
            }

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
                text-transform: uppercase;
                color: #64748b;
                min-width: 90px;
                flex-shrink: 0;
            }

            /* Pagination responsive */
            .pagination-modern {
                padding: 4px 8px;
                gap: 4px;
            }

            .page-nav,
            .page-num {
                min-width: 24px;
                height: 24px;
                font-size: 0.7rem;
                padding: 0 4px;
            }

            .page-dots {
                min-width: 16px;
                height: 24px;
                font-size: 0.7rem;
            }

            .pagination-wrapper {
                padding: 8px 12px;
                overflow-x: auto;
            }
        }

        @media (max-width: 480px) {
            .premium-table tbody td::before {
                min-width: 70px;
                font-size: 0.6rem;
            }

            .user-avatar-mini {
                width: 28px;
                height: 28px;
                font-size: 0.7rem;
            }

            .user-name {
                font-size: 0.75rem;
            }

            .user-email {
                font-size: 0.6rem;
            }

            /* Pagination mobile */
            .pagination-modern {
                padding: 3px 6px;
                gap: 2px;
                border-radius: 30px;
            }

            .page-nav,
            .page-num {
                min-width: 20px;
                height: 20px;
                font-size: 0.65rem;
                padding: 0 3px;
                border-radius: 6px;
            }

            .page-dots {
                min-width: 12px;
                height: 20px;
                font-size: 0.65rem;
            }
        }
    </style>

    <script>
        function showData(btn) {
            document.getElementById('modalTitle').innerHTML = '<i class="bi bi-database me-2"></i>' + btn.dataset.title;
            document.getElementById('modalContent').textContent = btn.dataset.content;
        }

        function copyToClipboard() {
            const content = document.getElementById('modalContent').textContent;
            if (!content || content.trim() === '') {
                return;
            }

            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(content).then(() => {
                    showToast('Data berhasil disalin!', 'success');
                }).catch(() => {
                    fallbackCopy(content);
                });
            } else {
                fallbackCopy(content);
            }
        }

        function fallbackCopy(text) {
            const textarea = document.createElement('textarea');
            textarea.value = text;
            textarea.style.position = 'fixed';
            textarea.style.opacity = '0';
            document.body.appendChild(textarea);
            textarea.select();
            try {
                document.execCommand('copy');
                showToast('Data berhasil disalin!', 'success');
            } catch (err) {
                showToast('Gagal menyalin data', 'danger');
            }
            document.body.removeChild(textarea);
        }

        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `position-fixed bottom-0 end-0 m-3 alert alert-${type} shadow-lg border-0`;
            toast.style.zIndex = '9999';
            toast.style.borderRadius = '40px';
            toast.style.padding = '10px 20px';
            toast.style.animation = 'fadeInUp 0.3s ease';
            toast.innerHTML =
                `<i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i> ${message}`;
            document.body.appendChild(toast);
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transition = 'opacity 0.3s';
                setTimeout(() => toast.remove(), 300);
            }, 2000);
        }

        // Event listener untuk modal
        document.addEventListener('DOMContentLoaded', function() {
            const dataModal = document.getElementById('dataModal');
            if (dataModal) {
                dataModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    if (button) {
                        showData(button);
                    }
                });
            }
        });
    </script>
@endsection
