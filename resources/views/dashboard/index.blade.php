@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
<style>
    /* ── DASHBOARD HERO GREETING (NEUMORPHIC SOFT SLATE) ── */
    .dash-greeting {
        background: linear-gradient(145deg, #071530 0%, #0d2352 55%, #133070 100%);
        border-radius: var(--neu-radius-lg, 18px);
        padding: 1.6rem 2rem;
        margin-bottom: 1.5rem;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.08);
        box-shadow: var(--neu-flat, 6px 6px 14px #c2ccd9, -6px -6px 14px #ffffff);
    }

    .dash-greeting::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image:
            linear-gradient(rgba(44, 127, 255, 0.08) 1px, transparent 1px),
            linear-gradient(90deg, rgba(44, 127, 255, 0.08) 1px, transparent 1px);
        background-size: 28px 28px;
        pointer-events: none;
    }

    .dash-greeting > * { position: relative; z-index: 1; }

    .greeting-title {
        font-size: 1.35rem;
        font-weight: 800;
        color: #ffffff;
        margin: 0 0 0.3rem;
        letter-spacing: -0.3px;
        text-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }

    .greeting-sub {
        font-size: 0.84rem;
        color: rgba(186, 214, 235, 0.85);
        margin: 0;
    }

    @media (max-width: 575.98px) {
        .dash-greeting {
            padding: 1.15rem 1rem !important;
            border-radius: 14px !important;
            margin-bottom: 1rem !important;
        }
        .greeting-title {
            font-size: 1.18rem !important;
        }
        .greeting-sub {
            font-size: 0.76rem !important;
        }
        .dash-hero-actions {
            width: 100%;
        }
        .dash-hero-actions .btn-open-tiket {
            width: 100%;
        }
    }

    /* ── STAT CARDS (NEUMORPHIC SOFT UI) ── */
    .stat-card {
        background: var(--neu-surface, #e6ecf4);
        border: 1px solid rgba(255, 255, 255, 0.7);
        border-radius: var(--neu-radius, 16px);
        padding: 1.3rem;
        position: relative;
        overflow: hidden;
        box-shadow: var(--neu-flat, 6px 6px 14px #c2ccd9, -6px -6px 14px #ffffff);
        transition: transform 0.22s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.22s ease;
        height: 100%;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        bottom: 0; left: 0; right: 0;
        height: 4px;
        border-radius: 0 0 16px 16px;
    }

    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--neu-floating, 10px 10px 22px #bdc7d4, -10px -10px 22px #ffffff);
    }

    .stat-card.c-all::before    { background: linear-gradient(90deg, #1b39da, #2C7FFF); }
    .stat-card.c-open::before   { background: linear-gradient(90deg, #d97706, #fbbf24); }
    .stat-card.c-proses::before { background: linear-gradient(90deg, #7c3aed, #a78bfa); }
    .stat-card.c-close::before  { background: linear-gradient(90deg, #059669, #34d399); }

    .stat-label {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.7px;
        color: var(--neu-text-muted, #64748b);
        margin-bottom: 0.4rem;
    }

    .stat-number {
        font-size: 2.1rem;
        font-weight: 800;
        line-height: 1;
        letter-spacing: -1px;
        margin-bottom: 0.25rem;
    }

    .stat-desc {
        font-size: 0.74rem;
        color: var(--neu-text-muted, #64748b);
        font-weight: 500;
    }

    .stat-icon-wrap {
        width: 46px; height: 46px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        flex-shrink: 0;
        box-shadow: var(--neu-emboss, 3px 3px 7px #c2ccd9, -3px -3px 7px #ffffff);
        border: 1px solid rgba(255, 255, 255, 0.6);
    }

    /* ── METRIC CARDS & NEU BOXES ── */
    .metric-card {
        background: var(--neu-surface, #e6ecf4);
        border: 1px solid rgba(255, 255, 255, 0.7);
        border-radius: var(--neu-radius, 16px);
        padding: 1.3rem;
        box-shadow: var(--neu-flat, 6px 6px 14px #c2ccd9, -6px -6px 14px #ffffff);
        height: 100%;
    }

    .metric-card-title {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.7px;
        color: var(--neu-text-muted, #64748b);
        margin-bottom: 0.75rem;
    }

    .neu-recessed-tile {
        background: #e1e7f0;
        border-radius: 12px;
        box-shadow: var(--neu-inset, inset 3px 3px 6px #c2ccd9, inset -3px -3px 6px #ffffff);
        padding: 1rem;
        text-align: center;
        border: 1px solid rgba(255, 255, 255, 0.4);
    }

    /* ── SLA ALERT ── */
    .sla-alert {
        background: var(--neu-surface, #e6ecf4);
        border: 1px solid rgba(239, 68, 68, 0.3);
        border-left: 5px solid #ef4444;
        border-radius: var(--neu-radius, 16px);
        padding: 1rem 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.9rem;
        margin-bottom: 1.5rem;
        box-shadow: var(--neu-flat, 6px 6px 14px #c2ccd9, -6px -6px 14px #ffffff);
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-0 pb-5 mb-5">

    {{-- ── GREETING HERO ── --}}
    <div class="dash-greeting">
        <div class="d-flex align-items-start align-items-md-center justify-content-between flex-column flex-md-row gap-3">
            <div>
                <h1 class="greeting-title">
                    @php
                        $hour = now()->hour;
                        $greet = $hour < 12 ? 'Selamat Pagi' : ($hour < 17 ? 'Selamat Siang' : 'Selamat Malam');
                    @endphp
                    {{ $greet }}, {{ explode(' ', $user->name)[0] }}! 👋
                </h1>
                <p class="greeting-sub">
                    Berikut ringkasan operasional gangguan backbone terkini &bull; {{ now()->translatedFormat('l, d F Y') }}
                </p>
            </div>
            <div class="dash-hero-actions d-flex flex-wrap align-items-center gap-2">
                <span class="badge bg-white text-success border-0 shadow-xs fw-semibold px-2.5 py-1.5 d-inline-flex align-items-center gap-1.5 rounded-pill text-nowrap" id="dashSyncBadge" style="font-size: 0.72rem; min-height: 32px;">
                    <span class="spinner-grow spinner-grow-sm text-success flex-shrink-0" style="width: 7px; height: 7px;"></span>
                    <span>Live Sync (60s)</span>
                </span>

                <span class="badge rounded-pill fw-semibold text-nowrap d-inline-flex align-items-center gap-1.5 px-3 py-1.5" style="background: rgba(44, 127, 255, 0.22); color: #dbe6fe; border: 1px solid rgba(147, 197, 253, 0.35); font-size: 0.72rem; min-height: 32px;">
                    <i class="bi bi-person-fill text-info flex-shrink-0"></i>
                    <span>{{ $user->role_label }}</span>
                </span>

                @if($user->hasRole(['admin', 'helpdesk']))
                <a href="{{ route('tiket.create') }}"
                   class="btn btn-primary btn-sm rounded-pill px-3.5 py-1.5 fw-bold d-inline-flex align-items-center justify-content-center gap-2 text-nowrap shadow-sm btn-open-tiket"
                   style="background: linear-gradient(135deg, #2C7FFF 0%, #1b39da 100%); border: none; font-size: 0.8rem; min-height: 32px; box-shadow: 0 4px 14px rgba(44, 127, 255, 0.45);">
                    <i class="bi bi-plus-circle-fill flex-shrink-0" style="font-size: 0.9rem;"></i>
                    <span>Open Tiket Baru</span>
                </a>
                @endif
            </div>
        </div>
    </div>

    {{-- ── SLA EXCEEDED ALERT ── --}}
    @if(($totalSlaExceeded > 0 || count($overSlaActiveTikets) > 0) && $user->hasRole(['admin', 'helpdesk', 'sa_cs']))
    <div class="sla-alert">
        <div style="width:38px;height:38px;background:#fef2f2;border:1px solid #fecaca;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="bi bi-exclamation-triangle-fill" style="color:#ef4444;font-size:1.1rem;"></i>
        </div>
        <div class="flex-grow-1">
            <div style="font-weight:700;color:#b91c1c;font-size:0.88rem;">
                Perhatian: {{ count($overSlaActiveTikets) > 0 ? count($overSlaActiveTikets) . ' Tiket Aktif Melewati Target SLA!' : $totalSlaExceeded . ' Tiket Tercatat Melebihi SLA' }}
            </div>
            <div style="font-size:0.78rem;color:#ef4444;opacity:0.85;margin-top:2px;">
                Segera prioritaskan koordinasi percepatan penyelesaian bersama tim teknis di lapangan.
            </div>
        </div>
        <a href="{{ route('reports.index', ['sla_status' => 'LEBIH']) }}" class="btn btn-danger btn-sm px-3 shadow-xs">
            Lihat Tiket &rarr;
        </a>
    </div>
    @endif

    {{-- ── STAT CARDS ROW ── --}}
    <div class="row g-3 mb-4">
        {{-- Total Tiket --}}
        <div class="col-6 col-lg-3">
            <div class="stat-card c-all">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">Total Tiket</div>
                        <div class="stat-number" style="color:#1e40af;">{{ $totalTiket }}</div>
                        <div class="stat-desc">Insiden tercatat</div>
                    </div>
                    <div class="stat-icon-wrap" style="background:#eff6ff;">
                        <i class="bi bi-ticket-perforated-fill" style="color:#3b82f6;"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tiket OPEN --}}
        <div class="col-6 col-lg-3">
            <div class="stat-card c-open">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">Tiket OPEN</div>
                        <div class="stat-number" style="color:#d97706;">{{ $totalOpen }}</div>
                        <div class="stat-desc">Menunggu penanganan</div>
                    </div>
                    <div class="stat-icon-wrap" style="background:#fffbeb;">
                        <i class="bi bi-hourglass-split" style="color:#f59e0b;"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tiket PROSES --}}
        <div class="col-6 col-lg-3">
            <div class="stat-card c-proses">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">Dalam Proses</div>
                        <div class="stat-number" style="color:#7c3aed;">{{ $totalProses }}</div>
                        <div class="stat-desc">Tim di lapangan</div>
                    </div>
                    <div class="stat-icon-wrap" style="background:#f5f3ff;">
                        <i class="bi bi-tools" style="color:#8b5cf6;"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tiket CLOSE --}}
        <div class="col-6 col-lg-3">
            <div class="stat-card c-close">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">Tiket Selesai</div>
                        <div class="stat-number" style="color:#065f46;">{{ $totalClose }}</div>
                        <div class="stat-desc">Terselesaikan</div>
                    </div>
                    <div class="stat-icon-wrap" style="background:#f0fdf4;">
                        <i class="bi bi-check2-circle" style="color:#10b981;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── ROLE SPECIFIC WIDGETS & CHARTS ROW ── --}}
    <div class="row g-4 mb-4">
        {{-- Left: Daily Incidents Chart (7 Days) --}}
        <div class="col-12 col-lg-7">
            <div class="card border-0 shadow-sm rounded-xl p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="fw-bold mb-0 text-navy"><i class="bi bi-bar-chart-fill text-primary me-1"></i> Aktivitas Gangguan (7 Hari Terakhir)</h6>
                        <small class="text-muted">Jumlah pembukaan tiket gangguan per hari</small>
                    </div>
                </div>
                <div style="height: 220px; position: relative;">
                    <canvas id="dailyIncidentsChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Right: SLA Compliance & Performance Card --}}
        <div class="col-12 col-lg-5">
            <div class="card border-0 shadow-sm rounded-xl p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0 text-navy"><i class="bi bi-speedometer2 text-teal me-1"></i> Performa SLA & MTTR</h6>
                    <a href="{{ route('reports.index') }}" class="small text-decoration-none fw-semibold text-teal">Detail Report &rarr;</a>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <div class="neu-recessed-tile">
                            <span class="small text-muted text-uppercase fw-bold" style="font-size: 0.68rem; letter-spacing: 0.5px;">Rata-rata MTTR</span>
                            <div class="h4 fw-bold text-navy mt-1 mb-0">{{ $avgMttrFormatted }}</div>
                            <small class="text-muted" style="font-size: 0.7rem;">{{ $avgMttrMinutes }} menit</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="neu-recessed-tile">
                            <span class="small text-muted text-uppercase fw-bold" style="font-size: 0.68rem; letter-spacing: 0.5px;">Kepatuhan SLA</span>
                            <div class="h4 fw-bold {{ $slaComplianceRate >= 85 ? 'text-success' : 'text-danger' }} mt-1 mb-0">
                                {{ $slaComplianceRate }}%
                            </div>
                            <small class="text-muted" style="font-size: 0.7rem;">Target: 95%</small>
                        </div>
                    </div>
                </div>

                <div class="progress" style="height: 8px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $slaComplianceRate }}%"></div>
                    <div class="progress-bar bg-danger" role="progressbar" style="width: {{ 100 - $slaComplianceRate }}%"></div>
                </div>
                <div class="d-flex justify-content-between text-muted mt-2 small" style="font-size: 0.75rem;">
                    <span><i class="bi bi-check-circle-fill text-success me-1"></i> Tepat: {{ $slaStats['total_tepat'] }}</span>
                    <span><i class="bi bi-x-circle-fill text-danger me-1"></i> Lebih: {{ $slaStats['total_lebih'] }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ── AKTIVITAS LAPANGAN & TIKET TERBARU ── --}}
    @if($user->hasRole('teknis') && count($activeTikets) > 0)
    <div class="card border-0 shadow-sm rounded-xl mb-4 overflow-hidden border-start border-4 border-teal">
        <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0 text-navy"><i class="bi bi-tools text-teal me-2"></i>Tiket Aktif Perlu Tindakan Lapangan</h6>
            <span class="badge bg-warning-subtle text-warning border border-warning-subtle">{{ count($activeTikets) }} Tiket Berjalan</span>
        </div>
        <div class="card-body p-0">
            <!-- ── MOBILE CARD VIEW (< 768px) ── -->
            <div class="d-block d-md-none p-2.5 p-sm-3 bg-light bg-opacity-25">
                @foreach($activeTikets as $act)
                @php
                    $elapsedMins = $act->tanggal_open ? $act->tanggal_open->diffInMinutes(now()) : 0;
                    $hours = floor($elapsedMins / 60);
                    $mins = $elapsedMins % 60;
                    $isOverSla = $elapsedMins > $act->sla_target_minutes;
                @endphp
                <div class="tiket-mobile-card border shadow-sm mb-2.5">
                    <!-- Top Status Stripe -->
                    <div class="{{ $act->card_stripe_class }}"></div>

                    <div class="tiket-mobile-card-body p-3">
                        <!-- Row 1: No Tiket & Durasi Berjalan / Over SLA -->
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <a href="{{ route('tiket.show', $act->id) }}" class="text-decoration-none">
                                <span class="badge bg-light text-primary font-monospace border px-2 py-1" style="font-size:0.75rem;">
                                    <i class="bi bi-ticket-perforated me-1"></i>{{ $act->no_tiket }}
                                </span>
                            </a>
                            <div class="d-flex align-items-center gap-1">
                                <span class="badge {{ $isOverSla ? 'bg-danger-subtle text-danger border border-danger-subtle' : 'bg-secondary-subtle text-dark border' }}" style="font-size:0.72rem;">
                                    <i class="bi bi-clock-history me-1"></i>{{ $hours }}j {{ $mins }}m
                                </span>
                                @if($isOverSla)
                                    <span class="badge bg-danger text-white" style="font-size:0.68rem;">Over SLA</span>
                                @endif
                            </div>
                        </div>

                        <!-- Row 2: Status Link Impact -->
                        <h6 class="fw-bold mb-1.5" style="font-size:0.88rem; line-height:1.35;">
                            <a href="{{ route('tiket.show', $act->id) }}" class="text-navy text-decoration-none">
                                {{ $act->status_link_impact }}
                            </a>
                        </h6>

                        <!-- Row 3: Segment Backbone -->
                        <div class="small text-muted d-flex align-items-center gap-1 mb-3" style="font-size:0.75rem;">
                            <i class="bi bi-geo-alt-fill text-teal"></i>
                            <span class="badge bg-light text-muted border fw-normal">{{ $act->backbone_segment }}</span>
                        </div>

                        <!-- Row 4: Aksi Cepat Buttons -->
                        <div class="d-flex align-items-center justify-content-between pt-2 border-top gap-2">
                            <a href="{{ route('tiket.show', $act->id) }}" class="btn btn-sm btn-outline-secondary py-1 px-2.5 rounded-2 d-flex align-items-center gap-1" style="font-size:0.75rem;">
                                <i class="bi bi-eye"></i> Detail
                            </a>
                            <a href="{{ route('tiket.show', ['tiket' => $act->id, 'tab' => 'kronologis']) }}" class="btn btn-cjp-teal btn-sm py-1 px-3 rounded-2 d-flex align-items-center gap-1" style="font-size:0.75rem;">
                                <i class="bi bi-plus-circle me-1"></i> Update Lapangan
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- ── DESKTOP TABLE VIEW (>= 768px) ── -->
            <div class="table-responsive d-none d-md-block">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th>No Tiket</th>
                            <th>Status Link Impact</th>
                            <th>Segment</th>
                            <th>Durasi Berjalan</th>
                            <th class="text-end">Aksi Cepat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($activeTikets as $act)
                        @php
                            $elapsedMins = $act->tanggal_open ? $act->tanggal_open->diffInMinutes(now()) : 0;
                            $hours = floor($elapsedMins / 60);
                            $mins = $elapsedMins % 60;
                        @endphp
                        <tr>
                            <td>
                                <a href="{{ route('tiket.show', $act->id) }}" class="fw-bold font-monospace text-decoration-none text-navy">
                                    {{ $act->no_tiket }}
                                </a>
                            </td>
                            <td class="fw-semibold text-navy">{{ $act->status_link_impact }}</td>
                            <td><span class="badge bg-light text-muted border">{{ $act->backbone_segment }}</span></td>
                            <td class="font-monospace small {{ $elapsedMins > $act->sla_target_minutes ? 'text-danger fw-bold' : 'text-navy' }}">
                                {{ $hours }}j {{ $mins }}m
                                @if($elapsedMins > $act->sla_target_minutes)
                                    <span class="badge bg-danger ms-1">Over SLA</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('tiket.show', ['tiket' => $act->id, 'tab' => 'kronologis']) }}" class="btn btn-cjp-teal btn-sm py-1 px-3">
                                    <i class="bi bi-plus-circle me-1"></i> Update Lapangan
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- ── DAFTAR TIKET TERBARU ── --}}
    <div class="card border-0 shadow-sm rounded-xl overflow-hidden mb-4">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0 text-navy d-flex align-items-center gap-2">
                <i class="bi bi-ticket-detailed-fill text-primary"></i> Daftar Tiket Terbaru
            </h6>
            <a href="{{ route('tiket.index') }}" class="btn btn-sm btn-outline-primary py-1 px-3 rounded-pill">
                Lihat Semua &rarr;
            </a>
        </div>

        <!-- ── MOBILE CARD VIEW (< 768px) ── -->
        <div class="d-block d-md-none p-2.5 p-sm-3 bg-light bg-opacity-25">
            @forelse($latestTikets as $tiket)
            <div class="tiket-mobile-card">
                <!-- Top Status Stripe -->
                <div class="{{ $tiket->card_stripe_class }}"></div>

                <div class="tiket-mobile-card-body">
                    <!-- Row 1: No Tiket & Status / SLA Badges -->
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <a href="{{ route('tiket.show', $tiket->id) }}" class="text-decoration-none">
                            <span class="badge bg-light text-primary font-monospace border px-2 py-1" style="font-size:0.75rem;">
                                <i class="bi bi-ticket-perforated me-1"></i>{{ $tiket->no_tiket }}
                            </span>
                        </a>
                        <div class="d-flex align-items-center gap-1">
                            <span class="badge {{ $tiket->status_badge_class }}" style="font-size:0.68rem;">
                                {{ $tiket->status }}
                            </span>
                            <span class="badge {{ $tiket->sla_badge_class }}" style="font-size:0.68rem;">
                                {{ $tiket->sla_status }}
                            </span>
                        </div>
                    </div>

                    <!-- Row 2: Impact / Judul Gangguan -->
                    <h6 class="fw-bold mb-1.5" style="font-size:0.88rem; line-height:1.35;">
                        <a href="{{ route('tiket.show', $tiket->id) }}" class="text-navy text-decoration-none">
                            {{ $tiket->status_link_impact }}
                        </a>
                    </h6>

                    <!-- Row 3: Segment Backbone -->
                    <div class="small text-muted d-flex align-items-center gap-1 mb-2" style="font-size:0.75rem;">
                        <i class="bi bi-geo-alt-fill text-teal"></i>
                        <span class="text-truncate">{{ $tiket->backbone_segment }}</span>
                    </div>

                    <!-- Row 4: Meta Info & Detail Action -->
                    <div class="d-flex align-items-center justify-content-between pt-2 border-top" style="font-size:0.72rem;">
                        <div class="text-muted">
                            <div>
                                <i class="bi bi-clock me-1 text-primary"></i>
                                Open: <strong class="text-dark">{{ $tiket->tanggal_open->format('d M Y, H:i') }}</strong> WIB
                            </div>
                            @if($tiket->status === 'CLOSE')
                                <div class="text-success mt-0.5">
                                    <i class="bi bi-check2 me-1"></i> MTTR: <strong>{{ $tiket->formatted_mttr }}</strong>
                                </div>
                            @else
                                <div class="text-muted mt-0.5">
                                    <i class="bi bi-person me-1"></i> Pelapor: <strong>{{ $tiket->creator ? $tiket->creator->name : '-' }}</strong>
                                </div>
                            @endif
                        </div>

                        <div class="d-flex align-items-center gap-1">
                            <a href="{{ route('tiket.show', $tiket->id) }}" class="btn btn-cjp-teal btn-sm px-2.5 py-1 d-flex align-items-center gap-1">
                                <span>Detail</span>
                                <i class="bi bi-chevron-right" style="font-size:0.7rem;"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-4 text-muted bg-white rounded-xl border">
                <i class="bi bi-inbox fs-2 d-block mb-1 opacity-50"></i>
                <div class="small fw-semibold">Belum ada tiket tercatat</div>
            </div>
            @endforelse
        </div>

        <!-- ── DESKTOP TABLE VIEW (>= 768px) ── -->
        <div class="d-none d-md-block table-responsive">
            <table class="table custom-table mb-0">
                <thead>
                    <tr>
                        <th>No Tiket</th>
                        <th>Link Impact & Segment</th>
                        <th>Waktu Buka</th>
                        <th>Status</th>
                        <th>SLA</th>
                        <th>MTTR</th>
                        <th>Pelapor</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($latestTikets as $tiket)
                    <tr>
                        <td>
                            <a href="{{ route('tiket.show', $tiket->id) }}" class="text-decoration-none">
                                <span style="font-family:monospace;font-weight:700;font-size:0.82rem;color:#0f3d63;background:#eff6ff;padding:3px 8px;border-radius:6px;white-space:nowrap;">
                                    {{ $tiket->no_tiket }}
                                </span>
                            </a>
                        </td>
                        <td>
                            <div style="font-weight:600;font-size:0.85rem;color:#0f172a;">{{ $tiket->status_link_impact }}</div>
                            <div style="font-size:0.75rem;color:#64748b;">
                                <i class="bi bi-geo-alt me-1" style="color:#0d9488;"></i>{{ $tiket->backbone_segment }}
                            </div>
                        </td>
                        <td>
                            <div style="font-weight:600;font-size:0.82rem;color:#0f172a;">{{ $tiket->tanggal_open->format('d M Y') }}</div>
                            <div style="font-size:0.73rem;color:#94a3b8;">{{ $tiket->tanggal_open->format('H:i') }} WIB</div>
                        </td>
                        <td>
                            <span class="badge {{ $tiket->status_badge_class }}">
                                {{ $tiket->status }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $tiket->sla_badge_class }}">
                                {{ $tiket->sla_status }}
                            </span>
                        </td>
                        <td>
                            <span style="font-weight:700;font-size:0.82rem;color:#0f172a;">{{ $tiket->formatted_mttr }}</span>
                        </td>
                        <td>
                            <span style="font-size:0.8rem;color:#475569;">{{ $tiket->creator ? $tiket->creator->name : '-' }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted small">
                            Belum ada tiket tercatat.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ── 1. DAILY INCIDENTS BAR CHART ──
    const ctxDaily = document.getElementById('dailyIncidentsChart');
    if (ctxDaily) {
        new Chart(ctxDaily, {
            type: 'bar',
            data: {
                labels: {!! json_encode($dailyLabels) !!},
                datasets: [{
                    label: 'Jumlah Tiket',
                    data: {!! json_encode($dailyCounts) !!},
                    backgroundColor: 'rgba(44, 127, 255, 0.85)',
                    hoverBackgroundColor: '#1b39da',
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 },
                        grid: { color: '#f1f5f9' }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    }

    // ── 2. AUTO-REFRESH DASHBOARD TIAP 60 DETIK ──
    setTimeout(function() {
        window.location.reload();
    }, 60000);
});
</script>
@endpush
