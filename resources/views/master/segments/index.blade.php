@extends('layouts.app')

@section('title', 'Master Segment & Backbone')

@section('content')
<div class="container-fluid px-0 pb-5 mb-5">

    <!-- ── BREADCRUMB ── -->
    <div class="d-none d-md-block mb-3">
        <x-breadcrumb :items="[
            'Dashboard' => route('dashboard'),
            'Master Data' => null,
            'Segment & Backbone' => null
        ]" />
    </div>

    <!-- ── PAGE HEADER & ACTIONS ── -->
    <div class="card border-0 shadow-sm rounded-xl mb-3 bg-white p-3 p-md-3.5">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2.5">
            <div class="d-flex align-items-center gap-2.5">
                <div class="page-title-icon-box shadow-xs flex-shrink-0" style="background: linear-gradient(135deg, #0d9488, #0f766e); box-shadow: 0 4px 12px rgba(13, 148, 136, 0.35); width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 1.15rem;">
                    <i class="bi bi-diagram-3-fill"></i>
                </div>
                <div>
                    <h1 class="page-title-text mb-0 fw-bold text-navy" style="font-size: 1.05rem; line-height: 1.3;">
                        Master Segment Backbone
                    </h1>
                    <p class="text-muted small mb-0 d-none d-md-block" style="font-size: 0.75rem; margin-top: 2px;">
                        Konfigurasi master segment jaringan fiber optik backbone beserta target SLA default
                    </p>
                </div>
            </div>

            <div>
                <button type="button" class="btn btn-primary btn-sm rounded-pill px-3.5 py-1.5 shadow-sm d-inline-flex align-items-center gap-1.5 fw-semibold" style="min-height: 34px; background: linear-gradient(135deg, #0d9488, #0f766e); border-color: #0d9488;" data-bs-toggle="modal" data-bs-target="#addSegmentModal">
                    <i class="bi bi-plus-lg"></i>
                    <span>Tambah Segment Baru</span>
                </button>
            </div>
        </div>
    </div>

    <!-- ── MASTER DATA NAVIGATION TABS ── -->
    <div class="report-nav-wrapper">
        <div class="report-nav-scroll">
            <a href="{{ route('master.segments.index') }}" class="report-nav-item {{ request()->routeIs('master.segments.*') ? 'active' : '' }}">
                <i class="bi bi-diagram-3-fill"></i>
                <span>Segment &amp; Backbone</span>
            </a>
            <a href="{{ route('master.sla.index') }}" class="report-nav-item {{ request()->routeIs('master.sla.*') ? 'active' : '' }}">
                <i class="bi bi-stopwatch-fill"></i>
                <span>Target SLA</span>
            </a>
            <a href="{{ route('master.materials.index') }}" class="report-nav-item {{ request()->routeIs('master.materials.*') ? 'active' : '' }}">
                <i class="bi bi-box-seam-fill"></i>
                <span>Material &amp; Peralatan</span>
            </a>
            <a href="{{ route('master.users.index') }}" class="report-nav-item {{ request()->routeIs('master.users.*') ? 'active' : '' }}">
                <i class="bi bi-people-fill"></i>
                <span>Manajemen Pengguna</span>
            </a>
        </div>
    </div>

    <!-- ── FLASH NOTIFICATIONS ── -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-xl shadow-xs border-0 d-flex align-items-center gap-2 mb-3" role="alert">
        <i class="bi bi-check-circle-fill fs-5 text-success"></i>
        <div class="small fw-semibold">{{ session('success') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-xl shadow-xs border-0 d-flex align-items-center gap-2 mb-3" role="alert">
        <i class="bi bi-exclamation-triangle-fill fs-5 text-danger"></i>
        <div class="small fw-semibold">{{ session('error') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show rounded-xl shadow-xs border-0 mb-3" role="alert">
        <div class="d-flex align-items-center gap-2 mb-1">
            <i class="bi bi-exclamation-circle-fill fs-5 text-danger"></i>
            <span class="small fw-bold">Terdapat kesalahan pengisian data:</span>
        </div>
        <ul class="mb-0 small ps-4">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- ── SUMMARY STAT CARDS ── -->
    @if(isset($stats))
    <div class="row g-2.5 g-md-3 mb-3">
        <div class="col-6 col-md-3">
            <div class="master-stat-card border-c-teal">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="master-stat-label">Total Segment</span>
                    <div class="master-stat-icon bg-teal-subtle text-teal">
                        <i class="bi bi-diagram-3-fill"></i>
                    </div>
                </div>
                <div>
                    <div class="master-stat-val">{{ $stats['total'] ?? 0 }}</div>
                    <div class="master-stat-desc">Segment terdaftar</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="master-stat-card border-c-blue">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="master-stat-label">Rata-rata SLA</span>
                    <div class="master-stat-icon bg-primary-subtle text-primary">
                        <i class="bi bi-speedometer2"></i>
                    </div>
                </div>
                <div>
                    <div class="master-stat-val">{{ $stats['avg_sla'] ?? 0 }} <span class="fs-6 fw-normal text-muted">mnt</span></div>
                    <div class="master-stat-desc">{{ round(($stats['avg_sla'] ?? 0) / 60, 1) }} Jam standar</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="master-stat-card border-c-emerald">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="master-stat-label">SLA Terpendek</span>
                    <div class="master-stat-icon bg-success-subtle text-success">
                        <i class="bi bi-lightning-charge-fill"></i>
                    </div>
                </div>
                <div>
                    <div class="master-stat-val text-success">{{ $stats['min_sla'] ?? 0 }} <span class="fs-6 fw-normal text-muted">mnt</span></div>
                    <div class="master-stat-desc">{{ round(($stats['min_sla'] ?? 0) / 60, 1) }} Jam target</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="master-stat-card border-c-amber">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="master-stat-label">SLA Terpanjang</span>
                    <div class="master-stat-icon bg-warning-subtle text-warning">
                        <i class="bi bi-clock-history"></i>
                    </div>
                </div>
                <div>
                    <div class="master-stat-val text-dark">{{ $stats['max_sla'] ?? 0 }} <span class="fs-6 fw-normal text-muted">mnt</span></div>
                    <div class="master-stat-desc">{{ round(($stats['max_sla'] ?? 0) / 60, 1) }} Jam target</div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- ── TABLE CARD ── -->
    <div class="card border-0 shadow-sm rounded-xl overflow-hidden bg-white mb-4">
        <!-- Toolbar Header -->
        <div class="card-header bg-white border-bottom p-3 d-flex flex-wrap align-items-center justify-content-between gap-2.5">
            <form method="GET" action="{{ route('master.segments.index') }}" class="d-flex align-items-center gap-2" style="max-width: 320px; width: 100%;">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="q" value="{{ $search }}" class="form-control border-start-0" placeholder="Cari nama segment...">
                    @if($search)
                        <a href="{{ route('master.segments.index') }}" class="btn btn-light border text-muted" title="Reset Filter"><i class="bi bi-x"></i></a>
                    @endif
                </div>
            </form>
            <div class="text-muted small">
                Menampilkan <strong class="text-dark">{{ $segments->total() }}</strong> segment backbone
            </div>
        </div>

        <!-- Desktop Table View -->
        <div class="table-responsive d-none d-md-block">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted small text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                    <tr>
                        <th class="ps-3.5 py-3" style="width: 60px;">No</th>
                        <th class="py-3">Nama Segment Backbone</th>
                        <th class="py-3">Target SLA</th>
                        <th class="py-3">Durasi Format</th>
                        <th class="py-3">Terdaftar Sejak</th>
                        <th class="pe-3.5 py-3 text-end" style="width: 130px;">Aksi</th>
                    </tr>
                </thead>
                <tbody class="small">
                    @forelse($segments as $idx => $s)
                    @php
                        $jam = floor($s->sla_target_minutes / 60);
                        $menit = $s->sla_target_minutes % 60;
                        $formattedSla = ($jam > 0 ? "{$jam} Jam " : "") . ($menit > 0 ? "{$menit} Menit" : "");
                    @endphp
                    <tr>
                        <td class="ps-3.5 text-muted">{{ $segments->firstItem() + $idx }}</td>
                        <td>
                            <div class="fw-bold text-dark d-flex align-items-center gap-2">
                                <span class="badge bg-teal-subtle text-teal p-1.5 rounded-2">
                                    <i class="bi bi-diagram-3"></i>
                                </span>
                                <span>{{ $s->backbone_segment }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border px-2.5 py-1 fw-semibold font-monospace">
                                <i class="bi bi-stopwatch text-teal me-1"></i>{{ $s->sla_target_minutes }} Menit
                            </span>
                        </td>
                        <td>
                            <span class="text-muted fw-medium">{{ $formattedSla ?: '-' }}</span>
                        </td>
                        <td class="text-muted">
                            {{ $s->created_at ? $s->created_at->format('d M Y') : '-' }}
                        </td>
                        <td class="pe-3.5 text-end">
                            <div class="d-inline-flex align-items-center gap-1">
                                <button type="button" class="btn btn-light btn-sm text-primary border shadow-2xs rounded-pill px-2.5 py-1"
                                        data-bs-toggle="modal" data-bs-target="#editSegmentModal{{ $s->id }}" title="Edit Segment">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <form method="POST" action="{{ route('master.segments.destroy', $s->id) }}" class="d-inline"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus segment {{ $s->backbone_segment }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-light btn-sm text-danger border shadow-2xs rounded-pill px-2.5 py-1" title="Hapus Segment">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-2 d-block mb-2 opacity-50"></i>
                            Tidak ada data segment backbone yang ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View (< 768px) -->
        <div class="d-block d-md-none p-2.5 p-sm-3 bg-light bg-opacity-25">
            @forelse($segments as $idx => $s)
            @php
                $jam = floor($s->sla_target_minutes / 60);
                $menit = $s->sla_target_minutes % 60;
                $formattedSla = ($jam > 0 ? "{$jam} Jam " : "") . ($menit > 0 ? "{$menit} Menit" : "");
            @endphp
            <div class="master-mobile-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="fw-bold text-dark d-flex align-items-center gap-2" style="font-size: 0.9rem;">
                        <span class="badge bg-teal-subtle text-teal p-1.5 rounded-2">
                            <i class="bi bi-diagram-3"></i>
                        </span>
                        <span>{{ $s->backbone_segment }}</span>
                    </div>
                    <span class="badge bg-light text-muted border font-monospace" style="font-size: 0.7rem;">
                        #{{ $segments->firstItem() + $idx }}
                    </span>
                </div>

                <div class="d-flex align-items-center justify-content-between py-2 border-top border-bottom my-2 small">
                    <div>
                        <div class="text-muted" style="font-size: 0.7rem;">Target SLA:</div>
                        <span class="badge bg-light text-dark border px-2 py-1 font-monospace fw-semibold">
                            <i class="bi bi-stopwatch text-teal me-1"></i>{{ $s->sla_target_minutes }} mnt ({{ $formattedSla ?: '-' }})
                        </span>
                    </div>
                    <div class="text-end">
                        <div class="text-muted" style="font-size: 0.7rem;">Terdaftar:</div>
                        <span class="text-dark fw-medium" style="font-size: 0.75rem;">
                            {{ $s->created_at ? $s->created_at->format('d M Y') : '-' }}
                        </span>
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-end gap-1.5 mt-2">
                    <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3 py-1"
                            data-bs-toggle="modal" data-bs-target="#editSegmentModal{{ $s->id }}">
                        <i class="bi bi-pencil-square me-1"></i> Edit
                    </button>
                    <form method="POST" action="{{ route('master.segments.destroy', $s->id) }}" class="d-inline"
                          onsubmit="return confirm('Hapus segment {{ $s->backbone_segment }}?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3 py-1">
                            <i class="bi bi-trash me-1"></i> Hapus
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <div class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-2 d-block mb-2 opacity-50"></i>
                Tidak ada data segment backbone yang ditemukan.
            </div>
            @endforelse
        </div>

        @if($segments->hasPages())
        <div class="card-footer bg-white border-top p-3 d-flex justify-content-center">
            {{ $segments->links() }}
        </div>
        @endif
    </div>
</div>

<!-- ── MODAL EDIT SEGMENT (LOOP) ── -->
@foreach($segments as $s)
<div class="modal fade" id="editSegmentModal{{ $s->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-xl">
            <form method="POST" action="{{ route('master.segments.update', $s->id) }}">
                @csrf
                @method('PUT')
                <div class="modal-header border-bottom p-3.5 bg-light bg-opacity-50">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle bg-teal-subtle text-teal d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                            <i class="bi bi-pencil-square"></i>
                        </div>
                        <h6 class="modal-title fw-bold text-dark mb-0">
                            Edit Segment Backbone
                        </h6>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-3.5">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-navy">Nama Segment Backbone <span class="text-danger">*</span></label>
                        <input type="text" name="backbone_segment" value="{{ $s->backbone_segment }}" class="form-control" required placeholder="Contoh: SW BBLU - SW Reog">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-navy">Default Target SLA (Menit) <span class="text-danger">*</span></label>
                        <input type="number" name="sla_target_minutes" value="{{ $s->sla_target_minutes }}" class="form-control" min="1" required placeholder="Contoh: 360 (6 jam)">
                        <div class="form-text small">Contoh durasi: 180 = 3 Jam, 240 = 4 Jam, 300 = 5 Jam, 360 = 6 Jam.</div>
                    </div>
                </div>
                <div class="modal-footer border-top p-3 bg-light bg-opacity-25">
                    <button type="button" class="btn btn-light rounded-pill px-3.5 btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 btn-sm" style="background: linear-gradient(135deg, #0d9488, #0f766e); border-color: #0d9488;">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<!-- ── MODAL TAMBAH SEGMENT ── -->
<div class="modal fade" id="addSegmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-xl">
            <form method="POST" action="{{ route('master.segments.store') }}">
                @csrf
                <div class="modal-header border-bottom p-3.5 bg-light bg-opacity-50">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle bg-teal-subtle text-teal d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                            <i class="bi bi-plus-lg"></i>
                        </div>
                        <h6 class="modal-title fw-bold text-dark mb-0">
                            Tambah Segment Backbone Baru
                        </h6>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-3.5">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-navy">Nama Segment Backbone <span class="text-danger">*</span></label>
                        <input type="text" name="backbone_segment" class="form-control" required placeholder="Contoh: SW Kopo - SW Cibaduyut">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-navy">Target SLA Default (Menit) <span class="text-danger">*</span></label>
                        <input type="number" name="sla_target_minutes" class="form-control" value="360" min="1" required placeholder="360">
                        <div class="form-text small">Contoh durasi: 180 = 3 Jam, 240 = 4 Jam, 300 = 5 Jam, 360 = 6 Jam.</div>
                    </div>
                </div>
                <div class="modal-footer border-top p-3 bg-light bg-opacity-25">
                    <button type="button" class="btn btn-light rounded-pill px-3.5 btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 btn-sm" style="background: linear-gradient(135deg, #0d9488, #0f766e); border-color: #0d9488;">Simpan Segment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.bg-teal { background-color: #0d9488 !important; color: #fff !important; }
.text-teal { color: #0d9488 !important; }
.bg-teal-subtle { background: rgba(13,148,136,0.12) !important; color: #0d9488 !important; }
</style>
@endsection
