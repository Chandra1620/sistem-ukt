<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>Register - Sistem UKT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap"
        rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, rgba(15, 43, 61, 0.42) 0%, rgba(10, 28, 42, 0.52) 100%), url('{{ asset('images/login-bg.png') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
            padding: 20px;
        }

        /* Animated Background */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="10" cy="10" r="2" fill="rgba(255,255,255,0.05)"/><circle cx="30" cy="20" r="1.5" fill="rgba(255,255,255,0.05)"/><circle cx="50" cy="15" r="2" fill="rgba(255,255,255,0.05)"/><circle cx="70" cy="25" r="1.5" fill="rgba(255,255,255,0.05)"/><circle cx="85" cy="10" r="2" fill="rgba(255,255,255,0.05)"/><circle cx="20" cy="40" r="1" fill="rgba(255,255,255,0.05)"/><circle cx="45" cy="35" r="1.5" fill="rgba(255,255,255,0.05)"/><circle cx="65" cy="45" r="1" fill="rgba(255,255,255,0.05)"/><circle cx="90" cy="50" r="2" fill="rgba(255,255,255,0.05)"/><circle cx="15" cy="70" r="1.5" fill="rgba(255,255,255,0.05)"/><circle cx="40" cy="80" r="1" fill="rgba(255,255,255,0.05)"/><circle cx="60" cy="75" r="2" fill="rgba(255,255,255,0.05)"/><circle cx="80" cy="85" r="1.5" fill="rgba(255,255,255,0.05)"/></svg>');
            opacity: 0.3;
            pointer-events: none;
        }

        /* Floating Shapes */
        .floating-shape {
            position: absolute;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 50%;
            pointer-events: none;
            z-index: 0;
        }

        .shape-1 {
            width: 300px;
            height: 300px;
            top: -100px;
            right: -100px;
            animation: float 8s ease-in-out infinite;
        }

        .shape-2 {
            width: 200px;
            height: 200px;
            bottom: -50px;
            left: -50px;
            animation: float 6s ease-in-out infinite reverse;
        }

        .shape-3 {
            width: 150px;
            height: 150px;
            top: 50%;
            left: 20%;
            animation: float 10s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0) rotate(0deg);
            }

            50% {
                transform: translateY(-20px) rotate(10deg);
            }
        }

        .register-wrapper {
            width: 100%;
            max-width: 500px;
            padding: 20px;
            position: relative;
            z-index: 1;
        }

        .register-card {
            background: rgba(255, 255, 255, 0.62);
            backdrop-filter: blur(20px) saturate(150%);
            -webkit-backdrop-filter: blur(20px) saturate(150%);
            border-radius: 32px;
            padding: 40px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
            border: 1px solid rgba(255, 255, 255, 0.55);
            transition: all 0.3s ease;
            animation: fadeInUp 0.5s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .register-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 30px 60px -12px rgba(0, 0, 0, 0.3);
        }

        /* Brand Section */
        .brand-logo {
            width: 110px;
            height: 110px;
            background: #ffffff;
            padding: 8px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 2rem;
            color: white;
            box-shadow: 0 10px 25px -5px rgba(30, 58, 95, 0.4);
            transition: all 0.3s ease;
        }

        .brand-logo:hover {
            transform: scale(1.05);
        }

        .brand-title {
            font-size: 1.6rem;
            font-weight: 800;
            background: linear-gradient(135deg, #1e293b, #2d3a4b);
            background-clip: text;
            -webkit-background-clip: text;
            color: transparent;
            text-align: center;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .brand-subtitle {
            color: #334155;
            text-align: center;
            font-size: 0.8rem;
            margin-bottom: 28px;
            line-height: 1.5;
        }

        /* Form Styling */
        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #334155;
            margin-bottom: 8px;
        }

        .input-group-custom {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 1rem;
            z-index: 2;
        }

        .form-control-custom {
            width: 100%;
            padding: 12px 16px 12px 46px;
            font-size: 0.9rem;
            border: 2px solid #e2e8f0;
            border-radius: 16px;
            transition: all 0.2s ease;
            background: rgba(255, 255, 255, 0.8);
        }

        .form-control-custom:focus {
            outline: none;
            border-color: #1e3a5f;
            box-shadow: 0 0 0 3px rgba(30, 58, 95, 0.1);
        }

        .form-control-custom.is-invalid {
            border-color: #dc2626;
        }

        /* Password Toggle */
        .password-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 4px;
            border-radius: 8px;
            transition: all 0.2s;
            z-index: 2;
        }

        .password-toggle:hover {
            color: #1e3a5f;
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

        .strength-bar-fill {
            height: 100%;
            width: 0%;
            transition: all 0.3s ease;
            border-radius: 4px;
        }

        .strength-bar-fill.weak {
            width: 25%;
            background: #dc2626;
        }

        .strength-bar-fill.medium {
            width: 50%;
            background: #f59e0b;
        }

        .strength-bar-fill.strong {
            width: 75%;
            background: #3b82f6;
        }

        .strength-bar-fill.very-strong {
            width: 100%;
            background: #10b981;
        }

        .strength-text {
            font-size: 0.65rem;
            color: #334155;
        }

        .strength-text span {
            font-weight: 600;
        }

        /* Password Match */
        .password-match-hint {
            font-size: 0.65rem;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .password-match-hint.success {
            color: #10b981;
        }

        .password-match-hint.error {
            color: #dc2626;
        }

        /* Button Register */
        .btn-register {
            background: linear-gradient(135deg, #1e3a5f, #2d6a9f);
            border: none;
            border-radius: 16px;
            padding: 14px 24px;
            font-size: 0.9rem;
            font-weight: 600;
            color: white;
            width: 100%;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(30, 58, 95, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 8px;
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(30, 58, 95, 0.4);
        }

        .btn-register:active {
            transform: translateY(0);
        }

        /* Divider */
        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 24px 0;
            color: #94a3b8;
            font-size: 0.75rem;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: linear-gradient(90deg, transparent, #e2e8f0, transparent);
        }

        /* Login Link */
        .login-link {
            color: #1e3a5f;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
        }

        .login-link:hover {
            color: #2d6a9f;
            text-decoration: underline;
        }

        /* Alert */
        .alert-custom {
            background: linear-gradient(135deg, #fee2e2, #fecaca);
            border: none;
            border-radius: 16px;
            padding: 14px 18px;
            font-size: 0.8rem;
            color: #991b1b;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 24px;
        }

        .alert-custom i {
            font-size: 1.2rem;
        }

        /* Footer */
        .footer-text {
            text-align: center;
            margin-top: 24px;
            font-size: 0.7rem;
            color: rgba(255, 255, 255, 0.5);
        }

        /* Error Message */
        .error-message {
            font-size: 0.7rem;
            color: #dc2626;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* Responsive */
        @media (max-width: 576px) {
            .register-wrapper {
                padding: 16px;
            }

            .register-card {
                padding: 28px 24px;
            }

            .brand-logo {
                width: 60px;
                height: 60px;
                font-size: 1.6rem;
            }

            .brand-title {
                font-size: 1.4rem;
            }

            .form-control-custom {
                padding: 10px 14px 10px 42px;
                font-size: 0.85rem;
            }
        }

        @media (max-width: 380px) {
            .register-card {
                padding: 24px 20px;
            }

            .brand-logo {
                width: 50px;
                height: 50px;
                font-size: 1.3rem;
            }

            .brand-title {
                font-size: 1.2rem;
            }

            .btn-register {
                padding: 12px 20px;
                font-size: 0.85rem;
            }
        }
    </style>
</head>

<body>
    <!-- Floating Shapes -->
    <div class="floating-shape shape-1"></div>
    <div class="floating-shape shape-2"></div>
    <div class="floating-shape shape-3"></div>

    <div class="register-wrapper">
        <div class="register-card">
            {{-- Brand --}}
            <div class="brand-logo">
                <img src="{{ asset('images/logo.png') }}" alt="Logo Politeknik Negeri Indramayu" style="width:100%;height:100%;object-fit:contain;">
            </div>
            <div class="brand-title">Buat Akun Baru</div>
            <div class="brand-subtitle">
                Daftar untuk mengakses Sistem Pendukung Keputusan UKT
            </div>

            {{-- Error Alert --}}
            @if ($errors->any())
                <div class="alert-custom">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <div>
                        <strong>Terjadi kesalahan!</strong>
                        <ul class="mb-0 mt-1 ps-3">
                            @foreach ($errors->all() as $error)
                                <li style="font-size: 0.7rem;">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            {{-- Form --}}
            <form method="POST" action="{{ route('register') }}" id="registerForm">
                @csrf

                {{-- Nama Lengkap --}}
                <div class="form-group">
                    <label class="form-label">Nama Lengkap</label>
                    <div class="input-group-custom">
                        <i class="bi bi-person-fill input-icon"></i>
                        <input type="text" name="name"
                            class="form-control-custom @error('name') is-invalid @enderror" value="{{ old('name') }}"
                            placeholder="Masukkan nama lengkap" autofocus>
                    </div>
                    @error('name')
                        <div class="error-message">
                            <i class="bi bi-exclamation-circle"></i>
                            <span>{{ $message }}</span>
                        </div>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <div class="input-group-custom">
                        <i class="bi bi-envelope-fill input-icon"></i>
                        <input type="email" name="email"
                            class="form-control-custom @error('email') is-invalid @enderror" value="{{ old('email') }}"
                            placeholder="nama@email.com" id="emailInput">
                    </div>
                    @error('email')
                        <div class="error-message">
                            <i class="bi bi-exclamation-circle"></i>
                            <span>{{ $message }}</span>
                        </div>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <div class="input-group-custom">
                        <i class="bi bi-lock-fill input-icon"></i>
                        <input type="password" name="password" id="passwordInput"
                            class="form-control-custom @error('password') is-invalid @enderror"
                            placeholder="Minimal 8 karakter">
                        <button type="button" class="password-toggle" onclick="togglePassword('passwordInput')">
                            <i class="bi bi-eye-slash" id="eyeIconPassword"></i>
                        </button>
                    </div>

                    {{-- Password Strength Indicator --}}
                    <div class="password-strength" id="passwordStrength">
                        <div class="strength-bar">
                            <div class="strength-bar-fill" id="strengthFill"></div>
                        </div>
                        <div class="strength-text" id="strengthText">
                            Kekuatan password: <span id="strengthLabel">Kosong</span>
                        </div>
                    </div>

                    @error('password')
                        <div class="error-message">
                            <i class="bi bi-exclamation-circle"></i>
                            <span>{{ $message }}</span>
                        </div>
                    @enderror
                </div>

                {{-- Konfirmasi Password --}}
                <div class="form-group">
                    <label class="form-label">Konfirmasi Password</label>
                    <div class="input-group-custom">
                        <i class="bi bi-lock-fill input-icon"></i>
                        <input type="password" name="password_confirmation" id="passwordConfirmInput"
                            class="form-control-custom" placeholder="Ulangi password">
                        <button type="button" class="password-toggle"
                            onclick="togglePassword('passwordConfirmInput')">
                            <i class="bi bi-eye-slash" id="eyeIconConfirm"></i>
                        </button>
                    </div>
                    <div class="password-match-hint" id="passwordMatchHint">
                        <i class="bi bi-info-circle"></i>
                        <span>Masukkan password yang sama</span>
                    </div>
                </div>

                <button type="submit" class="btn-register" id="submitBtn">
                    <i class="bi bi-person-plus"></i>
                    <span>Daftar Sekarang</span>
                </button>
            </form>

            <div class="divider">atau</div>

            <div class="text-center">
                <span class="text-muted" style="font-size: 0.8rem;">Sudah punya akun?</span>
                <a href="{{ route('login') }}" class="login-link ms-1">
                    Masuk sekarang
                </a>
            </div>
        </div>

        <div class="footer-text">
            &copy; {{ date('Y') }} Sistem UKT. All rights reserved.
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Password visibility toggle
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = inputId === 'passwordInput' ?
                document.getElementById('eyeIconPassword') :
                document.getElementById('eyeIconConfirm');

            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'bi bi-eye';
            } else {
                input.type = 'password';
                icon.className = 'bi bi-eye-slash';
            }
        }

        // Password strength checker
        const passwordInput = document.getElementById('passwordInput');
        const strengthFill = document.getElementById('strengthFill');
        const strengthLabel = document.getElementById('strengthLabel');

        function checkPasswordStrength(password) {
            let strength = 0;
            if (password.length >= 8) strength++;
            if (password.match(/[a-z]/)) strength++;
            if (password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^a-zA-Z0-9]/)) strength++;

            strengthFill.className = 'strength-bar-fill';

            if (password.length === 0) {
                strengthFill.className = 'strength-bar-fill';
                strengthLabel.textContent = 'Kosong';
                strengthLabel.style.color = '#64748b';
                return 0;
            } else if (strength <= 2) {
                strengthFill.classList.add('weak');
                strengthLabel.textContent = 'Lemah';
                strengthLabel.style.color = '#dc2626';
            } else if (strength === 3) {
                strengthFill.classList.add('medium');
                strengthLabel.textContent = 'Sedang';
                strengthLabel.style.color = '#f59e0b';
            } else if (strength === 4) {
                strengthFill.classList.add('strong');
                strengthLabel.textContent = 'Kuat';
                strengthLabel.style.color = '#3b82f6';
            } else {
                strengthFill.classList.add('very-strong');
                strengthLabel.textContent = 'Sangat Kuat';
                strengthLabel.style.color = '#10b981';
            }
            return strength;
        }

        passwordInput.addEventListener('input', function() {
            checkPasswordStrength(this.value);
            checkPasswordMatch();
        });

        // Password match checker
        const passwordConfirmInput = document.getElementById('passwordConfirmInput');
        const passwordMatchHint = document.getElementById('passwordMatchHint');

        function checkPasswordMatch() {
            const password = passwordInput.value;
            const confirm = passwordConfirmInput.value;

            if (confirm.length > 0) {
                if (password === confirm) {
                    passwordMatchHint.innerHTML = '<i class="bi bi-check-circle-fill"></i><span>Password cocok</span>';
                    passwordMatchHint.className = 'password-match-hint success';
                } else {
                    passwordMatchHint.innerHTML =
                        '<i class="bi bi-exclamation-circle-fill"></i><span>Password tidak cocok</span>';
                    passwordMatchHint.className = 'password-match-hint error';
                }
            } else {
                passwordMatchHint.innerHTML = '<i class="bi bi-info-circle"></i><span>Masukkan password yang sama</span>';
                passwordMatchHint.className = 'password-match-hint';
            }
        }

        passwordConfirmInput.addEventListener('input', checkPasswordMatch);

        // Email validation hint
        const emailInput = document.getElementById('emailInput');

        emailInput.addEventListener('input', function() {
            const email = this.value;
            if (email.includes('@') && email.includes('.')) {
                this.style.borderColor = '#10b981';
            } else if (email.length > 0) {
                this.style.borderColor = '#f59e0b';
            } else {
                this.style.borderColor = '#e2e8f0';
            }
        });

        // Form validation before submit
        const form = document.getElementById('registerForm');
        const submitBtn = document.getElementById('submitBtn');

        form.addEventListener('submit', function(e) {
            const password = passwordInput.value;
            const confirm = passwordConfirmInput.value;
            let hasError = false;

            if (password !== confirm) {
                e.preventDefault();
                alert('⚠️ Konfirmasi password tidak cocok!');
                hasError = true;
            }

            if (password.length > 0 && password.length < 8) {
                e.preventDefault();
                alert('⚠️ Password minimal 8 karakter!');
                hasError = true;
            }

            if (!hasError) {
                submitBtn.classList.add('loading');
                submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> <span>Memproses...</span>';
            }
        });

        // Add animation to form inputs on focus
        document.querySelectorAll('.form-control-custom').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.01)';
            });
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });
    </script>
</body>

</html>
