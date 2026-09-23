<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') | PT MSN</title>

    <!-- Favicon / Logo Tab PT MSN -->
    <link rel="icon" type="image/png" href="{{ asset('assets/logo-msn BG Trans - Copy2.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/logo-msn BG Trans - Copy2.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/logo-msn BG Trans - Copy2.png') }}">

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
        });
    </script>

    @stack('scripts')
</body>
</html>
