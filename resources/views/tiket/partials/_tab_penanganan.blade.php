                <!-- ════ TAB: PENANGANAN CORE & JOINT CLOSURE (JC) ════ -->
                @if($tiket->isBackbone())
                <div class="tab-pane fade" id="penanganan-pane" role="tabpanel">
                    @php
                        $rawPenanganan = $tiket->tipe_penanganan ?? $tiket->resume?->tipe_penanganan;
                        $hasJc = $tiket->jointClosures->count() > 0;
                        $hasManuver = $tiket->manuverCores->count() > 0;

                        if (in_array($rawPenanganan, ['KEDUA', 'KOMBINASI', 'SEMUA']) || ($hasJc && $hasManuver)) {
                            $activePenanganan = 'KEDUA';
                        } elseif ($rawPenanganan === 'MANUVER_CORE' || (!$hasJc && $hasManuver)) {
                            $activePenanganan = 'MANUVER_CORE';
                        } elseif ($rawPenanganan === 'JOINTING_LURUS' || ($hasJc && !$hasManuver)) {
                            $activePenanganan = 'JOINTING_LURUS';
                        } else {
                            $activePenanganan = 'JOINTING_LURUS';
                        }

                        $isJointingActive = in_array($activePenanganan, ['JOINTING_LURUS', 'KEDUA', 'KOMBINASI', 'SEMUA']);
                        $isManuverActive = in_array($activePenanganan, ['MANUVER_CORE', 'KEDUA', 'KOMBINASI', 'SEMUA']);
                    @endphp

                    <!-- Tipe Penanganan Switcher Banner -->
                    <div class="card border-0 shadow-sm rounded-xl mb-3 mb-md-4 bg-white overflow-hidden">
                        <div class="card-body p-2.5 p-sm-3 p-md-4">
                            <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-2.5 mb-3 pb-2 border-bottom">
                                <div>
                                    <div class="fw-bold text-navy fs-6 d-flex align-items-center gap-2">
                                        <i class="bi bi-bezier2" style="color: #6366f1;"></i>
                                        <span>Pilih Metode Penanganan Fisik / Core</span>
                                    </div>
                                    <div class="text-muted small" style="font-size: 0.8rem;">
                                        Tentukan metode perbaikan kabel. Anda dapat memilih salah satu atau <strong>mengaktifkan keduanya</strong> sekaligus.
                                    </div>
                                </div>
                                <div id="penangananStatusIndicator" class="d-flex align-items-center justify-content-between justify-content-md-end gap-2 flex-wrap w-100 w-md-auto">
                                    <span class="badge bg-light text-navy border px-2.5 py-1.5 rounded-pill small">
                                        Pilihan: <strong id="penangananActiveLabel" class="text-primary">
                                            @if($activePenanganan === 'KEDUA')
                                                Jointing &amp; Manuver (Keduanya)
                                            @elseif($activePenanganan === 'MANUVER_CORE')
                                                Manuver Core
                                            @else
                                                Jointing Lurus
                                            @endif
                                        </strong>
                                    </span>
                                    <button type="button" 
                                            class="btn btn-xs rounded-pill px-2.5 py-1 fw-semibold {{ $activePenanganan === 'KEDUA' ? 'btn-primary text-white shadow-xs' : 'btn-outline-primary' }}" 
                                            id="btnToggleKedua"
                                            onclick="toggleKeduaPenanganan()"
                                            title="Pilih dan aktifkan kedua metode sekaligus">
                                        @if($activePenanganan === 'KEDUA')
                                            <i class="bi bi-check2-all me-1"></i> Keduanya Aktif
                                        @else
                                            <i class="bi bi-layers-fill me-1"></i> Pilih Keduanya
                                        @endif
                                    </button>
                                </div>
                            </div>

                            <div class="row g-2 g-md-3" id="penangananModeSelector">
                                <!-- Option 1: Jointing Lurus -->
                                <div class="col-12 col-md-6">
                                    <div class="penanganan-mode-card {{ $isJointingActive ? 'active is-selected' : '' }}"
                                         id="cardModeJointing"
                                         onclick="togglePenangananCard('JOINTING_LURUS')">
                                        <div class="d-flex align-items-start gap-2.5 penanganan-mode-inner h-100">
                                            <div class="penanganan-mode-icon-box bg-indigo-subtle text-indigo rounded-circle d-flex align-items-center justify-content-center mt-0.5">
                                                <i class="bi bi-diagram-3-fill"></i>
                                            </div>
                                            <div class="flex-grow-1 min-w-0">
                                                <div class="d-flex align-items-center justify-content-between gap-1 mb-1">
                                                    <div class="fw-bold text-navy text-truncate penanganan-title-text">1. Jointing Lurus</div>
                                                    <div class="d-flex align-items-center gap-1.5 flex-shrink-0">
                                                        <span class="badge bg-indigo-subtle text-indigo font-monospace px-1.5 py-0.5 rounded-pill" style="font-size: 0.68rem;">{{ $tiket->jointClosures->count() }} Data JC</span>
                                                        <div class="penanganan-radio-check d-flex align-items-center">
                                                            <i class="bi {{ $isJointingActive ? 'bi-check-circle-fill text-indigo fs-5' : 'bi-circle text-muted fs-5' }}"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="text-muted penanganan-desc-text">
                                                    Penyambungan kabel lurus eksisting &amp; jumper, mapping tube-core per tray, serta penambahan closure baru.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Option 2: Manuver Core -->
                                <div class="col-12 col-md-6">
                                    <div class="penanganan-mode-card {{ $isManuverActive ? 'active is-selected' : '' }}"
                                         id="cardModeManuver"
                                         onclick="togglePenangananCard('MANUVER_CORE')">
                                        <div class="d-flex align-items-start gap-2.5 penanganan-mode-inner h-100">
                                            <div class="penanganan-mode-icon-box bg-purple-subtle text-purple rounded-circle d-flex align-items-center justify-content-center mt-0.5">
                                                <i class="bi bi-shuffle"></i>
                                            </div>
                                            <div class="flex-grow-1 min-w-0">
                                                <div class="d-flex align-items-center justify-content-between gap-1 mb-1">
                                                    <div class="fw-bold text-navy text-truncate penanganan-title-text">2. Manuver Core</div>
                                                    <div class="d-flex align-items-center gap-1.5 flex-shrink-0">
                                                        <span class="badge bg-purple-subtle text-purple font-monospace px-1.5 py-0.5 rounded-pill" style="font-size: 0.68rem;">{{ $tiket->manuverCores->count() }} Record</span>
                                                        <div class="penanganan-radio-check d-flex align-items-center">
                                                            <i class="bi {{ $isManuverActive ? 'bi-check-circle-fill text-purple fs-5' : 'bi-circle text-muted fs-5' }}"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="text-muted penanganan-desc-text">
                                                    Pengalihan alokasi core serat optik (swapping core / bypass jalur putus) sebelum dan sesudah perbaikan.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ── SUB-VIEW: JOINTING LURUS (KABEL & JC) ── -->
                    <div id="penangananSubViewJointing" class="{{ $isJointingActive ? '' : 'd-none' }}">
                        <div class="tab-header-banner">
                            <div class="tab-header-left">
                                <div class="tab-header-icon-box icon-box-indigo">
                                    <i class="bi bi-diagram-3-fill"></i>
                                </div>
                                <div>
                                    <div class="tab-header-title">Pencatatan Kabel &amp; Sambungan Joint Closure (JC)</div>
                                    <div class="tab-header-subtitle">Kapasitas kabel eksisting &amp; jumper, mapping tube-core, status core sisa, dan aset baru closure</div>
                                </div>
                            </div>
                            @if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'teknis', 'helpdesk']))
                            <button class="btn-tab-action-pro btn-indigo-pro btn-mobile-block" data-bs-toggle="modal" data-bs-target="#tambahJointClosureModal">
                                <i class="bi bi-plus-circle-fill"></i>
                                <span>Tambah Joint Closure</span>
                            </button>
                            @endif
                        </div>

                        @if($tiket->jointClosures->count() > 0)
                            <div class="d-flex flex-column gap-4">
                                @foreach($tiket->jointClosures as $jc)
                                <div class="jc-card-pro">
                                    <!-- JC Card Header -->
                                    <div class="jc-header-pro">
                                        <div class="d-flex align-items-center gap-2 flex-wrap">
                                            <div class="jc-title-badge">
                                                <i class="bi bi-box-seam text-cyan"></i>
                                                <span>{{ $jc->nama_closure }}</span>
                                            </div>

                                            @if($jc->is_aset_baru)
                                                <span class="jc-meta-chip jc-chip-baru">
                                                    <i class="bi bi-stars"></i> ASET BARU
                                                </span>
                                            @else
                                                <span class="jc-meta-chip jc-chip-eksisting">
                                                    <i class="bi bi-layers"></i> EKSISTING
                                                </span>
                                            @endif

                                            <span class="jc-meta-chip jc-chip-type">
                                                <i class="bi bi-tag-fill text-indigo"></i> {{ $jc->jenis_closure }}
                                            </span>

                                            <span class="jc-meta-chip jc-chip-location">
                                                <i class="bi bi-geo-fill text-orange"></i> {{ str_replace('_', ' ', $jc->lokasi_fisik) }}
                                            </span>

                                            @if($jc->latitude && $jc->longitude)
                                            <a href="{{ $jc->google_maps_url }}" target="_blank" class="jc-meta-chip jc-chip-geo" title="Buka di Google Maps">
                                                <i class="bi bi-geo-alt-fill text-primary"></i> {{ round($jc->latitude, 5) }}, {{ round($jc->longitude, 5) }}
                                                <i class="bi bi-box-arrow-up-right ms-0.5" style="font-size: 0.6rem;"></i>
                                            </a>
                                            @endif
                                        </div>

                                        <div class="d-flex align-items-center gap-2">
                                            <span class="text-muted small" style="font-size: 0.75rem;">
                                                <i class="bi bi-person text-secondary me-1"></i>{{ $jc->creator?->name ?? 'Sistem' }} &bull; {{ $jc->created_at->format('d/m H:i') }}
                                            </span>
                                            @if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'teknis', 'helpdesk']))
                                            <form action="{{ route('tiket.joint-closure.destroy', $jc->id) }}" method="POST"
                                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus data Joint Closure {{ $jc->nama_closure }} beserta semua sambungan core di dalamnya?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="jc-btn-delete" title="Hapus Joint Closure">
                                                    <i class="bi bi-trash3"></i>
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- JC Card Body -->
                                    <div class="card-body p-3 p-md-4">
                                        <!-- Cable Capacity Specs Row -->
                                        <div class="jc-spec-banner mb-3">
                                            <div class="row g-2 align-items-center">
                                                <div class="col-12 col-md-5">
                                                    <div class="jc-spec-box">
                                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                                            <span class="text-muted small fw-bold text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.3px;">
                                                                <i class="bi bi-arrow-down-right-circle text-indigo me-1"></i>Kabel Eksisting / Asal
                                                            </span>
                                                            <span class="jc-tube-badge-asal">
                                                                {{ $jc->jumlah_tube_asal }} Tube
                                                            </span>
                                                        </div>
                                                        <div class="d-flex align-items-baseline gap-2">
                                                            <span class="fw-bold text-navy fs-4">{{ $jc->kapasitas_kabel_asal }}</span>
                                                            <span class="text-muted small fw-semibold">Core</span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-12 col-md-2 text-center py-1">
                                                    <div class="d-inline-flex align-items-center justify-content-center bg-white border rounded-circle shadow-xs" style="width: 38px; height: 38px;">
                                                        <i class="bi bi-arrow-left-right text-indigo fs-5"></i>
                                                    </div>
                                                    <div class="text-muted" style="font-size: 0.68rem; font-weight: 600;">DISAMBUNG</div>
                                                </div>

                                                <div class="col-12 col-md-5">
                                                    <div class="jc-spec-box">
                                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                                            <span class="text-muted small fw-bold text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.3px;">
                                                                <i class="bi bi-arrow-up-left-circle text-success me-1"></i>Kabel Jumper / Distribusi
                                                            </span>
                                                            <span class="jc-tube-badge-jumper">
                                                                {{ $jc->jumlah_tube_jumper }} Tube
                                                            </span>
                                                        </div>
                                                        <div class="d-flex align-items-baseline gap-2">
                                                            <span class="fw-bold text-navy fs-4">{{ $jc->kapasitas_kabel_jumper }}</span>
                                                            <span class="text-muted small fw-semibold">Core</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            @if($jc->keterangan)
                                            <div class="mt-2 pt-2 border-top text-muted small" style="font-size: 0.78rem;">
                                                <i class="bi bi-info-circle text-primary me-1"></i><strong>Catatan:</strong> {{ $jc->keterangan }}
                                            </div>
                                            @endif
                                        </div>

                                        <!-- Summary Core Status & Action Bar -->
                                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3 pb-2 border-bottom">
                                            <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                                @php
                                                    $cSummary = $jc->core_summary;
                                                @endphp
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill small fw-semibold">
                                                    <i class="bi bi-check2-circle me-1"></i>{{ $cSummary['terhubung'] }} Terhubung
                                                </span>
                                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2.5 py-1 rounded-pill small fw-semibold">
                                                    <i class="bi bi-dash-circle me-1"></i>{{ $cSummary['spare'] }} Core Sisa / Spare
                                                </span>
                                                @if($cSummary['loss'] > 0)
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1 rounded-pill small fw-semibold">
                                                    <i class="bi bi-x-circle me-1"></i>{{ $cSummary['loss'] }} Loss / Putus
                                                </span>
                                                @endif
                                                @if($cSummary['manuver'] > 0)
                                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2.5 py-1 rounded-pill small fw-semibold">
                                                    <i class="bi bi-shuffle me-1"></i>{{ $cSummary['manuver'] }} Manuver
                                                </span>
                                                @endif
                                                <span class="badge bg-light text-navy border px-2.5 py-1 rounded-pill small fw-semibold">
                                                    Total: {{ $cSummary['total'] }} Splice
                                                </span>
                                            </div>

                                            @if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'teknis', 'helpdesk']))
                                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold open-add-core-btn"
                                                    data-jc-id="{{ $jc->id }}"
                                                    data-jc-name="{{ $jc->nama_closure }}"
                                                    data-jc-cap-asal="{{ $jc->kapasitas_kabel_asal ?? 24 }}"
                                                    data-jc-cap-jumper="{{ $jc->kapasitas_kabel_jumper ?? 24 }}"
                                                    data-jc-tube-asal="{{ $jc->jumlah_tube_asal ?? 2 }}"
                                                    data-jc-tube-jumper="{{ $jc->jumlah_tube_jumper ?? 2 }}"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#addJointClosureCoreModal">
                                                <i class="bi bi-plus-lg me-1"></i> Tambah Sambungan Core
                                            </button>
                                            @endif
                                        </div>

                                        <!-- Core Splicing Matrix Records -->
                                        @if($jc->cores->count() > 0)
                                            <div class="table-responsive">
                                                <table class="table table-sm table-hover align-middle border mb-0 rounded-3 overflow-hidden">
                                                    <thead class="table-light">
                                                        <tr style="font-size: 0.73rem; text-transform: uppercase; letter-spacing: 0.4px; color: #475569;">
                                                            <th class="ps-3 py-2">Kabel Asal (Tube - Core)</th>
                                                            <th style="width: 30px;"></th>
                                                            <th class="py-2">Kabel Jumper (Tube - Core)</th>
                                                            <th class="py-2">Status</th>
                                                            <th class="py-2">Loss (dB)</th>
                                                            <th class="py-2">Keterangan</th>
                                                            @if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'teknis', 'helpdesk']))
                                                            <th class="text-end pe-3 py-2">Aksi</th>
                                                            @endif
                                                        </tr>
                                                    </thead>
                                                    <tbody style="font-size: 0.82rem;">
                                                        @foreach($jc->cores as $c)
                                                        <tr>
                                                            <td class="ps-3">
                                                                <span class="badge bg-indigo-subtle text-indigo font-monospace px-2 py-0.5 me-1" style="font-size: 0.72rem;">
                                                                    {{ $c->tube_asal }}
                                                                </span>
                                                                <span class="font-monospace fw-bold text-navy">
                                                                    {{ $c->core_asal }}
                                                                </span>
                                                            </td>
                                                            <td class="text-center text-muted px-1" style="width: 30px;">
                                                                <i class="bi bi-arrow-right text-indigo"></i>
                                                            </td>
                                                            <td>
                                                                @if($c->tube_jumper || $c->core_jumper)
                                                                    <span class="badge bg-success-subtle text-success font-monospace px-2 py-0.5 me-1" style="font-size: 0.72rem;">
                                                                        {{ $c->tube_jumper ?: '-' }}
                                                                    </span>
                                                                    <span class="font-monospace fw-bold text-navy">
                                                                        {{ $c->core_jumper ?: '-' }}
                                                                    </span>
                                                                @else
                                                                    <span class="text-muted fst-italic small">Tanpa Sambungan (Dikosongkan)</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <span class="badge {{ $c->status_badge_class }} px-2 py-1 rounded-pill" style="font-size: 0.72rem;">
                                                                    {{ str_replace('_', ' ', $c->status) }}
                                                                </span>
                                                            </td>
                                                            <td class="font-monospace">
                                                                @if($c->loss_db !== null)
                                                                    <span class="badge bg-light text-navy border font-monospace px-2 py-0.5">
                                                                        {{ number_format($c->loss_db, 2) }} dB
                                                                    </span>
                                                                @else
                                                                    <span class="text-muted">-</span>
                                                                @endif
                                                            </td>
                                                            <td class="text-muted small">
                                                                {{ $c->keterangan ?: '-' }}
                                                            </td>
                                                            @if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'teknis', 'helpdesk']))
                                                            <td class="text-end pe-3">
                                                                <form action="{{ route('tiket.joint-closure.delete-core', $c->id) }}" method="POST"
                                                                      onsubmit="return confirm('Hapus baris sambungan ini?');" class="d-inline">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-sm btn-outline-danger p-0 px-1.5 py-0.5 rounded" title="Hapus baris core">
                                                                        <i class="bi bi-trash3"></i>
                                                                    </button>
                                                                </form>
                                                            </td>
                                                            @endif
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <div class="text-center py-3 bg-light rounded-3 border border-dashed">
                                                <p class="text-muted small mb-2">Belum ada baris sambungan core yang dicatat untuk closure ini.</p>
                                                @if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'teknis', 'helpdesk']))
                                                <button type="button" class="btn btn-xs btn-primary rounded-pill px-3 open-add-core-btn"
                                                        data-jc-id="{{ $jc->id }}"
                                                        data-jc-name="{{ $jc->nama_closure }}"
                                                        data-jc-cap-asal="{{ $jc->kapasitas_kabel_asal ?? 24 }}"
                                                        data-jc-cap-jumper="{{ $jc->kapasitas_kabel_jumper ?? 24 }}"
                                                        data-jc-tube-asal="{{ $jc->jumlah_tube_asal ?? 2 }}"
                                                        data-jc-tube-jumper="{{ $jc->jumlah_tube_jumper ?? 2 }}"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#addJointClosureCoreModal">
                                                    <i class="bi bi-plus-circle me-1"></i> Tambah Baris Core Pertama
                                                </button>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="tab-empty-card">
                                <div class="tab-empty-icon-circle icon-box-indigo">
                                    <i class="bi bi-diagram-3"></i>
                                </div>
                                <div class="tab-empty-title">Belum Ada Data Kabel &amp; Joint Closure</div>
                                <div class="tab-empty-desc">
                                    Catat spesifikasi kabel eksisting, kabel jumper, sambungan tube-core, core spare/sisa, dan tandai penambahan aset baru Joint Closure (JC).
                                </div>
                                @if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'teknis', 'helpdesk']))
                                <button class="btn-tab-action-pro btn-indigo-pro" data-bs-toggle="modal" data-bs-target="#tambahJointClosureModal">
                                    <i class="bi bi-plus-circle-fill"></i>
                                    <span>Tambah Joint Closure Sekarang</span>
                                </button>
                                @endif
                            </div>
                        @endif
                    </div>

                    <!-- ── SUB-VIEW: MANUVER CORE ── -->
                    <div id="penangananSubViewManuver" class="{{ $isManuverActive ? '' : 'd-none' }} {{ ($isJointingActive && $isManuverActive) ? 'mt-4 pt-3 border-top' : '' }}">
                        <div class="tab-header-banner">
                            <div class="tab-header-left">
                                <div class="tab-header-icon-box icon-box-violet">
                                    <i class="bi bi-shuffle"></i>
                                </div>
                                <div>
                                    <div class="tab-header-title">Catatan Manuver Core Fiber Optik</div>
                                    <div class="tab-header-subtitle">Mapping alokasi core sebelum dan sesudah perbaikan</div>
                                </div>
                            </div>
                            @if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'teknis', 'helpdesk']))
                            <button class="btn-tab-action-pro btn-violet-pro btn-mobile-block" data-bs-toggle="modal" data-bs-target="#addManuverModal">
                                <i class="bi bi-plus-circle"></i>
                                <span>Tambah Manuver Core</span>
                            </button>
                            @endif
                        </div>

                        @if($tiket->manuverCores->count() > 0)
                            @php
                                $groupedManuver = $tiket->manuverCores->groupBy('titik');
                            @endphp

                            <div class="row g-4">
                                @foreach($groupedManuver as $titikName => $cores)
                                    @php
                                        $sebelumList = $cores->where('tipe', 'SEBELUM');
                                        $sesudahList = $cores->where('tipe', 'SESUDAH');
                                    @endphp
                                    <div class="col-12 col-xl-6">
                                        <div class="manuver-closure-card-pro h-100">
                                            <div class="manuver-closure-header-pro">
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="badge bg-teal text-navy font-monospace fw-bold px-2 py-1">{{ $titikName }}</span>
                                                    <span class="small fw-semibold">Titik Joint Closure / Patching</span>
                                                </div>
                                                <span class="badge bg-white text-navy font-monospace">{{ $cores->count() }} Mapping Core</span>
                                            </div>

                                            <div class="card-body p-3">
                                                <div class="row g-3">
                                                    <!-- Kolom SEBELUM -->
                                                    <div class="col-12 col-md-6 border-end-md">
                                                        <div class="d-flex align-items-center justify-content-between mb-2 pb-1 border-bottom">
                                                            <span class="badge bg-secondary text-white"><i class="bi bi-clock-history me-1"></i> SEBELUM (Eksisting)</span>
                                                            <span class="text-muted small">{{ $sebelumList->count() }} item</span>
                                                        </div>

                                                        @if($sebelumList->count() > 0)
                                                            <div class="d-flex flex-column gap-2">
                                                                @foreach($sebelumList as $mPrev)
                                                                <div class="fiber-route-card-pro">
                                                                    <div class="d-flex align-items-center gap-1.5 flex-grow-1">
                                                                        <span class="text-muted">{{ $mPrev->core_asal }}</span>
                                                                        <i class="bi bi-arrow-right text-secondary mx-1"></i>
                                                                        <span class="fw-bold text-navy">{{ $mPrev->core_tujuan }}</span>
                                                                    </div>
                                                                    @if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'teknis', 'helpdesk']))
                                                                    <form action="{{ route('tiket.manuver-core.destroy', $mPrev->id) }}" method="POST"
                                                                          onsubmit="return confirm('Hapus record manuver ini?');" class="d-inline">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="btn btn-link text-danger p-0 px-1" title="Hapus">
                                                                            <i class="bi bi-trash3"></i>
                                                                        </button>
                                                                    </form>
                                                                    @endif
                                                                </div>
                                                                @endforeach
                                                            </div>
                                                        @else
                                                            <div class="p-3 bg-light rounded text-center text-muted small">
                                                                Belum ada data alokasi sebelum perbaikan.
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <!-- Kolom SESUDAH -->
                                                    <div class="col-12 col-md-6">
                                                        <div class="d-flex align-items-center justify-content-between mb-2 pb-1 border-bottom">
                                                            <span class="badge bg-success text-white"><i class="bi bi-check2-circle me-1"></i> SESUDAH (Hasil Perbaikan)</span>
                                                            <span class="text-muted small">{{ $sesudahList->count() }} item</span>
                                                        </div>

                                                        @if($sesudahList->count() > 0)
                                                            <div class="d-flex flex-column gap-2">
                                                                @foreach($sesudahList as $mNext)
                                                                <div class="fiber-route-card-pro fiber-route-card-success">
                                                                    <div class="d-flex align-items-center gap-1.5 flex-grow-1">
                                                                        <span class="text-muted">{{ $mNext->core_asal }}</span>
                                                                        <i class="bi bi-arrow-right text-success mx-1"></i>
                                                                        <span class="fw-bold text-success">{{ $mNext->core_tujuan }}</span>
                                                                    </div>
                                                                    @if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'teknis', 'helpdesk']))
                                                                    <form action="{{ route('tiket.manuver-core.destroy', $mNext->id) }}" method="POST"
                                                                          onsubmit="return confirm('Hapus record manuver ini?');" class="d-inline">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="btn btn-link text-danger p-0 px-1" title="Hapus">
                                                                            <i class="bi bi-trash3"></i>
                                                                        </button>
                                                                    </form>
                                                                    @endif
                                                                </div>
                                                                @endforeach
                                                            </div>
                                                        @else
                                                            <div class="p-3 bg-light rounded text-center text-muted small">
                                                                Belum ada data alokasi sesudah perbaikan.
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="tab-empty-card">
                                <div class="tab-empty-icon-circle icon-box-violet">
                                    <i class="bi bi-shuffle"></i>
                                </div>
                                <div class="tab-empty-title">Belum Ada Data Manuver Core</div>
                                <div class="tab-empty-desc">
                                    Catat alokasi perpindahan core kabel optik (contoh: <code>Tube 2 Core 1 &rarr; Tube 2 Core 1</code>) pada titik perbaikan.
                                </div>
                                @if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'teknis', 'helpdesk']))
                                <button class="btn-tab-action-pro btn-violet-pro" data-bs-toggle="modal" data-bs-target="#addManuverModal">
                                    <i class="bi bi-plus-circle"></i>
                                    <span>Tambah Manuver Sekarang</span>
                                </button>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
                @endif {{-- isBackbone: Penanganan Core & JC --}}

