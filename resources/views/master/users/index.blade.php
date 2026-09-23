@extends('layouts.app')

@section('title', 'Manajemen User & Role')

@section('content')
<div class="container-fluid px-4 py-3">

    <!-- ── HEADER ── -->
    <div class="d-md-flex align-items-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item">Master Data</li>
                    <li class="breadcrumb-item active">Manajemen User</li>
                </ol>
            </nav>
            <h4 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                <i class="bi bi-people-fill text-teal"></i> Manajemen Pengguna & Hak Akses
            </h4>
        </div>
        <div class="mt-3 mt-md-0">
            <button type="button" class="btn btn-teal px-3 shadow-sm rounded-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#addUserModal">
                <i class="bi bi-person-plus-fill me-1"></i> Tambah User Baru
            </button>
        </div>
    </div>

    <!-- ── ROLE SUMMARY METRICS ── -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-2">
            <div class="card border-0 shadow-sm rounded-4 p-3 text-center">
                <div class="text-muted small fw-semibold">Total User</div>
                <div class="fs-4 fw-bold text-dark">{{ $users->total() }}</div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card border-0 shadow-sm rounded-4 p-3 text-center">
                <div class="text-muted small fw-semibold">Admin NOC</div>
                <div class="fs-4 fw-bold text-primary">{{ $roleStats['admin'] ?? 0 }}</div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card border-0 shadow-sm rounded-4 p-3 text-center">
                <div class="text-muted small fw-semibold">HelpDesk</div>
                <div class="fs-4 fw-bold text-teal">{{ $roleStats['helpdesk'] ?? 0 }}</div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card border-0 shadow-sm rounded-4 p-3 text-center">
                <div class="text-muted small fw-semibold">Tim Teknis</div>
                <div class="fs-4 fw-bold text-warning">{{ $roleStats['teknis'] ?? 0 }}</div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card border-0 shadow-sm rounded-4 p-3 text-center">
                <div class="text-muted small fw-semibold">SA / CS</div>
                <div class="fs-4 fw-bold text-purple" style="color: #9333ea;">{{ $roleStats['sa_cs'] ?? 0 }}</div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card border-0 shadow-sm rounded-4 p-3 text-center">
                <div class="text-muted small fw-semibold">Client</div>
                <div class="fs-4 fw-bold text-secondary">{{ $roleStats['client'] ?? 0 }}</div>
            </div>
        </div>
    </div>

    <!-- ── USER TABLE CARD ── -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <!-- Filter Header -->
        <div class="card-header bg-white border-bottom p-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <form method="GET" action="{{ route('master.users.index') }}" class="d-flex flex-wrap align-items-center gap-2">
                <div class="input-group input-group-sm" style="width: 250px;">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="q" value="{{ $search }}" class="form-control border-start-0" placeholder="Cari nama, email, HP...">
                </div>
                <select name="role" class="form-select form-select-sm" style="width: 170px;" onchange="this.form.submit()">
                    <option value="">Semua Role</option>
                    <option value="admin" {{ $roleFilter === 'admin' ? 'selected' : '' }}>Admin NOC</option>
                    <option value="helpdesk" {{ $roleFilter === 'helpdesk' ? 'selected' : '' }}>HelpDesk</option>
                    <option value="teknis" {{ $roleFilter === 'teknis' ? 'selected' : '' }}>Team Teknis</option>
                    <option value="sa_cs" {{ $roleFilter === 'sa_cs' ? 'selected' : '' }}>SA / CS</option>
                    <option value="client" {{ $roleFilter === 'client' ? 'selected' : '' }}>Client</option>
                </select>
                @if($search || $roleFilter)
                    <a href="{{ route('master.users.index') }}" class="btn btn-sm btn-light border"><i class="bi bi-x me-1"></i>Reset</a>
                @endif
            </form>
            <div class="text-muted small">
                Menampilkan <strong>{{ $users->total() }}</strong> pengguna sistem
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted small text-uppercase" style="font-size:0.75rem; letter-spacing:0.5px;">
                    <tr>
                        <th class="ps-3 py-3" style="width: 50px;">No</th>
                        <th class="py-3">Pengguna</th>
                        <th class="py-3">Role Hak Akses</th>
                        <th class="py-3">No. Telepon / WA</th>
                        <th class="py-3">Status Akun</th>
                        <th class="py-3">Terdaftar</th>
                        <th class="pe-3 py-3 text-end" style="width: 180px;">Aksi</th>
                    </tr>
                </thead>
                <tbody class="small">
                    @forelse($users as $idx => $u)
                    @php
                        $roleBadgeColor = match($u->role) {
                            'admin' => 'primary',
                            'helpdesk' => 'info',
                            'teknis' => 'warning',
                            'sa_cs' => 'secondary',
                            'client' => 'dark',
                            default => 'light',
                        };
                    @endphp
                    <tr>
                        <td class="ps-3 text-muted">{{ $users->firstItem() + $idx }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2.5">
                                <div class="topbar-avatar" style="width:34px; height:34px; font-size:0.8rem; background: linear-gradient(135deg, #0f3d63, #0d9488); color:#fff; border-radius:10px; display:flex; align-items:center; justify-content:center; font-weight:700;">
                                    {{ strtoupper(substr($u->name, 0, 2)) }}
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">{{ $u->name }}</div>
                                    <div class="text-muted small" style="font-size:0.75rem;">{{ $u->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-{{ $roleBadgeColor }}-subtle text-{{ $roleBadgeColor }} border border-{{ $roleBadgeColor }}-subtle px-2.5 py-1 rounded-pill fw-semibold">
                                {{ $u->role_label }}
                            </span>
                        </td>
                        <td>
                            @if($u->phone)
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $u->phone) }}" target="_blank" class="text-decoration-none text-teal fw-medium">
                                    <i class="bi bi-whatsapp me-1 text-success"></i>{{ $u->phone }}
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <form method="POST" action="{{ route('master.users.toggle_active', $u->id) }}" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm p-0 border-0" title="Klik untuk ubah status aktif/nonaktif">
                                    @if($u->is_active)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1">
                                            <i class="bi bi-check-circle-fill me-1"></i>Aktif
                                        </span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2.5 py-1">
                                            <i class="bi bi-x-circle-fill me-1"></i>Nonaktif
                                        </span>
                                    @endif
                                </button>
                            </form>
                        </td>
                        <td class="text-muted">
                            {{ $u->created_at ? $u->created_at->format('d M Y') : '-' }}
                        </td>
                        <td class="pe-3 text-end">
                            <!-- Edit -->
                            <button type="button" class="btn btn-sm btn-outline-primary py-1 px-2 rounded-2"
                                    data-bs-toggle="modal" data-bs-target="#editUserModal{{ $u->id }}" title="Edit Data User">
                                <i class="bi bi-pencil-square"></i>
                            </button>

                            <!-- Reset Password -->
                            <button type="button" class="btn btn-sm btn-outline-warning py-1 px-2 rounded-2 text-dark"
                                    data-bs-toggle="modal" data-bs-target="#resetPasswordModal{{ $u->id }}" title="Reset Password">
                                <i class="bi bi-key-fill"></i>
                            </button>

                            <!-- Delete -->
                            @if($u->id !== auth()->id())
                            <form method="POST" action="{{ route('master.users.destroy', $u->id) }}" class="d-inline"
                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus user {{ $u->name }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2 rounded-2" title="Hapus User">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>

                    <!-- Modal Edit User -->
                    <div class="modal fade" id="editUserModal{{ $u->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow rounded-4">
                                <form method="POST" action="{{ route('master.users.update', $u->id) }}">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header border-bottom p-3.5">
                                        <h6 class="modal-title fw-bold text-dark">
                                            <i class="bi bi-pencil-square text-teal me-1"></i> Edit Pengguna: {{ $u->name }}
                                        </h6>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body p-3.5">
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <label class="form-label small fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
                                                <input type="text" name="name" value="{{ $u->name }}" class="form-control" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">Email <span class="text-danger">*</span></label>
                                                <input type="email" name="email" value="{{ $u->email }}" class="form-control" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">Role Hak Akses <span class="text-danger">*</span></label>
                                                <select name="role" class="form-select" required>
                                                    <option value="admin" {{ $u->role === 'admin' ? 'selected' : '' }}>Admin NOC</option>
                                                    <option value="helpdesk" {{ $u->role === 'helpdesk' ? 'selected' : '' }}>HelpDesk</option>
                                                    <option value="teknis" {{ $u->role === 'teknis' ? 'selected' : '' }}>Team Teknis</option>
                                                    <option value="sa_cs" {{ $u->role === 'sa_cs' ? 'selected' : '' }}>SA / CS</option>
                                                    <option value="client" {{ $u->role === 'client' ? 'selected' : '' }}>Client</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">No. WhatsApp / Telepon</label>
                                                <input type="text" name="phone" value="{{ $u->phone }}" class="form-control" placeholder="08xxxxxxxx">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">Status Akun</label>
                                                <select name="is_active" class="form-select">
                                                    <option value="1" {{ $u->is_active ? 'selected' : '' }}>Aktif</option>
                                                    <option value="0" {{ !$u->is_active ? 'selected' : '' }}>Nonaktif</option>
                                                </select>
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

                    <!-- Modal Reset Password -->
                    <div class="modal fade" id="resetPasswordModal{{ $u->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-sm">
                            <div class="modal-content border-0 shadow rounded-4">
                                <form method="POST" action="{{ route('master.users.reset_password', $u->id) }}">
                                    @csrf
                                    <div class="modal-header border-bottom p-3.5">
                                        <h6 class="modal-title fw-bold text-dark">
                                            <i class="bi bi-key-fill text-warning me-1"></i> Reset Password
                                        </h6>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body p-3.5">
                                        <p class="small text-muted mb-2">Reset password untuk user: <strong>{{ $u->name }}</strong></p>
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold">Password Baru <span class="text-danger">*</span></label>
                                            <input type="password" name="new_password" class="form-control" required minlength="6" placeholder="Min. 6 karakter">
                                        </div>
                                    </div>
                                    <div class="modal-footer border-top p-3">
                                        <button type="button" class="btn btn-light btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-warning btn-sm px-3 text-dark fw-bold">Reset</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-people fs-2 d-block mb-2 opacity-50"></i>
                            Tidak ada data pengguna yang ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
        <div class="card-footer bg-white border-top p-3 d-flex justify-content-center">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modal Tambah User -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <form method="POST" action="{{ route('master.users.store') }}">
                @csrf
                <div class="modal-header border-bottom p-3.5">
                    <h6 class="modal-title fw-bold text-dark">
                        <i class="bi bi-person-plus text-teal me-1"></i> Tambah Pengguna Baru
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-3.5">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Contoh: Andi Pratama" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Alamat Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" placeholder="andi@connecti.id" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Role Hak Akses <span class="text-danger">*</span></label>
                            <select name="role" class="form-select" required>
                                <option value="teknis" selected>Team Teknis Lapangan</option>
                                <option value="helpdesk">HelpDesk NOC</option>
                                <option value="sa_cs">SA / CS</option>
                                <option value="client">Client / Pelanggan</option>
                                <option value="admin">Admin NOC</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">No. WhatsApp / Telepon</label>
                            <input type="text" name="phone" class="form-control" placeholder="08123456789">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Password Awal <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" placeholder="Min. 6 karakter" required minlength="6">
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_active" id="isActiveNew" value="1" checked>
                                <label class="form-check-label small" for="isActiveNew">
                                    Akun langsung aktif dan dapat digunakan login
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top p-3">
                    <button type="button" class="btn btn-light btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-teal btn-sm px-3">Simpan User</button>
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
