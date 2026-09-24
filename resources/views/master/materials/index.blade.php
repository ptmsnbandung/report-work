@extends('layouts.app')

@section('title', 'Master Material & Peralatan')

@section('content')
<div class="container-fluid px-0 pb-5 mb-5">

    <!-- ── BREADCRUMB ── -->
    <div class="d-none d-md-block mb-3">
        <x-breadcrumb :items="[
            'Dashboard' => route('dashboard'),
            'Master Data' => null,
            'Material & Peralatan' => null
        ]" />
    </div>

    <!-- ── PAGE HEADER & ACTIONS ── -->
    <div class="card border-0 shadow-sm rounded-xl mb-3 bg-white p-3 p-md-3.5">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2.5">
            <div class="d-flex align-items-center gap-2.5">
                <div class="page-title-icon-box shadow-xs flex-shrink-0" style="background: linear-gradient(135deg, #10b981, #059669); box-shadow: 0 4px 12px rgba(16, 185, 129, 0.35); width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 1.15rem;">
                    <i class="bi bi-box-seam-fill"></i>
                </div>
                <div>
                    <h1 class="page-title-text mb-0 fw-bold text-navy" style="font-size: 1.05rem; line-height: 1.3;">
                        Master Material &amp; Peralatan
                    </h1>
                    <p class="text-muted small mb-0 d-none d-md-block" style="font-size: 0.75rem; margin-top: 2px;">
                        Katalog material perbaikan FO, satuan logistik, dan monitoring stok gudang teknis
                    </p>
                </div>
            </div>

            <div>
                <button type="button" class="btn btn-primary btn-sm rounded-pill px-3.5 py-1.5 shadow-sm d-inline-flex align-items-center gap-1.5 fw-semibold" style="min-height: 34px; background: linear-gradient(135deg, #10b981, #059669); border-color: #10b981;" data-bs-toggle="modal" data-bs-target="#addMaterialModal">
                    <i class="bi bi-plus-lg"></i>
                    <span>Tambah Material Baru</span>
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
            <span class="small fw-bold">Terdapat kesalahan data:</span>
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
            <div class="master-stat-card border-c-emerald">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="master-stat-label">Total Item</span>
                    <div class="master-stat-icon bg-success-subtle text-success">
                        <i class="bi bi-boxes"></i>
                    </div>
                </div>
                <div>
                    <div class="master-stat-val">{{ $stats['total'] ?? 0 }}</div>
                    <div class="master-stat-desc">Katalog material</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="master-stat-card border-c-blue">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="master-stat-label">Total Stok Fisik</span>
                    <div class="master-stat-icon bg-primary-subtle text-primary">
                        <i class="bi bi-archive-fill"></i>
                    </div>
                </div>
                <div>
                    <div class="master-stat-val">{{ number_format($stats['total_stok'] ?? 0, 0, ',', '.') }}</div>
                    <div class="master-stat-desc">Unit stok gudang</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="master-stat-card border-c-teal">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="master-stat-label">Status Aktif</span>
                    <div class="master-stat-icon bg-teal-subtle text-teal">
                        <i class="bi bi-check2-circle"></i>
                    </div>
                </div>
                <div>
                    <div class="master-stat-val text-teal">{{ $stats['active'] ?? 0 }}</div>
                    <div class="master-stat-desc">Tersedia digunakan</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="master-stat-card border-c-rose">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="master-stat-label">Stok Kritis</span>
                    <div class="master-stat-icon bg-danger-subtle text-danger">
                        <i class="bi bi-exclamation-octagon-fill"></i>
                    </div>
                </div>
                <div>
                    <div class="master-stat-val text-danger">{{ $stats['critical'] ?? 0 }}</div>
                    <div class="master-stat-desc">Perlu restock (&le; 20 unit)</div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- ── TABLE CARD ── -->
    <div class="card border-0 shadow-sm rounded-xl overflow-hidden bg-white mb-4">
        <!-- Toolbar Header -->
        <div class="card-header bg-white border-bottom p-3 d-flex flex-wrap align-items-center justify-content-between gap-2.5">
            <form method="GET" action="{{ route('master.materials.index') }}" class="d-flex align-items-center gap-2" style="max-width: 340px; width: 100%;">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="q" value="{{ $search }}" class="form-control border-start-0" placeholder="Cari kode/nama material...">
                    @if($search)
                        <a href="{{ route('master.materials.index') }}" class="btn btn-light border text-muted" title="Reset Filter"><i class="bi bi-x"></i></a>
                    @endif
                </div>
            </form>
            <div class="text-muted small">
                Menampilkan <strong class="text-dark">{{ $materials->total() }}</strong> item master material
            </div>
        </div>

        <!-- Desktop Table View -->
        <div class="table-responsive d-none d-md-block">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted small text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                    <tr>
                        <th class="ps-3.5 py-3" style="width: 50px;">No</th>
                        <th class="py-3">Kode Material</th>
                        <th class="py-3">Nama Material</th>
                        <th class="py-3">Satuan</th>
                        <th class="py-3">Stok Gudang</th>
                        <th class="py-3">Keterangan</th>
                        <th class="py-3">Status</th>
                        <th class="pe-3.5 py-3 text-end" style="width: 130px;">Aksi</th>
                    </tr>
                </thead>
                <tbody class="small">
                    @forelse($materials as $idx => $m)
                    <tr>
                        <td class="ps-3.5 text-muted">{{ $materials->firstItem() + $idx }}</td>
                        <td>
                            <span class="badge bg-light text-dark border font-monospace px-2 py-1 fw-bold">
                                {{ $m->kode_material }}
                            </span>
                        </td>
                        <td>
                            <div class="fw-bold text-dark d-flex align-items-center gap-2">
                                <span class="badge bg-teal-subtle text-teal p-1.5 rounded-2">
                                    <i class="bi bi-box-seam"></i>
                                </span>
                                <span>{{ $m->nama_material }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-secondary-subtle text-secondary px-2.5 py-1">{{ $m->satuan }}</span>
                        </td>
                        <td>
                            <span class="fw-bold font-monospace {{ $m->stok_tersedia > 20 ? 'text-success' : ($m->stok_tersedia > 0 ? 'text-warning' : 'text-danger') }}">
                                {{ number_format($m->stok_tersedia, 0, ',', '.') }}
                            </span>
                        </td>
                        <td class="text-muted" style="max-width: 220px;">
                            <span class="d-inline-block text-truncate" style="max-width: 200px;">
                                {{ $m->keterangan ?: '-' }}
                            </span>
                        </td>
                        <td>
                            @if($m->is_active)
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1">
                                    <i class="bi bi-check-circle me-1"></i>Aktif
                                </span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2.5 py-1">
                                    <i class="bi bi-x-circle me-1"></i>Nonaktif
                                </span>
                            @endif
                        </td>
                        <td class="pe-3.5 text-end">
                            <div class="d-inline-flex align-items-center gap-1">
                                <button type="button" class="btn btn-light btn-sm text-primary border shadow-2xs rounded-pill px-2.5 py-1"
                                        data-bs-toggle="modal" data-bs-target="#editMaterialModal{{ $m->id }}" title="Edit Material">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <form method="POST" action="{{ route('master.materials.destroy', $m->id) }}" class="d-inline"
                                      onsubmit="return confirm('Hapus master material {{ $m->nama_material }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-light btn-sm text-danger border shadow-2xs rounded-pill px-2.5 py-1" title="Hapus Material">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-2 d-block mb-2 opacity-50"></i>
                            Tidak ada data material yang ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View (< 768px) -->
        <div class="d-block d-md-none p-2.5 p-sm-3 bg-light bg-opacity-25">
            @forelse($materials as $idx => $m)
            <div class="master-mobile-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="fw-bold text-dark d-flex align-items-center gap-2" style="font-size: 0.9rem;">
                        <span class="badge bg-teal-subtle text-teal p-1.5 rounded-2">
                            <i class="bi bi-box-seam"></i>
                        </span>
                        <span>{{ $m->nama_material }}</span>
                    </div>
                    @if($m->is_active)
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">
                            Aktif
                        </span>
                    @else
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">
                            Nonaktif
                        </span>
                    @endif
                </div>

                <div class="d-flex align-items-center justify-content-between py-2 border-top border-bottom my-2 small">
                    <div>
                        <span class="badge bg-light text-dark border font-monospace px-2 py-1">
                            {{ $m->kode_material }}
                        </span>
                        <span class="badge bg-secondary-subtle text-secondary px-2 py-1 ms-1">
                            {{ $m->satuan }}
                        </span>
                    </div>
                    <div class="text-end">
                        <div class="text-muted" style="font-size: 0.7rem;">Stok Gudang:</div>
                        <span class="fw-bold font-monospace {{ $m->stok_tersedia > 20 ? 'text-success' : 'text-danger' }}" style="font-size: 0.85rem;">
                            {{ number_format($m->stok_tersedia, 0, ',', '.') }} {{ $m->satuan }}
                        </span>
                    </div>
                </div>

                @if($m->keterangan)
                <div class="text-muted small mb-2 text-truncate" style="font-size: 0.72rem;">
                    <i class="bi bi-info-circle me-1"></i>{{ $m->keterangan }}
                </div>
                @endif

                <div class="d-flex align-items-center justify-content-end gap-1.5 mt-2">
                    <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3 py-1"
                            data-bs-toggle="modal" data-bs-target="#editMaterialModal{{ $m->id }}">
                        <i class="bi bi-pencil-square me-1"></i> Edit
                    </button>
                    <form method="POST" action="{{ route('master.materials.destroy', $m->id) }}" class="d-inline"
                          onsubmit="return confirm('Hapus master material {{ $m->nama_material }}?')">
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
                Tidak ada data material yang ditemukan.
            </div>
            @endforelse
        </div>

        @if($materials->hasPages())
        <div class="card-footer bg-white border-top p-3 d-flex justify-content-center">
            {{ $materials->links() }}
        </div>
        @endif
    </div>
</div>

<!-- ── MODAL EDIT MATERIAL (LOOP) ── -->
@foreach($materials as $m)
<div class="modal fade" id="editMaterialModal{{ $m->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-xl">
            <form method="POST" action="{{ route('master.materials.update', $m->id) }}">
                @csrf
                @method('PUT')
                <div class="modal-header border-bottom p-3.5 bg-light bg-opacity-50">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                            <i class="bi bi-pencil-square"></i>
                        </div>
                        <h6 class="modal-title fw-bold text-dark mb-0">
                            Edit Master Material
                        </h6>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-3.5">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-navy">Kode Material <span class="text-danger">*</span></label>
                            <input type="text" name="kode_material" value="{{ $m->kode_material }}" class="form-control text-uppercase" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-navy">Satuan <span class="text-danger">*</span></label>
                            <select name="satuan" class="form-select" required>
                                <option value="Meter" {{ $m->satuan === 'Meter' ? 'selected' : '' }}>Meter</option>
                                <option value="Pcs" {{ $m->satuan === 'Pcs' ? 'selected' : '' }}>Pcs</option>
                                <option value="Unit" {{ $m->satuan === 'Unit' ? 'selected' : '' }}>Unit</option>
                                <option value="Set" {{ $m->satuan === 'Set' ? 'selected' : '' }}>Set</option>
                                <option value="Roll" {{ $m->satuan === 'Roll' ? 'selected' : '' }}>Roll</option>
                                <option value="Tube" {{ $m->satuan === 'Tube' ? 'selected' : '' }}>Tube</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-navy">Nama Material / Peralatan <span class="text-danger">*</span></label>
                            <input type="text" name="nama_material" value="{{ $m->nama_material }}" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-navy">Stok Tersedia</label>
                            <input type="number" name="stok_tersedia" value="{{ $m->stok_tersedia }}" class="form-control" min="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-navy">Status</label>
                            <select name="is_active" class="form-select">
                                <option value="1" {{ $m->is_active ? 'selected' : '' }}>Aktif</option>
                                <option value="0" {{ !$m->is_active ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-navy">Keterangan / Spesifikasi</label>
                            <textarea name="keterangan" class="form-control" rows="2">{{ $m->keterangan }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top p-3 bg-light bg-opacity-25">
                    <button type="button" class="btn btn-light rounded-pill px-3.5 btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 btn-sm" style="background: linear-gradient(135deg, #10b981, #059669); border-color: #10b981;">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<!-- ── MODAL TAMBAH MATERIAL ── -->
<div class="modal fade" id="addMaterialModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-xl">
            <form method="POST" action="{{ route('master.materials.store') }}">
                @csrf
                <div class="modal-header border-bottom p-3.5 bg-light bg-opacity-50">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                            <i class="bi bi-plus-lg"></i>
                        </div>
                        <h6 class="modal-title fw-bold text-dark mb-0">
                            Tambah Master Material Baru
                        </h6>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-3.5">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-navy">Kode Material <span class="text-danger">*</span></label>
                            <input type="text" name="kode_material" class="form-control text-uppercase" placeholder="Contoh: MAT-FO-05" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-navy">Satuan <span class="text-danger">*</span></label>
                            <select name="satuan" class="form-select" required>
                                <option value="Meter">Meter</option>
                                <option value="Pcs" selected>Pcs</option>
                                <option value="Unit">Unit</option>
                                <option value="Set">Set</option>
                                <option value="Roll">Roll</option>
                                <option value="Tube">Tube</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-navy">Nama Material / Peralatan <span class="text-danger">*</span></label>
                            <input type="text" name="nama_material" class="form-control" placeholder="Contoh: Kabel Drop Core 1 Core" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-navy">Stok Awal</label>
                            <input type="number" name="stok_tersedia" class="form-control" value="0" min="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-navy">Status</label>
                            <select name="is_active" class="form-select">
                                <option value="1" selected>Aktif</option>
                                <option value="0">Nonaktif</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-navy">Keterangan / Spesifikasi</label>
                            <textarea name="keterangan" class="form-control" rows="2" placeholder="Catatan spesifikasi tambahan..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top p-3 bg-light bg-opacity-25">
                    <button type="button" class="btn btn-light rounded-pill px-3.5 btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 btn-sm" style="background: linear-gradient(135deg, #10b981, #059669); border-color: #10b981;">Simpan Material</button>
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
