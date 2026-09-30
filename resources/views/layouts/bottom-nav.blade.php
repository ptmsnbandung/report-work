@php
    $currentRoute = request()->route() ? request()->route()->getName() : '';
    $currentUser  = auth()->user();
    $unreadCount  = $currentUser ? $currentUser->unreadNotifications()->count() : 0;

    $tiketObj = $tiket ?? (request()->route('tiket') instanceof \App\Models\Tiket ? request()->route('tiket') : null);

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

<!-- Mobile Bottom Quick Navigation Bar (Curved Notch App-Native) -->
<nav class="mobile-bottom-nav">
    <!-- Curved Notch SVG Background -->
    <div class="mobile-bottom-nav-bg">
        <svg viewBox="0 0 400 60" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <linearGradient id="bottomNavGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#07152b" />
                    <stop offset="50%" stop-color="#0c2147" />
                    <stop offset="100%" stop-color="#102d66" />
                </linearGradient>
            </defs>
            <path d="M 26 0 L 138 0 C 160 0, 172 34, 200 34 C 228 34, 240 0, 262 0 L 374 0 Q 400 0, 400 26 L 400 34 Q 400 60, 374 60 L 26 60 Q 0 60, 0 34 L 0 26 Q 0 0, 26 0 Z" fill="url(#bottomNavGrad)" stroke="rgba(255, 255, 255, 0.16)" stroke-width="1.2" />
        </svg>
    </div>
    <!-- 1. Home / Dashboard -->
    <a href="{{ route('dashboard') }}" class="bottom-nav-item {{ $currentRoute === 'dashboard' ? 'active' : '' }}">
        <i class="bi {{ $currentRoute === 'dashboard' ? 'bi-grid-fill' : 'bi-grid' }}"></i>
        <span>Home</span>
    </a>

    <!-- 2. Tiket / History -->
    @if($currentUser && $currentUser->hasRole('teknis'))
    <a href="{{ route('tiket.index') }}" class="bottom-nav-item {{ $isTeknisHistoryActive ? 'active' : '' }}">
        <i class="bi bi-clock-history"></i>
        <span>History</span>
    </a>
    @else
    <a href="{{ route('tiket.index') }}" class="bottom-nav-item {{ in_array($currentRoute, ['tiket.index', 'tiket.show', 'tiket.edit']) ? 'active' : '' }}">
        <i class="bi {{ in_array($currentRoute, ['tiket.index', 'tiket.show', 'tiket.edit']) ? 'bi-ticket-perforated-fill' : 'bi-ticket-perforated' }}"></i>
        <span>Tiket</span>
    </a>
    @endif

    <!-- 3. Prominent Raised Center Action Button -->
    @if($currentUser && $currentUser->hasRole(['teknis']))
    <a href="{{ route('tiket.index', ['status' => 'AKTIF']) }}" class="bottom-nav-center {{ $isTeknisTugasSayaActive ? 'active' : '' }}" title="Tugas Saya" aria-label="Tugas Saya">
        <div class="bottom-nav-center-btn">
            <i class="bi bi-tools"></i>
        </div>
    </a>
    @elseif($currentUser && $currentUser->hasRole(['admin', 'helpdesk']))
    <a href="{{ route('tiket.create') }}" class="bottom-nav-center {{ $currentRoute === 'tiket.create' ? 'active' : '' }}" title="Open Tiket" aria-label="Open Tiket">
        <div class="bottom-nav-center-btn">
            <i class="bi bi-plus-lg"></i>
        </div>
    </a>
    @else
    <a href="{{ route('tiket.index') }}" class="bottom-nav-center {{ in_array($currentRoute, ['tiket.index', 'tiket.show']) ? 'active' : '' }}" title="Pantau" aria-label="Pantau">
        <div class="bottom-nav-center-btn">
            <i class="bi bi-ticket-detailed-fill"></i>
        </div>
    </a>
    @endif

    <!-- 4. Notifikasi -->
    <a href="{{ route('notifications.index') }}" class="bottom-nav-item position-relative {{ $currentRoute === 'notifications.index' ? 'active' : '' }}">
        <i class="bi {{ $currentRoute === 'notifications.index' ? 'bi-bell-fill' : 'bi-bell' }}"></i>
        <span>Notif</span>
        @if($unreadCount > 0)
            <span class="position-absolute top-1 translate-middle p-1 bg-danger border border-light rounded-circle" style="right: 28%; width: 7px; height: 7px;"></span>
        @endif
    </a>

    <!-- 5. Monitoring (Role Manager Teknis, Helpdesk, Teknis, Admin) / Profil Akun (Role Lainnya) -->
    @if($currentUser && $currentUser->hasRole(['admin', 'manager_teknisi', 'helpdesk', 'teknis', 'sa_cs']))
    <a href="{{ route('reports.index') }}" class="bottom-nav-item {{ str_starts_with($currentRoute, 'reports.') ? 'active' : '' }}" title="Monitoring">
        <i class="bi {{ str_starts_with($currentRoute, 'reports.') ? 'bi-graph-up-arrow' : 'bi-graph-up' }}"></i>
        <span>Monitoring</span>
    </a>
    @else
    <a href="{{ route('profile') }}" class="bottom-nav-item {{ $currentRoute === 'profile' ? 'active' : '' }}">
        @if($currentUser && $currentUser->avatar_url)
            <img src="{{ $currentUser->avatar_url }}" alt="Akun" style="width: 22px; height: 22px; border-radius: 50%; object-fit: cover; margin-bottom: 2px; border: 1.5px solid rgba(255,255,255,0.7);">
        @else
            <i class="bi {{ $currentRoute === 'profile' ? 'bi-person-circle' : 'bi-person' }}"></i>
        @endif
        <span>Akun</span>
    </a>
    @endif
</nav>
