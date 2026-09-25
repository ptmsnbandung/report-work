<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Sistem Tiketing Gangguan PT MSN</title>
    <meta name="description" content="Login ke Sistem Tiketing Gangguan Backbone PT MSN">

    <!-- Favicon & PWA Primary Tags -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#071525">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="MSN Report">
    <meta name="application-name" content="MSN Report">
    <meta name="msapplication-TileColor" content="#071525">
    <link rel="icon" type="image/png" href="{{ asset('assets/logo-msn BG Trans - Copy2.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/logo-msn BG Trans - Copy2.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/logo-msn BG Trans - Copy2.png') }}">

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --navy:   #071525;
            --navy-m: #0c1f35;
            --teal:   #0d9488;
            --teal-l: #14b8a6;
            --elec:   #38bdf8;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html, body {
            height: 100%;
            font-family: 'Inter', system-ui, sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        body {
            background: var(--navy);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            position: relative;
            overflow: hidden;
            zoom: 0.9;
        }

        /* ── NETWORK CANVAS BACKGROUND ── */
        #networkCanvas {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
        }

        /* ── AMBIENT GLOW ORBS ── */
        .glow-orb {
            position: fixed;
            border-radius: 50%;
            pointer-events: none;
            filter: blur(80px);
            z-index: 0;
        }
        .glow-orb-1 {
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(13,148,136,0.18) 0%, transparent 70%);
            top: -100px; left: -100px;
        }
        .glow-orb-2 {
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(56,189,248,0.12) 0%, transparent 70%);
            bottom: -80px; right: -60px;
        }
        .glow-orb-3 {
            width: 350px; height: 350px;
            background: radial-gradient(circle, rgba(15,61,99,0.3) 0%, transparent 70%);
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
        }

        /* ── MAIN CONTAINER ── */
        .login-container {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 980px;
            margin: 1.5rem;
            border-radius: 24px;
            overflow: hidden;
            display: flex;
            box-shadow:
                0 0 0 1px rgba(13,148,136,0.15),
                0 25px 60px rgba(0,0,0,0.5),
                inset 0 0 0 1px rgba(255,255,255,0.04);
        }

        /* ── LEFT BRAND PANEL ── */
        .brand-panel {
            width: 42%;
            flex-shrink: 0;
            background: linear-gradient(160deg, #0c1f35 0%, #071525 60%, #030e1a 100%);
            padding: 2.5rem;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
        }

        /* Grid pattern on brand panel */
        .brand-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(13,148,136,0.06) 1px, transparent 1px),
                linear-gradient(90deg, rgba(13,148,136,0.06) 1px, transparent 1px);
            background-size: 28px 28px;
        }

        /* Glowing divider line */
        .brand-panel::after {
            content: '';
            position: absolute;
            top: 0; right: 0; bottom: 0;
            width: 1px;
            background: linear-gradient(
                180deg,
                transparent 0%,
                var(--teal) 30%,
                var(--elec) 65%,
                transparent 100%
            );
            opacity: 0.5;
        }

        .brand-panel > * { position: relative; z-index: 1; }

        /* Network node decorations */
        .node-deco {
            position: absolute;
            border-radius: 50%;
            border: 1px solid rgba(13,148,136,0.3);
            animation: node-pulse 4s ease-in-out infinite;
        }
        .node-deco.n1 {
            width: 180px; height: 180px;
            top: -60px; right: -60px;
            animation-delay: 0s;
        }
        .node-deco.n2 {
            width: 100px; height: 100px;
            bottom: 80px; left: -30px;
            animation-delay: 1.5s;
        }
        .node-deco.n3 {
            width: 60px; height: 60px;
            bottom: 60px; right: 40px;
            background: rgba(13,148,136,0.06);
            animation-delay: 2.5s;
        }

        @keyframes node-pulse {
            0%, 100% { transform: scale(1);   opacity: 0.3; }
            50%       { transform: scale(1.08); opacity: 0.6; }
        }

        /* Logo */
        .brand-logo {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            margin-bottom: auto;
        }

        .logo-icon {
            width: 46px; height: 46px;
            background: linear-gradient(135deg, var(--teal) 0%, var(--elec) 100%);
            border-radius: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 20px rgba(13,148,136,0.5), 0 0 40px rgba(13,148,136,0.2);
            flex-shrink: 0;
        }
        .logo-icon i { color: #fff; font-size: 1.4rem; }

        .logo-text strong {
            display: block;
            font-size: 1.15rem;
            font-weight: 800;
            color: #fff;
            letter-spacing: -0.3px;
        }
        .logo-text span {
            font-size: 0.67rem;
            color: var(--teal-l);
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-weight: 600;
        }

        /* Brand Content */
        .brand-content {
            margin-top: 2.5rem;
            flex-grow: 1;
        }

        .brand-headline {
            font-size: 1.6rem;
            font-weight: 800;
            color: #fff;
            line-height: 1.2;
            letter-spacing: -0.5px;
            margin-bottom: 1rem;
        }

        .brand-headline .highlight {
            background: linear-gradient(90deg, var(--teal-l), var(--elec));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .brand-desc {
            font-size: 0.83rem;
            color: rgba(148, 180, 200, 0.8);
            line-height: 1.7;
            margin-bottom: 2rem;
        }

        /* Feature pills */
        .feature-list {
            display: flex;
            flex-direction: column;
            gap: 0.6rem;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            font-size: 0.8rem;
            color: rgba(148, 180, 200, 0.85);
        }

        .feature-icon {
            width: 28px; height: 28px;
            border-radius: 8px;
            background: rgba(13,148,136,0.15);
            border: 1px solid rgba(13,148,136,0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .feature-icon i { font-size: 0.85rem; color: var(--teal-l); }

        /* Brand Footer */
        .brand-footer {
            margin-top: 2rem;
            padding-top: 1.25rem;
            border-top: 1px solid rgba(255,255,255,0.06);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.72rem;
            color: rgba(148, 180, 200, 0.45);
        }

        /* ── RIGHT FORM PANEL ── */
        .form-panel {
            flex-grow: 1;
            background: #ffffff;
            padding: 2.5rem 2.75rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-header {
            margin-bottom: 2rem;
        }

        .form-header h3 {
            font-size: 1.5rem;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.5px;
            margin-bottom: 0.35rem;
        }

        .form-header p {
            font-size: 0.85rem;
            color: #64748b;
            margin: 0;
        }

        /* Form label */
        .form-label {
            font-size: 0.82rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.45rem;
        }

        /* Input styling */
        .form-control, .form-select {
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            padding: 0.65rem 0.9rem;
            font-size: 0.875rem;
            color: #0f172a;
            background: #fafbfc;
            transition: all 0.18s ease;
        }

        .form-control:focus {
            border-color: var(--teal-l);
            box-shadow: 0 0 0 3px rgba(13,148,136,0.12);
            background: #fff;
            outline: none;
        }

        .input-group .form-control {
            border-left: none;
            border-radius: 0 10px 10px 0;
        }
        .input-group .form-control:focus {
            border-color: var(--teal-l);
            box-shadow: 0 0 0 3px rgba(13,148,136,0.12);
            z-index: 3;
        }

        .input-group-text {
            border: 1.5px solid #e2e8f0;
            border-right: none;
            border-radius: 10px 0 0 10px;
            background: #f8fafc;
            color: #94a3b8;
            padding: 0 0.9rem;
        }

        .input-group:focus-within .input-group-text {
            border-color: var(--teal-l);
        }

        .input-group .btn-icon-right {
            border: 1.5px solid #e2e8f0;
            border-left: none;
            border-radius: 0 10px 10px 0;
            background: #f8fafc;
            color: #94a3b8;
            padding: 0 0.9rem;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .input-group .btn-icon-right:hover { color: #475569; background: #f1f5f9; }

        /* Remember + submit */
        .form-check-input:checked {
            background-color: var(--teal);
            border-color: var(--teal);
        }

        /* Submit Button */
        .btn-login {
            background: linear-gradient(135deg, #0f3d63 0%, #0d9488 100%);
            border: none;
            color: #fff;
            font-weight: 700;
            font-size: 0.9rem;
            padding: 0.75rem;
            border-radius: 10px;
            letter-spacing: 0.2px;
            transition: all 0.18s ease;
            box-shadow: 0 4px 14px rgba(13,148,136,0.35);
            position: relative;
            overflow: hidden;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, transparent 50%);
            transition: opacity 0.18s ease;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 22px rgba(13,148,136,0.45);
            color: #fff;
        }
        .btn-login:active { transform: translateY(0); }

        /* Divider */
        .divider-text {
            text-align: center;
            font-size: 0.75rem;
            color: #94a3b8;
            position: relative;
            margin: 1.5rem 0 1rem;
        }
        .divider-text::before, .divider-text::after {
            content: '';
            position: absolute;
            top: 50%;
            width: calc(50% - 50px);
            height: 1px;
            background: #e2e8f0;
        }
        .divider-text::before { left: 0; }
        .divider-text::after  { right: 0; }

        /* Quick Demo Login */
        .demo-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.5rem;
        }

        .demo-btn {
            padding: 0.5rem 0.75rem;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            background: #fafbfc;
            cursor: pointer;
            text-align: left;
            font-size: 0.78rem;
            transition: all 0.15s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .demo-btn:hover {
            border-color: var(--teal-l);
            background: rgba(13,148,136,0.04);
            transform: translateY(-1px);
        }

        .demo-btn-icon {
            width: 28px; height: 28px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            flex-shrink: 0;
        }

        .demo-btn-text strong {
            display: block;
            font-size: 0.78rem;
            font-weight: 700;
            color: #1e293b;
            line-height: 1.2;
        }

        .demo-btn-text small {
            font-size: 0.67rem;
            color: #94a3b8;
        }

        /* Alert */
        .alert-custom {
            border-radius: 10px;
            border: none;
            font-size: 0.82rem;
            padding: 0.65rem 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.25rem;
        }

        /* Mobile */
        @media (max-width: 767px) {
            .brand-panel { display: none !important; }
            .login-container { margin: 1rem; border-radius: 16px; }
            .form-panel { padding: 2rem 1.5rem; }
        }

        @media (max-width: 991px) {
            .brand-panel { width: 38%; padding: 2rem; }
            .form-panel  { padding: 2rem 2rem; }
        }
    </style>
</head>
<body>

<!-- Network Canvas Background -->
<canvas id="networkCanvas"></canvas>

<!-- Ambient Glow Orbs -->
<div class="glow-orb glow-orb-1"></div>
<div class="glow-orb glow-orb-2"></div>
<div class="glow-orb glow-orb-3"></div>

<!-- Main Login Container -->
<div class="login-container">

    <!-- ── LEFT: BRAND PANEL ── -->
    <div class="brand-panel d-none d-md-flex flex-column">
        <!-- Network node decorations -->
        <div class="node-deco n1"></div>
        <div class="node-deco n2"></div>
        <div class="node-deco n3"></div>

        <!-- Logo -->
        <div class="brand-logo mb-3">
            <img src="{{ asset('assets/logo-msn BG Trans.png') }}" alt="Logo PT MSN" style="height: 42px; width: auto; max-width: 220px; object-fit: contain;">
        </div>

        <!-- Main Content -->
        <div class="brand-content">
            <h2 class="brand-headline">
                Sistem Tiketing<br>
                <span class="highlight">Backbone Network</span>
            </h2>
            <p class="brand-desc">
                Platform terpusat pengelolaan gangguan jaringan backbone.
                Monitoring realtime, tracking SLA, dan kolaborasi tim teknis secara efisien.
            </p>

            <div class="feature-list">
                <div class="feature-item">
                    <div class="feature-icon"><i class="bi bi-ticket-detailed-fill"></i></div>
                    <span>Tiket Gangguan Terintegrasi</span>
                </div>
                <div class="feature-item">
                    <div class="feature-icon"><i class="bi bi-clock-history"></i></div>
                    <span>Monitoring MTTR & SLA Realtime</span>
                </div>
                <div class="feature-item">
                    <div class="feature-icon"><i class="bi bi-geo-alt-fill"></i></div>
                    <span>Tagging Titik Perbaikan FO/Backbone</span>
                </div>
                <div class="feature-item">
                    <div class="feature-icon"><i class="bi bi-people-fill"></i></div>
                    <span>Role-Based Multi-Team Access</span>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="brand-footer">
            <i class="bi bi-shield-check" style="color: var(--teal-l); font-size: 0.9rem;"></i>
            &copy; {{ date('Y') }} PT MSN. All rights reserved.
        </div>
    </div>

    <!-- ── RIGHT: FORM PANEL ── -->
    <div class="form-panel">
        <!-- Mobile Logo (visible only on small screens) -->
        <div class="d-md-none d-flex align-items-center mb-3">
            <img src="{{ asset('assets/logo-msn BG Trans.png') }}" alt="Logo PT MSN" style="height: 32px; max-width: 165px; object-fit: contain;">
        </div>

        <div class="form-header">
            <h3>Masuk ke Sistem</h3>
            <p>Gunakan akun yang telah diberikan oleh administrator.</p>
        </div>

        <!-- Alert Error -->
        @if($errors->any())
            <div class="alert-custom" style="background:#fef2f2;color:#b91c1c;">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <div>{{ $errors->first() }}</div>
            </div>
        @endif

        @if(session('info'))
            <div class="alert-custom" style="background:#eff6ff;color:#1d4ed8;">
                <i class="bi bi-info-circle-fill"></i>
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

            <!-- Email -->
            <div class="mb-3">
                <label for="email" class="form-label">Alamat Email</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-envelope"></i>
                    </span>
                    <input type="email"
                           class="form-control @error('email') is-invalid @enderror"
                           id="email" name="email"
                           value="{{ old('email') }}"
                           placeholder="nama@connecti.id"
                           required autofocus autocomplete="username">
                </div>
            </div>

            <!-- Password -->
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-lock"></i>
                    </span>
                    <input type="password"
                           class="form-control @error('password') is-invalid @enderror"
                           id="password" name="password"
                           placeholder="••••••••"
                           required autocomplete="current-password">
                    <button class="btn-icon-right" type="button" id="togglePassword" title="Tampilkan/sembunyikan password">
                        <i class="bi bi-eye" id="eyeIcon"></i>
                    </button>
                </div>
            </div>

            <!-- Remember Me -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check mb-0">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" value="1">
                    <label class="form-check-label small text-muted" for="remember" style="font-size:0.82rem;">
                        Ingat saya di perangkat ini
                    </label>
                </div>
            </div>

            <!-- Submit -->
            <button type="submit" class="btn-login w-100" id="loginSubmitBtn">
                <span id="loginBtnText">
                    <i class="bi bi-box-arrow-in-right me-2"></i> Masuk Sekarang
                </span>
                <span id="loginBtnLoading" class="d-none">
                    <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                    Memproses...
                </span>
            </button>
        </form>

        <!-- Quick Demo Login -->
        <div class="divider-text">Quick Demo Login</div>

        <div class="demo-grid">
            <button type="button" class="demo-btn" onclick="fillLogin('admin@connecti.id', 'password')">
                <div class="demo-btn-icon" style="background:#eff6ff;">
                    <i class="bi bi-shield-lock-fill" style="color:#3b82f6;"></i>
                </div>
                <div class="demo-btn-text">
                    <strong>Admin NOC</strong>
                    <small>admin@connecti.id</small>
                </div>
            </button>
            <button type="button" class="demo-btn" onclick="fillLogin('helpdesk@connecti.id', 'password')">
                <div class="demo-btn-icon" style="background:#f0fdf4;">
                    <i class="bi bi-headset" style="color:#16a34a;"></i>
                </div>
                <div class="demo-btn-text">
                    <strong>HelpDesk NOC (HelpDesk & SA/CS)</strong>
                    <small>helpdesk@connecti.id</small>
                </div>
            </button>
            <button type="button" class="demo-btn" onclick="fillLogin('teknis@connecti.id', 'password')">
                <div class="demo-btn-icon" style="background:#fff7ed;">
                    <i class="bi bi-tools" style="color:#ea580c;"></i>
                </div>
                <div class="demo-btn-text">
                    <strong>Team Teknis</strong>
                    <small>teknis@connecti.id</small>
                </div>
            </button>
            <button type="button" class="demo-btn" onclick="fillLogin('client@connecti.id', 'password')">
                <div class="demo-btn-icon" style="background:#fafafa;">
                    <i class="bi bi-building" style="color:#64748b;"></i>
                </div>
                <div class="demo-btn-text">
                    <strong>Client</strong>
                    <small>client@connecti.id</small>
                </div>
            </button>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
/* ── NETWORK CANVAS ANIMATION ── */
(function () {
    const canvas = document.getElementById('networkCanvas');
    const ctx    = canvas.getContext('2d');

    let W, H, nodes = [], raf;

    const NODE_COUNT = 55;
    const MAX_DIST   = 140;
    const TEAL       = 'rgba(13,148,136,';
    const ELECTRIC   = 'rgba(56,189,248,';

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
                vx: (Math.random() - 0.5) * 0.45,
                vy: (Math.random() - 0.5) * 0.45,
                r:  Math.random() * 2 + 1.5,
                type: Math.random() > 0.6 ? 'elec' : 'teal',
                pulse: Math.random() * Math.PI * 2
            });
        }
    }

    function draw() {
        ctx.clearRect(0, 0, W, H);

        // Update positions
        nodes.forEach(n => {
            n.x += n.vx;
            n.y += n.vy;
            n.pulse += 0.025;
            if (n.x < 0 || n.x > W) n.vx *= -1;
            if (n.y < 0 || n.y > H) n.vy *= -1;
        });

        // Draw connections
        for (let i = 0; i < nodes.length; i++) {
            for (let j = i + 1; j < nodes.length; j++) {
                const dx   = nodes[i].x - nodes[j].x;
                const dy   = nodes[i].y - nodes[j].y;
                const dist = Math.sqrt(dx * dx + dy * dy);
                if (dist < MAX_DIST) {
                    const alpha = (1 - dist / MAX_DIST) * 0.18;
                    const color = nodes[i].type === 'elec' ? ELECTRIC : TEAL;
                    ctx.beginPath();
                    ctx.moveTo(nodes[i].x, nodes[i].y);
                    ctx.lineTo(nodes[j].x, nodes[j].y);
                    ctx.strokeStyle = color + alpha + ')';
                    ctx.lineWidth   = 0.8;
                    ctx.stroke();
                }
            }
        }

        // Draw nodes
        nodes.forEach(n => {
            const pulse = 0.5 + 0.5 * Math.sin(n.pulse);
            const alpha = 0.35 + 0.3 * pulse;
            const color = n.type === 'elec' ? ELECTRIC : TEAL;

            // Outer glow ring
            ctx.beginPath();
            ctx.arc(n.x, n.y, n.r * 2.5, 0, Math.PI * 2);
            ctx.fillStyle = color + (alpha * 0.15) + ')';
            ctx.fill();

            // Core dot
            ctx.beginPath();
            ctx.arc(n.x, n.y, n.r, 0, Math.PI * 2);
            ctx.fillStyle = color + alpha + ')';
            ctx.fill();
        });

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

    emailInput.value = email;
    pwdInput.value   = password;

    // Flash effect on inputs
    [emailInput, pwdInput].forEach(el => {
        el.style.borderColor = '#0d9488';
        el.style.boxShadow   = '0 0 0 3px rgba(13,148,136,0.15)';
        setTimeout(() => {
            el.style.borderColor = '';
            el.style.boxShadow   = '';
        }, 700);
    });
}

// Toggle password visibility
const toggleBtn = document.getElementById('togglePassword');
const pwdInput  = document.getElementById('password');
const eyeIcon   = document.getElementById('eyeIcon');

if (toggleBtn && pwdInput) {
    toggleBtn.addEventListener('click', () => {
        const isPassword = pwdInput.type === 'password';
        pwdInput.type = isPassword ? 'text' : 'password';
        eyeIcon.className = isPassword ? 'bi bi-eye-slash' : 'bi bi-eye';
    });
}

// Loading state on submit
const loginForm = document.getElementById('loginForm');
if (loginForm) {
    loginForm.addEventListener('submit', () => {
        document.getElementById('loginBtnText').classList.add('d-none');
        document.getElementById('loginBtnLoading').classList.remove('d-none');
        document.getElementById('loginSubmitBtn').disabled = true;
    });
}

// Register PWA Service Worker
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').catch(err => {
            console.debug('SW Registration failed:', err);
        });
    });
}
</script>

</body>
</html>
