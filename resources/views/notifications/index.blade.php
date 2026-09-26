@extends('layouts.app')

@section('title', 'Pusat Notifikasi')

@push('styles')
<style>
    /* ═══════════════════════════════════════════════════════════════════
       NOTIFICATION CENTER - MODERN ENTERPRISE AESTHETICS (DESKTOP & MOBILE)
       ═══════════════════════════════════════════════════════════════════ */
    .notif-hero-banner {
        background: linear-gradient(135deg, #07152b 0%, #0f2b5c 50%, #1e3a8a 100%);
        border-radius: 20px;
        position: relative;
        overflow: hidden;
        color: #ffffff;
        box-shadow: 0 10px 30px rgba(7, 21, 43, 0.15);
    }

    .notif-hero-banner::after {
        content: '';
        position: absolute;
        top: -60px;
        right: -40px;
        width: 220px;
        height: 220px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(56, 189, 248, 0.25) 0%, rgba(56, 189, 248, 0) 70%);
        pointer-events: none;
    }

    .notif-metric-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        padding: 1rem 1.15rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(15, 23, 42, 0.04);
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%;
    }

    .notif-metric-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
        border-color: #cbd5e1;
    }

    .notif-metric-card::before {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 4px;
        border-radius: 0 0 18px 18px;
    }

    .notif-metric-card.border-c-total::before  { background: linear-gradient(90deg, #2563eb, #38bdf8); }
    .notif-metric-card.border-c-unread::before { background: linear-gradient(90deg, #e11d48, #fb7185); }
    .notif-metric-card.border-c-read::before   { background: linear-gradient(90deg, #059669, #34d399); }

    .notif-icon-pill {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        flex-shrink: 0;
    }

    /* Filter segmented bar */
    .notif-filter-wrapper {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 50px;
        padding: 4px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        max-width: 100%;
        overflow-x: auto;
    }

    .filter-tab-pill {
        padding: 6px 16px;
        border-radius: 50px;
        font-size: 0.8rem;
        font-weight: 600;
        text-decoration: none;
        color: #64748b;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
    }

    .filter-tab-pill:hover {
        color: #0f172a;
        background: rgba(255, 255, 255, 0.7);
    }

    .filter-tab-pill.active {
        background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
        color: #ffffff !important;
        box-shadow: 0 3px 10px rgba(2, 132, 199, 0.35);
    }

    /* Notification Item Card */
    .notif-card-item {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 1.1rem 1.25rem;
        margin-bottom: 0.85rem;
        transition: all 0.2s ease;
        position: relative;
        cursor: pointer;
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.02);
    }

    .notif-card-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.07);
        border-color: #cbd5e1;
    }

    .notif-card-item.is-unread {
        background: #f8fbff;
        border-left: 4.5px solid #0284c7;
        border-color: #bae6fd #e2e8f0 #e2e8f0 #0284c7;
    }

    .notif-type-icon {
        width: 44px;
        height: 44px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        flex-shrink: 0;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.04);
    }

    .notif-unread-pulse {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #e11d48;
        box-shadow: 0 0 0 0 rgba(225, 29, 72, 0.7);
        animation: pulseDot 1.8s infinite;
    }

    @keyframes pulseDot {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(225, 29, 72, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(225, 29, 72, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(225, 29, 72, 0); }
    }

    .ticket-badge-pill {
        font-family: 'JetBrains Mono', 'Fira Code', Consolas, monospace;
        font-size: 0.75rem;
        font-weight: 700;
        background: #f1f5f9;
        color: #0f172a;
        padding: 2.5px 8px;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
        letter-spacing: 0.2px;
    }

    .notif-card-item.is-unread .ticket-badge-pill {
        background: #e0f2fe;
        color: #0369a1;
        border-color: #bae6fd;
    }

    .notif-metric-label {
        font-size: 0.72rem;
        letter-spacing: 0.3px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        min-width: 0;
    }

    .notif-metric-number {
        font-size: 1.65rem;
        line-height: 1.1;
        letter-spacing: -0.5px;
    }

    .notif-metric-sub {
        font-size: 0.7rem;
        line-height: 1.2;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: block;
    }

    /* Mobile Responsive Customizations */
    @media (max-width: 767.98px) {
        .notif-hero-banner {
            padding: 1.15rem 1rem !important;
            border-radius: 16px;
        }
        .notif-hero-icon {
            width: 40px !important;
            height: 40px !important;
            font-size: 1.15rem !important;
        }
        .notif-hero-title {
            font-size: 1.1rem !important;
        }
        .notif-hero-desc {
            font-size: 0.75rem !important;
            line-height: 1.35 !important;
        }
        .notif-metric-card {
            padding: 0.65rem 0.55rem;
            border-radius: 14px;
        }
        .notif-metric-card::before {
            height: 3px;
            border-radius: 0 0 14px 14px;
        }
        .notif-metric-number {
            font-size: 1.35rem !important;
            margin: 0.2rem 0;
        }
        .notif-metric-label {
            font-size: 0.62rem !important;
            letter-spacing: 0.1px !important;
        }
        .notif-icon-pill {
            width: 24px !important;
            height: 24px !important;
            font-size: 0.75rem !important;
            border-radius: 7px !important;
        }
        .notif-metric-sub {
            font-size: 0.62rem !important;
        }
        .notif-card-item {
            padding: 0.85rem 0.9rem;
            border-radius: 14px;
            margin-bottom: 0.75rem;
        }
        .notif-type-icon {
            width: 36px;
            height: 36px;
            font-size: 1rem;
            border-radius: 10px;
        }
        .filter-tab-pill {
            padding: 5px 12px;
            font-size: 0.74rem;
        }
        .notif-action-btn-text {
            display: none;
        }
    }

    @media (max-width: 420px) {
        .notif-metric-card {
            padding: 0.55rem 0.45rem;
            border-radius: 12px;
        }
        .notif-metric-card::before {
            height: 3px;
            border-radius: 0 0 12px 12px;
        }
        .notif-metric-label {
            font-size: 0.58rem !important;
        }
        .notif-icon-pill {
            width: 22px !important;
            height: 22px !important;
            font-size: 0.68rem !important;
            border-radius: 6px !important;
        }
        .notif-metric-number {
            font-size: 1.25rem !important;
            margin: 0.15rem 0;
        }
        .notif-metric-sub {
            font-size: 0.58rem !important;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-2 px-md-3 pb-5 mb-5">

    <!-- ── BREADCRUMB (DESKTOP) ── -->
    <div class="d-none d-md-block mb-3">
        <x-breadcrumb :items="['Pusat Notifikasi' => null]" />
    </div>

    <!-- ── MODERN HERO HEADER BAR ── -->
    <div class="notif-hero-banner p-3.5 p-md-4 mb-3 mb-md-4">
        <div class="row align-items-center g-3">
            <div class="col-12 col-md-7">
                <div class="d-flex align-items-center gap-2.5 gap-md-3">
                    <div class="notif-hero-icon rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width: 46px; height: 46px; background: rgba(56, 189, 248, 0.15); border: 1.5px solid rgba(56, 189, 248, 0.4); color: #38bdf8; font-size: 1.3rem;">
                        <i class="bi bi-bell-fill"></i>
                    </div>
                    <div>
                        <h1 class="notif-hero-title fw-bold mb-0 text-white" style="font-size: 1.25rem; letter-spacing: -0.2px;">
                            Pusat Notifikasi Sistem
                        </h1>
                        <p class="notif-hero-desc text-white-50 small mb-0 mt-0.5" style="font-size: 0.8rem;">
                            Monitoring pembaruan tiket gangguan, chat koordinasi, dokumentasi & SLA real-time
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-5">
                <div class="d-flex align-items-center justify-content-start justify-content-md-end gap-2 flex-wrap">
                    @if($unreadCount > 0)
                    <form method="POST" action="{{ route('notifications.read_all') }}" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-info text-white rounded-pill px-3 shadow-xs d-inline-flex align-items-center gap-1.5 fw-semibold" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); border: none; min-height: 34px; font-size: 0.8rem;">
                            <i class="bi bi-check2-all"></i>
                            <span>Tandai Semua Dibaca</span>
                        </button>
                    </form>
                    @endif

                    @if($totalCount > 0)
                    <form method="POST" action="{{ route('notifications.destroy_all') }}" class="m-0" onsubmit="return confirm('Apakah Anda yakin ingin menghapus seluruh riwayat notifikasi Anda?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-light border-opacity-25 rounded-pill px-3 shadow-xs d-inline-flex align-items-center gap-1.5" style="min-height: 34px; font-size: 0.78rem;">
                            <i class="bi bi-trash3"></i>
                            <span>Bersihkan Semua</span>
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- ── KPI METRICS SUMMARY CARDS ── -->
    <div class="row g-2 g-md-3 mb-3 mb-md-4">
        <!-- 1. Total Notifikasi -->
        <div class="col-4">
            <div class="notif-metric-card border-c-total">
                <div class="d-flex justify-content-between align-items-center mb-1 gap-1">
                    <span class="text-muted text-uppercase fw-bold notif-metric-label">Total</span>
                    <div class="notif-icon-pill" style="background: #eff6ff; color: #2563eb;">
                        <i class="bi bi-bell-fill"></i>
                    </div>
                </div>
                <div class="notif-metric-number fw-bold text-navy">
                    {{ $totalCount }}
                </div>
                <span class="text-muted notif-metric-sub">Seluruh aktivitas</span>
            </div>
        </div>

        <!-- 2. Belum Dibaca -->
        <div class="col-4">
            <div class="notif-metric-card border-c-unread">
                <div class="d-flex justify-content-between align-items-center mb-1 gap-1">
                    <span class="text-muted text-uppercase fw-bold notif-metric-label">Belum Dibaca</span>
                    <div class="notif-icon-pill" style="background: #fff1f2; color: #e11d48;">
                        <i class="bi bi-envelope-exclamation-fill"></i>
                    </div>
                </div>
                <div class="notif-metric-number fw-bold text-danger">
                    {{ $unreadCount }}
                </div>
                <span class="text-muted notif-metric-sub">Perlu perhatian</span>
            </div>
        </div>

        <!-- 3. Sudah Dibaca -->
        <div class="col-4">
            <div class="notif-metric-card border-c-read">
                <div class="d-flex justify-content-between align-items-center mb-1 gap-1">
                    <span class="text-muted text-uppercase fw-bold notif-metric-label">Sudah Dibaca</span>
                    <div class="notif-icon-pill" style="background: #ecfdf5; color: #059669;">
                        <i class="bi bi-envelope-check-fill"></i>
                    </div>
                </div>
                <div class="notif-metric-number fw-bold text-success">
                    {{ max(0, $totalCount - $unreadCount) }}
                </div>
                <span class="text-muted notif-metric-sub">Telah ditinjau</span>
            </div>
        </div>
    </div>

    <!-- ── SEGMENTED FILTER CONTROLS & STREAM HEADER ── -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 px-1">
        <div class="notif-filter-wrapper shadow-xs">
            <a class="filter-tab-pill {{ $filter === 'all' ? 'active' : '' }}"
               href="{{ route('notifications.index', ['filter' => 'all']) }}">
                <i class="bi bi-grid-fill"></i>
                <span>Semua</span>
                <span class="badge {{ $filter === 'all' ? 'bg-white text-dark' : 'bg-secondary bg-opacity-15 text-muted' }} rounded-pill ms-0.5" style="font-size: 0.65rem;">
                    {{ $totalCount }}
                </span>
            </a>
            <a class="filter-tab-pill {{ $filter === 'unread' ? 'active' : '' }}"
               href="{{ route('notifications.index', ['filter' => 'unread']) }}">
                <i class="bi bi-envelope-fill"></i>
                <span>Belum Dibaca</span>
                <span class="badge {{ $filter === 'unread' ? 'bg-white text-danger' : 'bg-danger bg-opacity-15 text-danger' }} rounded-pill ms-0.5" style="font-size: 0.65rem;">
                    {{ $unreadCount }}
                </span>
            </a>
            <a class="filter-tab-pill {{ $filter === 'read' ? 'active' : '' }}"
               href="{{ route('notifications.index', ['filter' => 'read']) }}">
                <i class="bi bi-envelope-open-fill"></i>
                <span>Sudah Dibaca</span>
                <span class="badge {{ $filter === 'read' ? 'bg-white text-success' : 'bg-success bg-opacity-15 text-success' }} rounded-pill ms-0.5" style="font-size: 0.65rem;">
                    {{ max(0, $totalCount - $unreadCount) }}
                </span>
            </a>
        </div>

        <div class="text-muted small" style="font-size: 0.78rem;">
            <i class="bi bi-list-check me-1"></i>Menampilkan <strong>{{ $notifications->count() }}</strong> notifikasi
        </div>
    </div>

    <!-- ── NOTIFICATION CARDS STREAM ── -->
    <div class="notif-stream-container">
        @forelse($notifications as $n)
            @php
                $data = $n->data;
                $isUnread = $n->read_at === null;
                $type = $data['type'] ?? 'INFO';

                // Extract Ticket Number
                $noTiket = $data['no_tiket'] ?? null;
                if (!$noTiket && !empty($data['title']) && preg_match('/(BDG-\d{8}-\d{3})/i', $data['title'], $matches)) {
                    $noTiket = $matches[1];
                }

                // Theme styling mapping based on event type
                $theme = match($type) {
                    'TIKET_BARU'       => ['bg' => '#eff6ff', 'color' => '#2563eb', 'border' => '#bfdbfe', 'icon' => 'bi-plus-circle-fill', 'label' => 'Tiket Baru'],
                    'KRONOLOGIS_BARU'  => ['bg' => '#f0fdf4', 'color' => '#16a34a', 'border' => '#bbf7d0', 'icon' => 'bi-chat-left-text-fill', 'label' => 'Chat Koordinasi'],
                    'TIKET_UPDATE'     => ['bg' => '#fffbeb', 'color' => '#d97706', 'border' => '#fde68a', 'icon' => 'bi-pencil-square', 'label' => 'Pembaruan Tiket'],
                    'TIKET_CLOSED'     => ['bg' => '#ecfdf5', 'color' => '#059669', 'border' => '#a7f3d0', 'icon' => 'bi-check-circle-fill', 'label' => 'Tiket Selesai'],
                    'HANDOVER_SHIFT'   => ['bg' => '#faf5ff', 'color' => '#9333ea', 'border' => '#e9d5ff', 'icon' => 'bi-arrow-left-right', 'label' => 'Handover Shift'],
                    default            => ['bg' => '#f8fafc', 'color' => '#0284c7', 'border' => '#e2e8f0', 'icon' => 'bi-bell-fill', 'label' => 'Pemberitahuan']
                };

                // Title cleanup
                $cleanTitle = preg_replace('/^Update\s*\[.*?\]\s*-\s*/i', 'Update Koordinasi - ', (string) ($data['title'] ?? 'Pemberitahuan Sistem'));

                // Message breakdown (extract sender if exists)
                $rawMsg = (string) ($data['message'] ?? '');
                $rawMsg = preg_replace('/^>\s*/m', '', $rawMsg);
                $rawMsg = preg_replace('/:\s*>\s*/', ': ', $rawMsg);

                $senderName = null;
                $msgBody = $rawMsg;

                if (preg_match('/^([a-zA-Z0-9\s_\.\-]+)\s*:\s*(.+)$/s', $rawMsg, $parts)) {
                    $senderName = trim($parts[1]);
                    $msgBody = trim($parts[2]);
                }
            @endphp

            <div class="notif-card-item {{ $isUnread ? 'is-unread' : '' }}" onclick="window.location='{{ route('notifications.read', $n->id) }}'">
                <div class="d-flex align-items-start gap-3">
                    <!-- Icon Box -->
                    <div class="notif-type-icon flex-shrink-0" style="background: {{ $theme['bg'] }}; color: {{ $theme['color'] }}; border: 1.5px solid {{ $theme['border'] }};">
                        <i class="bi {{ $theme['icon'] }}"></i>
                    </div>

                    <!-- Main Body Area -->
                    <div class="flex-grow-1 overflow-hidden">
                        <!-- Top Meta Header -->
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-1.5">
                            <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                @if($noTiket)
                                    <span class="ticket-badge-pill">#{{ $noTiket }}</span>
                                @endif
                                <span class="badge rounded-pill" style="background: {{ $theme['bg'] }}; color: {{ $theme['color'] }}; border: 1px solid {{ $theme['border'] }}; font-size: 0.7rem;">
                                    {{ $theme['label'] }}
                                </span>
                                @if($isUnread)
                                    <span class="badge bg-danger-subtle text-danger rounded-pill d-inline-flex align-items-center gap-1 px-2" style="font-size: 0.68rem;">
                                        <span class="notif-unread-pulse"></span>
                                        <span>Baru</span>
                                    </span>
                                @endif
                            </div>

                            <div class="text-muted small d-flex align-items-center gap-1" style="font-size: 0.72rem;">
                                <i class="bi bi-clock text-secondary"></i>
                                <span class="fw-semibold text-dark">{{ $n->created_at ? $n->created_at->diffForHumans() : '-' }}</span>
                                <span class="d-none d-md-inline text-muted">({{ $n->created_at ? $n->created_at->format('d M Y, H:i') : '' }} WIB)</span>
                            </div>
                        </div>

                        <!-- Title -->
                        <h6 class="fw-bold text-navy mb-1.5" style="font-size: 0.94rem; line-height: 1.35;">
                            {{ $cleanTitle }}
                        </h6>

                        <!-- Content Preview Card -->
                        <div class="p-2.5 rounded-3 bg-light border border-light-subtle mb-2.5 text-break" style="font-size: 0.83rem; line-height: 1.45;">
                            @if($senderName)
                                <div class="d-flex align-items-center gap-1.5 mb-1 text-navy fw-semibold" style="font-size: 0.78rem;">
                                    <i class="bi bi-person-fill text-primary"></i>
                                    <span>{{ $senderName }}</span>
                                </div>
                            @endif
                            <div class="text-secondary">{{ $msgBody }}</div>
                        </div>

                        <!-- Action Controls Footer -->
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2" onclick="event.stopPropagation();">
                            <div class="d-flex align-items-center gap-2">
                                @if(!empty($data['url']))
                                    <a href="{{ route('notifications.read', $n->id) }}" class="btn btn-sm btn-primary rounded-pill px-3 shadow-xs d-inline-flex align-items-center gap-1.5 fw-semibold" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); border: none; font-size: 0.76rem; min-height: 30px;">
                                        <i class="bi bi-arrow-right-circle-fill"></i>
                                        <span>Buka Tiket</span>
                                    </a>
                                @endif

                                @if($isUnread)
                                    <form method="POST" action="{{ route('notifications.read', $n->id) }}" class="d-inline m-0">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 shadow-2xs d-inline-flex align-items-center gap-1" style="font-size: 0.75rem; min-height: 30px;" title="Tandai Sudah Dibaca">
                                            <i class="bi bi-check2 text-success"></i>
                                            <span class="d-none d-sm-inline">Tandai Dibaca</span>
                                        </button>
                                    </form>
                                @endif
                            </div>

                            <form method="POST" action="{{ route('notifications.destroy', $n->id) }}" class="d-inline m-0" onsubmit="return confirm('Apakah Anda yakin ingin menghapus notifikasi ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-light border rounded-pill px-2.5 text-danger shadow-2xs d-inline-flex align-items-center gap-1" title="Hapus Notifikasi" style="font-size: 0.75rem; min-height: 30px;">
                                    <i class="bi bi-trash"></i>
                                    <span class="d-none d-sm-inline">Hapus</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="card border-0 shadow-sm rounded-4 text-center py-5 px-3 bg-white">
                <div class="rounded-circle mx-auto d-flex align-items-center justify-content-center mb-3 shadow-xs"
                     style="width: 72px; height: 72px; background: #f1f5f9; color: #94a3b8; font-size: 2rem;">
                    <i class="bi bi-bell-slash"></i>
                </div>
                <h6 class="fw-bold text-navy mb-1" style="font-size: 1.05rem;">Tidak Ada Notifikasi</h6>
                <p class="text-muted small mb-0" style="max-width: 360px; margin: 0 auto; font-size: 0.82rem;">
                    Belum ada riwayat notifikasi baru pada filter ini. Semua informasi penting akan tercatat secara otomatis di sini.
                </p>
            </div>
        @endforelse
    </div>

    <!-- ── PAGINATION ── -->
    @if($notifications->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $notifications->links('pagination::bootstrap-5') }}
    </div>
    @endif

</div>
@endsection
