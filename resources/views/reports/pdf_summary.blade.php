<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekapitulasi Tiket Gangguan Backbone</title>
    <style>
        @page {
            margin: 18px 22px 20px 22px;
            size: A4 landscape;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #0f172a;
            font-size: 8pt;
            line-height: 1.35;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }
        .company-sub {
            font-size: 7.8pt;
            color: #0d9488;
            font-weight: 700;
            letter-spacing: 0.3px;
        }
        .company-addr {
            font-size: 7pt;
            color: #64748b;
            margin-top: 1px;
        }
        .doc-title {
            font-size: 11pt;
            font-weight: 800;
            text-align: right;
            color: #0f172a;
            letter-spacing: 0.4px;
        }
        .doc-no-badge {
            display: inline-block;
            font-size: 7.8pt;
            font-family: monospace;
            color: #0369a1;
            font-weight: 700;
            text-align: right;
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            padding: 2px 8px;
            border-radius: 4px;
            margin-top: 3px;
        }

        .summary-boxes {
            width: 100%;
            margin-bottom: 10px;
            border-collapse: separate;
            border-spacing: 6px 0;
        }
        .summary-card {
            border: 1px solid #e2e8f0;
            background-color: #f8fafc;
            padding: 6px 8px;
            border-radius: 5px;
            text-align: center;
        }
        .summary-label {
            font-size: 6.8pt;
            text-transform: uppercase;
            color: #64748b;
            font-weight: 700;
            letter-spacing: 0.3px;
        }
        .summary-val {
            font-size: 11.5pt;
            font-weight: 800;
            color: #0f172a;
            margin-top: 2px;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #e2e8f0;
            padding: 4px 6px;
            font-size: 7.5pt;
        }
        table.data-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-weight: 700;
            font-size: 7.2pt;
            text-align: left;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        table.data-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }

        .badge {
            display: inline-block;
            padding: 2px 5px;
            font-size: 6.5pt;
            font-weight: 700;
            border-radius: 3px;
            text-transform: uppercase;
            letter-spacing: 0.2px;
        }
        .badge-success { background-color: #dcfce7; color: #15803d; border: 1px solid #86efac; }
        .badge-danger  { background-color: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; }
        .badge-warning { background-color: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
        .badge-primary { background-color: #dbeafe; color: #1d4ed8; border: 1px solid #93c5fd; }

        .footer-note {
            margin-top: 10px;
            font-size: 7pt;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 4px;
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

    <!-- ── HEADER ── -->
    <table class="header-table">
        <tr>
            <td style="width: 60%; vertical-align: middle;">
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" alt="MSN Logo" style="height: 38px; max-width: 190px; width: auto; display: block; margin-bottom: 4px;">
                @endif
                <div class="company-sub">LAPORAN REKAPITULASI PENANGANAN GANGGUAN BACKBONE</div>
                <div class="company-addr">Fiber Optic Backbone &amp; Network Operation Center</div>
            </td>
            <td style="width: 40%; vertical-align: middle;" align="right">
                <div class="doc-title">SUMMARY REPORT &amp; SLA COMPLIANCE</div>
                <div class="doc-no-badge">Dicetak: {{ $printDate }}</div>
            </td>
        </tr>
    </table>

    <!-- ── SUMMARY KPI CARDS ── -->
    <table class="summary-boxes">
        <tr>
            <td style="width: 16.66%;">
                <div class="summary-card">
                    <div class="summary-label">Total Tiket</div>
                    <div class="summary-val">{{ $metrics['total_tiket'] }}</div>
                </div>
            </td>
            <td style="width: 16.66%;">
                <div class="summary-card">
                    <div class="summary-label">Open / Proses</div>
                    <div class="summary-val" style="color: #d97706;">{{ $metrics['total_open'] + $metrics['total_proses'] }}</div>
                </div>
            </td>
            <td style="width: 16.66%;">
                <div class="summary-card">
                    <div class="summary-label">Tiket Closed</div>
                    <div class="summary-val" style="color: #16a34a;">{{ $metrics['total_close'] }}</div>
                </div>
            </td>
            <td style="width: 16.66%;">
                <div class="summary-card">
                    <div class="summary-label">Rata-rata MTTR</div>
                    <div class="summary-val" style="color: #2563eb;">{{ $metrics['avg_mttr_formatted'] }}</div>
                </div>
            </td>
            <td style="width: 16.66%;">
                <div class="summary-card">
                    <div class="summary-label">Tepat SLA</div>
                    <div class="summary-val" style="color: #16a34a;">{{ $metrics['total_tepat'] }}</div>
                </div>
            </td>
            <td style="width: 16.66%;">
                <div class="summary-card">
                    <div class="summary-label">SLA Compliance</div>
                    <div class="summary-val" style="color: #0d9488;">{{ $metrics['sla_compliance_rate'] }}%</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- ── TABLE DATA ── -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 3%; text-align: center;">No</th>
                <th style="width: 12%;">No Tiket</th>
                <th style="width: 18%;">Link Impact</th>
                <th style="width: 15%;">Segment Backbone</th>
                <th style="width: 7%; text-align: center;">Status</th>
                <th style="width: 11%;">Waktu Open</th>
                <th style="width: 11%;">Waktu Close</th>
                <th style="width: 9%; text-align: right;">MTTR</th>
                <th style="width: 8%; text-align: right;">Target SLA</th>
                <th style="width: 6%; text-align: center;">SLA</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tikets as $index => $t)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td style="font-family: monospace; font-weight: 700; color: #0284c7;">{{ $t->no_tiket }}</td>
                <td>{{ $t->status_link_impact }}</td>
                <td>{{ $t->backbone_segment }}</td>
                <td style="text-align: center;">
                    <span class="badge {{ $t->status === 'CLOSE' ? 'badge-success' : ($t->status === 'PROSES' ? 'badge-warning' : 'badge-danger') }}">
                        {{ $t->status }}
                    </span>
                </td>
                <td style="font-family: monospace;">{{ $t->tanggal_open ? $t->tanggal_open->format('d/m/Y H:i') : '-' }}</td>
                <td style="font-family: monospace;">{{ $t->tanggal_close ? $t->tanggal_close->format('d/m/Y H:i') : '-' }}</td>
                <td style="text-align: right; font-family: monospace; font-weight: 700;">{{ $t->formatted_mttr }}</td>
                <td style="text-align: right; font-family: monospace; color: #64748b;">{{ $t->formatted_sla_target }}</td>
                <td style="text-align: center;">
                    @if($t->sla_status === 'TEPAT')
                        <span class="badge badge-success">TEPAT</span>
                    @elseif($t->sla_status === 'LEBIH')
                        <span class="badge badge-danger">LEBIH</span>
                    @else
                        <span class="badge badge-primary">-</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="10" style="text-align: center; color: #94a3b8; padding: 15px;">Tidak ada data tiket sesuai filter.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer-note">
        Dokumen ini dihasilkan secara otomatis oleh Sistem Tiketing Gangguan PT MSN pada {{ $printDate }}
    </div>

</body>
</html>
