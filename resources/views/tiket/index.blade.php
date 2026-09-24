@extends('layouts.app')

@section('title', 'Daftar Tiket Gangguan')

@section('content')
@php
    $isTugasSaya = ($filters['status'] ?? '') === 'AKTIF' || request('view') === 'tugas_saya';
@endphp

<div class="container-fluid px-0 pb-5 mb-5">

    <!-- ── BREADCRUMB (DESKTOP ONLY) ── -->
    <div class="d-none d-md-block">
        @if($isTugasSaya)
            <x-breadcrumb :items="[(auth()->user()->hasRole('teknis') ? 'History' : 'Daftar Tiket Gangguan') => route('tiket.index'), 'Tugas Saya' => null]" />
        @elseif(auth()->user()->hasRole('teknis'))
            <x-breadcrumb :items="['History' => null]" />
        @else
            <x-breadcrumb :items="['Daftar Tiket Gangguan' => null]" />
        @endif
    </div>

    <!-- ── PAGE HEADER (ELEGANT & CLEAN) ── -->
    <div class="d-flex justify-content-between align-items-center mb-3 page-header-row p-3 px-md-3.5 rounded-xl bg-white shadow-xs border border-light">
        <div class="d-flex align-items-center gap-2.5 overflow-hidden">
            <div class="page-title-icon-box shadow-xs" style="{{ $isTugasSaya ? 'background: linear-gradient(135deg, #f59e0b, #d97706); box-shadow: 0 4px 12px rgba(245, 158, 11, 0.35);' : (auth()->user()->hasRole('teknis') ? 'background: linear-gradient(135deg, #2C7FFF, #1b39da); box-shadow: 0 4px 12px rgba(44, 127, 255, 0.35);' : '') }}">
                <i class="bi {{ $isTugasSaya ? 'bi-tools' : (auth()->user()->hasRole('teknis') ? 'bi-clock-history' : 'bi-ticket-detailed-fill') }}"></i>
            </div>
            <div class="overflow-hidden">
                <h1 class="page-title-text mb-0 text-truncate fw-bold text-navy" style="font-size:1.1rem;">
                    @if($isTugasSaya)
                        Tugas Saya <span class="badge bg-warning bg-opacity-25 text-warning border border-warning border-opacity-50 font-monospace px-2 py-0.5" style="font-size:0.68rem; font-weight:700;">AKTIF</span>
                        <span class="visually-hidden">Daftar Tiket Gangguan Backbone</span>
                    @elseif(auth()->user()->hasRole('teknis'))
                        History Tiket Gangguan
                        <span class="visually-hidden">Daftar Tiket Gangguan Backbone</span>
                    @else
                        Daftar Tiket Gangguan Backbone
                    @endif
                </h1>
                <p class="text-muted small mb-0 d-none d-md-block" style="font-size:0.75rem; margin-top:2px;">
                    @if($isTugasSaya)
                        Daftar penugasan gangguan backbone yang sedang aktif ditangani
                    @elseif(auth()->user()->hasRole('teknis'))
                        Riwayat penanganan tiket gangguan jaringan yang sudah closing
                    @else
                        Monitoring &amp; penanganan insiden backbone PT MSN
                    @endif
                </p>
            </div>
        </div>
        <div class="flex-shrink-0 ms-2">
            @if($isTugasSaya)
            <a href="{{ route('tiket.index') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1.5 shadow-xs rounded-pill px-3" title="{{ auth()->user()->hasRole('teknis') ? 'Lihat History' : 'Kembali ke Semua Tiket' }}">
                <i class="bi {{ auth()->user()->hasRole('teknis') ? 'bi-clock-history' : 'bi-list-ul' }}"></i>
                <span class="d-none d-sm-inline">{{ auth()->user()->hasRole('teknis') ? 'History' : 'Semua Tiket' }}</span>
                <span class="d-inline d-sm-none">{{ auth()->user()->hasRole('teknis') ? 'History' : 'Semua' }}</span>
            </a>
            @elseif(auth()->user()->hasRole(['admin', 'helpdesk']))
            <a href="{{ route('tiket.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-1.5 shadow-xs rounded-pill px-3" style="background: linear-gradient(135deg, #2C7FFF 0%, #1b39da 100%); border: none;">
                <i class="bi bi-plus-lg"></i>
                <span class="d-none d-sm-inline">Open Tiket Baru</span>
                <span class="d-inline d-sm-none">Tiket</span>
            </a>
            @endif
        </div>
    </div>

    @if(!$isTugasSaya)
    <!-- ── STATS SUMMARY CARDS (3-COLUMN MOBILE LAYOUT) ── -->
    <div class="row g-2 g-md-3 mb-3 stat-cards-grid">
        @if(auth()->user()->hasRole('teknis'))
        <!-- Khusus Teknis: Hanya tampilkan Total History Closing, <= SLA, dan > SLA (Tidak ada card OPEN & PROSES) -->
        <!-- 1. Total History -->
        <div class="col-4 col-md-4 col-lg-4">
            <a href="{{ route('tiket.index') }}" 
               class="stat-card-pro stat-theme-close h-100" 
               title="Total Riwayat Tiket Selesai">
                <div class="stat-pro-top">
                    <span class="stat-pro-label">TOTAL HISTORY</span>
                    <div class="stat-pro-icon">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                </div>
                <div class="stat-pro-number">
                    {{ $summary['close'] }}
                    <span class="stat-pro-unit text-success">tiket</span>
                </div>
                <div class="stat-pro-desc text-success">Tiket closing</div>
                <i class="bi bi-check-circle stat-watermark"></i>
            </a>
        </div>

        <!-- 2. Sesuai SLA -->
        <div class="col-4 col-md-4 col-lg-4">
            <a href="{{ route('tiket.index', ['sla_status' => 'TEPAT']) }}" 
               class="stat-card-pro stat-theme-tepat h-100" 
               title="Filter Tiket Sesuai SLA">
                <div class="stat-pro-top">
                    <span class="stat-pro-label">&le; SLA</span>
                    <div class="stat-pro-icon">
                        <i class="bi bi-shield-check"></i>
                    </div>
                </div>
                <div class="stat-pro-number">
                    {{ $summary['tepat_sla'] }}
                    <span class="stat-pro-unit text-teal">tiket</span>
                </div>
                <div class="stat-pro-desc text-teal">&le; Target SLA</div>
                <i class="bi bi-shield-check stat-watermark"></i>
            </a>
        </div>

        <!-- 3. Melebihi SLA -->
        <div class="col-4 col-md-4 col-lg-4">
            <a href="{{ route('tiket.index', ['sla_status' => 'LEBIH']) }}" 
               class="stat-card-pro stat-theme-lebih h-100" 
               title="Filter Tiket Melebihi SLA">
                <div class="stat-pro-top">
                    <span class="stat-pro-label">&gt; SLA</span>
                    <div class="stat-pro-icon">
                        <i class="bi bi-stopwatch-fill"></i>
                    </div>
                </div>
                <div class="stat-pro-number">
                    {{ $summary['lebih_sla'] }}
                    <span class="stat-pro-unit text-danger">tiket</span>
                </div>
                <div class="stat-pro-desc text-danger">&gt; Target SLA</div>
                <i class="bi bi-stopwatch stat-watermark"></i>
            </a>
        </div>
        @else
        <!-- 1. Total Tiket -->
        <div class="col-4 col-md-4 col-lg-2">
            <a href="{{ route('tiket.index') }}" 
               class="stat-card-pro stat-theme-total h-100" 
               title="Lihat Semua Tiket">
                <div class="stat-pro-top">
                    <span class="stat-pro-label">Total</span>
                    <div class="stat-pro-icon">
                        <i class="bi bi-collection-fill"></i>
                    </div>
                </div>
                <div class="stat-pro-number">
                    {{ $summary['total'] }}
                    <span class="stat-pro-unit text-muted">tiket</span>
                </div>
                <div class="stat-pro-desc text-muted">Keseluruhan</div>
                <i class="bi bi-collection stat-watermark"></i>
            </a>
        </div>

        <!-- 2. Status OPEN -->
        <div class="col-4 col-md-4 col-lg-2">
            <a href="{{ route('tiket.index', ['status' => 'OPEN']) }}" 
               class="stat-card-pro stat-theme-open h-100" 
               title="Filter Tiket OPEN">
                <div class="stat-pro-top">
                    <span class="stat-pro-label">OPEN</span>
                    <div class="stat-pro-icon">
                        <i class="bi bi-exclamation-circle-fill"></i>
                    </div>
                </div>
                <div class="stat-pro-number">
                    {{ $summary['open'] }}
                    <span class="stat-pro-unit text-danger">tiket</span>
                </div>
                <div class="stat-pro-desc text-danger">Belum ditangani</div>
                <i class="bi bi-exclamation-circle stat-watermark"></i>
            </a>
        </div>

        <!-- 3. Status PROSES -->
        <div class="col-4 col-md-4 col-lg-2">
            <a href="{{ route('tiket.index', ['status' => 'PROSES']) }}" 
               class="stat-card-pro stat-theme-proses h-100" 
               title="Filter Tiket PROSES">
                <div class="stat-pro-top">
                    <span class="stat-pro-label">PROSES</span>
                    <div class="stat-pro-icon">
                        <i class="bi bi-arrow-repeat"></i>
                    </div>
                </div>
                <div class="stat-pro-number">
                    {{ $summary['proses'] }}
                    <span class="stat-pro-unit text-warning">tiket</span>
                </div>
                <div class="stat-pro-desc text-warning">Sedang perbaikan</div>
                <i class="bi bi-arrow-repeat stat-watermark"></i>
            </a>
        </div>

        <!-- 4. Status CLOSE -->
        <div class="col-4 col-md-4 col-lg-2">
            <a href="{{ route('tiket.index', ['status' => 'CLOSE']) }}" 
               class="stat-card-pro stat-theme-close h-100" 
               title="Filter Tiket CLOSE">
                <div class="stat-pro-top">
                    <span class="stat-pro-label">CLOSE</span>
                    <div class="stat-pro-icon">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                </div>
                <div class="stat-pro-number">
                    {{ $summary['close'] }}
                    <span class="stat-pro-unit text-success">tiket</span>
                </div>
                <div class="stat-pro-desc text-success">Selesai &amp; normal</div>
                <i class="bi bi-check-circle stat-watermark"></i>
            </a>
        </div>

        <!-- 5. Melebihi SLA -->
        <div class="col-4 col-md-4 col-lg-2">
            <a href="{{ route('tiket.index', ['sla_status' => 'LEBIH']) }}" 
               class="stat-card-pro stat-theme-lebih h-100" 
               title="Filter Tiket Melebihi SLA">
                <div class="stat-pro-top">
                    <span class="stat-pro-label">&gt; SLA</span>
                    <div class="stat-pro-icon">
                        <i class="bi bi-stopwatch-fill"></i>
                    </div>
                </div>
                <div class="stat-pro-number">
                    {{ $summary['lebih_sla'] }}
                    <span class="stat-pro-unit text-danger">tiket</span>
                </div>
                <div class="stat-pro-desc text-danger">&gt; Target SLA</div>
                <i class="bi bi-stopwatch stat-watermark"></i>
            </a>
        </div>

        <!-- 6. Sesuai SLA -->
        <div class="col-4 col-md-4 col-lg-2">
            <a href="{{ route('tiket.index', ['sla_status' => 'TEPAT']) }}" 
               class="stat-card-pro stat-theme-tepat h-100" 
               title="Filter Tiket Sesuai SLA">
                <div class="stat-pro-top">
                    <span class="stat-pro-label">&le; SLA</span>
                    <div class="stat-pro-icon">
                        <i class="bi bi-shield-check"></i>
                    </div>
                </div>
                <div class="stat-pro-number">
                    {{ $summary['tepat_sla'] }}
                    <span class="stat-pro-unit text-teal">tiket</span>
                </div>
                <div class="stat-pro-desc text-teal">&le; Target SLA</div>
                <i class="bi bi-shield-check stat-watermark"></i>
            </a>
        </div>
        @endif
    </div>
    @endif

    <!-- ── SMART COLLAPSIBLE FILTER CARD (DROPDOWN SEPERTI MENU MTTR) ── -->
    @php
        $hasActiveFilter = request()->filled('q') || request()->filled('segment') || request()->filled('sla_status') || request()->filled('tanggal') || (request()->filled('status') && (!auth()->user()->hasRole('teknis') || request('status') !== 'CLOSE'));
    @endphp
    <div class="card border-0 shadow-sm rounded-xl mb-3 mb-md-4 overflow-hidden bg-white">
        <div class="card-header bg-white p-3 d-flex justify-content-between align-items-center border-bottom"
             role="button"
             data-bs-toggle="collapse"
             data-bs-target="#tiketFilterCollapse"
             aria-expanded="{{ $hasActiveFilter ? 'true' : 'false' }}"
             aria-controls="tiketFilterCollapse"
             style="cursor: pointer; user-select: none;">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; background: #eff6ff; color: #2563eb;">
                    <i class="bi bi-funnel-fill" style="font-size: 0.85rem;"></i>
                </div>
                <span class="fw-bold text-navy small">Filter &amp; Pencarian Tiket</span>
                @if($hasActiveFilter)
                    <span class="badge rounded-pill fw-bold" style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; font-size: 0.65rem;">
                        Filter Aktif
                    </span>
                @endif
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted small d-none d-sm-inline" id="tiketFilterToggleText" style="font-size: 0.72rem;">
                    {{ $hasActiveFilter ? 'Sembunyikan filter' : 'Tampilkan filter' }}
                </span>
                <i class="bi bi-chevron-down text-muted transition-transform {{ $hasActiveFilter ? 'rotate-180' : '' }}" id="tiketFilterChevron"></i>
            </div>
        </div>

        <div class="collapse {{ $hasActiveFilter ? 'show' : '' }} d-md-block" id="tiketFilterCollapse">
            <div class="card-body p-3.5 p-md-4 bg-light bg-opacity-50">
                <form action="{{ route('tiket.index') }}" method="GET" class="row g-3 g-md-3.5 align-items-end">
                    @if(request('view') === 'tugas_saya')
                        <input type="hidden" name="view" value="tugas_saya">
                    @endif

                    <!-- Pencarian Keyword (col-12 on mobile, col-md-6 col-lg-3 on desktop) -->
                    <div class="col-12 col-md-6 col-lg-3">
                        <label for="filter_q" class="filter-label">Pencarian Tiket</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white text-muted ps-2.5" style="border: 1px solid #cbd5e1; border-right: none; height: 38px; border-top-left-radius: 10px; border-bottom-left-radius: 10px;">
                                <i class="bi bi-search" style="font-size: 0.8rem;"></i>
                            </span>
                            <input type="text"
                                   id="filter_q"
                                   name="q"
                                   class="form-control form-control-sm filter-form-control"
                                   placeholder="No Tiket / Link Impact..."
                                   value="{{ $filters['q'] ?? '' }}"
                                   style="border-left: none !important; border-top-left-radius: 0 !important; border-bottom-left-radius: 0 !important;">
                        </div>
                    </div>

                    <!-- Segment Backbone (col-12 on mobile, col-sm-6 col-md-6 col-lg-2 on desktop) -->
                    <div class="col-12 col-sm-6 col-md-6 col-lg-2">
                        <label for="filter_segment" class="filter-label">Segment Backbone</label>
                        <select id="filter_segment" name="segment" class="form-select form-select-sm filter-form-select">
                            <option value="">Semua Segment</option>
                            @foreach($masterSegments as $ms)
                            <option value="{{ $ms->backbone_segment }}" {{ ($filters['segment'] ?? '') === $ms->backbone_segment ? 'selected' : '' }}>
                                {{ $ms->backbone_segment }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tanggal Open (col-6 on mobile, col-sm-6 col-md-4 col-lg-2 on desktop) -->
                    <div class="col-6 col-sm-6 col-md-4 col-lg-2">
                        <label for="filter_tanggal" class="filter-label">Tanggal Open</label>
                        <input type="date"
                               id="filter_tanggal"
                               name="tanggal"
                               class="form-control form-control-sm filter-form-control"
                               value="{{ $filters['tanggal'] ?? '' }}">
                    </div>

                    <!-- Kepatuhan SLA (col-6 on mobile, col-sm-6 col-md-4 col-lg-2 on desktop) -->
                    <div class="col-6 col-sm-6 col-md-4 col-lg-2">
                        <label for="filter_sla_status" class="filter-label">Kepatuhan SLA</label>
                        <select id="filter_sla_status" name="sla_status" class="form-select form-select-sm filter-form-select">
                            <option value="">Semua SLA</option>
                            <option value="TEPAT" {{ ($filters['sla_status'] ?? '') === 'TEPAT' ? 'selected' : '' }}>&le; Target SLA</option>
                            <option value="LEBIH" {{ ($filters['sla_status'] ?? '') === 'LEBIH' ? 'selected' : '' }}>&gt; Target SLA</option>
                            <option value="NA" {{ ($filters['sla_status'] ?? '') === 'NA' ? 'selected' : '' }}>Belum Close (N/A)</option>
                        </select>
                    </div>

                    <!-- Status Tiket (khusus non-teknis: col-12 on mobile, col-sm-6 col-md-4 col-lg-1 on desktop) -->
                    @if(!auth()->user()->hasRole('teknis'))
                    <div class="col-12 col-sm-6 col-md-4 col-lg-1">
                        <label for="filter_status" class="filter-label">Status</label>
                        <select id="filter_status" name="status" class="form-select form-select-sm filter-form-select">
                            <option value="">Semua</option>
                            <option value="AKTIF" {{ ($filters['status'] ?? '') === 'AKTIF' ? 'selected' : '' }}>AKTIF</option>
                            <option value="OPEN" {{ ($filters['status'] ?? '') === 'OPEN' ? 'selected' : '' }}>OPEN</option>
                            <option value="PROSES" {{ ($filters['status'] ?? '') === 'PROSES' ? 'selected' : '' }}>PROSES</option>
                            <option value="CLOSE" {{ ($filters['status'] ?? '') === 'CLOSE' ? 'selected' : '' }}>CLOSE</option>
                        </select>
                    </div>
                    @else
                        <input type="hidden" name="status" value="{{ $filters['status'] ?? 'CLOSE' }}">
                    @endif

                    <!-- Action Buttons -->
                    <div class="col-12 col-lg-auto ms-lg-auto d-flex align-items-center gap-2 pt-1.5 pt-lg-0">
                        <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3.5 fw-bold d-flex align-items-center justify-content-center gap-1.5 shadow-xs flex-fill flex-lg-grow-0" style="background: linear-gradient(135deg, #2C7FFF 0%, #1b39da 100%); border: none; min-height: 38px;">
                            <i class="bi bi-funnel-fill"></i> <span>Filter</span>
                        </button>
                        <a href="{{ route('tiket.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 d-flex align-items-center justify-content-center gap-1.5 shadow-xs" title="Reset Filter" style="min-height: 38px; white-space: nowrap;">
                            <i class="bi bi-arrow-counterclockwise"></i> <span>Reset</span>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ── TIKET DATA TABLE CARD ── -->
    <div class="card border-0 shadow-sm rounded-xl overflow-hidden table-pro-card">
        <div class="table-pro-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div class="d-flex align-items-center gap-2.5">
                <div class="table-header-icon {{ $isTugasSaya ? 'bg-warning-subtle text-warning' : 'bg-teal-subtle text-teal' }}">
                    <i class="bi {{ $isTugasSaya ? 'bi-tools' : (auth()->user()->hasRole('teknis') ? 'bi-clock-history' : 'bi-ticket-detailed-fill') }}"></i>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2">
                        <h6 class="fw-bold mb-0 text-navy">{{ $isTugasSaya ? 'Daftar Tugas Saya' : (auth()->user()->hasRole('teknis') ? 'History Tiket Selesai' : 'Data Tiket Gangguan') }}</h6>
                        <span class="header-badge">{{ $tikets->total() }} Tiket</span>
                    </div>
                    <div class="text-muted small" style="font-size: 0.75rem;">
                        {{ $isTugasSaya ? 'Daftar tiket penanganan aktif yang ditugaskan kepada Anda' : (auth()->user()->hasRole('teknis') ? 'Daftar riwayat tiket gangguan jaringan yang sudah closing' : 'Daftar riwayat dan monitoring tiket gangguan jaringan') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- ── MOBILE CARD VIEW (< 768px) ── -->
        <div class="d-block d-md-none p-2 p-sm-3 bg-light bg-opacity-25">
            @forelse($tikets as $tiket)
            <div class="tiket-mobile-card">
                <!-- Top Status Stripe -->
                <div class="{{ $tiket->status === 'OPEN' ? 'card-stripe-open' : ($tiket->status === 'PROSES' ? 'card-stripe-proses' : 'card-stripe-close') }}"></div>

                <div class="tiket-mobile-card-body">
                    <!-- Row 1: No Tiket & Status Badges -->
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <a href="{{ route('tiket.show', $tiket->id) }}" class="text-decoration-none">
                            <span class="badge bg-light text-primary font-monospace border px-2 py-1" style="font-size:0.75rem;">
                                <i class="bi bi-ticket-perforated me-1"></i>{{ $tiket->no_tiket }}
                            </span>
                        </a>
                        <div class="d-flex align-items-center gap-1 flex-wrap justify-content-end">
                            @if($tiket->status === 'OPEN')
                                <span class="badge-status badge-open" style="font-size:0.68rem;"><i class="bi bi-exclamation-circle"></i> OPEN</span>
                            @elseif($tiket->status === 'PROSES')
                                <span class="badge-status badge-proses" style="font-size:0.68rem;"><i class="bi bi-arrow-repeat"></i> PROSES</span>
                            @elseif($tiket->status === 'PENDING_VERIFIKASI')
                                <span class="badge-status" style="font-size:0.68rem; background-color:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe;"><i class="bi bi-hourglass-split"></i> VERIFIKASI</span>
                            @else
                                <span class="badge-status badge-close" style="font-size:0.68rem;"><i class="bi bi-check-circle"></i> CLOSE</span>
                            @endif

                            @if($tiket->is_stop_clock)
                                <span class="badge-status" style="font-size:0.68rem; background-color:#fef2f2; color:#b91c1c; border:1px solid #fecaca;"><i class="bi bi-pause-fill"></i> STOP CLOCK</span>
                            @endif

                            @if($tiket->sla_status === 'TEPAT')
                                <span class="badge-status badge-sla-tepat" style="font-size:0.68rem;"><i class="bi bi-shield-check"></i> TEPAT</span>
                            @elseif($tiket->sla_status === 'LEBIH')
                                <span class="badge-status badge-sla-lebih" style="font-size:0.68rem;"><i class="bi bi-exclamation-triangle"></i> LEBIH</span>
                            @endif
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

                    <!-- Row 4: Meta info & Action Buttons -->
                    <div class="d-flex align-items-center justify-content-between pt-2 border-top" style="font-size:0.72rem;">
                        <div class="text-muted">
                            <div><i class="bi bi-clock me-1 text-primary"></i> Open: <strong class="text-dark">{{ $tiket->tanggal_open->format('d/m/Y H:i') }}</strong></div>
                            @if($tiket->status === 'CLOSE')
                                <div class="text-success mt-0.5"><i class="bi bi-check2 me-1"></i> MTTR: <strong>{{ $tiket->formatted_mttr }}</strong></div>
                            @else
                                <div class="text-muted mt-0.5"><i class="bi bi-stopwatch me-1"></i> Target: <strong>{{ $tiket->formatted_sla_target }}</strong></div>
                            @endif
                        </div>

                        <div class="d-flex align-items-center gap-1">
                            @if(auth()->user()->hasRole(['admin', 'helpdesk']) && $tiket->status !== 'CLOSE')
                            <button type="button" class="btn btn-outline-success btn-sm px-2 py-1"
                                    title="Closing Tiket"
                                    data-bs-toggle="modal"
                                    data-bs-target="#closeTiketModal{{ $tiket->id }}">
                                <i class="bi bi-check-circle-fill"></i>
                            </button>
                            @endif

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
                <div class="small fw-semibold">Tidak ada data tiket yang sesuai</div>
            </div>
            @endforelse
        </div>

        <!-- ── DESKTOP TABLE VIEW (>= 768px) ── -->
        <div class="d-none d-md-block table-responsive">
            <table class="table table-pro align-middle mb-0">
                <colgroup>
                    <col style="width: 15%;">
                    <col style="width: 22%;">
                    <col style="width: 14%;">
                    <col style="width: 9%;">
                    <col style="width: 9%;">
                    <col style="width: 8%;">
                    <col style="width: 11%;">
                    <col style="width: 12%;">
                </colgroup>
                <thead>
                    <tr>
                        <th><i class="bi bi-hash me-1"></i>No Tiket</th>
                        <th><i class="bi bi-hdd-network me-1"></i>Segment &amp; Link</th>
                        <th><i class="bi bi-clock-history me-1"></i>Waktu</th>
                        <th class="text-center"><i class="bi bi-activity me-1"></i>Status</th>
                        <th class="text-center"><i class="bi bi-stopwatch me-1"></i>Target / MTTR</th>
                        <th class="text-center"><i class="bi bi-shield-check me-1"></i>SLA</th>
                        <th><i class="bi bi-person me-1"></i>Pelapor</th>
                        <th class="text-center"><i class="bi bi-gear me-1"></i>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tikets as $tiket)
                    <tr>
                        <!-- No Tiket -->
                        <td>
                            <a href="{{ route('tiket.show', $tiket->id) }}" class="text-decoration-none d-inline-block">
                                <span class="tiket-code-chip">
                                    <i class="bi bi-ticket-perforated"></i>
                                    {{ $tiket->no_tiket }}
                                </span>
                            </a>
                        </td>

                        <!-- Segment & Link Impact -->
                        <td>
                            <a href="{{ route('tiket.show', $tiket->id) }}" class="impact-title text-decoration-none text-truncate d-block" title="{{ $tiket->status_link_impact }}">
                                {{ $tiket->status_link_impact }}
                            </a>
                            <div class="d-flex align-items-center gap-1 mt-1">
                                <span class="badge-segment" title="{{ $tiket->backbone_segment }}">
                                    <i class="bi bi-geo-alt-fill text-teal me-1"></i>{{ $tiket->backbone_segment }}
                                </span>
                            </div>
                        </td>

                        <!-- Tanggal Open / Close -->
                        <td>
                            <div class="d-flex flex-column gap-0.5" style="font-size: 0.71rem;">
                                <div class="d-flex align-items-center text-nowrap">
                                    <span class="time-dot time-dot-open" title="Waktu Buka"></span>
                                    <span class="text-muted" style="font-size: 0.67rem; width: 30px;">Open:</span>
                                    <span class="fw-semibold text-dark">{{ $tiket->tanggal_open->format('d/m/y') }}</span>
                                    <span class="text-muted ms-1" style="font-size: 0.67rem;">{{ $tiket->tanggal_open->format('H:i') }}</span>
                                </div>
                                @if($tiket->tanggal_close)
                                <div class="d-flex align-items-center text-nowrap">
                                    <span class="time-dot time-dot-close" title="Waktu Tutup"></span>
                                    <span class="text-muted" style="font-size: 0.67rem; width: 30px;">Close:</span>
                                    <span class="fw-semibold text-success">{{ $tiket->tanggal_close->format('d/m/y') }}</span>
                                    <span class="text-muted ms-1" style="font-size: 0.67rem;">{{ $tiket->tanggal_close->format('H:i') }}</span>
                                </div>
                                @else
                                <div class="d-flex align-items-center text-nowrap">
                                    <span class="time-dot time-dot-pending" title="Belum Ditutup"></span>
                                    <span class="badge-not-closed">Belum Tutup</span>
                                </div>
                                @endif
                            </div>
                        </td>

                        <!-- Status Badge -->
                        <td class="text-center px-1">
                            @if($tiket->status === 'OPEN')
                                <span class="badge-status-pro status-open">
                                    <span class="status-pulse-dot" style="background-color: #0284c7;"></span> OPEN
                                </span>
                            @elseif($tiket->status === 'PROSES')
                                <span class="badge-status-pro status-proses">
                                    <span class="status-pulse-dot" style="background-color: #d97706;"></span> PROSES
                                </span>
                            @elseif($tiket->status === 'PENDING_VERIFIKASI')
                                <span class="badge-status-pro" style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe;">
                                    <span class="status-pulse-dot" style="background-color: #2563eb;"></span> VERIFIKASI
                                </span>
                            @else
                                <span class="badge-status-pro status-close">
                                    <i class="bi bi-check-lg"></i> CLOSE
                                </span>
                            @endif

                            @if($tiket->is_stop_clock)
                                <div class="mt-1">
                                    <span class="badge bg-danger text-white rounded-pill px-1.5 py-0.5" style="font-size:0.6rem;">
                                        <i class="bi bi-pause-fill"></i> STOP CLOCK
                                    </span>
                                </div>
                            @endif
                        </td>

                        <!-- Target SLA & MTTR -->
                        <td class="text-center px-1">
                            <div class="d-flex flex-column align-items-center lh-sm" style="font-size: 0.72rem;">
                                <div class="text-nowrap" title="{{ $tiket->sla_target_minutes }} Menit">
                                    <span class="text-muted">Target:</span>
                                    <strong class="text-slate-800">{{ $tiket->sla_target_minutes ? round($tiket->sla_target_minutes / 60, 1) . ' Jam' : '-' }}</strong>
                                </div>
                                <div class="text-nowrap mt-0.5">
                                    <span class="text-muted">MTTR:</span>
                                    <strong class="{{ $tiket->status === 'CLOSE' ? ($tiket->sla_status === 'LEBIH' ? 'text-danger' : 'text-success') : 'text-muted' }}">
                                        {{ $tiket->formatted_mttr }}
                                    </strong>
                                </div>
                            </div>
                        </td>

                        <!-- Status SLA -->
                        <td class="text-center px-1">
                            @if($tiket->sla_status === 'TEPAT')
                                <span class="badge-sla-pro sla-tepat">
                                    <i class="bi bi-shield-fill-check"></i> TEPAT
                                </span>
                            @elseif($tiket->sla_status === 'LEBIH')
                                <span class="badge-sla-pro sla-lebih">
                                    <i class="bi bi-shield-fill-x"></i> LEBIH
                                </span>
                            @else
                                <span class="badge-sla-pro sla-na">
                                    -
                                </span>
                            @endif
                        </td>

                        <!-- Pelapor -->
                        <td class="px-1">
                            @php
                                $creatorName = $tiket->creator?->name ?? 'Sistem';
                                $words = explode(' ', trim($creatorName));
                                $initials = count($words) >= 2 
                                    ? strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1))
                                    : strtoupper(substr($creatorName, 0, 2));
                            @endphp
                            <div class="d-flex align-items-center gap-1.5 overflow-hidden">
                                <div class="user-avatar-cell" title="{{ $creatorName }}">
                                    {{ $initials }}
                                </div>
                                <div class="user-info-text overflow-hidden" style="min-width: 0;">
                                    <div class="fw-semibold text-navy text-truncate user-name-text" title="{{ $creatorName }}">{{ $creatorName }}</div>
                                    <div class="text-muted text-truncate user-role-text" title="{{ $tiket->creator?->role_label ?? '-' }}">{{ $tiket->creator?->role_label ?? '-' }}</div>
                                </div>
                            </div>
                        </td>

                        <!-- Action Buttons -->
                        <td class="text-center px-1">
                            <div class="d-inline-flex gap-1 align-items-center justify-content-center">
                                <!-- Detail Button -->
                                <a href="{{ route('tiket.show', $tiket->id) }}" class="btn-action-pro btn-action-detail"
                                   title="Lihat Detail Tiket">
                                    <i class="bi bi-eye"></i>
                                </a>

                                <!-- Edit Button (Open Tiket only) -->
                                @if(auth()->user()->hasRole(['admin', 'helpdesk']) && $tiket->status === 'OPEN')
                                <a href="{{ route('tiket.edit', $tiket->id) }}" class="btn-action-pro btn-action-edit"
                                   title="Edit Data Tiket">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @endif

                                <!-- Quick Close Modal Trigger (Open or Proses Tiket) -->
                                @if(auth()->user()->hasRole(['admin', 'helpdesk']) && $tiket->status !== 'CLOSE')
                                <button type="button" class="btn-action-pro btn-action-close"
                                        title="Closing Tiket"
                                        data-bs-toggle="modal"
                                        data-bs-target="#closeTiketModal{{ $tiket->id }}">
                                    <i class="bi bi-check2-circle"></i>
                                </button>
                                @endif

                                <!-- Delete Button (Admin Only) -->
                                @if(auth()->user()->hasRole('admin'))
                                <button type="button" class="btn-action-pro btn-action-delete"
                                        title="Hapus Tiket"
                                        onclick="confirmDelete('{{ route('tiket.destroy', $tiket->id) }}', 'Hapus Tiket {{ $tiket->no_tiket }}?', 'Data tiket ini beserta seluruh relasi data (kronologis, resume, foto) akan dihapus permanen.')">
                                    <i class="bi bi-trash3"></i>
                                </button>
                                @endif
                            </div>

                            <!-- ── QUICK CLOSE MODAL FOR THIS TIKET ── -->
                            @if(auth()->user()->hasRole(['admin', 'helpdesk']) && $tiket->status !== 'CLOSE')
                            <div class="modal fade" id="closeTiketModal{{ $tiket->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow">
                                        <form action="{{ route('tiket.close', $tiket->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-header bg-navy text-white">
                                                <h6 class="modal-title fw-bold">
                                                    <i class="bi bi-check-circle-fill text-success me-2"></i>Closing Tiket [{{ $tiket->no_tiket }}]
                                                </h6>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body text-start p-4">
                                                <div class="alert alert-info py-2 px-3 small mb-3">
                                                    <i class="bi bi-info-circle me-1"></i> Penutupan tiket akan otomatis mengkalkulasi durasi MTTR dan status kepatuhan SLA.
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label small fw-semibold text-muted">Status Link Impact</label>
                                                    <input type="text" class="form-control form-control-sm bg-light" value="{{ $tiket->status_link_impact }}" readonly>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label small fw-semibold text-muted">Waktu Open Tiket</label>
                                                    <input type="text" class="form-control form-control-sm bg-light" value="{{ $tiket->tanggal_open->format('d/m/Y H:i:s') }}" readonly>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold text-navy">Waktu Closing Tiket (WIB) <span class="text-danger">*</span></label>
                                                    <input type="datetime-local" name="tanggal_close" class="form-control form-control-sm"
                                                           value="{{ now()->format('Y-m-d\TH:i') }}" required>
                                                    <div class="form-text small">Default: waktu saat ini.</div>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label small fw-semibold text-muted">Catatan Closing (Opsional)</label>
                                                    <textarea name="catatan_closing" class="form-control form-control-sm" rows="2" placeholder="Catatan akhir saat penutupan tiket..."></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer bg-light py-2">
                                                <button type="button" class="btn btn-light btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-success btn-sm px-3">
                                                    <i class="bi bi-check2-circle me-1"></i> Ya, Closing Tiket
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <div class="text-muted">
                                <div class="mb-3">
                                    <span class="d-inline-flex align-items-center justify-content-center bg-light rounded-circle" style="width:64px;height:64px;">
                                        <i class="bi bi-inbox text-muted opacity-50" style="font-size:1.8rem;"></i>
                                    </span>
                                </div>
                                <h6 class="fw-semibold text-navy">Tidak ada data tiket yang sesuai</h6>
                                <p class="small mb-3 text-secondary">Silakan sesuaikan kata kunci pencarian atau filter yang Anda pilih.</p>
                                @if(auth()->user()->hasRole(['admin', 'helpdesk']))
                                <a href="{{ route('tiket.create') }}" class="btn btn-sm btn-cjp-teal rounded-pill px-3">
                                    <i class="bi bi-plus-circle me-1"></i> Buat Tiket Sekarang
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- ── PAGINATION FOOTER ── -->
        @if($tikets->hasPages())
        <div class="card-footer bg-white py-3 px-4 border-top d-flex flex-wrap justify-content-between align-items-center gap-2">
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
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ── ROTATE FILTER CHEVRON ON COLLAPSE ──
    const filterCollapse = document.getElementById('tiketFilterCollapse');
    const filterChevron = document.getElementById('tiketFilterChevron');
    const filterToggleText = document.getElementById('tiketFilterToggleText');
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
});
</script>
@endpush
