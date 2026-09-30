<div class="container-fluid px-0">

    <!-- ── BREADCRUMB (Desktop only to save vertical space on mobile) ── -->
    <div class="d-none d-md-block mb-3">
        <x-breadcrumb :items="[
            'Daftar Tiket Gangguan' => route('tiket.index'),
            $tiket->no_tiket => null
        ]" />
    </div>

    <!-- ── ACTIVE STOP CLOCK ALERT BANNER ── -->
    @if($tiket->is_stop_clock && $tiket->activeStopClock)
    <div class="alert alert-warning border-2 border-warning shadow-sm rounded-xl p-3 p-md-3.5 mb-3 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle bg-warning bg-opacity-25 p-3 text-warning-emphasis d-flex align-items-center justify-content-center flex-shrink-0" style="width: 46px; height: 46px;">
                <i class="bi bi-pause-circle-fill fs-3 text-warning"></i>
            </div>
            <div>
                <h6 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
                    <span class="badge bg-warning text-dark font-monospace">SLA PAUSED</span>
                    <span>Tiket Sedang Dalam Status Stop Clock</span>
                </h6>
                <div class="small text-muted">
                    <strong>Alasan:</strong> <span class="badge bg-secondary bg-opacity-25 text-dark">{{ $tiket->activeStopClock->reason_label }}</span> &bull;
                    <strong>Keterangan:</strong> {{ $tiket->activeStopClock->notes ?: '-' }} &bull;
                    <strong>Sejak:</strong> {{ $tiket->activeStopClock->stopped_at->format('d/m/Y H:i') }} ({{ $tiket->activeStopClock->stopped_at->diffForHumans() }})
                </div>
            </div>
        </div>
        @if(auth()->user()->hasRole(['admin', 'helpdesk', 'teknis']))
        <form action="{{ route('tiket.stop-clock.stop', $tiket->id) }}" method="POST" class="flex-shrink-0 m-0" onsubmit="return confirm('Lanjutkan perhitungan SLA (Resume Clock)? Durasi jeda akan diakumulasikan.');">
            @csrf
            <button type="submit" class="btn btn-success btn-sm px-3 py-2 fw-semibold shadow-xs d-inline-flex align-items-center gap-1.5">
                <i class="bi bi-play-circle-fill"></i>
                <span>Resume Clock (Lanjutkan SLA)</span>
            </button>
        </form>
        @endif
    </div>
    @endif

    <!-- ── PENDING VERIFIKASI CALLOUT BANNER ── -->
    @if($tiket->status === 'PENDING_VERIFIKASI')
    <div class="verifikasi-banner d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3 min-w-0">
            <div class="verifikasi-icon-box">
                <i class="bi bi-hourglass-split"></i>
            </div>
            <div class="min-w-0">
                <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                    <span class="badge" style="background: rgba(37, 99, 235, 0.15); color: #1e40af; border: 1px solid rgba(37, 99, 235, 0.3); font-size: 0.65rem; font-weight: 700; letter-spacing: 0.4px; padding: 0.2rem 0.5rem; border-radius: 6px;">
                        MENUNGGU VERIFIKASI NOC
                    </span>
                    <span class="fw-bold" style="font-size: 0.92rem; color: #0f172a; letter-spacing: -0.2px;">
                        Pekerjaan Telah Diselesaikan oleh Teknisi
                    </span>
                </div>
                <div style="font-size: 0.76rem; color: #475569;">
                    Teknisi <strong class="text-navy">{{ $tiket->resolver?->name ?? 'Teknisi' }}</strong> telah menyelesaikan pekerjaan lapangan pada <span class="fw-semibold text-dark">{{ $tiket->resolved_at ? $tiket->resolved_at->format('d/m/Y H:i') : '-' }}</span>.
                </div>
                @if($tiket->closing_notes_teknisi)
                <div class="mt-2 p-2 rounded-lg" style="background: rgba(255,255,255,0.85); border: 1px dashed #bfdbfe; font-size: 0.74rem; color: #1e293b;">
                    <i class="bi bi-chat-quote-fill text-primary me-1"></i>
                    <strong class="text-navy">Catatan Teknisi:</strong> <span>{{ $tiket->closing_notes_teknisi }}</span>
                </div>
                @endif
            </div>
        </div>
        @if(auth()->user()->hasRole(['admin', 'helpdesk']))
        <div class="verifikasi-actions d-flex align-items-center gap-2 flex-shrink-0">
            <button type="button" class="btn-verifikasi-reject" data-bs-toggle="modal" data-bs-target="#rejectClosingAwalModal">
                <i class="bi bi-x-circle"></i>
                <span>Reject (Kembalikan)</span>
            </button>
            <button type="button" class="btn-verifikasi-approve" data-bs-toggle="modal" data-bs-target="#closeTiketModal">
                <i class="bi bi-shield-check"></i>
                <span>Verifikasi & Close Tiket</span>
            </button>
        </div>
        @else
        <div class="flex-shrink-0">
            <span class="badge rounded-pill px-3 py-2" style="background: rgba(37, 99, 235, 0.1); color: #1d4ed8; font-size: 0.76rem; font-weight: 600; border: 1px solid rgba(37, 99, 235, 0.25);">
                <i class="bi bi-clock-history me-1"></i> Menunggu Verifikasi Helpdesk
            </span>
        </div>
        @endif
    </div>
    @endif

    <!-- ── TOP HEADER HERO BANNER (BRAND BLUE FULL-WIDTH) ── -->
    <div class="card border-0 shadow-lg rounded-xl mb-2 text-white overflow-visible" style="background: linear-gradient(135deg, #07152b 0%, #0c2147 50%, #102d66 100%); border: 1px solid rgba(255, 255, 255, 0.15); box-shadow: 0 10px 30px rgba(7, 21, 43, 0.35); position: relative; z-index: 1; overflow: visible !important;">
        <div class="card-body p-3 p-md-4 overflow-visible" style="overflow: visible !important;">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2 gap-md-3">
                <div class="min-w-0 w-100 w-md-auto">
                    {{-- Baris 1: ID + Tipe + Status --}}
                    <div class="d-flex align-items-center flex-wrap gap-1 gap-md-2 mb-1">
                        <span class="d-inline-flex align-items-center gap-1 text-white font-monospace px-2 px-md-3 py-1 rounded-pill" style="background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(8px); border: 1px solid rgba(255, 255, 255, 0.25); font-size:0.73rem; font-weight:700; letter-spacing:0.4px;">
                            <i class="bi bi-ticket-perforated-fill text-info" style="font-size: 0.82rem;"></i>
                            <span>{{ $tiket->no_tiket }}</span>
                        </span>

                        @if($tiket->isBroadband())
                            <span class="badge text-white px-2 py-1 rounded-pill fw-bold shadow-xs" style="background-color: #4f46e5; border: 1px solid rgba(255,255,255,0.3); font-size:0.68rem;">
                                <i class="bi bi-router-fill me-1"></i> BROADBAND
                            </span>
                        @else
                            <span class="badge bg-primary bg-opacity-75 text-white px-2 py-1 rounded-pill fw-bold shadow-xs" style="border: 1px solid rgba(255,255,255,0.3); font-size:0.68rem;">
                                <i class="bi bi-diagram-3-fill me-1"></i> BACKBONE
                            </span>
                        @endif

                        @if($tiket->status === 'OPEN')
                            <span class="badge bg-info bg-opacity-25 text-info border border-info border-opacity-50 rounded-pill px-2 py-1 fw-bold" id="headerStatusBadge" style="font-size:0.68rem;">
                                <i class="bi bi-exclamation-circle-fill me-1"></i> OPEN
                            </span>
                        @elseif($tiket->status === 'PROSES')
                            <span class="badge bg-warning bg-opacity-25 text-warning border border-warning border-opacity-50 rounded-pill px-2 py-1 fw-bold" id="headerStatusBadge" style="font-size:0.68rem;">
                                <i class="bi bi-arrow-repeat me-1"></i> PROSES
                            </span>
                        @elseif($tiket->status === 'PENDING_VERIFIKASI')
                            <span class="badge rounded-pill px-2 py-1 fw-bold" id="headerStatusBadge" style="background-color: rgba(59, 130, 246, 0.3) !important; color: #93c5fd !important; border: 1px solid rgba(59, 130, 246, 0.6) !important; font-size:0.68rem;">
                                <i class="bi bi-hourglass-split me-1"></i> NOC VERIF
                            </span>
                        @else
                            <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-50 rounded-pill px-2 py-1 fw-bold" id="headerStatusBadge" style="font-size:0.68rem;">
                                <i class="bi bi-check-circle-fill me-1"></i> CLOSE
                            </span>
                        @endif

                        @if($tiket->is_stop_clock)
                            <span class="badge bg-danger text-white border border-white border-opacity-50 rounded-pill px-2 py-1 fw-bold" style="font-size:0.68rem;">
                                <i class="bi bi-pause-fill me-1"></i> STOP CLOCK
                            </span>
                        @endif

                        @if($tiket->sla_status === 'TEPAT')
                            <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-50 rounded-pill px-2 py-1 fw-bold" style="font-size:0.68rem;">
                                <i class="bi bi-shield-check me-1"></i> SLA TEPAT
                            </span>
                        @elseif($tiket->sla_status === 'LEBIH')
                            <span class="badge bg-danger bg-opacity-25 text-danger border border-danger border-opacity-50 rounded-pill px-2 py-1 fw-bold" style="font-size:0.68rem;">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i> SLA LEBIH
                            </span>
                        @endif

                    </div>
                    {{-- Baris 2: Tim Teknisi / PIC & Status Tugas Anda --}}
                    <div class="d-flex align-items-center flex-wrap gap-1 mb-2">
                        @if(auth()->check() && $tiket->isUserAssigned(auth()->user()))
                            <span class="badge rounded-pill px-2.5 py-1 fw-bold text-white d-inline-flex align-items-center gap-1 shadow-xs font-monospace" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); font-size:0.68rem; border: 1px solid rgba(255,255,255,0.3); box-shadow: 0 2px 6px rgba(16, 185, 129, 0.4);">
                                <i class="bi bi-person-check-fill" style="font-size:0.75rem;"></i>
                                <span>Tugas Anda {{ (int)$tiket->assigned_lead_id === (int)auth()->id() ? '(PIC)' : '' }}</span>
                            </span>
                        @endif

                        @if($tiket->assigned_lead_id)
                            <span class="badge bg-primary bg-opacity-35 text-white border border-primary border-opacity-60 rounded-pill px-2 py-1 fw-bold d-inline-flex align-items-center gap-1 shadow-xs" title="Leader / PIC Utama Lapangan" style="font-size:0.68rem;">
                                <i class="bi bi-person-badge-fill text-info" style="font-size:0.7rem;"></i>
                                <span>PIC: {{ $tiket->assignedLead?->name }}</span>
                            </span>
                            @if($tiket->assigned_team_users && $tiket->assigned_team_users->count() > 0)
                                @foreach($tiket->assigned_team_users as $tUser)
                                    @if($tUser->id !== $tiket->assigned_lead_id)
                                        <span class="badge rounded-pill px-2 py-1 fw-semibold text-white border border-info border-opacity-40 d-inline-flex align-items-center gap-1 shadow-xs" style="background: rgba(14, 165, 233, 0.22); backdrop-filter: blur(4px); font-size:0.68rem;" title="Anggota Tim Lapangan">
                                            <i class="bi bi-person-fill text-info" style="font-size:0.7rem;"></i>
                                            <span>{{ $tUser->name }}</span>
                                        </span>
                                    @endif
                                @endforeach
                            @endif
                        @else
                            <span class="badge bg-secondary bg-opacity-35 text-white border border-white border-opacity-25 rounded-pill px-2 py-1 fw-semibold" style="font-size:0.68rem;">
                                <i class="bi bi-person-dash me-1"></i> Belum Ditugaskan
                            </span>
                        @endif
                    </div>
                    <h1 class="fw-bold text-white mb-0 lh-sm" style="font-size: clamp(1rem, 4.5vw, 1.3rem); letter-spacing: -0.2px;">{{ $tiket->status_link_impact }}</h1>
                </div>

                <!-- Action Buttons -->
                <div class="tiket-hero-actions w-100 w-md-auto justify-content-start justify-content-md-end">
                    <a href="{{ route('tiket.index') }}" class="btn-tiket-hero btn-tiket-hero-ghost">
                        <i class="bi bi-arrow-left"></i>
                        <span>Kembali</span>
                    </a>

                    <!-- Single Unified Action Dropdown -->
                    <div class="dropdown position-relative d-inline-block" style="z-index: 10;">
                        <button type="button" class="btn-tiket-hero btn-tiket-hero-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-grid-fill"></i>
                            <span>Menu Aksi</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end mt-1 tiket-hero-dropdown-menu">
                            
                            <!-- 0. Dispatch / Penugasan Teknisi (Manager Teknis & Admin) -->
                            @if(auth()->user()->hasRole(['admin', 'manager_teknisi']) && $tiket->status !== 'CLOSE')
                            <li>
                                <button type="button" class="tiket-hero-dropdown-item text-info"
                                        data-bs-toggle="modal" data-bs-target="#assignTeknisiModal">
                                    <div class="tiket-dropdown-icon-box bg-info-subtle text-info">
                                        <i class="bi bi-person-gear"></i>
                                    </div>
                                    <span>Tugaskan Teknisi (Dispatch)</span>
                                </button>
                            </li>
                            <li><hr class="dropdown-divider my-1"></li>
                            @endif

                            <!-- 2. SLA & Shift Actions -->
                            @if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'helpdesk', 'teknis', 'teknisi']))
                                @if(!$tiket->is_stop_clock)
                                <li>
                                    <button type="button" class="tiket-hero-dropdown-item"
                                            data-bs-toggle="modal" data-bs-target="#startStopClockModal">
                                        <div class="tiket-dropdown-icon-box bg-warning-subtle text-warning">
                                            <i class="bi bi-pause-circle-fill"></i>
                                        </div>
                                        <span>Stop Clock (Jeda SLA)</span>
                                    </button>
                                </li>
                                @else
                                <li>
                                    <form action="{{ route('tiket.stop-clock.stop', $tiket->id) }}" method="POST" class="m-0 w-100" onsubmit="return confirm('Lanjutkan perhitungan SLA (Resume Clock)? Durasi jeda akan diakumulasikan.');">
                                        @csrf
                                        <button type="submit" class="tiket-hero-dropdown-item text-success">
                                            <div class="tiket-dropdown-icon-box bg-success-subtle text-success">
                                                <i class="bi bi-play-circle-fill"></i>
                                            </div>
                                            <span>Resume Clock (Lanjut SLA)</span>
                                        </button>
                                    </form>
                                </li>
                                @endif

                                <li>
                                    <button type="button" class="tiket-hero-dropdown-item"
                                            data-bs-toggle="modal" data-bs-target="#handoverShiftModal">
                                        <div class="tiket-dropdown-icon-box" style="background-color: rgba(139, 92, 246, 0.12); color: #8b5cf6;">
                                            <i class="bi bi-arrow-left-right"></i>
                                        </div>
                                        <span>Oper Shift (Handover)</span>
                                    </button>
                                </li>
                            @endif

                            <!-- 3. Edit & Share Actions -->
                            @if(auth()->user()->hasRole(['admin', 'helpdesk']))
                                @if($tiket->status === 'OPEN')
                                <li>
                                    <a href="{{ route('tiket.edit', $tiket->id) }}" class="tiket-hero-dropdown-item">
                                        <div class="tiket-dropdown-icon-box bg-warning-subtle text-warning">
                                            <i class="bi bi-pencil-square"></i>
                                        </div>
                                        <span>Edit Data Tiket</span>
                                    </a>
                                </li>
                                @endif

                                <li>
                                    <button type="button" class="tiket-hero-dropdown-item" id="btnCopyWaBroadcast">
                                        <div class="tiket-dropdown-icon-box bg-success-subtle text-success">
                                            <i class="bi bi-whatsapp"></i>
                                        </div>
                                        <span>Salin Info WhatsApp</span>
                                    </button>
                                </li>
                            @endif

                            <!-- 4. Export Section -->
                            <li><hr class="dropdown-divider my-1"></li>
                            <li class="tiket-dropdown-header-label">Export Dokumen</li>
                            <li>
                                <a class="tiket-hero-dropdown-item no-loader" data-no-loader="true" href="{{ route('reports.export.tiket.pdf', ['tiket' => $tiket->id, 'type' => 'internal']) }}" title="Laporan internal lengkap beserta mapping core dan kronologis">
                                    <div class="tiket-dropdown-icon-box bg-danger-subtle text-danger">
                                        <i class="bi bi-file-earmark-pdf-fill"></i>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span>PDF Internal (Lengkap)</span>
                                        <small class="text-muted" style="font-size: 0.68rem;">Data teknis, mapping core & kronologis</small>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a class="tiket-hero-dropdown-item no-loader" data-no-loader="true" href="{{ route('reports.export.tiket.pdf', ['tiket' => $tiket->id, 'type' => 'customer']) }}" title="Berita Acara resmi untuk diberikan ke pelanggan / mitra">
                                    <div class="tiket-dropdown-icon-box bg-primary-subtle text-primary">
                                        <i class="bi bi-file-earmark-person-fill"></i>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span>PDF Pelanggan (Customer)</span>
                                        <small class="text-muted" style="font-size: 0.68rem;">Berita Acara resmi, akar masalah & foto</small>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a class="tiket-hero-dropdown-item no-loader" data-no-loader="true" href="{{ route('reports.export.excel', ['search' => $tiket->no_tiket]) }}">
                                    <div class="tiket-dropdown-icon-box bg-success-subtle text-success">
                                        <i class="bi bi-file-earmark-excel-fill"></i>
                                    </div>
                                    <span>Export Data Excel</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ── BROADBAND CUSTOMER PROFILE CARD (KHUSUS BROADBAND) ── -->
    @if($tiket->isBroadband())
    <div class="card border-0 shadow-sm rounded-xl mb-2 overflow-hidden" style="border: 1.5px solid #c7d2fe !important; background: linear-gradient(135deg, #ffffff 0%, #f5f3ff 100%);">
        <div class="card-header py-2.5 px-3 px-md-3.5 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2" style="background: rgba(79, 70, 229, 0.06);">
            <div class="d-flex align-items-center gap-2">
                <span class="badge text-white px-2 py-1 rounded-pill" style="background-color: #4f46e5;">
                    <i class="bi bi-person-lines-fill me-1"></i> DATA PELANGGAN BROADBAND
                </span>
                <span class="fw-bold text-navy font-monospace small">CID: {{ $tiket->id_pelanggan ?: '-' }}</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                @if($tiket->customer_whatsapp_url)
                <a href="{{ $tiket->customer_whatsapp_url }}" target="_blank" class="btn btn-sm btn-success px-2.5 py-1 rounded-pill shadow-xs d-inline-flex align-items-center gap-1.5 fw-bold" style="font-size: 0.75rem;">
                    <i class="bi bi-whatsapp"></i> Chat WhatsApp
                </a>
                @endif
                @if($tiket->google_maps_url)
                <a href="{{ $tiket->google_maps_url }}" target="_blank" class="btn btn-sm btn-primary px-2.5 py-1 rounded-pill shadow-xs d-inline-flex align-items-center gap-1.5 fw-bold" style="font-size: 0.75rem;">
                    <i class="bi bi-geo-alt-fill"></i> Navigasi GPS
                </a>
                @endif
            </div>
        </div>
        <div class="card-body p-3 p-md-3.5">
            <div class="row g-3">
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="text-muted small mb-0.5"><i class="bi bi-person text-primary"></i> Nama Pelanggan:</div>
                    <div class="fw-bold text-navy fs-6">{{ $tiket->nama_pelanggan ?: '-' }}</div>
                    @if($tiket->no_kontak_pelanggan)
                    <div class="text-secondary small mt-0.5">
                        <i class="bi bi-telephone text-success"></i> {{ $tiket->no_kontak_pelanggan }}
                    </div>
                    @endif
                </div>

                <div class="col-12 col-md-6 col-lg-4">
                    <div class="text-muted small mb-0.5"><i class="bi bi-hdd-rack text-primary"></i> Titik ODP / Bandwidth / POP:</div>
                    <div class="fw-semibold text-dark">
                        <span class="badge bg-primary-subtle text-primary">{{ $tiket->titik_odp ?: 'ODP: -' }}</span>
                        <span class="badge bg-secondary-subtle text-secondary">{{ $nominalBandwith ? $nominalBandwith . ' Mbps' : ($tiket->kode_bandwith ?: 'BW: -') }}</span>
                        <span class="badge bg-light text-dark border">{{ $namaPop ?: ($tiket->kode_pop ?: 'POP: -') }}</span>
                        @if($tiket->wilayah)
                        <span class="badge bg-info-subtle text-info"><i class="bi bi-geo-alt-fill"></i> {{ $tiket->wilayah }}</span>
                        @endif
                    </div>
                    @if($tiket->sn_ont)
                    <div class="text-muted small mt-1 font-monospace">
                        <i class="bi bi-cpu text-primary"></i> SN ONT: {{ $tiket->sn_ont }}
                    </div>
                    @endif
                </div>

                <div class="col-12 col-md-12 col-lg-4">
                    <div class="text-muted small mb-0.5"><i class="bi bi-activity text-danger"></i> Kendala & Redaman:</div>
                    <div class="fw-bold text-danger small mb-1">
                        {{ $tiket->jenis_kendala_broadband ?: ($tiket->deskripsi ?: 'Gangguan Layanan Broadband') }}
                    </div>
                    <div class="d-flex align-items-center gap-2 text-muted small">
                        <span>Awal: <strong class="text-dark">{{ $tiket->redaman_sebelum ?: '-' }}</strong></span>
                        <span>&rarr;</span>
                        <span>Akhir: <strong class="text-success">{{ $tiket->redaman_sesudah ?: '-' }}</strong></span>
                    </div>
                </div>

                @if($tiket->alamat_pelanggan)
                <div class="col-12 border-top pt-2 mt-2">
                    <div class="text-muted small"><i class="bi bi-geo-alt text-danger"></i> Alamat Pemasangan:</div>
                    <div class="text-dark small mt-0.5">{{ $tiket->alamat_pelanggan }}</div>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- ── SUMMARY INFO CARD ── -->
    <div class="card border-0 shadow-sm rounded-xl mb-2 bg-white overflow-hidden">
        {{-- Toggle Header (mobile only) --}}
        <div class="d-flex d-md-none align-items-center justify-content-between px-3 py-1.5 border-bottom summary-toggle-header"
             role="button"
             data-bs-toggle="collapse"
             data-bs-target="#summaryInfoBody"
             aria-expanded="false"
             aria-controls="summaryInfoBody"
             style="cursor:pointer; user-select:none; background:rgba(248,250,252,0.85);">
            <div class="d-flex align-items-center gap-1.5">
                <i class="bi bi-info-circle-fill text-primary" style="font-size:0.8rem;"></i>
                <span class="fw-semibold text-navy" style="font-size:0.78rem;">Info Tiket</span>
                <span class="badge bg-primary-subtle text-primary rounded-pill" style="font-size:0.58rem;">{{ $tiket->isBroadband() ? ($namaPop ?: $tiket->kode_pop) : $tiket->backbone_segment }}</span>
            </div>
            <div class="d-flex align-items-center gap-1.5">
                <span class="text-muted font-monospace" style="font-size:0.65rem;">{{ $tiket->tanggal_open->format('d M H:i') }}</span>
                <i class="bi bi-chevron-down summary-toggle-icon text-muted" style="font-size:0.7rem; transition: transform 0.2s;"></i>
            </div>
        </div>
        {{-- Card Body: collapsible on mobile, always visible on desktop --}}
        <div class="collapse d-md-block" id="summaryInfoBody">
        <div class="card-body p-3 p-md-3.5">
            <!-- 4 Metric Micro-Tiles Grid -->
            <div class="row g-2 g-md-2.5 mb-2.5">
                <!-- 1. Segment Backbone / POP -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="tiket-metric-tile">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <div class="tiket-metric-icon bg-teal-subtle text-teal">
                                <i class="bi bi-diagram-3-fill"></i>
                            </div>
                            <span class="tiket-metric-label">{{ $tiket->isBroadband() ? 'POP / Area Layanan' : 'Segment Backbone' }}</span>
                        </div>
                        @if($tiket->isBroadband())
                        <div class="tiket-metric-value text-truncate" title="{{ $namaPop ?: $tiket->kode_pop }}">
                            {{ $namaPop ?: ($tiket->kode_pop ?: '-') }}
                        </div>
                        @else
                        <div class="tiket-metric-value text-truncate" title="{{ $tiket->backbone_segment }}">
                            {{ $tiket->backbone_segment }}
                        </div>
                        @endif
                    </div>
                </div>

                <!-- 2. Waktu Open -->
                <div class="col-6 col-sm-6 col-lg-3">
                    <div class="tiket-metric-tile">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <div class="tiket-metric-icon bg-primary-subtle text-primary">
                                <i class="bi bi-clock-history"></i>
                            </div>
                            <span class="tiket-metric-label">Waktu Open</span>
                        </div>
                        <div class="tiket-metric-value">
                            {{ $tiket->tanggal_open->format('d M, H:i') }} <span class="tiket-metric-unit">WIB</span>
                        </div>
                        <div class="tiket-metric-sub">
                            {{ $tiket->tanggal_open->diffForHumans() }}
                        </div>
                    </div>
                </div>

                <!-- 3. Waktu Close -->
                <div class="col-6 col-sm-6 col-lg-3">
                    <div class="tiket-metric-tile">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <div class="tiket-metric-icon {{ $tiket->tanggal_close ? 'bg-success-subtle text-success' : 'bg-light text-muted' }}">
                                <i class="bi {{ $tiket->tanggal_close ? 'bi-check2-circle' : 'bi-dash-circle' }}"></i>
                            </div>
                            <span class="tiket-metric-label">Waktu Close</span>
                        </div>
                        @if($tiket->tanggal_close)
                            <div class="tiket-metric-value text-success">
                                {{ $tiket->tanggal_close->format('d M, H:i') }} <span class="tiket-metric-unit text-success">WIB</span>
                            </div>
                            <div class="tiket-metric-sub text-success">
                                {{ $tiket->tanggal_close->diffForHumans($tiket->tanggal_open, true) }} durasi
                            </div>
                        @else
                            <div class="tiket-metric-value text-muted" style="font-size:0.8rem; font-style:italic; font-weight:500;">
                                Belum ditutup
                            </div>
                            <div class="tiket-metric-sub text-muted">
                                Tiket aktif
                            </div>
                        @endif
                    </div>
                </div>

                <!-- 4. Target SLA & MTTR -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="tiket-metric-tile">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <div class="tiket-metric-icon bg-danger-subtle text-danger">
                                <i class="bi bi-stopwatch-fill"></i>
                            </div>
                            <span class="tiket-metric-label">Target SLA & MTTR</span>
                        </div>
                        <div class="d-flex align-items-baseline justify-content-between">
                            <div class="tiket-metric-value">
                                Target: <span class="text-muted fw-normal">{{ $tiket->formatted_sla_target }}</span>
                            </div>
                            <div class="tiket-metric-sub fw-bold {{ $tiket->status === 'CLOSE' ? ($tiket->sla_status === 'LEBIH' ? 'text-danger' : 'text-success') : 'text-muted' }}">
                                @if($tiket->status === 'CLOSE')
                                    MTTR: {{ $tiket->formatted_mttr }}
                                @else
                                    MTTR: Berjalan...
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @php
                $fotoBuktiMasalah = $tiket->dokumentasis->where('kategori', 'Bukti Masalah');
                $hasDesc = !empty($tiket->deskripsi);
                $hasFoto = $fotoBuktiMasalah->count() > 0;
            @endphp

            @if($hasDesc || $hasFoto)
            <div class="tiket-info-strip mb-3">

                @if($hasDesc)
                {{-- DESKRIPSI --}}
                <div class="tiket-info-strip-desc {{ $hasFoto ? '' : 'w-100' }}">
                    <div class="tiket-info-strip-label">
                        <i class="bi bi-chat-left-text"></i>
                        <span>Deskripsi Gangguan &amp; Catatan Awal</span>
                    </div>
                    <div class="tiket-desc-text">{{ $tiket->deskripsi }}</div>
                </div>
                @endif

                @if($hasFoto)
                {{-- FOTO BUKTI --}}
                <div class="tiket-info-strip-foto {{ $hasDesc ? '' : 'w-100' }}">
                    <div class="tiket-info-strip-label tiket-info-strip-label--red">
                        <i class="bi bi-camera-fill"></i>
                        <span>Foto Bukti Awal</span>
                        <span class="badge rounded-pill ms-1" style="background:rgba(239,68,68,0.12);color:#dc2626;font-size:0.62rem;border:1px solid rgba(239,68,68,0.25);padding:1px 7px;">
                            {{ $fotoBuktiMasalah->count() }} Foto
                        </span>
                    </div>
                    <div class="tiket-foto-bukti-grid">
                        @foreach($fotoBuktiMasalah as $fb)
                        <div class="tiket-foto-thumb"
                             onclick="zoomPhoto('{{ asset('storage/' . $fb->file_path) }}', 'Bukti Masalah #{{ $tiket->no_tiket }}')"
                             title="{{ $fb->formatted_timestamp }}">
                            <img src="{{ asset('storage/' . $fb->file_path) }}"
                                 alt="Bukti"
                                 loading="lazy"
                                 onerror="this.onerror=null;this.src='{{ asset($fb->file_path) }}';">
                            <div class="tiket-foto-thumb-overlay"><i class="bi bi-arrows-fullscreen"></i></div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

            </div>
            @endif


            <!-- Meta Footer Strip -->
            <div class="tiket-meta-footer">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <i class="bi bi-person-fill text-primary"></i>
                    <span class="text-muted">Dibuat:</span>
                    <strong class="text-navy">{{ $tiket->creator?->name ?? 'Sistem' }}</strong>
                    <span class="badge bg-light text-muted border px-2 py-0.5 rounded" style="font-size:0.65rem;">{{ $tiket->creator?->role_short ?? '-' }}</span>
                </div>
                @if($tiket->closed_by)
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <i class="bi bi-person-check-fill text-success"></i>
                    <span class="text-muted">Ditutup:</span>
                    <strong class="text-navy">{{ $tiket->closer?->name ?? 'Sistem' }}</strong>
                    <span class="badge bg-light text-muted border px-2 py-0.5 rounded" style="font-size:0.65rem;">{{ $tiket->closer?->role_short ?? '-' }}</span>
                </div>
                @endif
            </div>
        </div>
        </div>{{-- end collapse --}}
    </div>

    <!-- ── VISUAL SLA TIMELINE STEPPER (POINT 5) ── -->
    @php
        $slaStages = $tiket->sla_timeline_stages;
    @endphp
    <div class="card border-0 shadow-sm rounded-xl mb-3 bg-white overflow-hidden">
        <div class="card-body p-3 p-md-3.5">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3 pb-2 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-primary bg-opacity-10 p-1.5 text-primary d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                        <i class="bi bi-bezier2"></i>
                    </div>
                    <div>
                        <span class="fw-bold text-navy small">Garis Alur SLA &amp; Siklus Hidup Tiket</span>
                        <span class="text-muted small ms-1 d-none d-sm-inline">(Open &rarr; Respon &rarr; Stop Clock &rarr; Closing Lapangan &rarr; Closing NOC)</span>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge {{ $tiket->status === 'CLOSE' ? ($tiket->sla_status === 'LEBIH' ? 'bg-danger text-white' : 'bg-success text-white') : 'bg-primary text-white' }} px-2.5 py-1 rounded-pill small fw-bold">
                        @if($tiket->status === 'CLOSE')
                            {{ $tiket->sla_status === 'LEBIH' ? 'SLA OVERDUE (' . $tiket->formatted_mttr . ')' : 'SLA ACHIEVED (' . $tiket->formatted_mttr . ')' }}
                        @else
                            Target SLA: {{ $tiket->formatted_sla_target }} (Aktif)
                        @endif
                    </span>
                </div>
            </div>

            <!-- Horizontal Stepper Progress -->
            <div class="sla-stepper-wrapper py-2">
                <div class="sla-stepper-track">
                    @foreach($slaStages as $index => $stage)
                        @php
                            $isCompleted = (bool) ($stage['is_completed'] ?? false);
                            $isCurrent = (bool) ($stage['is_current'] ?? false);
                            
                            $nodeColorClass = 'step-pending';
                            if ($isCompleted) {
                                $nodeColorClass = 'step-completed';
                            } elseif ($isCurrent) {
                                $nodeColorClass = ($stage['key'] === 'STOP_CLOCK' ? 'step-paused' : 'step-active');
                            }
                        @endphp
                        <div class="sla-step-item {{ $nodeColorClass }}">
                            <div class="sla-step-node-container">
                                <div class="sla-step-node">
                                    @if($isCompleted)
                                        <i class="bi bi-check-lg"></i>
                                    @elseif($isCurrent)
                                        @if($stage['key'] === 'STOP_CLOCK')
                                            <i class="bi bi-pause-fill"></i>
                                        @else
                                            <span class="spinner-grow spinner-grow-sm text-white" style="width: 10px; height: 10px;" role="status"></span>
                                        @endif
                                    @else
                                        <span class="step-num">{{ $index + 1 }}</span>
                                    @endif
                                </div>
                                @if(!$loop->last)
                                    <div class="sla-step-line {{ $isCompleted ? 'line-completed' : ($isCurrent ? 'line-active' : '') }}"></div>
                                @endif
                            </div>
                            <div class="sla-step-content mt-2">
                                <div class="sla-step-title fw-bold text-navy">{{ $stage['title'] }}</div>
                                <div class="sla-step-timestamp text-muted">
                                    {{ $stage['timestamp'] ?: '-' }}
                                </div>
                                <div class="sla-step-badge mt-1">
                                    @if(!empty($stage['badge']))
                                        <span class="badge {{ $isCompleted ? 'bg-success bg-opacity-15 text-success' : ($isCurrent ? 'bg-primary bg-opacity-15 text-primary' : 'bg-light text-muted') }} rounded-pill px-2 py-0.5">
                                            {{ $stage['badge'] }}
                                        </span>
                                    @elseif($isCompleted)
                                        <span class="badge bg-success bg-opacity-15 text-success rounded-pill px-2 py-0.5">
                                            Selesai
                                        </span>
                                    @elseif($isCurrent)
                                        <span class="badge bg-primary bg-opacity-15 text-primary rounded-pill px-2 py-0.5">
                                            Sedang Proses
                                        </span>
                                    @else
                                        <span class="badge bg-light text-muted rounded-pill px-2 py-0.5">
                                            Menunggu
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- ── 30-MINUTE FIELD REPORT INTERVAL & STOP CLOCK REMINDER WIDGET (POINT 6 & 9) ── -->
    @if($tiket->status !== 'CLOSE')
        @php
            $fieldStatus = $tiket->field_update_status;
            $minsSinceLast = $tiket->minutes_since_last_update;
            $avgInterval = $tiket->average_report_interval_minutes;
            $lastKronologis = $tiket->last_kronologis;
            $showIntervalAlert = in_array($fieldStatus, ['OVERDUE', 'WARNING']);
            $showStopClockAlert = $tiket->is_stop_clock && $tiket->activeStopClock;
        @endphp

        @if($showIntervalAlert || $showStopClockAlert)
        <div id="fieldReportIntervalBanner" class="card border-0 mb-2 overflow-hidden shadow-sm" style="{{ $fieldStatus === 'OVERDUE' ? 'background: linear-gradient(135deg, #fff5f5 0%, #fef2f2 50%, #fee2e2 100%); border: 1px solid #fca5a5 !important; border-left: 4px solid #ef4444 !important; border-radius: 12px; box-shadow: 0 3px 12px -2px rgba(239, 68, 68, 0.1);' : 'background: linear-gradient(135deg, #fffdf0 0%, #fefce8 50%, #fef3c7 100%); border: 1px solid #fde047 !important; border-left: 4px solid #f59e0b !important; border-radius: 12px; box-shadow: 0 3px 12px -2px rgba(245, 158, 11, 0.1);' }}">
            <div class="card-body p-2">
                @if($showIntervalAlert)
                <div id="fieldIntervalStatusRow">
                    @if($fieldStatus === 'OVERDUE')
                    {{-- ── Baris 1: Header Alert (Icon + Judul + Info Waktu) ── --}}
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="d-flex align-items-center justify-content-center flex-shrink-0" style="width:34px;height:34px;min-width:34px;background:linear-gradient(135deg,#ef4444 0%,#dc2626 100%);border-radius:10px;box-shadow:0 2px 6px rgba(239,68,68,0.25);">
                            <i class="bi bi-exclamation-triangle-fill text-white" style="font-size:0.95rem;"></i>
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                <span class="badge bg-danger text-white rounded-pill px-2 py-0.5" style="font-size:0.6rem;letter-spacing:0.2px;font-weight:700;">
                                    <span class="spinner-grow spinner-grow-sm me-0.5" style="width:0.3rem;height:0.3rem;" role="status"></span>
                                    OVERDUE &gt; 30 MENIT
                                </span>
                                <span class="fw-bold text-danger-emphasis" style="font-size:0.82rem;">Wajib Kirim Update!</span>
                            </div>
                            <div class="text-muted" style="font-size:0.68rem;line-height:1.3;margin-top:2px;">
                                Terakhir update <strong class="text-danger">{{ $tiket->minutes_since_last_update_formatted ? $tiket->minutes_since_last_update_formatted . ' lalu' : 'belum ada' }}</strong> &bull; SOP maks. 30 menit
                            </div>
                        </div>
                    </div>

                    {{-- ── Baris 2: Metric Chips & Tombol Kirim Update ── --}}
                    <div class="d-flex align-items-center gap-1.5 flex-wrap flex-sm-nowrap">
                        <div class="d-flex align-items-center gap-1.5 px-2 py-1.5 rounded-3 flex-fill" style="background:rgba(255,255,255,0.95);border:1px solid rgba(252,165,165,0.7);min-width:90px;">
                            <i class="bi bi-clock-history text-danger opacity-75" style="font-size:0.75rem;"></i>
                            <div>
                                <div class="text-muted" style="font-size:0.55rem;font-weight:700;text-transform:uppercase;letter-spacing:0.3px;line-height:1;">Terakhir</div>
                                <div class="fw-bold text-navy font-monospace" style="font-size:0.75rem;line-height:1.2;">{{ $lastKronologis ? ($lastKronologis->timestamp ? $lastKronologis->timestamp->format('H:i') : $lastKronologis->created_at->format('H:i')) . ' WIB' : '-' }}</div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-1.5 px-2 py-1.5 rounded-3 flex-fill" style="background:rgba(255,255,255,0.95);border:1px solid rgba(252,165,165,0.7);min-width:100px;">
                            <i class="bi bi-stopwatch text-danger opacity-75" style="font-size:0.75rem;"></i>
                            <div>
                                <div class="text-muted" style="font-size:0.55rem;font-weight:700;text-transform:uppercase;letter-spacing:0.3px;line-height:1;">Rata-rata</div>
                                <div class="fw-bold text-navy text-truncate" style="font-size:0.75rem;line-height:1.2;max-width:95px;">{{ $tiket->average_report_interval_formatted }}</div>
                            </div>
                        </div>
                        @if(auth()->user()->hasRole(['admin', 'teknis', 'helpdesk']))
                        <button type="button" class="btn fw-bold text-white d-inline-flex align-items-center justify-content-center gap-1 px-2.5 py-1.5 flex-fill flex-sm-grow-0" style="background:linear-gradient(135deg,#dc2626 0%,#b91c1c 100%);border:none;border-radius:8px;font-size:0.72rem;box-shadow:0 2px 6px rgba(220,38,38,0.25);white-space:nowrap;min-height:36px;" onclick="const kTab=document.getElementById('kronologis-tab');if(kTab)kTab.click();const waInp=document.getElementById('waChatTextInput')||document.getElementById('informasi');if(waInp){waInp.focus();waInp.scrollIntoView({behavior:'instant',block:'center'});}">
                            <i class="bi bi-chat-left-dots-fill"></i>
                            <span>Kirim Update</span>
                        </button>
                        @endif
                    </div>
                    @elseif($fieldStatus === 'WARNING')
                    {{-- ── Baris 1: Header Alert Warning ── --}}
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="d-flex align-items-center justify-content-center flex-shrink-0" style="width:34px;height:34px;min-width:34px;background:linear-gradient(135deg,#f59e0b 0%,#d97706 100%);border-radius:10px;box-shadow:0 2px 6px rgba(245,158,11,0.25);">
                            <i class="bi bi-hourglass-split text-white" style="font-size:0.95rem;"></i>
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                <span class="badge bg-warning text-dark rounded-pill px-2 py-0.5" style="font-size:0.6rem;letter-spacing:0.2px;font-weight:700;">
                                    <i class="bi bi-clock-history me-0.5"></i> PERINGATAN 20-30 MENIT
                                </span>
                                <span class="fw-bold text-dark" style="font-size:0.82rem;">Siapkan Update Laporan!</span>
                            </div>
                            <div class="text-muted" style="font-size:0.68rem;line-height:1.3;margin-top:2px;">
                                Terakhir update <strong class="text-dark">{{ $tiket->minutes_since_last_update_formatted }} lalu</strong> &bull; SOP maks. 30 menit
                            </div>
                        </div>
                    </div>

                    {{-- ── Baris 2: Metric Chips & Tombol Kirim Update ── --}}
                    <div class="d-flex align-items-center gap-1.5 flex-wrap flex-sm-nowrap">
                        <div class="d-flex align-items-center gap-1.5 px-2 py-1.5 rounded-3 flex-fill" style="background:rgba(255,255,255,0.95);border:1px solid rgba(253,224,71,0.8);min-width:90px;">
                            <i class="bi bi-clock-history text-warning opacity-75" style="font-size:0.75rem;"></i>
                            <div>
                                <div class="text-muted" style="font-size:0.55rem;font-weight:700;text-transform:uppercase;letter-spacing:0.3px;line-height:1;">Terakhir</div>
                                <div class="fw-bold text-navy font-monospace" style="font-size:0.75rem;line-height:1.2;">{{ $lastKronologis ? ($lastKronologis->timestamp ? $lastKronologis->timestamp->format('H:i') : $lastKronologis->created_at->format('H:i')) . ' WIB' : '-' }}</div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-1.5 px-2 py-1.5 rounded-3 flex-fill" style="background:rgba(255,255,255,0.95);border:1px solid rgba(253,224,71,0.8);min-width:100px;">
                            <i class="bi bi-stopwatch text-warning opacity-75" style="font-size:0.75rem;"></i>
                            <div>
                                <div class="text-muted" style="font-size:0.55rem;font-weight:700;text-transform:uppercase;letter-spacing:0.3px;line-height:1;">Rata-rata</div>
                                <div class="fw-bold text-navy text-truncate" style="font-size:0.75rem;line-height:1.2;max-width:95px;">{{ $tiket->average_report_interval_formatted }}</div>
                            </div>
                        </div>
                        @if(auth()->user()->hasRole(['admin', 'teknis', 'helpdesk']))
                        <button type="button" class="btn fw-bold text-white d-inline-flex align-items-center justify-content-center gap-1 px-2.5 py-1.5 flex-fill flex-sm-grow-0" style="background:linear-gradient(135deg,#2563eb 0%,#1d4ed8 100%);border:none;border-radius:8px;font-size:0.72rem;box-shadow:0 2px 6px rgba(37,99,235,0.25);white-space:nowrap;min-height:36px;" onclick="const kTab=document.getElementById('kronologis-tab');if(kTab)kTab.click();const waInp=document.getElementById('waChatTextInput')||document.getElementById('informasi');if(waInp){waInp.focus();waInp.scrollIntoView({behavior:'instant',block:'center'});}">
                            <i class="bi bi-chat-left-dots-fill"></i>
                            <span>Kirim Update</span>
                        </button>
                        @endif
                    </div>
                    @endif
                </div>
                @endif

                <!-- Point 9: Helpdesk Stop Clock Reminder & Checklist -->
                @if($tiket->is_stop_clock && $tiket->activeStopClock)
                    <div class="mt-3 pt-2.5 border-top border-warning border-opacity-30">
                        <div class="d-flex align-items-start gap-2.5 p-2.5 rounded-3" style="background: #fffbeb; border: 1px dashed #f59e0b;">
                            <i class="bi bi-bell-fill text-warning fs-5 flex-shrink-0 mt-0.5"></i>
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                    <strong class="text-dark small"><i class="bi bi-exclamation-circle me-1"></i>Reminder Helpdesk (Stop Clock Aktif):</strong>
                                    <span class="badge bg-warning text-dark font-monospace" style="font-size: 0.7rem;">Follow-up Diperlukan</span>
                                </div>
                                <div class="small text-muted mt-1" style="font-size: 0.75rem;">
                                    Pastikan Helpdesk secara berkala mengontak pihak eksternal (PLN / Vendor / Perizinan) terkait kendala <em>"{{ $tiket->activeStopClock->reason_label }}"</em>.
                                    Segera lakukan <strong>Resume Clock</strong> begitu hambatan terselesaikan agar perhitungan MTTR tetap akurat.
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        @endif
    @endif

    <!-- ── MANDATORY CLOSING CHECKLIST CARD (ANTI CLOSING SEMBARANGAN) ── -->
    @php
        $prereqs = $tiket->checkClosingPrerequisites();
        $isBroadband = $tiket->isBroadband();
        $totalItems = $isBroadband ? 3 : 4;
        
        $completedCount = 0;
        if (!empty($prereqs['items']['resume_filled'])) $completedCount++;
        if (!empty($prereqs['items']['photo_uploaded'])) $completedCount++;
        
        if ($isBroadband) {
            if (!empty($prereqs['items']['redaman_sesudah'])) $completedCount++;
        } else {
            if (!empty($prereqs['items']['titik_perbaikan'])) $completedCount++;
            if (!empty($prereqs['items']['tipe_penanganan'])) $completedCount++;
        }
        
        $progressPercent = (int) round(($completedCount / $totalItems) * 100);
    @endphp
    @if($tiket->status !== 'CLOSE')
    <div class="closing-readiness-card">
        <!-- Header with Progress -->
        <div class="closing-readiness-header">
            <div class="d-flex align-items-center gap-2.5">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px; background: linear-gradient(135deg, {{ $isBroadband ? '#059669 0%, #10b981 100%' : '#1e40af 0%, #3b82f6 100%' }}); color: #ffffff; box-shadow: 0 2px 8px rgba(16, 185, 129, 0.25);">
                    <i class="bi bi-shield-lock-fill" style="font-size: 0.95rem;"></i>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-1.5 flex-wrap">
                        <span class="fw-bold text-navy" style="font-size: 0.92rem; letter-spacing: -0.2px;">Kesiapan Closing {{ $isBroadband ? 'Maintenance Broadband' : 'Tiket Backbone' }}</span>
                        <span class="badge rounded-pill fw-bold" style="background: {{ $progressPercent === 100 ? '#ecfdf5' : '#eff6ff' }}; color: {{ $progressPercent === 100 ? '#059669' : '#1d4ed8' }}; border: 1px solid {{ $progressPercent === 100 ? '#a7f3d0' : '#bfdbfe' }}; font-size: 0.68rem;">
                            {{ $completedCount }}/{{ $totalItems }} Terpenuhi ({{ $progressPercent }}%)
                        </span>
                    </div>
                    <div class="text-muted small d-none d-sm-block" style="font-size: 0.72rem; margin-top: 1px;">
                        {{ $totalItems }} syarat mandatori yang wajib dilengkapi teknisi sebelum closing awal dapat diajukan
                    </div>
                </div>
            </div>

            <div>
                @if($prereqs['ready'])
                    <span class="badge bg-success bg-opacity-15 text-success border border-success border-opacity-30 px-3 py-1.5 rounded-pill small fw-bold d-inline-flex align-items-center gap-1.5 shadow-xs">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Siap Closing Awal</span>
                    </span>
                @else
                    <span class="badge bg-warning bg-opacity-15 text-warning-emphasis border border-warning border-opacity-40 px-3 py-1.5 rounded-pill small fw-bold d-inline-flex align-items-center gap-1.5 shadow-xs">
                        <i class="bi bi-exclamation-triangle-fill text-warning"></i>
                        <span>Belum Lengkap ({{ count($prereqs['missing_items']) }} item lagi)</span>
                    </span>
                @endif
            </div>
        </div>

        <!-- Slim Visual Progress Bar -->
        <div class="closing-progress-bar-track">
            <div class="closing-progress-bar-fill" style="width: {{ $progressPercent }}%; background: {{ $progressPercent === 100 ? 'linear-gradient(90deg, #10b981, #059669)' : ($progressPercent >= 50 ? 'linear-gradient(90deg, #3b82f6, #10b981)' : 'linear-gradient(90deg, #f59e0b, #3b82f6)') }};"></div>
        </div>

        <!-- Grid Tiles -->
        <div class="p-2.5 p-sm-3 bg-light bg-opacity-40">
            <div class="row g-2 g-sm-2.5">
                @if($isBroadband)
                <!-- ── BROADBAND MANDATORI TILES ── -->
                <!-- 1. Resume / Tindakan Perbaikan -->
                <div class="col-12 col-md-4">
                    <div class="closing-item-tile {{ !empty($prereqs['items']['resume_filled']) ? 'is-complete' : 'is-pending' }}" 
                         onclick="const tab = document.getElementById('resume-tab'); if(tab) { tab.click(); document.getElementById('resume-pane')?.scrollIntoView({behavior:'smooth', block:'start'}); }" 
                         title="Klik untuk mengisi / melihat Resume & Tindakan Perbaikan">
                        <div class="d-flex align-items-center gap-2 min-w-0">
                            <div class="closing-tile-icon-box">
                                <i class="bi {{ !empty($prereqs['items']['resume_filled']) ? 'bi-check-lg' : 'bi-file-earmark-text' }}"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="fw-bold text-navy text-truncate closing-tile-title" style="font-size: 0.82rem;">1. Resume &amp; Tindakan</div>
                                <div class="text-truncate closing-tile-desc" style="font-size: 0.7rem; color: {{ !empty($prereqs['items']['resume_filled']) ? '#059669' : '#64748b' }};">
                                    {{ !empty($prereqs['items']['resume_filled']) ? 'Problem & Action diisi' : 'Belum diisi lengkap' }}
                                </div>
                            </div>
                        </div>
                        <div class="closing-tile-arrow">
                            <i class="bi bi-chevron-right"></i>
                        </div>
                    </div>
                </div>

                <!-- 2. Dokumentasi Foto Lapangan / OPM -->
                <div class="col-12 col-md-4">
                    <div class="closing-item-tile {{ !empty($prereqs['items']['photo_uploaded']) ? 'is-complete' : 'is-pending' }}" 
                         onclick="const tab = document.getElementById('dokumentasi-tab'); if(tab) { tab.click(); document.getElementById('dokumentasi-pane')?.scrollIntoView({behavior:'smooth', block:'start'}); }" 
                         title="Klik untuk mengupload Foto Dokumentasi / Ukur OPM / Modem">
                        <div class="d-flex align-items-center gap-2 min-w-0">
                            <div class="closing-tile-icon-box">
                                <i class="bi {{ !empty($prereqs['items']['photo_uploaded']) ? 'bi-check-lg' : 'bi-camera' }}"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="fw-bold text-navy text-truncate closing-tile-title" style="font-size: 0.82rem;">2. Foto / OPM / Modem</div>
                                <div class="text-truncate closing-tile-desc" style="font-size: 0.7rem; color: {{ !empty($prereqs['items']['photo_uploaded']) ? '#059669' : '#64748b' }};">
                                    {{ $tiket->dokumentasis->where('kategori', '!=', 'Bukti Masalah')->count() > 0 ? $tiket->dokumentasis->where('kategori', '!=', 'Bukti Masalah')->count() . ' foto terupload' : 'Wajib minimal 1 foto' }}
                                </div>
                            </div>
                        </div>
                        <div class="closing-tile-arrow">
                            <i class="bi bi-chevron-right"></i>
                        </div>
                    </div>
                </div>

                <!-- 3. Redaman Sesudah (dBm) Normal -->
                <div class="col-12 col-md-4">
                    <div class="closing-item-tile {{ !empty($prereqs['items']['redaman_sesudah']) ? 'is-complete' : 'is-pending' }}" 
                         onclick="const tab = document.getElementById('resume-tab'); if(tab) { tab.click(); document.getElementById('resume-pane')?.scrollIntoView({behavior:'smooth', block:'start'}); }" 
                         title="Klik untuk melihat / mengisi Redaman Hasil Perbaikan">
                        <div class="d-flex align-items-center gap-2 min-w-0">
                            <div class="closing-tile-icon-box">
                                <i class="bi {{ !empty($prereqs['items']['redaman_sesudah']) ? 'bi-check-lg' : 'bi-activity' }}"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="fw-bold text-navy text-truncate closing-tile-title" style="font-size: 0.82rem;">3. Redaman Sesudah</div>
                                <div class="text-truncate closing-tile-desc" style="font-size: 0.7rem; color: {{ !empty($prereqs['items']['redaman_sesudah']) ? '#059669' : '#64748b' }};">
                                    {{ ($tiket->redaman_sesudah ?? $tiket->resume?->redaman_sesudah) ? ($tiket->redaman_sesudah ?? $tiket->resume?->redaman_sesudah) . ' dBm (Normal)' : 'Wajib diisi (dBm)' }}
                                </div>
                            </div>
                        </div>
                        <div class="closing-tile-arrow">
                            <i class="bi bi-chevron-right"></i>
                        </div>
                    </div>
                </div>

                @else
                <!-- ── BACKBONE MANDATORI TILES ── -->
                <!-- 1. Resume -->
                <div class="col-6 col-xl-3">
                    <div class="closing-item-tile {{ $prereqs['items']['resume_filled'] ? 'is-complete' : 'is-pending' }}" 
                         onclick="const tab = document.getElementById('resume-tab'); if(tab) { tab.click(); document.getElementById('resume-pane')?.scrollIntoView({behavior:'smooth', block:'start'}); }" 
                         title="Klik untuk mengisi / melihat Resume Pekerjaan">
                        <div class="d-flex align-items-center gap-2 min-w-0">
                            <div class="closing-tile-icon-box">
                                <i class="bi {{ $prereqs['items']['resume_filled'] ? 'bi-check-lg' : 'bi-file-earmark-text' }}"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="fw-bold text-navy text-truncate closing-tile-title" style="font-size: 0.82rem;">1. Resume Kerja</div>
                                <div class="text-truncate closing-tile-desc" style="font-size: 0.7rem; color: {{ $prereqs['items']['resume_filled'] ? '#059669' : '#64748b' }};">
                                    {{ $prereqs['items']['resume_filled'] ? 'Problem & Action diisi' : 'Belum diisi lengkap' }}
                                </div>
                            </div>
                        </div>
                        <div class="closing-tile-arrow">
                            <i class="bi bi-chevron-right"></i>
                        </div>
                    </div>
                </div>

                <!-- 2. Dokumentasi -->
                <div class="col-6 col-xl-3">
                    <div class="closing-item-tile {{ $prereqs['items']['photo_uploaded'] ? 'is-complete' : 'is-pending' }}" 
                         onclick="const tab = document.getElementById('dokumentasi-tab'); if(tab) { tab.click(); document.getElementById('dokumentasi-pane')?.scrollIntoView({behavior:'smooth', block:'start'}); }" 
                         title="Klik untuk mengupload Foto Dokumentasi / OTDR">
                        <div class="d-flex align-items-center gap-2 min-w-0">
                            <div class="closing-tile-icon-box">
                                <i class="bi {{ $prereqs['items']['photo_uploaded'] ? 'bi-check-lg' : 'bi-camera' }}"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="fw-bold text-navy text-truncate closing-tile-title" style="font-size: 0.82rem;">2. Foto / OTDR</div>
                                <div class="text-truncate closing-tile-desc" style="font-size: 0.7rem; color: {{ $prereqs['items']['photo_uploaded'] ? '#059669' : '#64748b' }};">
                                    {{ $tiket->dokumentasis->where('kategori', '!=', 'Bukti Masalah')->count() > 0 ? $tiket->dokumentasis->where('kategori', '!=', 'Bukti Masalah')->count() . ' foto terupload' : 'Wajib minimal 1 foto' }}
                                </div>
                            </div>
                        </div>
                        <div class="closing-tile-arrow">
                            <i class="bi bi-chevron-right"></i>
                        </div>
                    </div>
                </div>

                <!-- 3. Titik Perbaikan -->
                <div class="col-6 col-xl-3">
                    <div class="closing-item-tile {{ $prereqs['items']['titik_perbaikan'] ? 'is-complete' : 'is-pending' }}" 
                         onclick="const tab = document.getElementById('material-tab'); if(tab) { tab.click(); document.getElementById('material-pane')?.scrollIntoView({behavior:'smooth', block:'start'}); }" 
                         title="Klik untuk menambahkan Titik Koordinat / Joint Closure">
                        <div class="d-flex align-items-center gap-2 min-w-0">
                            <div class="closing-tile-icon-box">
                                <i class="bi {{ $prereqs['items']['titik_perbaikan'] ? 'bi-check-lg' : 'bi-geo-alt' }}"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="fw-bold text-navy text-truncate closing-tile-title" style="font-size: 0.82rem;">3. Koordinat / JC</div>
                                <div class="text-truncate closing-tile-desc" style="font-size: 0.7rem; color: {{ $prereqs['items']['titik_perbaikan'] ? '#059669' : '#64748b' }};">
                                    {{ $tiket->titikPerbaikans->count() > 0 ? $tiket->titikPerbaikans->count() . ' titik tercatat' : 'Wajib minimal 1 titik' }}
                                </div>
                            </div>
                        </div>
                        <div class="closing-tile-arrow">
                            <i class="bi bi-chevron-right"></i>
                        </div>
                    </div>
                </div>

                <!-- 4. Tipe Penanganan -->
                <div class="col-6 col-xl-3">
                    <div class="closing-item-tile {{ $prereqs['items']['tipe_penanganan'] ? 'is-complete' : 'is-pending' }}" 
                         onclick="const tab = document.getElementById('penanganan-tab'); if(tab) { tab.click(); document.getElementById('penanganan-pane')?.scrollIntoView({behavior:'smooth', block:'start'}); }" 
                         title="Klik untuk memilih Tipe Penanganan (Jointing Lurus / Manuver Core)">
                        <div class="d-flex align-items-center gap-2 min-w-0">
                            <div class="closing-tile-icon-box">
                                <i class="bi {{ $prereqs['items']['tipe_penanganan'] ? 'bi-check-lg' : 'bi-tools' }}"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="fw-bold text-navy text-truncate closing-tile-title" style="font-size: 0.82rem;">4. Penanganan</div>
                                @php
                                    $jcCount = $tiket->jointClosures()->count();
                                    $mvCount = $tiket->manuverCores()->count();
                                    $penangananDesc = $prereqs['items']['tipe_penanganan']
                                        ? collect([
                                            $jcCount > 0 ? $jcCount.' JC' : null,
                                            $mvCount > 0 ? $mvCount.' Manuver' : null,
                                          ])->filter()->join(', ')
                                        : 'Wajib tambah JC / Manuver Core';
                                @endphp
                                <div class="text-truncate closing-tile-desc" style="font-size: 0.7rem; color: {{ $prereqs['items']['tipe_penanganan'] ? '#059669' : '#64748b' }};">
                                    {{ $penangananDesc }}
                                </div>
                            </div>
                        </div>
                        <div class="closing-tile-arrow">
                            <i class="bi bi-chevron-right"></i>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            {{-- ── TOMBOL CLOSING AWAL (muncul saat semua syarat terpenuhi) ── --}}
            @if($prereqs['ready'] && $tiket->status === 'PROSES' && auth()->user()->hasRole(['teknis', 'teknisi']))
            <div class="mt-3">
                <div class="closing-ready-banner d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
                    <div class="d-flex align-items-center gap-3 min-w-0">
                        <div class="closing-ready-icon">
                            <i class="bi bi-check2-circle"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span class="badge" style="background: rgba(16, 185, 129, 0.18); color: #047857; border: 1px solid rgba(16, 185, 129, 0.3); font-size: 0.65rem; font-weight: 700; letter-spacing: 0.4px; padding: 0.2rem 0.5rem; border-radius: 6px;">
                                    SIAP DIAJUKAN
                                </span>
                                <span class="fw-bold" style="font-size: 0.9rem; color: #064e3b; letter-spacing: -0.2px;">
                                    Semua Syarat Mandatori Lengkap!
                                </span>
                            </div>
                            <div style="font-size: 0.74rem; color: #047857; margin-top: 2px;">
                                Seluruh data pekerjaan telah terpenuhi. Klik tombol di samping untuk mengajukan Closing Awal ke Helpdesk.
                            </div>
                        </div>
                    </div>
                    <button type="button"
                            class="btn-closing-action flex-shrink-0"
                            data-bs-toggle="modal" data-bs-target="#closingAwalModal">
                        <i class="bi bi-send-check-fill"></i>
                        <span>Ajukan Closing Awal</span>
                    </button>
                </div>
            </div>
            @endif

        </div>
    </div>
    @endif

    <!-- ── TABBED NAVIGATION (MOBILE SCROLLABLE) ── -->
</div>
