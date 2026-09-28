    <!-- ── TABBED NAVIGATION (MOBILE SCROLLABLE) ── -->
    <div class="card border-0 shadow-sm rounded-xl overflow-hidden mb-1 pb-0 mb-md-4 pb-md-0">
        <div class="card-header bg-white p-2 border-bottom">
            <ul class="nav nav-tabs-mobile" id="tiketTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active d-flex align-items-center gap-1.5"
                            id="kronologis-tab" data-bs-toggle="tab" data-bs-target="#kronologis-pane" type="button" role="tab">
                        <i class="bi bi-clock-history text-teal"></i>
                        <span>Kronologis</span>
                        <span class="badge bg-light text-navy border" id="kronologisCountBadge">{{ $tiket->kronologis->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link d-flex align-items-center gap-1.5"
                            id="resume-tab" data-bs-toggle="tab" data-bs-target="#resume-pane" type="button" role="tab">
                        <i class="bi bi-file-earmark-check text-primary"></i>
                        <span>Resume</span>
                        @if($tiket->resume)
                            <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size:0.65rem;">Ada</span>
                        @endif
                    </button>
                </li>
                @if($tiket->isBackbone())
                <li class="nav-item" role="presentation">
                    <button class="nav-link d-flex align-items-center gap-1.5"
                            id="penanganan-tab" data-bs-toggle="tab" data-bs-target="#penanganan-pane" type="button" role="tab">
                        <i class="bi bi-bezier2" style="color: #6366f1 !important;"></i>
                        <span>Penanganan Core &amp; JC</span>
                        <span class="badge bg-light text-navy border" id="penangananCountBadge">{{ $tiket->jointClosures->count() + $tiket->manuverCores->count() }}</span>
                    </button>
                </li>
                @endif
                <li class="nav-item" role="presentation">
                    <button class="nav-link d-flex align-items-center gap-1.5"
                            id="material-tab" data-bs-toggle="tab" data-bs-target="#material-pane" type="button" role="tab">
                        <i class="bi bi-box-seam text-warning"></i>
                        <span>Material &amp; Titik</span>
                        <span class="badge bg-light text-navy border">{{ $tiket->materials->count() + $tiket->titikPerbaikans->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link d-flex align-items-center gap-1.5"
                            id="dokumentasi-tab" data-bs-toggle="tab" data-bs-target="#dokumentasi-pane" type="button" role="tab">
                        <i class="bi bi-camera-fill text-info"></i>
                        <span>Dokumentasi</span>
                        <span class="badge bg-light text-navy border" id="dokumentasiCountBadge">{{ $tiket->dokumentasis->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link d-flex align-items-center gap-1.5"
                            id="stopclock-tab" data-bs-toggle="tab" data-bs-target="#stopclock-pane" type="button" role="tab">
                        <i class="bi bi-pause-circle text-warning"></i>
                        <span>Stop Clock &amp; Shift</span>
                        @if($tiket->stopClocks->count() > 0 || $tiket->handoverShifts->count() > 0)
                            <span class="badge bg-light text-navy border">{{ $tiket->stopClocks->count() + $tiket->handoverShifts->count() }}</span>
                        @endif
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body p-1 pb-1 p-sm-2.5 p-md-4">
            <div class="tab-content" id="tiketTabContent">

                <!-- ════ TAB 1: KRONOLOGIS (FASE 3 - WHATSAPP CHAT FEED) ════ -->
