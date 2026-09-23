@php
    $currentUser  = auth()->user();
    $currentRoute = request()->route() ? request()->route()->getName() : '';
    $tiketObj     = $tiket ?? (request()->route('tiket') instanceof \App\Models\Tiket ? request()->route('tiket') : null);

    $isTeknisTugasSayaActive = false;
    $isTeknisHistoryActive   = false;

    if ($currentUser && $currentUser->hasRole('teknis')) {
        if (in_array($currentRoute, ['tiket.show', 'tiket.edit']) && $tiketObj) {
            if ($tiketObj->status !== 'CLOSE') {
                $isTeknisTugasSayaActive = true;
            } else {
                $isTeknisHistoryActive = true;
            }
        } elseif ($currentRoute === 'tiket.index') {
            if (request('status') === 'AKTIF' || request('view') === 'tugas_saya') {
                $isTeknisTugasSayaActive = true;
            } else {
                $isTeknisHistoryActive = true;
            }
        }
    }
@endphp

<aside class="app-sidebar" id="appSidebar">

    <!-- ── BRAND HEADER ── -->
    <div class="sidebar-brand d-flex align-items-center justify-content-between">
        <a href="{{ route('dashboard') }}" class="d-flex align-items-center text-decoration-none" title="PT MSN - Dashboard">
            <img src="{{ asset('assets/logo-msn BG Trans.png') }}" alt="PT MSN" style="height: 30px; max-width: 175px; width: auto; object-fit: contain;">
        </a>
        <button type="button" class="btn-close btn-close-white d-lg-none ms-auto" id="sidebarCloseBtn" aria-label="Tutup Menu" style="filter: invert(1) grayscale(100%) brightness(200%); font-size: 0.75rem; opacity: 0.8;"></button>
    </div>


    <!-- ── NAVIGATION MENU ── -->
    <nav class="sidebar-nav" id="sidebarNav">

        <!-- MENU UTAMA -->
        <div class="nav-section-title">Menu Utama</div>

        <a href="{{ route('dashboard') }}"
           class="sidebar-link {{ $currentRoute === 'dashboard' ? 'active' : '' }}">
            <i class="bi bi-grid-1x2-fill"></i>
            <span>Dashboard</span>
        </a>

        <!-- TIKET GANGGUAN -->
        <div class="nav-section-title" style="margin-top:0.5rem;">Tiket Gangguan</div>

        @if($currentUser->hasRole(['admin', 'helpdesk']))
        <a href="{{ route('tiket.create') }}" class="sidebar-link {{ $currentRoute === 'tiket.create' ? 'active' : '' }}">
            <i class="bi bi-plus-circle-fill"></i>
            <span>Open Tiket Baru</span>
        </a>
        @endif

        @if($currentUser->hasRole('teknis'))
        <a href="{{ route('tiket.index', ['status' => 'AKTIF']) }}" class="sidebar-link {{ $isTeknisTugasSayaActive ? 'active' : '' }}">
            <i class="bi bi-tools"></i>
            <span>Tugas Saya</span>
        </a>
        <a href="{{ route('tiket.index') }}" class="sidebar-link {{ $isTeknisHistoryActive ? 'active' : '' }}">
            <i class="bi bi-clock-history"></i>
            <span>History</span>
        </a>
        @else
        <a href="{{ route('tiket.index') }}" class="sidebar-link {{ in_array($currentRoute, ['tiket.index', 'tiket.show', 'tiket.edit']) ? 'active' : '' }}">
            <i class="bi bi-ticket-detailed-fill"></i>
            <span>Daftar Tiket</span>
        </a>
        @endif

        <!-- LAPORAN & SLA -->
        @if($currentUser->hasRole(['admin', 'helpdesk', 'sa_cs', 'teknis']))
        <div class="nav-section-title" style="margin-top:0.5rem;">Laporan & SLA</div>

        <a href="{{ route('reports.index') }}" class="sidebar-link {{ $currentRoute === 'reports.index' ? 'active' : '' }}">
            <i class="bi bi-graph-up-arrow"></i>
            <span>Monitoring MTTR & SLA</span>
        </a>

        <a href="{{ route('reports.export.excel') }}" class="sidebar-link">
            <i class="bi bi-file-earmark-excel-fill"></i>
            <span>Export Rekap Excel</span>
        </a>
        @endif

        <!-- NOTIFIKASI -->
        <a href="{{ route('notifications.index') }}"
           class="sidebar-link {{ $currentRoute === 'notifications.index' ? 'active' : '' }}">
            <i class="bi bi-bell-fill"></i>
            <span>Notifikasi</span>
            @php $unreadSide = $currentUser->unreadNotifications()->count(); @endphp
            @if($unreadSide > 0)
                <span class="badge bg-danger ms-auto rounded-pill px-2" style="font-size:0.68rem;">{{ $unreadSide }}</span>
            @endif
        </a>

        <!-- MASTER DATA (ADMIN) -->
        @if($currentUser->hasRole('admin'))
        <div class="nav-section-title" style="margin-top:0.5rem;">Master Data</div>

        <a href="{{ route('master.segments.index') }}"
           class="sidebar-link {{ str_starts_with($currentRoute, 'master.segments') ? 'active' : '' }}">
            <i class="bi bi-diagram-3-fill"></i>
            <span>Segment & Backbone</span>
        </a>
        <a href="{{ route('master.sla.index') }}"
           class="sidebar-link {{ str_starts_with($currentRoute, 'master.sla') ? 'active' : '' }}">
            <i class="bi bi-stopwatch-fill"></i>
            <span>SLA Target</span>
        </a>
        <a href="{{ route('master.materials.index') }}"
           class="sidebar-link {{ str_starts_with($currentRoute, 'master.materials') ? 'active' : '' }}">
            <i class="bi bi-box-seam-fill"></i>
            <span>Material & Peralatan</span>
        </a>
        <a href="{{ route('master.users.index') }}"
           class="sidebar-link {{ str_starts_with($currentRoute, 'master.users') ? 'active' : '' }}">
            <i class="bi bi-people-fill"></i>
            <span>Manajemen User</span>
        </a>
        @endif

        <!-- AKUN -->
        <div class="nav-section-title" style="margin-top:0.5rem;">Akun</div>

        <a href="{{ route('profile') }}"
           class="sidebar-link {{ $currentRoute === 'profile' ? 'active' : '' }}">
            <i class="bi bi-person-gear"></i>
            <span>Profil Pengguna</span>
        </a>

    </nav>

    <!-- ── LOGOUT FOOTER ── -->
    <div class="sidebar-footer">
        <button type="button" class="btn-sidebar-logout"
                data-bs-toggle="modal" data-bs-target="#logoutModal">
            <i class="bi bi-box-arrow-right"></i>
            Keluar dari Sistem
        </button>
    </div>
