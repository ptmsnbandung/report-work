@extends('layouts.app')

@section('title', 'Laporan & Analisis MTTR / SLA')

@push('styles')
<style>
    /* ═══════════════════════════════════════════════════════════════════
       MONITORING MTTR & SLA - MODERN MOBILE RESPONSIVE STYLING
       ═══════════════════════════════════════════════════════════════════ */
    .stat-metric-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: var(--neu-radius, 16px);
        padding: 1rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .stat-metric-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08);
    }

    .stat-metric-card::before {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 3.5px;
    }

    .stat-metric-card.border-c-total::before  { background: linear-gradient(90deg, #2563eb, #38bdf8); }
    .stat-metric-card.border-c-open::before   { background: linear-gradient(90deg, #d97706, #fbbf24); }
    .stat-metric-card.border-c-close::before  { background: linear-gradient(90deg, #059669, #34d399); }
    .stat-metric-card.border-c-mttr::before   { background: linear-gradient(90deg, #0284c7, #38bdf8); }
    .stat-metric-card.border-c-tepat::before  { background: linear-gradient(90deg, #0d9488, #2dd4bf); }
    .stat-metric-card.border-c-sla::before    { background: linear-gradient(90deg, #10b981, #34d399); }
    .stat-metric-card.border-c-sla-danger::before { background: linear-gradient(90deg, #e11d48, #f43f5e); }

    .stat-icon-pill {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        flex-shrink: 0;
    }

    .transition-transform {
        transition: transform 0.25s ease;
    }

    .rotate-180 {
        transform: rotate(180deg);
    }

    @media (max-width: 575.98px) {
        .stat-metric-card {
            padding: 0.65rem 0.5rem;
            border-radius: 12px;
            min-height: 80px;
        }
        .stat-metric-card .stat-label-text {
            font-size: 0.58rem !important;
            letter-spacing: 0.2px !important;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1 !important;
        }
        .stat-metric-number {
            font-size: 1.25rem !important;
            letter-spacing: -0.3px;
            line-height: 1 !important;
            margin-bottom: 2px !important;
        }
        .stat-metric-number.number-compact {
            font-size: 0.95rem !important;
        }
        .stat-metric-card .stat-icon-pill {
            width: 22px;
            height: 22px;
            font-size: 0.72rem;
            border-radius: 6px;
        }
        .stat-metric-card .stat-desc-text {
            font-size: 0.58rem !important;
            line-height: 1.15;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-0 pb-5 mb-5">

    <!-- ── BREADCRUMB (DESKTOP) ── -->
    <div class="d-none d-md-block mb-3">
        <x-breadcrumb :items="['Monitoring' => null]" />
    </div>

    <!-- ── PAGE HEADER & EXPORT ACTIONS ── -->
    <div class="card border-0 shadow-sm rounded-xl mb-3 bg-white p-3 p-md-3.5">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2.5">
            <div class="d-flex align-items-center gap-2.5">
                <div class="page-title-icon-box shadow-xs flex-shrink-0" style="background: linear-gradient(135deg, #2C7FFF, #1b39da); box-shadow: 0 4px 12px rgba(44, 127, 255, 0.35); width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 1.15rem;">
                    <i class="bi bi-graph-up-arrow text-white"></i>
                </div>
                <div>
                    <h1 class="page-title-text mb-0 fw-bold text-navy" style="font-size: 1.05rem; line-height: 1.3;">
                        Laporan &amp; Analisis MTTR / SLA
                    </h1>
                    <p class="text-muted small mb-0 d-none d-md-block" style="font-size: 0.75rem; margin-top: 2px;">
                        Rekapitulasi performa perbaikan gangguan backbone dan kepatuhan SLA
                    </p>
                </div>
            </div>

            <!-- Export Buttons -->
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <a href="{{ route('reports.export.pdf', request()->query()) }}" class="btn btn-outline-danger btn-sm rounded-pill px-3 py-1 shadow-xs d-inline-flex align-items-center gap-1.5" style="font-size: 0.78rem; min-height: 32px;" title="Export PDF">
                    <i class="bi bi-file-earmark-pdf-fill"></i>
                    <span>Export PDF</span>
                </a>
                <a href="{{ route('reports.export.excel', request()->query()) }}" class="btn btn-outline-success btn-sm rounded-pill px-3 shadow-xs d-inline-flex align-items-center gap-1.5" style="font-size: 0.78rem; min-height: 32px;" title="Export Excel">
                    <i class="bi bi-file-earmark-excel-fill"></i>
                    <span>Export Excel</span>
                </a>
            </div>
        </div>
    </div>

    <!-- ── REPORT NAVIGATION TABS ── -->
    <div class="report-nav-wrapper">
        <div class="report-nav-scroll">
            <a href="{{ route('reports.index') }}" class="report-nav-item {{ request()->routeIs('reports.index') ? 'active' : '' }}">
                <i class="bi bi-graph-up"></i>
                <span>Analisis MTTR/SLA</span>
            </a>
            <a href="{{ route('reports.kpi') }}" class="report-nav-item {{ request()->routeIs('reports.kpi') ? 'active' : '' }}">
                <i class="bi bi-trophy-fill text-warning"></i>
                <span>KPI Dashboard</span>
            </a>
            <a href="{{ route('reports.shifts') }}" class="report-nav-item {{ request()->routeIs('reports.shifts') ? 'active' : '' }}">
                <i class="bi bi-arrow-left-right"></i>
                <span>Rekap Oper Shift</span>
            </a>
        </div>
    </div>

    <!-- ── SMART COLLAPSIBLE FILTER CARD ── -->
    @php
        $hasActiveFilter = request()->anyFilled(['start_date', 'end_date', 'segment', 'status', 'sla_status']);
    @endphp
    <div class="card border-0 shadow-sm rounded-xl mb-3 mb-md-4 overflow-hidden bg-white">
        <div class="card-header bg-white p-3 d-flex justify-content-between align-items-center border-bottom"
             role="button"
             data-bs-toggle="collapse"
             data-bs-target="#reportFilterCollapse"
             aria-expanded="{{ $hasActiveFilter ? 'true' : 'false' }}"
             aria-controls="reportFilterCollapse"
             style="cursor: pointer; user-select: none;">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; background: #eff6ff; color: #2563eb;">
                    <i class="bi bi-funnel-fill" style="font-size: 0.85rem;"></i>
                </div>
                <span class="fw-bold text-navy small">Filter &amp; Parameter Analisis</span>
                @if($hasActiveFilter)
                    <span class="badge rounded-pill fw-bold" style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; font-size: 0.65rem;">
                        Filter Aktif
                    </span>
                @endif
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted small d-none d-sm-inline" id="reportFilterToggleText" style="font-size: 0.72rem;">
                    {{ $hasActiveFilter ? 'Sembunyikan filter' : 'Tampilkan filter' }}
                </span>
                <i class="bi bi-chevron-down text-muted transition-transform {{ $hasActiveFilter ? 'rotate-180' : '' }}" id="filterChevron"></i>
            </div>
        </div>

        <div class="collapse {{ $hasActiveFilter ? 'show' : '' }} d-md-block" id="reportFilterCollapse">
            <div class="card-body p-3.5 p-md-4 bg-light bg-opacity-50">
                <form action="{{ route('reports.index') }}" method="GET" class="row g-3 g-md-3.5 align-items-end">
                    <!-- Dari Tanggal (col-6 on mobile, col-md-4 on tablet, col-xl-2 on desktop) -->
                    <div class="col-6 col-md-4 col-xl-2">
                        <label for="start_date" class="filter-label">Dari Tanggal</label>
                        <input type="date"
                               class="form-control form-control-sm filter-form-control"
                               id="start_date"
                               name="start_date"
                               value="{{ request('start_date') }}">
                    </div>

                    <!-- Sampai Tanggal (col-6 on mobile, col-md-4 on tablet, col-xl-2 on desktop) -->
                    <div class="col-6 col-md-4 col-xl-2">
                        <label for="end_date" class="filter-label">Sampai Tanggal</label>
                        <input type="date"
                               class="form-control form-control-sm filter-form-control"
                               id="end_date"
                               name="end_date"
                               value="{{ request('end_date') }}">
                    </div>

                    <!-- Segment Backbone (col-12 on mobile, col-md-4 on tablet, col-xl-2 on desktop) -->
                    <div class="col-12 col-md-4 col-xl-2">
                        <label for="segment" class="filter-label">Segment Backbone</label>
                        <select class="form-select form-select-sm filter-form-select" id="segment" name="segment">
                            <option value="">Semua Segment</option>
                            @foreach($segments as $seg)
                                <option value="{{ $seg->backbone_segment }}" {{ request('segment') === $seg->backbone_segment ? 'selected' : '' }}>
                                    {{ $seg->backbone_segment }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status Tiket (col-6 on mobile, col-md-4 on tablet, col-xl-2 on desktop) -->
                    <div class="col-6 col-md-4 col-xl-2">
                        <label for="status" class="filter-label">Status Tiket</label>
                        <select class="form-select form-select-sm filter-form-select" id="status" name="status">
                            <option value="">Semua Status</option>
                            <option value="OPEN" {{ request('status') === 'OPEN' ? 'selected' : '' }}>OPEN</option>
                            <option value="PROSES" {{ request('status') === 'PROSES' ? 'selected' : '' }}>PROSES</option>
                            <option value="CLOSE" {{ request('status') === 'CLOSE' ? 'selected' : '' }}>CLOSE</option>
                        </select>
                    </div>

                    <!-- Status SLA (col-6 on mobile, col-md-4 on tablet, col-xl-2 on desktop) -->
                    <div class="col-6 col-md-4 col-xl-2">
                        <label for="sla_status" class="filter-label">Status SLA</label>
                        <select class="form-select form-select-sm filter-form-select" id="sla_status" name="sla_status">
                            <option value="">Semua SLA</option>
                            <option value="TEPAT" {{ request('sla_status') === 'TEPAT' ? 'selected' : '' }}>TEPAT SLA</option>
                            <option value="LEBIH" {{ request('sla_status') === 'LEBIH' ? 'selected' : '' }}>MELEBIHI SLA</option>
                        </select>
                    </div>

                    <!-- Action Buttons -->
                    <div class="col-12 col-md-4 col-xl-2 d-flex gap-2 pt-1.5 pt-md-0">
                        <button type="submit" class="btn btn-primary btn-sm flex-fill rounded-pill shadow-xs fw-semibold d-flex align-items-center justify-content-center gap-1.5" style="background: linear-gradient(135deg, #2C7FFF 0%, #1b39da 100%); border: none; min-height: 38px;">
                            <i class="bi bi-filter"></i> <span>Terapkan</span>
                        </button>
                        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 shadow-xs d-flex align-items-center justify-content-center" title="Reset Filter" style="min-height: 38px;">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ── SUMMARY METRICS CARDS (3-COL COMPACT MOBILE & TABLET GRID) ── -->
    <div class="row g-2 g-md-3 mb-3 mb-md-4 stat-cards-grid">
        <!-- 1. Total Tiket -->
        <div class="col-4 col-md-4 col-xl-2">
            <div class="stat-metric-card border-c-total">
                <div class="d-flex justify-content-between align-items-center mb-1.5">
                    <span class="text-muted text-uppercase fw-bold stat-label-text">Total Tiket</span>
                    <div class="stat-icon-pill" style="background: #eff6ff; color: #2563eb;">
                        <i class="bi bi-ticket-perforated-fill"></i>
                    </div>
                </div>
                <div>
                    <div class="stat-metric-number fw-bold text-navy">
                        {{ $metrics['total_tiket'] }}
                    </div>
                    <small class="text-muted d-block stat-desc-text">Semua status</small>
                </div>
            </div>
        </div>

        <!-- 2. Open / Proses -->
        <div class="col-4 col-md-4 col-xl-2">
            <div class="stat-metric-card border-c-open">
                <div class="d-flex justify-content-between align-items-center mb-1.5">
                    <span class="text-muted text-uppercase fw-bold stat-label-text">Open/Proses</span>
                    <div class="stat-icon-pill" style="background: #fffbeb; color: #d97706;">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                </div>
                <div>
                    <div class="stat-metric-number fw-bold text-warning">
                        {{ $metrics['total_open'] + $metrics['total_proses'] }}
                    </div>
                    <small class="text-muted d-block stat-desc-text">Dikerjakan</small>
                </div>
            </div>
        </div>

        <!-- 3. Tiket Closed -->
        <div class="col-4 col-md-4 col-xl-2">
            <div class="stat-metric-card border-c-close">
                <div class="d-flex justify-content-between align-items-center mb-1.5">
                    <span class="text-muted text-uppercase fw-bold stat-label-text">Closed</span>
                    <div class="stat-icon-pill" style="background: #ecfdf5; color: #059669;">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                </div>
                <div>
                    <div class="stat-metric-number fw-bold text-success">
                        {{ $metrics['total_close'] }}
                    </div>
                    <small class="text-muted d-block stat-desc-text">Selesai</small>
                </div>
            </div>
        </div>

        <!-- 4. Rata-rata MTTR -->
        <div class="col-4 col-md-4 col-xl-2">
            <div class="stat-metric-card border-c-mttr">
                <div class="d-flex justify-content-between align-items-center mb-1.5">
                    <span class="text-muted text-uppercase fw-bold stat-label-text">Avg MTTR</span>
                    <div class="stat-icon-pill" style="background: #f0f9ff; color: #0284c7;">
                        <i class="bi bi-stopwatch-fill"></i>
                    </div>
                </div>
                <div>
                    <div class="stat-metric-number number-compact fw-bold text-navy text-truncate">
                        {{ $metrics['avg_mttr_formatted'] }}
                    </div>
                    <small class="text-muted d-block stat-desc-text">{{ $metrics['avg_mttr_minutes'] }} menit rata-rata</small>
                </div>
            </div>
        </div>

        <!-- 5. Tepat SLA -->
        <div class="col-4 col-md-4 col-xl-2">
            <div class="stat-metric-card border-c-tepat">
                <div class="d-flex justify-content-between align-items-center mb-1.5">
                    <span class="text-muted text-uppercase fw-bold stat-label-text">Tepat SLA</span>
                    <div class="stat-icon-pill" style="background: #f0fdfa; color: #0d9488;">
                        <i class="bi bi-shield-check"></i>
                    </div>
                </div>
                <div>
                    <div class="stat-metric-number fw-bold text-teal">
                        {{ $metrics['total_tepat'] }}
                    </div>
                    <small class="text-muted d-block stat-desc-text">&le; Target SLA</small>
                </div>
            </div>
        </div>

        <!-- 6. SLA Compliance -->
        @php
            $isSlaGood = $metrics['sla_compliance_rate'] >= 85;
        @endphp
        <div class="col-4 col-md-4 col-xl-2">
            <div class="stat-metric-card {{ $isSlaGood ? 'border-c-sla' : 'border-c-sla-danger' }}">
                <div class="d-flex justify-content-between align-items-center mb-1.5">
                    <span class="text-muted text-uppercase fw-bold stat-label-text">Compliance</span>
                    <div class="stat-icon-pill" style="background: {{ $isSlaGood ? '#ecfdf5' : '#fff1f2' }}; color: {{ $isSlaGood ? '#059669' : '#e11d48' }};">
                        <i class="bi {{ $isSlaGood ? 'bi-award-fill' : 'bi-exclamation-triangle-fill' }}"></i>
                    </div>
                </div>
                <div>
                    <div class="stat-metric-number fw-bold {{ $isSlaGood ? 'text-success' : 'text-danger' }}">
                        {{ $metrics['sla_compliance_rate'] }}%
                    </div>
                    <small class="text-muted d-block stat-desc-text">Target: &ge; 95%</small>
                </div>
            </div>
        </div>
    </div>

    <!-- ── VISUAL ANALYTICS SECTION (CHARTS) ── -->
    <div class="row g-3 g-md-4 mb-3 mb-md-4">
        <!-- MTTR Monthly Trend Chart -->
        <div class="col-12 col-lg-7">
            <div class="card border-0 shadow-sm rounded-xl p-3 p-md-4 h-100 bg-white">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="fw-bold mb-0 text-navy d-flex align-items-center gap-1.5" style="font-size: 0.95rem;">
                            <i class="bi bi-graph-up text-primary"></i> Tren Durasi MTTR Bulanan
                        </h6>
                        <small class="text-muted" style="font-size: 0.72rem;">Rata-rata waktu perbaikan (menit) tahun {{ $monthlyTrend['year'] }}</small>
                    </div>
                    <span class="badge bg-light text-navy border px-2 py-1 font-monospace" style="font-size: 0.7rem;">
                        {{ $monthlyTrend['year'] }}
                    </span>
                </div>
                <div style="height: 220px; position: relative;">
                    <canvas id="mttrTrendChart"></canvas>
                </div>
            </div>
        </div>

        <!-- SLA Compliance Breakdown Pie/Donut -->
        <div class="col-12 col-lg-5">
            <div class="card border-0 shadow-sm rounded-xl p-3 p-md-4 h-100 bg-white">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="fw-bold mb-0 text-navy d-flex align-items-center gap-1.5" style="font-size: 0.95rem;">
                            <i class="bi bi-pie-chart-fill text-teal"></i> Kepatuhan SLA
                        </h6>
                        <small class="text-muted" style="font-size: 0.72rem;">Distribusi tiket Tepat vs Melebihi SLA</small>
                    </div>
                </div>
                <div style="height: 175px; position: relative;" class="d-flex justify-content-center">
                    <canvas id="slaComplianceChart"></canvas>
                </div>
                <div class="d-flex justify-content-center gap-3 gap-sm-4 mt-3 pt-2.5 border-top">
                    <div class="text-center">
                        <span class="badge rounded-pill px-2.5 py-1 fw-bold d-inline-flex align-items-center gap-1" style="background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; font-size: 0.7rem;">
                            <span class="rounded-circle" style="width: 6px; height: 6px; background: #10b981;"></span> Tepat SLA
                        </span>
                        <div class="fw-bold text-navy mt-1" style="font-size: 0.9rem;">{{ $metrics['total_tepat'] }} Tiket</div>
                    </div>
                    <div class="text-center">
                        <span class="badge rounded-pill px-2.5 py-1 fw-bold d-inline-flex align-items-center gap-1" style="background: #fff1f2; color: #be123c; border: 1px solid #fecdd3; font-size: 0.7rem;">
                            <span class="rounded-circle" style="width: 6px; height: 6px; background: #ef4444;"></span> Melebihi SLA
                        </span>
                        <div class="fw-bold text-navy mt-1" style="font-size: 0.9rem;">{{ $metrics['total_lebih'] }} Tiket</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ── ADVANCED ANALYTICS (HOURLY HEATMAP, CATEGORY BREAKDOWN & STOP CLOCK) ── -->
    <div class="row g-3 g-md-4 mb-3 mb-md-4">
        <!-- 1. Hourly Incident Distribution Bar Chart -->
        <div class="col-12 col-lg-7">
            <div class="card border-0 shadow-sm rounded-xl p-3 p-md-4 h-100 bg-white">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="fw-bold mb-0 text-navy d-flex align-items-center gap-1.5" style="font-size: 0.95rem;">
                            <i class="bi bi-clock-history text-indigo"></i> Distribusi Jam Kejadian Gangguan (24 Jam)
                        </h6>
                        <small class="text-muted" style="font-size: 0.72rem;">Frekuensi tiket open berdasarkan waktu kejadian 00:00 - 23:00</small>
                    </div>
                    <span class="badge bg-light text-navy border px-2 py-1 font-monospace" style="font-size: 0.7rem;">
                        {{ array_sum($hourlyDistribution['counts']) }} Total Kejadian
                    </span>
                </div>
                <div style="height: 220px; position: relative;">
                    <canvas id="hourlyDistributionChart"></canvas>
                </div>
            </div>
        </div>

        <!-- 2. Category Breakdown & Stop Clock Impact Cards -->
        <div class="col-12 col-lg-5">
            <div class="row g-3 h-100">
                <!-- Tipe Penanganan (Jointing vs Manuver Core) -->
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-xl p-3 bg-white">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold mb-0 text-navy d-flex align-items-center gap-1.5" style="font-size: 0.88rem;">
                                <i class="bi bi-tools text-warning"></i> Tipe Penanganan Lapangan
                            </h6>
                            <span class="badge bg-light text-muted border" style="font-size: 0.68rem;">{{ $categoryBreakdown['total_all'] }} Tiket</span>
                        </div>
                        <div class="row g-2 align-items-center">
                            <div class="col-5" style="height: 110px; position: relative;">
                                <canvas id="categoryBreakdownChart"></canvas>
                            </div>
                            <div class="col-7">
                                <div class="d-flex flex-column gap-1.5 small" style="font-size: 0.75rem;">
                                    @forelse($categoryBreakdown['labels'] as $idx => $catLabel)
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="text-truncate" style="max-width: 120px;" title="{{ $catLabel }}">
                                            <span class="badge rounded-circle p-1 me-1" style="background: {{ $idx === 0 ? '#3b82f6' : ($idx === 1 ? '#8b5cf6' : '#10b981') }};"> </span>
                                            <span class="text-navy fw-semibold">{{ $catLabel }}</span>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-light text-dark border">{{ $categoryBreakdown['counts'][$idx] }}</span>
                                            <span class="text-muted font-monospace" style="font-size: 0.68rem;">{{ $categoryBreakdown['avg_mttr'][$idx] }}m</span>
                                        </div>
                                    </div>
                                    @empty
                                    <div class="text-muted text-center py-2">Belum ada data penanganan.</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stop Clock SLA Impact -->
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-xl p-3 bg-white">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold mb-0 text-navy d-flex align-items-center gap-1.5" style="font-size: 0.88rem;">
                                <i class="bi bi-pause-circle-fill text-danger"></i> Dampak Efisiensi Stop Clock
                            </h6>
                            <span class="badge rounded-pill fw-bold" style="background: #fff1f2; color: #e11d48; border: 1px solid #fecdd3; font-size: 0.68rem;">
                                {{ $stopClockImpact['total_events'] }} Kali Jeda
                            </span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between bg-light rounded-lg p-2 mb-2">
                            <div class="small text-muted" style="font-size: 0.72rem;">Total Waktu Terjeda (Dikecualikan dari SLA)</div>
                            <div class="fw-bold text-danger font-monospace" style="font-size: 0.88rem;">
                                {{ \App\Models\Tiket::formatDuration($stopClockImpact['total_paused_minutes']) }}
                                <span class="text-muted fw-normal" style="font-size: 0.68rem;">({{ $stopClockImpact['total_paused_minutes'] }} menit)</span>
                            </div>
                        </div>
                        @if(count($stopClockImpact['by_reason']) > 0)
                        <div class="d-flex flex-wrap gap-1.5">
                            @foreach(array_slice($stopClockImpact['by_reason'], 0, 3) as $scReason)
                            <div class="badge bg-white border text-navy fw-normal p-1.5 d-flex align-items-center gap-1" style="font-size: 0.68rem;">
                                <span class="fw-bold text-dark">{{ $scReason['label'] }}:</span>
                                <span class="text-danger font-monospace">{{ $scReason['total_minutes'] }}m ({{ $scReason['total_events'] }}x)</span>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ── TOP INCIDENT SEGMENTS & DATA TABLE ── -->
    <div class="row g-3 g-md-4 mb-4">
        <!-- Top Segment Gangguan -->
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm rounded-xl p-3 p-md-4 h-100 bg-white">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <i class="bi bi-diagram-3-fill text-warning"></i>
                    <h6 class="fw-bold mb-0 text-navy" style="font-size: 0.95rem;">Top Segment Gangguan</h6>
                </div>
                <p class="text-muted small mb-3" style="font-size: 0.73rem;">Segment backbone dengan frekuensi tiket tertinggi</p>

                @if(count($topSegments) > 0)
                    <div class="d-flex flex-column gap-3">
                        @foreach($topSegments as $ts)
                            @php
                                $maxCount = max(array_column($topSegments, 'total_gangguan')) ?: 1;
                                $pct = round(($ts['total_gangguan'] / $maxCount) * 100);
                            @endphp
                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-1 small">
                                    <span class="fw-semibold text-navy text-truncate" style="max-width: 170px;" title="{{ $ts['backbone_segment'] }}">
                                        {{ $ts['backbone_segment'] }}
                                    </span>
                                    <span class="badge bg-light text-navy border font-monospace" style="font-size: 0.68rem;">{{ $ts['total_gangguan'] }} Kejadian</span>
                                </div>
                                <div class="progress" style="height: 6px; border-radius: 4px; background: #e2e8f0;">
                                    <div class="progress-bar" role="progressbar" style="width: {{ $pct }}%; background: linear-gradient(90deg, #2C7FFF, #1b39da); border-radius: 4px;"></div>
                                </div>
                                <div class="d-flex justify-content-between text-muted mt-1" style="font-size: 0.68rem;">
                                    <span>Avg MTTR: <strong>{{ \App\Models\Tiket::formatDuration($ts['avg_mttr']) }}</strong></span>
                                    @if($ts['total_over_sla'] > 0)
                                        <span class="text-danger fw-semibold"><i class="bi bi-exclamation-circle me-0.5"></i>{{ $ts['total_over_sla'] }} over SLA</span>
                                    @else
                                        <span class="text-success fw-semibold"><i class="bi bi-check me-0.5"></i>100% SLA</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4 text-muted small">Belum ada data segmen.</div>
                @endif
            </div>
        </div>

        <!-- Detail Table -->
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm rounded-xl overflow-hidden h-100 bg-white">
                <div class="card-header bg-white py-3 px-3 px-md-4 border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold mb-0 text-navy" style="font-size: 0.95rem;"><span class="d-none d-md-inline">Tabel </span>Rekapitulasi Tiket</h6>
                        <small class="text-muted" style="font-size: 0.72rem;">Menampilkan {{ $tikets->count() }} dari total {{ $tikets->total() }} tiket</small>
                    </div>
                </div>

                <!-- ── MOBILE CARD VIEW (< 768px) ── -->
                <div class="d-block d-md-none p-2.5 p-sm-3 bg-light bg-opacity-25">
                    @forelse($tikets as $t)
                    <div class="tiket-mobile-card">
                        <!-- Top Status Stripe -->
                        <div class="{{ $t->status === 'OPEN' ? 'card-stripe-open' : ($t->status === 'PROSES' ? 'card-stripe-proses' : 'card-stripe-close') }}"></div>

                        <div class="tiket-mobile-card-body">
                            <!-- Row 1: No Tiket & Status / SLA Badges -->
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <a href="{{ route('tiket.show', $t->id) }}" class="text-decoration-none">
                                    <span class="badge bg-light text-primary font-monospace border px-2 py-1" style="font-size:0.75rem;">
                                        <i class="bi bi-ticket-perforated me-1"></i>{{ $t->no_tiket }}
                                    </span>
                                </a>
                                <div class="d-flex align-items-center gap-1">
                                    <span class="badge {{ $t->status_badge_class }}" style="font-size:0.68rem;">
                                        {{ $t->status }}
                                    </span>
                                    @if($t->sla_status)
                                    <span class="badge {{ $t->sla_badge_class }}" style="font-size:0.68rem;">
                                        {{ $t->sla_status }}
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Row 2: Impact / Judul Gangguan -->
                            <h6 class="fw-bold mb-1.5" style="font-size:0.88rem; line-height:1.35;">
                                <a href="{{ route('tiket.show', $t->id) }}" class="text-navy text-decoration-none">
                                    {{ $t->status_link_impact }}
                                </a>
                            </h6>

                            <!-- Row 3: Segment Backbone -->
                            <div class="small text-muted d-flex align-items-center gap-1 mb-2" style="font-size:0.75rem;">
                                <i class="bi bi-geo-alt-fill text-teal"></i>
                                <span class="text-truncate">{{ $t->backbone_segment }}</span>
                            </div>

                            <!-- Row 4: Meta Info & Actions -->
                            <div class="d-flex align-items-center justify-content-between pt-2 border-top" style="font-size:0.72rem;">
                                <div class="text-muted">
                                    <div>
                                        <i class="bi bi-clock me-1 text-primary"></i>
                                        Open: <strong class="text-dark">{{ $t->tanggal_open ? $t->tanggal_open->format('d/m/Y H:i') : '-' }}</strong>
                                    </div>
                                    <div class="text-success mt-0.5">
                                        <i class="bi bi-stopwatch me-1"></i> MTTR: <strong>{{ $t->formatted_mttr }}</strong>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center gap-1">
                                    <a href="{{ route('reports.export.tiket.pdf', $t->id) }}" class="btn btn-outline-danger btn-sm px-2 py-1" title="Export PDF Berita Acara">
                                        <i class="bi bi-file-earmark-pdf"></i>
                                    </a>
                                    <a href="{{ route('tiket.show', $t->id) }}" class="btn btn-cjp-teal btn-sm px-2.5 py-1 d-flex align-items-center gap-1">
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
                        <div class="small fw-semibold">Tidak ada data tiket sesuai parameter filter pencarian.</div>
                    </div>
                    @endforelse
                </div>

                <!-- ── DESKTOP TABLE VIEW (>= 768px) ── -->
                <div class="d-none d-md-block table-responsive">
                    <table class="table custom-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="text-nowrap">No. Tiket</th>
                                <th>Segment Backbone</th>
                                <th class="text-center text-nowrap">Status</th>
                                <th class="text-nowrap">Tanggal Open</th>
                                <th class="text-end text-nowrap">MTTR</th>
                                <th class="text-center text-nowrap">SLA</th>
                                <th class="text-end text-nowrap">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tikets as $t)
                            <tr>
                                <td class="text-nowrap">
                                    <a href="{{ route('tiket.show', $t->id) }}" class="fw-bold font-monospace text-decoration-none text-navy">
                                        {{ $t->no_tiket }}
                                    </a>
                                </td>
                                <td>
                                    <div class="small fw-semibold text-navy">{{ $t->backbone_segment }}</div>
                                    <small class="text-muted text-truncate d-block" style="max-width: 220px;">{{ $t->status_link_impact }}</small>
                                </td>
                                <td class="text-center text-nowrap">
                                    <span class="badge {{ $t->status_badge_class }}">
                                        {{ $t->status }}
                                    </span>
                                </td>
                                <td class="text-nowrap">
                                    <span class="small font-monospace text-muted">{{ $t->tanggal_open ? $t->tanggal_open->format('d/m/y H:i') : '-' }}</span>
                                </td>
                                <td class="text-end font-monospace small fw-bold text-nowrap">
                                    {{ $t->formatted_mttr }}
                                </td>
                                <td class="text-center text-nowrap">
                                    <span class="badge {{ $t->sla_badge_class }}">
                                        {{ $t->sla_status }}
                                    </span>
                                </td>
                                <td class="text-end text-nowrap">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('tiket.show', $t->id) }}" class="btn btn-light border btn-sm" title="Lihat Detail">
                                            <i class="bi bi-eye text-navy"></i>
                                        </a>
                                        <a href="{{ route('reports.export.tiket.pdf', $t->id) }}" class="btn btn-light border btn-sm text-danger" title="Export PDF Berita Acara">
                                            <i class="bi bi-file-earmark-pdf"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted small">
                                    Tidak ada data tiket sesuai parameter filter pencarian.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($tikets->hasPages())
                <div class="card-footer bg-white border-top py-2.5 px-3 px-md-4 d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div class="small text-muted">
                        Menampilkan <strong>{{ $tikets->firstItem() ?? 0 }}</strong> sampai <strong>{{ $tikets->lastItem() ?? 0 }}</strong> dari <strong>{{ $tikets->total() }}</strong> tiket
                    </div>
                    <div>
                        {{ $tikets->links('pagination::bootstrap-5') }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ── ROTATE FILTER CHEVRON ON COLLAPSE ──
    const filterCollapse = document.getElementById('reportFilterCollapse');
    const filterChevron = document.getElementById('filterChevron');
    const filterToggleText = document.getElementById('reportFilterToggleText');
    if (filterCollapse && filterChevron) {
        filterCollapse.addEventListener('show.bs.collapse', function() {
            filterChevron.classList.add('rotate-180');
            if (filterToggleText) filterToggleText.textContent = 'Sembunyikan filter';
        });
        filterCollapse.addEventListener('hide.bs.collapse', function() {
            filterChevron.classList.remove('rotate-180');
            if (filterToggleText) filterToggleText.textContent = 'Tampilkan filter';
        });
    }

    // ── 1. MTTR TREND LINE CHART (CHART.JS) ──
    const ctxMttr = document.getElementById('mttrTrendChart');
    if (ctxMttr) {
        new Chart(ctxMttr, {
            type: 'line',
            data: {
                labels: {!! json_encode($monthlyTrend['labels']) !!},
                datasets: [{
                    label: 'Avg MTTR (Menit)',
                    data: {!! json_encode($monthlyTrend['mttr_minutes']) !!},
                    borderColor: '#2C7FFF',
                    backgroundColor: 'rgba(44, 127, 255, 0.1)',
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#1b39da',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const val = context.parsed.y;
                                const hours = Math.floor(val / 60);
                                const mins = val % 60;
                                const dur = (hours > 0 ? `${hours} jam ` : '') + (mins > 0 || hours === 0 ? `${mins} menit` : '');
                                return `Avg MTTR: ${dur} (${val} menit)`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            callback: function(value) {
                                return value + ' mnt';
                            }
                        }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    }

    // ── 2. SLA COMPLIANCE DOUGHNUT CHART ──
    const ctxSla = document.getElementById('slaComplianceChart');
    if (ctxSla) {
        new Chart(ctxSla, {
            type: 'doughnut',
            data: {
                labels: ['Tepat SLA', 'Melebihi SLA'],
                datasets: [{
                    data: [{{ $metrics['total_tepat'] }}, {{ $metrics['total_lebih'] }}],
                    backgroundColor: ['#10b981', '#ef4444'],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }

    // ── 3. HOURLY DISTRIBUTION BAR CHART (24 HOURS) ──
    const ctxHourly = document.getElementById('hourlyDistributionChart');
    if (ctxHourly) {
        new Chart(ctxHourly, {
            type: 'bar',
            data: {
                labels: {!! json_encode($hourlyDistribution['labels']) !!},
                datasets: [{
                    label: 'Jumlah Insiden',
                    data: {!! json_encode($hourlyDistribution['counts']) !!},
                    backgroundColor: 'rgba(99, 102, 241, 0.75)',
                    borderColor: '#6366f1',
                    borderWidth: 1.5,
                    borderRadius: 4,
                    hoverBackgroundColor: '#4f46e5',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `Insiden: ${context.parsed.y} tiket`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, precision: 0 },
                        grid: { color: '#f1f5f9' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { maxRotation: 45, minRotation: 0, font: { size: 9 } }
                    }
                }
            }
        });
    }

    // ── 4. CATEGORY BREAKDOWN DOUGHNUT CHART ──
    const ctxCat = document.getElementById('categoryBreakdownChart');
    if (ctxCat) {
        new Chart(ctxCat, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($categoryBreakdown['labels']) !!},
                datasets: [{
                    data: {!! json_encode($categoryBreakdown['counts']) !!},
                    backgroundColor: ['#3b82f6', '#8b5cf6', '#10b981', '#f59e0b', '#ec4899'],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }
});
</script>
@endpush
