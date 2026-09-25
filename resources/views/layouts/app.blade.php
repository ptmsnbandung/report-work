<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') | PT MSN</title>

    <!-- Favicon & PWA Primary Tags -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#07152b">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="MSN Report">
    <meta name="application-name" content="MSN Report">
    <meta name="msapplication-TileColor" content="#07152b">
    <link rel="icon" type="image/png" href="{{ asset('assets/logo-msn BG Trans - Copy2.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/logo-msn BG Trans - Copy2.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/icon-512.png') }}">

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 CSS (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css" />

    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <!-- Custom Corporate CSS -->
    <link rel="stylesheet" href="{{ asset('css/connecti-custom.css') }}?v={{ file_exists(public_path('css/connecti-custom.css')) ? filemtime(public_path('css/connecti-custom.css')) : time() }}">

    <style>
        /* ── CORPORATE SWEETALERT2 STYLING (PT MSN THEME) ── */
        .swal2-popup.msn-swal-popup {
            border-radius: 18px !important;
            padding: 1.5rem 1.4rem 1.25rem !important;
            background: #ffffff !important;
            border: 1px solid rgba(15, 23, 42, 0.1) !important;
            box-shadow: 0 20px 45px rgba(15, 23, 42, 0.2), 0 4px 12px rgba(15, 23, 42, 0.08) !important;
            font-family: 'Inter', system-ui, -apple-system, sans-serif !important;
            max-width: 440px !important;
            width: calc(100% - 32px) !important;
        }
        .swal2-popup.msn-swal-popup .swal2-title {
            font-size: 1.15rem !important;
            font-weight: 700 !important;
            color: #0f172a !important;
            padding: 0 0 0.35rem 0 !important;
            letter-spacing: -0.2px !important;
        }
        .swal2-popup.msn-swal-popup .swal2-html-container {
            font-size: 0.88rem !important;
            color: #475569 !important;
            line-height: 1.5 !important;
            margin: 0.35rem 0 1.25rem 0 !important;
        }
        .swal2-popup.msn-swal-popup .swal2-actions {
            gap: 0.6rem !important;
            width: 100% !important;
            margin: 0.4rem 0 0 0 !important;
            display: flex !important;
            justify-content: flex-end !important;
        }
        .swal2-popup.msn-swal-popup .swal2-confirm,
        .swal2-popup.msn-swal-popup .swal2-cancel {
            border-radius: 999px !important;
            padding: 0.52rem 1.35rem !important;
            font-size: 0.84rem !important;
            font-weight: 600 !important;
            box-shadow: none !important;
            transition: all 0.18s ease !important;
        }
        .swal2-popup.msn-swal-popup .swal2-confirm:hover,
        .swal2-popup.msn-swal-popup .swal2-cancel:hover {
            transform: translateY(-1px) !important;
        }
        .swal2-icon {
            transform: scale(0.85) !important;
            margin: 0.5rem auto 0.5rem auto !important;
        }

        /* ── PHONE MOCKUP FRAME (APP PREVIEW) ── */
        .phone-mockup-frame {
            width: 52px;
            height: 98px;
            background: #0b1329;
            border: 2.5px solid #475569;
            border-radius: 13px;
            position: relative;
            padding: 2.5px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.45), 0 0 0 1px rgba(255, 255, 255, 0.15);
            display: flex;
            flex-direction: column;
            align-items: center;
            flex-shrink: 0;
            box-sizing: border-box;
        }
        .phone-mockup-frame .phone-notch {
            width: 18px;
            height: 3.5px;
            background: #0b1329;
            border-radius: 0 0 3px 3px;
            position: absolute;
            top: 2.5px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 4;
        }
        .phone-mockup-frame .phone-screen-inner {
            width: 100%;
            height: 100%;
            background: #ffffff;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            padding: 3px;
            box-sizing: border-box;
        }
        .phone-mockup-frame .phone-app-img {
            width: 100%;
            height: auto;
            max-height: 82%;
            object-fit: contain;
            display: block;
        }
        .phone-mockup-frame .phone-bar {
            width: 14px;
            height: 2px;
            background: #0b1329;
            border-radius: 2px;
            position: absolute;
            bottom: 4px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 4;
            opacity: 0.5;
        }

        /* Large Phone Mockup (for Dashboard Banner) */
        .phone-mockup-frame.lg {
            width: 64px;
            height: 120px;
            border: 3px solid #64748b;
            border-radius: 16px;
            padding: 3px;
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(255, 255, 255, 0.2);
        }
        .phone-mockup-frame.lg .phone-notch {
            width: 24px;
            height: 4.5px;
            border-radius: 0 0 4px 4px;
            top: 3px;
        }
        .phone-mockup-frame.lg .phone-screen-inner {
            border-radius: 12px;
            padding: 5px;
        }
        /* Mobile bottom offset for floating prompts to avoid overlapping bottom navigation bar */
        @media (max-width: 991.98px) {
            #webPushPromptBanner,
            #pwaInstallBanner {
                bottom: calc(74px + env(safe-area-inset-bottom, 0px)) !important;
                left: 10px !important;
                right: 10px !important;
                max-width: calc(100% - 20px) !important;
                width: calc(100% - 20px) !important;
            }
        }

        /* ═══════════════════════════════════════════════════════════════════
           PT MSN SIGNATURE LOGO SPINNER LOADER (GLOBAL OVERLAY)
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

        @media (max-width: 380px) {
            #webPushPromptBanner,
            #pwaInstallBanner {
                bottom: calc(66px + env(safe-area-inset-bottom, 0px)) !important;
                left: 6px !important;
                right: 6px !important;
                max-width: calc(100% - 12px) !important;
                width: calc(100% - 12px) !important;
            }
        }
    </style>

    @stack('styles')
@php
    $isAdminView = auth()->check() && auth()->user()->hasRole('admin');
@endphp
<body class="{{ $isAdminView ? 'is-admin-view' : '' }}">
    <!-- ════ PT MSN SIGNATURE LOGO PRELOADER ════ -->
    <div id="msnGlobalPreloader" class="msn-loader-backdrop">
        <div class="msn-logo-loader-container">
            <div class="msn-loader-ring-pulse"></div>
            <div class="msn-loader-ring"></div>
            <div class="msn-loader-logo-wrap">
                <img src="{{ asset('assets/logo-msn BG Trans - Copy2.png') }}" alt="PT MSN" class="msn-loader-logo-img" style="width: 52px; height: 52px; max-width: 52px; max-height: 52px; object-fit: contain;">
            </div>
        </div>
        <div class="msn-loader-text" id="msnGlobalLoaderText">
            <span id="msnLoaderMsg">Memuat Halaman</span>
            <span class="msn-loader-dots"><span>.</span><span>.</span><span>.</span></span>
        </div>
    </div>

    <!-- Sidebar Overlay for Mobile Drawer -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="app-wrapper">
        <!-- Sidebar Navigation -->
        @include('layouts.sidebar')

        <!-- Main Content Area -->
        <div class="app-main">
            <!-- Topbar Header -->
            @include('layouts.topbar')

            <!-- Page Content -->
            <main class="app-content">
                @yield('content')
            </main>

            <!-- Footer (Hidden on Mobile) -->
            <footer class="app-footer d-none d-md-flex justify-content-between align-items-center">
                <div>
                    <strong>&copy; {{ date('Y') }} PT MSN</strong> &bull; Sistem Tiketing Gangguan Backbone
                </div>
                <div class="small text-muted mt-2 mt-md-0">
                    Versi 1.0 (Fase 2 - Modul Tiket) &bull; Timezone: Asia/Jakarta
                </div>
            </footer>
        </div>
    </div>

    <!-- Mobile Bottom Quick Nav (Non-Admin only) -->
    @if(!$isAdminView)
        @include('layouts.bottom-nav')
    @endif

    <!-- Global Toast Notifications -->
    @include('components.toast')

    <!-- Global Confirm Modal -->
    @include('components.confirm-modal')

    <!-- Logout Confirmation Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true" style="z-index: 1065;">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content rounded-4 shadow-lg border-0">
                <div class="modal-body text-center p-4">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle" style="width:52px; height:52px; background:#fee2e2; color:#ef4444; font-size:1.35rem;">
                        <i class="bi bi-box-arrow-right"></i>
                    </div>
                    <h6 class="fw-bold mb-1 text-dark" style="font-size:1rem;">Konfirmasi Logout</h6>
                    <p class="text-muted small mb-4" style="font-size:0.82rem;">Apakah Anda yakin ingin keluar dari sistem?</p>
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-light btn-sm px-3 rounded-pill fw-semibold flex-grow-1" data-bs-dismiss="modal">Batal</button>
                        <form method="POST" action="{{ route('logout') }}" class="flex-grow-1">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-sm px-3 rounded-pill fw-semibold w-100">Keluar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 Bundle JS (with Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <!-- SweetAlert2 (Enterprise Confirmation & Notification Dialogs) -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Chart.js & Leaflet & Flatpickr -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>

    <!-- App Common Scripts -->
    <script>
        // ── PT MSN SIGNATURE LOGO LOADER CONTROLLER ──
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

        // Otomatis hilangkan loader saat halaman selesai dimuat
        window.addEventListener('load', function() {
            setTimeout(window.hideMsnLoader, 120);
        });
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(window.hideMsnLoader, 250);
        });
        // Batas waktu pengaman (maksimal 1.5 detik)
        setTimeout(window.hideMsnLoader, 1500);

        // Tampilkan loader saat navigasi internal antar halaman (sidebar / menu / link)
        document.addEventListener('click', function(e) {
            const link = e.target.closest('a[href]:not([target="_blank"]):not([href^="#"]):not([href^="javascript:"]):not([download]):not(.no-loader)');
            if (link && link.href && !e.ctrlKey && !e.metaKey && !e.shiftKey && !e.defaultPrevented) {
                try {
                    const url = new URL(link.href, window.location.origin);
                    if (url.origin === window.location.origin && url.pathname !== window.location.pathname) {
                        window.showMsnLoader('Memuat Halaman...');
                    }
                } catch(err) {}
            }
        });

        // ── GLOBAL ENTERPRISE CONFIRMATION DIALOG (SWEETALERT2) ──
        document.addEventListener('submit', function (e) {
            const form = e.target;
            if (form.dataset.swalBypassed === 'true') {
                delete form.dataset.swalBypassed;
                return;
            }

            const onsubmitAttr = form.getAttribute('onsubmit');
            const dataConfirm = form.getAttribute('data-confirm');

            let confirmMsg = null;
            let isDestructive = false;
            let isResumeClock = false;
            let confirmTitle = 'Konfirmasi Tindakan';
            let confirmBtnText = 'Ya, Lanjutkan';
            let confirmIcon = 'question';

            if (onsubmitAttr && onsubmitAttr.includes('confirm(')) {
                const match = onsubmitAttr.match(/confirm\(\s*['"](.+?)['"]\s*\)/);
                if (match && match[1]) {
                    confirmMsg = match[1];
                }
                form.removeAttribute('onsubmit');
            } else if (dataConfirm) {
                confirmMsg = dataConfirm;
                if (form.getAttribute('data-confirm-title')) confirmTitle = form.getAttribute('data-confirm-title');
                if (form.getAttribute('data-confirm-btn')) confirmBtnText = form.getAttribute('data-confirm-btn');
            }

            if (confirmMsg) {
                e.preventDefault();
                e.stopPropagation();

                const lower = confirmMsg.toLowerCase();
                if (lower.includes('hapus') || lower.includes('delete')) {
                    isDestructive = true;
                    confirmTitle = 'Konfirmasi Hapus Data';
                    confirmBtnText = '<i class="bi bi-trash3-fill me-1"></i> Ya, Hapus';
                    confirmIcon = 'warning';
                } else if (lower.includes('resume') || lower.includes('lanjutkan') || lower.includes('sla')) {
                    isResumeClock = true;
                    confirmTitle = 'Lanjutkan Perhitungan SLA?';
                    confirmBtnText = '<i class="bi bi-play-fill me-1"></i> Ya, Lanjutkan SLA';
                    confirmIcon = 'info';
                }

                Swal.fire({
                    title: confirmTitle,
                    html: `<div style="font-size: 0.9rem; color: #475569;">${confirmMsg}</div>`,
                    icon: confirmIcon,
                    showCancelButton: true,
                    confirmButtonText: confirmBtnText,
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    focusCancel: true,
                    customClass: {
                        popup: 'msn-swal-popup',
                        confirmButton: `btn ${isDestructive ? 'btn-danger' : (isResumeClock ? 'btn-success' : 'btn-primary')} rounded-pill px-4 py-2 fw-semibold`,
                        cancelButton: 'btn btn-light border rounded-pill px-4 py-2 fw-semibold text-secondary'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.dataset.swalBypassed = 'true';
                        form.submit();
                    }
                });
            }
        }, true);

        document.addEventListener('DOMContentLoaded', function () {
            // Ensure any modals defined inside page components are direct children of document.body
            // to avoid z-index/stacking context issues with nested parent wrappers
            document.querySelectorAll('.modal').forEach(function (modalEl) {
                if (modalEl.parentElement !== document.body) {
                    document.body.appendChild(modalEl);
                }
            });

            // Initialize All Tooltips
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            [...tooltipTriggerList].map(el => new bootstrap.Tooltip(el, { boundary: 'window' }));

            // Mobile Sidebar Toggle
            const sidebarToggle   = document.getElementById('sidebarToggle');
            const sidebarCloseBtn = document.getElementById('sidebarCloseBtn');
            const appSidebar      = document.getElementById('appSidebar');
            const sidebarOverlay  = document.getElementById('sidebarOverlay');

            if (sidebarToggle && appSidebar && sidebarOverlay) {
                sidebarToggle.addEventListener('click', () => {
                    appSidebar.classList.toggle('show');
                    sidebarOverlay.classList.toggle('show');
                });
                sidebarOverlay.addEventListener('click', () => {
                    appSidebar.classList.remove('show');
                    sidebarOverlay.classList.remove('show');
                });
            }

            if (sidebarCloseBtn && appSidebar && sidebarOverlay) {
                sidebarCloseBtn.addEventListener('click', () => {
                    appSidebar.classList.remove('show');
                    sidebarOverlay.classList.remove('show');
                });
            }

            // Live Clock (WIB)
            function updateClock() {
                const clockEl = document.getElementById('liveClock');
                if (!clockEl) return;
                const now     = new Date();
                const hh      = String(now.getHours()).padStart(2, '0');
                const mm      = String(now.getMinutes()).padStart(2, '0');
                const ss      = String(now.getSeconds()).padStart(2, '0');
                clockEl.textContent = `${hh}:${mm}:${ss} WIB`;
            }
            setInterval(updateClock, 1000);
            updateClock();

            // Auto-hide alerts after 5 seconds
            document.querySelectorAll('.alert.alert-dismissible').forEach(el => {
                setTimeout(() => {
                    const bsAlert = bootstrap.Alert.getOrCreateInstance(el);
                    if (bsAlert) bsAlert.close();
                }, 5000);
            });

            // Notification Real-Time Polling (every 30 seconds)
            function pollNotifications() {
                fetch('{{ route('notifications.unread_json') }}', {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(res => res.json())
                .then(data => {
                    const badge = document.getElementById('topbarNotifBadge');
                    const labelBadge = document.getElementById('topbarUnreadLabelBadge');
                    if (badge) {
                        if (data.unread_count > 0) {
                            badge.textContent = data.unread_count > 99 ? '99+' : data.unread_count;
                            badge.classList.remove('d-none');
                        } else {
                            badge.classList.add('d-none');
                        }
                    }
                    if (labelBadge) {
                        labelBadge.textContent = `${data.unread_count} Baru`;
                    }
                })
                .catch(() => {});
            }
            setInterval(pollNotifications, 30000);

            // ── DEVICE PERMISSION ENGINE (NOTIFIKASI, MAPS GPS, & KAMERA) ──
            const REPROMPT_COOLDOWN_MS = 2.5 * 60 * 1000; // 2.5 Menit (minta izin lagi setelah beberapa saat)
            let repromptTimer = null;

            function urlBase64ToUint8Array(base64String) {
                const padding = '='.repeat((4 - base64String.length % 4) % 4);
                const base64 = (base64String + padding).replace(/\-/g, '+').replace(/_/g, '/');
                const rawData = window.atob(base64);
                const outputArray = new Uint8Array(rawData.length);
                for (let i = 0; i < rawData.length; ++i) {
                    outputArray[i] = rawData.charCodeAt(i);
                }
                return outputArray;
            }

            async function checkAllPermissionsState() {
                const state = {
                    notification: ('Notification' in window) ? Notification.permission : 'unsupported',
                    geolocation: 'prompt',
                    camera: 'prompt'
                };

                // Check Geolocation Permission
                if ('permissions' in navigator && navigator.permissions.query) {
                    try {
                        const geoStatus = await navigator.permissions.query({ name: 'geolocation' });
                        state.geolocation = geoStatus.state; // 'granted', 'prompt', 'denied'
                    } catch(e) {
                        state.geolocation = localStorage.getItem('perm_geo_granted') === '1' ? 'granted' : 'prompt';
                    }
                } else if (localStorage.getItem('perm_geo_granted') === '1') {
                    state.geolocation = 'granted';
                }

                // Check Camera Permission
                if ('permissions' in navigator && navigator.permissions.query) {
                    try {
                        const camStatus = await navigator.permissions.query({ name: 'camera' });
                        state.camera = camStatus.state; // 'granted', 'prompt', 'denied'
                    } catch(e) {
                        state.camera = localStorage.getItem('perm_cam_granted') === '1' ? 'granted' : 'prompt';
                    }
                } else if (localStorage.getItem('perm_cam_granted') === '1') {
                    state.camera = 'granted';
                }

                // Update UI Badges
                updatePermBadge('badgePermNotif', state.notification);
                updatePermBadge('badgePermGeo', state.geolocation);
                updatePermBadge('badgePermCam', state.camera);

                const hasPending = (state.notification !== 'granted') || (state.geolocation !== 'granted') || (state.camera !== 'granted');
                return { state, hasPending };
            }

            function updatePermBadge(badgeId, status) {
                const el = document.getElementById(badgeId);
                if (!el) return;
                if (status === 'granted') {
                    el.style.background = 'rgba(34, 197, 94, 0.2)';
                    el.style.borderColor = 'rgba(34, 197, 94, 0.45)';
                    el.style.color = '#86efac';
                    const dot = el.querySelector('.perm-dot');
                    if (dot) {
                        dot.className = 'perm-dot text-success';
                        dot.innerHTML = '✓';
                    }
                } else {
                    el.style.background = 'rgba(255, 255, 255, 0.08)';
                    el.style.borderColor = 'rgba(255, 255, 255, 0.15)';
                    el.style.color = '#ffffff';
                    const dot = el.querySelector('.perm-dot');
                    if (dot) {
                        dot.className = 'perm-dot text-warning';
                        dot.innerHTML = '•';
                    }
                }
            }

            async function evaluateAndDisplayPermissionBanner() {
                const banner = document.getElementById('webPushPromptBanner');
                if (!banner) return;

                const { state, hasPending } = await checkAllPermissionsState();

                // If all permissions granted, hide banner
                if (!hasPending) {
                    banner.classList.add('d-none');
                    return;
                }

                // Check cooldown timer
                const dismissedAt = localStorage.getItem('app_perms_dismissed_at');
                const now = Date.now();
                if (dismissedAt && (now - Number(dismissedAt) < REPROMPT_COOLDOWN_MS)) {
                    // Masih dalam cooldown, jangan tampilkan dulu tapi set timer untuk muncul nanti
                    const remainingMs = REPROMPT_COOLDOWN_MS - (now - Number(dismissedAt));
                    clearTimeout(repromptTimer);
                    repromptTimer = setTimeout(() => {
                        evaluateAndDisplayPermissionBanner();
                    }, Math.max(remainingMs, 5000));
                    return;
                }

                // Tampilkan banner
                banner.classList.remove('d-none');
            }

            function dismissPermissionsBanner() {
                const banner = document.getElementById('webPushPromptBanner');
                if (banner) banner.classList.add('d-none');
                
                // Simpan timestamp kapan di-dismiss
                localStorage.setItem('app_perms_dismissed_at', Date.now().toString());

                // Minta izin lagi setelah beberapa saat (cooldown 2.5 menit)
                clearTimeout(repromptTimer);
                repromptTimer = setTimeout(() => {
                    evaluateAndDisplayPermissionBanner();
                }, REPROMPT_COOLDOWN_MS);
            }

            async function subscribeDeviceToPush(registration) {
                try {
                    const reg = registration || await navigator.serviceWorker.ready;
                    const res = await fetch('{{ route('push.vapid') }}');
                    const data = await res.json();
                    if (!data.public_key) return;

                    const convertedKey = urlBase64ToUint8Array(data.public_key);
                    const sub = await reg.pushManager.subscribe({
                        userVisibleOnly: true,
                        applicationServerKey: convertedKey
                    });

                    const isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
                    const isMobileDevice = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) || (navigator.userAgentData && navigator.userAgentData.mobile);
                    const deviceType = isStandalone ? 'pwa' : (isMobileDevice ? 'mobile' : 'desktop');

                    const subPayload = sub.toJSON();
                    subPayload.device_type = deviceType;
                    subPayload.is_mobile = isMobileDevice || isStandalone;

                    await fetch('{{ route('push.subscribe') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(subPayload)
                    });
                } catch (err) {
                    console.warn('Subscription error:', err);
                }
            }

            window.enableAllAppPermissions = async function() {
                const btn = document.getElementById('btnEnableWebPush');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" style="width:0.75rem;height:0.75rem;"></span> Memproses...';
                }

                // 1. Minta Izin Notifikasi Web Push
                if ('Notification' in window) {
                    try {
                        const notifPerm = await Notification.requestPermission();
                        if (notifPerm === 'granted') {
                            const reg = await navigator.serviceWorker.ready;
                            await subscribeDeviceToPush(reg);
                        }
                    } catch(e) {
                        console.warn('Notif perm request error:', e);
                    }
                }

                // 2. Minta Izin Lokasi GPS
                if (navigator.geolocation) {
                    try {
                        await new Promise((resolve) => {
                            navigator.geolocation.getCurrentPosition(
                                (pos) => {
                                    localStorage.setItem('perm_geo_granted', '1');
                                    resolve(true);
                                },
                                (err) => {
                                    console.warn('Geo perm error:', err);
                                    resolve(false);
                                },
                                { enableHighAccuracy: true, timeout: 6000 }
                            );
                        });
                    } catch(e) {}
                }

                // 3. Minta Izin Kamera
                if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                    try {
                        const stream = await navigator.mediaDevices.getUserMedia({ video: true });
                        localStorage.setItem('perm_cam_granted', '1');
                        // Matikan kamera segera setelah izin didapat
                        stream.getTracks().forEach(track => track.stop());
                    } catch(e) {
                        console.warn('Camera perm error:', e);
                    }
                }

                // Update UI
                const { hasPending } = await checkAllPermissionsState();
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bi bi-check-circle-fill"></i> Izinkan Semua';
                }

                if (!hasPending) {
                    const banner = document.getElementById('webPushPromptBanner');
                    if (banner) banner.classList.add('d-none');
                    localStorage.removeItem('app_perms_dismissed_at');
                    alert('✅ Semua izin perangkat (Notifikasi, Lokasi GPS, Kamera) berhasil diaktifkan!');
                } else {
                    dismissPermissionsBanner();
                }
            };

            async function registerServiceWorkerAndPush() {
                if (!('serviceWorker' in navigator)) return;

                try {
                    const reg = await navigator.serviceWorker.register('/sw.js');
                    
                    @auth
                    if ('PushManager' in window && Notification.permission === 'granted') {
                        subscribeDeviceToPush(reg);
                    }
                    evaluateAndDisplayPermissionBanner();
                    @endauth
                } catch (e) {
                    console.debug('ServiceWorker registration note:', e);
                }
            }

            document.getElementById('btnEnableWebPush')?.addEventListener('click', window.enableAllAppPermissions);
            document.getElementById('btnDismissWebPush')?.addEventListener('click', dismissPermissionsBanner);
            document.getElementById('btnCloseWebPush')?.addEventListener('click', dismissPermissionsBanner);

            // Periodically re-evaluate permissions every 45s while on page
            @auth
            setInterval(evaluateAndDisplayPermissionBanner, 45000);
            @endauth

            // Always register Service Worker for PWA caching & offline support
            registerServiceWorkerAndPush();

            // ── PWA INSTALLATION STATE & PROMPT HANDLER (100% Native & Legal) ──
            let deferredPwaPrompt = null;
            const pwaInstallBanner = document.getElementById('pwaInstallBanner');
            const btnPwaInstallAction = document.getElementById('btnPwaInstallAction');
            const btnDismissPwa = document.getElementById('btnDismissPwa');
            const btnClosePwaBanner = document.getElementById('btnClosePwaBanner');

            // Function to check if app is already installed and hide all install triggers
            window.applyPwaInstalledState = function() {
                const isStandalone = window.matchMedia('(display-mode: standalone)').matches 
                    || window.navigator.standalone === true 
                    || document.referrer.includes('android-app://')
                    || localStorage.getItem('pwa_installed') === '1';

                if (isStandalone) {
                    document.querySelectorAll('.pwa-download-dash-card, #pwaInstallBanner, .btn-trigger-pwa-install').forEach(el => {
                        el.classList.add('d-none');
                        el.style.display = 'none';
                    });
                }
                return isStandalone;
            };

            const isAlreadyPwa = window.applyPwaInstalledState();

            window.triggerPwaInstall = async function() {
                if (deferredPwaPrompt) {
                    deferredPwaPrompt.prompt();
                    const choiceResult = await deferredPwaPrompt.userChoice;
                    if (choiceResult.outcome === 'accepted') {
                        localStorage.setItem('pwa_installed', '1');
                        window.applyPwaInstalledState();
                        console.log('User accepted PWA installation');
                    }
                    deferredPwaPrompt = null;
                } else {
                    const isIos = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
                    if (isIos) {
                        alert('📲 CARA PASANG DI IPHONE / IPAD:\n1. Buka halaman ini di browser Safari.\n2. Tekan tombol Share / Bagikan (ikon kotak dengan panah ke atas).\n3. Pilih "Tambahkan ke Layar Utama" (Add to Home Screen).\n4. Tekan "Tambah".');
                    } else {
                        alert('📲 CARA PASANG DI ANDROID / PC:\n1. Tekan menu browser (titik tiga ⋮ di pojok kanan atas).\n2. Pilih "Instal Aplikasi" atau "Tambahkan ke Layar Utama".\n3. Konfirmasi pemasangan.');
                    }
                }
            };

            if (!isAlreadyPwa) {
                window.addEventListener('beforeinstallprompt', (e) => {
                    e.preventDefault();
                    deferredPwaPrompt = e;

                    if (pwaInstallBanner && !sessionStorage.getItem('dismiss_pwa_banner') && !window.applyPwaInstalledState()) {
                        pwaInstallBanner.classList.remove('d-none');
                    }
                });

                if (btnPwaInstallAction) {
                    btnPwaInstallAction.addEventListener('click', window.triggerPwaInstall);
                }

                const closeOrDismiss = () => {
                    if (pwaInstallBanner) pwaInstallBanner.classList.add('d-none');
                    sessionStorage.setItem('dismiss_pwa_banner', '1');
                };

                if (btnDismissPwa) btnDismissPwa.addEventListener('click', closeOrDismiss);
                if (btnClosePwaBanner) btnClosePwaBanner.addEventListener('click', closeOrDismiss);

                document.querySelectorAll('.btn-trigger-pwa-install').forEach(btn => {
                    btn.addEventListener('click', window.triggerPwaInstall);
                });
            }

            window.addEventListener('appinstalled', () => {
                localStorage.setItem('pwa_installed', '1');
                window.applyPwaInstalledState();
                deferredPwaPrompt = null;
                console.log('PT MSN PWA successfully installed!');
            });

            // Smooth transition feedback on Report navigation tabs
            const reportNavItems = document.querySelectorAll('.report-nav-item');
            reportNavItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    if (this.classList.contains('active') || e.ctrlKey || e.metaKey || e.shiftKey) return;
                    
                    reportNavItems.forEach(nav => nav.classList.remove('active'));
                    this.classList.add('active');
                    
                    const mainContent = document.querySelector('.app-content');
                    if (mainContent) {
                        mainContent.style.transition = 'opacity 0.18s ease, transform 0.18s ease';
                        mainContent.style.opacity = '0.72';
                        mainContent.style.transform = 'translateY(2px)';
                    }
                });
            });
        });
    </script>

    <!-- PWA Install Floating Banner (100% Safe, Legal & No Security Warnings) -->
    <div id="pwaInstallBanner" class="d-none position-fixed bottom-0 start-0 p-3" style="z-index: 1095; max-width: 410px; width: calc(100% - 24px);">
        <div class="card border-0 shadow-lg rounded-4 overflow-hidden position-relative" style="background: linear-gradient(135deg, #07152b 0%, #0d2757 100%); color: #fff; border: 1px solid rgba(56, 189, 248, 0.35) !important; box-shadow: 0 16px 36px rgba(0,0,0,0.55) !important;">
            <!-- Close Button -->
            <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-2.5 p-1" id="btnClosePwaBanner" aria-label="Tutup" style="font-size: 0.65rem; opacity: 0.7; z-index: 5;"></button>
            
            <div class="card-body p-3.5">
                <div class="d-flex align-items-center gap-3">
                    <!-- Phone Mockup Frame with Logo -->
                    <div class="phone-mockup-frame">
                        <div class="phone-notch"></div>
                        <div class="phone-screen-inner">
                            <img src="{{ asset('assets/work-report.png') }}" alt="MSN Work Report" class="phone-app-img">
                        </div>
                        <div class="phone-bar"></div>
                    </div>
                    <div class="flex-grow-1 pe-2">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <h6 class="fw-bold mb-0 text-white" style="font-size: 0.92rem; letter-spacing: -0.2px;">Install Aplikasi PT MSN</h6>
                            <span class="badge" style="background: rgba(56, 189, 248, 0.18); color: #38bdf8; border: 1px solid rgba(56, 189, 248, 0.35); font-size: 0.62rem; padding: 2px 6px; font-weight: 700;">PWA App</span>
                        </div>
                        <p class="text-white-50 mb-2.5" style="font-size: 0.75rem; line-height: 1.35; margin-bottom: 0.7rem !important;">
                            Pasang di HP / Desktop untuk akses instan full-screen, cepat, &amp; tanpa bar browser.
                        </p>
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 py-1.5 fw-bold shadow-sm d-inline-flex align-items-center gap-1.5" id="btnPwaInstallAction" style="font-size: 0.76rem; background: linear-gradient(135deg, #2C7FFF 0%, #0052cc 100%); border: none; letter-spacing: 0.2px;">
                                <i class="bi bi-download"></i> Pasang Aplikasi
                            </button>
                            <button type="button" class="btn btn-sm rounded-pill px-3 py-1.5 text-white-50 text-nowrap" id="btnDismissPwa" style="font-size: 0.76rem; background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15);">
                                Nanti
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Device Permissions & Web Push Floating Prompt (Notifikasi, Maps GPS, & Kamera) -->
    @auth
    <div id="webPushPromptBanner" class="d-none position-fixed bottom-0 end-0 p-3" style="z-index: 1090; max-width: 410px; width: calc(100% - 24px);">
        <div class="card border-0 shadow-lg rounded-4 overflow-hidden position-relative" style="background: linear-gradient(135deg, #07152b 0%, #0d2757 100%); color: #fff; border: 1px solid rgba(56, 189, 248, 0.35) !important; box-shadow: 0 16px 36px rgba(0,0,0,0.55) !important;">
            <!-- Close Button -->
            <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-2.5 p-1" id="btnCloseWebPush" aria-label="Tutup" style="font-size: 0.65rem; opacity: 0.75; z-index: 5;"></button>
            
            <div class="card-body p-3.5">
                <div class="d-flex align-items-start gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 42px; height: 42px; background: rgba(44, 127, 255, 0.25); color: #38bdf8; font-size: 1.25rem; border: 1px solid rgba(56, 189, 248, 0.3);">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <div class="flex-grow-1 pe-2">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <h6 class="fw-bold mb-0 text-white" style="font-size: 0.92rem; color: #ffffff !important; letter-spacing: -0.2px;">Izin Akses Aplikasi</h6>
                            <span class="badge" id="permStatusBadge" style="background: rgba(245, 158, 11, 0.2); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.4); font-size: 0.6rem; padding: 2px 6px; font-weight: 700;">Diperlukan</span>
                        </div>
                        <p class="text-white-50 mb-2" style="font-size: 0.75rem; line-height: 1.35; margin-bottom: 0.55rem !important;">
                            Aktifkan izin untuk notifikasi tiket, penandaan lokasi GPS lapangan, dan foto dokumentasi:
                        </p>

                        <!-- Chips status izin per fitur -->
                        <div class="d-flex flex-wrap gap-1.5 mb-2.5" id="permChipsContainer">
                            <span class="badge d-inline-flex align-items-center gap-1 py-1 px-2 rounded-pill" id="badgePermNotif" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); font-size: 0.69rem; font-weight: 500;">
                                <i class="bi bi-bell"></i> Notifikasi <span class="perm-dot text-warning">•</span>
                            </span>
                            <span class="badge d-inline-flex align-items-center gap-1 py-1 px-2 rounded-pill" id="badgePermGeo" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); font-size: 0.69rem; font-weight: 500;">
                                <i class="bi bi-geo-alt"></i> Lokasi GPS <span class="perm-dot text-warning">•</span>
                            </span>
                            <span class="badge d-inline-flex align-items-center gap-1 py-1 px-2 rounded-pill" id="badgePermCam" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); font-size: 0.69rem; font-weight: 500;">
                                <i class="bi bi-camera"></i> Kamera <span class="perm-dot text-warning">•</span>
                            </span>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 py-1.5 fw-bold shadow-sm d-inline-flex align-items-center gap-1.5" id="btnEnableWebPush" style="font-size: 0.76rem; background: linear-gradient(135deg, #2C7FFF 0%, #1b39da 100%); border: none;">
                                <i class="bi bi-check-circle-fill"></i> Izinkan Semua
                            </button>
                            <button type="button" class="btn btn-sm rounded-pill px-3 py-1.5 text-white-50 text-nowrap" id="btnDismissWebPush" style="font-size: 0.76rem; background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15);">
                                Nanti Saja
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endauth

    @stack('scripts')
</body>
</html>
