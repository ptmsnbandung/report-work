@extends('layouts.app')

@section('title', 'Manajemen Pengguna & Hak Akses')

@section('content')
<div class="container-fluid px-0 pb-5 mb-5">

    <!-- ── BREADCRUMB ── -->
    <div class="d-none d-md-block mb-3">
        <x-breadcrumb :items="[
            'Dashboard' => route('dashboard'),
            'Master Data' => null,
            'Manajemen Pengguna' => null
        ]" />
    </div>

    <!-- ── PAGE HEADER & ACTIONS ── -->
    <div class="card border-0 shadow-sm rounded-xl mb-3 bg-white p-3 p-md-3.5">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2.5">
            <div class="d-flex align-items-center gap-2.5">
                <div class="page-title-icon-box shadow-xs flex-shrink-0" style="background: linear-gradient(135deg, #2563eb, #1e40af); box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35); width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 1.15rem;">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div>
                    <h1 class="page-title-text mb-0 fw-bold text-navy" style="font-size: 1.05rem; line-height: 1.3;">
                        Manajemen Pengguna &amp; Hak Akses
                    </h1>
                    <p class="text-muted small mb-0 d-none d-md-block" style="font-size: 0.75rem; margin-top: 2px;">
                        Kelola akun pengguna, konfigurasi role otoritas, reset password, dan status akun sistem
                    </p>
                </div>
            </div>

            <div>
                <button type="button" class="btn btn-primary btn-sm rounded-pill px-3.5 py-1.5 shadow-sm d-inline-flex align-items-center gap-1.5 fw-semibold" style="min-height: 34px; background: linear-gradient(135deg, #2563eb, #1e40af); border-color: #2563eb;" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="bi bi-person-plus-fill"></i>
                    <span>Tambah User Baru</span>
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
            <span class="small fw-bold">Terdapat kesalahan data pengguna:</span>
        </div>
        <ul class="mb-0 small ps-4">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- ── ROLE SUMMARY METRICS (KPI-STYLE) ── -->
    <div class="row g-2.5 g-md-3 mb-3">
        <div class="col-6 col-md-4 col-lg">
            <div class="master-stat-card border-c-blue">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="master-stat-label">Total Pengguna</span>
                    <div class="master-stat-icon bg-primary-subtle text-primary">
                        <i class="bi bi-people-fill"></i>
                    </div>
                </div>
                <div>
                    <div class="master-stat-val">{{ $users->total() }}</div>
                    <div class="master-stat-desc">Akun terdaftar</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg">
            <div class="master-stat-card border-c-blue">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="master-stat-label">Admin NOC</span>
                    <div class="master-stat-icon bg-primary-subtle text-primary">
                        <i class="bi bi-shield-lock-fill"></i>
                    </div>
                </div>
                <div>
                    <div class="master-stat-val text-primary">{{ $roleStats['admin'] ?? 0 }}</div>
                    <div class="master-stat-desc">Hak akses penuh</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg">
            <div class="master-stat-card border-c-teal">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="master-stat-label">HelpDesk NOC</span>
                    <div class="master-stat-icon bg-teal-subtle text-teal">
                        <i class="bi bi-headset"></i>
                    </div>
                </div>
                <div>
                    <div class="master-stat-val text-teal">{{ $roleStats['helpdesk'] ?? 0 }}</div>
                    <div class="master-stat-desc">Dispatch &amp; Verifikasi</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-6 col-lg">
            <div class="master-stat-card border-c-amber">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="master-stat-label">Tim Teknis</span>
                    <div class="master-stat-icon bg-warning-subtle text-warning">
                        <i class="bi bi-tools"></i>
                    </div>
                </div>
                <div>
                    <div class="master-stat-val text-warning">{{ $roleStats['teknis'] ?? 0 }}</div>
                    <div class="master-stat-desc">Teknisi lapangan</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-6 col-lg">
            <div class="master-stat-card border-c-purple">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="master-stat-label">SA / CS</span>
                    <div class="master-stat-icon bg-purple-subtle text-purple" style="background: rgba(147, 51, 234, 0.12); color: #9333ea;">
                        <i class="bi bi-person-check-fill"></i>
                    </div>
                </div>
                <div>
                    <div class="master-stat-val text-purple" style="color: #9333ea;">{{ $roleStats['sa_cs'] ?? 0 }}</div>
                    <div class="master-stat-desc">Service Assurance</div>
                </div>
            </div>
        </div>
    </div>

    <!-- ── TABLE CARD ── -->
    <div class="card border-0 shadow-sm rounded-xl overflow-hidden bg-white mb-4">
        <!-- Toolbar Header -->
        <div class="card-header bg-white border-bottom p-3 d-flex flex-wrap align-items-center justify-content-between gap-2.5">
            <form method="GET" action="{{ route('master.users.index') }}" class="d-flex flex-wrap align-items-center gap-2">
                <div class="input-group input-group-sm" style="width: 240px;">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="q" value="{{ $search }}" class="form-control border-start-0" placeholder="Cari nama, email, HP...">
                </div>
                <select name="role" class="form-select form-select-sm" style="width: 170px;" onchange="this.form.submit()">
                    <option value="">Semua Role</option>
                    <option value="admin" {{ $roleFilter === 'admin' ? 'selected' : '' }}>Admin NOC</option>
                    <option value="helpdesk" {{ $roleFilter === 'helpdesk' ? 'selected' : '' }}>HelpDesk</option>
                    <option value="teknis" {{ $roleFilter === 'teknis' ? 'selected' : '' }}>Team Teknis</option>
                    <option value="sa_cs" {{ $roleFilter === 'sa_cs' ? 'selected' : '' }}>SA / CS</option>
                </select>
                @if($search || $roleFilter)
                    <a href="{{ route('master.users.index') }}" class="btn btn-sm btn-light border text-muted"><i class="bi bi-x me-1"></i>Reset</a>
                @endif
            </form>
            <div class="text-muted small">
                Menampilkan <strong class="text-dark">{{ $users->total() }}</strong> pengguna sistem
            </div>
        </div>

        <!-- Desktop Table View -->
        <div class="table-responsive d-none d-md-block">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted small text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                    <tr>
                        <th class="ps-3.5 py-3" style="width: 50px;">No</th>
                        <th class="py-3">Pengguna</th>
                        <th class="py-3">Role Hak Akses</th>
                        <th class="py-3">No. Telepon / WA</th>
                        <th class="py-3">Status Akun</th>
                        <th class="py-3">Terdaftar</th>
                        <th class="pe-3.5 py-3 text-end" style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody class="small">
                    @forelse($users as $idx => $u)
                    @php
                        $roleBadgeColor = match($u->role) {
                            'admin' => 'primary',
                            'helpdesk' => 'teal',
                            'teknis' => 'warning',
                            'sa_cs' => 'secondary',
                            default => 'light',
                        };
                    @endphp
                    <tr>
                        <td class="ps-3.5 text-muted">{{ $users->firstItem() + $idx }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2.5">
                                <div class="topbar-avatar shadow-2xs" style="width: 36px; height: 36px; font-size: 0.8rem; background: linear-gradient(135deg, #07152b, #1e3a8a); color:#fff; border-radius: 10px; display:flex; align-items:center; justify-content:center; font-weight:700; flex-shrink: 0;">
                                    {{ strtoupper(substr($u->name, 0, 2)) }}
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">{{ $u->name }}</div>
                                    <div class="text-muted small" style="font-size: 0.75rem;">{{ $u->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($u->role === 'admin')
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 rounded-pill fw-semibold">
                                    <i class="bi bi-shield-lock me-1"></i>{{ $u->role_label }}
                                </span>
                            @elseif($u->role === 'helpdesk')
                                <span class="badge bg-teal-subtle text-teal border border-teal-subtle px-2.5 py-1 rounded-pill fw-semibold">
                                    <i class="bi bi-headset me-1"></i>{{ $u->role_label }}
                                </span>
                            @elseif($u->role === 'teknis')
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2.5 py-1 rounded-pill fw-semibold">
                                    <i class="bi bi-tools me-1"></i>{{ $u->role_label }}
                                </span>
                            @elseif($u->role === 'sa_cs')
                                <span class="badge bg-purple-subtle text-purple border border-purple-subtle px-2.5 py-1 rounded-pill fw-semibold" style="background: rgba(147, 51, 234, 0.12); color: #9333ea;">
                                    <i class="bi bi-person-check me-1"></i>{{ $u->role_label }}
                                </span>
                            @else
                                <span class="badge bg-light text-dark border px-2.5 py-1 rounded-pill fw-semibold">
                                    {{ $u->role_label }}
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($u->phone)
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $u->phone) }}" target="_blank" class="text-decoration-none text-teal fw-medium d-inline-flex align-items-center gap-1">
                                    <i class="bi bi-whatsapp text-success"></i>
                                    <span>{{ $u->phone }}</span>
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
                        <td class="pe-3.5 text-end">
                            <div class="d-inline-flex align-items-center gap-1">
                                <!-- Edit -->
                                <button type="button" class="btn btn-light btn-sm text-primary border shadow-2xs rounded-pill px-2.5 py-1"
                                        data-bs-toggle="modal" data-bs-target="#editUserModal{{ $u->id }}" title="Edit Data User">
                                    <i class="bi bi-pencil-square"></i>
                                </button>

                                <!-- Reset Password -->
                                <button type="button" class="btn btn-light btn-sm text-warning border shadow-2xs rounded-pill px-2.5 py-1"
                                        data-bs-toggle="modal" data-bs-target="#resetPasswordModal{{ $u->id }}" title="Reset Password">
                                    <i class="bi bi-key-fill"></i>
                                </button>

                                <!-- Delete -->
                                @if($u->id !== auth()->id())
                                <form method="POST" action="{{ route('master.users.destroy', $u->id) }}" class="d-inline"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus user {{ $u->name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-light btn-sm text-danger border shadow-2xs rounded-pill px-2.5 py-1" title="Hapus User">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
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

        <!-- Mobile Card View (< 768px) -->
        <div class="d-block d-md-none p-2.5 p-sm-3 bg-light bg-opacity-25">
            @forelse($users as $idx => $u)
            <div class="master-mobile-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center gap-2">
                        <div class="topbar-avatar shadow-2xs" style="width: 32px; height: 32px; font-size: 0.75rem; background: linear-gradient(135deg, #07152b, #1e3a8a); color:#fff; border-radius: 8px; display:flex; align-items:center; justify-content:center; font-weight:700;">
                            {{ strtoupper(substr($u->name, 0, 2)) }}
                        </div>
                        <div>
                            <div class="fw-bold text-dark" style="font-size: 0.88rem;">{{ $u->name }}</div>
                            <div class="text-muted small" style="font-size: 0.72rem;">{{ $u->email }}</div>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('master.users.toggle_active', $u->id) }}" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-sm p-0 border-0">
                            @if($u->is_active)
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">
                                    Aktif
                                </span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">
                                    Nonaktif
                                </span>
                            @endif
                        </button>
                    </form>
                </div>

                <div class="d-flex align-items-center justify-content-between py-2 border-top border-bottom my-2 small">
                    <div>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-0.5 fw-semibold" style="font-size: 0.7rem;">
                            {{ $u->role_label }}
                        </span>
                    </div>
                    <div class="text-end">
                        @if($u->phone)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $u->phone) }}" target="_blank" class="text-decoration-none text-teal fw-medium" style="font-size: 0.75rem;">
                                <i class="bi bi-whatsapp text-success me-1"></i>{{ $u->phone }}
                            </a>
                        @else
                            <span class="text-muted" style="font-size: 0.75rem;">-</span>
                        @endif
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-end gap-1.5 mt-2">
                    <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-2.5 py-1"
                            data-bs-toggle="modal" data-bs-target="#editUserModal{{ $u->id }}">
                        <i class="bi bi-pencil-square me-1"></i> Edit
                    </button>
                    <button type="button" class="btn btn-outline-warning btn-sm rounded-pill px-2.5 py-1 text-dark"
                            data-bs-toggle="modal" data-bs-target="#resetPasswordModal{{ $u->id }}">
                        <i class="bi bi-key-fill me-1"></i> Reset
                    </button>
                    @if($u->id !== auth()->id())
                    <form method="POST" action="{{ route('master.users.destroy', $u->id) }}" class="d-inline"
                          onsubmit="return confirm('Hapus user {{ $u->name }}?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-2.5 py-1">
                            <i class="bi bi-trash me-1"></i> Hapus
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            @empty
            <div class="text-center py-5 text-muted">
                <i class="bi bi-people fs-2 d-block mb-2 opacity-50"></i>
                Tidak ada data pengguna yang ditemukan.
            </div>
            @endforelse
        </div>

        @if($users->hasPages())
        <div class="card-footer bg-white border-top p-3 d-flex justify-content-center">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>

