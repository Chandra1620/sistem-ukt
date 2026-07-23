@extends('layouts.app')
@section('title', 'Detail User')
@section('page-title', 'Detail User')

@section('content')
    <div class="row g-4 justify-content-center">
        <div class="col-md-6">
            <div class="card premium-card">
                <div class="card-header premium-card-header">
                    <div class="d-flex align-items-center gap-2">
                        <div class="header-icon primary-icon">
                            <i class="bi bi-person-circle"></i>
                        </div>
                        <h6 class="fw-semibold mb-0">Info User</h6>
                    </div>
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn-edit">
                        <i class="bi bi-pencil-square"></i> Edit User
                    </a>
                </div>
                <div class="card-body p-4">
                    {{-- Avatar Premium --}}
                    <div class="text-center mb-4">
                        <div class="avatar-wrapper">
                            <div class="user-avatar-large"
                                style="background: linear-gradient(135deg, {{ $user->avatar_color ?? '#3b82f6' }}, {{ $user->avatar_color_secondary ?? '#2563eb' }})">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div class="avatar-badge">
                                <i class="bi bi-shield-check"></i>
                            </div>
                        </div>
                        <h5 class="fw-bold mb-1 mt-3">{{ $user->name }}</h5>
                        <p class="text-muted mb-2">
                            <i class="bi bi-envelope-fill"></i> {{ $user->email }}
                        </p>
                        <div class="role-badges">
                            @foreach ($user->roles as $role)
                                <span class="role-badge role-{{ $role->name }}">
                                    <i class="bi {{ $role->icon ?? 'bi-shield-check' }}"></i>
                                    {{ ucfirst($role->name) }}
                                </span>
                            @endforeach
                        </div>
                    </div>

                    <hr class="my-4">

                    {{-- Informasi Detail --}}
                    <div class="info-grid">
                        <div class="info-card-item">
                            <div class="info-icon">
                                <i class="bi bi-calendar-plus"></i>
                            </div>
                            <div class="info-content">
                                <div class="info-label">Terdaftar</div>
                                <div class="info-value">{{ $user->created_at->format('d M Y') }}</div>
                                <div class="info-time">{{ $user->created_at->format('H:i') }} WIB</div>
                            </div>
                        </div>
                        <div class="info-card-item">
                            <div class="info-icon">
                                <i class="bi bi-calendar-check"></i>
                            </div>
                            <div class="info-content">
                                <div class="info-label">Terakhir Update</div>
                                <div class="info-value">{{ $user->updated_at->format('d M Y') }}</div>
                                <div class="info-time">{{ $user->updated_at->format('H:i') }} WIB</div>
                            </div>
                        </div>
                        <div class="info-card-item">
                            <div class="info-icon">
                                <i class="bi bi-envelope-check"></i>
                            </div>
                            <div class="info-content">
                                <div class="info-label">Email Verified</div>
                                <div class="info-value">
                                    @if ($user->email_verified_at)
                                        <span class="verified-badge">
                                            <i class="bi bi-check-circle-fill"></i> Terverifikasi
                                        </span>
                                    @else
                                        <span class="unverified-badge">
                                            <i class="bi bi-exclamation-circle-fill"></i> Belum Terverifikasi
                                        </span>
                                    @endif
                                </div>
                                @if ($user->email_verified_at)
                                    <div class="info-time">{{ $user->email_verified_at->format('d M Y H:i') }} WIB</div>
                                @endif
                            </div>
                        </div>
                        <div class="info-card-item">
                            <div class="info-icon">
                                <i class="bi bi-file-earmark-text"></i>
                            </div>
                            <div class="info-content">
                                <div class="info-label">Total Pendaftaran</div>
                                <div class="info-value">
                                    <span class="pendaftar-count">{{ $user->pendaftaran->count() }}</span>
                                    <span class="pendaftar-unit">Pendaftaran</span>
                                </div>
                                @if ($user->pendaftaran->count() > 0)
                                    <div class="info-time">
                                        Terakhir: {{ $user->pendaftaran->last()->created_at->format('d M Y') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Statistik Ringkas (Jika ada pendaftaran) --}}
                    @if ($user->pendaftaran->count() > 0)
                        <div class="stats-preview mt-4">
                            <div class="stats-header">
                                <i class="bi bi-graph-up"></i>
                                <span>Statistik Ringkas</span>
                            </div>
                            <div class="stats-grid">
                                <div class="stat-mini">
                                    <div class="stat-mini-value">
                                        {{ $user->pendaftaran->where('status', 'selesai')->count() }}</div>
                                    <div class="stat-mini-label">Selesai</div>
                                </div>
                                <div class="stat-mini">
                                    <div class="stat-mini-value">
                                        {{ $user->pendaftaran->where('status', 'final_ukt')->count() }}</div>
                                    <div class="stat-mini-label">Final UKT</div>
                                </div>
                                <div class="stat-mini">
                                    <div class="stat-mini-value">
                                        {{ $user->pendaftaran->where('status', 'menunggu_verifikasi')->count() }}</div>
                                    <div class="stat-mini-label">Menunggu</div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="card-footer bg-transparent border-top-0 pb-4">
                    <div class="action-footer">
                        <a href="{{ route('admin.users.index') }}" class="btn-back">
                            <i class="bi bi-arrow-left"></i> Kembali ke Daftar User
                        </a>
                        @if ($user->id !== auth()->id())
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                class="d-inline delete-form" onsubmit="return confirmDelete(event)">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-delete">
                                    <i class="bi bi-trash3"></i> Hapus User
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Premium Card */
        .premium-card {
            border: none;
            border-radius: 28px;
            box-shadow: 0 8px 28px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .premium-card:hover {
            box-shadow: 0 12px 36px rgba(0, 0, 0, 0.12);
        }

        /* Card Header */
        .premium-card-header {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border-bottom: 1px solid #e2e8f0;
            padding: 20px 28px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }

        .header-icon {
            width: 40px;
            height: 40px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
        }

        .primary-icon {
            background: linear-gradient(135deg, #dbeafe, #bfdbfe);
            color: #3b82f6;
        }

        /* Tombol Edit */
        .btn-edit {
            background: transparent;
            border: 1px solid #f59e0b;
            color: #f59e0b;
            padding: 6px 14px;
            border-radius: 40px;
            font-size: 0.7rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-edit:hover {
            background: #f59e0b;
            color: white;
            transform: translateY(-2px);
        }

        /* Avatar Premium */
        .avatar-wrapper {
            position: relative;
            display: inline-block;
        }

        .user-avatar-large {
            width: 100px;
            height: 100px;
            border-radius: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 2.5rem;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            margin: 0 auto;
            transition: transform 0.3s ease;
        }

        .user-avatar-large:hover {
            transform: scale(1.05);
        }

        .avatar-badge {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, #10b981, #059669);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            border: 3px solid white;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .avatar-badge i {
            font-size: 0.8rem;
        }

        /* Role Badges */
        .role-badges {
            display: flex;
            gap: 8px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .role-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 14px;
            border-radius: 40px;
            font-size: 0.75rem;
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

        /* Info Grid */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }

        .info-card-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 12px;
            background: #f8fafc;
            border-radius: 16px;
            transition: all 0.2s;
        }

        .info-card-item:hover {
            background: #f1f5f9;
            transform: translateY(-2px);
        }

        .info-icon {
            width: 40px;
            height: 40px;
            background: white;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #3b82f6;
            font-size: 1.1rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }

        .info-content {
            flex: 1;
        }

        .info-label {
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #94a3b8;
            margin-bottom: 4px;
        }

        .info-value {
            font-size: 0.85rem;
            font-weight: 600;
            color: #1e293b;
        }

        .info-time {
            font-size: 0.6rem;
            color: #94a3b8;
            margin-top: 2px;
        }

        /* Verified Badge */
        .verified-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            color: #10b981;
        }

        .unverified-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            color: #f59e0b;
        }

        .pendaftar-count {
            font-size: 1rem;
            font-weight: 700;
            color: #3b82f6;
        }

        .pendaftar-unit {
            font-size: 0.7rem;
            color: #94a3b8;
            margin-left: 4px;
        }

        /* Stats Preview */
        .stats-preview {
            background: linear-gradient(135deg, #eef2ff, #e0e7ff);
            border-radius: 16px;
            padding: 12px 16px;
        }

        .stats-header {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.7rem;
            font-weight: 600;
            color: #3b82f6;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px solid rgba(59, 130, 246, 0.2);
        }

        .stats-grid {
            display: flex;
            justify-content: space-around;
            gap: 12px;
        }

        .stat-mini {
            text-align: center;
            flex: 1;
        }

        .stat-mini-value {
            font-size: 1.1rem;
            font-weight: 800;
            color: #1e293b;
        }

        .stat-mini-label {
            font-size: 0.6rem;
            color: #64748b;
            margin-top: 4px;
        }

        /* Action Footer */
        .action-footer {
            display: flex;
            gap: 12px;
            justify-content: space-between;
            flex-wrap: wrap;
        }

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

        .btn-delete {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #fee2e2;
            color: #dc2626;
            padding: 8px 20px;
            border-radius: 40px;
            font-size: 0.8rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
        }

        .btn-delete:hover {
            background: #dc2626;
            color: white;
            transform: translateY(-2px);
        }

        /* Delete Form */
        .delete-form {
            display: inline-block;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .premium-card-header {
                padding: 16px 20px;
                flex-direction: column;
                text-align: center;
            }

            .card-body {
                padding: 20px !important;
            }

            .info-grid {
                grid-template-columns: 1fr;
                gap: 12px;
            }

            .action-footer {
                flex-direction: column;
            }

            .btn-back,
            .btn-delete {
                justify-content: center;
            }

            .stats-grid {
                flex-wrap: wrap;
            }
        }

        @media (max-width: 480px) {
            .user-avatar-large {
                width: 80px;
                height: 80px;
                font-size: 2rem;
            }

            .avatar-badge {
                width: 28px;
                height: 28px;
            }

            .avatar-badge i {
                font-size: 0.7rem;
            }

            .info-card-item {
                padding: 10px;
            }

            .info-icon {
                width: 36px;
                height: 36px;
                font-size: 1rem;
            }

            .info-value {
                font-size: 0.8rem;
            }
        }

        /* Animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .premium-card {
            animation: fadeInUp 0.4s ease;
        }

        .info-card-item {
            animation: fadeInUp 0.3s ease forwards;
            opacity: 0;
        }

        .info-card-item:nth-child(1) {
            animation-delay: 0.05s;
        }

        .info-card-item:nth-child(2) {
            animation-delay: 0.1s;
        }

        .info-card-item:nth-child(3) {
            animation-delay: 0.15s;
        }

        .info-card-item:nth-child(4) {
            animation-delay: 0.2s;
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
    </script>
@endsection
