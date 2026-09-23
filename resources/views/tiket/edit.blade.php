@extends('layouts.app')

@section('title', 'Edit Tiket ' . $tiket->no_tiket)

@section('content')
<div class="container-fluid px-0">

    <!-- ── BREADCRUMB ── -->
    <x-breadcrumb :items="[
        'Daftar Tiket Gangguan' => route('tiket.index'),
        $tiket->no_tiket => route('tiket.show', $tiket->id),
        'Edit Tiket' => null
    ]" />

    <!-- ── PAGE HEADER ── -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-navy">
                <i class="bi bi-pencil-square text-warning me-2"></i>Edit Tiket [{{ $tiket->no_tiket }}]
            </h4>
            <p class="text-muted small mb-0">Perbarui informasi gangguan backbone sebelum tiket ditutup</p>
        </div>
        <a href="{{ route('tiket.show', $tiket->id) }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Detail Tiket
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-9">
            <div class="card border-0 shadow-sm rounded-xl">
                <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-navy">
                        <i class="bi bi-sliders text-teal me-2"></i>Form Edit Data Gangguan
                    </h6>
                    <span class="badge bg-danger">STATUS: OPEN</span>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('tiket.update', $tiket->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- No Tiket Readonly -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-navy">Nomor Tiket</label>
                            <input type="text" class="form-control bg-light font-monospace" value="{{ $tiket->no_tiket }}" readonly>
                        </div>

                        <!-- Status Link Impact -->
                        <div class="mb-3">
                            <label for="status_link_impact" class="form-label fw-semibold text-navy">
                                Status Link Impact <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   class="form-control @error('status_link_impact') is-invalid @enderror"
                                   id="status_link_impact"
                                   name="status_link_impact"
                                   value="{{ old('status_link_impact', $tiket->status_link_impact) }}"
                                   required>
                            @error('status_link_impact')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-3">
                            <!-- Backbone Segment -->
                            <div class="col-12 col-md-6 mb-3">
                                <label for="backbone_segment" class="form-label fw-semibold text-navy">
                                    Segment / Backbone <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('backbone_segment') is-invalid @enderror"
                                        id="backbone_segment"
                                        name="backbone_segment"
                                        required>
                                    @foreach($masterSegments as $segment)
                                        <option value="{{ $segment->backbone_segment }}"
                                                data-sla="{{ $segment->sla_target_minutes }}"
                                                {{ old('backbone_segment', $tiket->backbone_segment) === $segment->backbone_segment ? 'selected' : '' }}>
                                            {{ $segment->backbone_segment }} (Target: {{ round($segment->sla_target_minutes / 60, 1) }} Jam)
                                        </option>
                                    @endforeach
                                </select>
                                @error('backbone_segment')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Target SLA Minutes -->
                            <div class="col-12 col-md-6 mb-3">
                                <label for="sla_target_minutes" class="form-label fw-semibold text-navy">
                                    Target SLA (Menit) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number"
                                           class="form-control @error('sla_target_minutes') is-invalid @enderror"
                                           id="sla_target_minutes"
                                           name="sla_target_minutes"
                                           value="{{ old('sla_target_minutes', $tiket->sla_target_minutes) }}"
                                           min="1"
                                           required>
                                    <span class="input-group-text bg-light text-muted" id="slaHoursPreview">
                                        {{ round($tiket->sla_target_minutes / 60, 1) }} Jam
                                    </span>
                                </div>
                                @error('sla_target_minutes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Tanggal & Jam Open Gangguan -->
                        <div class="mb-3">
                            <label for="tanggal_open" class="form-label fw-semibold text-navy">
                                Tanggal & Jam Awal Gangguan (WIB) <span class="text-danger">*</span>
                            </label>
                            <input type="datetime-local"
                                   class="form-control @error('tanggal_open') is-invalid @enderror"
                                   id="tanggal_open"
                                   name="tanggal_open"
                                   value="{{ old('tanggal_open', $tiket->tanggal_open->format('Y-m-d\TH:i')) }}"
                                   required>
                            @error('tanggal_open')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Deskripsi Gangguan -->
                        <div class="mb-4">
                            <label for="deskripsi" class="form-label fw-semibold text-navy">
                                Deskripsi Gangguan & Informasi Tambahan
                            </label>
                            <textarea class="form-control @error('deskripsi') is-invalid @enderror"
                                      id="deskripsi"
                                      name="deskripsi"
                                      rows="4">{{ old('deskripsi', $tiket->deskripsi) }}</textarea>
                            @error('deskripsi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-end gap-2 border-top pt-3">
                            <a href="{{ route('tiket.show', $tiket->id) }}" class="btn btn-light px-4">Batal</a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-check2 me-1"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
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

    function updateSlaPreview(minutes) {
        const mins = parseInt(minutes) || 0;
        const hours = (mins / 60).toFixed(1);
        slaHoursPreview.textContent = `${hours} Jam`;
    }

    segmentSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption && selectedOption.dataset.sla) {
            slaInput.value = selectedOption.dataset.sla;
            updateSlaPreview(selectedOption.dataset.sla);
        }
    });

    slaInput.addEventListener('input', function() {
        updateSlaPreview(this.value);
    });
});
</script>
@endpush
