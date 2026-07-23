@extends('layouts.app')
@section('title', 'Profil Saya')
@section('page-title', 'Profil Saya')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                {{-- Breadcrumb --}}
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb bg-transparent p-0 mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}" class="text-decoration-none text-muted">
                                <i class="bi bi-house-door me-1"></i> Dashboard
                            </a>
                        </li>
                        <li class="breadcrumb-item active text-primary">
                            <i class="bi bi-person-circle me-1"></i> Profil Saya
                        </li>
                    </ol>
                </nav>

                {{-- Profile Hero Card --}}
                <div class="card border-0 shadow-lg rounded-4 mb-4 overflow-hidden">
                    <div class="profile-hero">
                        <div class="profile-hero-bg"></div>
                        <div class="profile-hero-content">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                <div class="d-flex align-items-center gap-4 flex-wrap">
                                    <div class="profile-avatar">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <h4 class="fw-bold mb-1 text-white">{{ $user->name }}</h4>
                                        <p class="mb-1 text-white-50">
                                            <i class="bi bi-envelope me-2"></i> {{ $user->email }}
                                        </p>
                                        <p class="mb-0 text-white-50 small">
                                            <i class="bi bi-calendar3 me-2"></i>
                                            Bergabung {{ $user->created_at->isoFormat('D MMMM Y') }}
                                        </p>
                                    </div>
                                </div>
                                <a href="{{ route('profile.edit') }}" class="btn btn-edit-profile">
                                    <i class="bi bi-pencil-square me-2"></i> Edit Profil
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Info Detail --}}
                <div class="card border-0 shadow-lg rounded-4 mb-4 overflow-hidden">
                    <div class="card-header-custom">
                        <i class="bi bi-info-circle-fill text-primary me-2"></i>
                        Informasi Akun
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="bi bi-person"></i>
                                    </div>
                                    <div class="info-text">
                                        <div class="info-label">Nama Lengkap</div>
                                        <div class="info-value">{{ $user->name }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="bi bi-envelope"></i>
                                    </div>
                                    <div class="info-text">
                                        <div class="info-label">Alamat Email</div>
                                        <div class="info-value">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="bi bi-check-circle"></i>
                                    </div>
                                    <div class="info-text">
                                        <div class="info-label">Verifikasi Email</div>
                                        <div class="info-value">
                                            @if ($user->email_verified_at)
                                                <span class="badge-verified">
                                                    <i class="bi bi-check-circle-fill me-1"></i> Terverifikasi
                                                </span>
                                                <span class="text-muted small ms-2">
                                                    {{ $user->email_verified_at->isoFormat('D MMM Y') }}
                                                </span>
                                            @else
                                                <span class="badge-unverified">
                                                    <i class="bi bi-exclamation-circle-fill me-1"></i> Belum Terverifikasi
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="bi bi-clock-history"></i>
                                    </div>
                                    <div class="info-text">
                                        <div class="info-label">Terakhir Diperbarui</div>
                                        <div class="info-value">
                                            {{ $user->updated_at->isoFormat('D MMMM Y, HH:mm') }}
                                            <span class="text-muted small">
                                                ({{ $user->updated_at->diffForHumans() }})
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Quick Actions --}}
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="card-header-custom">
                        <i class="bi bi-lightning-charge-fill text-warning me-2"></i>
                        Aksi Cepat
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex gap-3 flex-wrap">
                            <a href="{{ route('profile.edit') }}" class="btn-action btn-action-primary">
                                <i class="bi bi-pencil-square"></i>
                                <span>Edit Profil</span>
                            </a>
                            <a href="{{ route('dashboard') }}" class="btn-action btn-action-secondary">
                                <i class="bi bi-speedometer2"></i>
                                <span>Dashboard</span>
                            </a>
                            @if ($user->hasRole('mahasiswa') && ($pendaftaran ?? false))
                                <a href="{{ route('mahasiswa.pendaftaran.show', $pendaftaran) }}"
                                    class="btn-action btn-action-info">
                                    <i class="bi bi-eye"></i>
                                    <span>Lihat Pendaftaran</span>
                                </a>
                            @endif
                            <button class="btn-action btn-action-outline" id="refreshProfileBtn"
                                onclick="location.reload()">
                                <i class="bi bi-arrow-repeat"></i>
                                <span>Refresh</span>
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <style>
        /* Profile Hero */
        .profile-hero {
            position: relative;
            border-radius: 24px;
            overflow: hidden;
        }

        .profile-hero-bg {
            background: linear-gradient(135deg, #1e3a5f 0%, #2d6a9f 50%, #3b82f6 100%);
            padding: 48px 32px;
            position: relative;
        }

        .profile-hero-bg::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            pointer-events: none;
        }

        .profile-hero-bg::after {
            content: '';
            position: absolute;
            bottom: -50%;
            left: -10%;
            width: 250px;
            height: 250px;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 50%;
            pointer-events: none;
        }

        .profile-hero-content {
            position: relative;
            z-index: 1;
            background: linear-gradient(135deg, #1e3a5f 0%, #2d6a9f 50%, #3b82f6 100%);
            padding: 40px 32px;
        }

        .profile-avatar {
            width: 90px;
            height: 90px;
            background: white;
            border-radius: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            font-weight: 700;
            color: #1e3a5f;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease;
        }

        .profile-avatar:hover {
            transform: scale(1.05);
        }

        .btn-edit-profile {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 50px;
            padding: 10px 24px;
            color: white;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn-edit-profile:hover {
            background: white;
            color: #1e3a5f;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        /* Card Header */
        .card-header-custom {
            background: white;
            border-bottom: 1px solid #f0f2f5;
            padding: 18px 24px;
            font-weight: 600;
            font-size: 0.9rem;
            color: #1e293b;
        }

        /* Info Items */
        .info-item {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-radius: 20px;
            padding: 18px 20px;
            display: flex;
            align-items: center;
            gap: 16px;
            transition: all 0.3s ease;
            height: 100%;
        }

        .info-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        }

        .info-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #667eea20 0%, #764ba220 100%);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            color: #667eea;
            flex-shrink: 0;
        }

        .info-text {
            flex: 1;
        }

        .info-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.7px;
            color: #94a3b8;
            margin-bottom: 6px;
        }

        .info-value {
            font-weight: 600;
            color: #1e293b;
            font-size: 0.9rem;
        }

        /* Badges */
        .badge-verified {
            display: inline-flex;
            align-items: center;
            background: #d1fae5;
            color: #065f46;
            padding: 5px 14px;
            border-radius: 40px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-unverified {
            display: inline-flex;
            align-items: center;
            background: #fed7aa;
            color: #9b3412;
            padding: 5px 14px;
            border-radius: 40px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        /* Action Buttons */
        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 28px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.85rem;
            text-decoration: none;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
        }

        .btn-action i {
            font-size: 1.1rem;
        }

        .btn-action-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        .btn-action-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .btn-action-secondary {
            background: #f1f5f9;
            color: #475569;
        }

        .btn-action-secondary:hover {
            background: #e2e8f0;
            transform: translateY(-2px);
            color: #1e293b;
        }

        .btn-action-info {
            background: linear-gradient(135deg, #0ea5e9, #3b82f6);
            color: white;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .btn-action-info:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
            color: white;
        }

        .btn-action-outline {
            background: transparent;
            border: 1px solid #e2e8f0;
            color: #64748b;
        }

        .btn-action-outline:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
            transform: translateY(-2px);
            color: #1e293b;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .profile-hero-content {
                padding: 28px 20px;
            }

            .profile-avatar {
                width: 70px;
                height: 70px;
                font-size: 2rem;
                border-radius: 22px;
            }

            .btn-edit-profile {
                width: 100%;
                text-align: center;
                margin-top: 10px;
            }

            .info-item {
                flex-direction: column;
                text-align: center;
            }

            .info-text {
                text-align: center;
            }

            .btn-action {
                width: 100%;
                justify-content: center;
            }

            .d-flex.align-items-center.justify-content-between.flex-wrap {
                flex-direction: column !important;
            }

            .d-flex.align-items-center.gap-4.flex-wrap {
                justify-content: center;
                text-align: center;
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

        .info-item {
            animation: fadeInUp 0.3s ease forwards;
            opacity: 0;
        }

        .info-item:nth-child(1) {
            animation-delay: 0.05s;
        }

        .info-item:nth-child(2) {
            animation-delay: 0.1s;
        }

        .info-item:nth-child(3) {
            animation-delay: 0.15s;
        }

        .info-item:nth-child(4) {
            animation-delay: 0.2s;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tooltips initialization
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Refresh button animation
            const refreshBtn = document.getElementById('refreshProfileBtn');
            if (refreshBtn) {
                refreshBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const icon = this.querySelector('i');
                    icon.style.animation = 'spin 0.5s ease';
                    setTimeout(() => {
                        icon.style.animation = '';
                    }, 500);
                    location.reload();
                });
            }
        });

        // Spin animation
        const style = document.createElement('style');
        style.textContent = `
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    `;
        document.head.appendChild(style);
    </script>
@endsection
