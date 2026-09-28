                                <i class="bi bi-file-earmark-text-fill"></i>
                            </div>
                            <div>
                                <div class="tab-header-title">Resume Akhir Pekerjaan</div>
                                <div class="tab-header-subtitle">Rangkuman temuan masalah, tindakan perbaikan, dan tim pelaksana</div>
                            </div>
                        </div>
                        @if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'teknis', 'helpdesk']))
                        <button class="btn-tab-action-pro btn-primary-pro btn-mobile-block" data-bs-toggle="modal" data-bs-target="#editResumeModal">
                            <i class="bi bi-pencil-square"></i>
                            <span>{{ $tiket->resume ? 'Edit Resume' : 'Input Resume Baru' }}</span>
                        </button>
                        @endif
                    </div>

                    @if($tiket->resume)
                        <div class="row g-3">
                            <!-- Team OM -->
                            <div class="col-12 col-md-6">
                                <div class="resume-card-pro resume-card-team">
                                    <div class="resume-card-label text-info">
                                        <i class="bi bi-people-fill"></i> Tim Teknis Pelaksana
                                    </div>
                                    <div class="resume-card-text">
                                        @if(is_array($tiket->resume->team_om))
                                            <div class="d-flex flex-wrap gap-1.5 mt-1">
                                                @foreach($tiket->resume->team_om as $namaTeknis)
                                                    <span class="badge bg-light text-navy border px-2.5 py-1.5 rounded-pill shadow-xs d-inline-flex align-items-center gap-1">
                                                        <i class="bi bi-person-fill text-primary" style="font-size: 0.75rem;"></i>
                                                        <span class="fw-semibold">{{ $namaTeknis }}</span>
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="fw-semibold text-navy">{{ $tiket->resume->team_om ?: '-' }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Problem -->
                            <div class="col-12 col-md-6">
                                <div class="resume-card-pro resume-card-problem">
                                    <div class="resume-card-label text-danger">
                                        <i class="bi bi-exclamation-triangle-fill"></i> Problem / Temuan Masalah
                                    </div>
                                    <div class="resume-card-text fw-bold text-navy">
                                        {{ $tiket->resume->problem_temuan ?: '-' }}
                                    </div>
                                </div>
                            </div>

                            <!-- Action -->
                            <div class="col-12">
                                <div class="resume-card-pro resume-card-action">
                                    <div class="resume-card-label text-success">
                                        <i class="bi bi-wrench-adjustable-circle-fill"></i> Action / Tindakan Perbaikan
                                    </div>
                                    <div class="resume-card-text" style="white-space: pre-line; line-height: 1.65;">{{ $tiket->resume->action ?: '-' }}</div>
                                </div>
                            </div>

                            <!-- Tipe Penanganan & Jointing Details (Point 4) -->
                            <!-- Tipe Penanganan & Jointing Summary -->
                            <div class="col-12 col-md-6">
                                <div class="resume-card-pro" style="border-left: 4px solid #0d9488; background: #f0fdfa;">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <div class="resume-card-label text-teal mb-0">
                                            <i class="bi bi-bezier2"></i> Tipe Penanganan Fisik / Core
                                        </div>
                                        <button type="button" class="btn btn-link btn-sm text-teal p-0 text-decoration-none fw-bold" style="font-size: 0.72rem;" onclick="document.getElementById('penanganan-tab')?.click(); document.getElementById('penanganan-pane')?.scrollIntoView({behavior:'smooth'});">
                                            Buka Menu &rarr;
                                        </button>
                                    </div>
                                    <div class="resume-card-text">
                                        @if(in_array(($tiket->tipe_penanganan ?? $tiket->resume?->tipe_penanganan), ['KEDUA', 'KOMBINASI', 'SEMUA']))
                                            <span class="badge bg-indigo text-white px-2.5 py-1 rounded-pill" style="background: #6366f1 !important;">
                                                <i class="bi bi-layers-fill me-1"></i> Jointing Lurus &amp; Manuver Core (Keduanya)
                                            </span>
                                        @elseif(($tiket->tipe_penanganan ?? $tiket->resume?->tipe_penanganan) === 'JOINTING_LURUS')
                                            <span class="badge bg-success text-white px-2.5 py-1 rounded-pill">
                                                <i class="bi bi-diagram-3-fill me-1"></i> Jointing Lurus (Kabel &amp; JC)
                                            </span>
                                        @elseif(($tiket->tipe_penanganan ?? $tiket->resume?->tipe_penanganan) === 'MANUVER_CORE')
                                            <span class="badge bg-primary text-white px-2.5 py-1 rounded-pill">
                                                <i class="bi bi-shuffle me-1"></i> Manuver Core (Swapping Core)
                                            </span>
                                        @else
                                            <span class="text-muted small">Belum ditentukan (Pilih di tab Penanganan Core &amp; JC)</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="resume-card-pro" style="border-left: 4px solid #6366f1; background: #eef2ff;">
                                    <div class="resume-card-label" style="color: #4f46e5;">
                                        <i class="bi bi-box-seam-fill"></i> Data Terinput di Menu Penanganan
                                    </div>
                                    <div class="resume-card-text">
                                        <div class="small text-navy">
                                            <strong>Joint Closure:</strong> {{ $tiket->jointClosures->count() }} Titik &bull;
                                            <strong>Manuver:</strong> {{ $tiket->manuverCores->count() }} Mapping Core
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Notes -->
                            @if($tiket->resume->catatan_tambahan)
                            <div class="col-12">
                                <div class="resume-card-pro resume-card-notes">
                                    <div class="resume-card-label text-purple">
                                        <i class="bi bi-journal-text"></i> Catatan Tambahan
                                    </div>
                                    <div class="resume-card-text text-muted" style="white-space: pre-line;">{{ $tiket->resume->catatan_tambahan }}</div>
                                </div>
                            </div>
                            @endif
                        </div>
                    @else
                        <!-- Modern Empty State -->
                        <div class="tab-empty-card">
                            <div class="tab-empty-icon-circle icon-box-blue">
                                <i class="bi bi-file-earmark-text"></i>
                            </div>
                            <div class="tab-empty-title">Resume Pekerjaan Belum Diinput</div>
                            <div class="tab-empty-desc">
                                Rangkuman penanganan teknis, akar penyebab gangguan, dan tindakan pemulihan di lapangan belum dicatat untuk tiket ini.
                            </div>
                            @if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'teknis', 'helpdesk']))
                            <button class="btn-tab-action-pro btn-primary-pro" data-bs-toggle="modal" data-bs-target="#editResumeModal">
                                <i class="bi bi-plus-circle"></i>
                                <span>Input Resume Sekarang</span>
                            </button>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- ════ TAB: PENANGANAN CORE & JOINT CLOSURE (JC) ════ -->
                @if($tiket->isBackbone())
                <div class="tab-pane fade" id="penanganan-pane" role="tabpanel">
                    @php
                        $rawPenanganan = $tiket->tipe_penanganan ?? $tiket->resume?->tipe_penanganan;
                        $hasJc = $tiket->jointClosures->count() > 0;