</aside>

<!-- Mobile Sidebar Overlay -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Phase Alert Toast (replacing window.alert) -->
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index:9999;" id="phaseToastContainer">
    <div id="phaseToast" class="toast align-items-center text-white border-0" role="alert"
         style="background: linear-gradient(135deg,#0f3d63,#0d9488); border-radius:12px;">
        <div class="d-flex">
            <div class="toast-body d-flex align-items-center gap-2" id="phaseToastBody">
                <i class="bi bi-hourglass-split"></i>
                <span></span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

<style>
/* Phase badge */
.phase-badge {
    font-size: 0.62rem;
    font-weight: 700;
    background: rgba(56, 189, 248, 0.12);
    color: rgba(56, 189, 248, 0.65);
    border: 1px solid rgba(56, 189, 248, 0.18);
    border-radius: 5px;
    padding: 1px 5px;
    letter-spacing: 0.3px;
    flex-shrink: 0;
}

/* Disabled links */
.sidebar-link.disabled-link {
    opacity: 0.55;
    cursor: not-allowed;
}
.sidebar-link.disabled-link:hover {
    opacity: 0.7;
    transform: none;
}
</style>

<script>
function showPhaseAlert(feature, fase) {
    const toastEl = document.getElementById('phaseToast');
    const body    = document.getElementById('phaseToastBody');
    if (body) {
        body.innerHTML = `<i class="bi bi-hourglass-split me-1"></i>
            <span><strong>${feature}</strong> akan aktif pada <strong>${fase}</strong>.</span>`;
    }
    if (toastEl && typeof bootstrap !== 'undefined') {
        const t = new bootstrap.Toast(toastEl, { delay: 3000 });
        t.show();
    }
}
</script>