<!-- ── MODAL EDIT USER (LOOP) ── -->
@foreach($users as $u)
<div class="modal fade" id="editUserModal{{ $u->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-xl">
            <form method="POST" action="{{ route('master.users.update', $u->id) }}">
                @csrf
                @method('PUT')
                <div class="modal-header border-bottom p-3.5 bg-light bg-opacity-50">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                            <i class="bi bi-pencil-square"></i>
                        </div>
                        <h6 class="modal-title fw-bold text-dark mb-0">
                            Edit Pengguna: {{ $u->name }}
                        </h6>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-3.5">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-bold text-navy">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="name" value="{{ $u->name }}" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-navy">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" value="{{ $u->email }}" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-navy">Role Hak Akses <span class="text-danger">*</span></label>
                            <select name="role" class="form-select" required>
                                <option value="admin" {{ $u->role === 'admin' ? 'selected' : '' }}>Admin NOC</option>
                                <option value="helpdesk" {{ $u->role === 'helpdesk' ? 'selected' : '' }}>HelpDesk NOC</option>
                                <option value="teknis" {{ $u->role === 'teknis' ? 'selected' : '' }}>Team Teknis Lapangan</option>
                                <option value="sa_cs" {{ $u->role === 'sa_cs' ? 'selected' : '' }}>SA / CS</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-navy">No. WhatsApp / Telepon</label>
                            <input type="text" name="phone" value="{{ $u->phone }}" class="form-control" placeholder="08xxxxxxxx">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-navy">Status Akun</label>
                            <select name="is_active" class="form-select">
                                <option value="1" {{ $u->is_active ? 'selected' : '' }}>Aktif</option>
                                <option value="0" {{ !$u->is_active ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top p-3 bg-light bg-opacity-25">
                    <button type="button" class="btn btn-light rounded-pill px-3.5 btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 btn-sm" style="background: linear-gradient(135deg, #2563eb, #1e40af); border-color: #2563eb;">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Reset Password -->
<div class="modal fade" id="resetPasswordModal{{ $u->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg rounded-xl">
            <form method="POST" action="{{ route('master.users.reset_password', $u->id) }}">
                @csrf
                <div class="modal-header border-bottom p-3.5 bg-light bg-opacity-50">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle bg-warning-subtle text-warning d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                            <i class="bi bi-key-fill"></i>
                        </div>
                        <h6 class="modal-title fw-bold text-dark mb-0">
                            Reset Password
                        </h6>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-3.5">
                    <p class="small text-muted mb-2">Reset password untuk: <strong>{{ $u->name }}</strong></p>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-navy">Password Baru <span class="text-danger">*</span></label>
                        <input type="password" name="new_password" class="form-control" required minlength="6" placeholder="Min. 6 karakter">
                    </div>
                </div>
                <div class="modal-footer border-top p-3 bg-light bg-opacity-25">
                    <button type="button" class="btn btn-light rounded-pill px-3.5 btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-4 btn-sm text-dark fw-bold">Reset Password</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<!-- ── MODAL TAMBAH USER ── -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-xl">
            <form method="POST" action="{{ route('master.users.store') }}">
                @csrf
                <div class="modal-header border-bottom p-3.5 bg-light bg-opacity-50">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                            <i class="bi bi-person-plus-fill"></i>
                        </div>
                        <h6 class="modal-title fw-bold text-dark mb-0">
                            Tambah Pengguna Baru
                        </h6>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-3.5">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-bold text-navy">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Contoh: Andi Pratama" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-navy">Alamat Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" placeholder="andi@connecti.id" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-navy">Role Hak Akses <span class="text-danger">*</span></label>
                            <select name="role" class="form-select" required>
                                <option value="teknis" selected>Team Teknis Lapangan</option>
                                <option value="helpdesk">HelpDesk NOC</option>
                                <option value="sa_cs">SA / CS</option>
                                <option value="admin">Admin NOC</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-navy">No. WhatsApp / Telepon</label>
                            <input type="text" name="phone" class="form-control" placeholder="08123456789">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-navy">Password Awal <span class="text-danger">*</span></label>
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
                <div class="modal-footer border-top p-3 bg-light bg-opacity-25">
                    <button type="button" class="btn btn-light rounded-pill px-3.5 btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 btn-sm" style="background: linear-gradient(135deg, #2563eb, #1e40af); border-color: #2563eb;">Simpan Pengguna</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.bg-teal { background-color: #0d9488 !important; color: #fff !important; }
.text-teal { color: #0d9488 !important; }
.bg-teal-subtle { background: rgba(13,148,136,0.12) !important; color: #0d9488 !important; }
.border-teal-subtle { border-color: rgba(13,148,136,0.25) !important; }
</style>
@endsection
