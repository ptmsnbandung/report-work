@extends('layouts.app')

@section('title', 'Master SLA Target')

@section('content')
<div class="container-fluid px-4 py-3">

    <!-- ── HEADER ── -->
    <div class="d-md-flex align-items-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item">Master Data</li>
                    <li class="breadcrumb-item active">SLA Target</li>
                </ol>
            </nav>
            <h4 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                <i class="bi bi-stopwatch-fill text-teal"></i> Master Target SLA Backbone
            </h4>
        </div>
        <div class="mt-3 mt-md-0">
            <button type="button" class="btn btn-teal px-3 shadow-sm rounded-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#addSlaModal">
                <i class="bi bi-plus-lg me-1"></i> Tambah Target SLA
            </button>
        </div>
    </div>

    <!-- ── INFO ALERT BANNER ── -->
    <div class="alert alert-light border shadow-sm rounded-4 p-3.5 mb-4 d-flex align-items-center gap-3">
        <div class="stat-icon-box bg-teal-subtle text-teal flex-shrink-0">
            <i class="bi bi-info-circle-fill fs-4"></i>
        </div>
        <div class="small text-muted">
            <strong class="text-dark">Standar Service Level Agreement (SLA) Backbone:</strong>
            Waktu target perbaikan dihitung sejak status <strong>OPEN</strong> hingga tiket di-<strong>CLOSE</strong> (MTTR). Tiket yang diselesaikan melebihi batas waktu ini akan ditandai <span class="badge bg-danger">LEBIH SLA</span> secara otomatis.
        </div>
    </div>

    <!-- ── TABLE CARD ── -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-header bg-white border-bottom p-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <form method="GET" action="{{ route('master.sla.index') }}" class="d-flex align-items-center gap-2" style="max-width:320px;">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="q" value="{{ $search }}" class="form-control border-start-0" placeholder="Cari segment SLA...">
                    @if($search)
                        <a href="{{ route('master.sla.index') }}" class="btn btn-light border" title="Reset Filter"><i class="bi bi-x"></i></a>
                    @endif
                </div>
            </form>
            <div class="text-muted small">
                Menampilkan <strong>{{ $slas->total() }}</strong> konfigurasi target SLA
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted small text-uppercase" style="font-size:0.75rem; letter-spacing:0.5px;">
                    <tr>
                        <th class="ps-3 py-3" style="width: 60px;">No</th>
                        <th class="py-3">Segment Backbone</th>
                        <th class="py-3">Target Durasi (Menit)</th>
                        <th class="py-3">Target Durasi (Jam)</th>
                        <th class="py-3">Terakhir Diupdate</th>
                        <th class="pe-3 py-3 text-end" style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody class="small">
                    @forelse($slas as $idx => $s)
                    @php
                        $jam = floor($s->sla_target_minutes / 60);
                        $menit = $s->sla_target_minutes % 60;
                        $formatted = ($jam > 0 ? "{$jam} Jam " : "") . ($menit > 0 ? "{$menit} Menit" : "");
                    @endphp
                    <tr>
                        <td class="ps-3 text-muted">{{ $slas->firstItem() + $idx }}</td>
                        <td>
                            <div class="fw-bold text-dark d-flex align-items-center gap-2">
                                <span class="badge bg-teal-subtle text-teal p-1.5 rounded-2">
                                    <i class="bi bi-shield-check"></i>
                                </span>
                                {{ $s->backbone_segment }}
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border px-2.5 py-1.5 fw-bold font-monospace">
                                {{ $s->sla_target_minutes }} mnt
                            </span>
                        </td>
                        <td>
                            <span class="fw-semibold text-teal">{{ $formatted ?: '-' }}</span>
                        </td>
                        <td class="text-muted">
                            {{ $s->updated_at ? $s->updated_at->format('d M Y, H:i') : '-' }}
                        </td>
                        <td class="pe-3 text-end">
                            <button type="button" class="btn btn-sm btn-outline-primary py-1 px-2 rounded-2"
                                    data-bs-toggle="modal" data-bs-target="#editSlaModal{{ $s->id }}" title="Edit Target SLA">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <form method="POST" action="{{ route('master.sla.destroy', $s->id) }}" class="d-inline"
                                  onsubmit="return confirm('Hapus konfigurasi SLA untuk {{ $s->backbone_segment }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2 rounded-2" title="Hapus SLA">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>

                    <!-- Modal Edit SLA -->
                    <div class="modal fade" id="editSlaModal{{ $s->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow rounded-4">
                                <form method="POST" action="{{ route('master.sla.update', $s->id) }}">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header border-bottom p-3.5">
                                        <h6 class="modal-title fw-bold text-dark">
                                            <i class="bi bi-pencil-square text-teal me-1"></i> Edit Target SLA
                                        </h6>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body p-3.5">
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold">Segment Backbone <span class="text-danger">*</span></label>
                                            <input type="text" name="backbone_segment" value="{{ $s->backbone_segment }}" class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold">Target Waktu SLA (Menit) <span class="text-danger">*</span></label>
                                            <input type="number" name="sla_target_minutes" value="{{ $s->sla_target_minutes }}" class="form-control" min="1" required>
                                            <div class="form-text small">180 = 3 Jam, 240 = 4 Jam, 300 = 5 Jam, 360 = 6 Jam.</div>
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
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-2 d-block mb-2 opacity-50"></i>
                            Tidak ada data target SLA yang ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($slas->hasPages())
        <div class="card-footer bg-white border-top p-3 d-flex justify-content-center">
            {{ $slas->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modal Tambah SLA -->
<div class="modal fade" id="addSlaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <form method="POST" action="{{ route('master.sla.store') }}">
                @csrf
                <div class="modal-header border-bottom p-3.5">
                    <h6 class="modal-title fw-bold text-dark">
                        <i class="bi bi-plus-circle text-teal me-1"></i> Tambah Konfigurasi SLA Baru
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-3.5">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Segment Backbone <span class="text-danger">*</span></label>
                        <input type="text" name="backbone_segment" class="form-control" placeholder="Contoh: SW Cimahi - SW Padalarang" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Target Waktu SLA (Menit) <span class="text-danger">*</span></label>
                        <input type="number" name="sla_target_minutes" class="form-control" value="360" min="1" required>
                        <div class="form-text small">Standar default: 360 Menit (6 Jam).</div>
                    </div>
                </div>
                <div class="modal-footer border-top p-3">
                    <button type="button" class="btn btn-light btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-teal btn-sm px-3">Simpan SLA</button>
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
.stat-icon-box {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
@endsection
