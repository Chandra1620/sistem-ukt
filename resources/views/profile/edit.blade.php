@extends('layouts.app')
@section('title', 'Edit Profil')
@section('page-title', 'Edit Profil')

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
                        <li class="breadcrumb-item">
                            <a href="{{ route('profile.index') }}" class="text-decoration-none text-muted">
                                <i class="bi bi-person-circle me-1"></i> Profil Saya
                            </a>
                        </li>
                        <li class="breadcrumb-item active text-primary">
                            <i class="bi bi-pencil-square me-1"></i> Edit Profil
                        </li>
                    </ol>
                </nav>

                {{-- Success Alerts --}}
                @if (session('status') === 'profile-updated')
                    <div class="alert alert-success border-0 rounded-3 shadow-sm mb-4 alert-dismissible fade show">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-check-circle-fill fs-5 me-3"></i>
                            <div>
                                <strong class="fw-semibold">Berhasil!</strong> Profil Anda telah diperbarui.
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('status') === 'password-updated')
                    <div class="alert alert-success border-0 rounded-3 shadow-sm mb-4 alert-dismissible fade show">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-check-circle-fill fs-5 me-3"></i>
                            <div>
                                <strong class="fw-semibold">Berhasil!</strong> Password Anda telah diperbarui.
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                {{-- Update Profile Card --}}
                <div class="card border-0 shadow-lg rounded-4 mb-4 overflow-hidden">
                    <div class="card-header bg-gradient-primary text-white py-3 border-0">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-white bg-opacity-25 p-2 me-3">
                                <i class="bi bi-person-circle fs-5"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0">Informasi Profil</h5>
                                <p class="small mb-0 opacity-75">Perbarui data diri Anda</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4 p-lg-5">
                        <form method="POST" action="{{ route('profile.update') }}">
                            @csrf
                            @method('PATCH')

                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-person text-primary me-1"></i>
                                    Nama Lengkap
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="name"
                                    class="form-control form-control-lg @error('name') is-invalid @enderror"
                                    value="{{ old('name', $user->name) }}" placeholder="Masukkan nama lengkap Anda"
                                    required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-envelope text-primary me-1"></i>
                                    Alamat Email
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="email" name="email"
                                    class="form-control form-control-lg @error('email') is-invalid @enderror"
                                    value="{{ old('email', $user->email) }}" placeholder="Masukkan alamat email Anda"
                                    required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text mt-2">
                                    <i class="bi bi-info-circle text-info me-1"></i>
                                    Email akan digunakan untuk masuk ke sistem dan menerima notifikasi.
                                </div>
                            </div>

                            <div class="d-flex gap-3 mt-4 pt-2">
                                <a href="{{ route('profile.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                                    <i class="bi bi-arrow-left me-1"></i> Kembali
                                </a>
                                <button type="submit" class="btn btn-gradient-primary rounded-pill px-5 ms-auto">
                                    <i class="bi bi-save me-2"></i> Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Update Password Card --}}
                <div class="card border-0 shadow-lg rounded-4 mb-4 overflow-hidden">
                    <div class="card-header bg-gradient-warning text-white py-3 border-0">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-white bg-opacity-25 p-2 me-3">
                                <i class="bi bi-lock fs-5"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0">Ubah Password</h5>
                                <p class="small mb-0 opacity-75">Perbarui password akun Anda</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4 p-lg-5">
                        <form method="POST" action="{{ route('password.update') }}">
                            @csrf
                            @method('PUT')

                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-key text-warning me-1"></i>
                                    Password Saat Ini
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="password" name="current_password"
                                    class="form-control form-control-lg @error('current_password', 'updatePassword') is-invalid @enderror"
                                    placeholder="Masukkan password saat ini">
                                @error('current_password', 'updatePassword')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-lock-fill text-warning me-1"></i>
                                    Password Baru
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="password" name="password"
                                    class="form-control form-control-lg @error('password', 'updatePassword') is-invalid @enderror"
                                    placeholder="Masukkan password baru (minimal 8 karakter)">
                                @error('password', 'updatePassword')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text mt-2">
                                    <i class="bi bi-shield-lock text-info me-1"></i>
                                    Gunakan kombinasi huruf, angka, dan simbol untuk password yang kuat.
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-check-circle text-warning me-1"></i>
                                    Konfirmasi Password Baru
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="password" name="password_confirmation" class="form-control form-control-lg"
                                    placeholder="Ulangi password baru">
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-gradient-warning rounded-pill px-5 py-2">
                                    <i class="bi bi-lock me-2"></i> Ubah Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Delete Account Card --}}
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="card-header bg-gradient-danger text-white py-3 border-0">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-white bg-opacity-25 p-2 me-3">
                                <i class="bi bi-trash fs-5"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0">Hapus Akun</h5>
                                <p class="small mb-0 opacity-75">Hapus akun secara permanen</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4 p-lg-5">
                        <div class="alert alert-warning border-0 rounded-3 mb-4">
                            <div class="d-flex">
                                <i class="bi bi-exclamation-triangle-fill fs-5 me-3 text-warning"></i>
                                <div class="small">
                                    <strong>Perhatian!</strong><br>
                                    Setelah akun dihapus, semua data Anda akan dihapus secara permanen dan tidak dapat
                                    dikembalikan.
                                    Pastikan Anda sudah menyimpan data penting sebelum menghapus akun.
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-gradient-danger rounded-pill px-5 py-2"
                                data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                                <i class="bi bi-trash me-2"></i> Hapus Akun Saya
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Modal Hapus Akun --}}
    <div class="modal fade" id="deleteAccountModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
                <div class="modal-header bg-gradient-danger text-white border-0 py-3">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-white bg-opacity-25 p-2 me-2">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                        </div>
                        <h6 class="modal-title fw-bold mb-0">Konfirmasi Hapus Akun</h6>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body p-4">
                        <div class="alert alert-danger border-0 rounded-3 mb-4">
                            <div class="d-flex">
                                <i class="bi bi-exclamation-circle-fill fs-4 me-3 text-danger"></i>
                                <div>
                                    Tindakan ini <strong class="text-danger">tidak dapat dibatalkan</strong>.<br>
                                    Semua data Anda akan dihapus secara permanen termasuk:
                                    <ul class="mb-0 mt-2">
                                        <li>Data profil</li>
                                        <li>Riwayat pendaftaran</li>
                                        <li>Data ekonomi</li>
                                        <li>Dokumen yang diunggah</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-key me-1"></i>
                                Masukkan Password untuk Konfirmasi
                            </label>
                            <input type="password" name="password"
                                class="form-control form-control-lg @error('password', 'userDeletion') is-invalid @enderror"
                                placeholder="Password Anda" required>
                            @error('password', 'userDeletion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer border-0 bg-light py-3">
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-4"
                            data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-danger rounded-pill px-4">
                            <i class="bi bi-trash me-1"></i> Hapus Akun Saya
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        /* Custom Gradients */
        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .bg-gradient-warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .bg-gradient-danger {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        }

        .btn-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            transition: all 0.3s ease;
        }

        .btn-gradient-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .btn-gradient-warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            border: none;
            color: white;
            transition: all 0.3s ease;
        }

        .btn-gradient-warning:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(240, 147, 251, 0.4);
            color: white;
        }

        .btn-gradient-danger {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            border: none;
            color: #333;
            transition: all 0.3s ease;
        }

        .btn-gradient-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(250, 112, 154, 0.4);
            color: #333;
        }

        /* Form Controls */
        .form-control {
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            padding: 12px 16px;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            transform: scale(1.01);
        }

        .form-control-lg {
            font-size: 1rem;
            padding: 14px 18px;
        }

        /* Label */
        .form-label {
            font-size: 0.85rem;
            margin-bottom: 8px;
            color: #334155;
        }

        /* Alert Styling */
        .alert-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border: none;
        }

        .alert-success .btn-close {
            filter: brightness(0) invert(1);
        }

        /* Form Text */
        .form-text {
            font-size: 0.75rem;
            margin-top: 6px;
        }

        /* Card Effects */
        .card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 1rem 2rem rgba(0, 0, 0, 0.1) !important;
        }

        /* Modal */
        .modal-content {
            border: none;
        }

        /* Button */
        .btn {
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .btn:active {
            transform: translateY(0);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .card-body {
                padding: 1.5rem !important;
            }

            .btn-gradient-primary,
            .btn-gradient-warning,
            .btn-gradient-danger {
                width: 100%;
            }

            .form-control-lg {
                font-size: 0.95rem;
                padding: 12px 16px;
            }

            .modal-dialog {
                margin: 1rem;
            }

            .d-flex.gap-3 {
                flex-direction: column;
            }

            .d-flex.gap-3 .btn {
                width: 100%;
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

        .card {
            animation: fadeInUp 0.3s ease forwards;
        }

        .card:nth-child(1) {
            animation-delay: 0.05s;
        }

        .card:nth-child(2) {
            animation-delay: 0.1s;
        }

        .card:nth-child(3) {
            animation-delay: 0.15s;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-dismiss alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });
        });
    </script>
@endsection
