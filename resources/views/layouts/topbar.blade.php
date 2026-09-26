@php
    $currentUser = auth()->user();
    $roleSlug = $currentUser?->role ?? 'guest';
    $badgeText = match($roleSlug) {
        'admin' => 'ADMIN NOC',
        'teknis' => 'TIM TEKNIS',
        'helpdesk' => 'HELPDESK NOC',
        'sa_cs' => 'SA & CS',
        default => strtoupper($currentUser?->role_short ?? $roleSlug),
    };

    $badgeStyle = match($roleSlug) {
        'admin' => 'background:#ede9fe; color:#6d28d9; border:1px solid #ddd6fe;',
        'teknis' => 'background:#fef3c7; color:#b45309; border:1px solid #fde68a;',
        'helpdesk' => 'background:#f0fdfa; color:#0f766e; border:1px solid #99f6e4;',
        'sa_cs' => 'background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe;',
        default => 'background:#f1f5f9; color:#475569; border:1px solid #cbd5e1;',
    };
@endphp

<header class="app-topbar">

    <!-- ── LEFT SECTION ── -->
    <div class="d-flex align-items-center gap-2 gap-md-3">

        @if($currentUser && $currentUser->hasRole('admin'))
        <!-- Mobile Sidebar Toggle (Admin only) -->
        <button class="topbar-btn d-lg-none shadow-xs" type="button" id="sidebarToggle" title="Buka Menu Sidebar" aria-label="Toggle Navigation">
            <i class="bi bi-list fs-5 text-dark"></i>
        </button>
        @endif

        <!-- PT MSN Brand Logo (Mobile only, hidden on Desktop to avoid duplicate with sidebar) -->
        <a href="{{ route('dashboard') }}" class="d-flex d-lg-none align-items-center text-decoration-none gap-1.5 py-1" title="PT MSN - Dashboard">
            <img src="{{ asset('assets/logo-msn BG Trans.png') }}" alt="Logo PT MSN" class="brand-logo-img" style="height: 26px; max-width: 125px; width: auto; object-fit: contain;">
            <span class="badge d-none d-xs-inline-block" style="{{ $badgeStyle }} font-size:0.6rem; font-weight:700; padding:2px 6px; border-radius:5px; letter-spacing:0.3px;">
                {{ $badgeText }}
            </span>
        </a>

        <!-- System Status Pill (Desktop) -->
        <div class="topbar-status d-none d-lg-inline-flex align-items-center">
            <span class="status-dot-pulse"></span>
            <span class="fw-bold text-dark" style="font-size: 0.78rem; letter-spacing: 0.2px;">Backbone Monitoring</span>
            <span class="badge bg-success text-white fw-bold px-2 py-0.5 rounded-pill shadow-xs" style="font-size: 0.62rem; letter-spacing: 0.4px;">LIVE</span>
        </div>
    </div>

    <!-- ── SPACER ── -->
    <div class="flex-grow-1"></div>

    <!-- ── RIGHT SECTION ── -->
    <div class="d-flex align-items-center gap-1.5 gap-sm-2.5">

        <!-- Live Clock Widget -->
        <div class="topbar-clock d-none d-sm-flex align-items-center">
            <div class="topbar-clock-icon">
                <i class="bi bi-clock-history"></i>
            </div>
            <div>
                <div class="topbar-clock-time" id="liveClock">--:--:-- WIB</div>
                <div class="topbar-clock-date">{{ now()->translatedFormat('l, d F Y') }}</div>
            </div>
        </div>

        <!-- Notification Bell -->
        @php
            $unreadTopCount = $currentUser ? $currentUser->unreadNotifications()->count() : 0;
            $recentNotifs = $currentUser ? $currentUser->notifications()->latest()->take(5)->get() : collect();
        @endphp
        <div class="dropdown" id="topbarNotificationDropdown">
            <button class="topbar-btn position-relative"
                    type="button"
                    data-bs-toggle="dropdown"
                    aria-expanded="false"
                    title="Pusat Notifikasi">
                <i class="bi bi-bell-fill" style="color:#64748b;font-size:1.05rem;"></i>
                @if($unreadTopCount > 0)
                    <span class="position-absolute top-0 end-0 translate-middle badge rounded-pill bg-danger"
                          id="topbarNotifBadge"
                          style="font-size:0.6rem;padding:2.5px 5.5px;box-shadow:0 0 0 2px #fff, 0 2px 5px rgba(239, 68, 68, 0.4);">
                        {{ $unreadTopCount > 99 ? '99+' : $unreadTopCount }}
                    </span>
                @else
                    <span class="position-absolute top-0 end-0 translate-middle badge rounded-pill bg-danger d-none"
                          id="topbarNotifBadge"
                          style="font-size:0.6rem;padding:2.5px 5.5px;box-shadow:0 0 0 2px #fff, 0 2px 5px rgba(239, 68, 68, 0.4);">
                        0
                    </span>
                @endif
            </button>

            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2" style="width:340px; max-width: calc(100vw - 20px); padding:0; overflow:hidden; border-radius:14px;">
                <li class="d-flex align-items-center justify-content-between px-3 py-2.5"
                    style="background:#f8fafc;border-bottom:1px solid #f1f5f9;">
                    <div class="d-flex align-items-center gap-2">
                        <span class="fw-bold" style="font-size:0.88rem;color:#0f172a;">Notifikasi</span>
                        <span class="badge" id="topbarUnreadLabelBadge"
                              style="background:#eff6ff;color:#3b82f6;border:1px solid #bfdbfe;font-size:0.68rem;border-radius:6px;padding:2px 8px;">
                            {{ $unreadTopCount }} Baru
                        </span>
                    </div>
                    @if($unreadTopCount > 0)
                        <form method="POST" action="{{ route('notifications.read_all') }}" class="d-inline" id="topbarMarkAllForm">
                            @csrf
                            <button type="submit" class="btn btn-link text-decoration-none p-0" style="font-size:0.75rem;color:#2563eb;font-weight:600;">
                                Tandai Dibaca
                            </button>
                        </form>
                    @endif
                </li>

                <div id="topbarNotifList" style="max-height: 320px; overflow-y: auto;">
                    @forelse($recentNotifs as $notif)
                        @php
                            $nData = $notif->data;
                            $nColor = $nData['color'] ?? 'primary';
                            $nIcon = $nData['icon'] ?? 'bi-bell-fill';
                            $isUnread = $notif->read_at === null;
                        @endphp
                        <li>
                            <a class="dropdown-item py-2.5 px-3 d-flex align-items-start gap-2.5 position-relative {{ $isUnread ? 'bg-light bg-opacity-75' : '' }}"
                               href="{{ !empty($nData['url']) ? $nData['url'] : route('notifications.read', $notif->id) }}"
                               style="border-bottom:1px solid #f1f5f9; white-space: normal;">
                                <div style="width:32px;height:32px;background:var(--bs-{{ $nColor }}, #2563eb);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;color:#fff;">
                                    <i class="bi {{ $nIcon }}" style="font-size:0.9rem;"></i>
                                </div>
                                <div class="flex-grow-1 overflow-hidden">
                                    <div class="d-flex justify-content-between align-items-center">
                                        @php
                                            $topNotifTitle = preg_replace('/^Update\s*\[.*?\]\s*-\s*/i', 'Update Koordinasi - ', $nData['title'] ?? 'Notifikasi');
                                        @endphp
                                        <div class="fw-bold text-truncate" style="font-size:0.8rem;color:#1e293b;max-width:180px;">
                                            {{ $topNotifTitle }}
                                        </div>
                                        <small class="text-muted" style="font-size:0.65rem;">
                                            {{ $notif->created_at ? $notif->created_at->diffForHumans(null, true) : '' }}
                                        </small>
                                    </div>
                                    <div class="text-muted text-truncate mt-0.5" style="font-size:0.73rem;">
                                        {{ preg_replace('/:\s*>\s*/', ': ', (string) ($nData['message'] ?? '')) }}
                                    </div>
                                </div>
                                @if($isUnread)
                                    <span class="position-absolute top-50 end-0 translate-middle-y me-2"
                                          style="width:7px;height:7px;background:#2563eb;border-radius:50%;"></span>
                                @endif
                            </a>
                        </li>
                    @empty
                        <li class="py-4 text-center text-muted">
                            <i class="bi bi-bell-slash fs-4 d-block mb-1 opacity-50"></i>
                            <div style="font-size:0.78rem;">Belum ada notifikasi</div>
                        </li>
                    @endforelse
                </div>

                <li class="text-center py-2.5" style="background:#f8fafc;border-top:1px solid #f1f5f9;">
                    <a href="{{ route('notifications.index') }}" style="font-size:0.78rem;color:#2563eb;font-weight:700;text-decoration:none;">
                        Buka Pusat Notifikasi <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </li>
            </ul>
        </div>

        <!-- User Profile Dropdown Chip -->
        @if($currentUser)
        <div class="dropdown">
            <button class="topbar-user-chip" type="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                <div class="text-end d-flex flex-column justify-content-center pe-1">
                    <div class="topbar-user-name text-truncate" style="font-size:0.82rem;font-weight:700;color:#0f172a;line-height:1.2;max-width:140px;">
                        {{ $currentUser->name }}
                    </div>
                    <div class="topbar-user-role text-truncate" style="font-size:0.68rem;font-weight:600;color:#2563eb;line-height:1.1;max-width:140px;">
                        <span class="d-none d-sm-inline">{{ $currentUser->role_label }}</span>
                        <span class="d-inline d-sm-none">{{ $currentUser->role_short }}</span>
                    </div>
                </div>
                <div class="topbar-avatar overflow-hidden">
                    @if($currentUser->avatar_url)
                        <img src="{{ $currentUser->avatar_url }}" alt="{{ $currentUser->name }}" style="width:100%;height:100%;object-fit:cover;">
                    @else
                        {{ strtoupper(substr($currentUser->name, 0, 1)) }}
                    @endif
                </div>
            </button>

            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2 rounded-xl" style="width:230px;padding:0;overflow:hidden;">
                <!-- User info header -->
                <li style="background:linear-gradient(135deg,#07152b 0%,#102d66 100%);padding:1rem 1rem 0.85rem;">
                    <div style="font-weight:700;color:#fff;font-size:0.88rem;">{{ $currentUser->name }}</div>
                    <div style="font-size:0.73rem;color:rgba(203,213,225,0.85);margin-top:2px;">{{ $currentUser->email }}</div>
                    <div class="mt-2">
                        <span style="background:rgba(255,255,255,0.15);color:#ffffff;border:1px solid rgba(255,255,255,0.25);font-size:0.68rem;font-weight:600;padding:2px 9px;border-radius:6px;letter-spacing:0.3px;">
                            {{ $currentUser->role_label }}
                        </span>
                    </div>
                </li>

                <li style="padding:0.4rem;">
                    <a class="dropdown-item d-flex align-items-center gap-2 rounded-2"
                       href="{{ route('profile') }}"
                       style="font-size:0.84rem;padding:0.55rem 0.75rem;">
                        <i class="bi bi-person-circle text-primary" style="font-size:1rem;"></i>
                        <span>Profil Saya</span>
                    </a>
                </li>
                <li style="padding:0 0.4rem 0.4rem;">
                    <button type="button"
                            class="dropdown-item d-flex align-items-center gap-2 rounded-2 text-danger"
                            data-bs-toggle="modal"
                            data-bs-target="#logoutModal"
                            style="font-size:0.84rem;padding:0.55rem 0.75rem;width:100%;background:none;border:none;cursor:pointer;">
                        <i class="bi bi-box-arrow-right" style="font-size:1rem;"></i>
                        <span>Keluar Sistem</span>
                    </button>
                </li>
            </ul>
        </div>
        @endif
    </div>
</header>
