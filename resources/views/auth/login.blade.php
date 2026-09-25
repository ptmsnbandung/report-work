<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Login | Sistem Tiketing Gangguan PT MSN</title>
    <meta name="description" content="Portal Terpadu Sistem Manajemen Gangguan & Tiketing Jaringan Backbone PT MSN">

    <!-- Favicon & PWA Primary Tags -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#060e18">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="MSN Backbone">
    <meta name="application-name" content="MSN Backbone">
    <meta name="msapplication-TileColor" content="#060e18">
    <link rel="icon" type="image/png" href="{{ asset('assets/logo-msn BG Trans - Copy2.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/logo-msn BG Trans - Copy2.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/icon-512.png') }}">

    <!-- Google Fonts: Plus Jakarta Sans / Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --bg-deep: #050b14;
            --bg-card: rgba(10, 22, 38, 0.82);
            --navy-primary: #0a192f;
            --navy-surface: #0e2442;
            --teal-accent: #0d9488;
            --teal-light: #14b8a6;
            --cyan-glow: #38bdf8;
            --indigo-accent: #6366f1;
            --border-glass: rgba(56, 189, 248, 0.16);
            --border-subtle: rgba(255, 255, 255, 0.08);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
        }

        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html, body {
            min-height: 100vh;
            min-height: 100dvh;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background-color: var(--bg-deep);
            color: var(--text-main);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        body {
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            padding: clamp(1rem, 2.5vh, 2rem) clamp(0.75rem, 2vw, 1.5rem);
            box-sizing: border-box;
            overflow-x: hidden;
        }

        /* ── CANVAS & BACKGROUND GLOW ── */
        #networkCanvas {
            position: fixed;
            inset: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            pointer-events: none;
        }

        .ambient-glow {
            position: fixed;
            border-radius: 50%;
            pointer-events: none;
            filter: blur(100px);
            z-index: 0;
            opacity: 0.6;
            transition: all 1s ease;
        }
        .glow-1 {
            width: 480px; height: 480px;
            background: radial-gradient(circle, rgba(13, 148, 136, 0.22) 0%, rgba(13, 148, 136, 0) 70%);
            top: -100px;
            left: -80px;
        }
        .glow-2 {
            width: 450px; height: 450px;
            background: radial-gradient(circle, rgba(56, 189, 248, 0.18) 0%, rgba(56, 189, 248, 0) 70%);
            bottom: -80px;
            right: -60px;
        }
        .glow-3 {
            width: 350px; height: 350px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.14) 0%, rgba(99, 102, 241, 0) 70%);
            top: 45%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        /* ── MAIN LOGIN CONTAINER ── */
        .login-wrapper {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 1020px;
            min-height: 560px;
            margin: auto;
            border-radius: 24px;
            background: var(--bg-card);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid var(--border-glass);
            box-shadow:
                0 0 0 1px rgba(255, 255, 255, 0.05),
                0 30px 70px -15px rgba(0, 0, 0, 0.8),
                0 0 45px rgba(13, 148, 136, 0.15);
            overflow: hidden;
            display: flex;
            flex-direction: row;
        }

        @media (max-width: 991px) {
            body {
                padding: 1.25rem 0.85rem;
            }
            .login-wrapper {
                max-width: 440px;
                min-height: auto;
                border-radius: 20px;
                flex-direction: column;
                box-shadow: 0 20px 45px rgba(0, 0, 0, 0.6), 0 0 30px rgba(13, 148, 136, 0.15);
            }
            .brand-panel {
                display: none !important;
            }
            .form-panel {
                padding: 2rem 1.6rem !important;
            }
            .form-header {
                text-align: center;
                margin-bottom: 1.15rem;
            }
            .form-header h2 {
                font-size: 1.4rem;
            }
            .quick-roles-grid {
                gap: 0.4rem !important;
            }
            .role-chip-btn {
                flex-direction: column !important;
                align-items: center !important;
                justify-content: center !important;
                text-align: center !important;
                padding: 0.55rem 0.25rem !important;
                border-radius: 10px !important;
                gap: 0.3rem !important;
            }
            .role-chip-icon {
                width: 28px !important;
                height: 28px !important;
                margin: 0 auto !important;
            }
            .role-chip-name {
                font-size: 0.74rem !important;
                width: 100% !important;
                text-align: center !important;
                white-space: normal !important;
            }
            .role-chip-sub {
                font-size: 0.62rem !important;
                width: 100% !important;
                text-align: center !important;
                white-space: normal !important;
            }
        }

        /* ── LEFT BRAND PANEL (DESKTOP) ── */
        .brand-panel {
            width: 46%;
            flex-shrink: 0;
            background: linear-gradient(155deg, rgba(14, 36, 66, 0.95) 0%, rgba(7, 18, 34, 0.98) 60%, rgba(4, 11, 20, 1) 100%);
            padding: 2.85rem 2.4rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
            border-right: 1px solid var(--border-subtle);
        }

        /* Subtle grid & circuit accents */
        .brand-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(56, 189, 248, 0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(56, 189, 248, 0.04) 1px, transparent 1px);
            background-size: 28px 28px;
            pointer-events: none;
        }

        .brand-header {
            position: relative;
            z-index: 2;
        }

        .brand-logo-img {
            height: 42px;
            width: auto;
            max-width: 190px;
            object-fit: contain;
            filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.4));
        }

        /* Live status badge */
        .portal-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.28rem 0.8rem;
            border-radius: 999px;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            color: var(--cyan-glow);
            background: rgba(56, 189, 248, 0.1);
            border: 1px solid rgba(56, 189, 248, 0.25);
            margin-top: 1.25rem;
            backdrop-filter: blur(8px);
        }

        .pulse-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #10b981;
            box-shadow: 0 0 8px #10b981;
            animation: pulse-ring 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes pulse-ring {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(1.3); }
        }

        .brand-headline {
            font-size: 1.65rem;
            font-weight: 800;
            line-height: 1.25;
            letter-spacing: -0.5px;
            margin-top: 1.15rem;
            margin-bottom: 0.6rem;
            color: #ffffff;
        }

        .brand-headline .gradient-text {
            background: linear-gradient(120deg, #38bdf8 0%, #14b8a6 50%, #818cf8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .brand-subtitle {
            font-size: 0.86rem;
            line-height: 1.6;
            color: rgba(226, 232, 240, 0.78);
            margin-bottom: 1.75rem;
        }

        /* Minimalist Brand Highlights */
        .brand-highlights {
            display: flex;
            flex-direction: column;
            gap: 0.9rem;
            position: relative;
            z-index: 2;
        }

        .highlight-item {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            font-size: 0.85rem;
            color: #e2e8f0;
            font-weight: 500;
        }

        .highlight-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            flex-shrink: 0;
        }
        .icon-cyan { background: rgba(56, 189, 248, 0.15); color: #38bdf8; border: 1px solid rgba(56, 189, 248, 0.25); }
        .icon-teal { background: rgba(20, 184, 166, 0.15); color: #2dd4bf; border: 1px solid rgba(20, 184, 166, 0.25); }
        .icon-indigo { background: rgba(99, 102, 241, 0.15); color: #a5b4fc; border: 1px solid rgba(99, 102, 241, 0.25); }

        .brand-footer {
            margin-top: 1.5rem;
            padding-top: 0.85rem;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.75rem;
            color: rgba(148, 163, 184, 0.65);
            position: relative;
            z-index: 2;
        }

        /* ── RIGHT FORM PANEL ── */
        .form-panel {
            flex-grow: 1;
            background: #ffffff;
            padding: 3rem 2.85rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
        }

        .form-header {
            margin-bottom: 1.35rem;
        }

        .form-header h2 {
            font-size: 1.6rem;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.5px;
            margin-bottom: 0.3rem;
        }

        .form-header p {
            font-size: 0.86rem;
            color: #64748b;
            margin: 0;
        }

        /* Form Labels & Controls */
        .form-group-custom {
            margin-bottom: 1.1rem;
        }

        .custom-label {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.8rem;
            font-weight: 700;
            color: #334155;
            margin-bottom: 0.4rem;
        }

        .input-box {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-box .input-icon-left {
            position: absolute;
            left: 14px;
            color: #94a3b8;
            font-size: 1.05rem;
            pointer-events: none;
            transition: color 0.2s ease;
            z-index: 5;
        }

        .custom-input {
            width: 100%;
            height: 48px;
            padding: 0.6rem 1rem 0.6rem 44px;
            border-radius: 12px;
            border: 1.5px solid #e2e8f0;
            background: #f8fafc;
            color: #0f172a;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .custom-input:focus {
            background: #ffffff;
            border-color: #0d9488;
            box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.12);
            outline: none;
        }

        .input-box:focus-within .input-icon-left {
            color: #0d9488;
        }

        .btn-toggle-pwd {
            position: absolute;
            right: 8px;
            background: transparent;
            border: none;
            color: #94a3b8;
            padding: 6px 10px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s ease;
            z-index: 5;
        }
        .btn-toggle-pwd:hover {
            color: #475569;
            background: #f1f5f9;
        }

        /* Checkbox */
        .custom-checkbox-container {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            user-select: none;
            cursor: pointer;
        }

        .form-check-input {
            width: 1.1rem;
            height: 1.1rem;
            border-radius: 5px;
            border: 1.5px solid #cbd5e1;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .form-check-input:checked {
            background-color: #0d9488;
            border-color: #0d9488;
            box-shadow: 0 0 0 2px rgba(13, 148, 136, 0.18);
        }

        /* Main Submit Button */
        .btn-submit-login {
            width: 100%;
            height: 48px;
            border-radius: 12px;
            background: linear-gradient(135deg, #0a2540 0%, #0d9488 100%);
            border: none;
            color: #ffffff;
            font-size: 0.94rem;
            font-weight: 700;
            letter-spacing: 0.2px;
            box-shadow: 0 8px 20px -4px rgba(13, 148, 136, 0.45);
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-submit-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 24px -4px rgba(13, 148, 136, 0.6);
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
            padding: 0 10px;
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
            border-color: #0d9488;
            background: #f0fdfa;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(13, 148, 136, 0.12);
        }
        .role-chip-btn:active {
            transform: scale(0.98);
        }

        .role-chip-icon {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            flex-shrink: 0;
        }

        .role-chip-info {
            overflow: hidden;
        }
        .role-chip-name {
            font-size: 0.78rem;
            font-weight: 700;
            color: #1e293b;
            line-height: 1.15;
            white-space: nowrap;
            text-overflow: ellipsis;
            overflow: hidden;
        }
        .role-chip-sub {
            font-size: 0.65rem;
            color: #64748b;
            white-space: nowrap;
            text-overflow: ellipsis;
            overflow: hidden;
        }

        /* Alert styling */
        .toast-banner {
            border-radius: 10px;
            padding: 0.65rem 0.85rem;
            font-size: 0.82rem;
            font-weight: 500;
            display: flex;
            align-items: flex-start;
            gap: 0.6rem;
            margin-bottom: 1rem;
            animation: fadeInDown 0.3s ease;
        }
        .toast-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
        }
        .toast-info {
            background: #f0fdfa;
            border: 1px solid #ccfbf1;
            color: #0f766e;
        }

        /* ═══════════════════════════════════════════════════════════════════
           PT MSN SIGNATURE LOGO SPINNER LOADER
           ═══════════════════════════════════════════════════════════════════ */
        .msn-loader-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(5, 11, 20, 0.86);
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
            width: 92px;
            height: 92px;
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
            border: 3.5px solid rgba(255, 255, 255, 0.08);
            border-top-color: #0080ff;
            border-right-color: #00d2ff;
            border-bottom-color: #38bdf8;
            animation: msnRingSpin 1s cubic-bezier(0.55, 0.15, 0.45, 0.85) infinite;
            box-shadow: 0 0 22px rgba(0, 210, 255, 0.55);
        }

        .msn-loader-ring-pulse {
            position: absolute;
            top: -6px;
            left: -6px;
            width: calc(100% + 12px);
            height: calc(100% + 12px);
            border-radius: 50%;
            border: 2px dashed rgba(56, 189, 248, 0.45);
            animation: msnRingSpinReverse 4s linear infinite;
        }

        .msn-loader-logo-wrap {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: #ffffff;
            padding: 5px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4), 0 0 0 2px rgba(0, 128, 255, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2;
            animation: msnLogoBreathing 1.8s ease-in-out infinite alternate;
        }

        .msn-loader-logo-img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 50%;
        }

        .msn-loader-text {
            margin-top: 1.25rem;
            font-size: 0.9rem;
            font-weight: 600;
            color: #ffffff;
            letter-spacing: 0.4px;
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
            0% { transform: scale(0.95); box-shadow: 0 4px 14px rgba(0, 128, 255, 0.2); }
            100% { transform: scale(1.05); box-shadow: 0 6px 26px rgba(0, 210, 255, 0.65); }
        }

        @keyframes msnDots {
            0%, 20% { opacity: 0; }
            50% { opacity: 1; }
            100% { opacity: 0; }
        }

        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

<!-- ════ PT MSN SIGNATURE LOGO PRELOADER ════ -->
<div id="msnGlobalPreloader" class="msn-loader-backdrop">
    <div class="msn-logo-loader-container">
        <div class="msn-loader-ring-pulse"></div>
        <div class="msn-loader-ring"></div>
        <div class="msn-loader-logo-wrap">
            <img src="{{ asset('assets/logo-msn BG Trans - Copy2.png') }}" alt="PT MSN" class="msn-loader-logo-img">
        </div>
    </div>
    <div class="msn-loader-text" id="msnGlobalLoaderText">
        <span id="msnLoaderMsg">Memuat Aplikasi</span>
        <span class="msn-loader-dots"><span>.</span><span>.</span><span>.</span></span>
    </div>
</div>

<!-- Network Canvas Ambient Animation -->
<canvas id="networkCanvas"></canvas>

<!-- Ambient Glow Elements -->
<div class="ambient-glow glow-1"></div>
<div class="ambient-glow glow-2"></div>
<div class="ambient-glow glow-3"></div>

<!-- Main Container Card -->
<div class="login-wrapper">

    <!-- ── LEFT: BRAND & CAPABILITIES PANEL ── -->
    <div class="brand-panel">
        <div class="brand-header">
            <!-- Brand Logo -->
            <div>
                <img src="{{ asset('assets/logo-msn BG Trans.png') }}"
                     alt="Logo PT MSN"
                     class="brand-logo-img">
            </div>

            <div class="portal-badge">
                <span class="pulse-dot"></span>
                <span>NOC BACKBONE</span>
            </div>

            <h1 class="brand-headline">
                Sistem Tiketing<br>
                <span class="gradient-text">Gangguan Backbone</span>
            </h1>

            <p class="brand-subtitle">
                Portal operasional insiden &amp; manajemen jaringan fiber optik PT Media Solusi Network.
            </p>

            <!-- Minimalist Highlights -->
            <div class="brand-highlights">
                <div class="highlight-item">
                    <div class="highlight-icon icon-cyan">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <span>Tracking SLA &amp; Stop Clock MTTR</span>
                </div>
                <div class="highlight-item">
                    <div class="highlight-icon icon-teal">
                        <i class="bi bi-diagram-3-fill"></i>
                    </div>
                    <span>Peta Joint Closure &amp; Manuver Core</span>
                </div>
                <div class="highlight-item">
                    <div class="highlight-icon icon-indigo">
                        <i class="bi bi-broadcast"></i>
                    </div>
                    <span>Koordinasi Lapangan Multi-Role</span>
                </div>
            </div>
        </div>

        <!-- Footer Info -->
        <div class="brand-footer">
            <div class="d-flex align-items-center gap-1.5">
                <i class="bi bi-shield-lock-fill text-info"></i>
                <span>Enterprise Security</span>
            </div>
            <span>&copy; {{ date('Y') }} PT MSN</span>
        </div>
    </div>

    <!-- ── RIGHT: LOGIN FORM PANEL ── -->
    <div class="form-panel">
        <!-- Mobile Brand Header (only on small screens) -->
        <div class="d-lg-none text-center mb-3">
            <img src="{{ asset('assets/logo-msn BG Trans.png') }}"
                 alt="Logo PT MSN"
                 style="height: 38px; max-width: 170px; object-fit: contain;">
            <div class="d-block mt-1">
                <span class="portal-badge" style="margin-top: 0.35rem; font-size: 0.65rem; padding: 0.2rem 0.65rem;">
                    <span class="pulse-dot"></span>
                    <span>NOC BACKBONE</span>
                </span>
            </div>
        </div>

        <div class="form-header">
            <h2>Masuk ke Akun</h2>
            <p>Silakan masukkan kredensial akun Anda untuk login.</p>
        </div>

        <!-- Flash Alert Error -->
        @if($errors->any())
            <div class="toast-banner toast-error">
                <i class="bi bi-exclamation-triangle-fill fs-5 flex-shrink-0"></i>
                <div>{{ $errors->first() }}</div>
            </div>
        @endif

        @if(session('info'))
            <div class="toast-banner toast-info">
                <i class="bi bi-info-circle-fill fs-5 flex-shrink-0"></i>
                <div>{{ session('info') }}</div>
            </div>
        @endif

        <!-- Login Form -->
        <form method="POST" action="{{ route('login.post') }}" id="loginForm">
            @csrf
            @if(request()->has('redirect'))
                <input type="hidden" name="redirect_url" value="{{ request('redirect') }}">
            @elseif(session()->has('url.intended'))
                <input type="hidden" name="redirect_url" value="{{ session('url.intended') }}">
            @endif

            <!-- Email Address Input -->
            <div class="form-group-custom">
                <label for="email" class="custom-label">
                    <span>Email Perusahaan</span>
                </label>
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
                <div class="custom-label">
                    <span>Kata Sandi (Password)</span>
                </div>
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

            <!-- Remember Me Session (Default Active 30 Days) -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <label class="custom-checkbox-container" for="remember">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" value="1" checked>
                    <span class="small text-secondary fw-semibold" style="font-size: 0.82rem;">
                        Ingat saya di perangkat ini (30 Hari)
                    </span>
                </label>
            </div>

            <!-- Action Button -->
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
            <!-- Admin -->
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
                    <div class="role-chip-sub">Tiket & Dispatch</div>
                </div>
            </button>

            <!-- Teknis -->
            <button type="button" class="role-chip-btn" onclick="fillLogin('teknis@connecti.id', 'password')">
                <div class="role-chip-icon" style="background:#fff7ed; color:#ea580c;">
                    <i class="bi bi-tools"></i>
                </div>
                <div class="role-chip-info">
                    <div class="role-chip-name">Tim Teknis</div>
                    <div class="role-chip-sub">Lapangan & JC</div>
                </div>
            </button>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
/* ── SMOOTH HIGH-PERFORMANCE NETWORK CANVAS ── */
(function () {
    const canvas = document.getElementById('networkCanvas');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');

    let W, H, nodes = [], raf;
    const NODE_COUNT = window.innerWidth < 768 ? 32 : 55;
    const MAX_DIST   = window.innerWidth < 768 ? 110 : 150;
    const COLOR_TEAL = 'rgba(20, 184, 166, ';
    const COLOR_CYAN = 'rgba(56, 189, 248, ';

    function resize() {
        W = canvas.width  = window.innerWidth;
        H = canvas.height = window.innerHeight;
    }

    function createNodes() {
        nodes = [];
        for (let i = 0; i < NODE_COUNT; i++) {
            nodes.push({
                x:  Math.random() * W,
                y:  Math.random() * H,
                vx: (Math.random() - 0.5) * 0.35,
                vy: (Math.random() - 0.5) * 0.35,
                r:  Math.random() * 2 + 1.2,
                type: Math.random() > 0.5 ? 'cyan' : 'teal',
                pulse: Math.random() * Math.PI * 2
            });
        }
    }

    function draw() {
        ctx.clearRect(0, 0, W, H);

        // Update positions
        for (let i = 0; i < nodes.length; i++) {
            const n = nodes[i];
            n.x += n.vx;
            n.y += n.vy;
            n.pulse += 0.02;

            if (n.x < 0 || n.x > W) n.vx *= -1;
            if (n.y < 0 || n.y > H) n.vy *= -1;
        }

        // Connect nodes
        for (let i = 0; i < nodes.length; i++) {
            for (let j = i + 1; j < nodes.length; j++) {
                const dx = nodes[i].x - nodes[j].x;
                const dy = nodes[i].y - nodes[j].y;
                const dist = Math.sqrt(dx * dx + dy * dy);

                if (dist < MAX_DIST) {
                    const alpha = (1 - dist / MAX_DIST) * 0.16;
                    const color = nodes[i].type === 'cyan' ? COLOR_CYAN : COLOR_TEAL;
                    ctx.beginPath();
                    ctx.moveTo(nodes[i].x, nodes[i].y);
                    ctx.lineTo(nodes[j].x, nodes[j].y);
                    ctx.strokeStyle = color + alpha + ')';
                    ctx.lineWidth   = 0.75;
                    ctx.stroke();
                }
            }
        }

        // Draw particle dots
        for (let i = 0; i < nodes.length; i++) {
            const n = nodes[i];
            const pulse = 0.6 + 0.4 * Math.sin(n.pulse);
            const alpha = 0.4 + 0.4 * pulse;
            const color = n.type === 'cyan' ? COLOR_CYAN : COLOR_TEAL;

            // Outer subtle halo
            ctx.beginPath();
            ctx.arc(n.x, n.y, n.r * 2.8, 0, Math.PI * 2);
            ctx.fillStyle = color + (alpha * 0.15) + ')';
            ctx.fill();

            // Core dot
            ctx.beginPath();
            ctx.arc(n.x, n.y, n.r, 0, Math.PI * 2);
            ctx.fillStyle = color + alpha + ')';
            ctx.fill();
        }

        raf = requestAnimationFrame(draw);
    }

    window.addEventListener('resize', () => { resize(); createNodes(); });
    resize();
    createNodes();
    draw();
})();

/* ── FORM INTERACTIONS ── */
function fillLogin(email, password) {
    const emailInput = document.getElementById('email');
    const pwdInput   = document.getElementById('password');

    if (!emailInput || !pwdInput) return;

    emailInput.value = email;
    pwdInput.value   = password;

    // Visual feedback highlight
    [emailInput, pwdInput].forEach(el => {
        el.style.borderColor = '#0d9488';
        el.style.backgroundColor = '#f0fdfa';
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

// Loading state on form submit
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
            console.debug('SW Registration info:', err);
        });
    });
}
</script>

</body>
</html>
