<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekapitulasi Handover & Oper Shift</title>
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
            color: #7c3aed;
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
            margin-top: 2px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
        }
        .data-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-weight: bold;
            text-align: left;
            padding: 5px 6px;
            border: 1px solid #0f172a;
            text-transform: uppercase;
            font-size: 7.5pt;
        }
        .data-table td {
            padding: 4.5px 6px;
            border: 1px solid #e2e8f0;
            vertical-align: top;
        }
        .data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .badge {
            display: inline-block;
            padding: 2px 4px;
            font-size: 7pt;
            font-weight: bold;
            border-radius: 3px;
            text-align: center;
        }
        .badge-pagi { background-color: #fef3c7; color: #b45309; }
        .badge-siang { background-color: #e0f2fe; color: #0369a1; }
        .badge-malam { background-color: #f3e8ff; color: #6b21a8; }
        .badge-status { background-color: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; }

        .footer-note {
            margin-top: 15px;
            font-size: 7.5pt;
            color: #64748b;
            text-align: right;
            border-top: 1px solid #e2e8f0;
            padding-top: 4px;
        }
    </style>
</head>
<body>

    <!-- Header Table -->
    <table class="header-table">
        <tr>
            <td style="width: 55%;">
                <div class="company-title">PT MEGA SIBER NUSANTARA (MSN)</div>
                <div class="company-sub">Laporan Audit &amp; Serah Terima (Handover) Shift Lapangan</div>
            </td>
            <td style="width: 45%;">
                <div class="doc-title">REKAPITULASI OPER SHIFT TIKET</div>
                <div style="font-size: 8pt; color: #64748b; text-align: right;">
                    Periode: 
                    @if(!empty($filters['start_date']) || !empty($filters['end_date']))
                        {{ $filters['start_date'] ?? 'Awal' }} s/d {{ $filters['end_date'] ?? 'Sekarang' }}
                    @else
                        Semua Periode Data
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <!-- 4 Summary Metric Boxes -->
    <table class="summary-boxes">
        <tr>
            <td style="width: 25%; padding-right: 4px;">
                <div class="summary-card">
                    <div class="summary-label">TOTAL HANDOVER</div>
                    <div class="summary-val">{{ $metrics['total_handover'] }}</div>
                </div>
            </td>
            <td style="width: 25%; padding-right: 4px;">
                <div class="summary-card">
                    <div class="summary-label">SERAH KE SHIFT 1 (PAGI)</div>
                    <div class="summary-val" style="color: #d97706;">{{ $metrics['shift_pagi'] }}</div>
                </div>
            </td>
            <td style="width: 25%; padding-right: 4px;">
                <div class="summary-card">
                    <div class="summary-label">SERAH KE SHIFT 2 (SIANG)</div>
                    <div class="summary-val" style="color: #0284c7;">{{ $metrics['shift_siang'] }}</div>
                </div>
            </td>
            <td style="width: 25%;">
                <div class="summary-card">
                    <div class="summary-label">SERAH KE SHIFT 3 (MALAM)</div>
                    <div class="summary-val" style="color: #7c3aed;">{{ $metrics['shift_malam'] }}</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Data Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 3%; text-align: center;">No</th>
                <th style="width: 11%;">Waktu Handover</th>
                <th style="width: 14%;">No Tiket</th>
                <th style="width: 16%;">Segment Backbone</th>
                <th style="width: 8%;">Status</th>
                <th style="width: 15%;">Perpindahan Shift</th>
                <th style="width: 15%;">Petugas (From ➔ To)</th>
                <th style="width: 18%;">Catatan Serah Terima</th>
            </tr>
        </thead>
        <tbody>
            @forelse($handovers as $index => $h)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td style="font-family: monospace;">{{ $h->created_at->format('d/m/Y H:i') }}</td>
                <td style="font-family: monospace; font-weight: bold; color: #2563eb;">{{ $h->tiket?->no_tiket ?? '-' }}</td>
                <td>{{ $h->tiket?->backbone_segment ?? '-' }}</td>
                <td><span class="badge badge-status">{{ $h->tiket?->status ?? '-' }}</span></td>
                <td>
                    <span class="badge {{ str_contains($h->shift_from, '1') ? 'badge-pagi' : (str_contains($h->shift_from, '2') ? 'badge-siang' : 'badge-malam') }}">
                        {{ explode('(', $h->shift_from)[0] }}
                    </span>
                    ➔
                    <span class="badge {{ str_contains($h->shift_to, '1') ? 'badge-pagi' : (str_contains($h->shift_to, '2') ? 'badge-siang' : 'badge-malam') }}">
                        {{ explode('(', $h->shift_to)[0] }}
                    </span>
                </td>
                <td>
                    <strong>{{ $h->userFrom?->name ?? 'Sistem' }}</strong> ➔ <strong>{{ $h->userTo?->name ?? 'PIC Shift Baru' }}</strong>
                </td>
                <td>{{ $h->catatan_handover }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align: center; color: #94a3b8; padding: 15px;">
                    Tidak ada data serah terima shift pada periode ini.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer-note">
        Dicetak secara otomatis dari Sistem Tiketing Gangguan PT Mega Siber Nusantara pada {{ $printDate }}
    </div>

</body>
</html>
