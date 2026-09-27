<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Berita Acara Gangguan Pelanggan - {{ $tiket->no_tiket }}</title>
    <style>
        @page {
            margin: 22px 28px 25px 28px;
            size: A4 portrait;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            font-size: 9pt;
            line-height: 1.45;
        }
        .header-table {
            width: 100%;
            border-bottom: 2.5px solid #0f172a;
            padding-bottom: 10px;
            margin-bottom: 14px;
        }
        .company-title {
            font-size: 13.5pt;
            font-weight: bold;
            color: #0f172a;
            letter-spacing: 0.5px;
        }
        .company-sub {
            font-size: 8pt;
            color: #0d9488;
            font-weight: bold;
            margin-top: 2px;
        }
        .company-addr {
            font-size: 7.5pt;
            color: #64748b;
            margin-top: 1px;
        }
        .doc-title {
            font-size: 11pt;
            font-weight: bold;
            text-align: right;
            color: #0f172a;
            text-transform: uppercase;
        }
        .doc-sub {
            font-size: 8pt;
            font-weight: bold;
            text-align: right;
            color: #2563eb;
            letter-spacing: 0.3px;
        }
        .doc-no {
            font-size: 9.5pt;
            font-family: monospace;
            color: #0f172a;
            font-weight: bold;
            text-align: right;
            margin-top: 2px;
        }

        .section-title {
            font-size: 9.5pt;
            font-weight: bold;
            color: #ffffff;
            background-color: #0f172a;
            padding: 4px 8px;
            margin-top: 12px;
            margin-bottom: 7px;
            border-radius: 3px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .info-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }
        .info-grid td {
            padding: 4px 5px;
            font-size: 8.5pt;
            vertical-align: top;
            border-bottom: 1px solid #f1f5f9;
        }
        .info-label {
            font-weight: bold;
            color: #475569;
            width: 28%;
        }
        .info-colon { width: 2%; text-align: center; }
        .info-val { width: 70%; color: #0f172a; }

        .status-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 4px solid #0d9488;
            padding: 8px 10px;
            margin-top: 6px;
            margin-bottom: 10px;
            border-radius: 0 4px 4px 0;
        }

        .badge {
            display: inline-block;
            padding: 2px 7px;
            font-size: 7.5pt;
            font-weight: bold;
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
            margin-top: 6px;
            margin-bottom: 10px;
        }
        .doc-gallery-table td {
            width: 33.33%;
            padding: 4px;
            vertical-align: top;
            text-align: center;
        }
        .doc-img-card {
            border: 1px solid #cbd5e1;
            padding: 4px;
            background: #ffffff;
            border-radius: 3px;
        }
        .doc-img {
            max-width: 100%;
            max-height: 110px;
            height: auto;
            border-radius: 2px;
            display: block;
            margin: 0 auto;
        }
        .doc-caption {
            font-size: 7pt;
            color: #475569;
            margin-top: 3px;
            line-height: 1.2;
        }

        .signature-table {
            width: 100%;
            margin-top: 24px;
            border-collapse: collapse;
        }
        .signature-box {
            width: 50%;
            text-align: center;
            vertical-align: top;
            font-size: 8.5pt;
        }
        .sign-space {
            height: 55px;
        }

        .footer-note {
            margin-top: 20px;
            font-size: 7pt;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 5px;
            text-align: right;
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

    <!-- ── HEADER PERUSAHAAN ── -->
    <table class="header-table">
        <tr>
            <td style="width: 58%; vertical-align: middle;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        @if($logoBase64)
                        <td style="width: 130px; vertical-align: middle; padding-right: 10px;">
                            <img src="{{ $logoBase64 }}" alt="Logo PT MSN" style="height: 36px; max-width: 125px; width: auto;">
                        </td>
                        @endif
                        <td style="vertical-align: middle;">
                            <div class="company-title">PT MEDIA SOLUSI NETWORK (MSN)</div>
                            <div class="company-sub">FIBER OPTIC BACKBONE &amp; NETWORK OPERATION CENTER</div>
                            <div class="company-addr">Layanan Pemeliharaan &amp; Operasional Jaringan Telekomunikasi</div>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width: 42%; vertical-align: middle;" class="text-right">
                <div class="doc-title">BERITA ACARA PENANGANAN GANGGUAN</div>
                <div class="doc-sub">INCIDENT REPORT &bull; CUSTOMER COPY</div>
                <div class="doc-no">No. Ref: {{ $tiket->no_tiket }}</div>
            </td>
        </tr>
    </table>

    <div class="status-box">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="width: 70%;">
                    <div style="font-size: 8pt; color: #64748b; font-weight: bold; text-transform: uppercase;">Status Penyelesaian Insiden:</div>
                    <div style="font-size: 11pt; font-weight: bold; color: #0d9488; margin-top: 2px;">
                        @if($tiket->status === 'CLOSE')
                            ✓ SELESAI &bull; LAYANAN NORMAL KEMBALI
                        @else
                            SELESAI DITANGANI (TAHAP CLOSING)
                        @endif
                    </div>
                </td>
                <td style="width: 30%; text-align: right;">
                    <div style="font-size: 8pt; color: #64748b; font-weight: bold;">Kepatuhan SLA:</div>
                    <div style="margin-top: 2px;">
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
            <td class="info-val"><strong style="font-family: monospace; font-size: 9pt;">{{ $tiket->no_tiket }}</strong></td>
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
                        <div style="height: 80px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; font-size: 7.5pt; color: #94a3b8;">
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
                <div style="color: #64748b; font-size: 8pt;">Penyedia Layanan (Service Provider):</div>
                <div style="font-weight: bold; margin-top: 2px;">PT MEDIA SOLUSI NETWORK</div>
                <div class="sign-space"></div>
                <div><strong>( {{ $tiket->closer?->name ?? ($tiket->creator?->name ?? 'Helpdesk / NOC MSN') }} )</strong></div>
                <div style="font-size: 7.5pt; color: #64748b;">Network Operation Center (NOC)</div>
            </td>
            <td class="signature-box">
                <div style="color: #64748b; font-size: 8pt;">Penerima Laporan / Pelanggan:</div>
                <div style="font-weight: bold; margin-top: 2px;">PERWAKILAN PELANGGAN / MITRA</div>
                <div class="sign-space"></div>
                <div><strong>( .................................................... )</strong></div>
                <div style="font-size: 7.5pt; color: #64748b;">PIC Operasional Pelanggan</div>
            </td>
        </tr>
    </table>

    <div class="footer-note">
        Dokumen Berita Acara resmi diterbitkan oleh PT MEDIA SOLUSI NETWORK pada {{ $printDate }}.
    </div>

</body>
</html>
