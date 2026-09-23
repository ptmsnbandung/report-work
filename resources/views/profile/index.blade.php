@extends('layouts.app')

@section('title', 'Profil Pengguna')

@push('styles')
<style>
    /* ═══════════════════════════════════════════════════════════════════
       PROFILE PAGE NEUMORPHIC & BRAND BLUE STYLING
       ═══════════════════════════════════════════════════════════════════ */
    .profile-banner {
        height: 85px;
        background: linear-gradient(135deg, #07152b 0%, #0c2147 50%, #102d66 100%);
        border-radius: var(--neu-radius, 18px) var(--neu-radius, 18px) 0 0;
        position: relative;
        overflow: hidden;
    }

    .profile-banner::after {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at top right, rgba(44, 127, 255, 0.35), transparent 70%);
        pointer-events: none;
    }

    .profile-avatar-wrapper {
        position: relative;
        width: 105px;
        height: 105px;
        margin: -52px auto 0.75rem;
        z-index: 2;
    }

    .profile-avatar-img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #ffffff;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.18);
        background: #ffffff;
        display: block;
    }

    .profile-avatar-initials {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        background: linear-gradient(135deg, #2C7FFF 0%, #1b39da 100%);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.2rem;
        font-weight: 800;
        border: 4px solid #ffffff;
        box-shadow: 0 8px 24px rgba(44, 127, 255, 0.35);
        letter-spacing: 0.5px;
    }

    .avatar-edit-badge {
        position: absolute;
        bottom: 2px;
        right: 2px;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: linear-gradient(135deg, #2C7FFF 0%, #1b39da 100%);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.88rem;
        border: 2.5px solid #ffffff;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.25);
        cursor: pointer;
        transition: transform 0.2s ease, background 0.2s ease;
    }

    .avatar-edit-badge:hover {
        transform: scale(1.12);
        background: linear-gradient(135deg, #1b39da 0%, #122894 100%);
    }

    .avatar-edit-badge:active {
        transform: scale(0.95);
    }

    .profile-info-tile {
        background: var(--neu-surface, #edf2f9);
        border: 1px solid var(--neu-border, rgba(255, 255, 255, 0.85));
        border-radius: var(--neu-radius-sm, 12px);
        padding: 0.75rem 0.95rem;
        box-shadow: var(--neu-flat-xs, 2px 2px 6px #c5d1e2, -2px -2px 6px #ffffff);
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: transform 0.15s ease;
    }

    .profile-info-tile:hover {
        transform: translateY(-1px);
    }

    .password-toggle-btn {
        background: transparent;
        border: none;
        color: #64748b;
        cursor: pointer;
        padding: 0 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .password-toggle-btn:hover {
        color: #1b39da;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-0">

    <!-- ── BREADCRUMB ── -->
    <div class="d-none d-md-block mb-3">
        <x-breadcrumb :items="['Profil Pengguna' => null]" />
    </div>

    <!-- ── PAGE HEADER BAR ── -->
    <div class="d-flex justify-content-between align-items-center mb-3 p-3 px-md-3.5 rounded-xl bg-white shadow-xs border border-light">
        <div class="d-flex align-items-center gap-3">
            <div class="page-title-icon-box shadow-xs flex-shrink-0" style="background: linear-gradient(135deg, #2C7FFF, #1b39da); box-shadow: 0 4px 12px rgba(44, 127, 255, 0.35);">
                <i class="bi bi-person-badge-fill text-white"></i>
            </div>
            <div>
                <h1 class="page-title-text mb-0 fw-bold text-navy" style="font-size: 1.15rem; line-height: 1.3;">Profil &amp; Akun Saya</h1>
                <p class="text-muted small mb-0 d-none d-sm-block" style="font-size: 0.75rem; margin-top: 2px;">
                    Kelola informasi data pribadi, foto profil, dan kredensial keamanan akun Anda
                </p>
            </div>
        </div>
        <div>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 shadow-xs d-flex align-items-center justify-content-center gap-1.5" style="min-height: 36px;" title="Kembali ke Dashboard">
                <i class="bi bi-arrow-left"></i> <span class="d-none d-sm-inline fw-semibold">Dashboard</span>
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-xl shadow-xs border-0 d-flex align-items-center gap-2 mb-3" role="alert">
        <i class="bi bi-check-circle-fill fs-5 text-success"></i>
        <div>{{ session('success') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row g-3 g-md-4">
        <!-- ════ LEFT COLUMN: USER OVERVIEW CARD ════ -->
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm rounded-xl overflow-hidden bg-white text-center">
                <!-- Top Decorative Banner -->
                <div class="profile-banner"></div>

                <!-- Avatar with Interactive Trigger -->
                <div class="profile-avatar-wrapper">
                    <img src="{{ $user->avatar_url ?? '' }}" 
                         alt="{{ $user->name }}" 
                         id="leftAvatarImg" 
                         class="profile-avatar-img {{ $user->avatar_url ? '' : 'd-none' }}">

                    <div id="leftAvatarInitials" class="profile-avatar-initials {{ $user->avatar_url ? 'd-none' : '' }}">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>

                    <label for="avatarInput" class="avatar-edit-badge" title="Ganti Foto Profil">
                        <i class="bi bi-camera-fill"></i>
                    </label>
                </div>

                <div class="card-body p-3 pt-0">
                    <h5 class="fw-bold text-navy mb-1" id="leftCardUserName">{{ $user->name }}</h5>
                    <div class="text-muted small mb-2.5 d-flex align-items-center justify-content-center gap-1">
                        <i class="bi bi-envelope"></i>
                        <span id="leftCardUserEmail">{{ $user->email }}</span>
                    </div>

                    <!-- Role Badge (High Contrast & Clear) -->
                    <div class="mb-3">
                        <span class="badge rounded-pill px-3 py-1.5 fw-bold d-inline-flex align-items-center gap-1.5 shadow-2xs" 
                              style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; font-size: 0.75rem;">
                            <i class="bi bi-shield-check"></i> {{ $user->role_label }}
                        </span>
                    </div>

                    <!-- Micro-Info Tiles Grid -->
                    <div class="d-flex flex-column gap-2 text-start mt-2">
                        <!-- Nomor WhatsApp -->
                        <div class="profile-info-tile">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-whatsapp text-success fs-6"></i>
                                <span class="text-muted small">Nomor WhatsApp</span>
                            </div>
                            <span class="fw-bold text-navy small" id="leftCardUserPhone">{{ $user->phone ?: '-' }}</span>
                        </div>

                        <!-- Status Akun -->
                        <div class="profile-info-tile">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-shield-check text-success fs-6"></i>
                                <span class="text-muted small">Status Akun</span>
                            </div>
                            <span class="badge rounded-pill px-2.5 py-1 fw-bold d-inline-flex align-items-center gap-1.5 shadow-2xs" 
                                  style="background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; font-size: 0.72rem; letter-spacing: 0.2px;">
                                <span class="rounded-circle" style="width: 7px; height: 7px; background: #10b981; box-shadow: 0 0 5px rgba(16,185,129,0.7); display: inline-block;"></span>
                                Aktif
                            </span>
                        </div>

                        <!-- Tanggal Bergabung -->
                        <div class="profile-info-tile">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-calendar-check text-primary fs-6"></i>
                                <span class="text-muted small">Bergabung Sejak</span>
                            </div>
                            <span class="fw-semibold text-navy small">{{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ════ RIGHT COLUMN: EDIT PROFILE & SECURITY FORM ════ -->
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm rounded-xl overflow-hidden bg-white">
                <div class="card-header bg-white p-3 p-md-3.5 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold text-navy mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-sliders text-primary"></i> Pengaturan Data Diri &amp; Keamanan
                    </h6>
                    <span class="badge bg-light text-muted border px-2 py-1 font-monospace" style="font-size:0.68rem;">ID: #{{ $user->id }}</span>
                </div>

                <div class="card-body p-3.5 p-md-4">
                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" id="profileUpdateForm">
                        @csrf
                        @method('PUT')

                        <input type="hidden" name="remove_avatar" id="removeAvatarInput" value="0">

                        <!-- ── SECTION 1: FOTO PROFIL MANAGEMENT ── -->
                        <div class="p-3 bg-light rounded-xl border mb-4">
                            <label class="form-label small fw-bold text-navy mb-2 d-flex align-items-center gap-1.5">
                                <i class="bi bi-image-fill text-primary"></i> Foto Profil Pengguna
                            </label>

                            <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center gap-3">
                                <!-- Thumbnail Preview Box -->
                                <div class="position-relative flex-shrink-0">
                                    <img src="{{ $user->avatar_url ?? '' }}" 
                                         alt="Preview Foto" 
                                         id="formAvatarPreviewImg" 
                                         class="rounded-circle border shadow-xs {{ $user->avatar_url ? '' : 'd-none' }}" 
                                         style="width: 68px; height: 68px; object-fit: cover; background: #fff;">

                                    <div id="formAvatarInitials" class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-xs {{ $user->avatar_url ? 'd-none' : '' }}" 
                                         style="width: 68px; height: 68px; background: linear-gradient(135deg, #2C7FFF, #1b39da); font-size: 1.5rem;">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                </div>

                                <!-- Action Buttons & Hints -->
                                <div class="flex-grow-1">
                                    <div class="d-flex flex-wrap gap-2 mb-1.5">
                                        <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3 shadow-xs d-flex align-items-center gap-1.5" id="btnBrowseAvatar">
                                            <i class="bi bi-upload"></i> <span>Pilih Foto Baru</span>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3 shadow-xs d-flex align-items-center gap-1.5 {{ $user->avatar_url ? '' : 'd-none' }}" id="btnRemoveAvatar">
                                            <i class="bi bi-trash3"></i> <span>Hapus Foto</span>
                                        </button>
                                    </div>
                                    <div class="text-muted small" style="font-size: 0.72rem; line-height: 1.4;">
                                        Format file yang didukung: <strong>JPG, PNG, atau WEBP</strong>. Ukuran maksimal <strong>3MB</strong>.
                                    </div>
                                </div>

                                <!-- Hidden Real File Input -->
                                <input type="file" name="avatar" id="avatarInput" accept="image/jpeg,image/png,image/jpg,image/webp" class="d-none">
                            </div>

                            @error('avatar')
                                <div class="text-danger small mt-2 fw-semibold">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- ── SECTION 2: DATA PRIBADI ── -->
                        <div class="row g-3 mb-4">
                            <div class="col-12 col-md-6">
                                <label for="name" class="form-label small fw-bold text-navy mb-1 d-flex align-items-center gap-1">
                                    <i class="bi bi-person-fill text-primary"></i> Nama Lengkap
                                </label>
                                <input type="text" class="form-control form-control-sm @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $user->name) }}" required placeholder="Nama lengkap Anda">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="email" class="form-label small fw-bold text-navy mb-1 d-flex align-items-center gap-1">
                                    <i class="bi bi-envelope-fill text-info"></i> Alamat Email
                                </label>
                                <input type="email" class="form-control form-control-sm @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email', $user->email) }}" required placeholder="nama@connecti.id">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="phone" class="form-label small fw-bold text-navy mb-1 d-flex align-items-center gap-1">
                                    <i class="bi bi-whatsapp text-success"></i> Nomor WhatsApp / HP
                                </label>
                                <input type="text" class="form-control form-control-sm @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="Contoh: 081234567890">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-bold text-navy mb-1 d-flex align-items-center gap-1">
                                    <i class="bi bi-shield-lock-fill text-warning"></i> Role Akses (Otoritas)
                                </label>
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control form-control-sm bg-light text-muted border-end-0" value="{{ $user->role_label }} ({{ $user->role }})" disabled readonly>
                                    <span class="input-group-text bg-light border-start-0 text-muted" title="Role dikelola Admin">
                                        <i class="bi bi-lock-fill"></i>
                                    </span>
                                </div>
                                <div class="form-text text-muted" style="font-size: 0.68rem; margin-top: 3px;">
                                    Role akun diatur oleh Administrator.
                                </div>
                            </div>
                        </div>

                        <!-- ── SECTION 3: KEAMANAN & GANTI PASSWORD ── -->
                        <div class="p-3 bg-light rounded-xl border mb-4">
                            <h6 class="fw-bold text-navy mb-1 d-flex align-items-center gap-2">
                                <i class="bi bi-key-fill text-warning"></i> Ganti Password
                            </h6>
                            <p class="text-muted small mb-3" style="font-size: 0.74rem;">
                                Kosongkan kolom di bawah jika Anda tidak ingin mengubah password akun.
                            </p>

                            <div class="row g-3">
                                <div class="col-12 col-md-4">
                                    <label for="current_password" class="form-label small fw-semibold text-navy mb-1">Password Saat Ini</label>
                                    <div class="input-group input-group-sm">
                                        <input type="password" class="form-control form-control-sm @error('current_password') is-invalid @enderror" 
                                               id="current_password" name="current_password" placeholder="Password lama">
                                        <button type="button" class="input-group-text password-toggle-btn" data-target="current_password">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        @error('current_password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12 col-md-4">
                                    <label for="new_password" class="form-label small fw-semibold text-navy mb-1">Password Baru</label>
                                    <div class="input-group input-group-sm">
                                        <input type="password" class="form-control form-control-sm @error('new_password') is-invalid @enderror" 
                                               id="new_password" name="new_password" placeholder="Minimal 8 karakter">
                                        <button type="button" class="input-group-text password-toggle-btn" data-target="new_password">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        @error('new_password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12 col-md-4">
                                    <label for="new_password_confirmation" class="form-label small fw-semibold text-navy mb-1">Konfirmasi Password Baru</label>
                                    <div class="input-group input-group-sm">
                                        <input type="password" class="form-control form-control-sm" 
                                               id="new_password_confirmation" name="new_password_confirmation" placeholder="Ulangi password baru">
                                        <button type="button" class="input-group-text password-toggle-btn" data-target="new_password_confirmation">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-end gap-2 pt-2">
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-4 shadow-xs">
                                Batal
                            </a>
                            <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold shadow-xs d-flex align-items-center gap-1.5" id="btnSubmitProfile" style="background: linear-gradient(135deg, #2C7FFF 0%, #1b39da 100%); border: none; min-height: 38px;">
                                <i class="bi bi-check2-circle"></i> <span>Simpan Perubahan</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const avatarInput = document.getElementById('avatarInput');
    const btnBrowseAvatar = document.getElementById('btnBrowseAvatar');
    const btnRemoveAvatar = document.getElementById('btnRemoveAvatar');
    const removeAvatarInput = document.getElementById('removeAvatarInput');

    const leftAvatarImg = document.getElementById('leftAvatarImg');
    const leftAvatarInitials = document.getElementById('leftAvatarInitials');
    const formAvatarPreviewImg = document.getElementById('formAvatarPreviewImg');
    const formAvatarInitials = document.getElementById('formAvatarInitials');

    // Trigger file chooser
    btnBrowseAvatar?.addEventListener('click', function() {
        avatarInput?.click();
    });

    // Instant Photo Preview on File Selection
    avatarInput?.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const file = this.files[0];
            const reader = new FileReader();

            reader.onload = function(e) {
                const newSrc = e.target.result;

                // Update preview on left card
                if (leftAvatarImg) {
                    leftAvatarImg.src = newSrc;
                    leftAvatarImg.classList.remove('d-none');
                }
                if (leftAvatarInitials) {
                    leftAvatarInitials.classList.add('d-none');
                }

                // Update preview in form box
                if (formAvatarPreviewImg) {
                    formAvatarPreviewImg.src = newSrc;
                    formAvatarPreviewImg.classList.remove('d-none');
                }
                if (formAvatarInitials) {
                    formAvatarInitials.classList.add('d-none');
                }

                // Show remove button
                if (btnRemoveAvatar) {
                    btnRemoveAvatar.classList.remove('d-none');
                }

                // Reset removal flag
                if (removeAvatarInput) {
                    removeAvatarInput.value = '0';
                }
            };

            reader.readAsDataURL(file);
        }
    });

    // Remove Avatar handler
    btnRemoveAvatar?.addEventListener('click', function() {
        if (confirm('Hapus foto profil dan gunakan inisial nama?')) {
            if (avatarInput) avatarInput.value = '';
            if (removeAvatarInput) removeAvatarInput.value = '1';

            // Hide images, show initials
            if (leftAvatarImg) {
                leftAvatarImg.src = '';
                leftAvatarImg.classList.add('d-none');
            }
            if (leftAvatarInitials) {
                leftAvatarInitials.classList.remove('d-none');
            }

            if (formAvatarPreviewImg) {
                formAvatarPreviewImg.src = '';
                formAvatarPreviewImg.classList.add('d-none');
            }
            if (formAvatarInitials) {
                formAvatarInitials.classList.remove('d-none');
            }

            // Hide remove button
            btnRemoveAvatar.classList.add('d-none');
        }
    });

    // Toggle Password Visibility
    document.querySelectorAll('.password-toggle-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const targetInput = document.getElementById(targetId);
            const icon = this.querySelector('i');

            if (targetInput) {
                if (targetInput.type === 'password') {
                    targetInput.type = 'text';
                    icon.classList.remove('bi-eye');
                    icon.classList.add('bi-eye-slash');
                } else {
                    targetInput.type = 'password';
                    icon.classList.remove('bi-eye-slash');
                    icon.classList.add('bi-eye');
                }
            }
        });
    });

    // Live sync form typing with left card preview
    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    const phoneInput = document.getElementById('phone');

    nameInput?.addEventListener('input', function() {
        const leftName = document.getElementById('leftCardUserName');
        if (leftName) leftName.textContent = this.value.trim() || 'Nama Pengguna';
    });

    emailInput?.addEventListener('input', function() {
        const leftEmail = document.getElementById('leftCardUserEmail');
        if (leftEmail) leftEmail.textContent = this.value.trim() || 'email@connecti.id';
    });

    phoneInput?.addEventListener('input', function() {
        const leftPhone = document.getElementById('leftCardUserPhone');
        if (leftPhone) leftPhone.textContent = this.value.trim() || '-';
    });

    // Loading indicator on form submit
    const profileForm = document.getElementById('profileUpdateForm');
    profileForm?.addEventListener('submit', function() {
        const submitBtn = document.getElementById('btnSubmitProfile');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Menyimpan...';
        }
    });
});
</script>
@endpush
@endsection
