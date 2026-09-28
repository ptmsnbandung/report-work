                <!-- ════ TAB 4: DOKUMENTASI FOTO (FASE 5) ════ -->
                <div class="tab-pane fade" id="dokumentasi-pane" role="tabpanel">
                    <div class="tab-header-banner">
                        <div class="tab-header-left">
                            <div class="tab-header-icon-box icon-box-cyan">
                                <i class="bi bi-camera-fill"></i>
                            </div>
                            <div>
                                <div class="tab-header-title">Dokumentasi Foto Lapangan</div>
                                <div class="tab-header-subtitle">Foto jointing, closure, kondisi tiang, dan hasil ukur OTDR</div>
                            </div>
                        </div>
                        @if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'teknis', 'helpdesk']))
                        <button class="btn-tab-action-pro btn-cyan-pro btn-mobile-block" data-bs-toggle="modal" data-bs-target="#uploadDokumentasiModal">
                            <i class="bi bi-cloud-arrow-up-fill"></i>
                            <span>Upload Foto Dokumentasi</span>
                        </button>
                        @endif
                    </div>

                    @if($tiket->dokumentasis->count() > 0)
                        <!-- Category Filter Chips -->
                        <div class="d-flex flex-wrap gap-2 mb-3 align-items-center" id="docFilterChips">
                            <span class="small text-muted fw-bold me-1"><i class="bi bi-funnel-fill text-teal"></i> Filter:</span>
                            <button type="button" class="doc-filter-pill-pro active" data-filter="all">
                                Semua ({{ $tiket->dokumentasis->count() }})
                            </button>
                            @php
                                $categories = $tiket->dokumentasis->groupBy('kategori');
                            @endphp
                            @foreach($categories as $catName => $items)
                            <button type="button" class="doc-filter-pill-pro" data-filter="{{ Str::slug($catName) }}">
                                {{ $catName }} ({{ $items->count() }})
                            </button>
                            @endforeach
                        </div>

                        <!-- Gallery Grid -->
                        <div class="row g-3" id="docGalleryGrid">
                            @foreach($tiket->dokumentasis as $dok)
                            <div class="col-12 col-sm-6 col-md-4 col-lg-3 doc-gallery-item" data-category="{{ Str::slug($dok->kategori) }}">
                                <div class="doc-card-pro h-100 position-relative">
                                    <!-- Photo Thumbnail -->
                                    <div class="doc-img-wrapper position-relative" style="height: 180px; background-color: #0f172a; overflow: hidden; cursor: pointer;"
                                         onclick="zoomPhoto('{{ asset('storage/' . $dok->file_path) }}', '{{ $dok->kategori }} &bull; {{ $dok->formatted_timestamp }}')">
                                        <img src="{{ asset('storage/' . $dok->file_path) }}"
                                             class="w-100 h-100"
                                             style="object-fit: cover; transition: transform 0.3s ease;"
                                             alt="{{ $dok->kategori }}"
                                             loading="lazy"
                                             onerror="this.onerror=null; this.src='{{ asset($dok->file_path) }}';">
                                        
                                        <!-- Category Badge (Top Left) -->
                                        <div class="position-absolute top-0 start-0 m-2">
                                            <span class="badge {{ $dok->kategori_badge_class }} shadow-sm">
                                                {{ $dok->kategori }}
                                            </span>
                                        </div>

                                        <!-- Timestamp Overlay (Bottom Left) -->
                                        <div class="position-absolute bottom-0 start-0 end-0 p-2" style="background: linear-gradient(transparent, rgba(0,0,0,0.85));">
                                            <span class="text-white font-monospace" style="font-size: 0.72rem;">
                                                <i class="bi bi-clock me-1"></i>{{ $dok->formatted_timestamp }}
                                            </span>
                                        </div>

                                        <!-- Hover Zoom Overlay Icon -->
                                        <div class="doc-hover-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center"
                                             style="background: rgba(0,0,0,0.3); opacity: 0; transition: opacity 0.2s ease;">
                                            <span class="badge bg-white text-dark shadow-sm px-2.5 py-1.5 rounded-pill"><i class="bi bi-zoom-in me-1"></i> Lihat Foto</span>
                                        </div>
                                    </div>

                                    <!-- Card Info & Actions Footer -->
                                    <div class="card-body p-2 bg-white d-flex justify-content-between align-items-center border-top">
                                        <div>
                                            @if($dok->latitude && $dok->longitude)
                                            <a href="{{ $dok->google_maps_url }}" target="_blank" class="small text-navy text-decoration-none font-monospace" title="Buka di Google Maps">
                                                <i class="bi bi-geo-alt-fill text-danger me-1"></i>{{ round($dok->latitude, 5) }}, {{ round($dok->longitude, 5) }}
                                                <i class="bi bi-box-arrow-up-right text-muted" style="font-size: 0.65rem;"></i>
                                            </a>
                                            @else
                                            <span class="text-muted small" style="font-size: 0.75rem;"><i class="bi bi-geo text-muted me-1"></i>Tanpa GPS</span>
                                            @endif
                                        </div>

                                        @if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'teknis', 'helpdesk']))
                                        <form action="{{ route('tiket.dokumentasi.destroy', $dok->id) }}" method="POST"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus foto dokumentasi ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-link text-danger p-0 px-1" title="Hapus foto">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="tab-empty-card">
                            <div class="tab-empty-icon-circle icon-box-cyan">
                                <i class="bi bi-camera"></i>
                            </div>
                            <div class="tab-empty-title">Belum Ada Foto Dokumentasi</div>
                            <div class="tab-empty-desc">
                                Unggah foto hasil sambungan (jointing), penutupan closure, tiang jalur kabel, atau hasil pengukuran OTDR sebagai dokumentasi fisik.
                            </div>
                            @if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'teknis', 'helpdesk']))
                            <button class="btn-tab-action-pro btn-cyan-pro" data-bs-toggle="modal" data-bs-target="#uploadDokumentasiModal">
                                <i class="bi bi-cloud-arrow-up-fill"></i>
                                <span>Upload Foto Sekarang</span>
                            </button>
                            @endif
                        </div>
                    @endif
                </div>

