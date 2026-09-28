@extends('layouts.app')

@section('title', 'Open Tiket Baru')

@push('styles')
<style>
    /* ── FORM CONTROL ENHANCEMENTS (SOFT UI MODERN) ── */
    .form-control-custom,
    .form-select-custom {
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        padding: 0.68rem 0.95rem;
        font-size: 0.88rem;
        color: #1e293b;
        background-color: #f8fafc;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .form-control-custom:focus,
    .form-select-custom:focus {
        background-color: #ffffff;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3.5px rgba(59, 130, 246, 0.14);
        outline: none;
    }

    .form-label-custom {
        font-size: 0.82rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 0.4rem;
        display: flex;
        align-items: center;
        gap: 0.35rem;
    }

    .input-hint-box {
        background: rgba(241, 245, 249, 0.85);
        border: 1px solid rgba(226, 232, 240, 0.7);
        border-radius: 8px;
        padding: 0.35rem 0.65rem;
        font-size: 0.74rem;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 0.4rem;
        margin-top: 0.38rem;
    }

    /* Category Selection Cards */
    .kategori-card {
        border: 2px solid #e2e8f0;
        border-radius: 14px;
        padding: 1rem 1.1rem;
        cursor: pointer;
        transition: all 0.22s ease;
        background: #f8fafc;
        position: relative;
    }
    .kategori-card:hover {
        border-color: #93c5fd;
        background: #ffffff;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.08);
    }
    .kategori-card.active {
        border-color: #2563eb;
        background: #eff6ff;
        box-shadow: 0 4px 14px rgba(37, 99, 235, 0.15);
    }
    .kategori-card.active .kategori-indicator {
        background: #2563eb;
        color: #fff;
        border-color: #2563eb;
    }
    .kategori-indicator {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        border: 2px solid #cbd5e1;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.65rem;
    }

    /* IMS Autocomplete Dropdown */
    .ims-search-wrapper {
        position: relative;
    }
    .ims-dropdown-results {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        z-index: 1050;
        background: #ffffff;
        border: 1.5px solid #3b82f6;
        border-radius: 12px;
        max-height: 280px;
        overflow-y: auto;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        margin-top: 4px;
    }
    .ims-result-item {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #f1f5f9;
        cursor: pointer;
        transition: background 0.15s ease;
    }
    .ims-result-item:last-child {
        border-bottom: none;
    }
    .ims-result-item:hover {
        background: #f0f7ff;
    }

    /* Target SLA add-on pill */
    .sla-preview-addon {
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.1) 0%, rgba(29, 78, 216, 0.12) 100%);
        border: 1.5px solid #e2e8f0;
        border-left: 0;
        border-radius: 0 12px 12px 0;
        color: #1d4ed8;
        font-weight: 700;
        font-size: 0.82rem;
        padding: 0 1rem;
        display: flex;
        align-items: center;
    }

    /* Submit Button Glow */
    .btn-submit-tiket {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        border: none;
        color: #ffffff;
        font-weight: 700;
        transition: transform 0.18s ease, box-shadow 0.18s ease;
        box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
    }

    .btn-submit-tiket:hover {
        background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
        color: #ffffff;
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(37, 99, 235, 0.45);
    }

    .btn-submit-tiket:active {
        transform: scale(0.98);
    }

    @media (max-width: 991.98px) {
        .form-actions-wrapper {
            margin-bottom: 5.5rem !important;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-0 pb-4">

    <!-- ── BREADCRUMB ── -->
    <x-breadcrumb :items="[
        'Daftar Tiket Gangguan' => route('tiket.index'),
        'Open Tiket Baru' => null
    ]" />

    <!-- ── MODERN HERO HEADER ── -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden" style="background: linear-gradient(135deg, #07152b 0%, #0d2352 60%, #173b82 100%);">
        <div class="card-body p-3 p-md-4 position-relative">
            <div class="position-absolute end-0 top-0 w-50 h-100 opacity-20 pointer-events-none" style="background: radial-gradient(circle at right top, #2C7FFF, transparent 70%);"></div>

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 position-relative z-1">
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center text-white shadow-sm flex-shrink-0" style="width: 48px; height: 48px; border-radius: 14px; background: linear-gradient(135deg, #2563eb, #1d4ed8); border: 1.5px solid rgba(255,255,255,0.25);">
                        <i class="bi bi-plus-circle-fill fs-4 text-white"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                            <h4 class="fw-bold text-white mb-0" style="letter-spacing: -0.3px;">Open Tiket Gangguan Baru</h4>
                            <span class="badge bg-danger bg-opacity-90 text-white border border-danger-subtle px-2 py-0p5 small rounded-pill d-inline-flex align-items-center gap-1" style="font-size: 0.68rem;">
                                <span class="spinner-grow spinner-grow-sm" role="status" style="width: 0.4rem; height: 0.4rem;"></span>STATUS: OPEN
                            </span>
                        </div>
                        <p class="text-white-50 small mb-0">Input tiket gangguan Backbone FO atau Maintenance Pelanggan Broadband FTTH</p>
                    </div>
                </div>

                <a href="{{ route('tiket.index') }}" class="btn btn-sm text-white px-3 py-2 rounded-pill flex-shrink-0 d-inline-flex align-items-center gap-2" style="background: rgba(255, 255, 255, 0.12); border: 1px solid rgba(255, 255, 255, 0.25); backdrop-filter: blur(8px); transition: all 0.2s ease;">
                    <i class="bi bi-arrow-left"></i>
                    <span class="fw-semibold">Kembali ke Daftar</span>
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- ── FORM SECTION (LEFT) ── -->
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
                <!-- Card Header -->
                <div class="card-header bg-white py-3 px-3 px-md-4 border-bottom d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <div class="p-2 rounded-3 text-primary d-flex align-items-center justify-content-center" style="background: rgba(37, 99, 235, 0.08); width: 34px; height: 34px;">
                            <i class="bi bi-file-earmark-medical-fill fs-5"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0 text-navy">Formulir Tiket Gangguan</h6>
                            <small class="text-muted" style="font-size: 0.75rem;">Pilih kategori tiket & lengkapi rincian kendala</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-1 font-monospace small" id="previewNoTiketMobile">
                            {{ $previewNoTiket }}
                        </span>
                    </div>
                </div>

                <div class="card-body p-3 p-md-4">
                    <form action="{{ route('tiket.store') }}" method="POST" id="formOpenTiket">
                        @csrf

                        <!-- 0. PILIHAN KATEGORI TIKET -->
                        <div class="mb-4">
                            <label class="form-label-custom mb-2">
                                <i class="bi bi-tags-fill text-primary"></i> Kategori Tiket Gangguan
                                <span class="badge bg-danger-subtle text-danger ms-1" style="font-size: 0.6rem; padding: 2px 5px;">Wajib</span>
                            </label>
                            <input type="hidden" name="kategori_tiket" id="kategori_tiket_input" value="{{ old('kategori_tiket', 'BACKBONE') }}">
                            
                            <div class="row g-3">
                                <!-- Backbone Card Option -->
                                <div class="col-12 col-sm-6">
                                    <div class="kategori-card {{ old('kategori_tiket', 'BACKBONE') === 'BACKBONE' ? 'active' : '' }}" id="optBackbone" onclick="selectCategory('BACKBONE')">
                                        <div class="d-flex align-items-start justify-content-between">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="p-2 rounded-3 bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                                    <i class="bi bi-diagram-3-fill fs-5"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-navy" style="font-size: 0.9rem;">Backbone FO</div>
                                                    <small class="text-muted d-block" style="font-size: 0.72rem;">Transmisi kabel optik utama</small>
                                                </div>
                                            </div>
                                            <div class="kategori-indicator"><i class="bi bi-check"></i></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Broadband Card Option -->
                                <div class="col-12 col-sm-6">
                                    <div class="kategori-card {{ old('kategori_tiket') === 'BROADBAND' ? 'active' : '' }}" id="optBroadband" onclick="selectCategory('BROADBAND')">
                                        <div class="d-flex align-items-start justify-content-between">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="p-2 rounded-3 bg-indigo bg-opacity-10 text-indigo d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; color: #4f46e5; background: rgba(79, 70, 229, 0.1);">
                                                    <i class="bi bi-router-fill fs-5"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-navy" style="font-size: 0.9rem;">Maintenance Broadband</div>
                                                    <small class="text-muted d-block" style="font-size: 0.72rem;">User FTTH / Pelanggan Home</small>
                                                </div>
                                            </div>
                                            <div class="kategori-indicator"><i class="bi bi-check"></i></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ═══════════════════════════════════════════════════════════════ -->
                        <!-- ── SECTION A: KHUSUS MAINTENANCE BROADBAND (IMS INTEGRATION) ── -->
                        <!-- ═══════════════════════════════════════════════════════════════ -->
                        <div id="sectionBroadband" style="{{ old('kategori_tiket', 'BACKBONE') === 'BROADBAND' ? '' : 'display: none;' }}">
                            <div class="p-3 mb-4 rounded-4" style="background: #f8fafc; border: 1.5px dashed #cbd5e1;">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge text-white px-2 py-1" style="background-color: #4f46e5;">
                                            <i class="bi bi-database-check me-1"></i> IMS Database
                                        </span>
                                        <span class="fw-bold text-navy small">Cari Data Pelanggan (Auto-fill)</span>
                                    </div>
                                    <span class="badge bg-light text-muted border font-monospace small" id="imsStatusBadge">Koneksi Aktif</span>
                                </div>

                                <!-- Live Autocomplete Search Input -->
                                <div class="ims-search-wrapper mb-3">
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0" style="border-radius: 12px 0 0 12px; border-color: #cbd5e1;">
                                            <i class="bi bi-search text-primary" id="imsSearchIcon"></i>
                                            <span class="spinner-border spinner-border-sm text-primary d-none" id="imsSearchSpinner" role="status"></span>
                                        </span>
                                        <input type="text"
                                               class="form-control form-control-custom border-start-0 ps-0"
                                               id="imsSearchInput"
                                               placeholder="Ketik Nomor Internet (CID), NIK, atau Nama Pelanggan..."
                                               autocomplete="off"
                                               style="border-radius: 0 12px 12px 0;">
                                    </div>
                                    <!-- Results Dropdown -->
                                    <div class="ims-dropdown-results d-none" id="imsResultsDropdown"></div>
                                    <div class="input-hint-box mt-1">
                                        <i class="bi bi-info-circle text-primary"></i>
                                        <span>Ketik minimal 2 karakter untuk mencari otomatis dari sistem IMS. Klik data untuk auto-fill.</span>
                                    </div>
                                </div>

                                <!-- Form Grid Detail Pelanggan Broadband -->
                                <div class="row g-3">
                                    <!-- ID Pelanggan (CID) -->
                                    <div class="col-12 col-md-6">
                                        <label for="id_pelanggan" class="form-label-custom">
                                            <i class="bi bi-person-badge text-primary"></i> ID Pelanggan / CID
                                            <span class="badge bg-danger-subtle text-danger ms-1" style="font-size: 0.6rem; padding: 2px 5px;">Wajib</span>
                                        </label>
                                        <input type="text"
                                               class="form-control form-control-custom @error('id_pelanggan') is-invalid @enderror"
                                               id="id_pelanggan"
                                               name="id_pelanggan"
                                               value="{{ old('id_pelanggan') }}"
                                               placeholder="Contoh: 10029384">
                                        @error('id_pelanggan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Nama Pelanggan -->
                                    <div class="col-12 col-md-6">
                                        <label for="nama_pelanggan" class="form-label-custom">
                                            <i class="bi bi-person-fill text-primary"></i> Nama Pelanggan
                                            <span class="badge bg-danger-subtle text-danger ms-1" style="font-size: 0.6rem; padding: 2px 5px;">Wajib</span>
                                        </label>
                                        <input type="text"
                                               class="form-control form-control-custom @error('nama_pelanggan') is-invalid @enderror"
                                               id="nama_pelanggan"
                                               name="nama_pelanggan"
                                               value="{{ old('nama_pelanggan') }}"
                                               placeholder="Nama lengkap pelanggan">
                                        @error('nama_pelanggan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- No Kontak / WhatsApp -->
                                    <div class="col-12 col-md-6">
                                        <label for="no_kontak_pelanggan" class="form-label-custom">
                                            <i class="bi bi-whatsapp text-success"></i> No. HP / WhatsApp Pelanggan
                                        </label>
                                        <input type="text"
                                               class="form-control form-control-custom @error('no_kontak_pelanggan') is-invalid @enderror"
                                               id="no_kontak_pelanggan"
                                               name="no_kontak_pelanggan"
                                               value="{{ old('no_kontak_pelanggan') }}"
                                               placeholder="Contoh: 081234567890">
                                        @error('no_kontak_pelanggan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Titik ODP / Fat -->
                                    <div class="col-12 col-md-6">
                                        <label for="titik_odp" class="form-label-custom">
                                            <i class="bi bi-hdd-rack text-primary"></i> Titik ODP / FAT
                                        </label>
                                        <input type="text"
                                               class="form-control form-control-custom @error('titik_odp') is-invalid @enderror"
                                               id="titik_odp"
                                               name="titik_odp"
                                               value="{{ old('titik_odp') }}"
                                               placeholder="Contoh: ODP-BBL-01 / Port 4">
                                        @error('titik_odp')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Paket Bandwidth & POP -->
                                    <div class="col-12 col-md-6">
                                        <label for="kode_bandwith" class="form-label-custom">
                                            <i class="bi bi-speedometer2 text-primary"></i> Paket Bandwidth & POP
                                        </label>
                                        <div class="input-group">
                                            <input type="text"
                                                   class="form-control form-control-custom @error('kode_bandwith') is-invalid @enderror"
                                                   id="kode_bandwith"
                                                   name="kode_bandwith"
                                                   value="{{ old('kode_bandwith') }}"
                                                   placeholder="Bandwidth (e.g. 50M)"
                                                   style="border-radius: 12px 0 0 12px;">
                                            <input type="text"
                                                   class="form-control form-control-custom @error('kode_pop') is-invalid @enderror"
                                                   id="kode_pop"
                                                   name="kode_pop"
                                                   value="{{ old('kode_pop') }}"
                                                   placeholder="POP (e.g. BDG-01)"
                                                   style="border-radius: 0 12px 12px 0;">
                                        </div>
                                    </div>

                                    <!-- SN ONT Modem & Koordinat GPS -->
                                    <div class="col-12 col-md-6">
                                        <label for="sn_ont" class="form-label-custom">
                                            <i class="bi bi-cpu text-primary"></i> SN ONT Modem & Koordinat GPS
                                        </label>
                                        <div class="input-group">
                                            <input type="text"
                                                   class="form-control form-control-custom @error('sn_ont') is-invalid @enderror"
                                                   id="sn_ont"
                                                   name="sn_ont"
                                                   value="{{ old('sn_ont') }}"
                                                   placeholder="SN ONT (ZTE/Huawei)"
                                                   style="border-radius: 12px 0 0 12px;">
                                            <input type="text"
                                                   class="form-control form-control-custom @error('lon_lat_pelanggan') is-invalid @enderror"
                                                   id="lon_lat_pelanggan"
                                                   name="lon_lat_pelanggan"
                                                   value="{{ old('lon_lat_pelanggan') }}"
                                                   placeholder="Lat, Long"
                                                   style="border-radius: 0 12px 12px 0;">
                                        </div>
                                    </div>

                                    <!-- Alamat Lengkap Pelanggan -->
                                    <div class="col-12">
                                        <label for="alamat_pelanggan" class="form-label-custom">
                                            <i class="bi bi-geo-alt-fill text-danger"></i> Alamat Pemasangan Pelanggan
                                        </label>
                                        <textarea class="form-control form-control-custom @error('alamat_pelanggan') is-invalid @enderror"
                                                  id="alamat_pelanggan"
                                                  name="alamat_pelanggan"
                                                  rows="2"
                                                  placeholder="Alamat jalan, nomor rumah, RT/RW, kelurahan...">{{ old('alamat_pelanggan') }}</textarea>
                                        @error('alamat_pelanggan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Jenis Kendala Broadband -->
                                    <div class="col-12 col-md-6">
                                        <label for="jenis_kendala_broadband" class="form-label-custom">
                                            <i class="bi bi-exclamation-triangle-fill text-warning"></i> Gejala / Jenis Kendala
                                        </label>
                                        <select class="form-select form-select-custom @error('jenis_kendala_broadband') is-invalid @enderror"
                                                id="jenis_kendala_broadband"
                                                name="jenis_kendala_broadband">
                                            <option value="" selected>-- Pilih Jenis Kendala --</option>
                                            <option value="LOS Merah (Kabel Putus / Tidak Ada Redaman)" {{ old('jenis_kendala_broadband') == 'LOS Merah (Kabel Putus / Tidak Ada Redaman)' ? 'selected' : '' }}>LOS Merah (Kabel Putus / Tidak Ada Redaman)</option>
                                            <option value="Redaman Tinggi (High Loss > -27 dBm)" {{ old('jenis_kendala_broadband') == 'Redaman Tinggi (High Loss > -27 dBm)' ? 'selected' : '' }}>Redaman Tinggi (High Loss > -27 dBm)</option>
                                            <option value="Internet Lambat / Intermittent" {{ old('jenis_kendala_broadband') == 'Internet Lambat / Intermittent' ? 'selected' : '' }}>Internet Lambat / Intermittent</option>
                                            <option value="Tidak Bisa Browsing (No Internet)" {{ old('jenis_kendala_broadband') == 'Tidak Bisa Browsing (No Internet)' ? 'selected' : '' }}>Tidak Bisa Browsing (No Internet)</option>
                                            <option value="Dropcore Putus Terkena Pohon / Kendaraan" {{ old('jenis_kendala_broadband') == 'Dropcore Putus Terkena Pohon / Kendaraan' ? 'selected' : '' }}>Dropcore Putus Terkena Pohon / Kendaraan</option>
                                            <option value="Modem ONT Mati / Rusak / Terbakar" {{ old('jenis_kendala_broadband') == 'Modem ONT Mati / Rusak / Terbakar' ? 'selected' : '' }}>Modem ONT Mati / Rusak / Terbakar</option>
                                            <option value="Lain-lain / Request Relokasi" {{ old('jenis_kendala_broadband') == 'Lain-lain / Request Relokasi' ? 'selected' : '' }}>Lain-lain / Request Relokasi</option>
                                        </select>
                                    </div>

                                    <!-- Redaman Sebelum (dBm) -->
                                    <div class="col-12 col-md-6">
                                        <label for="redaman_sebelum" class="form-label-custom">
                                            <i class="bi bi-activity text-danger"></i> Redaman Awal (dBm)
                                        </label>
                                        <input type="text"
                                               class="form-control form-control-custom @error('redaman_sebelum') is-invalid @enderror"
                                               id="redaman_sebelum"
                                               name="redaman_sebelum"
                                               value="{{ old('redaman_sebelum') }}"
                                               placeholder="Contoh: -31.5 dBm atau LOS">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ═══════════════════════════════════════════════════════════════ -->
                        <!-- ── SECTION B: KHUSUS TIKET BACKBONE TRANSMISSION ── -->
                        <!-- ═══════════════════════════════════════════════════════════════ -->
                        <div id="sectionBackbone" style="{{ old('kategori_tiket', 'BACKBONE') === 'BROADBAND' ? 'display: none;' : '' }}">
                            <!-- Status Link Impact -->
                            <div class="mb-3">
                                <label for="status_link_impact" class="form-label-custom">
                                    <i class="bi bi-hdd-network text-primary"></i> Status Link Impact
                                    <span class="badge bg-danger-subtle text-danger ms-1" style="font-size: 0.6rem; padding: 2px 5px;">Wajib</span>
                                </label>
                                <input type="text"
                                       class="form-control form-control-custom @error('status_link_impact') is-invalid @enderror"
                                       id="status_link_impact"
                                       name="status_link_impact"
                                       value="{{ old('status_link_impact') }}"
                                       placeholder="Contoh: Backbone : SW BBLU - SW Reog Down">
                                @error('status_link_impact')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="input-hint-box">
                                    <i class="bi bi-info-circle text-primary flex-shrink-0"></i>
                                    <span>Tuliskan nama link switch / perangkat dan status gangguan dengan jelas.</span>
                                </div>
                            </div>

                            <!-- Backbone Segment -->
                            <div class="mb-3">
                                <label for="backbone_segment" class="form-label-custom">
                                    <i class="bi bi-diagram-3 text-primary"></i> Segment / Backbone
                                    <span class="badge bg-danger-subtle text-danger ms-1" style="font-size: 0.6rem; padding: 2px 5px;">Wajib</span>
                                </label>
                                <select class="form-select form-select-custom @error('backbone_segment') is-invalid @enderror"
                                        id="backbone_segment"
                                        name="backbone_segment">
                                    <option value="" disabled {{ old('backbone_segment') ? '' : 'selected' }}>-- Pilih Segment Backbone --</option>
                                    @foreach($masterSegments as $segment)
                                        <option value="{{ $segment->backbone_segment }}"
                                                data-sla="{{ $segment->sla_target_minutes }}"
                                                {{ old('backbone_segment') === $segment->backbone_segment ? 'selected' : '' }}>
                                            {{ $segment->backbone_segment }} (Target: {{ round($segment->sla_target_minutes / 60, 1) }} Jam)
                                        </option>
                                    @endforeach
                                </select>
                                @error('backbone_segment')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="input-hint-box">
                                    <i class="bi bi-lightning-charge text-warning flex-shrink-0"></i>
                                    <span>Pilih jalur link untuk menghitung SLA otomatis.</span>
                                </div>
                            </div>
                        </div>

                        <!-- ═══════════════════════════════════════════════════════════════ -->
                        <!-- ── SECTION C: PARAMETER UMUM (SLA & WAKTU GANGGUAN) ── -->
                        <!-- ═══════════════════════════════════════════════════════════════ -->
                        <div class="row g-3">
                            <!-- Target SLA Minutes -->
                            <div class="col-12 col-md-6 mb-3">
                                <label for="sla_target_minutes" class="form-label-custom">
                                    <i class="bi bi-stopwatch text-primary"></i> Target SLA (Menit)
                                    <span class="badge bg-danger-subtle text-danger ms-1" style="font-size: 0.6rem; padding: 2px 5px;">Wajib</span>
                                </label>
                                <div class="input-group">
                                    <input type="number"
                                           class="form-control form-control-custom @error('sla_target_minutes') is-invalid @enderror"
                                           id="sla_target_minutes"
                                           name="sla_target_minutes"
                                           value="{{ old('sla_target_minutes', 360) }}"
                                           min="1"
                                           style="border-radius: 12px 0 0 12px;"
                                           required>
                                    <span class="sla-preview-addon" id="slaHoursPreview">6.0 Jam</span>
                                </div>
                                @error('sla_target_minutes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Tanggal & Jam Open Gangguan -->
                            <div class="col-12 col-md-6 mb-3">
                                <label for="tanggal_open" class="form-label-custom">
                                    <i class="bi bi-calendar2-event text-primary"></i> Tanggal & Jam Gangguan (WIB)
                                    <span class="badge bg-danger-subtle text-danger ms-1" style="font-size: 0.6rem; padding: 2px 5px;">Wajib</span>
                                </label>
                                <input type="datetime-local"
                                       class="form-control form-control-custom @error('tanggal_open') is-invalid @enderror"
                                       id="tanggal_open"
                                       name="tanggal_open"
                                       value="{{ old('tanggal_open', $currentDateTime) }}"
                                       required>
                                @error('tanggal_open')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Deskripsi Gangguan -->
                        <div class="mb-4">
                            <label for="deskripsi" class="form-label-custom">
                                <i class="bi bi-card-text text-primary"></i> Deskripsi Gangguan & Informasi Awal
                                <span class="badge bg-light text-muted ms-1 border" style="font-size: 0.6rem; padding: 2px 5px;">Opsional</span>
                            </label>
                            <textarea class="form-control form-control-custom @error('deskripsi') is-invalid @enderror"
                                      id="deskripsi"
                                      name="deskripsi"
                                      rows="3"
                                      placeholder="Catatan tambahan kondisi gangguan lapangan atau informasi awal...">{{ old('deskripsi') }}</textarea>
                            @error('deskripsi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit Action Buttons -->
                        <div class="form-actions-wrapper d-flex flex-column-reverse flex-sm-row justify-content-end gap-2 border-top pt-4">
                            <a href="{{ route('tiket.index') }}" class="btn btn-light border text-secondary px-4 py-2 rounded-pill fw-semibold d-inline-flex align-items-center justify-content-center gap-1">
                                <i class="bi bi-x-lg"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-submit-tiket px-4 py-2 rounded-pill fw-bold shadow-sm d-inline-flex align-items-center justify-content-center gap-2" id="btnSubmitTiket">
                                <i class="bi bi-send-check-fill"></i> Simpan & Open Tiket
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ── PREVIEW & INFO SECTION (RIGHT) ── -->
        <div class="col-12 col-lg-4">
            <!-- Digital Ticket Pass Card -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4" style="background: linear-gradient(145deg, #07152b 0%, #0c2147 60%, #102d66 100%); border: 1px solid rgba(255,255,255,0.15) !important;">
                <div class="card-body p-4 text-white position-relative">
                    <!-- Header of Ticket Pass -->
                    <div class="d-flex justify-content-between align-items-center border-bottom border-white border-opacity-10 pb-3 mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-ticket-perforated-fill text-primary fs-5"></i>
                            <span class="small fw-bold text-uppercase tracking-wider text-white-50" style="font-size: 0.72rem;">Preview Tiket</span>
                        </div>
                        <span class="badge bg-danger bg-opacity-90 text-white border border-danger-subtle px-2 py-1 small rounded-pill">
                            STATUS: OPEN
                        </span>
                    </div>

                    <!-- Category Badge in Pass -->
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-white-50 small">Kategori:</span>
                        <span class="badge bg-primary px-2 py-1 rounded-pill" id="previewCategoryBadge">BACKBONE</span>
                    </div>

                    <!-- Ticket Number Box -->
                    <div class="text-center py-3 px-2 rounded-3 mb-3" style="background: rgba(255,255,255,0.06); border: 1px dashed rgba(255,255,255,0.22);">
                        <div class="text-white-50 small mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">NOMOR TIKET OTOMATIS:</div>
                        <h4 class="fw-bold text-white font-monospace mb-0" id="previewNoTiketText" style="letter-spacing: 1px;">
                            {{ $previewNoTiket }}
                        </h4>
                    </div>

                    <!-- Metadata Rows -->
                    <div class="d-flex flex-column gap-2 text-white-50 small">
                        <div class="d-flex justify-content-between align-items-center py-1 border-bottom border-white border-opacity-5">
                            <span class="text-white-50">PIC Pembuat:</span>
                            <span class="fw-semibold text-white">{{ auth()->user()->name }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center py-1 border-bottom border-white border-opacity-5">
                            <span class="text-white-50">Segment / Target:</span>
                            <span class="fw-semibold text-white text-truncate" style="max-width: 170px;" id="previewSegment">-</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center py-1">
                            <span class="text-white-50">Target SLA:</span>
                            <span class="badge px-2 py-1 rounded-pill" style="background: rgba(44, 127, 255, 0.35); border: 1px solid rgba(147, 197, 253, 0.4); color: #93c5fd;" id="previewSla">6 Jam (360 menit)</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Guidelines Card -->
            <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
                <div class="card-header bg-white py-3 px-4 border-bottom">
                    <h6 class="fw-bold mb-0 text-navy d-flex align-items-center gap-2">
                        <i class="bi bi-shield-check text-success fs-5"></i> Panduan Open Tiket
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex align-items-start gap-3">
                            <div class="flex-shrink-0 rounded-circle bg-primary bg-opacity-10 text-primary fw-bold d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; font-size: 0.78rem;">1</div>
                            <div>
                                <div class="fw-semibold text-navy small mb-0">Pilihan Kategori Tiket</div>
                                <p class="text-muted mb-0" style="font-size: 0.76rem;">Gunakan <strong>Backbone</strong> untuk transmisi FO utama (<code>BDG-</code>) dan <strong>Broadband</strong> untuk pelanggan FTTH (<code>BRD-</code>).</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-start gap-3">
                            <div class="flex-shrink-0 rounded-circle bg-indigo bg-opacity-10 text-indigo fw-bold d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; font-size: 0.78rem; color: #4f46e5; background: rgba(79, 70, 229, 0.1);">2</div>
                            <div>
                                <div class="fw-semibold text-navy small mb-0">Integrasi IMS Customer</div>
                                <p class="text-muted mb-0" style="font-size: 0.76rem;">Ketik CID atau nama di kolom pencarian untuk mengisi otomatis data nomor HP, ODP, POP, dan GPS.</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-start gap-3">
                            <div class="flex-shrink-0 rounded-circle bg-primary bg-opacity-10 text-primary fw-bold d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; font-size: 0.78rem;">3</div>
                            <div>
                                <div class="fw-semibold text-navy small mb-0">Dispatch Manager Teknis</div>
                                <p class="text-muted mb-0" style="font-size: 0.76rem;">Setelah tiket dibuat, Manager Teknis akan menugaskan tim teknisi lapangan sebelum tiket ditangani.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
let currentCategory = "{{ old('kategori_tiket', 'BACKBONE') }}";

function selectCategory(cat) {
    currentCategory = cat;
    document.getElementById('kategori_tiket_input').value = cat;

    const optBackbone = document.getElementById('optBackbone');
    const optBroadband = document.getElementById('optBroadband');
    const secBackbone = document.getElementById('sectionBackbone');
    const secBroadband = document.getElementById('sectionBroadband');
    const previewCatBadge = document.getElementById('previewCategoryBadge');
    const slaInput = document.getElementById('sla_target_minutes');

    if (cat === 'BROADBAND') {
        optBackbone.classList.remove('active');
        optBroadband.classList.add('active');
        secBackbone.style.display = 'none';
        secBroadband.style.display = 'block';
        if (previewCatBadge) {
            previewCatBadge.textContent = 'BROADBAND';
            previewCatBadge.className = 'badge text-white px-2 py-1 rounded-pill';
            previewCatBadge.style.backgroundColor = '#4f46e5';
        }
        if (!slaInput.value || slaInput.value == 360) {
            slaInput.value = 240;
            updateSlaPreview(240);
        }
        updatePreviewSegment();
    } else {
        optBroadband.classList.remove('active');
        optBackbone.classList.add('active');
        secBroadband.style.display = 'none';
        secBackbone.style.display = 'block';
        if (previewCatBadge) {
            previewCatBadge.textContent = 'BACKBONE';
            previewCatBadge.className = 'badge bg-primary px-2 py-1 rounded-pill';
            previewCatBadge.style.backgroundColor = '';
        }
        updateSlaFromSegment();
    }

    refreshTicketNumber();
}

function updateSlaFromSegment() {
    const segmentSelect = document.getElementById('backbone_segment');
    const slaInput = document.getElementById('sla_target_minutes');
    const selectedOption = segmentSelect.options[segmentSelect.selectedIndex];

    if (selectedOption && selectedOption.dataset.sla) {
        const slaVal = parseInt(selectedOption.dataset.sla);
        slaInput.value = slaVal;
        updateSlaPreview(slaVal);
    }
    updatePreviewSegment();
}

function updatePreviewSegment() {
    const previewSegment = document.getElementById('previewSegment');
    if (!previewSegment) return;

    if (currentCategory === 'BROADBAND') {
        const custName = document.getElementById('nama_pelanggan').value.trim();
        const odp = document.getElementById('titik_odp').value.trim();
        if (custName) {
            previewSegment.textContent = custName + (odp ? ' (' + odp + ')' : '');
        } else {
            previewSegment.textContent = 'Pelanggan Broadband';
        }
    } else {
        const segmentSelect = document.getElementById('backbone_segment');
        const selectedOption = segmentSelect.options[segmentSelect.selectedIndex];
        previewSegment.textContent = (selectedOption && selectedOption.value) ? selectedOption.value : '-';
    }
}

function formatDurationJs(minutes) {
    const mins = parseInt(minutes) || 0;
    if (mins <= 0) return '0 menit';
    const h = Math.floor(mins / 60);
    const m = mins % 60;
    if (h > 0 && m > 0) return `${h} jam ${m} menit`;
    if (h > 0) return `${h} jam`;
    return `${m} menit`;
}

function updateSlaPreview(minutes) {
    const mins = parseInt(minutes) || 0;
    const formatted = formatDurationJs(mins);
    const slaHoursPreview = document.getElementById('slaHoursPreview');
    const previewSla = document.getElementById('previewSla');
    if (slaHoursPreview) slaHoursPreview.textContent = formatted;
    if (previewSla) previewSla.textContent = `${formatted} (${mins} menit)`;
}

function refreshTicketNumber() {
    const tanggalOpenInput = document.getElementById('tanggal_open');
    const dateVal = tanggalOpenInput.value;
    const previewNoTiketText = document.getElementById('previewNoTiketText');
    const previewNoTiketMobile = document.getElementById('previewNoTiketMobile');

    fetch(`{{ route('tiket.preview-number') }}?tanggal=${encodeURIComponent(dateVal)}&kategori=${encodeURIComponent(currentCategory)}`)
        .then(res => res.json())
        .then(data => {
            if (data.no_tiket) {
                if (previewNoTiketText) previewNoTiketText.textContent = data.no_tiket;
                if (previewNoTiketMobile) previewNoTiketMobile.textContent = data.no_tiket;
            }
        })
        .catch(err => console.error('Error fetching preview number:', err));
}

// ── IMS SEARCH AUTOCOMPLETE ──
let imsSearchTimeout = null;
const imsSearchInput = document.getElementById('imsSearchInput');
const imsResultsDropdown = document.getElementById('imsResultsDropdown');
const imsSearchIcon = document.getElementById('imsSearchIcon');
const imsSearchSpinner = document.getElementById('imsSearchSpinner');

if (imsSearchInput) {
    imsSearchInput.addEventListener('input', function() {
        const query = this.value.trim();
        clearTimeout(imsSearchTimeout);

        if (query.length < 2) {
            imsResultsDropdown.classList.add('d-none');
            imsResultsDropdown.innerHTML = '';
            return;
        }

        imsSearchIcon.classList.add('d-none');
        imsSearchSpinner.classList.remove('d-none');

        imsSearchTimeout = setTimeout(() => {
            fetch(`{{ route('api.ims.customers.search') }}?q=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(data => {
                    imsSearchIcon.classList.remove('d-none');
                    imsSearchSpinner.classList.add('d-none');

                    if (data.data && data.data.length > 0) {
                        let html = '';
                        data.data.forEach(item => {
                            const cid = item.id_pelanggan || item.nomor_internet || '';
                            const name = item.nama_pelanggan || '-';
                            const hp = item.no_kontak || item.nomor_hp || '-';
                            const odp = item.titik_odp || '-';
                            const addr = item.alamat || item.alamat_pelanggan || item.alamat_pasang || '-';

                            html += `
                                <div class="ims-result-item" onclick='populateImsCustomer(${JSON.stringify(item).replace(/'/g, "&#39;")})'>
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="fw-bold text-navy" style="font-size: 0.88rem;">${name}</span>
                                        <span class="badge bg-primary-subtle text-primary font-monospace" style="font-size: 0.72rem;">CID: ${cid}</span>
                                    </div>
                                    <div class="d-flex flex-wrap gap-2 text-muted" style="font-size: 0.75rem;">
                                        <span><i class="bi bi-telephone text-success"></i> ${hp}</span>
                                        <span><i class="bi bi-hdd-rack text-primary"></i> ${odp}</span>
                                    </div>
                                    <div class="text-secondary text-truncate mt-1" style="font-size: 0.73rem; max-width: 100%;">
                                        <i class="bi bi-geo-alt text-danger"></i> ${addr}
                                    </div>
                                </div>
                            `;
                        });
                        imsResultsDropdown.innerHTML = html;
                        imsResultsDropdown.classList.remove('d-none');
                    } else {
                        imsResultsDropdown.innerHTML = `
                            <div class="p-3 text-center text-muted" style="font-size: 0.82rem;">
                                <i class="bi bi-search me-1"></i> Data pelanggan tidak ditemukan untuk kata kunci "${query}".
                            </div>
                        `;
                        imsResultsDropdown.classList.remove('d-none');
                    }
                })
                .catch(err => {
                    imsSearchIcon.classList.remove('d-none');
                    imsSearchSpinner.classList.add('d-none');
                    console.error('IMS search error:', err);
                });
        }, 300);
    });

    // Close dropdown on click outside
    document.addEventListener('click', function(e) {
        if (!imsSearchInput.contains(e.target) && !imsResultsDropdown.contains(e.target)) {
            imsResultsDropdown.classList.add('d-none');
        }
    });
}

function populateImsCustomer(item) {
    if (!item) return;

    const cid = item.id_pelanggan || item.nomor_internet || '';
    const name = item.nama_pelanggan || '';
    const hp = item.no_kontak || item.nomor_hp || '';
    const addr = item.alamat || item.alamat_pelanggan || item.alamat_pasang || '';
    const odp = item.titik_odp || '';
    const bw = item.kode_bandwith || '';
    const pop = item.kode_pop || '';
    const sn = item.sn_ont || '';
    const gps = item.lon_lat || item.lon_lat_pelanggan || item.loc_maps || '';

    const elIdPelanggan = document.getElementById('id_pelanggan');
    const elNamaPelanggan = document.getElementById('nama_pelanggan');
    const elNoKontak = document.getElementById('no_kontak_pelanggan');
    const elAlamat = document.getElementById('alamat_pelanggan');
    const elTitikOdp = document.getElementById('titik_odp');
    const elKodeBw = document.getElementById('kode_bandwith');
    const elKodePop = document.getElementById('kode_pop');
    const elSnOnt = document.getElementById('sn_ont');
    const elLonLat = document.getElementById('lon_lat_pelanggan');

    if (elIdPelanggan) elIdPelanggan.value = cid;
    if (elNamaPelanggan) elNamaPelanggan.value = name;
    if (elNoKontak) elNoKontak.value = hp;
    if (elAlamat) elAlamat.value = addr;
    if (elTitikOdp) elTitikOdp.value = odp;
    if (elKodeBw) elKodeBw.value = bw;
    if (elKodePop) elKodePop.value = pop;
    if (elSnOnt) elSnOnt.value = sn;
    if (elLonLat) elLonLat.value = gps;

    imsResultsDropdown.classList.add('d-none');
    imsSearchInput.value = `${name} (${cid})`;

    updatePreviewSegment();
}

document.addEventListener('DOMContentLoaded', function() {
    const segmentSelect = document.getElementById('backbone_segment');
    const slaInput = document.getElementById('sla_target_minutes');
    const tanggalOpenInput = document.getElementById('tanggal_open');
    const namaPelangganInput = document.getElementById('nama_pelanggan');

    if (segmentSelect) {
        segmentSelect.addEventListener('change', updateSlaFromSegment);
    }
    if (slaInput) {
        slaInput.addEventListener('input', function() {
            updateSlaPreview(this.value);
        });
    }
    if (tanggalOpenInput) {
        tanggalOpenInput.addEventListener('change', refreshTicketNumber);
    }
    if (namaPelangganInput) {
        namaPelangganInput.addEventListener('input', updatePreviewSegment);
    }

    // Set initial state
    selectCategory(currentCategory);
});
</script>
@endpush
