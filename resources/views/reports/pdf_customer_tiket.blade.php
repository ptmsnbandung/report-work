<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Berita Acara Gangguan Pelanggan - {{ $tiket->no_tiket }}</title>
    <style>
        @page {
            margin-top: 68px;
            margin-bottom: 56px;
            margin-left: 24px;
            margin-right: 24px;
            size: A4 portrait;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #0f172a;
            font-size: 8.5pt;
            line-height: 1.4;
            position: relative;
        }

        /* ── KOP SURAT RESMI PT. MEDIA SOLUSI NETWORK ── */
        .msn-letterhead-header {
            position: fixed;
            top: -68px;
            left: -24px;
            right: -24px;
            height: 58px;
            z-index: 1000;
        }

        /* ── WATERMARK RESMI MSN TENGAH HALAMAN ── */
        .msn-watermark {
            position: fixed;
            top: 25%;
            left: 15%;
            width: 70%;
            height: 50%;
            text-align: center;
            z-index: -1000;
            opacity: 0.045;
        }

        /* ── FOOTER RESMI PT. MEDIA SOLUSI NETWORK ── */
        .msn-letterhead-footer {
            position: fixed;
            bottom: -56px;
            left: -24px;
            right: -24px;
            height: 48px;
            z-index: 1000;
        }

        /* ── DOKUMEN HEADER & BADGE ── */
        .doc-header-table {
            width: 100%;
            border-bottom: 2px solid #0c2033;
            padding-bottom: 6px;
            margin-bottom: 8px;
        }
        .doc-title {
            font-size: 11pt;
            font-weight: 800;
            color: #0c2033;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }
        .doc-sub {
            font-size: 7.2pt;
            font-weight: 700;
            color: #0284c7;
            letter-spacing: 0.4px;
            margin-top: 1px;
            text-transform: uppercase;
        }
        .doc-no-badge {
            display: inline-block;
            font-size: 8.5pt;
            font-family: monospace;
            color: #0369a1;
            font-weight: 700;
            text-align: right;
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            padding: 2px 8px;
            border-radius: 4px;
        }

        .section-title {
            font-size: 8.5pt;
            font-weight: 700;
            color: #ffffff;
            background-color: #0c2033;
            padding: 4px 8px;
            margin-top: 8px;
            margin-bottom: 5px;
            border-radius: 3px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .info-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }
        .info-grid td {
            padding: 3px 5px;
            font-size: 8pt;
            vertical-align: top;
            border-bottom: 1px solid #f1f5f9;
        }
        .info-label {
            font-weight: 700;
            color: #475569;
            width: 28%;
        }
        .info-colon { width: 2%; text-align: center; color: #94a3b8; }
        .info-val { width: 70%; color: #0f172a; }

        .status-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 4px solid #00bcd4;
            padding: 6px 9px;
            margin-top: 2px;
            margin-bottom: 6px;
            border-radius: 0 4px 4px 0;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 7pt;
            font-weight: 700;
            border-radius: 3px;
            text-transform: uppercase;
        }
        .badge-success { background-color: #dcfce7; color: #15803d; border: 1px solid #86efac; }
        .badge-danger  { background-color: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; }
        .badge-primary { background-color: #dbeafe; color: #1d4ed8; border: 1px solid #93c5fd; }
        .badge-gray    { background-color: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }

        .doc-gallery-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            margin-bottom: 6px;
        }
        .doc-gallery-table td {
            width: 33.33%;
            padding: 3px;
            vertical-align: top;
            text-align: center;
        }
        .doc-img-card {
            border: 1px solid #cbd5e1;
            padding: 4px;
            background: #f8fafc;
            border-radius: 3px;
        }
        .doc-img {
            max-width: 100%;
            max-height: 98px;
            height: auto;
            border-radius: 2px;
            display: block;
            margin: 0 auto;
            border: 1px solid #e2e8f0;
        }
        .doc-caption {
            font-size: 6.8pt;
            color: #334155;
            margin-top: 3px;
            line-height: 1.25;
        }

        .signature-table {
            width: 100%;
            margin-top: 14px;
            border-collapse: collapse;
            page-break-inside: avoid;
        }
        .signature-box {
            width: 50%;
            text-align: center;
            vertical-align: top;
            font-size: 8pt;
            padding: 4px;
        }
        .sign-space {
            height: 42px;
        }
    </style>
@php
    $logoPath = public_path('assets/logo-msn BG Trans.png');
    if (!file_exists($logoPath)) {
        $logoPath = public_path('assets/logo-msn BG Trans - Copy2.png');
    }
    $logoBase64 = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : null;
@endphp
</head>
<body>

    <!-- ── 1. HEADER KOP SURAT RESMI (TEMPLATING PT MEDIA SOLUSI NETWORK) ── -->
    <div class="msn-letterhead-header">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <!-- Sisi Kiri: Logo Resmi PT Media Solusi Network -->
                <td style="width: 50%; vertical-align: middle; text-align: left; padding-left: 24px; padding-top: 4px;">
                    @if($logoBase64)
                        <img src="{{ $logoBase64 }}" alt="MSN Logo" style="height: 40px; width: auto; display: block;">
                    @else
                        <svg width="36" height="36" viewBox="0 0 100 100">
                            <line x1="25" y1="30" x2="75" y2="30" stroke="#00d2ff" stroke-width="6"/>
                            <line x1="25" y1="30" x2="50" y2="75" stroke="#0284c7" stroke-width="6"/>
                            <line x1="75" y1="30" x2="50" y2="75" stroke="#00d2ff" stroke-width="6"/>
                            <circle cx="25" cy="30" r="14" fill="#0284c7" stroke="#ffffff" stroke-width="3"/>
                            <circle cx="25" cy="30" r="6" fill="#ffffff"/>
                            <circle cx="75" cy="30" r="14" fill="#00d2ff" stroke="#ffffff" stroke-width="3"/>
                            <circle cx="75" cy="30" r="6" fill="#ffffff"/>
                            <circle cx="50" cy="75" r="16" fill="#0ea5e9" stroke="#ffffff" stroke-width="3"/>
                            <circle cx="50" cy="75" r="7" fill="#ffffff"/>
                        </svg>
                    @endif
                </td>
                <!-- Sisi Kanan: Ornamen Grafis Geometris Slate & Cyan -->
                <td style="width: 50%; vertical-align: top; text-align: right; padding: 0;">
                    <svg width="240" height="52" viewBox="0 0 240 52" style="display: block; float: right;">
                        <polygon points="30,0 240,0 240,52 55,52" fill="#0c2033"/>
                        <polygon points="15,0 25,0 50,52 40,52" fill="#00d2ff"/>
                        <polygon points="0,0 10,0 35,52 25,52" fill="#0284c7"/>
                        <polygon points="225,0 240,0 240,52 212,52" fill="#00d2ff" opacity="0.25"/>
                    </svg>
                </td>
            </tr>
        </table>
    </div>

    <!-- ── 2. WATERMARK TENGAH HALAMAN ── -->
    <div class="msn-watermark">
        <svg width="340" height="340" viewBox="0 0 100 100">
            <line x1="25" y1="30" x2="75" y2="30" stroke="#0284c7" stroke-width="7"/>
            <line x1="25" y1="30" x2="50" y2="75" stroke="#0284c7" stroke-width="7"/>
            <line x1="75" y1="30" x2="50" y2="75" stroke="#0284c7" stroke-width="7"/>
            <circle cx="25" cy="30" r="16" fill="#0284c7"/>
            <circle cx="75" cy="30" r="16" fill="#0284c7"/>
            <circle cx="50" cy="75" r="18" fill="#0284c7"/>
        </svg>
    </div>

    <!-- ── 3. FOOTER BAR RESMI PT. MEDIA SOLUSI NETWORK ── -->
    <div class="msn-letterhead-footer">
        <table style="width: 100%; border-collapse: collapse; background: #0c2033; border-top: 2.5px solid #00d2ff; color: #ffffff; padding: 4px 12px; font-size: 6.2pt;">
            <tr>
                <td style="width: 32%; vertical-align: middle; padding: 2px 6px; border-right: 1px solid rgba(255,255,255,0.12);">
                    <div style="color: #00d2ff; font-weight: 800; font-size: 6.8pt; margin-bottom: 1px; letter-spacing: 0.3px;">OFFICE</div>
                    <div style="color: #cbd5e1; line-height: 1.25;">
                        Jl. Raya Leuwigajah No. 223 Kel. Utama, Kec. Cimahi Selatan, Kota Cimahi, 40533
                    </div>
                </td>
                <td style="width: 33%; vertical-align: middle; padding: 2px 6px; border-right: 1px solid rgba(255,255,255,0.12);">
                    <div style="color: #00d2ff; font-weight: 800; font-size: 6.8pt; margin-bottom: 1px; letter-spacing: 0.3px;">OPERATIONAL</div>
                    <div style="color: #cbd5e1; line-height: 1.25;">
                        Jl. Reog No 18, Kel. Turangga, Kec. Lengkong, Kota Bandung, 40264
                    </div>
                </td>
                <td style="width: 18%; vertical-align: middle; padding: 2px 6px; text-align: center; border-right: 1px solid rgba(255,255,255,0.12);">
                    <div style="color: #ffffff; font-weight: 700; font-size: 6.8pt; line-height: 1.3;">
                        0896-9662-9955<br>
                        (022)-7303384
                    </div>
                </td>
                <td style="width: 17%; vertical-align: middle; padding: 2px 6px; text-align: right;">
                    <div style="color: #00d2ff; font-weight: 700; font-size: 6.8pt;">ptmsn.co.id</div>
                    <div style="color: #cbd5e1; font-size: 6.2pt; margin-top: 1px;">info@ptmsn.co.id</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- ── 4. KONTEN DOKUMEN BERITA ACARA ── -->
    <table class="doc-header-table">
        <tr>
            <td style="vertical-align: middle;">
                <div class="doc-title">BERITA ACARA PENANGANAN GANGGUAN</div>
                <div class="doc-sub">INCIDENT REPORT &bull; CUSTOMER COPY</div>
            </td>
            <td style="vertical-align: middle; text-align: right;">
                <div class="doc-no-badge">{{ $tiket->no_tiket }}</div>
            </td>
        </tr>
    </table>

    <div class="status-box">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="width: 70%;">
                    <div style="font-size: 7.5pt; color: #64748b; font-weight: bold; text-transform: uppercase;">Status Penyelesaian Insiden:</div>
                    <div style="font-size: 10.5pt; font-weight: bold; color: #0d9488; margin-top: 1px;">
                        @if($tiket->status === 'CLOSE')
                            ✓ SELESAI &bull; LAYANAN NORMAL KEMBALI
                        @else
                            SELESAI DITANGANI (TAHAP CLOSING)
                        @endif
                    </div>
                </td>
                <td style="width: 30%; text-align: right;">
                    <div style="font-size: 7.5pt; color: #64748b; font-weight: bold;">Kepatuhan SLA:</div>
                    <div style="margin-top: 1px;">
                        @if($tiket->sla_status === 'TEPAT')
                            <span class="badge badge-success">MEMENUHI SLA</span>
                        @elseif($tiket->sla_status === 'LEBIH')
                            <span class="badge badge-danger">MELEBIHI SLA</span>
                        @else
                            <span class="badge badge-primary">SESUAI TARGET</span>
                        @endif
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- ── 1. INFORMASI LAYANAN & GANGGUAN ── -->
    <div class="section-title">1. Informasi Gangguan &amp; Layanan</div>
    <table class="info-grid">
        <tr>
            <td class="info-label">Nomor Tiket / Referensi</td>
            <td class="info-colon">:</td>
            <td class="info-val"><strong style="font-family: monospace; font-size: 8.5pt;">{{ $tiket->no_tiket }}</strong></td>
        </tr>
        <tr>
            <td class="info-label">Segmen Link Layanan</td>
            <td class="info-colon">:</td>
            <td class="info-val"><strong>{{ $tiket->backbone_segment }}</strong></td>
        </tr>
        <tr>
            <td class="info-label">Status Dampak Layanan</td>
            <td class="info-colon">:</td>
            <td class="info-val">{{ $tiket->status_link_impact }}</td>
        </tr>
        <tr>
            <td class="info-label">Waktu Mulai Gangguan (Open)</td>
            <td class="info-colon">:</td>
            <td class="info-val">{{ $tiket->tanggal_open ? $tiket->tanggal_open->translatedFormat('l, d F Y - H:i') . ' WIB' : '-' }}</td>
        </tr>
        <tr>
            <td class="info-label">Waktu Pemulihan / Selesai (Close)</td>
            <td class="info-colon">:</td>
            <td class="info-val">{{ $tiket->tanggal_close ? $tiket->tanggal_close->translatedFormat('l, d F Y - H:i') . ' WIB' : ($tiket->updated_at->translatedFormat('l, d F Y - H:i') . ' WIB') }}</td>
        </tr>
        <tr>
            <td class="info-label">Total Durasi Gangguan (Downtime)</td>
            <td class="info-colon">:</td>
            <td class="info-val">
                <strong>{{ $tiket->formatted_mttr }}</strong>
                @if($tiket->mttr_minutes)
                    <span style="color: #64748b;">({{ $tiket->mttr_minutes }} Menit)</span>
                @endif
            </td>
        </tr>
        @if($tiket->titikPerbaikans && $tiket->titikPerbaikans->count() > 0)
        <tr>
            <td class="info-label">Lokasi / Titik Penanganan</td>
            <td class="info-colon">:</td>
            <td class="info-val">{{ $tiket->titikPerbaikans->pluck('nama_titik')->join(', ') }}</td>
        </tr>
        @endif
    </table>

    <!-- ── 2. AKAR MASALAH & TINDAKAN PERBAIKAN ── -->
    <div class="section-title">2. Temuan Masalah &amp; Tindakan Perbaikan (Root Cause &amp; Action)</div>
    <table class="info-grid">
        <tr>
            <td class="info-label">Deskripsi / Indikasi Awal</td>
            <td class="info-colon">:</td>
            <td class="info-val">{{ $tiket->deskripsi ?: 'Laporan gangguan transmisi fiber optik pada segmen terkait.' }}</td>
        </tr>
        <tr>
            <td class="info-label">Akar Masalah (Root Cause)</td>
            <td class="info-colon">:</td>
            <td class="info-val" style="color: #b91c1c; font-weight: bold;">
                {{ $tiket->resume?->problem_temuan ?: 'Terjadi kendala pada jalur kabel fiber optik di lapangan.' }}
            </td>
        </tr>
        <tr>
            <td class="info-label">Tindakan Perbaikan (Action Taken)</td>
            <td class="info-colon">:</td>
            <td class="info-val" style="white-space: pre-line;">{{ $tiket->resume?->action ?: 'Tim teknis telah melakukan penelusuran, perbaikan kabel di titik gangguan, pengelasan (splicing) serta pengukuran kembali redaman optik hingga normal.' }}</td>
        </tr>
        @if($tiket->resume?->catatan_tambahan)
        <tr>
            <td class="info-label">Catatan Pemeliharaan</td>
            <td class="info-colon">:</td>
            <td class="info-val">{{ $tiket->resume->catatan_tambahan }}</td>
        </tr>
        @endif
        <tr>
            <td class="info-label">Kondisi Akhir Link</td>
            <td class="info-colon">:</td>
            <td class="info-val" style="color: #15803d; font-weight: bold;">
                ✓ Redaman normal, link transmisi aktif dan beroperasi normal (Link UP).
            </td>
        </tr>
    </table>

    <!-- ── 3. DOKUMENTASI FOTO PERBAIKAN LAPANGAN ── -->
    @if($tiket->dokumentasis && $tiket->dokumentasis->count() > 0)
    <div class="section-title">3. Dokumentasi Foto Lapangan (Field Evidence)</div>
    @php
        $selectedDocs = $tiket->dokumentasis->take(3);
    @endphp
    <table class="doc-gallery-table">
        <tr>
            @foreach($selectedDocs as $doc)
            @php
                $resolvedPath = null;
                if (!empty($doc->file_path)) {
                    $candidates = [
                        storage_path('app/public/' . $doc->file_path),
                        public_path('storage/' . $doc->file_path),
                        public_path($doc->file_path),
                        storage_path('app/' . $doc->file_path),
                        public_path('uploads/' . $doc->file_path),
                        base_path($doc->file_path),
                        $doc->file_path,
                    ];
                    foreach ($candidates as $cand) {
                        if ($cand && file_exists($cand) && !is_dir($cand)) {
                            $resolvedPath = $cand;
                            break;
                        }
                    }
                }
                $imgBase64 = null;
                if ($resolvedPath) {
                    $mime = mime_content_type($resolvedPath) ?: 'image/jpeg';
                    $imgBase64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($resolvedPath));
                }
            @endphp
            <td>
                <div class="doc-img-card">
                    @if($imgBase64)
                        <img src="{{ $imgBase64 }}" alt="Dokumentasi" class="doc-img">
                    @else
                        <div style="height: 75px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; font-size: 7.5pt; color: #94a3b8;">
                            Foto Dokumentasi Terlampir
                        </div>
                    @endif
                    <div class="doc-caption">
                        <strong>{{ $doc->kategori_label ?? $doc->kategori }}</strong><br>
                        {{ $doc->keterangan ?: 'Dokumentasi perbaikan lapangan' }}
                    </div>
                </div>
            </td>
            @endforeach
            @for($i = count($selectedDocs); $i < 3; $i++)
            <td style="width: 33.33%;"></td>
            @endfor
        </tr>
    </table>
    @endif

    <!-- ── 4. LEMBAR PENGESAHAN ── -->
    <table class="signature-table">
        <tr>
            <td class="signature-box">
                <div style="color: #64748b; font-size: 7.8pt;">Penyedia Layanan (Service Provider):</div>
                <div style="font-weight: bold; margin-top: 2px;">PT MEDIA SOLUSI NETWORK</div>
                <div class="sign-space"></div>
                <div><strong>( {{ $tiket->closer?->name ?? ($tiket->creator?->name ?? 'Helpdesk / NOC MSN') }} )</strong></div>
                <div style="font-size: 7.2pt; color: #64748b;">Network Operation Center (NOC)</div>
            </td>
            <td class="signature-box">
                <div style="color: #64748b; font-size: 7.8pt;">Penerima Laporan / Pelanggan:</div>
                <div style="font-weight: bold; margin-top: 2px;">PERWAKILAN PELANGGAN / MITRA</div>
                <div class="sign-space"></div>
                <div><strong>( .................................................... )</strong></div>
                <div style="font-size: 7.2pt; color: #64748b;">PIC Operasional Pelanggan</div>
            </td>
        </tr>
    </table>

</body>
</html>
