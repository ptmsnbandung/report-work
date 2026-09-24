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

    /* Mobile clearance to avoid bottom nav bar overlap */
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
            <!-- Background Glow Watermark -->
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
                        <p class="text-white-50 small mb-0">Input laporan gangguan backbone baru untuk penanganan segera oleh Team Teknis</p>
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
                            <h6 class="fw-bold mb-0 text-navy">Formulir Gangguan Backbone</h6>
                            <small class="text-muted" style="font-size: 0.75rem;">Lengkapi parameter link & informasi awal gangguan</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <!-- Mobile Ticket Badge Indicator -->
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-1 font-monospace small" id="previewNoTiketMobile">
                            {{ $previewNoTiket }}
                        </span>
                    </div>
                </div>

                <div class="card-body p-3 p-md-4">
                    <form action="{{ route('tiket.store') }}" method="POST" id="formOpenTiket">
                        @csrf

                        <!-- 1. Status Link Impact -->
                        <div class="mb-3p5 mb-3">
                            <label for="status_link_impact" class="form-label-custom">
                                <i class="bi bi-hdd-network text-primary"></i> Status Link Impact
                                <span class="badge bg-danger-subtle text-danger ms-1" style="font-size: 0.6rem; padding: 2px 5px;">Wajib</span>
                            </label>
                            <input type="text"
                                   class="form-control form-control-custom @error('status_link_impact') is-invalid @enderror"
                                   id="status_link_impact"
                                   name="status_link_impact"
                                   value="{{ old('status_link_impact') }}"
                                   placeholder="Contoh: Backbone : SW BBLU - SW Reog Down"
                                   required>
                            @error('status_link_impact')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="input-hint-box">
                                <i class="bi bi-info-circle text-primary flex-shrink-0"></i>
                                <span>Tuliskan nama link switch / perangkat dan status gangguan dengan jelas.</span>
                            </div>
                        </div>

                        <!-- 2. Segment & Target SLA Row -->
                        <div class="row g-3">
                            <!-- Backbone Segment -->
                            <div class="col-12 col-md-6 mb-3">
                                <label for="backbone_segment" class="form-label-custom">
                                    <i class="bi bi-diagram-3 text-primary"></i> Segment / Backbone
                                    <span class="badge bg-danger-subtle text-danger ms-1" style="font-size: 0.6rem; padding: 2px 5px;">Wajib</span>
                                </label>
                                <select class="form-select form-select-custom @error('backbone_segment') is-invalid @enderror"
                                        id="backbone_segment"
                                        name="backbone_segment"
                                        required>
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
                                <div class="input-hint-box">
                                    <i class="bi bi-clock text-primary flex-shrink-0"></i>
                                    <span>Otomatis disinkronkan sesuai segment pilihan.</span>
                                </div>
                            </div>
                        </div>

                        <!-- 3. Tanggal & Jam Open Gangguan -->
                        <div class="mb-3">
                            <label for="tanggal_open" class="form-label-custom">
                                <i class="bi bi-calendar2-event text-primary"></i> Tanggal & Jam Awal Gangguan (WIB)
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
                            <div class="input-hint-box">
                                <i class="bi bi-bell text-danger flex-shrink-0"></i>
                                <span>Waktu deteksi awal alarm atau status link dinyatakan down oleh monitoring NOC.</span>
                            </div>
                        </div>

                        <!-- 4. Deskripsi Gangguan -->
                        <div class="mb-4">
                            <label for="deskripsi" class="form-label-custom">
                                <i class="bi bi-card-text text-primary"></i> Deskripsi Gangguan & Informasi Awal
                                <span class="badge bg-light text-muted ms-1 border" style="font-size: 0.6rem; padding: 2px 5px;">Opsional</span>
                            </label>
                            <textarea class="form-control form-control-custom @error('deskripsi') is-invalid @enderror"
                                      id="deskripsi"
                                      name="deskripsi"
                                      rows="4"
                                      placeholder="Contoh: Alarm LOS terdeteksi di SW BBLU port 1/1/2. Indikasi kabel fiber optik putus atau redaman tinggi...">{{ old('deskripsi') }}</textarea>
                            @error('deskripsi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="input-hint-box">
                                <i class="bi bi-pencil-square text-muted flex-shrink-0"></i>
                                <span>Tuliskan gejala alarm LOS, indikasi kabel putus, redaman tinggi, atau catatan awal.</span>
                            </div>
                        </div>

                        <!-- 5. Submit Action Buttons -->
                        <div class="form-actions-wrapper d-flex flex-column-reverse flex-sm-row justify-content-end gap-2 border-top pt-4">
                            <a href="{{ route('tiket.index') }}" class="btn btn-light border text-secondary px-4 py-2 rounded-pill fw-semibold d-inline-flex align-items-center justify-content-center gap-1 shadow-2xs">
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
                            <span class="text-white-50">Role PIC:</span>
                            <span class="badge bg-white bg-opacity-15 text-white border border-white border-opacity-10">{{ auth()->user()->role_label }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center py-1 border-bottom border-white border-opacity-5">
                            <span class="text-white-50">Segment:</span>
                            <span class="fw-semibold text-white text-truncate" style="max-width: 170px;" id="previewSegment">-</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center py-1">
                            <span class="text-white-50">Target SLA:</span>
                            <span class="badge px-2 py-1 rounded-pill" style="background: rgba(44, 127, 255, 0.35); border: 1px solid rgba(147, 197, 253, 0.4); color: #93c5fd;" id="previewSla">6 Jam (360 menit)</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Guidelines Card (Actionable & Clean) -->
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
                                <div class="fw-semibold text-navy small mb-0">Nomor Tiket Otomatis</div>
                                <p class="text-muted mb-0" style="font-size: 0.76rem;">Format <code>BDG-YYYYMMDD-XXX</code> terisi otomatis berdasarkan tanggal awal gangguan.</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-start gap-3">
                            <div class="flex-shrink-0 rounded-circle bg-primary bg-opacity-10 text-primary fw-bold d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; font-size: 0.78rem;">2</div>
                            <div>
                                <div class="fw-semibold text-navy small mb-0">Pilih Jalur Segment</div>
                                <p class="text-muted mb-0" style="font-size: 0.76rem;">Pilih jalur backbone yang tepat agar target SLA & status kepatuhan terhitung akurat.</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-start gap-3">
                            <div class="flex-shrink-0 rounded-circle bg-primary bg-opacity-10 text-primary fw-bold d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; font-size: 0.78rem;">3</div>
                            <div>
                                <div class="fw-semibold text-navy small mb-0">Notifikasi Tim Teknis</div>
                                <p class="text-muted mb-0" style="font-size: 0.76rem;">Tiket OPEN langsung muncul di menu "Tugas Saya" teknis lapangan secara real-time.</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-start gap-3">
                            <div class="flex-shrink-0 rounded-circle bg-primary bg-opacity-10 text-primary fw-bold d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; font-size: 0.78rem;">4</div>
                            <div>
                                <div class="fw-semibold text-navy small mb-0">Pemantauan Kronologis</div>
                                <p class="text-muted mb-0" style="font-size: 0.76rem;">Setiap progres investigasi kabel/perangkat dapat dipantau melalui log kronologis.</p>
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
document.addEventListener('DOMContentLoaded', function() {
    const segmentSelect = document.getElementById('backbone_segment');
    const slaInput = document.getElementById('sla_target_minutes');
    const slaHoursPreview = document.getElementById('slaHoursPreview');
    const previewSla = document.getElementById('previewSla');
    const previewSegment = document.getElementById('previewSegment');
    const tanggalOpenInput = document.getElementById('tanggal_open');
    const previewNoTiketText = document.getElementById('previewNoTiketText');
    const previewNoTiketMobile = document.getElementById('previewNoTiketMobile');

    // Auto update SLA when Segment changes
    function updateSlaFromSegment() {
        const selectedOption = segmentSelect.options[segmentSelect.selectedIndex];
        if (selectedOption && selectedOption.dataset.sla) {
            const slaVal = parseInt(selectedOption.dataset.sla);
            slaInput.value = slaVal;
            updateSlaPreview(slaVal);
            if (previewSegment) previewSegment.textContent = selectedOption.value;
        } else {
            if (previewSegment) previewSegment.textContent = '-';
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
        if (slaHoursPreview) slaHoursPreview.textContent = formatted;
        if (previewSla) previewSla.textContent = `${formatted} (${mins} menit)`;
    }

    segmentSelect.addEventListener('change', updateSlaFromSegment);

    slaInput.addEventListener('input', function() {
        updateSlaPreview(this.value);
    });

    // Update ticket number preview when date changes via AJAX
    tanggalOpenInput.addEventListener('change', function() {
        const dateVal = this.value;
        if (!dateVal) return;

        fetch(`{{ route('tiket.preview-number') }}?tanggal=${encodeURIComponent(dateVal)}`)
            .then(res => res.json())
            .then(data => {
                if (data.no_tiket) {
                    if (previewNoTiketText) previewNoTiketText.textContent = data.no_tiket;
                    if (previewNoTiketMobile) previewNoTiketMobile.textContent = data.no_tiket;
                }
            })
            .catch(err => console.error('Error fetching preview number:', err));
    });

    // Initialize initial state
    if (segmentSelect.value) {
        updateSlaFromSegment();
    } else {
        updateSlaPreview(slaInput.value);
    }
});
</script>
@endpush
