                                <div class="tab-header-left">
                                    <div class="tab-header-icon-box icon-box-amber">
                                        <i class="bi bi-box-seam-fill"></i>
                                    </div>
                                    <div>
                                        <div class="tab-header-title">Material Digunakan</div>
                                        <div class="tab-header-subtitle">Pemakaian kabel, splice, & aksesoris</div>
                                    </div>
                                </div>
                                @if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'teknis', 'helpdesk']))
                                <button class="btn-tab-action-pro btn-amber-pro btn-mobile-block" data-bs-toggle="modal" data-bs-target="#addMaterialModal">
                                    <i class="bi bi-plus-circle"></i>
                                    <span>Tambah Material</span>
                                </button>
                                @endif
                            </div>

                            @if($tiket->materials->count() > 0)
                                <!-- Mobile card view -->
                                <div class="d-flex flex-column gap-2 d-md-none">
                                    @foreach($tiket->materials as $mat)
                                    <div class="material-card-mobile">
                                        <div class="d-flex align-items-center gap-2 overflow-hidden flex-grow-1">
                                            <i class="bi bi-box text-warning" style="font-size: 0.85rem;"></i>
                                            <div class="fw-semibold text-navy text-truncate small">{{ $mat->nama_material }}</div>
                                        </div>
                                        <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                            <span class="material-qty-pill">{{ $mat->jumlah }} {{ $mat->satuan }}</span>
                                            @if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'teknis', 'helpdesk']))
                                            <form action="{{ route('tiket.material.destroy', [$tiket->id, $mat->id]) }}" method="POST"
                                                  onsubmit="return confirm('Hapus material ini?');" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-link text-danger p-0" title="Hapus">
                                                    <i class="bi bi-trash3"></i>
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach
                                </div>

                                <!-- Desktop table view -->
                                <div class="table-responsive border rounded-3 bg-white d-none d-md-block shadow-xs">
                                    <table class="table custom-table table-sm align-middle mb-0">
                                        <thead>
                                             <tr>
                                                <th>Nama Material</th>
                                                <th class="text-end">Jumlah</th>
                                                <th>Satuan</th>
                                                @if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'teknis', 'helpdesk']))
                                                <th class="text-end" style="width: 40px;"></th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($tiket->materials as $mat)
                                            <tr>
                                                <td class="fw-semibold text-navy">{{ $mat->nama_material }}</td>
                                                <td class="text-end font-monospace fw-bold text-navy">{{ $mat->jumlah }}</td>
                                                <td><span class="badge bg-light text-navy border">{{ $mat->satuan }}</span></td>
                                                @if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'teknis', 'helpdesk']))
                                                <td class="text-end">
                                                    <form action="{{ route('tiket.material.destroy', [$tiket->id, $mat->id]) }}" method="POST"
                                                          onsubmit="return confirm('Hapus material ini?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-link text-danger p-0" title="Hapus">
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
                                <div class="tab-empty-card">
                                    <div class="tab-empty-icon-circle icon-box-amber">
                                        <i class="bi bi-box-seam"></i>
                                    </div>
                                    <div class="tab-empty-title">Belum Ada Material Dicatat</div>
                                    <div class="tab-empty-desc">
                                        Catat pemakaian kabel fiber optik, protection sleeve, clamp, atau joint closure untuk tiket ini.
                                    </div>
                                    @if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'teknis', 'helpdesk']))
                                    <button class="btn-tab-action-pro btn-amber-pro" data-bs-toggle="modal" data-bs-target="#addMaterialModal">
                                        <i class="bi bi-plus-circle"></i>
                                        <span>Tambah Material</span>
                                    </button>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <!-- Titik Perbaikan Column -->
                        <div class="col-12 col-lg-6">
                            <div class="tab-header-banner">
                                <div class="tab-header-left">
                                    <div class="tab-header-icon-box icon-box-rose">
                                        <i class="bi bi-geo-alt-fill"></i>
                                    </div>
                                    <div>
                                        <div class="tab-header-title">Titik Tagging Perbaikan</div>
                                        <div class="tab-header-subtitle">Koordinat GPS closure & jointing</div>
                                    </div>
                                </div>
                                @if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'teknis', 'helpdesk']))
                                <button class="btn-tab-action-pro btn-rose-pro btn-mobile-block" data-bs-toggle="modal" data-bs-target="#addTitikModal">
                                    <i class="bi bi-plus-circle"></i>
                                    <span>Tambah Titik</span>
                                </button>
                                @endif
                            </div>

                            @if($tiket->titikPerbaikans->count() > 0)
                                <!-- Leaflet Map View of Points -->
                                <div id="titikPerbaikanMap" class="mb-3 border rounded-3 shadow-xs overflow-hidden"></div>

                                <!-- Mobile card view -->
                                <div class="d-flex flex-column gap-2 d-md-none">
                                    @foreach($tiket->titikPerbaikans as $tp)
                                    <div class="titik-tag-card-mobile">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="titik-name-badge">{{ $tp->nama_titik }}</span>
                                            @if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'teknis', 'helpdesk']))
                                            <form action="{{ route('tiket.titik-perbaikan.destroy', [$tiket->id, $tp->id]) }}" method="POST"
                                                  onsubmit="return confirm('Hapus titik perbaikan ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-link text-danger p-0" title="Hapus">
                                                    <i class="bi bi-trash3"></i>
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                        <div class="mb-1">
                                            <a href="{{ $tp->google_maps_url }}" target="_blank" class="small font-monospace text-decoration-none text-navy d-inline-flex align-items-center gap-1">
                                                <i class="bi bi-geo-alt-fill text-danger"></i>
                                                <span>{{ $tp->latitude }}, {{ $tp->longitude }}</span>
                                                <i class="bi bi-box-arrow-up-right text-muted" style="font-size: 0.65rem;"></i>
                                            </a>
                                        </div>
                                        @if($tp->keterangan)
                                        <div class="small text-muted fst-italic">{{ $tp->keterangan }}</div>
                                        @endif
                                    </div>
                                    @endforeach
                                </div>

                                <!-- Desktop table view -->
                                <div class="table-responsive border rounded-3 bg-white d-none d-md-block shadow-xs">
                                    <table class="table custom-table table-sm align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>Titik</th>
                                                <th>Koordinat</th>
                                                <th>Keterangan</th>
                                                @if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'teknis', 'helpdesk']))
                                                <th class="text-end" style="width: 40px;"></th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($tiket->titikPerbaikans as $tp)
                                            <tr>
                                                <td><span class="titik-name-badge">{{ $tp->nama_titik }}</span></td>
                                                <td>
                                                    <a href="{{ $tp->google_maps_url }}" target="_blank" class="small font-monospace text-decoration-none text-navy">
                                                        {{ $tp->latitude }}, {{ $tp->longitude }} <i class="bi bi-box-arrow-up-right text-muted" style="font-size: 0.7rem;"></i>
                                                    </a>
                                                </td>
                                                <td class="small text-muted">{{ $tp->keterangan ?: '-' }}</td>
                                                @if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'teknis', 'helpdesk']))
                                                <td class="text-end">
                                                    <form action="{{ route('tiket.titik-perbaikan.destroy', [$tiket->id, $tp->id]) }}" method="POST"
                                                          onsubmit="return confirm('Hapus titik perbaikan ini?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-link text-danger p-0" title="Hapus">
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
                                <div class="tab-empty-card">
                                    <div class="tab-empty-icon-circle icon-box-rose">
                                        <i class="bi bi-geo-alt"></i>
                                    </div>
                                    <div class="tab-empty-title">Belum Ada Titik Tagging</div>
                                    <div class="tab-empty-desc">
                                        Tandai titik lokasi tiang, penutupan closure (JC1, JC2), atau lokasi putus kabel pada peta GPS.
                                    </div>
                                    @if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'teknis', 'helpdesk']))
                                    <button class="btn-tab-action-pro btn-rose-pro" data-bs-toggle="modal" data-bs-target="#addTitikModal">
                                        <i class="bi bi-plus-circle"></i>
                                        <span>Tambah Titik Perbaikan</span>
                                    </button>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- ════ TAB 4: DOKUMENTASI FOTO (FASE 5) ════ -->
                <div class="tab-pane fade" id="dokumentasi-pane" role="tabpanel">
                    <div class="tab-header-banner">
                        <div class="tab-header-left">
                            <div class="tab-header-icon-box icon-box-cyan">
                                <i class="bi bi-camera-fill"></i>
                            </div>
