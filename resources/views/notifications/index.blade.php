@extends('layouts.app')

@section('title', 'Pusat Notifikasi')

@push('styles')
<style>
    /* ═══════════════════════════════════════════════════════════════════
       NOTIFICATION CENTER - MODERN MOBILE RESPONSIVE STYLING
       ═══════════════════════════════════════════════════════════════════ */
    .notif-metric-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: var(--neu-radius, 16px);
        padding: 0.85rem 1rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .notif-metric-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(15, 23, 42, 0.08);
    }

    .notif-metric-card::before {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 3.5px;
    }

    .notif-metric-card.border-c-total::before  { background: linear-gradient(90deg, #2563eb, #38bdf8); }
    .notif-metric-card.border-c-unread::before { background: linear-gradient(90deg, #e11d48, #fb7185); }
    .notif-metric-card.border-c-read::before   { background: linear-gradient(90deg, #059669, #34d399); }

    .notif-icon-pill {
        width: 32px;
        height: 32px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
        flex-shrink: 0;
    }

    .filter-tab-pill {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.74rem;
        font-weight: 600;
        text-decoration: none;
        color: #64748b;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        white-space: nowrap;
    }

    .filter-tab-pill:hover {
        color: #0f172a;
        background: rgba(0, 0, 0, 0.04);
    }

    .filter-tab-pill.active {
        background: linear-gradient(135deg, #2C7FFF 0%, #1b39da 100%);
        color: #ffffff !important;
        box-shadow: 0 2px 8px rgba(44, 127, 255, 0.35);
    }

    .notif-item {
        border-bottom: 1px solid #f1f5f9;
        transition: background-color 0.15s ease;
    }

    .notif-item:last-child {
        border-bottom: none;
    }

    .notif-item:hover {
        background-color: #f8fafc;
    }

    .notif-item.is-unread {
        background-color: #f6f9fe;
        border-left: 3.5px solid #2C7FFF;
    }

    @media (max-width: 575.98px) {
        .notif-metric-card {
            padding: 0.7rem 0.65rem;
            border-radius: 14px;
        }
        .notif-metric-number {
            font-size: 1.35rem !important;
        }
        .notif-metric-label {
            font-size: 0.68rem !important;
        }
        .notif-icon-pill {
            width: 28px;
            height: 28px;
            font-size: 0.85rem;
        }
        .filter-tab-pill {
            padding: 4px 10px;
            font-size: 0.7rem;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-0 pb-5 mb-5">

    <!-- ── BREADCRUMB (DESKTOP) ── -->
    <div class="d-none d-md-block mb-3">
        <x-breadcrumb :items="['Pusat Notifikasi' => null]" />
    </div>

    <!-- ── PAGE HEADER BAR ── -->
    <div class="card border-0 shadow-sm rounded-xl mb-3 mb-md-4 bg-white p-3 p-md-3.5">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="page-title-icon-box shadow-xs flex-shrink-0" style="background: linear-gradient(135deg, #2C7FFF, #1b39da); box-shadow: 0 4px 12px rgba(44, 127, 255, 0.35);">
                    <i class="bi bi-bell-fill text-white"></i>
                </div>
                <div>
                    <h1 class="page-title-text mb-0 fw-bold text-navy" style="font-size: 1.15rem; line-height: 1.3;">
                        Pusat Notifikasi
                    </h1>
                    <p class="text-muted small mb-0 d-none d-sm-block" style="font-size: 0.75rem; margin-top: 2px;">
                        Pantau pembaruan tiket gangguan, eskalasi penanganan, dan catatan aktivitas tim
                    </p>
                </div>
            </div>

            @if($unreadCount > 0)
            <div class="d-flex align-items-center">
                <form method="POST" action="{{ route('notifications.read_all') }}" class="w-100 w-sm-auto">
                    @csrf
                    <button type="submit" class="btn btn-outline-primary btn-sm rounded-pill px-3 shadow-xs d-inline-flex align-items-center justify-content-center gap-1.5 w-100" style="min-height: 35px;">
                        <i class="bi bi-check2-all"></i>
                        <span>Tandai Semua Dibaca</span>
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>

    <!-- ── KPI METRICS CARDS (COMPACT 3-COL ROW ON MOBILE & DESKTOP) ── -->
    <div class="row g-2.5 g-md-3 mb-3 mb-md-4">
        <!-- 1. Total Notifikasi -->
        <div class="col-4">
            <div class="notif-metric-card border-c-total">
                <div class="d-flex justify-content-between align-items-start mb-1.5">
                    <span class="text-muted small text-uppercase fw-bold notif-metric-label" style="font-size: 0.7rem; letter-spacing: 0.2px;">Total</span>
                    <div class="notif-icon-pill" style="background: #eff6ff; color: #2563eb;">
                        <i class="bi bi-bell-fill"></i>
                    </div>
                </div>
                <div class="notif-metric-number fw-bold text-navy" style="font-size: 1.65rem; line-height: 1.1;">
                    {{ $totalCount }}
                </div>
            </div>
        </div>

        <!-- 2. Belum Dibaca -->
        <div class="col-4">
            <div class="notif-metric-card border-c-unread">
                <div class="d-flex justify-content-between align-items-start mb-1.5">
                    <span class="text-muted small text-uppercase fw-bold notif-metric-label" style="font-size: 0.7rem; letter-spacing: 0.2px;">Belum Baca</span>
                    <div class="notif-icon-pill" style="background: #fff1f2; color: #e11d48;">
                        <i class="bi bi-envelope-exclamation-fill"></i>
                    </div>
                </div>
                <div class="notif-metric-number fw-bold text-danger" style="font-size: 1.65rem; line-height: 1.1;">
                    {{ $unreadCount }}
                </div>
            </div>
        </div>

        <!-- 3. Sudah Dibaca -->
        <div class="col-4">
            <div class="notif-metric-card border-c-read">
                <div class="d-flex justify-content-between align-items-start mb-1.5">
                    <span class="text-muted small text-uppercase fw-bold notif-metric-label" style="font-size: 0.7rem; letter-spacing: 0.2px;">Sudah Baca</span>
                    <div class="notif-icon-pill" style="background: #ecfdf5; color: #059669;">
                        <i class="bi bi-envelope-check-fill"></i>
                    </div>
                </div>
                <div class="notif-metric-number fw-bold text-success" style="font-size: 1.65rem; line-height: 1.1;">
                    {{ $totalCount - $unreadCount }}
                </div>
            </div>
        </div>
    </div>

    <!-- ── NOTIFICATION LIST CARD WITH SLEEK SEGMENTED FILTER BAR ── -->
    <div class="card border-0 shadow-sm rounded-xl overflow-hidden bg-white mb-4">
        <!-- Segmented Filter Control Header -->
        <div class="card-header bg-white border-bottom p-2.5 p-sm-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div class="d-flex align-items-center gap-1 p-1 bg-light rounded-pill border overflow-x-auto text-nowrap" style="max-width: 100%;">
                <a class="filter-tab-pill {{ $filter === 'all' ? 'active' : '' }}"
                   href="{{ route('notifications.index', ['filter' => 'all']) }}">
                    Semua
                    <span class="badge {{ $filter === 'all' ? 'bg-white text-primary' : 'bg-secondary bg-opacity-10 text-muted' }} rounded-pill ms-1" style="font-size: 0.65rem;">
                        {{ $totalCount }}
                    </span>
                </a>
                <a class="filter-tab-pill {{ $filter === 'unread' ? 'active' : '' }}"
                   href="{{ route('notifications.index', ['filter' => 'unread']) }}">
                    Belum Dibaca
                    <span class="badge {{ $filter === 'unread' ? 'bg-white text-danger' : 'bg-danger bg-opacity-10 text-danger' }} rounded-pill ms-1" style="font-size: 0.65rem;">
                        {{ $unreadCount }}
                    </span>
                </a>
                <a class="filter-tab-pill {{ $filter === 'read' ? 'active' : '' }}"
                   href="{{ route('notifications.index', ['filter' => 'read']) }}">
                    Sudah Dibaca
                    <span class="badge {{ $filter === 'read' ? 'bg-white text-success' : 'bg-success bg-opacity-10 text-success' }} rounded-pill ms-1" style="font-size: 0.65rem;">
                        {{ $totalCount - $unreadCount }}
                    </span>
                </a>
            </div>

            <div class="text-muted small d-none d-md-block" style="font-size: 0.74rem;">
                Menampilkan {{ $notifications->count() }} notifikasi
            </div>
        </div>

        <!-- Notification Items Stream -->
        <div class="list-group list-group-flush">
            @forelse($notifications as $n)
                @php
                    $data = $n->data;
                    $color = $data['color'] ?? 'primary';
                    $icon = $data['icon'] ?? 'bi-bell-fill';
                    $isUnread = $n->read_at === null;

                    // Color theme mapping for clean soft badges
                    $colorMap = [
                        'primary' => ['bg' => '#eff6ff', 'color' => '#2563eb', 'border' => '#bfdbfe'],
                        'success' => ['bg' => '#ecfdf5', 'color' => '#059669', 'border' => '#a7f3d0'],
                        'warning' => ['bg' => '#fffbeb', 'color' => '#d97706', 'border' => '#fde68a'],
                        'danger'  => ['bg' => '#fff1f2', 'color' => '#e11d48', 'border' => '#fecdd3'],
                        'info'    => ['bg' => '#f0f9ff', 'color' => '#0284c7', 'border' => '#bae6fd'],
                    ];
                    $theme = $colorMap[$color] ?? $colorMap['primary'];
                @endphp
                <div class="list-group-item notif-item p-3 p-md-3.5 {{ $isUnread ? 'is-unread' : '' }}">
                    <div class="d-flex align-items-start gap-2.5 gap-md-3">
                        <!-- Icon Box -->
                        <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width: 42px; height: 42px; background: {{ $theme['bg'] }}; color: {{ $theme['color'] }}; border: 1px solid {{ $theme['border'] }}; font-size: 1.15rem;">
                            <i class="bi {{ $icon }}"></i>
                        </div>

                        <!-- Content Area -->
                        <div class="flex-grow-1 overflow-hidden">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-1">
                                <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                    @php
                                        $notifTitle = preg_replace('/^Update\s*\[.*?\]\s*-\s*/i', 'Update Koordinasi - ', $data['title'] ?? 'Pemberitahuan Sistem');
                                    @endphp
                                    <h6 class="fw-bold mb-0 text-navy" style="font-size: 0.92rem;">
                                        {{ $notifTitle }}
                                    </h6>
                                    @if($isUnread)
                                        <span class="badge rounded-pill fw-bold" style="background: #fff1f2; color: #e11d48; border: 1px solid #fecdd3; font-size: 0.62rem; padding: 2px 7px;">
                                            Baru
                                        </span>
                                    @endif
                                </div>
                                <small class="text-muted font-monospace" style="font-size: 0.72rem;">
                                    <i class="bi bi-clock me-1"></i>{{ $n->created_at ? $n->created_at->format('d M Y, H:i') : '' }}
                                    <span class="d-none d-sm-inline">({{ $n->created_at ? $n->created_at->diffForHumans() : '' }})</span>
                                </small>
                            </div>

                            <p class="text-secondary small mb-2.5" style="line-height: 1.5; font-size: 0.82rem;">
                                {{ $data['message'] ?? '' }}
                            </p>

                            <!-- Actions Row -->
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                @if(!empty($data['url']))
                                    <a href="{{ route('notifications.read', $n->id) }}" class="btn btn-sm btn-primary rounded-pill px-3 shadow-xs d-inline-flex align-items-center gap-1" style="background: linear-gradient(135deg, #2C7FFF 0%, #1b39da 100%); border: none; font-size: 0.74rem; min-height: 28px;">
                                        <i class="bi bi-box-arrow-up-right"></i>
                                        <span>Buka Tiket</span>
                                    </a>
                                @endif

                                @if($isUnread)
                                    <form method="POST" action="{{ route('notifications.read', $n->id) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-light border rounded-pill px-2.5 text-muted shadow-2xs d-inline-flex align-items-center gap-1" style="font-size: 0.74rem; min-height: 28px;">
                                            <i class="bi bi-check2"></i>
                                            <span>Tandai Dibaca</span>
                                        </button>
                                    </form>
                                @endif

                                <form method="POST" action="{{ route('notifications.destroy', $n->id) }}" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus notifikasi ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light border rounded-pill px-2 text-danger shadow-2xs" title="Hapus Notifikasi" style="font-size: 0.74rem; min-height: 28px;">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5 px-3">
                    <div class="rounded-circle mx-auto d-flex align-items-center justify-content-center mb-3 shadow-xs"
                         style="width: 68px; height: 68px; background: #f1f5f9; color: #94a3b8; font-size: 1.75rem;">
                        <i class="bi bi-bell-slash"></i>
                    </div>
                    <h6 class="fw-bold text-navy mb-1" style="font-size: 1rem;">Tidak Ada Notifikasi</h6>
                    <p class="text-muted small mb-0" style="max-width: 320px; margin: 0 auto; font-size: 0.8rem;">
                        Belum ada riwayat notifikasi baru pada filter ini.
                    </p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($notifications->hasPages())
        <div class="card-footer bg-white border-top p-3 d-flex justify-content-center">
            {{ $notifications->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>

</div>
@endsection
