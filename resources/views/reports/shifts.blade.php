@extends('layouts.app')

@section('title', 'Rekapitulasi Handover & Oper Shift')

@push('styles')
<style>
    .shift-stat-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 1.15rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .shift-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08);
    }
    .shift-stat-card::before {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 3.5px;
    }
    .shift-stat-card.border-c-total::before { background: linear-gradient(90deg, #3b82f6, #1d4ed8); }
    .shift-stat-card.border-c-pagi::before { background: linear-gradient(90deg, #f59e0b, #d97706); }
    .shift-stat-card.border-c-siang::before { background: linear-gradient(90deg, #06b6d4, #0891b2); }
    .shift-stat-card.border-c-malam::before { background: linear-gradient(90deg, #8b5cf6, #6d28d9); }

    .shift-badge-pagi { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
    .shift-badge-siang { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
    .shift-badge-malam { background: #f3e8ff; color: #6b21a8; border: 1px solid #e9d5ff; }

    .rotate-180 {
        transform: rotate(180deg);
    }
    .transition-transform {
        transition: transform 0.25s ease;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-0 pb-5 mb-5">

    <!-- ── BREADCRUMB ── -->
    <div class="d-none d-md-block mb-3">
        <x-breadcrumb :items="[
            'Laporan & Analisis' => route('reports.index'),
            'Audit & Rekap Oper Shift' => null
        ]" />
    </div>

    <!-- ── PAGE HEADER & EXPORT ACTIONS ── -->
    <div class="card border-0 shadow-sm rounded-xl mb-3 bg-white p-3 p-md-3.5">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2.5">
            <div class="d-flex align-items-center gap-2.5">
                <div class="page-title-icon-box shadow-xs flex-shrink-0" style="background: linear-gradient(135deg, #8b5cf6, #6d28d9); box-shadow: 0 4px 12px rgba(139, 92, 246, 0.35); width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 1.15rem;">
                    <i class="bi bi-arrow-left-right text-white"></i>
                </div>
                <div>
                    <h1 class="page-title-text mb-0 fw-bold text-navy" style="font-size: 1.05rem; line-height: 1.3;">
                        Audit &amp; Rekapitulasi Handover / Oper Shift
                    </h1>
                    <p class="text-muted small mb-0 d-none d-md-block" style="font-size: 0.75rem; margin-top: 2px;">
                        Log serah terima penanganan tiket antar shift dan catatan kondisi lapangan
                    </p>
                </div>
            </div>

            <!-- Export Buttons -->
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <a href="{{ route('reports.export.shifts.pdf', request()->query()) }}" class="btn btn-outline-danger btn-sm rounded-pill px-3 py-1 shadow-xs d-inline-flex align-items-center gap-1.5" style="font-size: 0.78rem; min-height: 32px;" title="Export PDF">
                    <i class="bi bi-file-earmark-pdf-fill"></i>
                    <span>Export PDF</span>
                </a>
                <a href="{{ route('reports.export.shifts.excel', request()->query()) }}" class="btn btn-outline-success btn-sm rounded-pill px-3 py-1 shadow-xs d-inline-flex align-items-center gap-1.5" style="font-size: 0.78rem; min-height: 32px;" title="Export Excel">
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

    <!-- ── 4 STAT METRIC CARDS ── -->
    <div class="row g-2 g-md-3 mb-3 mb-md-4">
        <!-- 1. Total Handover -->
        <div class="col-6 col-md-3">
            <div class="shift-stat-card border-c-total">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-bold">TOTAL HANDOVER</span>
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                        <i class="bi bi-arrow-repeat"></i>
                    </div>
                </div>
                <div>
                    <div class="h3 fw-bold text-navy mb-0">{{ $metrics['total_handover'] }}</div>
                    <div class="small text-muted mt-1" style="font-size:0.75rem;">
                        Pada {{ $metrics['unique_tikets_count'] }} tiket gangguan
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Shift Pagi -->
        <div class="col-6 col-md-3">
            <div class="shift-stat-card border-c-pagi">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-bold">KE SHIFT 1 (PAGI)</span>
                    <div class="rounded-circle bg-warning bg-opacity-15 text-warning p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                        <i class="bi bi-brightness-high-fill"></i>
                    </div>
                </div>
                <div>
                    <div class="h3 fw-bold text-navy mb-0">{{ $metrics['shift_pagi'] }}</div>
                    <div class="small text-muted mt-1" style="font-size:0.75rem;">
                        Pukul 07:00 - 15:00 WIB
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Shift Siang -->
        <div class="col-6 col-md-3">
            <div class="shift-stat-card border-c-siang">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-bold">KE SHIFT 2 (SIANG)</span>
                    <div class="rounded-circle bg-info bg-opacity-15 text-info p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                        <i class="bi bi-sun-fill"></i>
                    </div>
                </div>
                <div>
                    <div class="h3 fw-bold text-navy mb-0">{{ $metrics['shift_siang'] }}</div>
                    <div class="small text-muted mt-1" style="font-size:0.75rem;">
                        Pukul 15:00 - 23:00 WIB
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Shift Malam -->
        <div class="col-6 col-md-3">
            <div class="shift-stat-card border-c-malam">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-bold">KE SHIFT 3 (MALAM)</span>
                    <div class="rounded-circle bg-purple bg-opacity-15 text-purple p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; color: #8b5cf6;">
                        <i class="bi bi-moon-stars-fill"></i>
                    </div>
                </div>
                <div>
                    <div class="h3 fw-bold text-navy mb-0">{{ $metrics['shift_malam'] }}</div>
                    <div class="small text-muted mt-1" style="font-size:0.75rem;">
                        Pukul 23:00 - 07:00 WIB
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ── FILTER CARD ── -->
    @php
        $hasActiveFilter = request()->anyFilled(['start_date', 'end_date', 'shift_from', 'shift_to', 'user_id', 'search']);
    @endphp
    <div class="card border-0 shadow-sm rounded-xl mb-3 mb-md-4 overflow-hidden bg-white">
        <div class="card-header bg-white p-3 d-flex justify-content-between align-items-center border-bottom"
             role="button"
             data-bs-toggle="collapse"
             data-bs-target="#shiftFilterCollapse"
             aria-expanded="{{ $hasActiveFilter ? 'true' : 'false' }}"
             aria-controls="shiftFilterCollapse"
             style="cursor: pointer; user-select: none;">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; background: #f5f3ff; color: #7c3aed;">
                    <i class="bi bi-funnel-fill" style="font-size: 0.85rem;"></i>
                </div>
                <span class="fw-bold text-navy small">Filter &amp; Parameter Audit Shift</span>
                @if($hasActiveFilter)
                    <span class="badge rounded-pill fw-bold" style="background: #f5f3ff; color: #6d28d9; border: 1px solid #ddd6fe; font-size: 0.65rem;">
                        Filter Aktif
                    </span>
                @endif
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted small d-none d-sm-inline" style="font-size: 0.72rem;">
                    {{ $hasActiveFilter ? 'Sembunyikan filter' : 'Tampilkan filter' }}
                </span>
                <i class="bi bi-chevron-down text-muted transition-transform {{ $hasActiveFilter ? 'rotate-180' : '' }}" id="filterChevron"></i>
            </div>
        </div>

        <div class="collapse {{ $hasActiveFilter ? 'show' : '' }}" id="shiftFilterCollapse">
            <div class="card-body p-3 p-md-3.5">
                <form action="{{ route('reports.shifts') }}" method="GET" class="row g-2 g-md-3">
                    <!-- 1. Rentang Tanggal -->
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label class="form-label small fw-semibold text-navy mb-1">Dari Tanggal</label>
                        <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label class="form-label small fw-semibold text-navy mb-1">Sampai Tanggal</label>
                        <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}">
                    </div>

                    <!-- 2. Shift Asal -->
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label class="form-label small fw-semibold text-navy mb-1">Shift Asal (From)</label>
                        <select name="shift_from" class="form-select form-select-sm">
                            <option value="">Semua Shift Asal</option>
                            <option value="Shift 1 (Pagi 07:00-15:00)" {{ request('shift_from') === 'Shift 1 (Pagi 07:00-15:00)' ? 'selected' : '' }}>Shift 1 (Pagi)</option>
                            <option value="Shift 2 (Siang 15:00-23:00)" {{ request('shift_from') === 'Shift 2 (Siang 15:00-23:00)' ? 'selected' : '' }}>Shift 2 (Siang)</option>
                            <option value="Shift 3 (Malam 23:00-07:00)" {{ request('shift_from') === 'Shift 3 (Malam 23:00-07:00)' ? 'selected' : '' }}>Shift 3 (Malam)</option>
                        </select>
                    </div>

                    <!-- 3. Shift Tujuan -->
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label class="form-label small fw-semibold text-navy mb-1">Shift Tujuan (To)</label>
                        <select name="shift_to" class="form-select form-select-sm">
                            <option value="">Semua Shift Tujuan</option>
                            <option value="Shift 1 (Pagi 07:00-15:00)" {{ request('shift_to') === 'Shift 1 (Pagi 07:00-15:00)' ? 'selected' : '' }}>Shift 1 (Pagi)</option>
                            <option value="Shift 2 (Siang 15:00-23:00)" {{ request('shift_to') === 'Shift 2 (Siang 15:00-23:00)' ? 'selected' : '' }}>Shift 2 (Siang)</option>
                            <option value="Shift 3 (Malam 23:00-07:00)" {{ request('shift_to') === 'Shift 3 (Malam 23:00-07:00)' ? 'selected' : '' }}>Shift 3 (Malam)</option>
                        </select>
                    </div>

                    <!-- 4. Petugas -->
                    <div class="col-12 col-sm-6 col-lg-4">
                        <label class="form-label small fw-semibold text-navy mb-1">Petugas (Pengirim / Penerima)</label>
                        <select name="user_id" class="form-select form-select-sm">
                            <option value="">Semua Petugas</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
                                    {{ $u->name }} ({{ strtoupper($u->role) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- 5. Pencarian Tiket / Segment -->
                    <div class="col-12 col-sm-6 col-lg-5">
                        <label class="form-label small fw-semibold text-navy mb-1">Pencarian No Tiket / Segment</label>
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Contoh: TKT/MSN/... atau SW BBLU" value="{{ request('search') }}">
                    </div>

                    <!-- Tombol Filter -->
                    <div class="col-12 col-lg-3 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3 shadow-xs flex-fill d-inline-flex align-items-center justify-content-center gap-1" style="min-height: 31px;">
                            <i class="bi bi-funnel"></i>
                            <span>Terapkan</span>
                        </button>
                        @if($hasActiveFilter)
                            <a href="{{ route('reports.shifts') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-2.5 shadow-xs" title="Reset Filter" style="min-height: 31px;">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ── TABEL AUDIT LOG HANDOVER SHIFT ── -->
    <div class="card border-0 shadow-sm rounded-xl overflow-hidden bg-white">
        <div class="card-header bg-white p-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-journal-text text-navy"></i>
                <span class="fw-bold text-navy small">Daftar Audit Serah Terima Shift</span>
            </div>
            <span class="badge bg-light text-navy border px-2.5 py-1 rounded-pill small font-monospace">
                Total {{ $handovers->total() }} Data
            </span>
        </div>

        <div class="card-body p-0">
            <!-- ── DESKTOP TABLE VIEW (>= 768px) ── -->
            <div class="table-responsive d-none d-md-block">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.82rem;">
                    <thead class="bg-light text-navy fw-bold border-bottom">
                        <tr>
                            <th class="ps-3" style="width: 130px;">Waktu Handover</th>
                            <th style="width: 170px;">Tiket &amp; Segment</th>
                            <th style="width: 100px;">Status</th>
                            <th style="width: 200px;">Perpindahan Shift</th>
                            <th style="width: 180px;">Petugas Serah &amp; Terima</th>
                            <th>Catatan Serah Terima / Kondisi Lapangan</th>
                            <th class="text-end pe-3" style="width: 80px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($handovers as $h)
                            <tr>
                                <!-- Waktu -->
                                <td class="ps-3 font-monospace">
                                    <div class="fw-bold text-dark">{{ $h->created_at->format('d/m/Y') }}</div>
                                    <div class="text-muted small" style="font-size: 0.72rem;">{{ $h->created_at->format('H:i') }} WIB</div>
                                </td>

                                <!-- Tiket & Segment -->
                                <td>
                                    <div class="fw-bold text-primary font-monospace">
                                        <a href="{{ route('tiket.show', $h->id_tiket) }}" class="text-decoration-none">
                                            {{ $h->tiket?->no_tiket ?? '-' }}
                                        </a>
                                    </div>
                                    <div class="text-muted small text-truncate" style="max-width: 160px;" title="{{ $h->tiket?->backbone_segment }}">
                                        {{ $h->tiket?->backbone_segment ?? '-' }}
                                    </div>
                                </td>

                                <!-- Status Tiket -->
                                <td>
                                    @php
                                        $st = $h->tiket?->status;
                                        $badgeClass = match($st) {
                                            'OPEN' => 'bg-info bg-opacity-15 text-info border border-info border-opacity-25',
                                            'PROSES' => 'bg-warning bg-opacity-15 text-warning-emphasis border border-warning border-opacity-50',
                                            'PENDING_VERIFIKASI' => 'bg-primary bg-opacity-15 text-primary border border-primary border-opacity-25',
                                            'CLOSE' => 'bg-success bg-opacity-15 text-success border border-success border-opacity-25',
                                            default => 'bg-secondary bg-opacity-15 text-secondary',
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }} rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">
                                        {{ $st ?: '-' }}
                                    </span>
                                </td>

                                <!-- Shift Asal -> Tujuan -->
                                <td>
                                    <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                        <span class="badge shift-badge-{{ str_contains($h->shift_from, '1') ? 'pagi' : (str_contains($h->shift_from, '2') ? 'siang' : 'malam') }} rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">
                                            {{ explode('(', $h->shift_from)[0] }}
                                        </span>
                                        <i class="bi bi-arrow-right text-muted small"></i>
                                        <span class="badge shift-badge-{{ str_contains($h->shift_to, '1') ? 'pagi' : (str_contains($h->shift_to, '2') ? 'siang' : 'malam') }} rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">
                                            {{ explode('(', $h->shift_to)[0] }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Petugas -->
                                <td>
                                    <div class="small">
                                        <span class="text-muted">Dari:</span> <strong class="text-dark">{{ $h->userFrom?->name ?? 'Sistem' }}</strong>
                                    </div>
                                    <div class="small">
                                        <span class="text-muted">Ke:</span> <strong class="text-primary">{{ $h->userTo?->name ?? 'PIC Shift Baru' }}</strong>
                                    </div>
                                </td>

                                <!-- Catatan Handover -->
                                <td>
                                    <div class="p-2 rounded bg-light border text-dark small" style="white-space: pre-wrap; word-break: break-word; font-size: 0.78rem;">
                                        {{ $h->catatan_handover }}
                                    </div>
                                </td>

                                <!-- Aksi -->
                                <td class="text-end pe-3">
                                    <a href="{{ route('tiket.show', $h->id_tiket) }}" class="btn btn-outline-primary btn-sm rounded-pill px-2.5 py-1" style="font-size: 0.74rem;" title="Lihat Tiket">
                                        <i class="bi bi-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-arrow-left-right text-secondary fs-1 d-block mb-2"></i>
                                        <span class="fw-semibold">Belum ada riwayat handover shift yang sesuai kriteria.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- ── MOBILE CARD VIEW (< 768px) ── -->
            <div class="d-md-none p-3">
                @forelse($handovers as $h)
                    @php
                        $st = $h->tiket?->status;
                        $badgeClass = match($st) {
                            'OPEN' => 'bg-info bg-opacity-15 text-info border border-info border-opacity-25',
                            'PROSES' => 'bg-warning bg-opacity-15 text-warning-emphasis border border-warning border-opacity-50',
                            'PENDING_VERIFIKASI' => 'bg-primary bg-opacity-15 text-primary border border-primary border-opacity-25',
                            'CLOSE' => 'bg-success bg-opacity-15 text-success border border-success border-opacity-25',
                            default => 'bg-secondary bg-opacity-15 text-secondary',
                        };
                    @endphp
                    <div class="shift-mobile-card">
                        <!-- Top Row: Ticket number, status, date -->
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <a href="{{ route('tiket.show', $h->id_tiket) }}" class="fw-bold text-primary font-monospace text-decoration-none" style="font-size: 0.95rem;">
                                    {{ $h->tiket?->no_tiket ?? '-' }}
                                </a>
                                <div class="text-muted small" style="font-size: 0.75rem;">
                                    <i class="bi bi-diagram-3 me-1"></i>{{ $h->tiket?->backbone_segment ?? '-' }}
                                </div>
                            </div>
                            <span class="badge {{ $badgeClass }} rounded-pill px-2.5 py-1" style="font-size: 0.7rem;">
                                {{ $st ?: '-' }}
                            </span>
                        </div>

                        <!-- Shift Transition & Time -->
                        <div class="d-flex justify-content-between align-items-center bg-light p-2 rounded-3 mb-2.5">
                            <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                <span class="badge shift-badge-{{ str_contains($h->shift_from, '1') ? 'pagi' : (str_contains($h->shift_from, '2') ? 'siang' : 'malam') }} rounded-pill px-2 py-0.5" style="font-size: 0.7rem;">
                                    {{ explode('(', $h->shift_from)[0] }}
                                </span>
                                <i class="bi bi-arrow-right text-muted small"></i>
                                <span class="badge shift-badge-{{ str_contains($h->shift_to, '1') ? 'pagi' : (str_contains($h->shift_to, '2') ? 'siang' : 'malam') }} rounded-pill px-2 py-0.5" style="font-size: 0.7rem;">
                                    {{ explode('(', $h->shift_to)[0] }}
                                </span>
                            </div>
                            <div class="text-end font-monospace text-muted small" style="font-size: 0.72rem;">
                                <i class="bi bi-clock me-1"></i>{{ $h->created_at->format('d/m/Y H:i') }} WIB
                            </div>
                        </div>

                        <!-- Officers Serah & Terima -->
                        <div class="row g-2 mb-2.5" style="font-size: 0.78rem;">
                            <div class="col-6">
                                <div class="p-2 rounded bg-light border border-light">
                                    <div class="text-muted small" style="font-size: 0.68rem;">DARI PETUGAS:</div>
                                    <div class="fw-semibold text-dark text-truncate">{{ $h->userFrom?->name ?? 'Sistem' }}</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-2 rounded bg-light border border-light">
                                    <div class="text-muted small" style="font-size: 0.68rem;">DISERAHKAN KE:</div>
                                    <div class="fw-semibold text-primary text-truncate">{{ $h->userTo?->name ?? 'PIC Shift Baru' }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Handover Notes -->
                        <div class="mb-2.5">
                            <div class="text-muted small fw-semibold mb-1" style="font-size: 0.72rem;">Catatan Handover / Kondisi:</div>
                            <div class="p-2 rounded bg-light border text-dark small" style="white-space: pre-wrap; word-break: break-word; font-size: 0.78rem; line-height: 1.4;">
                                {{ $h->catatan_handover }}
                            </div>
                        </div>

                        <!-- Action Link -->
                        <div class="text-end pt-1">
                            <a href="{{ route('tiket.show', $h->id_tiket) }}" class="btn btn-outline-primary btn-sm rounded-pill px-3 py-1 w-100" style="font-size: 0.8rem;">
                                <i class="bi bi-box-arrow-up-right me-1"></i> Buka Detail Tiket
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-arrow-left-right text-secondary fs-1 d-block mb-2"></i>
                        <span class="fw-semibold">Belum ada riwayat handover shift yang sesuai kriteria.</span>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($handovers->hasPages())
                <div class="p-3 border-top d-flex justify-content-center">
                    {{ $handovers->links() }}
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
