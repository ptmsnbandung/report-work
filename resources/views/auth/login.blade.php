<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Login | MSN Work Report</title>
    <meta name="description" content="Portal Terpadu Sistem Manajemen Gangguan & Tiketing Jaringan MSN Work Report - PT MSN">

    <!-- Favicon & PWA Primary Tags -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#0b63e5">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="MSN Work Report">
    <meta name="application-name" content="MSN Work Report">
    <meta name="msapplication-TileColor" content="#0b63e5">
    <link rel="icon" type="image/png" href="{{ asset('assets/logo-msn BG Trans - Copy2.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/logo-msn BG Trans - Copy2.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/icon-512.png') }}">

    <!-- Google Fonts: Plus Jakarta Sans / Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --primary-blue: #0b63e5;
            --primary-dark: #0052cc;
            --primary-light: #2684ff;
            --bg-page: #e8f1fc;
            --text-dark: #1e293b;
            --text-muted: #64748b;
            --input-bg: #f1f5fb;
            --input-border: #e2e8f0;
        }

        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html, body {
            min-height: 100vh;
            min-height: 100dvh;
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background-color: var(--bg-page);
            color: var(--text-dark);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        body {
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            padding: clamp(0.75rem, 2.5vh, 2rem) clamp(0.75rem, 2vw, 1.5rem);
            overflow-x: hidden;
            background: radial-gradient(circle at 15% 15%, #dbeafe 0%, #eef4fc 40%, #e2edfb 100%);
        }

        /* ── BACKGROUND AMBIENT GLOW ── */
        .ambient-shape {
            position: fixed;
            border-radius: 50%;
            pointer-events: none;
            filter: blur(80px);
            z-index: 0;
            opacity: 0.5;
        }
        .shape-1 {
            width: 450px;
            height: 450px;
            background: rgba(38, 132, 255, 0.25);
            top: -120px;
            left: -100px;
        }
        .shape-2 {
            width: 400px;
            height: 400px;
            background: rgba(11, 99, 229, 0.18);
            bottom: -100px;
            right: -80px;
        }

        /* ── MAIN LOGIN CONTAINER (DESKTOP) ── */
        .login-card-wrapper {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 980px;
            min-height: 560px;
            background: #ffffff;
            border-radius: 32px;
            box-shadow:
                0 25px 60px -15px rgba(11, 99, 229, 0.18),
                0 10px 30px -5px rgba(0, 0, 0, 0.05),
                0 0 0 1px rgba(11, 99, 229, 0.06);
            overflow: hidden;
            display: flex;
            flex-direction: row;
        }

        /* ── LEFT BRAND PANEL (DESKTOP) ── */
        .brand-panel {
            width: 46%;
            flex-shrink: 0;
            background: linear-gradient(165deg, #1b73f8 0%, #0b63e5 45%, #0548b8 100%);
            padding: 3rem 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            text-align: center;
            position: relative;
            overflow: hidden;
            color: #ffffff;
        }

        .brand-welcome-title {
            font-size: 1.55rem;
            font-weight: 700;
            letter-spacing: -0.3px;
            color: #ffffff;
            margin-bottom: 0.25rem;
        }

        /* Centered White Circle Badge with Logo */
        .brand-logo-circle {
            width: 105px;
            height: 105px;
            background: #ffffff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 1.6rem auto 1.1rem;
            box-shadow: 0 12px 28px rgba(0, 40, 120, 0.25);
            padding: 14px;
            position: relative;
            z-index: 2;
            transition: transform 0.3s ease;
        }
        .brand-logo-circle:hover {
            transform: scale(1.05);
        }

        .brand-logo-circle img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .brand-app-name {
            font-size: 1.45rem;
            font-weight: 800;
            letter-spacing: -0.4px;
            color: #ffffff;
            margin-bottom: 0.75rem;
            text-shadow: 0 2px 8px rgba(0, 30, 90, 0.2);
        }

        .brand-desc {
            font-size: 0.85rem;
            line-height: 1.6;
            color: rgba(255, 255, 255, 0.88);
            max-width: 320px;
            margin: 0 auto;
        }

        .brand-footer-links {
            font-size: 0.74rem;
            font-weight: 700;
            letter-spacing: 0.8px;
            color: rgba(255, 255, 255, 0.8);
            text-transform: uppercase;
            margin-top: 1.5rem;
            position: relative;
            z-index: 2;
        }

        /* ── DESKTOP VERTICAL CLOUD/WAVE TRANSITION ── */
        .desktop-cloud-divider {
            position: absolute;
            top: 0;
            right: -1px;
            bottom: 0;
            width: 85px;
            height: 100%;
            pointer-events: none;
            z-index: 5;
        }
        .desktop-cloud-divider svg {
            width: 100%;
            height: 100%;
            display: block;
        }

        /* ── RIGHT FORM PANEL ── */
        .form-panel {
            flex-grow: 1;
            background: #ffffff;
            padding: 3.2rem 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            z-index: 6;
        }

        .form-title {
            font-size: 1.65rem;
            font-weight: 800;
            color: var(--text-dark);
            letter-spacing: -0.5px;
            margin-bottom: 0.35rem;
        }

        .form-subtitle {
            font-size: 0.86rem;
            color: var(--text-muted);
            margin-bottom: 1.45rem;
        }

        /* Form Controls */
        .form-group-custom {
            margin-bottom: 1.15rem;
        }

        .custom-label {
            font-size: 0.82rem;
            font-weight: 700;
            color: #334155;
            margin-bottom: 0.45rem;
            display: block;
        }

        .input-box {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-box .input-icon-left {
            position: absolute;
            left: 16px;
            color: #94a3b8;
            font-size: 1.05rem;
            pointer-events: none;
            transition: color 0.2s ease;
            z-index: 5;
        }

        .custom-input {
            width: 100%;
            height: 50px;
            padding: 0.65rem 1rem 0.65rem 48px;
            border-radius: 14px;
            border: 1.5px solid var(--input-border);
            background: var(--input-bg);
            color: var(--text-dark);
            font-size: 0.92rem;
            font-weight: 500;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .custom-input::placeholder {
            color: #94a3b8;
            font-weight: 400;
        }

        .custom-input:focus {
            background: #ffffff;
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 3.5px rgba(11, 99, 229, 0.14);
            outline: none;
        }

        .input-box:focus-within .input-icon-left {
            color: var(--primary-blue);
        }

        .btn-toggle-pwd {
            position: absolute;
            right: 10px;
            background: transparent;
            border: none;
            color: #94a3b8;
            padding: 6px 10px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.15rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s ease;
            z-index: 5;
        }
        .btn-toggle-pwd:hover {
            color: var(--text-dark);
            background: #e2e8f0;
        }

        /* Checkbox */
        .form-check-input {
            width: 1.15rem;
            height: 1.15rem;
            border-radius: 5px;
            border: 1.5px solid #cbd5e1;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .form-check-input:checked {
            background-color: var(--primary-blue);
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 2px rgba(11, 99, 229, 0.2);
        }

        /* Submit Action Button */
        .btn-submit-login {
            width: 100%;
            height: 50px;
            border-radius: 14px;
            background: linear-gradient(135deg, #0b63e5 0%, #0052cc 100%);
            border: none;
            color: #ffffff;
            font-size: 0.96rem;
            font-weight: 700;
            letter-spacing: 0.2px;
            box-shadow: 0 10px 24px -5px rgba(11, 99, 229, 0.45);
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-submit-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 28px -5px rgba(11, 99, 229, 0.6);
            color: #ffffff;
        }
        .btn-submit-login:active {
            transform: translateY(0);
        }

        /* Divider & Demo Grid */
        .divider-box {
            position: relative;
            text-align: center;
            margin: 1.35rem 0 1rem;
        }
        .divider-box::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            width: 100%;
            height: 1px;
            background: #e2e8f0;
        }
        .divider-box span {
            position: relative;
            background: #ffffff;
            padding: 0 12px;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.6px;
            text-transform: uppercase;
            color: #94a3b8;
        }

        .quick-roles-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.55rem;
        }

        .role-chip-btn {
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            background: #f8fafc;
            padding: 0.55rem 0.65rem;
            cursor: pointer;
            transition: all 0.18s ease;
            text-align: left;
            display: flex;
            align-items: center;
            gap: 0.55rem;
        }
        .role-chip-btn:hover {
            border-color: var(--primary-blue);
            background: #eff6ff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(11, 99, 229, 0.12);
        }
        .role-chip-btn:active {
            transform: scale(0.98);
        }

        .role-chip-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.95rem;
            flex-shrink: 0;
        }

        .role-chip-name {
            font-size: 0.78rem;
            font-weight: 700;
            color: #1e293b;
            line-height: 1.15;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .role-chip-sub {
            font-size: 0.65rem;
            color: #64748b;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Alert styling */
        .toast-banner {
            border-radius: 12px;
            padding: 0.75rem 1rem;
            font-size: 0.84rem;
            font-weight: 500;
            display: flex;
            align-items: flex-start;
            gap: 0.65rem;
            margin-bottom: 1.15rem;
        }
        .toast-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
        }
        .toast-info {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1d4ed8;
        }

        /* ── MOBILE RESPONSIVE LAYOUT (CLEAN NO-SCROLL SINGLE SCREEN FIT) ── */
        .mobile-brand-top {
            display: none;
        }

        @media (max-width: 991px) {
            html, body {
                height: 100%;
                min-height: 100dvh;
                overflow: hidden;
            }

            body {
                padding: 0.45rem 0.65rem;
                background: #eef4fc;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .login-card-wrapper {
                max-width: 385px;
                width: 100%;
                max-height: calc(100dvh - 0.9rem);
                height: auto;
                min-height: auto;
                border-radius: 24px;
                flex-direction: column;
                justify-content: space-between;
                box-shadow: 0 15px 35px rgba(11, 99, 229, 0.16), 0 4px 12px rgba(0, 0, 0, 0.04);
                overflow: hidden;
            }

            .brand-panel {
                display: none !important;
            }

            /* Mobile Top Vibrant Blue Banner */
            .mobile-brand-top {
                display: block;
                flex-shrink: 0;
                background: linear-gradient(165deg, #1b73f8 0%, #0b63e5 55%, #0548b8 100%);
                padding: 0.9rem 1rem 0;
                text-align: center;
                position: relative;
                color: #ffffff;
            }

            .mobile-brand-top .brand-welcome-title {
                font-size: 0.82rem;
                font-weight: 600;
                margin-bottom: 0.1rem;
                opacity: 0.95;
            }

            .mobile-brand-top .brand-logo-circle {
                width: 54px;
                height: 54px;
                margin: 0.2rem auto 0.35rem;
                padding: 6px;
                box-shadow: 0 6px 14px rgba(0, 30, 90, 0.22);
            }

            .mobile-brand-top .brand-app-name {
                font-size: 1.12rem;
                font-weight: 800;
                margin-bottom: 0.1rem;
                letter-spacing: -0.2px;
            }

            .mobile-brand-top .mobile-brand-sub {
                font-size: 0.68rem;
                color: rgba(255, 255, 255, 0.88);
                margin-bottom: 0.45rem;
            }

            /* Mobile Cloud Transition SVG */
            .mobile-cloud-divider {
                width: 100%;
                height: 26px;
                margin-bottom: -1px;
                display: block;
            }
            .mobile-cloud-divider svg {
                width: 100%;
                height: 100%;
                display: block;
            }

            .form-panel {
                flex-grow: 1;
                padding: 0.65rem 1.15rem 0.9rem !important;
                display: flex;
                flex-direction: column;
                justify-content: center;
            }

            .form-title {
                font-size: 1.12rem;
                text-align: center;
                margin-bottom: 0.1rem;
                font-weight: 800;
            }

            .form-subtitle {
                font-size: 0.72rem;
                text-align: center;
                margin-bottom: 0.65rem;
                line-height: 1.25;
            }

            .toast-banner {
                padding: 0.4rem 0.7rem;
                font-size: 0.74rem;
                margin-bottom: 0.55rem;
                border-radius: 8px;
            }

            .form-group-custom {
                margin-bottom: 0.55rem;
            }

            .custom-label {
                font-size: 0.74rem;
                margin-bottom: 0.2rem;
            }

            .custom-input {
                height: 40px;
                padding: 0.35rem 0.75rem 0.35rem 38px;
                font-size: 0.82rem;
                border-radius: 10px;
            }

            .input-box .input-icon-left {
                left: 12px;
                font-size: 0.92rem;
            }

            .btn-toggle-pwd {
                right: 6px;
                font-size: 1rem;
                padding: 4px 6px;
            }

            .remember-container {
                margin-bottom: 0.65rem !important;
            }

            .remember-container span {
                font-size: 0.72rem !important;
            }

            .form-check-input {
                width: 0.95rem;
                height: 0.95rem;
            }

            .btn-submit-login {
                height: 40px;
                font-size: 0.86rem;
                border-radius: 10px;
                box-shadow: 0 6px 16px -4px rgba(11, 99, 229, 0.45);
            }

            .divider-box {
                margin: 0.65rem 0 0.5rem;
            }

            .divider-box span {
                font-size: 0.64rem;
                padding: 0 8px;
            }

            .quick-roles-grid {
                gap: 0.35rem !important;
            }

            .role-chip-btn {
                flex-direction: column !important;
                align-items: center !important;
                justify-content: center !important;
                text-align: center !important;
                padding: 0.35rem 0.15rem !important;
                border-radius: 8px !important;
                gap: 0.15rem !important;
            }

            .role-chip-icon {
                width: 22px !important;
                height: 22px !important;
                font-size: 0.72rem !important;
                border-radius: 6px !important;
                margin: 0 auto !important;
            }

            .role-chip-name {
                font-size: 0.68rem !important;
                width: 100% !important;
                text-align: center !important;
            }

            .role-chip-sub {
                font-size: 0.56rem !important;
                width: 100% !important;
                text-align: center !important;
            }
        }

        /* ── PT MSN SIGNATURE LOADER BACKDROP ── */
        .msn-loader-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(11, 99, 229, 0.88);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            z-index: 99999;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            opacity: 1;
            visibility: visible;
            transition: opacity 0.35s ease, visibility 0.35s ease;
        }

        .msn-loader-backdrop.fade-out {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }

        .msn-logo-loader-container {
            position: relative;
            width: 90px;
            height: 90px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .msn-loader-ring {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 3.5px solid rgba(255, 255, 255, 0.2);
            border-top-color: #ffffff;
            animation: msnRingSpin 1s cubic-bezier(0.55, 0.15, 0.45, 0.85) infinite;
        }

        .msn-loader-logo-wrap {
            width: 62px;
            height: 62px;
            border-radius: 50%;
            background: #ffffff;
            padding: 6px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2;
        }

        .msn-loader-logo-img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .msn-loader-text {
            margin-top: 1.2rem;
            font-size: 0.9rem;
            font-weight: 600;
            color: #ffffff;
            letter-spacing: 0.3px;
        }

        @keyframes msnRingSpin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>

<!-- PT MSN Signature Preloader -->
<div id="msnGlobalPreloader" class="msn-loader-backdrop">
    <div class="msn-logo-loader-container">
        <div class="msn-loader-ring"></div>
        <div class="msn-loader-logo-wrap">
            <img src="{{ asset('assets/logo-msn BG Trans - Copy2.png') }}" alt="PT MSN" class="msn-loader-logo-img">
        </div>
    </div>
    <div class="msn-loader-text">
        <span id="msnLoaderMsg">Memuat Aplikasi...</span>
    </div>
</div>

<!-- Ambient Glow Shapes -->
<div class="ambient-shape shape-1"></div>
<div class="ambient-shape shape-2"></div>

<!-- ════ MAIN CARD CONTAINER ════ -->
<div class="login-card-wrapper">

    <!-- ── 1. DESKTOP LEFT BRAND PANEL (Vibrant Blue with Cloud Divider) ── -->
    <div class="brand-panel">
        <div>
            <div class="brand-welcome-title">Selamat Datang di</div>
            
            <!-- Large White Circle Badge with PT MSN Logo -->
            <div class="brand-logo-circle">
                <img src="{{ asset('assets/logo-msn BG Trans - Copy2.png') }}" alt="Logo PT MSN">
            </div>

            <div class="brand-app-name">MSN Work Report</div>
            <p class="brand-desc">
                Sistem manajemen gangguan, tiket operasional &amp; pelaporan kerja jaringan terpadu PT Media Solusi Network.
            </p>
        </div>

        <div class="brand-footer-links">
            PT MEDIA SOLUSI NETWORK
        </div>

        <!-- Layered Cloud / Wave SVG Divider (Desktop) -->
        <div class="desktop-cloud-divider">
            <svg viewBox="0 0 100 700" preserveAspectRatio="none">
                <!-- Layer 1: Sky Blue Shadow Contour -->
                <path d="M0,0 C45,60 15,140 50,210 C85,280 35,360 65,430 C95,500 45,590 75,650 C90,680 95,700 100,700 L100,0 Z" fill="rgba(147, 197, 253, 0.45)"/>
                <!-- Layer 2: Light Blue Contour -->
                <path d="M20,0 C65,70 35,150 70,220 C105,290 55,370 85,440 C110,510 65,600 95,660 L100,700 L100,0 Z" fill="rgba(219, 234, 254, 0.75)"/>
                <!-- Layer 3: Solid White Flowing Cloud Curve -->
                <path d="M45,0 C85,80 55,160 90,230 C120,300 75,380 100,450 C120,520 85,610 100,670 L100,700 L100,0 Z" fill="#ffffff"/>
            </svg>
        </div>
    </div>

    <!-- ── 2. MOBILE TOP BANNER (Vibrant Blue with Cloud Divider) ── -->
    <div class="mobile-brand-top">
        <div class="brand-welcome-title">Selamat Datang di</div>
        <div class="brand-logo-circle">
            <img src="{{ asset('assets/logo-msn BG Trans - Copy2.png') }}" alt="Logo PT MSN">
        </div>
        <div class="brand-app-name">MSN Work Report</div>
        <div class="mobile-brand-sub">Sistem Tiketing &amp; Manajemen Jaringan</div>

        <!-- Layered Cloud / Wave SVG Divider (Mobile) -->
        <div class="mobile-cloud-divider">
            <svg viewBox="0 0 1440 120" preserveAspectRatio="none">
                <path d="M0,0 C320,65 520,10 820,55 C1120,100 1280,30 1440,65 L1440,120 L0,120 Z" fill="rgba(147, 197, 253, 0.45)"/>
                <path d="M0,25 C360,85 580,30 880,75 C1160,115 1320,50 1440,85 L1440,120 L0,120 Z" fill="rgba(219, 234, 254, 0.75)"/>
                <path d="M0,50 C400,105 640,50 940,95 C1200,130 1360,70 1440,105 L1440,120 L0,120 Z" fill="#ffffff"/>
            </svg>
        </div>
    </div>

    <!-- ── 3. FORM PANEL (Clean Pure White) ── -->
    <div class="form-panel">
        <div class="form-title">Masuk ke Akun</div>
        <div class="form-subtitle">Silakan masukkan kredensial akun Anda untuk login.</div>

        <!-- Flash Alert Messages -->
        @if($errors->any())
            <div class="toast-banner toast-error">
                <i class="bi bi-exclamation-triangle-fill fs-6 flex-shrink-0"></i>
                <div>{{ $errors->first() }}</div>
            </div>
        @endif

        @if(session('info'))
            <div class="toast-banner toast-info">
                <i class="bi bi-info-circle-fill fs-6 flex-shrink-0"></i>
                <div>{{ session('info') }}</div>
            </div>
        @endif

        <!-- Form Login -->
        <form method="POST" action="{{ route('login.post') }}" id="loginForm">
            @csrf
            @if(request()->has('redirect'))
                <input type="hidden" name="redirect_url" value="{{ request('redirect') }}">
            @elseif(session()->has('url.intended'))
                <input type="hidden" name="redirect_url" value="{{ session('url.intended') }}">
            @endif

            <!-- Email Address Input -->
            <div class="form-group-custom">
                <label for="email" class="custom-label">Email Perusahaan</label>
                <div class="input-box">
                    <i class="bi bi-envelope input-icon-left"></i>
                    <input type="email"
                           class="custom-input @error('email') is-invalid @enderror"
                           id="email"
                           name="email"
                           value="{{ old('email') }}"
                           placeholder="nama@connecti.id"
                           required
                           autofocus
                           autocomplete="username">
                </div>
            </div>

            <!-- Password Input -->
            <div class="form-group-custom">
                <label for="password" class="custom-label">Kata Sandi (Password)</label>
                <div class="input-box">
                    <i class="bi bi-lock input-icon-left"></i>
                    <input type="password"
                           class="custom-input @error('password') is-invalid @enderror"
                           id="password"
                           name="password"
                           placeholder="••••••••••••"
                           required
                           autocomplete="current-password">
                    <button class="btn-toggle-pwd" type="button" id="togglePassword" title="Tampilkan/sembunyikan password" aria-label="Toggle password visibility">
                        <i class="bi bi-eye" id="eyeIcon"></i>
                    </button>
                </div>
            </div>

            <!-- Remember Me Session Checkbox -->
            <div class="d-flex justify-content-between align-items-center remember-container mb-3">
                <label class="d-flex align-items-center gap-2" for="remember" style="cursor: pointer; user-select: none;">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" value="1" checked>
                    <span class="small text-secondary fw-semibold" style="font-size: 0.8rem;">
                        Ingat saya di perangkat ini (30 Hari)
                    </span>
                </label>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn-submit-login" id="loginSubmitBtn">
                <span id="loginBtnText" class="d-flex align-items-center gap-2">
                    <i class="bi bi-box-arrow-in-right fs-5"></i>
                    <span>Masuk ke Dashboard</span>
                </span>
                <span id="loginBtnLoading" class="d-none d-flex align-items-center gap-2">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    <span>Memverifikasi Akun...</span>
                </span>
            </button>
        </form>

        <!-- Quick Demo Switcher -->
        <div class="divider-box">
            <span>Akses Cepat Demo Akun</span>
        </div>

        <div class="quick-roles-grid">
            <!-- Admin NOC -->
            <button type="button" class="role-chip-btn" onclick="fillLogin('admin@connecti.id', 'password')">
                <div class="role-chip-icon" style="background:#eff6ff; color:#2563eb;">
                    <i class="bi bi-shield-lock-fill"></i>
                </div>
                <div class="role-chip-info">
                    <div class="role-chip-name">Admin NOC</div>
                    <div class="role-chip-sub">Full System</div>
                </div>
            </button>

            <!-- HelpDesk NOC -->
            <button type="button" class="role-chip-btn" onclick="fillLogin('helpdesk@connecti.id', 'password')">
                <div class="role-chip-icon" style="background:#f0fdf4; color:#16a34a;">
                    <i class="bi bi-headset"></i>
                </div>
                <div class="role-chip-info">
                    <div class="role-chip-name">HelpDesk NOC</div>
                    <div class="role-chip-sub">Tiket &amp; Dispatch</div>
                </div>
            </button>

            <!-- Tim Teknis -->
            <button type="button" class="role-chip-btn" onclick="fillLogin('teknis@connecti.id', 'password')">
                <div class="role-chip-icon" style="background:#fff7ed; color:#ea580c;">
                    <i class="bi bi-tools"></i>
                </div>
                <div class="role-chip-info">
                    <div class="role-chip-name">Tim Teknis</div>
                    <div class="role-chip-sub">Lapangan &amp; JC</div>
                </div>
            </button>
        </div>
    </div>
</div>

<!-- JavaScript Logic -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function fillLogin(email, password) {
    const emailInput = document.getElementById('email');
    const pwdInput   = document.getElementById('password');

    if (!emailInput || !pwdInput) return;

    emailInput.value = email;
    pwdInput.value   = password;

    [emailInput, pwdInput].forEach(el => {
        el.style.borderColor = '#0b63e5';
        el.style.backgroundColor = '#eff6ff';
        setTimeout(() => {
            el.style.borderColor = '';
            el.style.backgroundColor = '';
        }, 500);
    });

    emailInput.focus();
}

// Password toggle eye icon
const toggleBtn = document.getElementById('togglePassword');
const pwdInput  = document.getElementById('password');
const eyeIcon   = document.getElementById('eyeIcon');

if (toggleBtn && pwdInput && eyeIcon) {
    toggleBtn.addEventListener('click', () => {
        const isPassword = pwdInput.type === 'password';
        pwdInput.type = isPassword ? 'text' : 'password';
        eyeIcon.className = isPassword ? 'bi bi-eye-slash' : 'bi bi-eye';
    });
}

// PT MSN Signature Loader
window.showMsnLoader = function(text) {
    const el = document.getElementById('msnGlobalPreloader');
    const txt = document.getElementById('msnLoaderMsg');
    if (txt && text) txt.textContent = text;
    if (el) {
        el.classList.remove('fade-out');
        el.style.display = 'flex';
    }
};

window.hideMsnLoader = function() {
    const el = document.getElementById('msnGlobalPreloader');
    if (el) {
        el.classList.add('fade-out');
        setTimeout(() => {
            if (el.classList.contains('fade-out')) {
                el.style.display = 'none';
            }
        }, 350);
    }
};

window.addEventListener('load', () => setTimeout(window.hideMsnLoader, 150));
document.addEventListener('DOMContentLoaded', () => setTimeout(window.hideMsnLoader, 300));
setTimeout(window.hideMsnLoader, 1200);

// Submit loading state
const loginForm = document.getElementById('loginForm');
if (loginForm) {
    loginForm.addEventListener('submit', () => {
        const btnText = document.getElementById('loginBtnText');
        const btnLoad = document.getElementById('loginBtnLoading');
        const submitBtn = document.getElementById('loginSubmitBtn');

        if (btnText && btnLoad && submitBtn) {
            btnText.classList.add('d-none');
            btnLoad.classList.remove('d-none');
            submitBtn.disabled = true;
        }

        window.showMsnLoader('Memverifikasi Akun & Masuk...');
    });
}

// Service Worker for PWA
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').catch(err => {
            console.debug('SW Registration note:', err);
        });
    });
}
</script>

</body>
</html>
