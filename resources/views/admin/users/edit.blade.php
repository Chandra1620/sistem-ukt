@extends('layouts.app')
@section('title', 'Edit User')
@section('page-title', 'Edit User')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card premium-card">
                <div class="card-header premium-card-header">
                    <div class="d-flex align-items-center gap-2">
                        <div class="header-icon warning-icon">
                            <i class="bi bi-person-gear"></i>
                        </div>
                        <h6 class="fw-semibold mb-0">Edit User: {{ $user->name }}</h6>
                    </div>
                    <div class="user-status-badge">
                        <i class="bi bi-info-circle"></i>
                        ID: #{{ $user->id }}
                    </div>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.users.update', $user) }}" method="POST" id="userForm">
                        @csrf @method('PUT')

                        <!-- Nama Lengkap -->
                        <div class="form-group-modern mb-4">
                            <label class="form-label-modern">
                                <i class="bi bi-person"></i>
                                Nama Lengkap
                                <span class="text-danger">*</span>
                            </label>
                            <div class="input-group-modern">
                                <input type="text" name="name"
                                    class="form-control-modern @error('name') is-invalid @enderror"
                                    value="{{ old('name', $user->name) }}" placeholder="Masukkan nama lengkap"
                                    id="nameInput">
                                <div class="input-focus-border"></div>
                            </div>
                            <div class="form-hint">
                                <i class="bi bi-info-circle"></i>
                                Nama lengkap akan ditampilkan di sistem
                            </div>
                            @error('name')
                                <div class="invalid-feedback-modern">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="form-group-modern mb-4">
                            <label class="form-label-modern">
                                <i class="bi bi-envelope"></i>
                                Email Address
                                <span class="text-danger">*</span>
                            </label>
                            <div class="input-group-modern">
                                <input type="email" name="email"
                                    class="form-control-modern @error('email') is-invalid @enderror"
                                    value="{{ old('email', $user->email) }}" placeholder="Masukkan email aktif"
                                    id="emailInput">
                                <div class="input-focus-border"></div>
                            </div>
                            <div class="form-hint" id="emailHint">
                                <i class="bi bi-info-circle"></i>
                                Email akan digunakan untuk login
                            </div>
                            @error('email')
                                <div class="invalid-feedback-modern">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password Baru (Opsional) -->
                        <div class="form-group-modern mb-4">
                            <label class="form-label-modern">
                                <i class="bi bi-key"></i>
                                Password Baru
                                <span class="text-muted fw-normal">(Opsional)</span>
                            </label>
                            <div class="input-group-modern password-group">
                                <input type="password" name="password"
                                    class="form-control-modern @error('password') is-invalid @enderror"
                                    placeholder="Kosongkan jika tidak ingin mengubah password" id="passwordInput">
                                <button type="button" class="password-toggle" onclick="togglePassword('passwordInput')">
                                    <i class="bi bi-eye-slash"></i>
                                </button>
                                <div class="input-focus-border"></div>
                            </div>
                            <div class="password-strength" id="passwordStrength" style="display: none;">
                                <div class="strength-bar"></div>
                                <div class="strength-text">Kekuatan password: <span>Kosong</span></div>
                            </div>
                            <div class="form-hint">
                                <i class="bi bi-shield-check"></i>
                                Kosongkan jika tidak ingin mengubah password. Minimal 8 karakter jika diisi.
                            </div>
                            @error('password')
                                <div class="invalid-feedback-modern">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Konfirmasi Password Baru -->
                        <div class="form-group-modern mb-4">
                            <label class="form-label-modern">
                                <i class="bi bi-key-fill"></i>
                                Konfirmasi Password Baru
                            </label>
                            <div class="input-group-modern">
                                <input type="password" name="password_confirmation" class="form-control-modern"
                                    placeholder="Ulangi password baru" id="passwordConfirmInput">
                                <div class="input-focus-border"></div>
                            </div>
                            <div class="form-hint" id="passwordMatchHint">
                                <i class="bi bi-info-circle"></i>
                                Masukkan password yang sama jika mengubah password
                            </div>
                        </div>

                        <!-- Role -->
                        <div class="form-group-modern mb-4">
                            <label class="form-label-modern">
                                <i class="bi bi-shield-check"></i>
                                Role / Hak Akses
                                <span class="text-danger">*</span>
                            </label>

                            @php
                                $isSelf = $user->id === auth()->id();
                                $isLastAdmin = $user->hasRole('admin') && ($adminCount ?? \App\Models\User::role('admin')->count()) <= 1;
                                $roleLocked = $isSelf || $isLastAdmin;
                            @endphp

                            @if ($isSelf)
                                <div class="role-lock-notice mb-2">
                                    <i class="bi bi-shield-lock-fill text-warning me-2"></i>
                                    <span class="small text-muted">
                                        Anda sedang mengedit akun sendiri. Opsi role selain <strong>Admin</strong> dinonaktifkan demi menjaga hak akses.
                                    </span>
                                </div>
                            @elseif ($isLastAdmin)
                                <div class="role-lock-notice mb-2">
                                    <i class="bi bi-shield-lock-fill text-danger me-2"></i>
                                    <span class="small text-muted">
                                        User ini merupakan <strong>Admin terakhir</strong> di sistem. Opsi role selain Admin dinonaktifkan.
                                    </span>
                                </div>
                            @endif

                            <div class="role-options">
                                @foreach ($roles as $role)
                                    @php
                                        $isDisabled = $roleLocked && $role->name !== 'admin';
                                    @endphp
                                    <label class="role-option {{ $isDisabled ? 'is-disabled' : '' }}"
                                        @if($isDisabled) title="{{ $isSelf ? 'Role akun sendiri tidak dapat diubah ke non-admin' : 'Admin terakhir tidak dapat diubah ke non-admin' }}" @endif>
                                        <input type="radio" name="role" value="{{ $role->name }}"
                                            {{ old('role', $user->getRoleNames()->first()) === $role->name ? 'checked' : '' }}
                                            {{ $isDisabled ? 'disabled' : '' }}>
                                        <div class="role-card role-{{ $role->name }}">
                                            <i class="bi {{ $role->icon ?? 'bi-shield-check' }}"></i>
                                            <div class="role-title">
                                                {{ ucfirst($role->name) }}
                                                @if($isDisabled)
                                                    <i class="bi bi-lock-fill ms-1 text-muted" style="font-size: 0.75rem;"></i>
                                                @endif
                                            </div>
                                            <div class="role-desc">
                                                @switch($role->name)
                                                    @case('admin')
                                                        Akses penuh ke semua fitur sistem
                                                    @break

                                                    @case('reviewer')
                                                        Verifikasi dokumen pendaftaran
                                                    @break

                                                    @case('panitia')
                                                        Perhitungan SAW dan finalisasi UKT
                                                    @break

                                                    @case('management')
                                                        Melihat laporan dan analitik
                                                    @break

                                                    @default
                                                        Akses mahasiswa
                                                @endswitch
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            @error('role')
                                <div class="invalid-feedback-modern">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Info Warning -->
                        <div class="info-warning mb-4">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <div>
                                <strong>Perhatian!</strong> Perubahan role akan mempengaruhi hak akses user.
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="action-buttons">
                            <button type="submit" class="btn-submit" id="submitBtn">
                                <i class="bi bi-save"></i>
                                <span>Update User</span>
                            </button>
                            <a href="{{ route('admin.users.index') }}" class="btn-cancel">
                                <i class="bi bi-arrow-left"></i>
                                <span>Kembali</span>
                            </a>
                        </div>
                    </form>
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

        .warning-icon {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            color: #f59e0b;
        }

        .user-status-badge {
            background: #f1f5f9;
            padding: 6px 14px;
            border-radius: 40px;
            font-size: 0.7rem;
            font-weight: 600;
            color: #475569;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        /* Form Groups */
        .form-group-modern {
            position: relative;
        }

        .form-label-modern {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.8rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 8px;
        }

        .form-label-modern i {
            color: #f59e0b;
            font-size: 0.9rem;
        }

        .input-group-modern {
            position: relative;
            display: flex;
            align-items: center;
        }

        .form-control-modern {
            width: 100%;
            padding: 12px 16px;
            font-size: 0.9rem;
            border: 2px solid #e2e8f0;
            border-radius: 16px;
            transition: all 0.2s ease;
            background: white;
        }

        .form-control-modern:focus {
            outline: none;
            border-color: #f59e0b;
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
        }

        .form-control-modern.is-invalid {
            border-color: #ef4444;
        }

        .input-focus-border {
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: #f59e0b;
            transition: all 0.3s ease;
        }

        .form-control-modern:focus~.input-focus-border {
            width: 100%;
            left: 0;
        }

        .form-hint {
            font-size: 0.7rem;
            color: #94a3b8;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .invalid-feedback-modern {
            font-size: 0.7rem;
            color: #ef4444;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .invalid-feedback-modern::before {
            content: '⚠️';
            font-size: 0.65rem;
        }

        /* Password Toggle */
        .password-group {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 4px;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .password-toggle:hover {
            color: #f59e0b;
        }

        /* Password Strength */
        .password-strength {
            margin-top: 8px;
        }

        .strength-bar {
            height: 4px;
            background: #e2e8f0;
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 6px;
        }

        .strength-bar::before {
            content: '';
            display: block;
            height: 100%;
            width: 0%;
            background: #ef4444;
            transition: all 0.3s;
            border-radius: 4px;
        }

        .strength-bar.weak::before {
            width: 25%;
            background: #ef4444;
        }

        .strength-bar.medium::before {
            width: 50%;
            background: #f59e0b;
        }

        .strength-bar.strong::before {
            width: 75%;
            background: #3b82f6;
        }

        .strength-bar.very-strong::before {
            width: 100%;
            background: #10b981;
        }

        .strength-text {
            font-size: 0.65rem;
            color: #64748b;
        }

        .strength-text span {
            font-weight: 600;
        }

        /* Role Options */
        .role-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 12px;
        }

        .role-option {
            cursor: pointer;
        }

        .role-option.is-disabled {
            cursor: not-allowed;
            opacity: 0.5;
        }

        .role-option.is-disabled .role-card {
            background: #f8fafc;
            border-color: #e2e8f0;
            cursor: not-allowed;
        }

        .role-option.is-disabled:hover .role-card {
            transform: none;
            box-shadow: none;
        }

        .role-lock-notice {
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 12px;
            padding: 10px 14px;
            display: flex;
            align-items: center;
        }

        .role-option input {
            display: none;
        }

        .role-card {
            padding: 14px 12px;
            border-radius: 16px;
            text-align: center;
            transition: all 0.2s ease;
            border: 2px solid #e2e8f0;
            cursor: pointer;
            background: white;
        }

        .role-option input:checked+.role-card {
            border-color: currentColor;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transform: scale(1.02);
        }

        .role-card i {
            font-size: 1.5rem;
            margin-bottom: 8px;
        }

        .role-admin {
            color: #dc2626;
        }

        .role-reviewer {
            color: #f59e0b;
        }

        .role-panitia {
            color: #06b6d4;
        }

        .role-management {
            color: #475569;
        }

        .role-mahasiswa {
            color: #10b981;
        }

        .role-title {
            font-weight: 700;
            font-size: 0.85rem;
            margin-bottom: 4px;
        }

        .role-desc {
            font-size: 0.6rem;
            color: #64748b;
        }

        /* Info Warning */
        .info-warning {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 12px 16px;
            border-radius: 12px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 0.75rem;
            color: #92400e;
        }

        .info-warning i {
            font-size: 1rem;
            margin-top: 1px;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 12px;
            margin-top: 32px;
            padding-top: 16px;
            border-top: 1px solid #e2e8f0;
        }

        .btn-submit {
            flex: 1;
            background: linear-gradient(135deg, #f59e0b, #d97706);
            border: none;
            padding: 12px 24px;
            border-radius: 40px;
            color: white;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(245, 158, 11, 0.4);
        }

        .btn-cancel {
            flex: 1;
            background: #f1f5f9;
            border: none;
            padding: 12px 24px;
            border-radius: 40px;
            color: #475569;
            font-weight: 600;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .btn-cancel:hover {
            background: #e2e8f0;
            transform: translateY(-2px);
            color: #1e293b;
        }

        /* Loading State */
        .btn-submit.loading {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .btn-submit.loading i {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
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

            .action-buttons {
                flex-direction: column;
            }

            .role-options {
                grid-template-columns: 1fr;
                gap: 10px;
            }

            .role-card {
                display: flex;
                align-items: center;
                gap: 12px;
                text-align: left;
                padding: 12px;
            }

            .role-card i {
                margin-bottom: 0;
                font-size: 1.2rem;
            }

            .info-warning {
                flex-direction: column;
                text-align: center;
            }
        }

        @media (max-width: 480px) {
            .form-control-modern {
                padding: 10px 14px;
                font-size: 0.85rem;
            }

            .role-card {
                padding: 10px;
            }

            .role-title {
                font-size: 0.8rem;
            }

            .role-desc {
                font-size: 0.55rem;
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
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Password visibility toggle
            window.togglePassword = function(inputId) {
                const input = document.getElementById(inputId);
                const toggle = input.parentElement.querySelector('.password-toggle i');

                if (input.type === 'password') {
                    input.type = 'text';
                    toggle.classList.remove('bi-eye-slash');
                    toggle.classList.add('bi-eye');
                } else {
                    input.type = 'password';
                    toggle.classList.remove('bi-eye');
                    toggle.classList.add('bi-eye-slash');
                }
            };

            // Password strength checker (only when password field has value)
            const passwordInput = document.getElementById('passwordInput');
            const passwordStrengthDiv = document.getElementById('passwordStrength');
            const strengthBar = document.querySelector('.strength-bar');
            const strengthText = document.querySelector('.strength-text span');
            const passwordConfirm = document.getElementById('passwordConfirmInput');
            const passwordMatchHint = document.getElementById('passwordMatchHint');

            function checkPasswordStrength(password) {
                let strength = 0;
                if (password.length >= 8) strength++;
                if (password.match(/[a-z]/)) strength++;
                if (password.match(/[A-Z]/)) strength++;
                if (password.match(/[0-9]/)) strength++;
                if (password.match(/[^a-zA-Z0-9]/)) strength++;

                strengthBar.className = 'strength-bar';

                if (password.length === 0) {
                    strengthBar.className = 'strength-bar';
                    strengthText.textContent = 'Kosong';
                    strengthText.style.color = '#64748b';
                    return false;
                } else if (strength <= 2) {
                    strengthBar.classList.add('weak');
                    strengthText.textContent = 'Lemah';
                    strengthText.style.color = '#ef4444';
                } else if (strength === 3) {
                    strengthBar.classList.add('medium');
                    strengthText.textContent = 'Sedang';
                    strengthText.style.color = '#f59e0b';
                } else if (strength === 4) {
                    strengthBar.classList.add('strong');
                    strengthText.textContent = 'Kuat';
                    strengthText.style.color = '#3b82f6';
                } else {
                    strengthBar.classList.add('very-strong');
                    strengthText.textContent = 'Sangat Kuat';
                    strengthText.style.color = '#10b981';
                }
                return true;
            }

            passwordInput.addEventListener('input', function() {
                const hasValue = this.value.length > 0;
                passwordStrengthDiv.style.display = hasValue ? 'block' : 'none';
                checkPasswordStrength(this.value);
                checkPasswordMatch();
            });

            function checkPasswordMatch() {
                const password = passwordInput.value;
                const confirm = passwordConfirm.value;

                if (confirm.length > 0) {
                    if (password === confirm) {
                        passwordMatchHint.innerHTML =
                            '<i class="bi bi-check-circle-fill text-success"></i> Password cocok';
                        passwordMatchHint.style.color = '#10b981';
                    } else {
                        passwordMatchHint.innerHTML =
                            '<i class="bi bi-exclamation-triangle-fill text-danger"></i> Password tidak cocok';
                        passwordMatchHint.style.color = '#dc2626';
                    }
                } else if (password.length > 0) {
                    passwordMatchHint.innerHTML = '<i class="bi bi-info-circle"></i> Masukkan konfirmasi password';
                    passwordMatchHint.style.color = '#94a3b8';
                } else {
                    passwordMatchHint.innerHTML =
                        '<i class="bi bi-info-circle"></i> Masukkan password yang sama jika mengubah password';
                    passwordMatchHint.style.color = '#94a3b8';
                }
            }

            passwordConfirm.addEventListener('input', checkPasswordMatch);

            // Email validation hint
            const emailInput = document.getElementById('emailInput');
            const emailHint = document.getElementById('emailHint');

            emailInput.addEventListener('input', function() {
                const email = this.value;
                if (email.includes('@') && email.includes('.')) {
                    emailHint.innerHTML =
                        '<i class="bi bi-check-circle-fill text-success"></i> Format email valid';
                    emailHint.style.color = '#10b981';
                } else if (email.length > 0) {
                    emailHint.innerHTML =
                        '<i class="bi bi-exclamation-triangle-fill text-warning"></i> Format email tidak valid';
                    emailHint.style.color = '#f59e0b';
                } else {
                    emailHint.innerHTML =
                        '<i class="bi bi-info-circle"></i> Email akan digunakan untuk login';
                    emailHint.style.color = '#94a3b8';
                }
            });

            // Form validation
            const form = document.getElementById('userForm');
            const submitBtn = document.getElementById('submitBtn');

            form.addEventListener('submit', function(e) {
                const name = document.getElementById('nameInput').value;
                const email = emailInput.value;
                const password = passwordInput.value;
                const confirm = passwordConfirm.value;
                const role = document.querySelector('input[name="role"]:checked');

                if (!name) {
                    e.preventDefault();
                    alert('⚠️ Nama lengkap harus diisi!');
                    return false;
                }

                if (!email) {
                    e.preventDefault();
                    alert('⚠️ Email harus diisi!');
                    return false;
                }

                if (!email.includes('@') || !email.includes('.')) {
                    e.preventDefault();
                    alert('⚠️ Format email tidak valid!');
                    return false;
                }

                if (password.length > 0 && password.length < 8) {
                    e.preventDefault();
                    alert('⚠️ Password minimal 8 karakter!');
                    return false;
                }

                if (password !== confirm) {
                    e.preventDefault();
                    alert('⚠️ Konfirmasi password tidak cocok!');
                    return false;
                }

                if (!role) {
                    e.preventDefault();
                    alert('⚠️ Role harus dipilih!');
                    return false;
                }

                submitBtn.classList.add('loading');
                submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> <span>Menyimpan...</span>';
                return true;
            });
        });
    </script>
@endsection
