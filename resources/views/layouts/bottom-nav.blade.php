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

<!-- Mobile Bottom Quick Navigation Bar (App-Native Pro) -->
<nav class="mobile-bottom-nav">
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
    <a href="{{ route('tiket.index', ['status' => 'AKTIF']) }}" class="bottom-nav-center {{ $isTeknisTugasSayaActive ? 'active' : '' }}">
        <div class="bottom-nav-center-btn">
            <i class="bi bi-tools"></i>
        </div>
        <span class="bottom-nav-center-label">Tugas Saya</span>
    </a>
    @elseif($currentUser && $currentUser->hasRole(['admin', 'helpdesk']))
    <a href="{{ route('tiket.create') }}" class="bottom-nav-center {{ $currentRoute === 'tiket.create' ? 'active' : '' }}">
        <div class="bottom-nav-center-btn">
            <i class="bi bi-plus-lg"></i>
        </div>
        <span class="bottom-nav-center-label">Open Tiket</span>
    </a>
    @else
    <a href="{{ route('tiket.index') }}" class="bottom-nav-center {{ in_array($currentRoute, ['tiket.index', 'tiket.show']) ? 'active' : '' }}">
        <div class="bottom-nav-center-btn">
            <i class="bi bi-ticket-detailed-fill"></i>
        </div>
        <span class="bottom-nav-center-label">Pantau</span>
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

    <!-- 5. Monitoring MTTR & SLA (Role Helpdesk & Teknis) / Profil Akun (Role Lainnya) -->
    @if($currentUser && $currentUser->hasRole(['helpdesk', 'teknis']))
    <a href="{{ route('reports.index') }}" class="bottom-nav-item {{ str_starts_with($currentRoute, 'reports.') ? 'active' : '' }}" title="Monitoring MTTR & SLA">
        <i class="bi {{ str_starts_with($currentRoute, 'reports.') ? 'bi-graph-up-arrow' : 'bi-graph-up' }}"></i>
        <span>MTTR & SLA</span>
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
