@extends('layouts.app')

@section('title', 'Master Material & Peralatan')

@section('content')
<div class="container-fluid px-4 py-3">

    <!-- ── HEADER ── -->
    <div class="d-md-flex align-items-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item">Master Data</li>
                    <li class="breadcrumb-item active">Material & Peralatan</li>
                </ol>
            </nav>
            <h4 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                <i class="bi bi-box-seam-fill text-teal"></i> Master Material & Peralatan
            </h4>
        </div>
        <div class="mt-3 mt-md-0">
            <button type="button" class="btn btn-teal px-3 shadow-sm rounded-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#addMaterialModal">
                <i class="bi bi-plus-lg me-1"></i> Tambah Material Baru
            </button>
        </div>
    </div>

    <!-- ── TABLE CARD ── -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-header bg-white border-bottom p-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <form method="GET" action="{{ route('master.materials.index') }}" class="d-flex align-items-center gap-2" style="max-width:340px;">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="q" value="{{ $search }}" class="form-control border-start-0" placeholder="Cari kode/nama material...">
                    @if($search)
                        <a href="{{ route('master.materials.index') }}" class="btn btn-light border" title="Reset Filter"><i class="bi bi-x"></i></a>
                    @endif
                </div>
            </form>
            <div class="text-muted small">
                Menampilkan <strong>{{ $materials->total() }}</strong> item master material
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted small text-uppercase" style="font-size:0.75rem; letter-spacing:0.5px;">
                    <tr>
                        <th class="ps-3 py-3" style="width: 50px;">No</th>
                        <th class="py-3">Kode Material</th>
                        <th class="py-3">Nama Material</th>
                        <th class="py-3">Satuan</th>
                        <th class="py-3">Stok Gudang</th>
                        <th class="py-3">Keterangan</th>
                        <th class="py-3">Status</th>
                        <th class="pe-3 py-3 text-end" style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody class="small">
                    @forelse($materials as $idx => $m)
                    <tr>
                        <td class="ps-3 text-muted">{{ $materials->firstItem() + $idx }}</td>
                        <td>
                            <span class="badge bg-light text-dark border font-monospace px-2 py-1 fw-bold">
                                {{ $m->kode_material }}
                            </span>
                        </td>
                        <td>
                            <div class="fw-bold text-dark d-flex align-items-center gap-2">
                                <span class="badge bg-teal-subtle text-teal p-1.5 rounded-2">
                                    <i class="bi bi-box"></i>
                                </span>
                                {{ $m->nama_material }}
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-secondary-subtle text-secondary px-2 py-1">{{ $m->satuan }}</span>
                        </td>
                        <td>
                            <span class="fw-bold {{ $m->stok_tersedia > 0 ? 'text-success' : 'text-danger' }}">
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
                        <td class="pe-3 text-end">
                            <button type="button" class="btn btn-sm btn-outline-primary py-1 px-2 rounded-2"
                                    data-bs-toggle="modal" data-bs-target="#editMaterialModal{{ $m->id }}" title="Edit Material">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <form method="POST" action="{{ route('master.materials.destroy', $m->id) }}" class="d-inline"
                                  onsubmit="return confirm('Hapus master material {{ $m->nama_material }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2 rounded-2" title="Hapus Material">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>

                    <!-- Modal Edit Material -->
                    <div class="modal fade" id="editMaterialModal{{ $m->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow rounded-4">
                                <form method="POST" action="{{ route('master.materials.update', $m->id) }}">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header border-bottom p-3.5">
                                        <h6 class="modal-title fw-bold text-dark">
                                            <i class="bi bi-pencil-square text-teal me-1"></i> Edit Master Material
                                        </h6>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body p-3.5">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">Kode Material <span class="text-danger">*</span></label>
                                                <input type="text" name="kode_material" value="{{ $m->kode_material }}" class="form-control text-uppercase" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">Satuan <span class="text-danger">*</span></label>
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
                                                <label class="form-label small fw-bold">Nama Material / Peralatan <span class="text-danger">*</span></label>
                                                <input type="text" name="nama_material" value="{{ $m->nama_material }}" class="form-control" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">Stok Tersedia</label>
                                                <input type="number" name="stok_tersedia" value="{{ $m->stok_tersedia }}" class="form-control" min="0">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">Status</label>
                                                <select name="is_active" class="form-select">
                                                    <option value="1" {{ $m->is_active ? 'selected' : '' }}>Aktif</option>
                                                    <option value="0" {{ !$m->is_active ? 'selected' : '' }}>Nonaktif</option>
                                                </select>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label small fw-bold">Keterangan / Spesifikasi</label>
                                                <textarea name="keterangan" class="form-control" rows="2">{{ $m->keterangan }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-top p-3">
                                        <button type="button" class="btn btn-light btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-teal btn-sm px-3">Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
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

        @if($materials->hasPages())
        <div class="card-footer bg-white border-top p-3 d-flex justify-content-center">
            {{ $materials->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modal Tambah Material -->
<div class="modal fade" id="addMaterialModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <form method="POST" action="{{ route('master.materials.store') }}">
                @csrf
                <div class="modal-header border-bottom p-3.5">
                    <h6 class="modal-title fw-bold text-dark">
                        <i class="bi bi-plus-circle text-teal me-1"></i> Tambah Master Material Baru
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-3.5">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Kode Material <span class="text-danger">*</span></label>
                            <input type="text" name="kode_material" class="form-control text-uppercase" placeholder="Contoh: MAT-FO-05" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Satuan <span class="text-danger">*</span></label>
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
                            <label class="form-label small fw-bold">Nama Material / Peralatan <span class="text-danger">*</span></label>
                            <input type="text" name="nama_material" class="form-control" placeholder="Contoh: Kabel Drop Core 1 Core" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Stok Awal</label>
                            <input type="number" name="stok_tersedia" class="form-control" value="0" min="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Status</label>
                            <select name="is_active" class="form-select">
                                <option value="1" selected>Aktif</option>
                                <option value="0">Nonaktif</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Keterangan / Spesifikasi</label>
                            <textarea name="keterangan" class="form-control" rows="2" placeholder="Catatan spesifikasi tambahan..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top p-3">
                    <button type="button" class="btn btn-light btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-teal btn-sm px-3">Simpan Material</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.bg-teal { background-color: #0d9488 !important; color: #fff !important; }
.text-teal { color: #0d9488 !important; }
.btn-teal { background: #0d9488; color: #fff; border: none; }
.btn-teal:hover { background: #0f766e; color: #fff; }
.bg-teal-subtle { background: rgba(13,148,136,0.12); }
</style>
@endsection
