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
    <link rel="apple-touch-icon" href="{{ asset('assets/work-report.png') }}">

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

    @stack('styles')
@php
    $isAdminView = auth()->check() && auth()->user()->hasRole('admin');
@endphp
<body class="{{ $isAdminView ? 'is-admin-view' : '' }}">
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

    <!-- Chart.js & Leaflet & Flatpickr -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>

    <!-- App Common Scripts -->
    <script>
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

            // ── WEB PUSH NOTIFICATION REGISTRATION (VAPID / GOOGLE FCM) ──
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

            async function registerServiceWorkerAndPush() {
                if (!('serviceWorker' in navigator)) return;

                try {
                    const reg = await navigator.serviceWorker.register('/sw.js');
                    
                    @auth
                    if ('PushManager' in window) {
                        if (Notification.permission === 'granted') {
                            subscribeDeviceToPush(reg);
                        } else if (Notification.permission === 'default') {
                            const pushBanner = document.getElementById('webPushPromptBanner');
                            if (pushBanner && !sessionStorage.getItem('dismiss_push_prompt')) {
                                pushBanner.classList.remove('d-none');
                            }
                        }
                    }
                    @endauth
                } catch (e) {
                    console.debug('ServiceWorker registration note:', e);
                }
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

                    await fetch('{{ route('push.subscribe') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(sub.toJSON())
                    });

                    const promptBanner = document.getElementById('webPushPromptBanner');
                    if (promptBanner) promptBanner.classList.add('d-none');
                } catch (err) {
                    console.warn('Subscription error:', err);
                }
            }

            window.enableWebPush = async function() {
                if (!('Notification' in window)) {
                    alert('Browser Anda tidak mendukung notifikasi Web Push.');
                    return;
                }
                const perm = await Notification.requestPermission();
                if (perm === 'granted') {
                    const reg = await navigator.serviceWorker.ready;
                    await subscribeDeviceToPush(reg);
                    alert('Notifikasi HP berhasil diaktifkan! Anda akan menerima update tiket langsung di perangkat ini.');
                } else {
                    alert('Izin notifikasi ditolak. Anda dapat mengaktifkannya lewat pengaturan browser (ikon gembok di URL bar).');
                }
            };

            document.getElementById('btnEnableWebPush')?.addEventListener('click', window.enableWebPush);
            document.getElementById('btnDismissWebPush')?.addEventListener('click', function() {
                document.getElementById('webPushPromptBanner')?.classList.add('d-none');
                sessionStorage.setItem('dismiss_push_prompt', '1');
            });

            // Always register Service Worker for PWA caching & offline support
            registerServiceWorkerAndPush();

            // ── PWA INSTALLATION PROMPT HANDLER (100% Native & Legal) ──
            let deferredPwaPrompt = null;
            const pwaInstallBanner = document.getElementById('pwaInstallBanner');
            const btnPwaInstallAction = document.getElementById('btnPwaInstallAction');
            const btnDismissPwa = document.getElementById('btnDismissPwa');

            // Check if app is already running in standalone PWA mode
            const isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;

            if (!isStandalone) {
                window.addEventListener('beforeinstallprompt', (e) => {
                    // Prevent default mini-infobar
                    e.preventDefault();
                    deferredPwaPrompt = e;

                    // Show custom beautiful PWA install banner if not dismissed this session
                    if (pwaInstallBanner && !sessionStorage.getItem('dismiss_pwa_banner')) {
                        pwaInstallBanner.classList.remove('d-none');
                    }
                });

                if (btnPwaInstallAction) {
                    btnPwaInstallAction.addEventListener('click', async () => {
                        if (!deferredPwaPrompt) {
                            // Fallback instruction for browsers where prompt isn't directly triggerable
                            alert('Untuk memasang di HP:\n1. Tekan tombol menu browser (titik tiga ⋮ atau Bagikan/Share)\n2. Pilih "Tambahkan ke Layar Utama" / "Install App"');
                            return;
                        }
                        deferredPwaPrompt.prompt();
                        const choiceResult = await deferredPwaPrompt.userChoice;
                        if (choiceResult.outcome === 'accepted') {
                            console.log('User installed the PWA');
                            if (pwaInstallBanner) pwaInstallBanner.classList.add('d-none');
                        }
                        deferredPwaPrompt = null;
                    });
                }

                if (btnDismissPwa) {
                    btnDismissPwa.addEventListener('click', () => {
                        if (pwaInstallBanner) pwaInstallBanner.classList.add('d-none');
                        sessionStorage.setItem('dismiss_pwa_banner', '1');
                    });
                }
            }

            window.addEventListener('appinstalled', () => {
                if (pwaInstallBanner) pwaInstallBanner.classList.add('d-none');
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
    <div id="pwaInstallBanner" class="d-none position-fixed bottom-0 start-0 p-3" style="z-index: 1095; max-width: 380px;">
        <div class="card border-0 shadow-lg rounded-4 overflow-hidden" style="background: linear-gradient(135deg, #07152b 0%, #0d2757 100%); color: #fff; border: 1px solid rgba(56, 189, 248, 0.28); box-shadow: 0 12px 32px rgba(0,0,0,0.45) !important;">
            <div class="card-body p-3.5">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0 p-1.5" style="width: 46px; height: 46px; background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15);">
                        <img src="{{ asset('assets/work-report.png') }}" alt="MSN Work Report" style="width: 100%; height: 100%; object-fit: contain;">
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center gap-1.5 mb-0.5">
                            <h6 class="fw-bold mb-0 text-white" style="font-size: 0.92rem;">Install Aplikasi PT MSN</h6>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size: 0.6rem; padding: 2px 5px;">PWA App</span>
                        </div>
                        <p class="small text-white-50 mb-2.5" style="font-size: 0.74rem; line-height: 1.35;">
                            Pasang di HP / Desktop untuk akses instan full-screen, cepat, &amp; tanpa bar browser.
                        </p>
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 fw-bold shadow-sm" id="btnPwaInstallAction" style="font-size: 0.75rem; background: linear-gradient(135deg, #2C7FFF 0%, #0052cc 100%); border: none;">
                                <i class="bi bi-download me-1"></i> Pasang Aplikasi
                            </button>
                            <button type="button" class="btn btn-link text-white-50 btn-sm text-decoration-none p-0" id="btnDismissPwa" style="font-size: 0.74rem;">
                                Nanti
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Web Push Permission Floating Prompt -->
    @auth
    <div id="webPushPromptBanner" class="d-none position-fixed bottom-0 end-0 p-3" style="z-index: 1090; max-width: 360px;">
        <div class="card border-0 shadow-lg rounded-4 overflow-hidden" style="background: linear-gradient(135deg, #07152b 0%, #0c2147 100%); color: #fff; border: 1px solid rgba(255,255,255,0.15);">
            <div class="card-body p-3.5">
                <div class="d-flex align-items-start gap-2.5">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px; background: rgba(44, 127, 255, 0.25); color: #38bdf8; font-size: 1.2rem;">
                        <i class="bi bi-bell-fill"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="fw-bold mb-1" style="font-size: 0.9rem;">Aktifkan Notifikasi HP</h6>
                        <p class="small text-white-50 mb-2.5" style="font-size: 0.75rem; line-height: 1.4;">
                            Dapatkan pemberitahuan tiket baru &amp; pesan koordinasi langsung ke HP Anda secara instan.
                        </p>
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 fw-semibold shadow-xs" id="btnEnableWebPush" style="font-size: 0.74rem; background: linear-gradient(135deg, #2C7FFF 0%, #1b39da 100%); border: none;">
                                <i class="bi bi-check-circle-fill me-1"></i> Izinkan
                            </button>
                            <button type="button" class="btn btn-link text-white-50 btn-sm text-decoration-none p-0" id="btnDismissWebPush" style="font-size: 0.74rem;">
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
