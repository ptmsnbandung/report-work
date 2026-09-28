<!-- ── MODAL PENUGASAN TEKNISI / DISPATCH (MANAGER TEKNIS & ADMIN) ── -->
@if(auth()->user()->hasRole(['admin', 'manager_teknisi']) && $tiket->status !== 'CLOSE')
<div class="modal fade" id="assignTeknisiModal" tabindex="-1" aria-labelledby="assignTeknisiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <form action="{{ route('tiket.assign-teknisi', $tiket->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-navy text-white" style="background: linear-gradient(135deg, #07152b 0%, #0c2147 50%, #102d66 100%) !important; border-bottom: 1px solid rgba(255,255,255,0.15) !important;">
                    <div class="d-flex align-items-center gap-2.5">
                        <div class="rounded-circle bg-info bg-opacity-25 p-2 text-info d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px;">
                            <i class="bi bi-person-gear fs-5 text-info"></i>
                        </div>
                        <div>
                            <h6 class="modal-title fw-bold text-white mb-0" id="assignTeknisiModalLabel" style="color: #ffffff !important;">
                                Penugasan Teknisi Lapangan (Dispatch)
                            </h6>
                            <div class="small text-white-50" style="font-size: 0.78rem;">Tiket #{{ $tiket->no_tiket }} &bull; {{ $tiket->status_link_impact }}</div>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4 bg-light">
                    <!-- Policy / Web Notification Info Banner -->
                    <div class="alert alert-info border-info border-opacity-25 rounded-xl p-3 mb-3 d-flex align-items-start gap-2.5" style="background-color: #f0f9ff;">
                        <i class="bi bi-info-circle-fill fs-5 text-info flex-shrink-0 mt-0.5"></i>
                        <div class="small text-dark">
                            <strong>Sistem Notifikasi Web & Hak Akses Koordinasi:</strong>
                            <ul class="mb-0 ps-3 mt-1 text-muted">
                                <li>Notifikasi Web (In-App & Push) akan otomatis dikirimkan ke <strong>seluruh teknisi</strong>.</li>
                                <li><strong>Teknisi yang ditunjuk</strong> (PIC Utama & Tim) mendapatkan prioritas tugas dan hak penuh untuk mengirim chat/kronologis & foto lapangan.</li>
                                <li>Teknisi lain yang tidak ditunjuk berada dalam <strong>Mode Pemantauan (Read-Only)</strong> untuk memonitor progres tanpa mengganggu alur koordinasi.</li>
                            </ul>
                        </div>
                    </div>

                    @if(isset($allTechnicians) && $allTechnicians->count() > 0)
                        <!-- 1. Leader / PIC Utama -->
                        <div class="card border-0 shadow-sm rounded-xl p-3 mb-3 bg-white">
                            <label class="form-label fw-bold text-dark mb-1">
                                <i class="bi bi-person-badge-fill text-primary me-1"></i> Pilih Leader / PIC Utama Teknisi <span class="text-danger">*</span>
                            </label>
                            <p class="text-muted small mb-2">Penanggung jawab utama pekerjaan dan pelaporan di lapangan.</p>
                            <select name="assigned_lead_id" id="assigned_lead_id" class="form-select select2" required style="border-radius: 10px;">
                                <option value="">-- Pilih PIC Utama --</option>
                                @foreach($allTechnicians as $tech)
                                    @php
                                        $workload = $technicianWorkloads[$tech->id] ?? 0;
                                        $isSelected = old('assigned_lead_id', $tiket->assigned_lead_id) == $tech->id;
                                    @endphp
                                    <option value="{{ $tech->id }}" {{ $isSelected ? 'selected' : '' }}>
                                        {{ $tech->name }} ({{ $tech->email ?? $tech->username }}) — Beban Aktif: {{ $workload }} Tiket
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- 2. Anggota Tim Tambahan -->
                        <div class="card border-0 shadow-sm rounded-xl p-3 mb-3 bg-white">
                            <label class="form-label fw-bold text-dark mb-1">
                                <i class="bi bi-people-fill text-teal me-1"></i> Anggota Tim Tambahan (Opsional)
                            </label>
                            <p class="text-muted small mb-2">Pilih teknisi tambahan yang ikut mendampingi dalam penanganan tiket ini.</p>
                            <div class="row g-2" style="max-height: 200px; overflow-y: auto; padding-right: 4px;">
                                @php
                                    $currentTeam = old('assigned_team', $tiket->assigned_team ?? []);
                                    if (!is_array($currentTeam)) $currentTeam = [];
                                @endphp
                                @foreach($allTechnicians as $tech)
                                    @php
                                        $workload = $technicianWorkloads[$tech->id] ?? 0;
                                        $isChecked = in_array($tech->id, $currentTeam);
                                    @endphp
                                    <div class="col-md-6">
                                        <div class="form-check p-2 rounded border bg-light d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center gap-2">
                                                <input class="form-check-input ms-0 me-2" type="checkbox" name="assigned_team[]" value="{{ $tech->id }}" id="team_tech_{{ $tech->id }}" {{ $isChecked ? 'checked' : '' }}>
                                                <label class="form-check-label small fw-semibold text-dark mb-0 cursor-pointer" for="team_tech_{{ $tech->id }}">
                                                    {{ $tech->name }}
                                                </label>
                                            </div>
                                            <span class="badge {{ $workload > 3 ? 'bg-danger' : ($workload > 0 ? 'bg-warning text-dark' : 'bg-success') }} rounded-pill" style="font-size: 0.68rem;">
                                                {{ $workload }} Aktif
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- 3. Catatan & Instruksi Khusus -->
                        <div class="card border-0 shadow-sm rounded-xl p-3 bg-white">
                            <label for="catatan_dispatch" class="form-label fw-bold text-dark mb-1">
                                <i class="bi bi-chat-left-text-fill text-warning me-1"></i> Instruksi / Catatan Khusus Manager Teknis (Opsional)
                            </label>
                            <textarea name="catatan_dispatch" id="catatan_dispatch" class="form-control" rows="3" placeholder="Contoh: Bawa kabel dropcore 100m, koordinasi dengan PIC gedung saat tiba di lokasi..." style="border-radius: 10px; font-size: 0.85rem;">{{ old('catatan_dispatch', $tiket->catatan_dispatch) }}</textarea>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-people text-muted" style="font-size: 2.5rem;"></i>
                            <p class="text-muted mt-2">Belum ada data user dengan role Teknisi di sistem.</p>
                        </div>
                    @endif
                </div>

                <div class="modal-footer bg-white border-top p-3 d-flex justify-content-between">
                    <button type="button" class="btn btn-light px-3 py-2 fw-semibold rounded-pill" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold rounded-pill shadow-sm d-inline-flex align-items-center gap-2">
                        <i class="bi bi-send-check-fill"></i>
                        <span>Tugaskan & Kirim Notifikasi Web</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- ── MODAL INPUT RESUME PEKERJAAN (FASE 4) ── -->
@if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'teknis', 'helpdesk']))
<div class="modal fade" id="editResumeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <form action="{{ route('tiket.resume.store', $tiket->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-navy text-white">
                    <h6 class="modal-title fw-bold text-white">
                        <i class="bi bi-file-earmark-text-fill text-teal me-2"></i>{{ $tiket->resume ? 'Edit' : 'Input' }} Resume Pekerjaan
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Team OM -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-navy">
                            Team OM / Daftar Nama Teknis Lapangan <span class="text-danger">*</span>
                        </label>
                        <div id="teamOmContainer">
                            @php
                                $teamOmList = $tiket->resume && is_array($tiket->resume->team_om) ? $tiket->resume->team_om : [auth()->user()->name];
                            @endphp
                            @foreach($teamOmList as $index => $nama)
                            <div class="input-group input-group-sm mb-2 team-om-row">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" name="team_om[]" class="form-control" value="{{ $nama }}" placeholder="Nama Teknisi" required>
                                <button type="button" class="btn btn-outline-danger btn-remove-team" {{ count($teamOmList) > 1 ? '' : 'disabled' }}>
                                    <i class="bi bi-dash"></i>
                                </button>
                            </div>
                            @endforeach
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm mt-1" id="btnAddTeamMember">
                            <i class="bi bi-plus-circle me-1"></i> Tambah Anggota Team
                        </button>
                    </div>

                    <!-- Problem / Temuan Masalah -->
                    <div class="mb-3">
                        <label for="problem_temuan" class="form-label small fw-bold text-navy">
                            Problem / Temuan Masalah di Lapangan <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control form-control-sm"
                                  id="problem_temuan"
                                  name="problem_temuan"
                                  rows="2"
                                  placeholder="Contoh: Kabel Tertarik Truk di Crossingan Jalan span KM 12+400"
                                  required>{{ old('problem_temuan', $tiket->resume?->problem_temuan) }}</textarea>
                    </div>

                    <!-- Action / Tindakan Perbaikan -->
                    <div class="mb-3">
                        <label for="action" class="form-label small fw-bold text-navy">
                            Action / Tindakan Perbaikan yang Dilakukan <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control form-control-sm"
                                  id="action"
                                  name="action"
                                  rows="3"
                                  placeholder="Contoh: Jumper Kabel 150 meter, Pemasangan New JC 2 Titik (JC1 dan JC2)..."
                                  required>{{ old('action', $tiket->resume?->action) }}</textarea>
                    </div>

                    @if($tiket->isBackbone())
                    <!-- Info Tipe Penanganan (Dikelola di tab Penanganan Core & JC) - BACKBONE ONLY -->
                    <div class="mb-3 p-3 bg-light rounded-3 border">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div>
                                <label class="form-label small fw-bold text-navy mb-0">
                                    <i class="bi bi-bezier2 text-primary me-1"></i> Tipe Penanganan Fisik / Core:
                                </label>
                                <span class="badge {{ ($tiket->tipe_penanganan ?? $tiket->resume?->tipe_penanganan) === 'MANUVER_CORE' ? 'bg-primary text-white' : 'bg-success text-white' }} ms-1 px-2.5 py-1 rounded-pill">
                                    {{ $tiket->tipe_penanganan_label }}
                                </span>
                            </div>
                            <small class="text-muted" style="font-size:0.72rem;">
                                Dikelola di menu <strong>Penanganan Core &amp; JC</strong>
                            </small>
                        </div>
                    </div>
                    @else
                    <!-- Input Redaman Optik - BROADBAND ONLY -->
                    <div class="mb-3 p-3 rounded-3 border" style="background:#f0f7ff; border-color:#0d6efd33 !important;">
                        <label class="form-label small fw-bold mb-2" style="color:#0d6efd;">
                            <i class="bi bi-activity me-1"></i> Hasil Pengukuran Redaman Optik
                        </label>
                        <div class="row g-3">
                            <div class="col-12 col-sm-6">
                                <label for="resume_redaman_sebelum" class="form-label small fw-semibold text-navy">
                                    Redaman Sebelum Perbaikan (dBm)
                                    <span class="text-muted fw-normal">(opsional)</span>
                                </label>
                                <div class="input-group input-group-sm">
                                    <input type="number"
                                           step="0.01" min="-60" max="0"
                                           class="form-control"
                                           id="resume_redaman_sebelum"
                                           name="redaman_sebelum"
                                           value="{{ old('redaman_sebelum', $tiket->redaman_sebelum ?? $tiket->resume?->redaman_sebelum) }}"
                                           placeholder="-30.00">
                                    <span class="input-group-text">dBm</span>
                                </div>
                                <div class="form-text" style="font-size:0.72rem;">Nilai negatif, contoh: -28.5</div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label for="resume_redaman_sesudah" class="form-label small fw-semibold text-navy">
                                    Redaman Sesudah Perbaikan (dBm)
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-sm">
                                    <input type="number"
                                           step="0.01" min="-60" max="0"
                                           class="form-control"
                                           id="resume_redaman_sesudah"
                                           name="redaman_sesudah"
                                           value="{{ old('redaman_sesudah', $tiket->redaman_sesudah ?? $tiket->resume?->redaman_sesudah) }}"
                                           placeholder="-18.50">
                                    <span class="input-group-text">dBm</span>
                                </div>
                                <div class="form-text" style="font-size:0.72rem;">Nilai negatif, wajib diisi untuk closing.</div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Catatan Tambahan -->
                    <div class="mb-0">
                        <label for="catatan_tambahan" class="form-label small fw-semibold text-navy">Catatan Tambahan (Opsional)</label>
                        <textarea class="form-control form-control-sm"
                                  id="catatan_tambahan"
                                  name="catatan_tambahan"
                                  rows="2"
                                  placeholder="Catatan tambahan hasil pengujian redaman / informasi khusus...">{{ old('catatan_tambahan', $tiket->resume?->catatan_tambahan) }}</textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-light btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4">
                        <i class="bi bi-save me-1"></i> Simpan Resume
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ── MODAL TAMBAH MATERIAL (FASE 4) ── -->
<div class="modal fade" id="addMaterialModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form action="{{ route('tiket.material.store', $tiket->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-navy text-white">
                    <h6 class="modal-title fw-bold text-white">
                        <i class="bi bi-box-seam-fill text-warning me-2"></i>Tambah Material Digunakan
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="nama_material" class="form-label small fw-bold text-navy">
                            Nama Material <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control form-control-sm mb-1" id="nama_material" name="nama_material" placeholder="Contoh: JC 24C / Kabel 24C" required list="presetMaterials">
                        <datalist id="presetMaterials">
                            <option value="JC 24C">
                            <option value="Kabel ADSS 24C">
                            <option value="Kabel Duct 24C">
                            <option value="Protection Sleeve 60mm">
                            <option value="Closure Dome 24C">
                            <option value="Pigtail SC-UPC">
                            <option value="Adapter SC-UPC">
                            <option value="Suspension Clamp">
                            <option value="Dead End Clamp">
                            <option value="Stainless Steel Band">
                        </datalist>
                        <div class="form-text small">Pilih dari rekomendasi atau ketik nama material baru.</div>
                    </div>

                    <div class="row g-2">
                        <div class="col-6 mb-3">
                            <label for="jumlah" class="form-label small fw-bold text-navy">
                                Jumlah <span class="text-danger">*</span>
                            </label>
                            <input type="number" class="form-control form-control-sm" id="jumlah" name="jumlah" min="1" value="1" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label for="satuan" class="form-label small fw-bold text-navy">
                                Satuan <span class="text-danger">*</span>
                            </label>
                            <select class="form-select form-select-sm" id="satuan" name="satuan" required>
                                <option value="pcs" selected>pcs</option>
                                <option value="meter">meter</option>
                                <option value="unit">unit</option>
                                <option value="set">set</option>
                                <option value="roll">roll</option>
                                <option value="tube">tube</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-light btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning btn-sm px-4">
                        <i class="bi bi-plus-circle me-1"></i> Tambahkan Material
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ── MODAL TAMBAH JOINT CLOSURE (JC) ── -->
<div class="modal fade" id="tambahJointClosureModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">
            <form action="{{ route('tiket.joint-closure.store', $tiket->id) }}" method="POST" id="formTambahJointClosure">
                @csrf
                <div class="modal-header bg-navy text-white px-3 py-2.5 border-bottom border-secondary border-opacity-25">
                    <div class="d-flex align-items-center gap-2 overflow-hidden pe-2">
                        <i class="bi bi-diagram-3-fill text-teal fs-6 flex-shrink-0"></i>
                        <h6 class="modal-title fw-bold text-white mb-0 text-truncate" style="font-size: 0.88rem;">
                            Tambah Data Joint Closure (JC) &amp; Sambungan Kabel
                        </h6>
                    </div>
                    <button type="button" class="btn-close btn-close-white flex-shrink-0" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-2 p-sm-3">
                    <!-- Section 1: Identitas & Lokasi Closure -->
                    <div class="section-card-jc p-2.5 p-sm-3 bg-light rounded-3 border mb-2.5">
                        <h6 class="section-title-jc fw-bold text-navy mb-2 small text-uppercase letter-spacing-1 d-flex align-items-center gap-1.5">
                            <i class="bi bi-geo-alt-fill text-danger"></i> 1. Identitas &amp; Lokasi Fisik Closure
                        </h6>
                        <div class="row g-2">
                            <div class="col-12 col-md-5">
                                <label for="jc_nama" class="form-label small fw-bold text-navy mb-1">
                                    Nama / Kode Closure <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control form-control-sm" id="jc_nama" name="nama_closure" placeholder="Contoh: JC-01, JC Tiang Span 14" required>
                            </div>

                            <div class="col-6 col-md-3">
                                <label for="jc_jenis" class="form-label small fw-bold text-navy mb-1">
                                    Jenis Closure <span class="text-danger">*</span>
                                </label>
                                <select class="form-select form-select-sm" id="jc_jenis" name="jenis_closure" required>
                                    <option value="DOME" selected>DOME (Kubah)</option>
                                    <option value="INLINE">INLINE (Lurus)</option>
                                    <option value="BOX_FAT">BOX FAT / FDT</option>
                                    <option value="OTB">OTB (Optical Box)</option>
                                </select>
                            </div>

                            <div class="col-6 col-md-4">
                                <label for="jc_lokasi" class="form-label small fw-bold text-navy mb-1">
                                    Penempatan Fisik <span class="text-danger">*</span>
                                </label>
                                <select class="form-select form-select-sm" id="jc_lokasi" name="lokasi_fisik" required>
                                    <option value="POLE" selected>POLE (Tiang Udara)</option>
                                    <option value="MANHOLE">MANHOLE (Bawah Tanah)</option>
                                    <option value="HANDHOLE">HANDHOLE</option>
                                    <option value="PEDESTAL">PEDESTAL</option>
                                    <option value="INDOOR_RACK">INDOOR (Rak OTB)</option>
                                </select>
                            </div>

                            <div class="col-12 col-md-7">
                                <div class="d-flex align-items-center justify-content-between mb-1 flex-wrap gap-1">
                                    <label class="form-label small fw-bold text-navy mb-0">
                                        <i class="bi bi-crosshair text-danger me-1"></i> Koordinat GPS Lokasi Closure
                                    </label>
                                    <button type="button" class="btn btn-link p-0 text-teal small text-decoration-none fw-semibold" id="btnGetLocationJc" style="font-size: 0.72rem;">
                                        <i class="bi bi-crosshair"></i> Ambil GPS Saya
                                    </button>
                                </div>
                                <div class="row g-1.5">
                                    <div class="col-6">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text px-2 bg-light text-muted small fw-semibold">Lat</span>
                                            <input type="number" step="any" class="form-control form-control-sm" id="latitude_jc" name="latitude" placeholder="-6.xxxxxx">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text px-2 bg-light text-muted small fw-semibold">Lng</span>
                                            <input type="number" step="any" class="form-control form-control-sm" id="longitude_jc" name="longitude" placeholder="107.xxxxxx">
                                            <button type="button" class="btn btn-outline-primary px-2" id="btnOpenMapPickerJc" title="Pilih di Peta">
                                                <i class="bi bi-map"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-5 d-flex align-items-stretch">
                                <div class="form-check form-switch p-2 bg-white rounded-3 border w-100 mb-0 d-flex align-items-start gap-2 shadow-xs">
                                    <input class="form-check-input ms-0 mt-0.5 flex-shrink-0" type="checkbox" role="switch" id="jc_is_aset_baru" name="is_aset_baru" value="1">
                                    <div class="flex-grow-1">
                                        <label class="form-check-label small fw-bold text-navy cursor-pointer d-block mb-0" for="jc_is_aset_baru" style="font-size: 0.75rem;">
                                            <i class="bi bi-stars text-warning me-1"></i> Tandai Penambahan Aset Baru (New Cut)
                                        </label>
                                        <div class="text-muted" style="font-size: 0.68rem; line-height: 1.25;">Centang jika ada pemasangan closure fisik baru.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Spesifikasi Kabel & Kapasitas Tube -->
                    <div class="section-card-jc p-2.5 p-sm-3 bg-light rounded-3 border mb-2.5">
                        <h6 class="section-title-jc fw-bold text-navy mb-2 small text-uppercase letter-spacing-1 d-flex align-items-center gap-1.5">
                            <i class="bi bi-bezier2 text-primary"></i> 2. Spesifikasi Kabel &amp; Kapasitas
                        </h6>
                        <div class="row g-2">
                            <!-- Kabel Asal -->
                            <div class="col-12 col-md-6">
                                <div class="p-2 p-sm-2.5 bg-white rounded-3 border h-100 shadow-xs">
                                    <div class="d-flex align-items-center gap-1 mb-1.5">
                                        <span class="badge bg-primary text-white rounded-pill px-2 py-0.5" style="font-size: 0.65rem; letter-spacing: 0.2px;">
                                            <i class="bi bi-box-arrow-in-right me-1"></i> KABEL ASAL / EKSISTING
                                        </span>
                                    </div>
                                    <div class="row g-1.5">
                                        <div class="col-7">
                                            <label for="jc_kapasitas_asal" class="form-label small fw-bold text-navy mb-1">
                                                Kapasitas Kabel <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-select form-select-sm" id="jc_kapasitas_asal" name="kapasitas_kabel_asal" required>
                                                <option value="2">2 Core</option>
                                                <option value="12">12 Core</option>
                                                <option value="24" selected>24 Core</option>
                                                <option value="48">48 Core</option>
                                                <option value="96">96 Core</option>
                                                <option value="144">144 Core</option>
                                                <option value="288">288 Core</option>
                                            </select>
                                        </div>
                                        <div class="col-5">
                                            <label for="jc_tube_asal" class="form-label small fw-bold text-navy mb-1">
                                                Jumlah Tube <span class="text-danger">*</span>
                                            </label>
                                            <input type="number" class="form-control form-control-sm" id="jc_tube_asal" name="jumlah_tube_asal" value="2" min="1" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Kabel Jumper -->
                            <div class="col-12 col-md-6">
                                <div class="p-2 p-sm-2.5 bg-white rounded-3 border h-100 shadow-xs">
                                    <div class="d-flex align-items-center gap-1 mb-1.5">
                                        <span class="badge bg-teal text-white rounded-pill px-2 py-0.5" style="font-size: 0.65rem; letter-spacing: 0.2px;">
                                            <i class="bi bi-box-arrow-right me-1"></i> KABEL JUMPER / DISTRIBUSI
                                        </span>
                                    </div>
                                    <div class="row g-1.5">
                                        <div class="col-7">
                                            <label for="jc_kapasitas_jumper" class="form-label small fw-bold text-navy mb-1">
                                                Kapasitas Kabel <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-select form-select-sm" id="jc_kapasitas_jumper" name="kapasitas_kabel_jumper" required>
                                                <option value="2">2 Core</option>
                                                <option value="12">12 Core</option>
                                                <option value="24" selected>24 Core</option>
                                                <option value="48">48 Core</option>
                                                <option value="96">96 Core</option>
                                                <option value="144">144 Core</option>
                                                <option value="288">288 Core</option>
                                            </select>
                                        </div>
                                        <div class="col-5">
                                            <label for="jc_tube_jumper" class="form-label small fw-bold text-navy mb-1">
                                                Jumlah Tube <span class="text-danger">*</span>
                                            </label>
                                            <input type="number" class="form-control form-control-sm" id="jc_tube_jumper" name="jumlah_tube_jumper" value="2" min="1" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Visual Interactive Splicing Tray & Dynamic Core Splicing Matrix -->
                    <div class="section-card-jc p-2.5 p-sm-3 bg-light rounded-3 border">
                        <!-- Visual Interactive Splicing Tray Component -->
                        <div class="fiber-patcher-box mb-2.5">
                            <div class="fiber-patcher-header">
                                <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                    <span class="badge bg-primary text-white px-2 py-0.5 rounded-pill" style="font-size: 0.68rem;">
                                        <i class="bi bi-bezier2 me-1"></i> Visual Fiber Patcher (Jointing Lurus)
                                    </span>
                                </div>
                                <div class="fiber-preset-actions-scroll">
                                    <button type="button" class="fiber-preset-btn" id="btnJcStraightPreset" title="Sambungkan 1:1 untuk tube yang sedang aktif">
                                        <i class="bi bi-arrows-expand me-1 text-info"></i> Sambung (1:1)
                                    </button>
                                    <button type="button" class="fiber-preset-btn fiber-preset-btn-warning" id="btnJcSwapPreset" title="Swap sambungan Tube 1 ke Tube 2">
                                        <i class="bi bi-shuffle me-1 text-warning"></i> Swap T1 &rarr; T2
                                    </button>
                                    <button type="button" class="fiber-preset-btn fiber-preset-btn-danger" id="btnJcClearPreset" title="Hapus semua sambungan kabel">
                                        <i class="bi bi-trash3 me-1 text-danger"></i> Reset
                                    </button>
                                </div>
                            </div>

                            <!-- Connection Real-time Indicator -->
                            <div class="text-center mb-2">
                                <div class="fiber-conn-status-badge" id="jcLiveWireBadge">
                                    <span class="fiber-pulse-laser text-info"><i class="bi bi-lightning-charge-fill"></i></span>
                                    <span id="jcLiveWireText">Klik port core (C1–C12) untuk langsung menyambung 1-ke-1 secara otomatis</span>
                                </div>
                            </div>

                            <div class="fiber-patcher-grid">
                                <!-- Left: Kabel Asal / Eksisting -->
                                <div class="fiber-panel-card">
                                    <div class="fiber-panel-title text-info">
                                        <span><i class="bi bi-arrow-right-circle me-1"></i> Kabel Asal (Input)</span>
                                        <div class="d-flex align-items-center gap-1">
                                            <select class="fiber-cap-select" id="jcAsalCapacity">
                                                <option value="2">2 Core</option>
                                                <option value="4">4 Core</option>
                                                <option value="6">6 Core</option>
                                                <option value="8">8 Core</option>
                                                <option value="12">12 Core</option>
                                                <option value="24" selected>24 Core (2T)</option>
                                                <option value="48">48 Core (4T)</option>
                                                <option value="96">96 Core (8T)</option>
                                                <option value="144">144 Core (12T)</option>
                                                <option value="288">288 Core (24T)</option>
                                            </select>
                                        </div>
                                    </div>
                                    <!-- Dynamic Tube Tabs -->
                                    <div class="fiber-tube-tabs" id="jcAsalTubeTabs"></div>
                                    <!-- Dynamic Fiber Core Ports List -->
                                    <div class="fiber-core-list" id="jcAsalCoreList"></div>
                                </div>

                                <!-- Center: Interactive Multi-Wire SVG Fiber Laser Canvas (Desktop) -->
                                <div class="fiber-canvas-center d-none d-lg-flex">
                                    <svg class="fiber-svg-wire" id="jcSvgCanvas" viewBox="0 0 120 260" preserveAspectRatio="none">
                                        <g id="jcSvgWiresGroup">
                                            <!-- Dynamic SVG Paths rendered via JavaScript -->
                                        </g>
                                    </svg>
                                    <div class="text-center mt-1">
                                        <div class="badge bg-dark bg-opacity-75 border border-secondary text-info font-monospace" style="font-size:0.68rem;" id="jcWireCountBadge">
                                            <i class="bi bi-arrow-left-right me-1"></i> 0 Sambungan
                                        </div>
                                    </div>
                                </div>

                                <!-- Right: Kabel Jumper / Distribusi -->
                                <div class="fiber-panel-card">
                                    <div class="fiber-panel-title text-teal">
                                        <span><i class="bi bi-arrow-left-circle me-1"></i> Kabel Jumper (Output)</span>
                                        <div class="d-flex align-items-center gap-1">
                                            <select class="fiber-cap-select" id="jcJumperCapacity">
                                                <option value="2">2 Core</option>
                                                <option value="4">4 Core</option>
                                                <option value="6">6 Core</option>
                                                <option value="8">8 Core</option>
                                                <option value="12">12 Core</option>
                                                <option value="24" selected>24 Core (2T)</option>
                                                <option value="48">48 Core (4T)</option>
                                                <option value="96">96 Core (8T)</option>
                                                <option value="144">144 Core (12T)</option>
                                                <option value="288">288 Core (24T)</option>
                                            </select>
                                        </div>
                                    </div>
                                    <!-- Dynamic Tube Tabs -->
                                    <div class="fiber-tube-tabs" id="jcJumperTubeTabs"></div>
                                    <!-- Dynamic Fiber Core Ports List -->
                                    <div class="fiber-core-list" id="jcJumperCoreList"></div>
                                </div>
                            </div>

                            <!-- Connected Chips Tray -->
                            <div class="fiber-chips-tray">
                                <div class="d-flex align-items-center justify-content-between mb-1.5">
                                    <span class="small fw-bold text-white" style="font-size:0.72rem;">
                                        <i class="bi bi-link-45deg me-1 text-info"></i> Sambungan Aktif:
                                    </span>
                                    <span class="small text-muted" id="jcTotalCoresText" style="font-size:0.68rem;">0 Core Terhubung</span>
                                </div>
                                <div class="fiber-connections-chips" id="jcConnectionsChips">
                                    <span class="text-muted small fst-italic py-1" style="font-size:0.7rem;">Belum ada core yang disambungkan. Tap port Asal lalu Jumper.</span>
                                </div>
                            </div>
                        </div>

                        <!-- Synchronized Splicing Table -->
                        <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-1.5">
                            <span class="small fw-bold text-navy" style="font-size: 0.78rem;"><i class="bi bi-table me-1"></i> Daftar Core yang Disambung / Matrix:</span>
                            <button type="button" class="btn btn-xs btn-outline-success rounded-pill px-2.5" id="btnAddCoreRow" style="font-size: 0.72rem;">
                                <i class="bi bi-plus-lg me-1"></i> Tambah Baris Manual
                            </button>
                        </div>

                        <div id="jcCoreRowsContainer" class="d-flex flex-column gap-1.5">
                            <!-- Template Row 1 (Default) -->
                            <div class="jc-core-input-row p-2 bg-white rounded-3 border shadow-xs">
                                <div class="row g-1.5 align-items-center">
                                    <div class="col-6 col-md-2">
                                        <label class="form-label small text-muted mb-0.5" style="font-size: 0.65rem;">Tube Asal</label>
                                        <input type="text" class="form-control form-control-sm font-monospace" name="tube_asal[]" placeholder="Tube 1" value="Tube 1">
                                    </div>
                                    <div class="col-6 col-md-2">
                                        <label class="form-label small text-muted mb-0.5" style="font-size: 0.65rem;">Core Asal</label>
                                        <input type="text" class="form-control form-control-sm font-monospace" name="core_asal[]" placeholder="Core 1" value="Core 1">
                                    </div>
                                    <div class="col-6 col-md-2">
                                        <label class="form-label small text-muted mb-0.5" style="font-size: 0.65rem;">Tube Jumper</label>
                                        <input type="text" class="form-control form-control-sm font-monospace" name="tube_jumper[]" placeholder="Tube 1" value="Tube 1">
                                    </div>
                                    <div class="col-6 col-md-2">
                                        <label class="form-label small text-muted mb-0.5" style="font-size: 0.65rem;">Core Jumper</label>
                                        <input type="text" class="form-control form-control-sm font-monospace" name="core_jumper[]" placeholder="Core 1" value="Core 1">
                                    </div>
                                    <div class="col-6 col-md-2">
                                        <label class="form-label small text-muted mb-0.5" style="font-size: 0.65rem;">Status</label>
                                        <select class="form-select form-select-sm" name="core_status[]">
                                            <option value="TERHUBUNG" selected>TERHUBUNG</option>
                                            <option value="SPARE">SPARE (Sisa)</option>
                                            <option value="LOSS_PUTUS">LOSS / PUTUS</option>
                                            <option value="MANUVER">MANUVER</option>
                                        </select>
                                    </div>
                                    <div class="col-4 col-md-1">
                                        <label class="form-label small text-muted mb-0.5" style="font-size: 0.65rem;">Loss (dB)</label>
                                        <input type="number" step="0.01" class="form-control form-control-sm font-monospace" name="loss_db[]" placeholder="0.02" value="0.02">
                                    </div>
                                    <div class="col-2 col-md-1 text-end pt-md-3">
                                        <button type="button" class="btn btn-sm btn-outline-danger w-100 p-1 btn-remove-core-row" title="Hapus baris ini">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light px-3 py-2 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <button type="button" class="btn btn-light border btn-sm px-3 flex-fill flex-sm-grow-0" data-bs-dismiss="modal">
                        <i class="bi bi-x me-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-indigo-pro btn-sm px-4 fw-semibold shadow-xs flex-fill flex-sm-grow-0">
                        <i class="bi bi-check2-circle me-1"></i> Simpan Data Joint Closure
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ── MODAL TAMBAH SINGLE CORE JOINT CLOSURE ── -->
<div class="modal fade" id="addJointClosureCoreModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <form action="" method="POST" id="formAddSingleCore">
                @csrf
                <div class="modal-header bg-navy text-white">
                    <h6 class="modal-title fw-bold text-white">
                        <i class="bi bi-diagram-2-fill text-teal me-2"></i>Tambah Sambungan Core - <span id="modalCoreJcTitle">JC</span>
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Visual Port Selector for Single Core -->
                    <div class="fiber-patcher-box mb-3">
                        <div class="fiber-patcher-header">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-teal text-white px-2 py-0.5 rounded-pill font-monospace" style="font-size:0.75rem;">
                                    <i class="bi bi-bezier2 me-1"></i> Single Core Splicer (Jointing Lurus)
                                </span>
                                <span class="small text-light" style="font-size:0.75rem;">Klik nomor port core (C1–C12) untuk memilih titik sambungan</span>
                            </div>
                        </div>
                        <div class="fiber-patcher-grid">
                            <!-- Left: Asal -->
                            <div class="fiber-panel-card">
                                <div class="fiber-panel-title text-info">
                                    <span><i class="bi bi-arrow-right-circle me-1"></i> Kabel Asal (Input)</span>
                                    <div class="d-flex align-items-center gap-1">
                                        <select class="fiber-cap-select" id="singleAsalCapacity">
                                            <option value="2">2 Core</option>
                                            <option value="4">4 Core</option>
                                            <option value="6">6 Core</option>
                                            <option value="8">8 Core</option>
                                            <option value="12">12 Core</option>
                                            <option value="24" selected>24 Core (2T)</option>
                                            <option value="48">48 Core (4T)</option>
                                            <option value="96">96 Core (8T)</option>
                                            <option value="144">144 Core (12T)</option>
                                            <option value="288">288 Core (24T)</option>
                                        </select>
                                        <span class="badge bg-info bg-opacity-25 text-info font-monospace" id="singleAsalActiveBadge">T1 C1 (Biru)</span>
                                    </div>
                                </div>
                                <div class="fiber-tube-tabs" id="singleAsalTubeTabs"></div>
                                <div class="fiber-core-list" id="singleAsalCoreList"></div>
                            </div>
                            <!-- Center: Laser Wire -->
                            <div class="fiber-canvas-center">
                                <svg class="fiber-svg-wire" id="singleSvgCanvas" viewBox="0 0 130 220" preserveAspectRatio="none">
                                    <path class="fiber-wire-path" id="singleSvgWirePath" d="M 0 20 C 65 20, 65 20, 130 20" stroke="#2563eb" stroke-width="3" fill="none" />
                                </svg>
                                <div class="badge bg-dark bg-opacity-75 border border-secondary text-info font-monospace mt-1" style="font-size:0.68rem;" id="singleWireCountBadge">
                                    1:1 Straight
                                </div>
                            </div>
                            <!-- Right: Jumper -->
                            <div class="fiber-panel-card">
                                <div class="fiber-panel-title text-teal">
                                    <span><i class="bi bi-arrow-left-circle me-1"></i> Kabel Jumper (Output)</span>
                                    <div class="d-flex align-items-center gap-1">
                                        <select class="fiber-cap-select" id="singleJumperCapacity">
                                            <option value="2">2 Core</option>
                                            <option value="4">4 Core</option>
                                            <option value="6">6 Core</option>
                                            <option value="8">8 Core</option>
                                            <option value="12">12 Core</option>
                                            <option value="24" selected>24 Core (2T)</option>
                                            <option value="48">48 Core (4T)</option>
                                            <option value="96">96 Core (8T)</option>
                                            <option value="144">144 Core (12T)</option>
                                            <option value="288">288 Core (24T)</option>
                                        </select>
                                        <span class="badge bg-teal bg-opacity-25 text-teal font-monospace" id="singleJumperActiveBadge">T1 C1 (Biru)</span>
                                    </div>
                                </div>
                                <div class="fiber-tube-tabs" id="singleJumperTubeTabs"></div>
                                <div class="fiber-core-list" id="singleJumperCoreList"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-navy">Tube Asal <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm font-monospace" id="single_tube_asal" name="tube_asal" placeholder="Contoh: Tube 1" value="Tube 1" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-navy">Core Asal <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm font-monospace" id="single_core_asal" name="core_asal" placeholder="Contoh: Core 1" value="Core 1" required>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-navy">Tube Jumper</label>
                            <input type="text" class="form-control form-control-sm font-monospace" id="single_tube_jumper" name="tube_jumper" placeholder="Contoh: Tube 1 (opsional jika spare)" value="Tube 1">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-navy">Core Jumper</label>
                            <input type="text" class="form-control form-control-sm font-monospace" id="single_core_jumper" name="core_jumper" placeholder="Contoh: Core 1 (opsional jika spare)" value="Core 1">
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-7">
                            <label class="form-label small fw-bold text-navy">Status Sambungan <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm" name="status" required>
                                <option value="TERHUBUNG" selected>TERHUBUNG (Spliced)</option>
                                <option value="SPARE">SPARE (Core Sisa / Tidak disambung)</option>
                                <option value="LOSS_PUTUS">LOSS / PUTUS</option>
                                <option value="MANUVER">MANUVER</option>
                            </select>
                        </div>
                        <div class="col-5">
                            <label class="form-label small fw-semibold text-navy">Loss Sambungan (dB)</label>
                            <input type="number" step="0.01" class="form-control form-control-sm font-monospace" name="loss_db" placeholder="0.02" value="0.02">
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label small fw-semibold text-navy">Keterangan Tambahan (Opsional)</label>
                        <input type="text" class="form-control form-control-sm" name="keterangan" placeholder="Contoh: Sambung ke arah Node C">
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-light btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4">
                        <i class="bi bi-plus-circle me-1"></i> Simpan Sambungan Core
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ── MODAL MAP PICKER FOR JOINT CLOSURE ── -->
<div class="modal fade" id="mapPickerJcModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-navy text-white py-2">
                <h6 class="modal-title fw-bold text-white"><i class="bi bi-pin-map-fill text-danger me-2"></i>Pilih Titik Lokasi Joint Closure di Peta</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3">
                <div id="mapPickerJc" style="height: 350px; width: 100%; border-radius: 8px;"></div>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <span class="small font-monospace" id="pickedCoordsJcText">Koordinat: -6.917464, 107.619123</span>
                    <button type="button" class="btn btn-primary btn-sm px-3" id="btnApplyPickedCoordsJc">
                        <i class="bi bi-check2 me-1"></i> Terapkan Koordinat Ini
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ── MODAL TAMBAH TITIK PERBAIKAN (FASE 4) ── -->
<div class="modal fade" id="addTitikModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form action="{{ route('tiket.titik-perbaikan.store', $tiket->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-navy text-white">
                    <h6 class="modal-title fw-bold text-white">
                        <i class="bi bi-geo-alt-fill text-danger me-2"></i>Tambah Titik Tagging Perbaikan
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="nama_titik" class="form-label small fw-bold text-navy">
                            Nama Titik Tagging <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control form-control-sm" id="nama_titik" name="nama_titik" placeholder="Contoh: JC1 / JC2 / Tiang Span 14" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-navy d-flex justify-content-between">
                            <span>Koordinat GPS <span class="text-danger">*</span></span>
                            <button type="button" class="btn btn-link p-0 text-teal small text-decoration-none" id="btnGetLocationTitik">
                                <i class="bi bi-crosshair"></i> GPS Saya
                            </button>
                        </label>
                        <div class="input-group input-group-sm mb-1">
                            <input type="number" step="any" class="form-control" id="latitude_titik" name="latitude" placeholder="Latitude (-6.xxx)" required>
                            <input type="number" step="any" class="form-control" id="longitude_titik" name="longitude" placeholder="Longitude (106.xxx)" required>
                            <button type="button" class="btn btn-outline-secondary" id="btnOpenMapPickerTitik" title="Pilih di Peta">
                                <i class="bi bi-map"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label for="keterangan" class="form-label small fw-semibold text-navy">Keterangan Titik (Opsional)</label>
                        <input type="text" class="form-control form-control-sm" id="keterangan" name="keterangan" placeholder="Contoh: Closure di tiang pinggir jalan raya...">
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-light btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger btn-sm px-4">
                        <i class="bi bi-pin-map me-1"></i> Simpan Titik
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ── MODAL MAP PICKER FOR TITIK PERBAIKAN ── -->
<div class="modal fade" id="mapPickerTitikModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-navy text-white py-2">
                <h6 class="modal-title fw-bold text-white"><i class="bi bi-pin-map-fill text-danger me-2"></i>Pilih Titik Perbaikan di Peta</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3">
                <div id="mapPickerTitik"></div>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <span class="small font-monospace" id="pickedCoordsTitikText">Koordinat: -6.917464, 107.619123</span>
                    <button type="button" class="btn btn-primary btn-sm px-3" id="btnApplyPickedCoordsTitik">
                        <i class="bi bi-check2 me-1"></i> Terapkan Koordinat Ini
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ── MODAL UPLOAD DOKUMENTASI FOTO (FASE 5) ── -->
<div class="modal fade" id="uploadDokumentasiModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <form action="{{ route('tiket.dokumentasi.store', $tiket->id) }}" method="POST" enctype="multipart/form-data" id="formUploadDokumentasi">
                @csrf
                <input type="hidden" name="source_photo_url" id="docSourcePhotoUrl" value="">
                <div class="modal-header bg-navy text-white">
                    <h6 class="modal-title fw-bold text-white" id="uploadDokModalTitle">
                        <i class="bi bi-camera-fill text-info me-2"></i>Upload Foto Dokumentasi Lapangan
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <!-- Forwarded Photo Preview Box (Shows when saving from chat) -->
                        <div id="docSourcePhotoPreviewBox" class="col-12 d-none">
                            <div class="card border-primary border-opacity-50 bg-primary-subtle bg-opacity-10 p-3 rounded-3 shadow-xs">
                                <div class="d-flex align-items-center gap-3">
                                    <img id="docSourcePhotoImg" src="#" alt="Foto Chat" class="rounded-3 border shadow-xs" style="width: 72px; height: 72px; object-fit: cover;">
                                    <div class="flex-grow-1">
                                        <div class="badge bg-primary text-white mb-1"><i class="bi bi-chat-left-text me-1"></i> Foto dari Chat Lapangan</div>
                                        <div class="small fw-bold text-navy">Foto ini akan disimpan ke Dokumentasi Wajib Pekerjaan</div>
                                        <div class="text-muted" style="font-size: 0.75rem;">Silakan pilih kategori dokumentasi dan verifikasi koordinat/catatan di bawah ini.</div>
                                    </div>
                                    <button type="button" class="btn-close" style="font-size: 0.65rem;" id="btnCancelForwardDoc" title="Batal simpan dari chat"></button>
                                </div>
                            </div>
                        </div>

                        <!-- Kategori Dokumentasi -->
                        <div class="col-12 col-md-6">
                            <label for="doc_kategori" class="form-label small fw-bold text-navy">
                                Kategori Dokumentasi <span class="text-danger">*</span>
                            </label>
                            <select class="form-select form-select-sm" id="doc_kategori" name="kategori" required>
                                <option value="" disabled selected>-- Pilih Kategori --</option>
                                <option value="Hasil Jointing">🔧 Hasil Jointing / Splicing</option>
                                <option value="Closure Terpasang">📦 Closure Terpasang</option>
                                <option value="Kondisi Lokasi">📍 Kondisi Lokasi & Kerusakan</option>
                                <option value="Hasil OTDR">📊 Hasil Pengukuran OTDR</option>
                                <option value="Dokumentasi Lapangan">📸 Dokumentasi Lapangan Umum</option>
                                <option value="Lain-lain">💬 Lain-lain</option>
                            </select>
                            <!-- Quick Category Selector Chips -->
                            <div class="d-flex flex-wrap gap-1 mt-2">
                                <span class="badge bg-light text-navy border cursor-pointer doc-cat-preset" data-cat="Hasil Jointing">Hasil Jointing</span>
                                <span class="badge bg-light text-navy border cursor-pointer doc-cat-preset" data-cat="Closure Terpasang">Closure Terpasang</span>
                                <span class="badge bg-light text-navy border cursor-pointer doc-cat-preset" data-cat="Kondisi Lokasi">Kondisi Lokasi</span>
                                <span class="badge bg-light text-navy border cursor-pointer doc-cat-preset" data-cat="Hasil OTDR">Hasil OTDR</span>
                            </div>
                        </div>

                        <!-- Waktu Dokumentasi -->
                        <div class="col-12 col-md-6">
                            <label for="doc_timestamp" class="form-label small fw-bold text-navy">
                                Waktu Foto / Dokumentasi (WIB) <span class="text-danger">*</span>
                            </label>
                            <input type="datetime-local"
                                   class="form-control form-control-sm"
                                   id="doc_timestamp"
                                   name="timestamp"
                                   value="{{ now()->format('Y-m-d\TH:i') }}"
                                   required>
                        </div>

                        <!-- Multi-File Image Upload Zone (Hidden if saving from chat) -->
                        <div class="col-12" id="docMultiUploadWrapper">
                            <label class="form-label small fw-bold text-navy">
                                Pilih File Foto (Bisa Multiple) <span class="text-danger">*</span>
                            </label>
                            <div class="border border-2 border-dashed rounded-3 p-3 text-center bg-light position-relative" id="dropzoneDoc">
                                <i class="bi bi-cloud-arrow-up display-5 text-teal opacity-75 d-block mb-1"></i>
                                <span class="small fw-semibold text-navy d-block mb-1">Pilih satu atau beberapa file foto</span>
                                <span class="text-muted" style="font-size: 0.75rem;">Maksimal 5 MB per foto (Format: JPG, PNG, WEBP).</span>
                                <input type="file"
                                       class="form-control form-control-sm mt-2"
                                       id="photosInput"
                                       name="photos[]"
                                       accept="image/jpeg,image/png,image/webp"
                                       multiple
                                       required>
                            </div>
                            <!-- Instant Thumbnails Preview Container -->
                            <div id="docThumbnailsContainer" class="d-flex flex-wrap gap-2 mt-3 d-none"></div>
                        </div>

                        <!-- Koordinat GPS Opsional -->
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-navy d-flex justify-content-between align-items-center mb-1">
                                <span><i class="bi bi-geo-alt-fill text-danger me-1"></i> Koordinat GPS Lokasi (Otomatis / EXIF)</span>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="gps-lock-badge d-none" id="docGpsStatusBadge">
                                        <span class="gps-pulse-dot"></span> <span id="docGpsStatusText">GPS Terkunci</span>
                                    </span>
                                    <button type="button" class="btn btn-link p-0 text-teal small text-decoration-none" id="btnGetLocationDoc">
                                        <i class="bi bi-crosshair"></i> Ambil GPS
                                    </button>
                                </div>
                            </label>
                            <div class="input-group input-group-sm">
                                <input type="number" step="any" class="form-control" id="latitude_doc" name="latitude" placeholder="Latitude (-6.xxx)">
                                <input type="number" step="any" class="form-control" id="longitude_doc" name="longitude" placeholder="Longitude (106.xxx)">
                                <button type="button" class="btn btn-outline-secondary" id="btnOpenMapPickerDoc" title="Pilih di Peta">
                                    <i class="bi bi-map"></i>
                                </button>
                            </div>
                            <div class="form-text small">Koordinat otomatis diambil dari GPS perangkat / metadata EXIF foto saat diupload.</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-semibold shadow-xs" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); border: none; color: #ffffff !important;">
                        <i class="bi bi-cloud-arrow-up-fill me-1 text-white"></i> Simpan Dokumentasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ════ WHATSAPP-STYLE MESSAGE INFO MODAL (READ BY / SEEN BY) ════ -->
<div class="modal fade" id="msgInfoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-navy text-white py-2.5 px-3.5">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:34px; height:34px; background:rgba(56,189,248,0.2); color:#38bdf8;">
                        <i class="bi bi-info-circle-fill fs-6"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-white mb-0" style="font-size:0.95rem;">Info Pesan</h6>
                        <span class="text-white-50" style="font-size:0.72rem;">Detail pembacaan & pengiriman pesan</span>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3.5 bg-light">
                <!-- Message Preview Snippet Card -->
                <div class="card border-0 shadow-xs rounded-3 mb-3 bg-white p-3">
                    <div class="d-flex align-items-center justify-content-between mb-1.5">
                        <span class="fw-bold small text-navy" id="msgInfoSender">Pengirim</span>
                        <span class="text-muted small" style="font-size:0.74rem;" id="msgInfoTime">Waktu</span>
                    </div>
                    <div class="small text-secondary text-break p-2 rounded bg-light border" id="msgInfoContent" style="max-height:110px; overflow-y:auto; font-size:0.82rem;">
                        Isi pesan...
                    </div>
                    <div id="msgInfoPhotoContainer" class="d-none mt-2 text-center">
                        <img id="msgInfoPhotoThumb" src="#" alt="Foto Lampiran" class="rounded border shadow-xs" style="max-height: 100px; max-width: 100%; object-fit: contain;">
                    </div>
                </div>

                <!-- Read Status Section (Dibaca Oleh) -->
                <div class="card border-0 shadow-xs rounded-3 bg-white p-3 mb-2.5">
                    <div class="d-flex align-items-center gap-1.5 mb-2 pb-1.5 border-bottom">
                        <i class="bi bi-check2-all text-info fw-bold fs-5"></i>
                        <h6 class="fw-bold mb-0 text-dark" style="font-size:0.86rem;">Dibaca Oleh</h6>
                        <span class="badge bg-info-subtle text-info ms-auto" id="msgInfoReadCountBadge">0 Anggota</span>
                    </div>
                    <div id="msgInfoReadList" class="d-flex flex-column gap-2" style="max-height: 190px; overflow-y: auto;">
                        <!-- Rendered via JS -->
                    </div>
                </div>

                <!-- Delivered Status Section (Terkirim) -->
                <div class="card border-0 shadow-xs rounded-3 bg-white p-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-check2 text-secondary fw-bold fs-5"></i>
                        <div class="flex-grow-1">
                            <div class="fw-bold small text-dark" style="font-size:0.84rem;">Terkirim ke Server</div>
                            <div class="text-muted small" style="font-size:0.72rem;" id="msgInfoDeliveredTime">Terkirim</div>
                        </div>
                        <span class="badge bg-secondary-subtle text-secondary" style="font-size:0.7rem;">Tersimpan</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light py-2 px-3">
                <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3.5" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- ── MODAL MAP PICKER FOR DOKUMENTASI ── -->
<div class="modal fade" id="mapPickerDocModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-navy text-white py-2">
                <h6 class="modal-title fw-bold text-white"><i class="bi bi-pin-map-fill text-danger me-2"></i>Pilih Lokasi Dokumentasi di Peta</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3">
                <div id="mapPickerDoc"></div>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <span class="small font-monospace" id="pickedCoordsDocText">Koordinat: -6.917464, 107.619123</span>
                    <button type="button" class="btn btn-primary btn-sm px-3" id="btnApplyPickedCoordsDoc">
                        <i class="bi bi-check2 me-1"></i> Terapkan Koordinat Ini
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ── MODAL TAMBAH MANUVER CORE (FASE 5) ── -->
<div class="modal fade" id="addManuverModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
            <form action="{{ route('tiket.manuver-core.store', $tiket->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-navy text-white px-3 py-2 border-bottom border-secondary border-opacity-25">
                    <div class="d-flex align-items-center gap-2 overflow-hidden pe-2">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-3 bg-teal bg-opacity-25 p-1 text-teal flex-shrink-0" style="width: 28px; height: 28px;">
                            <i class="bi bi-shuffle text-info" style="font-size: 0.85rem;"></i>
                        </div>
                        <div class="overflow-hidden">
                            <h6 class="modal-title fw-bold text-white mb-0 text-truncate" style="font-size: 0.88rem;">
                                Tambah Record Manuver Core
                            </h6>
                            <div class="text-white-50 text-truncate" style="font-size: 0.68rem;">Splicing &amp; Bypassing Fiber Optik</div>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white flex-shrink-0" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-2.5 p-sm-3" style="max-height: calc(100dvh - 120px); overflow-y: auto; -webkit-overflow-scrolling: touch;">
                    <div class="row g-2">
                        <!-- Titik Lokasi & Jenis Lokasi -->
                        <div class="col-12 col-md-7">
                            <label for="titik_manuver" class="form-label small fw-bold text-navy mb-1">
                                Titik / Lokasi Manuver <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   class="form-control form-control-sm text-uppercase font-monospace"
                                   id="titik_manuver"
                                   name="titik"
                                   placeholder="Contoh: JC-01, OTB-POP-BDG, FAT-04"
                                   required>
                            <!-- Quick suggestions from existing Titik Perbaikan & Joint Closures -->
                            @if($tiket->titikPerbaikans->count() > 0 || $tiket->jointClosures->count() > 0)
                            <div class="d-flex flex-wrap gap-1 mt-1 align-items-center">
                                <span class="small text-muted me-1" style="font-size: 0.68rem;">Preset:</span>
                                @foreach($tiket->titikPerbaikans as $tp)
                                <button type="button" class="btn btn-xs btn-outline-primary titik-preset-btn py-0 px-1.5" style="font-size: 0.68rem;" data-titik="{{ $tp->nama_titik }}">
                                    {{ $tp->nama_titik }}
                                </button>
                                @endforeach
                                @foreach($tiket->jointClosures as $jc)
                                <button type="button" class="btn btn-xs btn-outline-indigo titik-preset-btn py-0 px-1.5" style="font-size: 0.68rem;" data-titik="{{ $jc->nama_closure }}">
                                    {{ $jc->nama_closure }}
                                </button>
                                @endforeach
                            </div>
                            @endif
                        </div>

                        <div class="col-12 col-md-5">
                            <label for="lokasi_tipe_manuver" class="form-label small fw-bold text-navy mb-1">
                                Tipe Lokasi Aset <span class="text-danger">*</span>
                            </label>
                            <select class="form-select form-select-sm" id="lokasi_tipe_manuver" name="lokasi_tipe" required>
                                <option value="CLOSURE_LAPANGAN" selected>CLOSURE LAPANGAN (JC)</option>
                                <option value="POP">POP (Point of Presence)</option>
                                <option value="OTB">OTB (Optical Termination Box)</option>
                                <option value="FAT_FDT">FAT / FDT / ODC</option>
                            </select>
                        </div>

                        <!-- Tipe Manuver (Sebelum / Sesudah) -->
                        <div class="col-6 col-sm-6">
                            <label class="form-label small fw-bold text-navy d-block mb-1">
                                Status Alokasi <span class="text-danger">*</span>
                            </label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="tipe" id="tipeSebelum" value="SEBELUM" autocomplete="off" checked>
                                <label class="btn btn-outline-secondary btn-sm" for="tipeSebelum">
                                    <i class="bi bi-clock-history me-1"></i> SEBELUM
                                </label>

                                <input type="radio" class="btn-check" name="tipe" id="tipeSesudah" value="SESUDAH" autocomplete="off">
                                <label class="btn btn-outline-success btn-sm" for="tipeSesudah">
                                    <i class="bi bi-check2-circle me-1"></i> SESUDAH
                                </label>
                            </div>
                        </div>

                        <!-- Durasi Status Manuver -->
                        <div class="col-6 col-sm-6">
                            <label class="form-label small fw-bold text-navy d-block mb-1">
                                Sifat Manuver <span class="text-danger">*</span>
                            </label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="status_manuver" id="manuverTemp" value="TEMPORARY" autocomplete="off" checked>
                                <label class="btn btn-outline-warning btn-sm" for="manuverTemp">
                                    <i class="bi bi-hourglass-split me-1"></i> SEMENTARA
                                </label>

                                <input type="radio" class="btn-check" name="status_manuver" id="manuverPerm" value="PERMANENT" autocomplete="off">
                                <label class="btn btn-outline-primary btn-sm" for="manuverPerm">
                                    <i class="bi bi-pin-angle-fill me-1"></i> PERMANEN
                                </label>
                            </div>
                        </div>

                        <!-- VISUAL INTERACTIVE CABLE PATCHER FOR MANUVER CORE -->
                        <div class="col-12">
                            <div class="fiber-patcher-box">
                                <div class="fiber-patcher-header">
                                    <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                        <span class="badge bg-primary text-white px-2 py-0.5 rounded-pill" style="font-size: 0.68rem;">
                                            <i class="bi bi-bezier2 me-1"></i> Visual Fiber Patcher
                                        </span>
                                    </div>
                                    <div class="fiber-preset-actions-scroll">
                                        <button type="button" class="fiber-preset-btn" id="btnManuverStraightPreset" title="Sambungkan 1:1 untuk tube yang sedang aktif">
                                            <i class="bi bi-arrows-expand me-1 text-info"></i> Sambung (1:1)
                                        </button>
                                        <button type="button" class="fiber-preset-btn fiber-preset-btn-warning" id="btnManuverSwapPreset" title="Swap sambungan Tube 1 ke Tube 2">
                                            <i class="bi bi-shuffle me-1 text-warning"></i> Swap T1 &rarr; T2
                                        </button>
                                        <button type="button" class="fiber-preset-btn fiber-preset-btn-danger" id="btnManuverClearPreset" title="Hapus semua sambungan kabel">
                                            <i class="bi bi-trash3 me-1 text-danger"></i> Reset
                                        </button>
                                    </div>
                                </div>

                                <!-- Connection Real-time Indicator -->
                                <div class="text-center mb-2">
                                    <div class="fiber-conn-status-badge" id="manuverLiveWireBadge">
                                        <span class="fiber-pulse-laser text-info"><i class="bi bi-lightning-charge-fill"></i></span>
                                        <span id="manuverLiveWireText">Tap Port Asal (Kiri) &rarr; Tap Port Tujuan (Kanan)</span>
                                    </div>
                                </div>

                                <div class="fiber-patcher-grid">
                                    <!-- Panel Kiri: Port Asal / Input Cable -->
                                    <div class="fiber-panel-card">
                                        <div class="fiber-panel-title text-info">
                                            <span><i class="bi bi-box-arrow-in-right me-1"></i> ASAL (INPUT)</span>
                                            <div class="d-flex align-items-center gap-1">
                                                <select class="fiber-cap-select" id="manuverAsalCapacity">
                                                    <option value="2">2 Core</option>
                                                    <option value="4">4 Core</option>
                                                    <option value="6">6 Core</option>
                                                    <option value="8">8 Core</option>
                                                    <option value="12">12 Core</option>
                                                    <option value="24" selected>24 Core (2T)</option>
                                                    <option value="48">48 Core (4T)</option>
                                                    <option value="96">96 Core (8T)</option>
                                                    <option value="144">144 Core (12T)</option>
                                                </select>
                                            </div>
                                        </div>
                                        <!-- Dynamic Tube Tabs -->
                                        <div class="fiber-tube-tabs" id="manuverAsalTubeTabs"></div>
                                        <!-- Dynamic Fiber Core Ports List -->
                                        <div class="fiber-core-list" id="manuverAsalCoreList"></div>
                                    </div>

                                    <!-- Center: Interactive Multi-Wire SVG Fiber Laser Canvas (Desktop) -->
                                    <div class="fiber-canvas-center d-none d-lg-flex">
                                        <svg class="fiber-svg-wire" id="manuverSvgCanvas" viewBox="0 0 120 260" preserveAspectRatio="none">
                                            <g id="manuverSvgWiresGroup">
                                                <!-- Dynamic SVG Paths rendered via JavaScript -->
                                            </g>
                                        </svg>
                                        <div class="text-center mt-1">
                                            <div class="badge bg-dark bg-opacity-75 border border-secondary text-info font-monospace" style="font-size:0.68rem;" id="manuverWireCountBadge">
                                                <i class="bi bi-arrow-left-right me-1"></i> 0 Garis
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Panel Kanan: Port Tujuan / Output Cable -->
                                    <div class="fiber-panel-card">
                                        <div class="fiber-panel-title text-success">
                                            <span><i class="bi bi-box-arrow-right me-1"></i> TUJUAN (OUTPUT)</span>
                                            <div class="d-flex align-items-center gap-1">
                                                <select class="fiber-cap-select" id="manuverTujuanCapacity">
                                                    <option value="2">2 Core</option>
                                                    <option value="4">4 Core</option>
                                                    <option value="6">6 Core</option>
                                                    <option value="8">8 Core</option>
                                                    <option value="12">12 Core</option>
                                                    <option value="24" selected>24 Core (2T)</option>
                                                    <option value="48">48 Core (4T)</option>
                                                    <option value="96">96 Core (8T)</option>
                                                    <option value="144">144 Core (12T)</option>
                                                </select>
                                            </div>
                                        </div>
                                        <!-- Dynamic Tube Tabs -->
                                        <div class="fiber-tube-tabs" id="manuverTujuanTubeTabs"></div>
                                        <!-- Dynamic Fiber Core Ports List -->
                                        <div class="fiber-core-list" id="manuverTujuanCoreList"></div>
                                    </div>
                                </div>

                                <!-- Connected Lines Tray (Chips) -->
                                <div class="fiber-chips-tray" id="manuverChipsTray">
                                    <div class="d-flex align-items-center justify-content-between mb-1.5">
                                        <span class="small fw-bold text-light" style="font-size:0.72rem;">
                                            <i class="bi bi-diagram-3 me-1 text-info"></i> Sambungan Terpasang:
                                        </span>
                                        <span class="small text-muted font-monospace" style="font-size:0.68rem;" id="manuverTotalCoresText">0 Core</span>
                                    </div>
                                    <div class="fiber-connections-chips" id="manuverConnectionsChips">
                                        <span class="text-muted small fst-italic py-1" style="font-size:0.7rem;">Belum ada core yang disambungkan. Tap port Asal lalu Tujuan.</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Synchronized Inputs for Backend Submission (Handles multiple pairs) -->
                            <div id="manuverHiddenInputsContainer">
                                <input type="hidden" id="core_asal" name="core_asal[]" value="Tube 1 Core 1">
                                <input type="hidden" id="core_tujuan" name="core_tujuan[]" value="Tube 1 Core 1">
                            </div>
                        </div>

                        <!-- Core Dialihkan & Titik Kembali -->
                        <div class="col-12 col-sm-6">
                            <label for="core_dialihkan" class="form-label small fw-semibold text-navy mb-1">Core Yang Dialihkan (Opsional)</label>
                            <input type="text" class="form-control form-control-sm font-monospace" id="core_dialihkan" name="core_dialihkan" placeholder="Contoh: Core 4 dialihkan ke Core 12">
                        </div>

                        <div class="col-12 col-sm-6">
                            <label for="titik_kembali" class="form-label small fw-semibold text-navy mb-1">Titik Normalisasi / Kembali (Opsional)</label>
                            <input type="text" class="form-control form-control-sm font-monospace" id="titik_kembali" name="titik_kembali" placeholder="Contoh: OTB POP Bandung Rack 2">
                        </div>

                        <!-- Status Core Aset -->
                        <div class="col-12 col-sm-6">
                            <label for="status_core_aset" class="form-label small fw-bold text-navy mb-1">Status Core Aset</label>
                            <select class="form-select form-select-sm" id="status_core_aset" name="status_core_aset">
                                <option value="OCCUPIED_MANUVER" selected>OCCUPIED MANUVER (Terpakai Jalur Baru)</option>
                                <option value="BROKEN_LOSS">BROKEN / LOSS (Core Rusak)</option>
                                <option value="SPARE_AVAILABLE">SPARE AVAILABLE (Tersedia)</option>
                            </select>
                        </div>

                        <!-- Keterangan -->
                        <div class="col-12 col-sm-6">
                            <label for="keterangan_manuver" class="form-label small fw-semibold text-navy mb-1">Keterangan / Alasan Manuver</label>
                            <input type="text" class="form-control form-control-sm" id="keterangan_manuver" name="keterangan" placeholder="Contoh: Bypassing kabel putus span 14">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light px-3 py-2 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3 flex-fill flex-sm-grow-0" data-bs-dismiss="modal">
                        <i class="bi bi-x me-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-cjp-teal btn-sm px-4 fw-semibold shadow-xs flex-fill flex-sm-grow-0">
                        <i class="bi bi-check2-circle me-1"></i> Simpan Manuver Core
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- ── MODAL INPUT KRONOLOGIS (FASE 3) ── -->
@if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'teknis', 'helpdesk']))
<div class="modal fade" id="addKronologisModal" tabindex="-1" aria-labelledby="addKronologisModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <form action="{{ route('tiket.kronologis.store', $tiket->id) }}" method="POST" enctype="multipart/form-data" id="formAddKronologis">
                @csrf
                <div class="modal-header bg-navy text-white">
                    <h6 class="modal-title fw-bold text-white" id="addKronologisModalLabel">
                        <i class="bi bi-plus-circle-fill text-teal me-2"></i>Tambah Update Kronologis Lapangan
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <!-- Kategori Kronologis -->
                        <div class="col-12 col-md-6">
                            <label for="kategori" class="form-label small fw-bold text-navy">
                                Kategori Update <span class="text-danger">*</span>
                            </label>
                            <select class="form-select form-select-sm" id="kategori" name="kategori" required>
                                <option value="" disabled selected>-- Pilih Kategori --</option>
                                <option value="IZIN">🔑 IZIN - Izin Masuk Ruangan / Lokasi</option>
                                <option value="OTDR">📊 OTDR - Hasil Pengukuran OTDR</option>
                                <option value="TRACING">🔍 TRACING - Tracing Jalur Kabel</option>
                                <option value="MATERIAL">📦 MATERIAL - Material Menuju / Sampai Lokasi</option>
                                <option value="JOINTING">🔧 JOINTING - Proses Jointing / Splicing</option>
                                <option value="LINK_UP">📶 LINK_UP - Link UP / Normalisasi</option>
                                <option value="SELESAI">✅ SELESAI - Pekerjaan Selesai</option>
                                <option value="LAIN">💬 LAIN - Update Lapangan Lainnya</option>
                            </select>
                        </div>

                        <!-- Waktu Update -->
                        <div class="col-12 col-md-6">
                            <label for="krono_timestamp" class="form-label small fw-bold text-navy">
                                Tanggal & Jam Update (WIB) <span class="text-danger">*</span>
                            </label>
                            <input type="datetime-local"
                                   class="form-control form-control-sm"
                                   id="krono_timestamp"
                                   name="timestamp"
                                   value="{{ now()->format('Y-m-d\TH:i') }}"
                                   required>
                        </div>

                        <!-- Informasi Kronologis -->
                        <div class="col-12">
                            <label for="informasi" class="form-label small fw-bold text-navy">
                                Informasi & Catatan Pekerjaan <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control form-control-sm"
                                      id="informasi"
                                      name="informasi"
                                      rows="3"
                                      placeholder="Tuliskan perkembangan pekerjaan (misal: 'Team tiba di lokasi span KM 12+400, persiapan pasang closure JC1...')"
                                      required></textarea>
                        </div>

                        <!-- Upload Foto Lapangan (Opsional) -->
                        <div class="col-12 col-md-6">
                            <label for="foto" class="form-label small fw-semibold text-navy">
                                <i class="bi bi-camera me-1"></i> Upload Foto Lapangan (Opsional)
                            </label>
                            <input type="file"
                                   class="form-control form-control-sm"
                                   id="foto"
                                   name="foto"
                                   accept="image/*">
                            <div class="form-text small">Maks 5 MB (Format: JPG, PNG, WEBP).</div>
                            <div id="fotoPreviewContainer" class="mt-2 d-none">
                                <img id="fotoPreview" src="#" alt="Preview Foto" class="img-thumbnail" style="max-height: 90px;">
                            </div>
                        </div>

                        <!-- Koordinat GPS (Opsional) -->
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold text-navy d-flex justify-content-between">
                                <span><i class="bi bi-geo-alt-fill text-danger me-1"></i> Koordinat GPS (Opsional)</span>
                                <button type="button" class="btn btn-link p-0 text-teal small text-decoration-none" id="btnGetLocation">
                                    <i class="bi bi-crosshair"></i> GPS Saya
                                </button>
                            </label>
                            <div class="input-group input-group-sm mb-1">
                                <input type="number" step="any" class="form-control" id="latitude" name="latitude" placeholder="Latitude (-6.xxx)">
                                <input type="number" step="any" class="form-control" id="longitude" name="longitude" placeholder="Longitude (106.xxx)">
                                <button type="button" class="btn btn-outline-secondary" id="btnOpenMapPicker" title="Pilih di Peta">
                                    <i class="bi bi-map"></i>
                                </button>
                            </div>
                            <div class="form-text small" id="locationStatus">Klik "GPS Saya" atau ikon peta untuk memilih titik.</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-light btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-cjp-teal btn-sm px-4" id="btnSubmitKronologis">
                        <i class="bi bi-send-fill me-1"></i> Kirim Update Kronologis
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ── MODAL MAP PICKER (LEAFLET.JS) ── -->
<div class="modal fade" id="mapPickerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-navy text-white py-2">
                <h6 class="modal-title fw-bold text-white"><i class="bi bi-pin-map-fill text-danger me-2"></i>Pilih Titik Koordinat di Peta</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3">
                <div id="mapPicker"></div>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <span class="small font-monospace" id="pickedCoordsText">Koordinat: -6.917464, 107.619123</span>
                    <button type="button" class="btn btn-primary btn-sm px-3" id="btnApplyPickedCoords">
                        <i class="bi bi-check2 me-1"></i> Gunakan Koordinat Ini
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- ── MODAL EDIT PESAN KOORDINASI ── -->
<div class="modal fade" id="editKronoMsgModal" tabindex="-1" aria-labelledby="editKronoMsgModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 shadow-lg border-0">
            <div class="modal-header border-bottom py-3 bg-light rounded-top-4">
                <h6 class="modal-title fw-bold text-dark d-flex align-items-center gap-2 mb-0" id="editKronoMsgModalLabel">
                    <i class="bi bi-pencil-square text-warning fs-5"></i> Edit Pesan Koordinasi
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditKronoMsg">
                <div class="modal-body py-3">
                    <input type="hidden" id="editKronoId" value="">
                    <div class="mb-2">
                        <label for="editKronoTextInput" class="form-label small fw-semibold text-muted">Isi Pesan</label>
                        <textarea id="editKronoTextInput" class="form-control rounded-3" rows="4" required placeholder="Tulis perbaikan isi pesan koordinasi..." style="font-size: 0.88rem; resize: vertical;"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top py-2.5 bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4" id="btnSaveEditKrono">
                        <i class="bi bi-check-lg me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ── MODAL KONFIRMASI HAPUS PESAN KOORDINASI ── -->
<div class="modal fade" id="deleteKronoMsgModal" tabindex="-1" aria-labelledby="deleteKronoMsgModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-4 shadow-lg border-0 text-center p-3">
            <div class="modal-body py-2">
                <div class="rounded-circle bg-danger-subtle text-danger d-inline-flex align-items-center justify-content-center mb-3" style="width: 48px; height: 48px; font-size: 1.35rem;">
                    <i class="bi bi-trash3-fill"></i>
                </div>
                <h6 class="fw-bold text-dark mb-1">Hapus Pesan?</h6>
                <p class="text-muted small mb-0">Apakah Anda yakin ingin menghapus catatan koordinasi ini? Tindakan tidak dapat dibatalkan.</p>
                <input type="hidden" id="deleteKronoId" value="">
            </div>
            <div class="d-flex gap-2 justify-content-center mt-3">
                <button type="button" class="btn btn-light rounded-pill px-3 flex-grow-1" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger rounded-pill px-3 flex-grow-1" id="btnConfirmDeleteKrono">
                    <i class="bi bi-trash3 me-1"></i> Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ── MODAL ZOOM PHOTO LIGHTBOX ── -->
<div class="modal fade photo-lightbox-modal" id="photoZoomModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content photo-lightbox-content">
            <div class="photo-lightbox-header">
                <div class="d-flex align-items-center gap-2 overflow-hidden me-2">
                    <span class="badge bg-primary bg-opacity-25 text-primary border border-primary border-opacity-50 px-2 py-1 rounded-pill small">
                        <i class="bi bi-camera-fill me-1"></i>Foto Lapangan
                    </span>
                    <span class="text-white fw-semibold small text-truncate" id="photoZoomTitle">Foto Dokumentasi</span>
                </div>
                <div class="d-flex align-items-center gap-1.5 flex-shrink-0">
                    <a href="#" id="photoZoomDownloadBtn" target="_blank" download="foto-lapangan.jpg" class="btn-lightbox-action" title="Download Foto">
                        <i class="bi bi-download"></i>
                    </a>
                    <button type="button" class="btn-lightbox-close" data-bs-dismiss="modal" title="Tutup">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>
            <div class="photo-lightbox-body">
                <img src="#" id="photoZoomImg" class="photo-lightbox-img" alt="Zoom Foto Lapangan">
            </div>
        </div>
    </div>
</div>

<!-- ── MODAL CLOSING AWAL TEKNISI (POINT 1 & 2) ── -->
@if(auth()->user()->hasRole(['admin', 'teknis', 'teknisi']) && $tiket->status === 'PROSES')
<div class="modal fade" id="closingAwalModal" tabindex="-1" aria-labelledby="closingAwalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <form action="{{ route('tiket.closing-awal', $tiket->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-navy text-white">
                    <h6 class="modal-title fw-bold text-white" id="closingAwalModalLabel">
                        <i class="bi bi-check2-all text-info me-2"></i>Penyelesaian Pekerjaan Lapangan (Closing Awal)
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-primary py-2 px-3 small mb-3">
                        <i class="bi bi-info-circle me-1"></i>
                        Setelah disubmit, status tiket akan berubah menjadi <strong>MENUNGGU VERIFIKASI (PENDING_VERIFIKASI)</strong>. HelpDesk / NOC akan melakukan pengecekan stabilitas link sebelum closing akhir.
                    </div>

                    <!-- Checklist Prasyarat Mandatori -->
                    <div class="card border rounded-3 p-3 mb-3 bg-light">
                        <h6 class="fw-bold text-navy small mb-2 d-flex align-items-center gap-1">
                            <i class="bi bi-shield-check text-primary"></i> Checklist Mandatori Penyelesaian Tiket
                        </h6>
                        <div class="row g-2">
                            {{-- ── 1. Resume Pekerjaan (SEMUA TIPE) ──  --}}
                            <div class="col-12 col-sm-6">
                                <div class="d-flex align-items-center gap-2 p-2 rounded bg-white border">
                                    <i class="bi {{ $prereqs['items']['resume_filled'] ? 'bi-check-circle-fill text-success' : 'bi-x-circle-fill text-danger' }} fs-5"></i>
                                    <div class="small">
                                        <div class="fw-bold text-navy">1. Resume Pekerjaan</div>
                                        <div class="text-muted" style="font-size:0.75rem;">{{ $prereqs['items']['resume_filled'] ? 'Problem & Action terisi' : 'Belum diisi di tab Resume' }}</div>
                                    </div>
                                </div>
                            </div>

                            {{-- ── 2. Dokumentasi Foto (SEMUA TIPE) ── --}}
                            <div class="col-12 col-sm-6">
                                <div class="d-flex align-items-center gap-2 p-2 rounded bg-white border">
                                    <i class="bi {{ $prereqs['items']['photo_uploaded'] ? 'bi-check-circle-fill text-success' : 'bi-x-circle-fill text-danger' }} fs-5"></i>
                                    <div class="small">
                                        <div class="fw-bold text-navy">2. Dokumentasi Foto Lapangan</div>
                                        <div class="text-muted" style="font-size:0.75rem;">{{ $prereqs['items']['photo_uploaded'] ? $tiket->dokumentasis->where('kategori', '!=', 'Bukti Masalah')->count() . ' foto terupload' : 'Minimal 1 foto dokumentasi wajib diunggah' }}</div>
                                    </div>
                                </div>
                            </div>

                            @if($tiket->isBroadband())
                            {{-- ── 3. Redaman Sesudah (KHUSUS BROADBAND) ── --}}
                            <div class="col-12 col-sm-6">
                                <div class="d-flex align-items-center gap-2 p-2 rounded bg-white border">
                                    <i class="bi {{ $prereqs['items']['redaman_sesudah'] ? 'bi-check-circle-fill text-success' : 'bi-x-circle-fill text-danger' }} fs-5"></i>
                                    <div class="small">
                                        <div class="fw-bold text-navy">3. Pengukuran Redaman Sesudah (dBm)</div>
                                        <div class="text-muted" style="font-size:0.75rem;">
                                            @if($prereqs['items']['redaman_sesudah'])
                                                Terisi: {{ $tiket->redaman_sesudah ?? $tiket->resume?->redaman_sesudah }} dBm
                                            @else
                                                Wajib diisi di bawah (hasil ukur OPM/modem)
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @else
                            {{-- ── 3. Titik Perbaikan / JC (KHUSUS BACKBONE) ── --}}
                            <div class="col-12 col-sm-6">
                                <div class="d-flex align-items-center gap-2 p-2 rounded bg-white border">
                                    <i class="bi {{ $prereqs['items']['titik_perbaikan'] ? 'bi-check-circle-fill text-success' : 'bi-x-circle-fill text-danger' }} fs-5"></i>
                                    <div class="small">
                                        <div class="fw-bold text-navy">3. Titik Koordinat Perbaikan / JC</div>
                                        <div class="text-muted" style="font-size:0.75rem;">{{ $prereqs['items']['titik_perbaikan'] ? $tiket->titikPerbaikans->count() . ' titik tercatat' : 'Minimal 1 titik perbaikan wajib diinput' }}</div>
                                    </div>
                                </div>
                            </div>

                            {{-- ── 4. Tipe Penanganan (KHUSUS BACKBONE) ── --}}
                            <div class="col-12 col-sm-6">
                                <div class="d-flex align-items-center gap-2 p-2 rounded bg-white border">
                                    <i class="bi {{ $prereqs['items']['tipe_penanganan'] ? 'bi-check-circle-fill text-success' : 'bi-x-circle-fill text-danger' }} fs-5"></i>
                                    <div class="small">
                                        <div class="fw-bold text-navy">4. Tipe Penanganan</div>
                                        <div class="text-muted" style="font-size:0.75rem;">
                                            @if($prereqs['items']['tipe_penanganan'])
                                                @php
                                                    $jcC = $tiket->jointClosures()->count();
                                                    $mvC = $tiket->manuverCores()->count();
                                                    $parts = collect([
                                                        $jcC > 0 ? $jcC.' Joint Closure' : null,
                                                        $mvC > 0 ? $mvC.' Manuver Core' : null,
                                                    ])->filter()->join(', ');
                                                @endphp
                                                {{ $parts }}
                                            @else
                                                Wajib tambah data Joint Closure atau Manuver Core
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>

                        @if(!$prereqs['ready'])
                        <div class="alert alert-danger py-2 px-3 small mt-2 mb-0">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>
                            <strong>Perhatian:</strong> Anda belum dapat mengajukan closing karena masih ada <strong>{{ count($prereqs['missing_items']) }} item prasyarat mandatori</strong> yang belum dilengkapi.
                        </div>
                        @endif
                    </div>

                    @if($tiket->isBroadband())
                    {{-- ── INPUT REDAMAN (KHUSUS BROADBAND) ── --}}
                    <div class="card border rounded-3 p-3 mb-3" style="border-color: #0d6efd22 !important; background: #f0f7ff;">
                        <h6 class="fw-bold small mb-3 d-flex align-items-center gap-1" style="color: #0d6efd;">
                            <i class="bi bi-activity"></i> Hasil Pengukuran Redaman Optik
                        </h6>
                        <div class="row g-3">
                            <div class="col-12 col-sm-6">
                                <label for="redaman_sebelum_closing" class="form-label small fw-semibold text-navy">
                                    Redaman Sebelum Perbaikan (dBm)
                                    <span class="text-muted fw-normal">(opsional)</span>
                                </label>
                                <div class="input-group input-group-sm">
                                    <input type="number"
                                           step="0.01"
                                           min="-60"
                                           max="0"
                                           class="form-control"
                                           id="redaman_sebelum_closing"
                                           name="redaman_sebelum"
                                           value="{{ old('redaman_sebelum', $tiket->redaman_sebelum ?? $tiket->resume?->redaman_sebelum) }}"
                                           placeholder="-30.00">
                                    <span class="input-group-text">dBm</span>
                                </div>
                                <div class="form-text" style="font-size:0.72rem;">Nilai negatif, contoh: -28.5</div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label for="redaman_sesudah_closing" class="form-label small fw-semibold text-navy">
                                    Redaman Sesudah Perbaikan (dBm)
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-sm">
                                    <input type="number"
                                           step="0.01"
                                           min="-60"
                                           max="0"
                                           class="form-control"
                                           id="redaman_sesudah_closing"
                                           name="redaman_sesudah"
                                           value="{{ old('redaman_sesudah', $tiket->redaman_sesudah ?? $tiket->resume?->redaman_sesudah) }}"
                                           placeholder="-18.50"
                                           {{ $tiket->isBroadband() && !$prereqs['items']['redaman_sesudah'] ? 'required' : '' }}>
                                    <span class="input-group-text">dBm</span>
                                </div>
                                <div class="form-text" style="font-size:0.72rem;">Nilai negatif, contoh: -18.5. Wajib diisi!</div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="mb-3">
                        <label for="catatan_closing_teknisi" class="form-label small fw-bold text-navy">
                            Catatan Ringkas Hasil Lapangan untuk Helpdesk / NOC <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control form-control-sm"
                                  id="catatan_closing_teknisi"
                                  name="catatan_closing_teknisi"
                                  rows="3"
                                  placeholder="{{ $tiket->isBroadband() ? 'Contoh: OPM terbaca -18.2 dBm (normal), modem sudah sinkron, pelanggan terkonfirmasi sudah normal...' : 'Contoh: Splicing core 1-12 di closure KM 14 selesai, redaman terukur -18.2 dBm, link siap diverifikasi NOC...' }}"
                                  required>{{ old('catatan_closing_teknisi') }}</textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-light btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4" {{ $prereqs['ready'] ? '' : 'disabled' }}>
                        <i class="bi bi-send-check me-1"></i> Selesaikan & Kirim ke NOC
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endif

<!-- ── MODAL REJECT CLOSING AWAL OLEH HELPDESK (POINT 2) ── -->
@if(auth()->user()->hasRole(['admin', 'helpdesk']) && $tiket->status === 'PENDING_VERIFIKASI')
<div class="modal fade" id="rejectClosingAwalModal" tabindex="-1" aria-labelledby="rejectClosingAwalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form action="{{ route('tiket.reject-closing-awal', $tiket->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h6 class="modal-title fw-bold text-white" id="rejectClosingAwalModalLabel">
                        <i class="bi bi-arrow-return-left me-2"></i>Kembalikan ke Lapangan (Reject Closing)
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-warning py-2 px-3 small mb-3">
                        <i class="bi bi-exclamation-triangle me-1"></i> Status tiket akan dikembalikan ke <strong>PROSES</strong>. Teknisi akan diminta melakukan investigasi atau perbaikan lanjutan.
                    </div>

                    <div class="mb-0">
                        <label for="alasan_reject" class="form-label small fw-bold text-navy">
                            Alasan Penolakan / Catatan untuk Teknisi <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control form-control-sm"
                                  id="alasan_reject"
                                  name="alasan_reject"
                                  rows="3"
                                  placeholder="Contoh: Redaman di OLT masih tinggi (-28 dBm) atau link masih flapping, mohon periksa kembali core nomor 4..."
                                  required>{{ old('alasan_reject') }}</textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-light btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger btn-sm px-4">
                        <i class="bi bi-x-circle me-1"></i> Konfirmasi Kembalikan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- ── MODAL STOP CLOCK (POINT 3) ── -->
@if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'helpdesk', 'teknis']))
<div class="modal fade" id="startStopClockModal" tabindex="-1" aria-labelledby="startStopClockModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form action="{{ route('tiket.stop-clock.start', $tiket->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-warning text-dark">
                    <h6 class="modal-title fw-bold" id="startStopClockModalLabel">
                        <i class="bi bi-pause-circle-fill me-2"></i>Stop Clock SLA (Jeda Perhitungan)
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-info py-2 px-3 small mb-3">
                        <i class="bi bi-info-circle me-1"></i> Selama stop clock aktif, durasi jeda waktu tidak akan dihitung ke dalam SLA / MTTR gangguan link.
                    </div>

                    <div class="mb-3">
                        <label for="stop_clock_reason" class="form-label small fw-bold text-navy">
                            Alasan Stop Clock <span class="text-danger">*</span>
                        </label>
                        <select class="form-select form-select-sm" id="stop_clock_reason" name="reason" required>
                            <option value="">-- Pilih Alasan Stop Clock --</option>
                            <option value="MENUNGGU_AKSES_PELANGGAN">Menunggu Izin / Akses Lokasi Pelanggan</option>
                            <option value="CUACA_BURUK">Cuaca Buruk / Hujan Deras / Banjir</option>
                            <option value="MENUNGGU_MATERIAL">Menunggu Pengiriman Material / Sparing Khusus</option>
                            <option value="KENDALA_PIHAK_KETIGA">Kendala Perizinan Pihak Ketiga (PLN/Bina Marga/dll)</option>
                            <option value="PERMINTAAN_PELANGGAN">Penundaan atas Permintaan Pelanggan</option>
                            <option value="FORCE_MAJEURE">Force Majeure / Bencana Alam</option>
                            <option value="LAINNYA">Alasan Lainnya</option>
                        </select>
                    </div>

                    <div class="mb-0">
                        <label for="stop_clock_notes" class="form-label small fw-semibold text-navy">Keterangan Tambahan / Detail Kendala</label>
                        <textarea class="form-control form-control-sm"
                                  id="stop_clock_notes"
                                  name="notes"
                                  rows="2"
                                  placeholder="Contoh: Petugas keamanan gedung belum memberikan izin masuk sebelum jam 13:00..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-light btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning btn-sm px-4 fw-bold">
                        <i class="bi bi-pause-fill me-1"></i> Mulai Stop Clock
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- ── MODAL OPER SHIFT / HANDOVER (POINT 8) ── -->
@if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'helpdesk', 'teknis']))
<div class="modal fade" id="handoverShiftModal" tabindex="-1" aria-labelledby="handoverShiftModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-xl overflow-hidden">
            <form action="{{ route('tiket.handover-shift', $tiket->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-navy text-white p-3.5" style="background: linear-gradient(135deg, #07152b 0%, #102d66 100%);">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: rgba(139, 92, 246, 0.25); color: #c4b5fd;">
                            <i class="bi bi-arrow-left-right"></i>
                        </div>
                        <div>
                            <h6 class="modal-title fw-bold text-white mb-0" id="handoverShiftModalLabel">Serah Terima Pekerjaan (Oper Shift)</h6>
                            <span class="small text-white-50" style="font-size: 0.72rem;">Tiket: {{ $tiket->no_tiket }} ({{ $tiket->backbone_segment }})</span>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3 p-md-4">
                    <!-- Status Ringkasan Lapangan Saat Ini -->
                    <div class="p-2.5 rounded-3 mb-3 bg-light border border-light-subtle d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="small">
                            <span class="text-muted">Status:</span> <strong class="text-navy">{{ $tiket->status }}</strong> &bull;
                            <span class="text-muted">Update:</span> <strong>{{ $tiket->minutes_since_last_update !== null ? $tiket->minutes_since_last_update . 'm lalu' : 'Baru' }}</strong>
                        </div>
                        @if($tiket->is_stop_clock)
                            <span class="badge bg-warning text-dark font-monospace" style="font-size: 0.68rem;">
                                <i class="bi bi-pause-circle-fill me-1"></i>SLA Paused
                            </span>
                        @endif
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label for="shift_sebelum" class="form-label small fw-semibold text-navy mb-1">Shift Asal (From)</label>
                            <select class="form-select form-select-sm" id="shift_sebelum" name="shift_sebelum" required>
                                <option value="Shift 1 (Pagi 07:00-15:00)" selected>Shift 1 (Pagi 07:00-15:00)</option>
                                <option value="Shift 2 (Siang 15:00-23:00)">Shift 2 (Siang 15:00-23:00)</option>
                                <option value="Shift 3 (Malam 23:00-07:00)">Shift 3 (Malam 23:00-07:00)</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label for="shift_tujuan" class="form-label small fw-bold text-navy mb-1">
                                Shift Tujuan (To) <span class="text-danger">*</span>
                            </label>
                            <select class="form-select form-select-sm" id="shift_tujuan" name="shift_tujuan" required>
                                <option value="Shift 1 (Pagi 07:00-15:00)">Shift 1 (Pagi 07:00-15:00)</option>
                                <option value="Shift 2 (Siang 15:00-23:00)" selected>Shift 2 (Siang 15:00-23:00)</option>
                                <option value="Shift 3 (Malam 23:00-07:00)">Shift 3 (Malam 23:00-07:00)</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="user_to_id" class="form-label small fw-bold text-navy mb-1">
                            Petugas Penerima Handover (PIC Lanjutan)
                        </label>
                        <select class="form-select form-select-sm" id="user_to_id" name="user_to_id">
                            <option value="">-- Pilih Petugas Penerima Shift --</option>
                            @foreach($mentionableUsers as $mu)
                                <option value="{{ $mu['id'] }}">
                                    {{ $mu['name'] }} ({{ $mu['role'] }})
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text" style="font-size: 0.7rem;">Petugas terpilih akan menerima notifikasi serah terima tiket ini secara instan di HP &amp; browser.</div>
                    </div>

                    <!-- Quick Template Chips -->
                    <div class="mb-2">
                        <label class="form-label small fw-semibold text-navy mb-1">Template Cepat Catatan:</label>
                        <div class="d-flex flex-wrap gap-1">
                            <button type="button" class="btn btn-light btn-xs border rounded-pill py-0.5 px-2 text-muted" style="font-size: 0.7rem;" onclick="appendHandoverNote('[Jointing Core Berlangsung] ')">+ Jointing Core</button>
                            <button type="button" class="btn btn-light btn-xs border rounded-pill py-0.5 px-2 text-muted" style="font-size: 0.7rem;" onclick="appendHandoverNote('[Menunggu OTDR Ulang] ')">+ Butuh OTDR</button>
                            <button type="button" class="btn btn-light btn-xs border rounded-pill py-0.5 px-2 text-muted" style="font-size: 0.7rem;" onclick="appendHandoverNote('[Material & Splicer Lengkap] ')">+ Material OK</button>
                            <button type="button" class="btn btn-light btn-xs border rounded-pill py-0.5 px-2 text-muted" style="font-size: 0.7rem;" onclick="appendHandoverNote('[Kunci Shelter Diserahterimakan] ')">+ Kunci Shelter</button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="status_lapangan" class="form-label small fw-bold text-navy mb-1">
                            Catatan Serah Terima / Kondisi Lapangan <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control form-control-sm"
                                  id="status_lapangan"
                                  name="status_lapangan"
                                  rows="3"
                                  placeholder="Tuliskan perkembangan pekerjaan terakhir, sisa core/kabel yang perlu disambung, dan hal penting bagi petugas shift berikutnya..."
                                  required></textarea>
                    </div>

                    <div class="mb-0">
                        <label for="kendala_pending" class="form-label small fw-semibold text-navy mb-1">Hambatan / Catatan Tambahan (Opsional)</label>
                        <input type="text" class="form-control form-control-sm" id="kendala_pending" name="kendala_pending" placeholder="Contoh: Genset/baterai splicer menipis, butuh recharge">
                    </div>
                </div>
                <div class="modal-footer bg-light py-2.5 px-3.5 d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3 rounded-pill" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 rounded-pill shadow-xs d-inline-flex align-items-center gap-1.5" style="background: linear-gradient(135deg, #7c3aed, #6d28d9); border: none;">
                        <i class="bi bi-arrow-left-right"></i>
                        <span>Kirim &amp; Simpan Handover</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function appendHandoverNote(text) {
        const textarea = document.getElementById('status_lapangan');
        if (textarea) {
            textarea.value = (textarea.value ? textarea.value + ' ' : '') + text;
            textarea.focus();
        }
    }
</script>
@endif

<!-- ── MODAL CLOSING TIKET / VERIFIKASI CLOSING AKHIR ── -->
@if(auth()->user()->hasRole(['admin', 'helpdesk']) && $tiket->status === 'PENDING_VERIFIKASI')
<div class="modal fade" id="closeTiketModal" tabindex="-1" aria-labelledby="closeTiketModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form action="{{ route('tiket.close', $tiket->id) }}" method="POST" id="formCloseTiket">
                @csrf
                <div class="modal-header bg-navy text-white">
                    <h6 class="modal-title fw-bold text-white" id="closeTiketModalLabel">
                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                        {{ $tiket->status === 'PENDING_VERIFIKASI' ? 'Verifikasi & Closing Akhir' : 'Penutupan Tiket' }} [{{ $tiket->no_tiket }}]
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    @if($tiket->status === 'PENDING_VERIFIKASI')
                    <div class="alert alert-success py-2.5 px-3 small mb-3">
                        <div class="fw-bold"><i class="bi bi-check2-all me-1"></i> Closing Awal dari Teknisi:</div>
                        <div class="text-muted mt-1">
                            Diselesaikan oleh <strong>{{ $tiket->resolver?->name ?? 'Teknisi' }}</strong> pada {{ $tiket->resolved_at ? $tiket->resolved_at->format('d/m/Y H:i') : '-' }}.
                            @if($tiket->closing_notes_teknisi)
                            <div class="mt-1 fst-italic">"{{ $tiket->closing_notes_teknisi }}"</div>
                            @endif
                        </div>
                    </div>
                    @else
                    <div class="alert alert-info py-2 px-3 small mb-3">
                        <i class="bi bi-info-circle me-1"></i> Saat tiket di-close, sistem akan mengunci data dan secara otomatis menghitung durasi total gangguan (MTTR) serta memeriksa kepatuhan SLA.
                    </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Status Link Impact</label>
                        <input type="text" class="form-control form-control-sm bg-light" value="{{ $tiket->status_link_impact }}" readonly>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-muted">Waktu Open</label>
                            <input type="text" class="form-control form-control-sm bg-light font-monospace" value="{{ $tiket->tanggal_open->format('d/m/Y H:i') }}" readonly>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-muted">Target SLA</label>
                            <input type="text" class="form-control form-control-sm bg-light" value="{{ $tiket->formatted_sla_target }}" readonly>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="modal_tanggal_close" class="form-label small fw-bold text-navy">
                            Waktu Closing Tiket (WIB) <span class="text-danger">*</span>
                        </label>
                        <input type="datetime-local"
                               class="form-control form-control-sm"
                               id="modal_tanggal_close"
                               name="tanggal_close"
                               value="{{ now()->format('Y-m-d\TH:i') }}"
                               required>
                        <div class="form-text small">Sesuaikan jika waktu link UP berbeda dengan saat closing.</div>
                    </div>

                    <!-- Live MTTR and SLA Calculator Preview in Modal -->
                    <div class="p-3 bg-light rounded-3 border mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small text-muted">Total Jeda Stop Clock:</span>
                            <span class="fw-bold font-monospace text-warning">{{ $tiket->total_stop_clock_minutes }} Menit</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small text-muted">Estimasi MTTR (Bersih):</span>
                            <span class="fw-bold font-monospace text-navy" id="modalMttrPreview">-</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="small text-muted">Status SLA:</span>
                            <span class="badge bg-secondary" id="modalSlaPreview">Menghitung...</span>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label for="modal_catatan_closing" class="form-label small fw-semibold text-navy">Catatan Closing (Opsional)</label>
                        <textarea class="form-control form-control-sm"
                                  id="modal_catatan_closing"
                                  name="catatan_closing"
                                  rows="2"
                                  placeholder="Contoh: Link backbone telah UP normal dan stabil setelah proses perbaikan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-light btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success btn-sm px-4">
                        <i class="bi bi-check2-circle me-1"></i> {{ $tiket->status === 'PENDING_VERIFIKASI' ? 'Konfirmasi Verifikasi & Close' : 'Konfirmasi Closing Tiket' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
