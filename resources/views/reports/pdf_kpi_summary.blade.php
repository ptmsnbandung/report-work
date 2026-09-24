<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Eksekutif KPI & Evaluasi Kinerja</title>
    <style>
        @page {
            margin: 20px 25px 25px 25px;
            size: A4 portrait;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            font-size: 8.5pt;
            line-height: 1.35;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .company-title {
            font-size: 13pt;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
        }
        .company-sub {
            font-size: 8pt;
            color: #2563eb;
            font-weight: bold;
        }
        .doc-title {
            font-size: 11pt;
            font-weight: bold;
            text-align: right;
            color: #0f172a;
        }

        .section-title {
            font-size: 9pt;
            font-weight: bold;
            color: #ffffff;
            background-color: #0f172a;
            padding: 4px 8px;
            margin-top: 12px;
            margin-bottom: 6px;
            border-radius: 3px;
            text-transform: uppercase;
        }

        .summary-boxes {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        .summary-card {
            border: 1px solid #cbd5e1;
            background-color: #f8fafc;
            padding: 6px 8px;
            border-radius: 4px;
            text-align: center;
        }
        .summary-label {
            font-size: 6.8pt;
            text-transform: uppercase;
            color: #64748b;
            font-weight: bold;
        }
        .summary-val {
            font-size: 11pt;
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
            background-color: #1e293b;
            color: #ffffff;
            font-weight: bold;
            text-align: left;
            padding: 5px 6px;
            border: 1px solid #1e293b;
            font-size: 7.5pt;
        }
        .data-table td {
            padding: 4.5px 6px;
            border: 1px solid #e2e8f0;
            vertical-align: middle;
        }
        .data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .badge {
            display: inline-block;
            padding: 2px 5px;
            font-size: 7pt;
            font-weight: bold;
            border-radius: 3px;
        }
        .badge-success { background-color: #dcfce7; color: #15803d; }
        .badge-warning { background-color: #fef3c7; color: #b45309; }
        .badge-danger { background-color: #fee2e2; color: #b91c1c; }
        .badge-primary { background-color: #dbeafe; color: #1d4ed8; }

        .rank-circle {
            display: inline-block;
            width: 16px;
            height: 16px;
            line-height: 16px;
            text-align: center;
            border-radius: 50%;
            font-weight: bold;
            font-size: 7pt;
            background-color: #f1f5f9;
            color: #334155;
        }
        .rank-1 { background-color: #fef08a; color: #854d0e; }
        .rank-2 { background-color: #e2e8f0; color: #334155; }
        .rank-3 { background-color: #fed7aa; color: #9a3412; }

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
                <div class="company-sub">Key Performance Indicators (KPI) &amp; Executive Performance Report</div>
            </td>
            <td style="width: 45%;">
                <div class="doc-title">EVALUASI KINERJA OPERASIONAL</div>
                <div style="font-size: 8pt; color: #64748b; text-align: right;">
                    Periode: 
                    @if($startDate || $endDate)
                        {{ $startDate ?? 'Awal' }} s/d {{ $endDate ?? 'Sekarang' }}
                    @else
                        Semua Waktu (All Time)
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <!-- 1. Ringkasan Eksekutif KPI -->
    <div class="section-title">1. Ringkasan Eksekutif KPI Utama</div>
    <table class="summary-boxes">
        <tr>
            <td style="width: 20%; padding-right: 4px;">
                <div class="summary-card">
                    <div class="summary-label">KEPATUHAN SLA</div>
                    <div class="summary-val" style="color: {{ $executiveKpi['sla_compliance_rate'] >= 90 ? '#15803d' : ($executiveKpi['sla_compliance_rate'] >= 75 ? '#b45309' : '#b91c1c') }};">
                        {{ $executiveKpi['sla_compliance_rate'] }}%
                    </div>
                </div>
            </td>
            <td style="width: 20%; padding-right: 4px;">
                <div class="summary-card">
                    <div class="summary-label">AVG RESPON PERTAMA</div>
                    <div class="summary-val" style="color: #2563eb;">{{ $executiveKpi['formatted_avg_response'] }}</div>
                </div>
            </td>
            <td style="width: 20%; padding-right: 4px;">
                <div class="summary-card">
                    <div class="summary-label">AVG MTTR</div>
                    <div class="summary-val" style="color: #0d9488;">{{ $executiveKpi['formatted_avg_mttr'] }}</div>
                </div>
            </td>
            <td style="width: 20%; padding-right: 4px;">
                <div class="summary-card">
                    <div class="summary-label">TOTAL STOP CLOCK</div>
                    <div class="summary-val" style="color: #d97706;">{{ $executiveKpi['formatted_total_stop_clock'] }}</div>
                </div>
            </td>
            <td style="width: 20%;">
                <div class="summary-card">
                    <div class="summary-label">AVG VERIFIKASI NOC</div>
                    <div class="summary-val" style="color: #7c3aed;">{{ $executiveKpi['formatted_avg_verification'] }}</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- 2. Leaderboard Teknisi Lapangan -->
    <div class="section-title">2. Peringkat &amp; Evaluasi Kinerja Teknisi Lapangan</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 6%; text-align: center;">Rank</th>
                <th style="width: 26%;">Nama Teknisi Lapangan</th>
                <th style="width: 12%; text-align: center;">Total Tiket</th>
                <th style="width: 14%; text-align: center;">Kepatuhan SLA</th>
                <th style="width: 14%; text-align: center;">Avg MTTR</th>
                <th style="width: 14%; text-align: center;">Update 30 Mnt</th>
                <th style="width: 14%; text-align: center;">Skor KPI</th>
            </tr>
        </thead>
        <tbody>
            @forelse($teknisiKpi as $index => $t)
            <tr>
                <td style="text-align: center;">
                    <span class="rank-circle {{ $index === 0 ? 'rank-1' : ($index === 1 ? 'rank-2' : ($index === 2 ? 'rank-3' : '')) }}">
                        {{ $index + 1 }}
                    </span>
                </td>
                <td>
                    <strong>{{ $t['name'] }}</strong><br>
                    <small style="color: #64748b; font-size: 7pt;">{{ $t['email'] }}</small>
                </td>
                <td style="text-align: center;">{{ $t['total_resolved'] }}</td>
                <td style="text-align: center;">
                    <span class="badge {{ $t['sla_compliance_rate'] >= 90 ? 'badge-success' : ($t['sla_compliance_rate'] >= 75 ? 'badge-warning' : 'badge-danger') }}">
                        {{ $t['sla_compliance_rate'] }}%
                    </span>
                </td>
                <td style="text-align: center;">{{ $t['formatted_avg_mttr'] }}</td>
                <td style="text-align: center;">
                    <span class="badge {{ $t['interval_compliance_rate'] >= 85 ? 'badge-success' : 'badge-warning' }}">
                        {{ $t['interval_compliance_rate'] }}%
                    </span>
                </td>
                <td style="text-align: center; font-weight: bold; color: #1e40af;">
                    {{ $t['kpi_score'] }} / 100
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; color: #94a3b8; padding: 12px;">Belum ada data teknisi.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- 3. Kinerja Helpdesk & NOC Verifier -->
    <div class="section-title">3. Kinerja Helpdesk &amp; NOC Verifier</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 6%; text-align: center;">No</th>
                <th style="width: 34%;">Nama Helpdesk / Operator NOC</th>
                <th style="width: 20%; text-align: center;">Tiket Diterbitkan</th>
                <th style="width: 20%; text-align: center;">Closing Diverifikasi</th>
                <th style="width: 20%; text-align: center;">Avg Waktu Verifikasi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($helpdeskKpi as $index => $h)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>
                    <strong>{{ $h['name'] }}</strong><br>
                    <small style="color: #64748b; font-size: 7pt;">{{ $h['email'] }}</small>
                </td>
                <td style="text-align: center;">{{ $h['total_created'] }} tiket</td>
                <td style="text-align: center;">
                    <span class="badge badge-primary">{{ $h['total_verified'] }} tiket</span>
                </td>
                <td style="text-align: center; font-weight: bold;">{{ $h['formatted_avg_verification'] }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center; color: #94a3b8; padding: 12px;">Belum ada data helpdesk.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer-note">
        Dokumen Laporan KPI ini dihasilkan secara otomatis oleh Sistem Tiketing Gangguan PT MSN pada {{ $printDate }}
    </div>

</body>
</html>
