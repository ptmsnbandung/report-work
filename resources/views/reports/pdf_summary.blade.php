<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekapitulasi Tiket Gangguan Backbone</title>
    <style>
        @page {
            margin: 20px 25px 25px 25px;
            size: A4 landscape;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            font-size: 8.5pt;
            line-height: 1.3;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }
        .company-title {
            font-size: 13pt;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
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

        .summary-boxes {
            width: 100%;
            margin-bottom: 12px;
            border-collapse: collapse;
        }
        .summary-card {
            border: 1px solid #cbd5e1;
            background-color: #f8fafc;
            padding: 6px 10px;
            border-radius: 4px;
            text-align: center;
        }
        .summary-label {
            font-size: 7pt;
            text-transform: uppercase;
            color: #64748b;
            font-weight: bold;
        }
        .summary-val {
            font-size: 12pt;
            font-weight: bold;
            color: #0f172a;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #cbd5e1;
            padding: 4px 5px;
            font-size: 7.5pt;
        }
        table.data-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-weight: bold;
            text-align: left;
        }

        .badge {
            display: inline-block;
            padding: 2px 4px;
            font-size: 6.5pt;
            font-weight: bold;
            border-radius: 2px;
            text-transform: uppercase;
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
</head>
<body>

    <!-- ── HEADER ── -->
    <table class="header-table">
        <tr>
            <td style="width: 60%;">
                <div class="company-title">PT MEDIA SOLUSI NETWORK (MSN)</div>
                <div class="company-sub">LAPORAN REKAPITULASI PENANGANAN GANGGUAN BACKBONE</div>
            </td>
            <td style="width: 40%;" class="text-right">
                <div class="doc-title">SUMMARY REPORT & SLA COMPLIANCE</div>
                <div style="font-size: 7.5pt; color: #64748b;">
                    Dicetak: {{ $printDate }}
                </div>
            </td>
        </tr>
    </table>

    <!-- ── SUMMARY KPI CARDS ── -->
    <table class="summary-boxes">
        <tr>
            <td style="width: 16.6%; padding: 2px;">
                <div class="summary-card">
                    <div class="summary-label">Total Tiket</div>
                    <div class="summary-val">{{ $metrics['total_tiket'] }}</div>
                </div>
            </td>
            <td style="width: 16.6%; padding: 2px;">
                <div class="summary-card">
                    <div class="summary-label">Open / Proses</div>
                    <div class="summary-val" style="color: #d97706;">{{ $metrics['total_open'] + $metrics['total_proses'] }}</div>
                </div>
            </td>
            <td style="width: 16.6%; padding: 2px;">
                <div class="summary-card">
                    <div class="summary-label">Tiket Closed</div>
                    <div class="summary-val" style="color: #16a34a;">{{ $metrics['total_close'] }}</div>
                </div>
            </td>
            <td style="width: 16.6%; padding: 2px;">
                <div class="summary-card">
                    <div class="summary-label">Rata-rata MTTR</div>
                    <div class="summary-val" style="color: #2563eb;">{{ $metrics['avg_mttr_formatted'] }}</div>
                </div>
            </td>
            <td style="width: 16.6%; padding: 2px;">
                <div class="summary-card">
                    <div class="summary-label">Tepat SLA</div>
                    <div class="summary-val" style="color: #16a34a;">{{ $metrics['total_tepat'] }}</div>
                </div>
            </td>
            <td style="width: 16.6%; padding: 2px;">
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
                <th style="width: 3%;">No</th>
                <th style="width: 12%;">No Tiket</th>
                <th style="width: 18%;">Link Impact</th>
                <th style="width: 14%;">Segment Backbone</th>
                <th style="width: 7%;">Status</th>
                <th style="width: 11%;">Waktu Open</th>
                <th style="width: 11%;">Waktu Close</th>
                <th style="width: 9%; text-align: right;">MTTR</th>
                <th style="width: 8%; text-align: right;">Target SLA</th>
                <th style="width: 7%;">SLA</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tikets as $index => $t)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td style="font-family: monospace; font-weight: bold;">{{ $t->no_tiket }}</td>
                <td>{{ $t->status_link_impact }}</td>
                <td>{{ $t->backbone_segment }}</td>
                <td>
                    <span class="badge {{ $t->status === 'CLOSE' ? 'badge-success' : ($t->status === 'PROSES' ? 'badge-warning' : 'badge-danger') }}">
                        {{ $t->status }}
                    </span>
                </td>
                <td style="font-family: monospace;">{{ $t->tanggal_open ? $t->tanggal_open->format('d/m/Y H:i') : '-' }}</td>
                <td style="font-family: monospace;">{{ $t->tanggal_close ? $t->tanggal_close->format('d/m/Y H:i') : '-' }}</td>
                <td style="text-align: right; font-family: monospace; font-weight: bold;">{{ $t->formatted_mttr }}</td>
                <td style="text-align: right; font-family: monospace;">{{ $t->formatted_sla_target }}</td>
                <td>
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
