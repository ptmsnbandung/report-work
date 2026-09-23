<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Gangguan - {{ $tiket->no_tiket }}</title>
    <style>
        @page {
            margin: 20px 25px 25px 25px;
            size: A4 portrait;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            font-size: 9pt;
            line-height: 1.4;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .company-title {
            font-size: 14pt;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .company-sub {
            font-size: 8pt;
            color: #0d9488;
            font-weight: bold;
        }
        .doc-title {
            font-size: 11pt;
            font-weight: bold;
            text-align: right;
            color: #0f172a;
        }
        .doc-no {
            font-size: 9pt;
            font-family: monospace;
            color: #2563eb;
            font-weight: bold;
            text-align: right;
        }

        .section-title {
            font-size: 9.5pt;
            font-weight: bold;
            color: #ffffff;
            background-color: #0f172a;
            padding: 4px 8px;
            margin-top: 10px;
            margin-bottom: 6px;
            border-radius: 3px;
            text-transform: uppercase;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #cbd5e1;
            padding: 4px 6px;
            font-size: 8.5pt;
        }
        table.data-table th {
            background-color: #f1f5f9;
            color: #0f172a;
            font-weight: bold;
            text-align: left;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 7.5pt;
            font-weight: bold;
            border-radius: 3px;
            text-transform: uppercase;
        }
        .badge-success { background-color: #dcfce7; color: #15803d; border: 1px solid #86efac; }
        .badge-danger  { background-color: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; }
        .badge-primary { background-color: #dbeafe; color: #1d4ed8; border: 1px solid #93c5fd; }
        .badge-gray    { background-color: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }

        .info-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }
        .info-grid td {
            padding: 3px 4px;
            font-size: 8.5pt;
            vertical-align: top;
        }
        .info-label {
            font-weight: bold;
            color: #475569;
            width: 25%;
        }
        .info-colon { width: 2%; }
        .info-val { width: 73%; color: #0f172a; }

        .signature-table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        .signature-box {
            width: 33.33%;
            text-align: center;
            vertical-align: top;
            font-size: 8.5pt;
        }
        .sign-space {
            height: 45px;
        }

        .footer-note {
            margin-top: 15px;
            font-size: 7pt;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 4px;
            text-align: right;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>

    <!-- ── HEADER ── -->
    <table class="header-table">
        <tr>
            <td style="width: 55%;">
                <div class="company-title">PT MEDIA SOLUSI NETWORK (MSN)</div>
                <div class="company-sub">FIBER OPTIC BACKBONE & NETWORK OPERATION CENTER</div>
            </td>
            <td style="width: 45%;" class="text-right">
                <div class="doc-title">BERITA ACARA GANGGUAN</div>
                <div class="doc-no">{{ $tiket->no_tiket }}</div>
            </td>
        </tr>
    </table>

    <!-- ── 1. INFORMASI UMUM & STATUS SLA ── -->
    <div class="section-title">1. Informasi Gangguan & Kepatuhan SLA</div>
    <table class="info-grid">
        <tr>
            <td class="info-label">Status Link Impact</td>
            <td class="info-colon">:</td>
            <td class="info-val"><strong>{{ $tiket->status_link_impact }}</strong></td>
        </tr>
        <tr>
            <td class="info-label">Segment Backbone</td>
            <td class="info-colon">:</td>
            <td class="info-val">{{ $tiket->backbone_segment }}</td>
        </tr>
        <tr>
            <td class="info-label">Deskripsi Insiden</td>
            <td class="info-colon">:</td>
            <td class="info-val">{{ $tiket->deskripsi ?: '-' }}</td>
        </tr>
        <tr>
            <td class="info-label">Waktu Open Tiket</td>
            <td class="info-colon">:</td>
            <td class="info-val">{{ $tiket->tanggal_open ? $tiket->tanggal_open->translatedFormat('d F Y H:i') . ' WIB' : '-' }} (Oleh: {{ $tiket->creator?->name ?? 'System' }})</td>
        </tr>
        <tr>
            <td class="info-label">Waktu Closing Tiket</td>
            <td class="info-colon">:</td>
            <td class="info-val">{{ $tiket->tanggal_close ? $tiket->tanggal_close->translatedFormat('d F Y H:i') . ' WIB' : 'Belum Ditutup' }} (Oleh: {{ $tiket->closer?->name ?? '-' }})</td>
        </tr>
        <tr>
            <td class="info-label">Durasi MTTR</td>
            <td class="info-colon">:</td>
            <td class="info-val">
                <strong>{{ $tiket->formatted_mttr }}</strong> ({{ $tiket->mttr_minutes ?? 0 }} Menit)
            </td>
        </tr>
        <tr>
            <td class="info-label">Target SLA & Status</td>
            <td class="info-colon">:</td>
            <td class="info-val">
                Target: {{ $tiket->formatted_sla_target }} |
                Status:
                @if($tiket->sla_status === 'TEPAT')
                    <span class="badge badge-success">TEPAT SLA</span>
                @elseif($tiket->sla_status === 'LEBIH')
                    <span class="badge badge-danger">MELEBIHI SLA</span>
                @else
                    <span class="badge badge-gray">PROSES / N/A</span>
                @endif
            </td>
        </tr>
    </table>

    <!-- ── 2. RESUME PEKERJAAN ── -->
    <div class="section-title">2. Resume Akhir Pekerjaan & Tindakan Perbaikan</div>
    <table class="info-grid">
        <tr>
            <td class="info-label">Tim OM Pelaksana</td>
            <td class="info-colon">:</td>
            <td class="info-val">
                @if($tiket->resume && is_array($tiket->resume->team_om))
                    {{ implode(', ', $tiket->resume->team_om) }}
                @else
                    {{ $tiket->resume?->team_om ?? '-' }}
                @endif
            </td>
        </tr>
        <tr>
            <td class="info-label">Problem / Temuan Masalah</td>
            <td class="info-colon">:</td>
            <td class="info-val" style="color: #b91c1c; font-weight: bold;">
                {{ $tiket->resume?->problem_temuan ?? '-' }}
            </td>
        </tr>
        <tr>
            <td class="info-label">Action / Tindakan Perbaikan</td>
            <td class="info-colon">:</td>
            <td class="info-val" style="white-space: pre-line;">
                {{ $tiket->resume?->action ?? '-' }}
            </td>
        </tr>
        @if($tiket->resume?->catatan_tambahan)
        <tr>
            <td class="info-label">Catatan Tambahan</td>
            <td class="info-colon">:</td>
            <td class="info-val">{{ $tiket->resume->catatan_tambahan }}</td>
        </tr>
        @endif
    </table>

    <!-- ── 3. MATERIAL & TITIK TAGGING ── -->
    <div class="section-title">3. Penggunaan Material & Titik Perbaikan</div>
    <table style="width: 100%;">
        <tr>
            <td style="width: 50%; vertical-align: top; padding-right: 5px;">
                <strong style="font-size: 8pt; color: #475569;">Material yang Digunakan:</strong>
                <table class="data-table" style="margin-top: 3px;">
                    <thead>
                        <tr>
                            <th>Material</th>
                            <th style="text-align: right;">Jumlah</th>
                            <th>Satuan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tiket->materials as $m)
                        <tr>
                            <td>{{ $m->nama_material }}</td>
                            <td style="text-align: right; font-family: monospace;">{{ $m->jumlah }}</td>
                            <td>{{ $m->satuan }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" style="text-align: center; color: #94a3b8;">Tidak ada material khusus</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </td>
            <td style="width: 50%; vertical-align: top; padding-left: 5px;">
                <strong style="font-size: 8pt; color: #475569;">Titik Tagging / Joint Closure:</strong>
                <table class="data-table" style="margin-top: 3px;">
                    <thead>
                        <tr>
                            <th>Titik</th>
                            <th>Koordinat GPS</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tiket->titikPerbaikans as $tp)
                        <tr>
                            <td><strong>{{ $tp->nama_titik }}</strong></td>
                            <td style="font-family: monospace; font-size: 7.5pt;">{{ $tp->latitude }}, {{ $tp->longitude }}</td>
                            <td>{{ $tp->keterangan ?: '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" style="text-align: center; color: #94a3b8;">Tidak ada titik tagging</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </td>
        </tr>
    </table>

    <!-- ── 4. MANUVER CORE OPTIK ── -->
    @if($tiket->manuverCores->count() > 0)
    <div class="section-title">4. Alokasi & Manuver Core Optik</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 15%;">Titik</th>
                <th style="width: 15%;">Tipe</th>
                <th style="width: 35%;">Core Asal / Input</th>
                <th style="width: 35%;">Core Tujuan / Output</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tiket->manuverCores as $mc)
            <tr>
                <td><strong>{{ $mc->titik }}</strong></td>
                <td>
                    <span class="badge {{ $mc->tipe === 'SEBELUM' ? 'badge-gray' : 'badge-success' }}">
                        {{ $mc->tipe }}
                    </span>
                </td>
                <td style="font-family: monospace;">{{ $mc->core_asal }}</td>
                <td style="font-family: monospace;">{{ $mc->core_tujuan }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- ── 5. TIMELINE KRONOLOGIS LAPANGAN ── -->
    <div class="section-title">5. Timeline Kronologis Koordinasi Lapangan</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 22%;">Waktu (WIB)</th>
                <th style="width: 23%;">PIC / Teknis</th>
                <th style="width: 55%;">Informasi & Catatan Lapangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tiket->kronologis as $krono)
            <tr>
                <td style="font-family: monospace; font-size: 7.5pt;">
                    {{ $krono->timestamp->format('d/m/Y H:i') }}
                </td>
                <td>{{ $krono->user?->name ?? 'User' }} <small style="color: #64748b;">({{ $krono->user?->role }})</small></td>
                <td>{{ $krono->informasi }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="3" style="text-align: center; color: #94a3b8;">Belum ada update kronologis</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- ── 6. TANDA TANGAN PENGESAHAN ── -->
    <table class="signature-table">
        <tr>
            <td class="signature-box">
                <div>Dibuat Oleh (Helpdesk / NOC)</div>
                <div class="sign-space"></div>
                <div><strong>({{ $tiket->creator?->name ?? 'Helpdesk NOC' }})</strong></div>
                <div style="font-size: 7.5pt; color: #64748b;">PT MSN</div>
            </td>
            <td class="signature-box">
                <div>Tim Pelaksana Lapangan (OM)</div>
                <div class="sign-space"></div>
                <div>
                    <strong>(
                    @if($tiket->resume && is_array($tiket->resume->team_om) && count($tiket->resume->team_om) > 0)
                        {{ $tiket->resume->team_om[0] }}
                    @else
                        {{ $tiket->closer?->name ?? 'Leader Teknis' }}
                    @endif
                    )</strong>
                </div>
                <div style="font-size: 7.5pt; color: #64748b;">Teknis Backbone MSN</div>
            </td>
            <td class="signature-box">
                <div>Mengetahui (Manager Operasional)</div>
                <div class="sign-space"></div>
                <div><strong>( Delli Digital )</strong></div>
                <div style="font-size: 7.5pt; color: #64748b;">Business Development / NOC</div>
            </td>
        </tr>
    </table>

    <div class="footer-note">
        Dokumen ini dihasilkan secara otomatis oleh Sistem Tiketing Gangguan PT MSN pada {{ $printDate }}
    </div>

</body>
</html>
