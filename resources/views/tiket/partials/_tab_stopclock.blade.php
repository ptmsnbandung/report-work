                            </div>
                            <div>
                                <div class="tab-header-title">Stop Clock SLA & Serah Terima Shift</div>
                                <div class="tab-header-subtitle">Log riwayat jeda penghitungan SLA dan handover antar shift</div>
                            </div>
                        </div>
                        @if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'teknis', 'helpdesk']))
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            @if(!$tiket->is_stop_clock)
                            <button class="btn-tab-action-pro btn-warning-pro" data-bs-toggle="modal" data-bs-target="#startStopClockModal">
                                <i class="bi bi-pause-circle"></i>
                                <span>Stop Clock</span>
                            </button>
                            @else
                            <form action="{{ route('tiket.stop-clock.stop', $tiket->id) }}" method="POST" class="d-inline m-0" onsubmit="return confirm('Lanjutkan perhitungan SLA (Resume Clock)? Durasi jeda akan diakumulasikan.');">
                                @csrf
                                <button type="submit" class="btn-tab-action-pro btn-success-pro" style="background: linear-gradient(135deg, #059669 0%, #047857 100%); color: #ffffff; border: none;">
                                    <i class="bi bi-play-circle-fill"></i>
                                    <span>Resume Clock</span>
                                </button>
                            </form>
                            @endif
                            <button class="btn-tab-action-pro btn-violet-pro" data-bs-toggle="modal" data-bs-target="#handoverShiftModal">
                                <i class="bi bi-arrow-left-right"></i>
                                <span>Oper Shift</span>
                            </button>
                        </div>
                        @endif
                    </div>

                    <!-- 1. RIWAYAT STOP CLOCK -->
                    <div class="card border-0 shadow-sm rounded-xl mb-4 bg-white">
                        <div class="card-header bg-white p-3 border-bottom d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-pause-circle-fill text-warning"></i>
                                <span class="fw-bold text-navy small">Log Stop Clock (Pengurangan Durasi SLA)</span>
                            </div>
                            <span class="badge bg-light text-navy border font-monospace">{{ $tiket->total_stop_clock_minutes }} Menit Total Jeda</span>
                        </div>
                        <div class="card-body p-0">
                            @if($tiket->stopClocks->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0" style="font-size:0.83rem;">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-3 py-2.5">Alasan Stop Clock</th>
                                            <th class="py-2.5">Mulai Stop</th>
                                            <th class="py-2.5">Resume / Selesai</th>
                                            <th class="py-2.5">Durasi Jeda</th>
                                            <th class="py-2.5">Petugas</th>
                                            <th class="pe-3 py-2.5">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($tiket->stopClocks as $sc)
                                        <tr>
                                            <td class="ps-3 fw-bold text-navy">
                                                <span class="badge bg-secondary bg-opacity-15 text-dark">{{ $sc->reason_label }}</span>
                                            </td>
                                            <td class="font-monospace text-muted">{{ $sc->stopped_at->format('d/m/Y H:i') }}</td>
                                            <td class="font-monospace">
                                                @if($sc->resumed_at)
                                                    <span class="text-success">{{ $sc->resumed_at->format('d/m/Y H:i') }}</span>
                                                @else
                                                    <span class="badge bg-warning text-dark animate-pulse">Sedang Berlangsung</span>
                                                @endif
                                            </td>
                                            <td class="fw-bold font-monospace text-navy">
                                                {{ $sc->duration_minutes ? $sc->duration_minutes . ' Menit' : ($sc->is_active ? round(now()->diffInMinutes($sc->stopped_at)) . ' Mnt (Aktif)' : '-') }}
                                            </td>
                                            <td>
                                                <div class="small fw-semibold text-navy">{{ $sc->user?->name ?? 'Sistem' }}</div>
                                                <div class="text-muted" style="font-size:0.7rem;">{{ $sc->user?->role_short ?? '-' }}</div>
                                            </td>
                                            <td class="pe-3 text-muted small">{{ $sc->notes ?: '-' }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="p-4 text-center text-muted small">
                                <i class="bi bi-stopwatch text-muted d-block fs-3 mb-1"></i>
                                Belum ada riwayat Stop Clock pada tiket ini.
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- 2. RIWAYAT OPER SHIFT (HANDOVER) -->
                    <div class="card border-0 shadow-sm rounded-xl bg-white">
                        <div class="card-header bg-white p-3 border-bottom d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-arrow-left-right text-purple"></i>
                                <span class="fw-bold text-navy small">Log Serah Terima Pekerjaan (Oper Shift)</span>
                            </div>
                            <span class="badge bg-light text-navy border font-monospace">{{ $tiket->handoverShifts->count() }} Handover</span>
                        </div>
                        <div class="card-body p-0">
                            @if($tiket->handoverShifts->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0" style="font-size:0.83rem;">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-3 py-2.5">Waktu Handover</th>
                                            <th class="py-2.5">Dari Shift &rarr; Menuju</th>
                                            <th class="py-2.5">Status Lapangan</th>
                                            <th class="py-2.5">Kendala Pending</th>
                                            <th class="py-2.5">Alokasi Tim Lanjutan</th>
                                            <th class="pe-3 py-2.5">Petugas Handover</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($tiket->handoverShifts as $ho)
                                        <tr>
                                            <td class="ps-3 font-monospace text-muted">{{ $ho->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <span class="badge bg-secondary bg-opacity-25 text-dark">{{ $ho->shift_sebelum ?: 'Shift Sebelumnya' }}</span>
                                                <i class="bi bi-arrow-right mx-1 text-muted"></i>
                                                <span class="badge bg-primary bg-opacity-25 text-primary fw-bold">{{ $ho->shift_tujuan }}</span>
                                            </td>
                                            <td class="small text-navy">{{ $ho->status_lapangan }}</td>
                                            <td class="small text-danger">{{ $ho->kendala_pending ?: '-' }}</td>
                                            <td class="small text-muted">{{ $ho->alokasi_team ?: '-' }}</td>
                                            <td class="pe-3">
                                                <div class="small fw-semibold text-navy">{{ $ho->user?->name ?? 'Sistem' }}</div>
                                                <div class="text-muted" style="font-size:0.7rem;">{{ $ho->user?->role_short ?? '-' }}</div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="p-4 text-center text-muted small">
                                <i class="bi bi-arrow-left-right text-muted d-block fs-3 mb-1"></i>
                                Belum ada riwayat handover shift pada tiket ini.
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>

