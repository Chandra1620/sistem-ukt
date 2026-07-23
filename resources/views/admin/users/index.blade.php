@extends('layouts.app')
@section('title', 'Kelola User')
@section('page-title', 'Kelola User')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h5 class="fw-bold mb-1">
                <i class="bi bi-people-fill text-primary me-2"></i>
                Kelola User
            </h5>
            <p class="text-muted small mb-0">Manajemen akun pengguna sistem</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-premium">
            <i class="bi bi-person-plus me-1"></i> Tambah User
        </a>
    </div>

    {{-- Filter Premium --}}
    <div class="card filter-card mb-4">
        <div class="card-body py-3">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <div class="search-input-wrapper">
                        <i class="bi bi-search"></i>
                        <input type="text" name="search" class="search-input-filter"
                            placeholder="Cari nama atau email..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="role" class="form-select-modern">
                        <option value="">Semua Role</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->name }}" {{ request('role') === $role->name ? 'selected' : '' }}>
                                {{ ucfirst($role->name) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn-search">
                        <i class="bi bi-search"></i> Cari
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('admin.users.index') }}" class="btn-reset">
                        <i class="bi bi-arrow-repeat"></i> Reset
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
                            <th><i class="bi bi-person-circle"></i> Nama</th>
                            <th><i class="bi bi-envelope"></i> Email</th>
                            <th><i class="bi bi-shield-check"></i> Role</th>
                            <th><i class="bi bi-calendar3"></i> Terdaftar</th>
                            <th><i class="bi bi-gear"></i> Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr class="user-row">
                                <td data-label="Nama">
                                    <div class="user-info">
                                        <div class="user-avatar"
                                            style="background: linear-gradient(135deg, {{ $user->avatar_color ?? '#3b82f6' }}, {{ $user->avatar_color_secondary ?? '#2563eb' }})">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <span class="user-name">{{ $user->name }}</span>
                                    </div>
                                </td>
                                <td data-label="Email">
                                    <div class="email-info">
                                        <i class="bi bi-envelope-fill text-muted"></i>
                                        <span>{{ $user->email }}</span>
                                    </div>
                                </td>
                                <td data-label="Role">
                                    <div class="role-badges">
                                        @foreach ($user->roles as $role)
                                            <span class="role-badge role-{{ $role->name }}">
                                                <i class="bi {{ $role->icon ?? 'bi-shield-check' }}"></i>
                                                {{ ucfirst($role->name) }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td data-label="Terdaftar">
                                    <div class="date-info">
                                        <i class="bi bi-calendar3"></i>
                                        <span>{{ $user->created_at->format('d M Y') }}</span>
                                        <span class="time-ago">{{ $user->created_at->diffForHumans() }}</span>
                                    </div>
                                </td>
                                <td data-label="Aksi">
                                    <div class="action-buttons">
                                        <a href="{{ route('admin.users.show', $user) }}" class="btn-action view"
                                            title="Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.users.edit', $user) }}" class="btn-action edit"
                                            title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        @if ($user->id !== auth()->id())
                                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                                class="d-inline delete-form" onsubmit="return confirmDelete(event)">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn-action delete" title="Hapus">
                                                    <i class="bi bi-trash3"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="empty-state">
                                    <div class="empty-state-content">
                                        <div class="empty-icon">
                                            <i class="bi bi-people"></i>
                                        </div>
                                        <h6 class="mt-3 mb-1">Tidak Ada User Ditemukan</h6>
                                        <p class="text-muted small mb-3">Coba ubah kata kunci pencarian atau reset filter
                                        </p>
                                        <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
                                            <i class="bi bi-person-plus"></i> Tambah User Baru
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($users->hasPages())
                <div class="pagination-wrapper">
                    {{ $users->withQueryString()->links() }}
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
        }

        .form-select-modern:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .btn-search {
            width: 100%;
            padding: 10px 16px;
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            border: none;
            border-radius: 40px;
            color: white;
            font-weight: 500;
            font-size: 0.85rem;
            transition: all 0.2s;
        }

        .btn-search:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .btn-reset {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            width: 100%;
            padding: 10px 16px;
            background: #f1f5f9;
            border: none;
            border-radius: 40px;
            color: #475569;
            font-weight: 500;
            font-size: 0.85rem;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-reset:hover {
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
            min-width: 700px;
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
            transition: all 0.2s;
            border-bottom: 1px solid #f1f5f9;
        }

        .premium-table tbody tr:hover {
            background: #fafcff;
        }

        .premium-table tbody td {
            padding: 16px 20px;
            vertical-align: middle;
        }

        /* User Info */
        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 1rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .user-name {
            font-weight: 600;
            color: #1e293b;
        }

        /* Email Info */
        .email-info {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .email-info i {
            font-size: 0.9rem;
        }

        /* Role Badges */
        .role-badges {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
        }

        .role-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            border-radius: 40px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .role-admin {
            background: #fee2e2;
            color: #dc2626;
        }

        .role-reviewer {
            background: #fed7aa;
            color: #f59e0b;
        }

        .role-panitia {
            background: #cffafe;
            color: #06b6d4;
        }

        .role-management {
            background: #e2e8f0;
            color: #475569;
        }

        .role-mahasiswa {
            background: #d1fae5;
            color: #10b981;
        }

        /* Date Info */
        .date-info {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .date-info i {
            color: #94a3b8;
            font-size: 0.9rem;
        }

        .time-ago {
            font-size: 0.7rem;
            color: #94a3b8;
            background: #f1f5f9;
            padding: 2px 8px;
            border-radius: 40px;
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

        .btn-action.delete {
            background: #fee2e2;
            color: #ef4444;
        }

        .btn-action.delete:hover {
            background: #ef4444;
            color: white;
            transform: translateY(-2px);
        }

        /* Delete Form */
        .delete-form {
            display: inline-block;
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

        /* Pagination Wrapper */
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
        .user-row {
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

        .user-row:nth-child(1) {
            animation-delay: 0.03s;
        }

        .user-row:nth-child(2) {
            animation-delay: 0.06s;
        }

        .user-row:nth-child(3) {
            animation-delay: 0.09s;
        }

        .user-row:nth-child(4) {
            animation-delay: 0.12s;
        }

        .user-row:nth-child(5) {
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

            .user-name {
                font-size: 0.85rem;
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

            /* Filter menjadi column */
            .filter-card .row {
                display: flex;
                flex-direction: column;
                gap: 10px;
            }

            .filter-card .col-md-4,
            .filter-card .col-md-3,
            .filter-card .col-md-2 {
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
                min-width: 90px;
                flex-shrink: 0;
            }

            .user-info {
                flex-direction: column;
                align-items: flex-start;
            }

            .role-badges {
                flex-wrap: wrap;
            }

            .date-info {
                flex-wrap: wrap;
            }

            .action-buttons {
                justify-content: flex-start;
            }

            .btn-action {
                width: 36px;
                height: 36px;
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

            .user-info {
                flex-direction: row;
                align-items: center;
            }

            .user-avatar {
                width: 32px;
                height: 32px;
                font-size: 0.8rem;
            }

            .user-name {
                font-size: 0.8rem;
            }

            .role-badge {
                font-size: 0.6rem;
                padding: 3px 8px;
            }

            .time-ago {
                font-size: 0.6rem;
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

            .user-avatar {
                width: 28px;
                height: 28px;
                font-size: 0.7rem;
            }

            .email-info {
                flex-wrap: wrap;
            }

            .role-badge i {
                display: none;
            }

            .btn-action {
                width: 28px;
                height: 28px;
                font-size: 0.7rem;
            }
        }
    </style>

    <script>
        function confirmDelete(event) {
            event.preventDefault();
            const form = event.target.closest('form');

            swalConfirm('⚠️ Apakah Anda yakin ingin menghapus user ini?\n\nData user akan dihapus secara permanen.').then(
                function(ok) {
                    if (!ok) return;
                    form.submit();
                });
            return false;
        }

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
