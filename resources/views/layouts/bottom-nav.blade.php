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
    <!-- Curved Notch SVG Background with Luxury Rim Light & Glass Glow -->
    <div class="mobile-bottom-nav-bg">
        <svg viewBox="0 0 400 60" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <linearGradient id="bottomNavGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#07142a" />
                    <stop offset="35%" stop-color="#0c234b" />
                    <stop offset="65%" stop-color="#0f2b5c" />
                    <stop offset="100%" stop-color="#08152c" />
                </linearGradient>
                <linearGradient id="navRimLight" x1="0%" y1="0%" x2="100%" y2="0%">
                    <stop offset="0%" stop-color="rgba(255, 255, 255, 0.15)" />
                    <stop offset="30%" stop-color="rgba(147, 197, 253, 0.35)" />
                    <stop offset="50%" stop-color="rgba(255, 255, 255, 0.75)" />
                    <stop offset="70%" stop-color="rgba(147, 197, 253, 0.35)" />
                    <stop offset="100%" stop-color="rgba(255, 255, 255, 0.15)" />
                </linearGradient>
            </defs>
            <path d="M 28 0 L 140 0 C 158 0, 168 31, 200 31 C 232 31, 242 0, 260 0 L 372 0 Q 400 0, 400 28 L 400 32 Q 400 60, 372 60 L 28 60 Q 0 60, 0 32 L 0 28 Q 0 0, 28 0 Z" fill="url(#bottomNavGrad)" stroke="url(#navRimLight)" stroke-width="1.2" />
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
        <span class="bottom-nav-center-dot"></span>
    </a>
    @elseif($currentUser && $currentUser->hasRole(['admin', 'helpdesk']))
    <a href="{{ route('tiket.create') }}" class="bottom-nav-center {{ $currentRoute === 'tiket.create' ? 'active' : '' }}" title="Open Tiket" aria-label="Open Tiket">
        <div class="bottom-nav-center-btn">
            <i class="bi bi-plus-lg"></i>
        </div>
        <span class="bottom-nav-center-dot"></span>
    </a>
    @else
    <a href="{{ route('tiket.index') }}" class="bottom-nav-center {{ in_array($currentRoute, ['tiket.index', 'tiket.show']) ? 'active' : '' }}" title="Pantau" aria-label="Pantau">
        <div class="bottom-nav-center-btn">
            <i class="bi bi-ticket-detailed-fill"></i>
        </div>
        <span class="bottom-nav-center-dot"></span>
    </a>
    @endif

    <!-- 4. Notifikasi -->
    <a href="{{ route('notifications.index') }}" class="bottom-nav-item position-relative {{ $currentRoute === 'notifications.index' ? 'active' : '' }}">
        <i class="bi {{ $currentRoute === 'notifications.index' ? 'bi-bell-fill' : 'bi-bell' }}"></i>
        <span>Notif</span>
        @if($unreadCount > 0)
            <span class="bottom-nav-badge"></span>
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
