                <!-- ════ TAB 1: KRONOLOGIS (FASE 3 - WHATSAPP CHAT FEED) ════ -->
                <div class="tab-pane fade show active" id="kronologis-pane" role="tabpanel">
                    <div class="wa-chat-container shadow-xs">
                        <!-- Chat Header -->
                        <div class="wa-chat-header">
                            <div class="d-flex align-items-center gap-2 gap-sm-2.5 overflow-hidden flex-grow-1">
                                <div class="wa-header-avatar">
                                    <i class="bi bi-chat-dots-fill text-white"></i>
                                </div>
                                <div class="overflow-hidden flex-grow-1">
                                    <div class="wa-header-title d-flex align-items-center gap-1.5">
                                        <span class="text-truncate"><span class="d-none d-sm-inline">Koordinasi </span>Update Lapangan</span>
                                        <span class="badge rounded-pill d-none d-sm-inline-flex align-items-center" style="font-size:0.65rem; font-weight:700; background: rgba(34, 197, 94, 0.2); color: #4ade80; border: 1px solid rgba(74, 222, 128, 0.4); padding: 3px 8px;">
                                            <span class="wa-pulse-dot"></span> Live Sync (5s)
                                        </span>
                                    </div>
                                    <div class="wa-header-meta text-truncate">
                                        <span class="d-inline d-sm-none fw-semibold" style="color: #4ade80;"><span class="wa-pulse-dot"></span>Live &bull; </span>
                                        <span class="d-none d-sm-inline">Catatan teknis tiket </span><strong>{{ $tiket->no_tiket }}</strong>
                                    </div>
                                </div>
                            </div>

                            <!-- Tombol Fullscreen -->
                            <button id="btnWaFullscreen" class="wa-fullscreen-btn" title="Perbesar chat">
                                <i class="bi bi-arrows-fullscreen" id="icoWaFullscreen"></i>
                            </button>

                        </div>

                        <!-- Chat Messages Stream Area -->
                        <div id="timelineWrapper">
                            @if($tiket->kronologis->count() > 0)
                                <div class="wa-chat-stream" id="timelineList">
                                    @php 
                                        $lastDate = null; 
                                        $currentUserId = auth()->id();
                                        $nameColors = ['#075e54', '#128c7e', '#0284c7', '#7c3aed', '#d97706', '#059669', '#2563eb'];
                                        $totalKronoCount = $totalKronologis ?? $tiket->kronologis()->count();
                                        $renderedCount = $tiket->kronologis->count();
                                        $hasOlderKrono = $totalKronoCount > $renderedCount;
                                        $oldestRenderedId = $tiket->kronologis->first()?->id ?? 0;
                                        
                                        // Baca timestamp kehadiran/view pengguna lain dari cache
                                        $viewsKey = "tiket_{$tiket->id}_user_views";
                                        $ticketViews = \Illuminate\Support\Facades\Cache::get($viewsKey, []);
                                        $otherViewTimes = collect($ticketViews)->where('user_id', '!=', $currentUserId)->pluck('viewed_at');
                                        $maxOtherViewTime = $otherViewTimes->max() ?? 0;
                                        
                                        $lastOtherKronoTime = $tiket->kronologis->where('user_id', '!=', $currentUserId)->max('timestamp');
                                        $lastOtherKronoTimestamp = $lastOtherKronoTime ? $lastOtherKronoTime->timestamp : 0;
                                        $maxReadTimestampByOthers = max((int)$maxOtherViewTime, (int)$lastOtherKronoTimestamp);
                                        $isTiketClosedOrVerified = in_array($tiket->status, ['CLOSE', 'MENUNGGU_VERIFIKASI', 'RESOLVED']);
                                    @endphp

                                    @if($hasOlderKrono)
                                        <div id="loadOlderKronoWrapper" class="text-center py-2.5 mb-2">
                                            <button type="button" class="btn btn-sm btn-light border rounded-pill px-3.5 shadow-xs fw-semibold text-secondary" id="btnLoadOlderKrono" data-oldest-id="{{ $oldestRenderedId }}">
                                                <i class="bi bi-clock-history me-1.5 text-primary"></i> Muat Pesan Sebelumnya (<span id="olderKronoCount">{{ $totalKronoCount - $renderedCount }}</span> lagi)
                                            </button>
                                        </div>
                                    @endif
                                    @foreach($tiket->kronologis as $krono)
                                        @php 
                                            $currentDate = $krono->timestamp->format('Y-m-d'); 
                                            $isMe = ($krono->user_id === $currentUserId);
                                            $colorIndex = abs(crc32($krono->user?->name ?? 'User')) % count($nameColors);
                                            $senderColor = $nameColors[$colorIndex];
                                            $initials = strtoupper(substr($krono->user?->name ?? 'U', 0, 2));
                                            $userAvatar = $krono->user?->avatar_url;
                                            $kronoTimeUnix = $krono->timestamp->timestamp;
                                            $isReadByOthers = $isTiketClosedOrVerified || ($maxReadTimestampByOthers > 0 && $kronoTimeUnix <= $maxReadTimestampByOthers);
                                        @endphp

                                        @if($currentDate !== $lastDate)
                                            <div class="wa-date-divider">
                                                <span class="wa-date-chip">
                                                    <i class="bi bi-calendar3 me-1"></i> {{ $krono->timestamp->translatedFormat('l, d F Y') }}
                                                </span>
                                            </div>
                                            @php $lastDate = $currentDate; @endphp
                                        @endif

                                        <div class="wa-msg-row {{ $isMe ? 'wa-msg-outgoing' : 'wa-msg-incoming' }}" id="krono-item-{{ $krono->id }}" data-id="{{ $krono->id }}" data-timestamp="{{ $krono->timestamp->timestamp }}">
                                            @if(!$isMe)
                                            <div class="wa-avatar" style="background-color: {{ $userAvatar ? 'transparent' : $senderColor }};" title="{{ $krono->user?->name }}">
                                                @if($userAvatar)
                                                    <img src="{{ $userAvatar }}" alt="{{ $krono->user?->name }}" class="wa-avatar-img">
                                                @else
                                                    {{ $initials }}
                                                @endif
                                            </div>
                                            @endif

                                            <div class="wa-bubble {{ $isMe ? 'wa-bubble-outgoing' : 'wa-bubble-incoming' }}">
                                                <!-- Bubble Header: Sender, Role & 3-Dots Action Menu -->
                                                <div class="wa-bubble-header">
                                                    <div class="wa-sender-info">
                                                        <span class="wa-sender-name" style="color: {{ $isMe ? '#0f766e' : $senderColor }};">
                                                            {{ $isMe ? 'Anda' : ($krono->user?->name ?? 'User') }}
                                                        </span>
                                                        <span class="wa-role-pill">{{ $krono->user?->role_short ?? '-' }}</span>
                                                    </div>

                                                    <!-- 3-Dots Message Action Dropdown -->
                                                    <div class="dropdown wa-bubble-menu-wrapper">
                                                        <button type="button" class="wa-msg-menu-btn" data-bs-toggle="dropdown" aria-expanded="false" title="Pilihan pesan">
                                                            <i class="bi bi-three-dots-vertical"></i>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end wa-msg-dropdown-menu shadow border-0">
                                                            <li>
                                                                <button type="button" class="dropdown-item wa-msg-dropdown-item btn-action-copy" data-id="{{ $krono->id }}" data-text="{{ e($krono->informasi) }}">
                                                                    <i class="bi bi-clipboard text-primary"></i> Salin
                                                                </button>
                                                            </li>
                                                            <li>
                                                                <button type="button" class="dropdown-item wa-msg-dropdown-item btn-action-msg-info"
                                                                        data-id="{{ $krono->id }}"
                                                                        data-user-id="{{ $krono->user_id }}"
                                                                        data-sender="{{ $isMe ? 'Anda' : ($krono->user?->name ?? 'User') }}"
                                                                        data-sender-role="{{ $krono->user?->role_short ?? '-' }}"
                                                                        data-time="{{ $krono->timestamp->format('d/m/Y H:i') }} WIB"
                                                                        data-text="{{ e($krono->informasi) }}"
                                                                        data-photo="{{ $krono->foto_url ? asset($krono->foto_url) : '' }}"
                                                                        data-timestamp="{{ $krono->timestamp->timestamp }}">
                                                                    <i class="bi bi-info-circle-fill text-info"></i> Info Pesan
                                                                </button>
                                                            </li>
                                                            @if($krono->foto_url && $tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'teknis', 'helpdesk']))
                                                            <li>
                                                                <button type="button" class="dropdown-item wa-msg-dropdown-item btn-action-forward-doc text-success"
                                                                        onclick="forwardPhotoToDoc('{{ asset($krono->foto_url) }}', '{{ $krono->latitude ?? '' }}', '{{ $krono->longitude ?? '' }}', '{{ $krono->timestamp->format('Y-m-d\TH:i') }}', '{{ $krono->kategori }}')">
                                                                    <i class="bi bi-folder-plus text-success"></i> Simpan ke Dokumentasi
                                                                </button>
                                                            </li>
                                                            @endif
                                                            @if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'teknis', 'helpdesk']))
                                                            <li>
                                                                <button type="button" class="dropdown-item wa-msg-dropdown-item btn-action-reply" data-id="{{ $krono->id }}" data-sender="{{ $isMe ? 'Anda' : ($krono->user?->name ?? 'User') }}" data-text="{{ e($krono->informasi) }}">
                                                                    <i class="bi bi-reply-fill text-info"></i> Balas
                                                                </button>
                                                            </li>
                                                            @endif
                                                            @php
                                                                $sentMoment = $krono->created_at ?? $krono->timestamp;
                                                                $diffMinutes = $sentMoment ? $sentMoment->diffInMinutes(now()) : 999;
                                                                $canEditMessage = ($tiket->status !== 'CLOSE') && ($isMe || auth()->user()->hasRole('admin')) && ($diffMinutes <= 5);
                                                                $canDeleteMessage = auth()->user()->hasRole('admin') || ($isMe && $tiket->status !== 'CLOSE');
                                                            @endphp
                                                            @if($canEditMessage)
                                                            <li>
                                                                <button type="button" class="dropdown-item wa-msg-dropdown-item btn-action-edit" data-id="{{ $krono->id }}" data-text="{{ e($krono->informasi) }}">
                                                                    <i class="bi bi-pencil-square text-warning"></i> Edit
                                                                </button>
                                                            </li>
                                                            @endif
                                                            @if($canDeleteMessage)
                                                            <li><hr class="dropdown-divider my-1"></li>
                                                            <li>
                                                                <button type="button" class="dropdown-item wa-msg-dropdown-item text-danger btn-action-delete" data-id="{{ $krono->id }}">
                                                                    <i class="bi bi-trash3-fill"></i> Hapus
                                                                </button>
                                                            </li>
                                                            @endif
                                                        </ul>
                                                    </div>
                                                </div>

                                                <!-- Message Body with Quoted Reply & Mention Highlights -->
                                                @php
                                                    $rawInfo = $krono->informasi ?? '';
                                                    $quoteSender = null;
                                                    $quoteText = null;
                                                    if (preg_match('/^>?\s*\[Membalas\s+([^\]]+)\]:\s*([^\n]+)\n+/i', $rawInfo, $quoteMatches)) {
                                                        $quoteSender = $quoteMatches[1];
                                                        $quoteText = $quoteMatches[2];
                                                        $rawInfo = substr($rawInfo, strlen($quoteMatches[0]));
                                                    }
                                                    $cleanedInfo = preg_replace('/(\r?\n\s*){2,}/', "\n", trim($rawInfo));
                                                    // Otomatis ubah angka menit menjadi format jam dan menit yang rapi (misal: 751 menit -> 12 jam 31 menit)
                                                    $cleanedInfo = preg_replace_callback('/(Durasi Jeda|Total Jeda SLA Tiket|Total Stop Clock):\s*(\d+)\s*menit/i', function($m) {
                                                        $mins = (int) $m[2];
                                                        return $m[1] . ': ' . \App\Models\Tiket::formatDuration($mins);
                                                    }, $cleanedInfo);
                                                @endphp
                                                @if($quoteSender)
                                                <div class="wa-quote-box">
                                                    <div class="wa-quote-sender"><i class="bi bi-reply-fill me-1"></i>{{ $quoteSender }}</div>
                                                    <div class="wa-quote-text">{{ $quoteText }}</div>
                                                </div>
                                                @endif
                                                <div class="wa-msg-text">{!! preg_replace('/(@[a-zA-Z0-9_\.\-]+(?:\s+[a-zA-Z0-9_\.\-]+)?)/u', '<span class="wa-mention-tag-highlight">$1</span>', nl2br(e($cleanedInfo))) !!}</div>

                                                <!-- Attached Media: Video or Photo (WhatsApp Media Card) -->
                                                @if($krono->is_video || $krono->video_url)
                                                <div class="wa-media-card wa-video-card">
                                                    <video src="{{ asset($krono->video_url ?? $krono->foto_url) }}" controls playsinline preload="metadata" class="wa-video-player"></video>
                                                </div>
                                                @elseif($krono->foto_url)
                                                <div class="wa-media-card" onclick="zoomPhoto('{{ asset($krono->foto_url) }}', '{{ $krono->kategori }} - {{ $krono->timestamp->format('d/m/Y H:i') }} WIB')">
                                                    <img src="{{ asset($krono->foto_url) }}" alt="Foto Kronologis" class="wa-media-img" width="280" height="158" loading="eager" decoding="async">
                                                    <div class="wa-media-badge">
                                                        <i class="bi bi-arrows-fullscreen"></i>
                                                        <span>Klik untuk memperbesar</span>
                                                    </div>
                                                </div>
                                                @endif

                                                <!-- Shared Location Card -->
                                                @if($krono->has_coordinates)
                                                <div class="wa-location-card">
                                                    <div class="wa-loc-icon">
                                                        <i class="bi bi-geo-alt-fill text-danger"></i>
                                                    </div>
                                                    <div class="wa-loc-info">
                                                        <div class="wa-loc-title">Lokasi Titik Lapangan</div>
                                                        <div class="wa-loc-coords">{{ $krono->latitude }}, {{ $krono->longitude }}</div>
                                                    </div>
                                                    <a href="{{ $krono->google_maps_url }}" target="_blank" class="wa-loc-btn" title="Buka di Google Maps">
                                                        <span>Peta</span>
                                                        <i class="bi bi-box-arrow-up-right"></i>
                                                    </a>
                                                </div>
                                                @endif

                                                <!-- Bubble Footer: Time & Double Checkmark (Abu jika belum ada tanggapan orang lain, Biru jika sudah) -->
                                                <div class="wa-bubble-footer">
                                                    <span class="wa-time">{{ $krono->timestamp->format('H:i') }} WIB</span>
                                                    @if($isMe)
                                                        <i class="bi bi-check2-all wa-status-icon {{ $isReadByOthers ? 'wa-status-read' : 'wa-status-sent' }}" title="{{ $isReadByOthers ? 'Dilihat oleh tim' : 'Terkirim (Belum dilihat)' }}"></i>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    <div id="waStreamBottomAnchor" style="height: 1px; width: 100%; flex-shrink: 0; pointer-events: none;"></div>
                                </div>
                            @else
                                <div class="wa-empty-state py-5 text-center" id="emptyTimeline">
                                    <div class="wa-empty-icon mb-3">
                                        <i class="bi bi-chat-square-dots-fill text-muted opacity-50"></i>
                                    </div>
                                    <h6 class="fw-bold text-navy mb-1">Belum Ada Catatan Koordinasi</h6>
                                    <p class="text-muted small mb-3 px-3 mx-auto" style="max-width: 420px;">
                                        Mulai percakapan perkembangan update teknis di lapangan. Seluruh aktivitas perbaikan akan tercatat secara kronologis.
                                    </p>
                                    @if($tiket->status !== 'CLOSE' && auth()->user()->hasRole(['admin', 'teknis', 'helpdesk']))
                                    <button class="btn btn-cjp-teal btn-sm rounded-pill px-4 shadow-xs" data-bs-toggle="modal" data-bs-target="#addKronologisModal">
                                        <i class="bi bi-plus-circle me-1"></i> Mulai Catatan Kronologis
                                    </button>
                                    @endif
                                </div>
                            @endif
                        </div>
                        <script>
                            (function() {
                                var s = document.getElementById('timelineList');
                                if (s) {
                                    s.style.setProperty('scroll-behavior', 'auto', 'important');
                                    s.scrollTop = s.scrollHeight || 999999;
                                }
                            })();
                        </script>

                        <!-- Floating Scroll-to-Bottom Button (WhatsApp Style) -->
                        <button type="button" id="btnWaScrollBottom" class="wa-scroll-bottom-btn d-none" title="Ke Pesan Terbaru">
                            <i class="bi bi-chevron-double-down"></i>
                        </button>

                        <!-- Gojek-Style Quick Reply Chips (Rekomendasi Chat Cepat) -->
                        @if($tiket->status !== 'CLOSE' && $tiket->canUserUpdateKoordinasi(auth()->user()))
                        <div class="wa-quick-replies-wrapper" id="waQuickRepliesWrapper">
                            <button type="button" class="wa-quick-chip" data-text="Sedang menuju ke lokasi titik gangguan">
                                <span class="wa-quick-chip-icon text-primary"><i class="bi bi-geo-alt-fill"></i></span>
                                <span>Menuju lokasi</span>
                            </button>
                            <button type="button" class="wa-quick-chip" data-text="Sedang investigasi di lapangan & pengukuran OTDR">
                                <span class="wa-quick-chip-icon text-info"><i class="bi bi-search"></i></span>
                                <span>Investigasi & OTDR</span>
                            </button>
                            <button type="button" class="wa-quick-chip" data-text="Ditemukan kabel fiber optik putus / bending">
                                <span class="wa-quick-chip-icon text-danger"><i class="bi bi-scissors"></i></span>
                                <span>Kabel putus / bending</span>
                            </button>
                            <button type="button" class="wa-quick-chip" data-text="Sedang proses splicing / penyambungan core kabel">
                                <span class="wa-quick-chip-icon text-warning"><i class="bi bi-lightning-charge-fill"></i></span>
                                <span>Proses splicing core</span>
                            </button>
                            <button type="button" class="wa-quick-chip" data-text="Sedang ukur nilai redaman / power level optik">
                                <span class="wa-quick-chip-icon" style="color: #6366f1;"><i class="bi bi-speedometer2"></i></span>
                                <span>Ukur redaman optik</span>
                            </button>
                            <button type="button" class="wa-quick-chip" data-text="Redaman sudah normal & link sudah UP kembali">
                                <span class="wa-quick-chip-icon text-success"><i class="bi bi-check-circle-fill"></i></span>
                                <span>Redaman normal & Link UP</span>
                            </button>
                            <button type="button" class="wa-quick-chip" data-text="Ada kendala di lapangan, mohon bantuan koordinasi / manuver core">
                                <span class="wa-quick-chip-icon text-warning"><i class="bi bi-shuffle"></i></span>
                                <span>Kendala / butuh manuver</span>
                            </button>
                            <button type="button" class="wa-quick-chip" data-text="Melampirkan foto dokumentasi hasil perbaikan di lapangan">
                                <span class="wa-quick-chip-icon text-secondary"><i class="bi bi-camera-fill"></i></span>
                                <span>Dokumentasi perbaikan</span>
                            </button>
                        </div>
                        @endif

                        @if($tiket->status !== 'CLOSE')
                            @if($tiket->canUserUpdateKoordinasi(auth()->user()))
                            <!-- WhatsApp Direct Inline Chat Input Bar (WhatsApp-Native Pro) -->
                            <form action="{{ route('tiket.kronologis.store', $tiket->id) }}" method="POST" enctype="multipart/form-data" id="waDirectChatForm" class="wa-chat-input-bar">
                                @csrf
                                <input type="hidden" name="kategori" value="LAIN">
                                <input type="hidden" name="latitude" id="waChatLatitude" value="">
                                <input type="hidden" name="longitude" id="waChatLongitude" value="">
                                <input type="file" name="foto" id="waChatFotoInput" accept="image/*,video/*" class="d-none">
                                <input type="file" id="waChatVideoRecordInput" accept="video/*" capture="environment" class="d-none">

                                <!-- Floating White Capsule Pill (Enclosing Paperclip + Textarea + Attachments) -->
                                <div class="wa-floating-input-pill">
                                    <!-- Paperclip Attachment Dropdown Menu -->
                                    <div class="dropup position-relative d-flex align-items-center">
                                        <button type="button" class="wa-attach-btn" id="waAttachDropdownBtn" data-bs-toggle="dropdown" aria-expanded="false" title="Lampirkan media / lokasi">
                                            <i class="bi bi-paperclip"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-start wa-attach-menu shadow-lg border-0" aria-labelledby="waAttachDropdownBtn">
                                            <li>
                                                <button type="button" class="dropdown-item d-flex align-items-center gap-2 py-2" id="btnWaUploadFoto">
                                                    <div class="wa-attach-icon bg-primary-subtle text-primary">
                                                        <i class="bi bi-images"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold small text-dark">Upload Media (Foto / Video)</div>
                                                        <div class="text-muted" style="font-size: 0.7rem;">Pilih foto atau video dari galeri HP / perangkat</div>
                                                    </div>
                                                </button>
                                            </li>
                                            <li>
                                                <button type="button" class="dropdown-item d-flex align-items-center gap-2 py-2" id="btnWaRecordVideo">
                                                    <div class="wa-attach-icon bg-danger-subtle text-danger">
                                                        <i class="bi bi-camera-reels-fill"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold small text-dark">Rekam Video Lapangan</div>
                                                        <div class="text-muted" style="font-size: 0.7rem;">Rekam langsung kamera HP (Video Ringan &amp; Cepat)</div>
                                                    </div>
                                                </button>
                                            </li>
                                            <li>
                                                <button type="button" class="dropdown-item d-flex align-items-center gap-2 py-2" id="btnWaCameraWatermark">
                                                    <div class="wa-attach-icon bg-success-subtle text-success">
                                                        <i class="bi bi-camera-fill"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold small text-dark">Kamera GPS (Watermark)</div>
                                                        <div class="text-muted" style="font-size: 0.7rem;">Dengan Logo MSN, Timestamp &amp; Alamat</div>
                                                    </div>
                                                </button>
                                            </li>
                                            <li>
                                                <button type="button" class="dropdown-item d-flex align-items-center gap-2 py-2" id="btnWaCameraPolos">
                                                    <div class="wa-attach-icon bg-info-subtle text-info">
                                                        <i class="bi bi-camera"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold small text-dark">Kamera Polos (Tanpa Watermark)</div>
                                                        <div class="text-muted" style="font-size: 0.7rem;">Potret langsung foto kamera original</div>
                                                    </div>
                                                </button>
                                            </li>
                                            <li><hr class="dropdown-divider my-1"></li>
                                            <li>
                                                <button type="button" class="dropdown-item d-flex align-items-center gap-2 py-2" id="btnWaShareLocation">
                                                    <div class="wa-attach-icon bg-danger-subtle text-danger">
                                                        <i class="bi bi-geo-alt-fill"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold small text-dark">Share Lokasi Saya</div>
                                                        <div class="text-muted" style="font-size: 0.7rem;">Sematkan koordinat GPS lapangan</div>
                                                    </div>
                                                </button>
                                            </li>
                                        </ul>
                                    </div>

                                    <!-- Text Input Area & Attachment Chips -->
                                    <div class="wa-input-wrapper position-relative">
                                        <!-- WhatsApp @Mention User Autocomplete Popup -->
                                        <div id="waMentionDropdown" class="wa-mention-dropdown d-none">
                                            <div class="wa-mention-header">
                                                <i class="bi bi-at text-primary"></i> Tag Anggota Tim
                                            </div>
                                            <div class="wa-mention-list" id="waMentionList"></div>
                                        </div>

                                        <!-- WhatsApp Reply Bar Preview (shows when replying to a message) -->
                                        <div id="waReplyPreviewBar" class="wa-reply-preview-bar d-none">
                                            <div class="wa-reply-preview-content">
                                                <div class="wa-reply-preview-sender" id="waReplySenderText">Membalas User</div>
                                                <div class="wa-reply-preview-text" id="waReplySnippetText">Isi pesan yang dibalas...</div>
                                            </div>
                                            <button type="button" class="btn-close" style="font-size: 0.6rem;" id="btnCancelWaReply" title="Batalkan Balasan"></button>
                                        </div>

                                        <textarea name="informasi" id="waChatTextInput" class="wa-chat-textarea" rows="1" placeholder="Ketik update koordinasi ..." required></textarea>
                                        <!-- Attachment Previews Bar (shows when photo, video or location is attached) -->
                                        <div id="waAttachmentPreviewBar" class="wa-attach-preview-bar d-none">
                                            <div id="waPhotoPreviewChip" class="d-none align-items-center gap-1.5 badge bg-white text-dark border shadow-xs me-1 py-1 px-2 rounded-pill" style="max-width: 100%;">
                                                <img id="waPhotoThumb" src="#" class="rounded-circle border d-none" style="width: 20px; height: 20px; object-fit: cover;" alt="Foto">
                                                <i class="bi bi-image text-primary" id="waPhotoDefaultIcon"></i>
                                                <span id="waPhotoFileName" class="text-truncate fw-semibold" style="max-width: 85px;">foto.jpg</span>
                                                <span id="waPhotoSizeBadge" class="badge bg-success-subtle text-success border border-success-subtle rounded-pill py-0.5 px-1.5" style="font-size: 0.65rem;">
                                                    <i class="bi bi-check2 me-0.5"></i> <span id="waPhotoSizeText">Ready</span>
                                                </span>
                                                <button type="button" class="btn-close ms-1" style="font-size: 0.55rem;" id="btnRemoveWaPhoto" title="Hapus Foto"></button>
                                            </div>
                                            <div id="waVideoPreviewChip" class="d-none align-items-center gap-1.5 badge bg-white text-dark border shadow-xs me-1 py-1 px-2 rounded-pill" style="max-width: 100%;">
                                                <div class="position-relative d-flex align-items-center justify-content-center bg-dark text-white rounded-circle" style="width: 22px; height: 22px; overflow: hidden; flex-shrink: 0;">
                                                    <img id="waVideoThumb" src="#" class="d-none w-100 h-100" style="object-fit: cover;" alt="Video">
                                                    <i class="bi bi-camera-reels-fill text-warning" id="waVideoDefaultIcon" style="font-size: 0.65rem;"></i>
                                                </div>
                                                <span id="waVideoFileName" class="text-truncate fw-semibold" style="max-width: 85px;">video.mp4</span>
                                                <span id="waVideoSizeBadge" class="badge bg-success-subtle text-success border border-success-subtle rounded-pill py-0.5 px-1.5" style="font-size: 0.65rem;">
                                                    <i class="bi bi-lightning-charge-fill me-0.5 text-warning"></i> <span id="waVideoSizeText">Ringan</span>
                                                </span>
                                                <button type="button" class="btn-close ms-1" style="font-size: 0.55rem;" id="btnRemoveWaVideo" title="Hapus Video"></button>
                                            </div>
                                            <div id="waLocationChip" class="d-none align-items-center gap-1.5 badge bg-danger-subtle text-danger border border-danger-subtle py-1 px-2 rounded-pill">
                                                <i class="bi bi-geo-alt-fill"></i>
                                                <span id="waLocationCoordsText">GPS Attached</span>
                                                <button type="button" class="btn-close ms-1" style="font-size: 0.55rem;" id="btnRemoveWaLocation" title="Hapus Lokasi"></button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Voice Note to Text (Speech Recognition) Microphone Button -->
                                    <button type="button" class="wa-voice-btn" id="btnWaVoiceNote" title="Ketik dengan Suara (Voice Note to Text)">
                                        <i class="bi bi-mic-fill"></i>
                                    </button>
                                </div>

                                <!-- Floating Submit Button -->
                                <button type="submit" class="wa-send-btn" id="btnWaSendMsg" title="Kirim Update Kronologis">
                                    <i class="bi bi-send-fill"></i>
                                </button>
                            </form>
                            @else
                            <!-- Mode Pemantauan (Read-Only) untuk Teknisi yang Tidak Ditunjuk -->
                            <div class="wa-closed-notice" style="background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 0.85rem 1.25rem;">
                                <div class="d-flex align-items-center justify-content-center gap-2 text-secondary small text-center flex-wrap">
                                    <i class="bi bi-shield-lock-fill text-primary" style="font-size: 1.15rem;"></i>
                                    <span>
                                        <strong>Mode Pemantauan (Read-Only):</strong> 
                                        Tiket ini sedang ditangani oleh <strong>{{ $tiket->assigned_team_names_formatted }}</strong>. 
                                        Hanya tim yang ditugaskan, Manager Teknis, dan HelpDesk NOC yang dapat mengirim update koordinasi.
                                    </span>
                                </div>
                            </div>
                            @endif
                        @endif

                        @if($tiket->status === 'CLOSE')
                        <div class="wa-closed-notice">
                            <i class="bi bi-lock-fill text-muted"></i>
                            <span>Tiket ini berstatus <strong>CLOSE</strong>. Riwayat percakapan koordinasi diarsipkan.</span>
                        </div>
                        @endif
                    </div>
                </div>

