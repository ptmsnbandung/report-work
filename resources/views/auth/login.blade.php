<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Login | MSN Work Report</title>
    <meta name="description" content="Portal Terpadu Sistem Manajemen Gangguan & Tiketing Jaringan MSN Work Report - PT MSN">

    <!-- Favicon & PWA Primary Tags (Matched with Dashboard) -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#07152b">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="MSN Work Report">
    <meta name="application-name" content="MSN Work Report">
    <meta name="msapplication-TileColor" content="#07152b">
    <link rel="icon" type="image/png" href="{{ asset('assets/logo-msn BG Trans - Copy2.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/logo-msn BG Trans - Copy2.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/icon-512.png') }}">

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --brand-navy: #07152b;
            --brand-navy-mid: #0d2757;
            --brand-strong: #1b39da;
            --brand-base: #2C7FFF;
            --brand-cyan: #38bdf8;
            --text-dark: #0f172a;
            --text-muted: #64748b;
            --input-bg: #f8fafc;
            --input-border: #e2e8f0;
        }

        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html, body {
            width: 100%;
            min-height: 100vh;
            min-height: 100dvh;
            margin: 0;
            padding: 0;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: #ffffff;
            color: var(--text-dark);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            overflow-x: hidden;
        }

        /* ── FULLSCREEN LOGIN LAYOUT ── */
        .login-fullscreen-container {
            display: flex;
            width: 100vw;
            min-height: 100vh;
            min-height: 100dvh;
            position: relative;
        }

        /* ── DESKTOP LEFT BRAND SIDE (FULL HEIGHT - DASHBOARD NAVY & ROYAL BLUE THEME) ── */
        .desktop-brand-side {
            width: 45%;
            min-height: 100vh;
            min-height: 100dvh;
            background: linear-gradient(150deg, #07152b 0%, #0d2757 45%, #1b39da 85%, #2C7FFF 100%);
            padding: 4rem 3.5rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            text-align: center;
            position: relative;
            color: #ffffff;
            overflow: hidden;
            flex-shrink: 0;
        }

        .brand-welcome-title {
            font-size: 1.6rem;
            font-weight: 700;
            letter-spacing: -0.3px;
            color: #ffffff;
            margin-bottom: 0.35rem;
        }

        /* Centered White Circle Badge with Logo */
        .brand-logo-circle {
            width: 110px;
            height: 110px;
            background: #ffffff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 1.8rem auto 1.25rem;
            box-shadow: 0 14px 32px rgba(7, 21, 43, 0.4), 0 0 0 3px rgba(44, 127, 255, 0.25);
            padding: 16px;
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
            font-size: 1.55rem;
            font-weight: 800;
            letter-spacing: -0.4px;
            color: #ffffff;
            margin-bottom: 0.85rem;
            text-shadow: 0 2px 10px rgba(7, 21, 43, 0.5);
        }

        .brand-desc {
            font-size: 0.88rem;
            line-height: 1.65;
            color: rgba(255, 255, 255, 0.9);
            max-width: 330px;
            margin: 0 auto;
        }

        .brand-footer-links {
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.8px;
            color: rgba(255, 255, 255, 0.75);
            text-transform: uppercase;
            margin-top: 1.5rem;
            position: relative;
            z-index: 2;
        }

        /* ── DESKTOP VERTICAL WAVE DIVIDER ── */
        .desktop-wave-wrapper {
            position: absolute;
            top: 0;
            right: -1px;
            bottom: 0;
            width: 95px;
            height: 100%;
            pointer-events: none;
            z-index: 5;
        }
        .desktop-wave-wrapper svg {
            width: 100%;
            height: 100%;
            display: block;
        }

        /* ── DESKTOP RIGHT FORM SIDE ── */
        .desktop-form-side {
            width: 55%;
            min-height: 100vh;
            min-height: 100dvh;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem 4rem;
            position: relative;
            z-index: 6;
        }

        .form-inner-box {
            width: 100%;
            max-width: 440px;
        }

        .form-title {
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--text-dark);
            letter-spacing: -0.5px;
            margin-bottom: 0.35rem;
        }

        .form-subtitle {
            font-size: 0.88rem;
            color: var(--text-muted);
            margin-bottom: 1.5rem;
        }

        /* Form Controls */
        .form-group-custom {
            margin-bottom: 1.2rem;
        }

        .custom-label {
            font-size: 0.84rem;
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
            border-color: var(--brand-base);
            box-shadow: 0 0 0 3.5px rgba(44, 127, 255, 0.16);
            outline: none;
        }

        .input-box:focus-within .input-icon-left {
            color: var(--brand-base);
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
            background-color: var(--brand-base);
            border-color: var(--brand-base);
            box-shadow: 0 0 0 2px rgba(44, 127, 255, 0.2);
        }

        /* Submit Action Button (Dashboard Gradient Match) */
        .btn-submit-login {
            width: 100%;
            height: 50px;
            border-radius: 14px;
            background: linear-gradient(135deg, #2C7FFF 0%, #1b39da 100%);
            border: none;
            color: #ffffff;
            font-size: 0.96rem;
            font-weight: 700;
            letter-spacing: 0.2px;
            box-shadow: 0 10px 24px -5px rgba(44, 127, 255, 0.45);
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
            box-shadow: 0 14px 28px -5px rgba(44, 127, 255, 0.6);
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
            border-color: var(--brand-base);
            background: #eff6ff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(44, 127, 255, 0.15);
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

        /* ── MOBILE FULLSCREEN SINGLE SCREEN LAYOUT (NO SCROLL, FULL WIDTH) ── */
        .mobile-brand-top {
            display: none;
        }

        @media (max-width: 991px) {
            html, body {
                width: 100vw;
                height: 100%;
                min-height: 100dvh;
                overflow: hidden;
                background: #ffffff;
            }

            .login-fullscreen-container {
                flex-direction: column;
                width: 100vw;
                height: 100dvh;
                min-height: 100dvh;
                justify-content: space-between;
                overflow: hidden;
            }

            .desktop-brand-side {
                display: none !important;
            }

            /* Mobile Top Full-Width Dashboard Theme Header */
            .mobile-brand-top {
                display: flex !important;
                flex-direction: column;
                width: 100%;
                background: linear-gradient(155deg, #07152b 0%, #0d2757 50%, #1b39da 100%);
                padding: 1rem 1rem 0;
                text-align: center;
                position: relative;
                color: #ffffff;
                flex-shrink: 0;
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
                box-shadow: 0 6px 16px rgba(7, 21, 43, 0.4), 0 0 0 2px rgba(44, 127, 255, 0.3);
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

            /* Mobile Wave Transition SVG (Full-Width Edge to Edge) */
            .mobile-wave-wrapper {
                width: 100%;
                height: 32px;
                margin-bottom: -1px;
                display: block;
                overflow: hidden;
                line-height: 0;
            }
            .mobile-wave-wrapper svg {
                width: 100%;
                height: 100%;
                display: block;
            }

            /* Mobile Bottom Full-Width White Form */
            .desktop-form-side {
                width: 100% !important;
                min-height: auto !important;
                flex-grow: 1;
                padding: 0.6rem 1.25rem 0.9rem !important;
                background: #ffffff;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .form-inner-box {
                width: 100%;
                max-width: 380px;
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
                box-shadow: 0 6px 16px -4px rgba(44, 127, 255, 0.45);
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

        /* ═══════════════════════════════════════════════════════════════════
           PT MSN SIGNATURE LOGO SPINNER LOADER (100% MATCH WITH DASHBOARD)
           ═══════════════════════════════════════════════════════════════════ */
        .msn-loader-backdrop {
            position: fixed;
            inset: 0;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(7, 21, 43, 0.86);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            z-index: 999999;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            opacity: 1;
            visibility: visible;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }

        .msn-loader-backdrop.fade-out {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }

        .msn-logo-loader-container {
            position: relative;
            width: 96px;
            height: 96px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Outer spinning neon glow ring */
        .msn-loader-ring {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 3.5px solid rgba(255, 255, 255, 0.08);
            border-top-color: #0080ff;
            border-right-color: #00d2ff;
            border-bottom-color: #38bdf8;
            animation: msnRingSpin 1s cubic-bezier(0.55, 0.15, 0.45, 0.85) infinite;
            box-shadow: 0 0 20px rgba(0, 210, 255, 0.5);
        }

        /* Secondary outer counter-spinning dash ring */
        .msn-loader-ring-pulse {
            position: absolute;
            top: -6px;
            left: -6px;
            width: calc(100% + 12px);
            height: calc(100% + 12px);
            border-radius: 50%;
            border: 2px dashed rgba(56, 189, 248, 0.4);
            animation: msnRingSpinReverse 4s linear infinite;
        }

        /* Inner logo circle container */
        .msn-loader-logo-wrap {
            width: 64px;
            height: 64px;
            max-width: 64px;
            max-height: 64px;
            border-radius: 50%;
            background: #ffffff;
            padding: 6px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.35), 0 0 0 2px rgba(0, 128, 255, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2;
            overflow: hidden;
            animation: msnLogoBreathing 1.8s ease-in-out infinite alternate;
        }

        .msn-loader-logo-img {
            width: 100% !important;
            height: 100% !important;
            max-width: 52px !important;
            max-height: 52px !important;
            object-fit: contain;
            border-radius: 50%;
            display: block;
        }

        .msn-loader-text {
            margin-top: 1.25rem;
            font-size: 0.88rem;
            font-weight: 600;
            color: #ffffff;
            letter-spacing: 0.3px;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.6);
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .msn-loader-dots span {
            animation: msnDots 1.4s infinite;
            opacity: 0;
        }
        .msn-loader-dots span:nth-child(1) { animation-delay: 0s; }
        .msn-loader-dots span:nth-child(2) { animation-delay: 0.2s; }
        .msn-loader-dots span:nth-child(3) { animation-delay: 0.4s; }

        @keyframes msnRingSpin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @keyframes msnRingSpinReverse {
            0% { transform: rotate(360deg); }
            100% { transform: rotate(0deg); }
        }

        @keyframes msnLogoBreathing {
            0% { transform: scale(0.95); box-shadow: 0 4px 12px rgba(0, 128, 255, 0.2); }
            100% { transform: scale(1.05); box-shadow: 0 6px 24px rgba(0, 210, 255, 0.6); }
        }

        @keyframes msnDots {
            0%, 20% { opacity: 0; }
            50% { opacity: 1; }
            100% { opacity: 0; }
        }
    </style>
</head>
<body>

<!-- ════ PT MSN SIGNATURE LOGO PRELOADER (EXACT MATCH WITH DASHBOARD) ════ -->
<div id="msnGlobalPreloader" class="msn-loader-backdrop">
    <div class="msn-logo-loader-container">
        <div class="msn-loader-ring-pulse"></div>
        <div class="msn-loader-ring"></div>
        <div class="msn-loader-logo-wrap">
            <img src="{{ asset('assets/logo-msn BG Trans - Copy2.png') }}" alt="PT MSN" class="msn-loader-logo-img">
        </div>
    </div>
    <div class="msn-loader-text" id="msnGlobalLoaderText">
        <span id="msnLoaderMsg">Memuat Halaman</span>
        <span class="msn-loader-dots"><span>.</span><span>.</span><span>.</span></span>
    </div>
</div>

<!-- ════ FULLSCREEN CONTAINER (EDGE-TO-EDGE) ════ -->
<div class="login-fullscreen-container">

    <!-- ── 1. DESKTOP LEFT BRAND PANEL (FULL-HEIGHT SPLIT) ── -->
    <div class="desktop-brand-side">
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

        <!-- Layered Cloud / Wave SVG Divider (Desktop Full Height) -->
        <div class="desktop-wave-wrapper">
            <svg viewBox="0 0 120 1000" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
                <!-- Layer 1: Sky Blue Shadow Contour -->
                <path d="M0,0 C50,80 15,200 55,320 C95,440 35,560 70,680 C100,780 40,890 80,1000 L120,1000 L120,0 Z" fill="rgba(44, 127, 255, 0.35)"/>
                <!-- Layer 2: Light Blue Contour -->
                <path d="M25,0 C70,90 35,220 75,340 C115,460 55,580 90,700 C120,800 65,910 100,1000 L120,1000 L120,0 Z" fill="rgba(219, 234, 254, 0.85)"/>
                <!-- Layer 3: Pure White Flowing Wave -->
                <path d="M50,0 C95,105 55,240 95,360 C135,480 80,600 110,720 C140,820 90,930 120,1000 L120,1000 L120,0 Z" fill="#ffffff"/>
            </svg>
        </div>
    </div>

    <!-- ── 2. MOBILE TOP BANNER (FULL-WIDTH WITH WAVE) ── -->
    <div class="mobile-brand-top">
        <div class="brand-welcome-title">Selamat Datang di</div>
        <div class="brand-logo-circle">
            <img src="{{ asset('assets/logo-msn BG Trans - Copy2.png') }}" alt="Logo PT MSN">
        </div>
        <div class="brand-app-name">MSN Work Report</div>
        <div class="mobile-brand-sub">Sistem Tiketing &amp; Manajemen Jaringan</div>

        <!-- Layered Cloud / Wave SVG Divider (Mobile Edge to Edge) -->
        <div class="mobile-wave-wrapper">
            <svg viewBox="0 0 1440 140" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
                <!-- Layer 1: Sky Blue Shadow Layer -->
                <path d="M0,0 C240,60 480,15 720,55 C960,95 1200,30 1440,60 L1440,140 L0,140 Z" fill="rgba(44, 127, 255, 0.35)"/>
                <!-- Layer 2: Light Blue Layer -->
                <path d="M0,25 C260,80 520,30 760,75 C1000,120 1220,50 1440,85 L1440,140 L0,140 Z" fill="rgba(219, 234, 254, 0.85)"/>
                <!-- Layer 3: Solid White Flowing Wave -->
                <path d="M0,50 C280,105 540,55 800,95 C1060,135 1260,75 1440,110 L1440,140 L0,140 Z" fill="#ffffff"/>
            </svg>
        </div>
    </div>

    <!-- ── 3. FORM SECTION (FULL-WIDTH / FULL-HEIGHT RIGHT SIDE) ── -->
    <div class="desktop-form-side">
        <div class="form-inner-box">
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
        el.style.borderColor = '#2C7FFF';
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

// PT MSN Signature Loader (Exact match with dashboard)
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

window.addEventListener('load', () => setTimeout(window.hideMsnLoader, 120));
document.addEventListener('DOMContentLoaded', () => setTimeout(window.hideMsnLoader, 250));
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
