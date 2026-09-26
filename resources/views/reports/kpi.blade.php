@extends('layouts.app')

@section('title', 'Dashboard KPI & Evaluasi Kinerja')

@push('styles')
<style>
    .kpi-stat-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 0.85rem 1rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .kpi-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.07);
    }
    .kpi-stat-card::before {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 3px;
    }
    .kpi-stat-card.border-c-sla::before { background: linear-gradient(90deg, #10b981, #059669); }
    .kpi-stat-card.border-c-resp::before { background: linear-gradient(90deg, #3b82f6, #1d4ed8); }
    .kpi-stat-card.border-c-mttr::before { background: linear-gradient(90deg, #0d9488, #0f766e); }
    .kpi-stat-card.border-c-stop::before { background: linear-gradient(90deg, #f59e0b, #d97706); }
    .kpi-stat-card.border-c-verif::before { background: linear-gradient(90deg, #8b5cf6, #6d28d9); }

    .kpi-stat-label {
        font-size: 0.68rem;
        font-weight: 700;
        color: #64748b;
        letter-spacing: 0.35px;
        text-transform: uppercase;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .kpi-stat-icon {
        width: 26px;
        height: 26px;
        border-radius: 7px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        flex-shrink: 0;
    }
    .kpi-stat-val {
        font-size: 1.12rem;
        font-weight: 700;
        color: #0f172a;
        letter-spacing: -0.3px;
        line-height: 1.25;
        white-space: nowrap;
    }
    .kpi-stat-desc {
        font-size: 0.72rem;
        color: #64748b;
        margin-top: 0.2rem;
        line-height: 1.25;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .kpi-score-badge {
        font-size: 1.15rem;
        font-weight: 800;
        padding: 0.35rem 0.75rem;
        border-radius: 12px;
        letter-spacing: -0.5px;
    }
    .kpi-rank-circle {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 0.85rem;
        flex-shrink: 0;
    }
    .kpi-rank-1 { background: linear-gradient(135deg, #fef08a, #facc15); color: #854d0e; box-shadow: 0 2px 8px rgba(250, 204, 21, 0.4); }
    .kpi-rank-2 { background: linear-gradient(135deg, #f1f5f9, #cbd5e1); color: #334155; }
    .kpi-rank-3 { background: linear-gradient(135deg, #fed7aa, #fdba74); color: #9a3412; }
    .kpi-rank-other { background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0; }

    .nav-pills-kpi .nav-link {
        border-radius: 10px;
        color: #64748b;
        font-weight: 600;
        font-size: 0.88rem;
        padding: 0.5rem 1.15rem;
        transition: all 0.2s;
    }
    .nav-pills-kpi .nav-link.active {
        background: linear-gradient(135deg, #07152b 0%, #0c2147 100%);
    /* ── REPORT HERO HEADER (DASHBOARD-STYLE DARK TECH GRADIENT) ── */
    .report-hero-header {
        background: linear-gradient(145deg, #071530 0%, #0d2352 55%, #133070 100%);
        border-radius: var(--neu-radius-lg, 16px);
        padding: 1.15rem 1.4rem;
        margin-bottom: 1.15rem;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.08);
        box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.12);
        color: #ffffff;
    }
    .report-hero-header::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image:
            linear-gradient(rgba(44, 127, 255, 0.08) 1px, transparent 1px),
            linear-gradient(90deg, rgba(44, 127, 255, 0.08) 1px, transparent 1px);
        background-size: 28px 28px;
        pointer-events: none;
    }
    .report-hero-header > * { position: relative; z-index: 2; }

    .btn-report-export-pdf {
        background: rgba(239, 68, 68, 0.18);
        color: #ffffff !important;
        border: 1px solid rgba(248, 113, 113, 0.35);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        transition: all 0.2s ease;
    }
    .btn-report-export-pdf:hover {
        background: rgba(239, 68, 68, 0.35);
        border-color: rgba(248, 113, 113, 0.6);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
    }

    .btn-report-export-excel {
        background: rgba(34, 197, 94, 0.18);
        color: #ffffff !important;
        border: 1px solid rgba(74, 222, 128, 0.35);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        transition: all 0.2s ease;
    }
    .btn-report-export-excel:hover {
        background: rgba(34, 197, 94, 0.35);
        border-color: rgba(74, 222, 128, 0.6);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(34, 197, 94, 0.3);
    }

    @media (max-width: 575.98px) {
        .report-hero-header {
            padding: 1rem 1rem !important;
            border-radius: 14px !important;
            margin-bottom: 1rem !important;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-0 pb-5 mb-5">

    <!-- ── BREADCRUMB ── -->
    <div class="d-none d-md-block mb-3">
        <x-breadcrumb :items="[
            'Monitoring' => route('reports.index'),
            'Dashboard KPI' => null
        ]" />
    </div>

    <!-- ── PAGE HEADER & EXPORT ACTIONS (DASHBOARD-STYLE DARK TECH GRADIENT) ── -->
    <div class="report-hero-header">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2.5">
            <div class="d-flex align-items-center gap-2.5">
                <div class="page-title-icon-box shadow-xs flex-shrink-0" style="background: linear-gradient(135deg, #2563eb, #1e40af); box-shadow: 0 4px 12px rgba(37, 99, 235, 0.45); border: 1px solid rgba(255, 255, 255, 0.2); width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 1.15rem;">
                    <i class="bi bi-award-fill"></i>
                </div>
                <div>
                    <h1 class="page-title-text mb-0 fw-bold text-white" style="font-size: 1.15rem; line-height: 1.3; letter-spacing: -0.2px;">
                        Key Performance Indicators (KPI) &amp; Evaluasi Kinerja
                    </h1>
                    <p class="small mb-0 d-none d-md-block" style="color: rgba(186, 214, 235, 0.85); font-size: 0.76rem; margin-top: 2px;">
                        Monitoring kepatuhan SLA, kecepatan respon, interval 30 menit, dan durasi verifikasi
                    </p>
                </div>
            </div>

            <!-- Export Buttons -->
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <a href="{{ route('reports.export.kpi.pdf', request()->query()) }}" class="btn btn-report-export-pdf btn-sm rounded-pill px-3.5 py-1.5 shadow-xs d-inline-flex align-items-center gap-1.5" style="font-size: 0.78rem; min-height: 32px;" title="Export PDF">
                    <i class="bi bi-file-earmark-pdf-fill" style="color: #fca5a5;"></i>
                    <span>Export PDF</span>
                </a>
                <a href="{{ route('reports.export.kpi.excel', request()->query()) }}" class="btn btn-report-export-excel btn-sm rounded-pill px-3.5 py-1.5 shadow-xs d-inline-flex align-items-center gap-1.5" style="font-size: 0.78rem; min-height: 32px;" title="Export Excel">
                    <i class="bi bi-file-earmark-excel-fill" style="color: #86efac;"></i>
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

    <!-- ── DATE FILTER BAR ── -->
    <div class="card border-0 shadow-sm rounded-xl mb-3 bg-white">
        <div class="card-body p-3">
            <form action="{{ route('reports.kpi') }}" method="GET" class="row g-2 align-items-end">
                <div class="col-12 col-sm-4 col-md-3">
                    <label class="form-label small fw-semibold text-navy mb-1">Dari Tanggal</label>
                    <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $startDate }}">
                </div>
                <div class="col-12 col-sm-4 col-md-3">
                    <label class="form-label small fw-semibold text-navy mb-1">Sampai Tanggal</label>
                    <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $endDate }}">
                </div>
                <div class="col-12 col-sm-4 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3.5 w-100 fw-semibold" style="background: linear-gradient(135deg, #2563eb, #1d4ed8);">
                        <i class="bi bi-funnel-fill me-1"></i> Filter KPI
                    </button>
                    <a href="{{ route('reports.kpi') }}" class="btn btn-light btn-sm rounded-pill px-3" title="Reset Filter">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- ── 5 EXECUTIVE KPI METRIC CARDS ── -->
    <div class="row g-2 g-md-2.5 mb-3.5">
        <!-- 1. Kepatuhan SLA -->
        <div class="col-12 col-sm-6 col-xl">
            <div class="kpi-stat-card border-c-sla">
                <div class="d-flex align-items-center justify-content-between mb-1.5">
                    <span class="kpi-stat-label">KEPATUHAN SLA</span>
                    <div class="kpi-stat-icon bg-success-subtle text-success">
                        <i class="bi bi-shield-check"></i>
                    </div>
                </div>
                <div>
                    <div class="kpi-stat-val">{{ $executiveKpi['sla_compliance_rate'] }}%</div>
                    <div class="kpi-stat-desc">
                        <span class="text-success fw-bold">{{ $executiveKpi['total_tepat_sla'] }}</span> tepat &bull;
                        <span class="text-danger fw-bold">{{ $executiveKpi['total_lebih_sla'] }}</span> lewat SLA
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Rata-Rata Response Time -->
        <div class="col-12 col-sm-6 col-xl">
            <div class="kpi-stat-card border-c-resp">
                <div class="d-flex align-items-center justify-content-between mb-1.5">
                    <span class="kpi-stat-label">AVG RESPONSE TIME</span>
                    <div class="kpi-stat-icon bg-primary-subtle text-primary">
                        <i class="bi bi-lightning-charge-fill"></i>
                    </div>
                </div>
                <div>
                    <div class="kpi-stat-val">{{ $executiveKpi['formatted_avg_response'] }}</div>
                    <div class="kpi-stat-desc">
                        Waktu respon teknisi pertama
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Rata-Rata MTTR -->
        <div class="col-12 col-sm-6 col-xl">
            <div class="kpi-stat-card border-c-mttr">
                <div class="d-flex align-items-center justify-content-between mb-1.5">
                    <span class="kpi-stat-label">AVG MTTR (BERSIH)</span>
                    <div class="kpi-stat-icon bg-teal-subtle text-teal">
                        <i class="bi bi-stopwatch-fill"></i>
                    </div>
                </div>
                <div>
                    <div class="kpi-stat-val">{{ $executiveKpi['formatted_avg_mttr'] }}</div>
                    <div class="kpi-stat-desc">
                        Dari {{ $executiveKpi['total_closed'] }} tiket diselesaikan
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Total Stop Clock -->
        <div class="col-12 col-sm-6 col-xl">
            <div class="kpi-stat-card border-c-stop">
                <div class="d-flex align-items-center justify-content-between mb-1.5">
                    <span class="kpi-stat-label">TOTAL STOP CLOCK</span>
                    <div class="kpi-stat-icon bg-warning-subtle text-warning">
                        <i class="bi bi-pause-circle-fill"></i>
                    </div>
                </div>
                <div>
                    <div class="kpi-stat-val">{{ $executiveKpi['formatted_total_stop_clock'] }}</div>
                    <div class="kpi-stat-desc">
                        Jeda waktu SLA yang disahkan
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. Durasi Verifikasi NOC -->
        <div class="col-12 col-sm-6 col-xl">
            <div class="kpi-stat-card border-c-verif">
                <div class="d-flex align-items-center justify-content-between mb-1.5">
                    <span class="kpi-stat-label">AVG VERIFIKASI NOC</span>
                    <div class="kpi-stat-icon bg-purple-subtle text-purple">
                        <i class="bi bi-check2-all"></i>
                    </div>
                </div>
                <div>
                    <div class="kpi-stat-val">{{ $executiveKpi['formatted_avg_verification'] }}</div>
                    <div class="kpi-stat-desc">
                        Closing Awal &rarr; Closing Akhir
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ── TABS: KPI TEKNISI VS KPI HELPDESK ── -->
    <div class="card border-0 shadow-sm rounded-xl overflow-hidden bg-white mb-4">
        <div class="card-header bg-white p-3 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
            <ul class="nav nav-pills nav-pills-kpi" id="kpiTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="teknisi-kpi-tab" data-bs-toggle="pill" data-bs-target="#teknisi-kpi-pane" type="button" role="tab">
                        <i class="bi bi-tools me-1.5"></i> Leaderboard KPI Teknisi Lapangan ({{ $teknisiKpi->count() }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="helpdesk-kpi-tab" data-bs-toggle="pill" data-bs-target="#helpdesk-kpi-pane" type="button" role="tab">
                        <i class="bi bi-headset me-1.5"></i> KPI HelpDesk / NOC ({{ $helpdeskKpi->count() }})
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body p-0">
            <div class="tab-content" id="kpiTabContent">

                <!-- ════ TAB 1: KPI TEKNISI LAPANGAN ════ -->
                <div class="tab-pane fade show active" id="teknisi-kpi-pane" role="tabpanel">
                    <!-- 1A. Desktop Table View (>= md) -->
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3 py-3" style="width: 50px;">Rank</th>
                                    <th class="py-3">Nama Teknisi</th>
                                    <th class="py-3 text-center">Tiket Selesai</th>
                                    <th class="py-3 text-center">Avg Respon</th>
                                    <th class="py-3 text-center">Kepatuhan 30 Mnt</th>
                                    <th class="py-3 text-center">Avg MTTR</th>
                                    <th class="py-3 text-center">SLA Compliance</th>
                                    <th class="pe-3 py-3 text-end">Score KPI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($teknisiKpi as $index => $tek)
                                <tr>
                                    <td class="ps-3">
                                        <div class="kpi-rank-circle {{ $index === 0 ? 'kpi-rank-1' : ($index === 1 ? 'kpi-rank-2' : ($index === 2 ? 'kpi-rank-3' : 'kpi-rank-other')) }}">
                                            {{ $index + 1 }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-navy">{{ $tek['name'] }}</div>
                                        <div class="text-muted small" style="font-size:0.75rem;">{{ $tek['email'] }}</div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-navy border font-monospace">{{ $tek['total_resolved'] }} Tiket</span>
                                    </td>
                                    <td class="text-center font-monospace">
                                        {{ $tek['formatted_avg_response'] }}
                                    </td>
                                    <td class="text-center">
                                        <span class="badge {{ $tek['interval_compliance_rate'] >= 85 ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }} border px-2 py-1">
                                            {{ $tek['interval_compliance_rate'] }}%
                                        </span>
                                        <div class="text-muted" style="font-size:0.7rem;">{{ $tek['overdue_updates'] }} telat update</div>
                                    </td>
                                    <td class="text-center font-monospace fw-semibold text-navy">
                                        {{ $tek['formatted_avg_mttr'] }}
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex align-items-center justify-content-center gap-1.5">
                                            <div class="progress flex-grow-1" style="height: 6px; max-width: 70px;">
                                                <div class="progress-bar {{ $tek['sla_compliance_rate'] >= 90 ? 'bg-success' : ($tek['sla_compliance_rate'] >= 75 ? 'bg-warning' : 'bg-danger') }}"
                                                     style="width: {{ $tek['sla_compliance_rate'] }}%"></div>
                                            </div>
                                            <span class="fw-bold small font-monospace">{{ $tek['sla_compliance_rate'] }}%</span>
                                        </div>
                                    </td>
                                    <td class="pe-3 text-end">
                                        <span class="kpi-score-badge {{ $tek['kpi_score'] >= 85 ? 'bg-success text-white' : ($tek['kpi_score'] >= 70 ? 'bg-primary text-white' : 'bg-warning text-dark') }}">
                                            {{ $tek['kpi_score'] }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">
                                        Belum ada data tiket untuk evaluasi teknisi pada rentang waktu ini.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- 1B. Mobile Card View (< md) -->
                    <div class="d-md-none p-2.5">
                        @forelse($teknisiKpi as $index => $tek)
                        <div class="kpi-mobile-card">
                            <div class="d-flex align-items-center justify-content-between mb-2.5 pb-2 border-bottom">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="kpi-rank-circle {{ $index === 0 ? 'kpi-rank-1' : ($index === 1 ? 'kpi-rank-2' : ($index === 2 ? 'kpi-rank-3' : 'kpi-rank-other')) }}" style="width: 28px; height: 28px; font-size: 0.78rem;">
                                        {{ $index + 1 }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-navy" style="font-size: 0.9rem;">{{ $tek['name'] }}</div>
                                        <div class="text-muted" style="font-size: 0.7rem;">{{ $tek['email'] }}</div>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <span class="kpi-score-badge {{ $tek['kpi_score'] >= 85 ? 'bg-success text-white' : ($tek['kpi_score'] >= 70 ? 'bg-primary text-white' : 'bg-warning text-dark') }}" style="font-size: 0.92rem; padding: 0.2rem 0.55rem;">
                                        {{ $tek['kpi_score'] }}
                                    </span>
                                    <div class="text-muted" style="font-size: 0.65rem; margin-top: 2px;">Score KPI</div>
                                </div>
                            </div>

                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="kpi-mobile-metric-tile">
                                        <div class="text-muted" style="font-size: 0.68rem;">Tiket Selesai</div>
                                        <div class="fw-bold text-navy font-monospace" style="font-size: 0.85rem;">{{ $tek['total_resolved'] }} Tiket</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="kpi-mobile-metric-tile">
                                        <div class="text-muted" style="font-size: 0.68rem;">Avg Respon</div>
                                        <div class="fw-bold text-navy font-monospace" style="font-size: 0.85rem;">{{ $tek['formatted_avg_response'] }}</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="kpi-mobile-metric-tile">
                                        <div class="text-muted" style="font-size: 0.68rem;">Kepatuhan 30 Mnt</div>
                                        <div class="d-flex align-items-center gap-1 mt-0.5">
                                            <span class="badge {{ $tek['interval_compliance_rate'] >= 85 ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }} border px-1.5 py-0.5" style="font-size: 0.72rem;">
                                                {{ $tek['interval_compliance_rate'] }}%
                                            </span>
                                            <span class="text-muted" style="font-size: 0.65rem;">({{ $tek['overdue_updates'] }} telat)</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="kpi-mobile-metric-tile">
                                        <div class="text-muted" style="font-size: 0.68rem;">Avg MTTR</div>
                                        <div class="fw-bold text-navy font-monospace" style="font-size: 0.85rem;">{{ $tek['formatted_avg_mttr'] }}</div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="kpi-mobile-metric-tile">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="text-muted" style="font-size: 0.68rem;">SLA Compliance</span>
                                            <span class="fw-bold font-monospace" style="font-size: 0.75rem;">{{ $tek['sla_compliance_rate'] }}%</span>
                                        </div>
                                        <div class="progress" style="height: 5px;">
                                            <div class="progress-bar {{ $tek['sla_compliance_rate'] >= 90 ? 'bg-success' : ($tek['sla_compliance_rate'] >= 75 ? 'bg-warning' : 'bg-danger') }}"
                                                 style="width: {{ $tek['sla_compliance_rate'] }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-4 text-muted small">
                            Belum ada data tiket untuk evaluasi teknisi pada rentang waktu ini.
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- ════ TAB 2: KPI HELPDESK NOC ════ -->
                <div class="tab-pane fade" id="helpdesk-kpi-pane" role="tabpanel">
                    <!-- 2A. Desktop Table View (>= md) -->
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3 py-3" style="width: 50px;">#</th>
                                    <th class="py-3">Nama Petugas NOC</th>
                                    <th class="py-3">Role</th>
                                    <th class="py-3 text-center">Tiket Diterbitkan (Open)</th>
                                    <th class="py-3 text-center">Tiket Diverifikasi (Close)</th>
                                    <th class="py-3 text-center">Rata-Rata Waktu Verifikasi</th>
                                    <th class="pe-3 py-3 text-end">Kepatuhan SLA Tiket</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($helpdeskKpi as $index => $hd)
                                <tr>
                                    <td class="ps-3 font-monospace text-muted">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="fw-bold text-navy">{{ $hd['name'] }}</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-navy border font-monospace">{{ strtoupper($hd['role']) }}</span>
                                    </td>
                                    <td class="text-center font-monospace">
                                        <strong>{{ $hd['total_created'] }}</strong> tiket
                                    </td>
                                    <td class="text-center font-monospace">
                                        <strong class="text-success">{{ $hd['total_closed'] }}</strong> tiket
                                    </td>
                                    <td class="text-center font-monospace">
                                        <span class="badge bg-purple-subtle text-purple border px-2 py-1">
                                            {{ $hd['formatted_avg_verification'] }}
                                        </span>
                                    </td>
                                    <td class="pe-3 text-end font-monospace fw-bold {{ $hd['sla_compliance_rate'] >= 90 ? 'text-success' : 'text-danger' }}">
                                        {{ $hd['sla_compliance_rate'] }}%
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        Belum ada data tiket untuk evaluasi HelpDesk pada rentang waktu ini.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- 2B. Mobile Card View (< md) -->
                    <div class="d-md-none p-2.5">
                        @forelse($helpdeskKpi as $index => $hd)
                        <div class="kpi-mobile-card">
                            <div class="d-flex align-items-center justify-content-between mb-2.5 pb-2 border-bottom">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-light border d-flex align-items-center justify-content-center font-monospace fw-bold text-muted" style="width: 28px; height: 28px; font-size: 0.78rem;">
                                        {{ $index + 1 }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-navy" style="font-size: 0.9rem;">{{ $hd['name'] }}</div>
                                        <span class="badge bg-light text-navy border font-monospace" style="font-size: 0.65rem;">{{ strtoupper($hd['role']) }}</span>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold font-monospace {{ $hd['sla_compliance_rate'] >= 90 ? 'text-success' : 'text-danger' }}" style="font-size: 1rem;">
                                        {{ $hd['sla_compliance_rate'] }}%
                                    </div>
                                    <div class="text-muted" style="font-size: 0.65rem;">SLA Tiket</div>
                                </div>
                            </div>

                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="kpi-mobile-metric-tile">
                                        <div class="text-muted" style="font-size: 0.68rem;">Tiket Open</div>
                                        <div class="fw-bold text-navy font-monospace" style="font-size: 0.85rem;">{{ $hd['total_created'] }} Tiket</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="kpi-mobile-metric-tile">
                                        <div class="text-muted" style="font-size: 0.68rem;">Tiket Verified / Close</div>
                                        <div class="fw-bold text-success font-monospace" style="font-size: 0.85rem;">{{ $hd['total_closed'] }} Tiket</div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="kpi-mobile-metric-tile d-flex justify-content-between align-items-center">
                                        <div class="text-muted" style="font-size: 0.68rem;">Rata-Rata Waktu Verifikasi</div>
                                        <span class="badge bg-purple-subtle text-purple border px-2 py-0.5 font-monospace" style="font-size: 0.75rem;">
                                            {{ $hd['formatted_avg_verification'] }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-4 text-muted small">
                            Belum ada data tiket untuk evaluasi HelpDesk pada rentang waktu ini.
                        </div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>
@endsection
