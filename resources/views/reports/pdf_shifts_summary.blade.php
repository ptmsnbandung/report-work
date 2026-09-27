<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekapitulasi Handover &amp; Oper Shift</title>
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
            color: #7c3aed;
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
            color: #6b21a8;
            font-weight: 700;
            text-align: right;
            background: #faf5ff;
            border: 1px solid #e9d5ff;
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

        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.5pt;
            margin-bottom: 8px;
        }
        .data-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-weight: 700;
            text-align: left;
            padding: 4.5px 6px;
            border: 1px solid #0f172a;
            text-transform: uppercase;
            font-size: 7.2pt;
            letter-spacing: 0.3px;
        }
        .data-table td {
            padding: 4px 6px;
            border: 1px solid #e2e8f0;
            vertical-align: top;
        }
        .data-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }

        .badge {
            display: inline-block;
            padding: 2px 5px;
            font-size: 6.8pt;
            font-weight: 700;
            border-radius: 3px;
            text-align: center;
            text-transform: uppercase;
        }
        .badge-pagi { background-color: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
        .badge-siang { background-color: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
        .badge-malam { background-color: #f3e8ff; color: #6b21a8; border: 1px solid #e9d5ff; }
        .badge-status { background-color: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; }

        .footer-note {
            margin-top: 10px;
            font-size: 7pt;
            color: #94a3b8;
            text-align: right;
            border-top: 1px solid #e2e8f0;
            padding-top: 4px;
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

    <!-- Header Table -->
    <table class="header-table">
        <tr>
            <td style="width: 60%; vertical-align: middle;">
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" alt="MSN Logo" style="height: 38px; max-width: 190px; width: auto; display: block; margin-bottom: 4px;">
                @endif
                <div class="company-sub">Laporan Audit &amp; Serah Terima (Handover) Shift Lapangan</div>
                <div class="company-addr">Fiber Optic Backbone &amp; Network Operation Center</div>
            </td>
            <td style="width: 40%; vertical-align: middle;" align="right">
                <div class="doc-title">REKAPITULASI OPER SHIFT TIKET</div>
                <div class="doc-no-badge">
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
            <td style="width: 25%;">
                <div class="summary-card">
                    <div class="summary-label">TOTAL HANDOVER</div>
                    <div class="summary-val">{{ $metrics['total_handover'] }}</div>
                </div>
            </td>
            <td style="width: 25%;">
                <div class="summary-card">
                    <div class="summary-label">SERAH KE SHIFT 1 (PAGI)</div>
                    <div class="summary-val" style="color: #d97706;">{{ $metrics['shift_pagi'] }}</div>
                </div>
            </td>
            <td style="width: 25%;">
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
                <th style="width: 7%; text-align: center;">Status</th>
                <th style="width: 16%; text-align: center;">Perpindahan Shift</th>
                <th style="width: 15%;">Petugas (From &rarr; To)</th>
                <th style="width: 18%;">Catatan Serah Terima</th>
            </tr>
        </thead>
        <tbody>
            @forelse($handovers as $index => $h)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td style="font-family: monospace;">{{ $h->created_at->format('d/m/Y H:i') }}</td>
                <td style="font-family: monospace; font-weight: 700; color: #0284c7;">{{ $h->tiket?->no_tiket ?? '-' }}</td>
                <td>{{ $h->tiket?->backbone_segment ?? '-' }}</td>
                <td style="text-align: center;"><span class="badge badge-status">{{ $h->tiket?->status ?? '-' }}</span></td>
                <td style="text-align: center;">
                    <span class="badge {{ str_contains($h->shift_from, '1') ? 'badge-pagi' : (str_contains($h->shift_from, '2') ? 'badge-siang' : 'badge-malam') }}">
                        {{ explode('(', $h->shift_from)[0] }}
                    </span>
                    &rarr;
                    <span class="badge {{ str_contains($h->shift_to, '1') ? 'badge-pagi' : (str_contains($h->shift_to, '2') ? 'badge-siang' : 'badge-malam') }}">
                        {{ explode('(', $h->shift_to)[0] }}
                    </span>
                </td>
                <td>
                    <strong style="color: #0f172a;">{{ $h->userFrom?->name ?? 'Sistem' }}</strong> &rarr; <strong style="color: #0f172a;">{{ $h->userTo?->name ?? 'PIC Shift Baru' }}</strong>
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
        Dicetak secara otomatis dari Sistem Tiketing Gangguan PT MSN pada {{ $printDate }}
    </div>

</body>
</html>
