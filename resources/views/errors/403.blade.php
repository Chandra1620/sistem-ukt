<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>403 - Akses Ditolak | Sistem UKT</title>
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
            padding: 20px;
            position: relative;
            overflow-x: hidden;
        }

        /* Animated Background */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="10" cy="10" r="2" fill="rgba(255,255,255,0.05)"/><circle cx="30" cy="20" r="1.5" fill="rgba(255,255,255,0.05)"/><circle cx="50" cy="15" r="2" fill="rgba(255,255,255,0.05)"/><circle cx="70" cy="25" r="1.5" fill="rgba(255,255,255,0.05)"/><circle cx="85" cy="10" r="2" fill="rgba(255,255,255,0.05)"/><circle cx="20" cy="40" r="1" fill="rgba(255,255,255,0.05)"/><circle cx="45" cy="35" r="1.5" fill="rgba(255,255,255,0.05)"/><circle cx="65" cy="45" r="1" fill="rgba(255,255,255,0.05)"/><circle cx="90" cy="50" r="2" fill="rgba(255,255,255,0.05)"/><circle cx="15" cy="70" r="1.5" fill="rgba(255,255,255,0.05)"/><circle cx="40" cy="80" r="1" fill="rgba(255,255,255,0.05)"/><circle cx="60" cy="75" r="2" fill="rgba(255,255,255,0.05)"/><circle cx="80" cy="85" r="1.5" fill="rgba(255,255,255,0.05)"/></svg>');
            opacity: 0.3;
            pointer-events: none;
        }

        .error-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            border-radius: 32px;
            padding: 48px 40px;
            text-align: center;
            max-width: 500px;
            width: 100%;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            position: relative;
            z-index: 1;
            animation: fadeInUp 0.5s ease-out;
            border: 1px solid rgba(255, 255, 255, 0.2);
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

        /* Animated Icon Container */
        .error-icon {
            width: 110px;
            height: 110px;
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            position: relative;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.4);
            }

            50% {
                box-shadow: 0 0 0 20px rgba(220, 38, 38, 0);
            }
        }

        .error-icon i {
            font-size: 3.5rem;
            color: #dc2626;
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {

            0%,
            100% {
                transform: rotate(0deg);
            }

            25% {
                transform: rotate(-10deg);
            }

            75% {
                transform: rotate(10deg);
            }
        }

        .error-code {
            font-size: 5.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, #dc2626, #ef4444);
            background-clip: text;
            -webkit-background-clip: text;
            color: transparent;
            line-height: 1;
            margin-bottom: 8px;
            letter-spacing: -2px;
        }

        .error-title {
            font-size: 1.6rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 12px;
        }

        .error-desc {
            color: #64748b;
            margin-bottom: 32px;
            font-size: 0.9rem;
            line-height: 1.5;
        }

        /* Button Styles */
        .btn-custom {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 24px;
            border-radius: 40px;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn-back {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
        }

        .btn-back:hover {
            background: #e2e8f0;
            transform: translateX(-4px);
            color: #1e293b;
        }

        .btn-dashboard {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .btn-dashboard:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(59, 130, 246, 0.4);
        }

        /* Additional Info */
        .additional-info {
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid #e2e8f0;
        }

        .info-tip {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .tip-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.75rem;
            color: #94a3b8;
        }

        .tip-item i {
            font-size: 0.9rem;
            color: #3b82f6;
        }

        /* Responsive */
        @media (max-width: 576px) {
            .error-card {
                padding: 32px 24px;
            }

            .error-icon {
                width: 80px;
                height: 80px;
            }

            .error-icon i {
                font-size: 2.5rem;
            }

            .error-code {
                font-size: 4rem;
            }

            .error-title {
                font-size: 1.3rem;
            }

            .error-desc {
                font-size: 0.8rem;
            }

            .btn-custom {
                padding: 8px 18px;
                font-size: 0.75rem;
            }

            .tip-item {
                font-size: 0.65rem;
            }
        }

        @media (max-width: 380px) {
            .error-card {
                padding: 24px 16px;
            }

            .d-flex.gap-2 {
                flex-direction: column;
                gap: 10px !important;
            }

            .btn-custom {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="error-card">
        <div class="error-icon">
            <i class="bi bi-shield-x"></i>
        </div>
        <div class="error-code">403</div>
        <div class="error-title">Akses Ditolak</div>
        <p class="error-desc">
            Anda tidak memiliki izin untuk mengakses halaman ini.
            Silakan hubungi administrator jika Anda merasa ini adalah kesalahan.
        </p>
        <div class="d-flex gap-3 justify-content-center">
            <a href="javascript:history.back()" class="btn-custom btn-back">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
            <a href="{{ route('dashboard') }}" class="btn-custom btn-dashboard">
                <i class="bi bi-house-door"></i> Dashboard
            </a>
        </div>

        <div class="additional-info">
            <div class="info-tip">
                <div class="tip-item">
                    <i class="bi bi-envelope"></i>
                    <span>admin@ukt.ac.id</span>
                </div>
                <div class="tip-item">
                    <i class="bi bi-telephone"></i>
                    <span>(021) 1234-5678</span>
                </div>
                <div class="tip-item">
                    <i class="bi bi-question-circle"></i>
                    <span>Hubungi IT Support</span>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
