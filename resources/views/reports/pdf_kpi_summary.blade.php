<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Eksekutif KPI &amp; Evaluasi Kinerja</title>
    <style>
        @page {
            margin: 18px 24px 22px 24px;
            size: A4 portrait;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #0f172a;
            font-size: 8.2pt;
            line-height: 1.4;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }
        .company-sub {
            font-size: 7.8pt;
            color: #2563eb;
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
            font-size: 7.5pt;
            font-family: monospace;
            color: #1e40af;
            font-weight: 700;
            text-align: right;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            padding: 2px 8px;
            border-radius: 4px;
            margin-top: 3px;
        }

        .section-title {
            font-size: 8.5pt;
            font-weight: 700;
            color: #ffffff;
            background-color: #0f172a;
            padding: 4px 8px;
            margin-top: 10px;
            margin-bottom: 6px;
            border-radius: 3px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .summary-boxes {
            width: 100%;
            border-collapse: separate;
            border-spacing: 5px 0;
            margin-bottom: 8px;
        }
        .summary-card {
            border: 1px solid #e2e8f0;
            background-color: #f8fafc;
            padding: 6px 6px;
            border-radius: 4px;
            text-align: center;
        }
        .summary-label {
            font-size: 6.5pt;
            text-transform: uppercase;
            color: #64748b;
            font-weight: 700;
            letter-spacing: 0.2px;
        }
        .summary-val {
            font-size: 11pt;
            font-weight: 800;
            color: #0f172a;
            margin-top: 2px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.8pt;
            margin-bottom: 4px;
        }
        .data-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-weight: 700;
            text-align: left;
            padding: 4.5px 6px;
            border: 1px solid #0f172a;
            font-size: 7.2pt;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .data-table td {
            padding: 4.5px 6px;
            border: 1px solid #e2e8f0;
            vertical-align: middle;
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
            text-transform: uppercase;
        }
        .badge-success { background-color: #dcfce7; color: #15803d; border: 1px solid #86efac; }
        .badge-warning { background-color: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
        .badge-danger  { background-color: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; }
        .badge-primary { background-color: #dbeafe; color: #1d4ed8; border: 1px solid #93c5fd; }

        .rank-circle {
            display: inline-block;
            width: 16px;
            height: 16px;
            line-height: 16px;
            text-align: center;
            border-radius: 50%;
            font-weight: 700;
            font-size: 7pt;
            background-color: #f1f5f9;
            color: #334155;
            border: 1px solid #cbd5e1;
        }
        .rank-1 { background-color: #fef08a; color: #854d0e; border-color: #facc15; }
        .rank-2 { background-color: #e2e8f0; color: #334155; border-color: #94a3b8; }
        .rank-3 { background-color: #fed7aa; color: #9a3412; border-color: #fdba74; }

        .footer-note {
            margin-top: 12px;
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
                <div class="company-sub">Key Performance Indicators (KPI) &amp; Executive Performance Report</div>
                <div class="company-addr">Fiber Optic Backbone &amp; Network Operation Center</div>
            </td>
            <td style="width: 40%; vertical-align: middle;" align="right">
                <div class="doc-title">EVALUASI KINERJA OPERASIONAL</div>
                <div class="doc-no-badge">
                    Periode: 
                    @if($startDate || $endDate)
                        {{ $startDate ?? 'Awal' }} s/d {{ $endDate ?? 'Sekarang' }}
                    @else
                        Semua Periode (All Time)
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <!-- 1. Ringkasan Eksekutif KPI -->
    <div class="section-title">1. Ringkasan Eksekutif KPI Utama</div>
    <table class="summary-boxes">
        <tr>
            <td style="width: 20%;">
                <div class="summary-card">
                    <div class="summary-label">KEPATUHAN SLA</div>
                    <div class="summary-val" style="color: {{ $executiveKpi['sla_compliance_rate'] >= 90 ? '#15803d' : ($executiveKpi['sla_compliance_rate'] >= 75 ? '#b45309' : '#b91c1c') }};">
                        {{ $executiveKpi['sla_compliance_rate'] }}%
                    </div>
                </div>
            </td>
            <td style="width: 20%;">
                <div class="summary-card">
                    <div class="summary-label">AVG RESPON PERTAMA</div>
                    <div class="summary-val" style="color: #2563eb;">{{ $executiveKpi['formatted_avg_response'] }}</div>
                </div>
            </td>
            <td style="width: 20%;">
                <div class="summary-card">
                    <div class="summary-label">AVG MTTR</div>
                    <div class="summary-val" style="color: #0d9488;">{{ $executiveKpi['formatted_avg_mttr'] }}</div>
                </div>
            </td>
            <td style="width: 20%;">
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
                    <strong style="color: #0f172a;">{{ $t['name'] }}</strong><br>
                    <small style="color: #64748b; font-size: 7pt;">{{ $t['email'] }}</small>
                </td>
                <td style="text-align: center; font-weight: 700;">{{ $t['total_resolved'] }}</td>
                <td style="text-align: center;">
                    <span class="badge {{ $t['sla_compliance_rate'] >= 90 ? 'badge-success' : ($t['sla_compliance_rate'] >= 75 ? 'badge-warning' : 'badge-danger') }}">
                        {{ $t['sla_compliance_rate'] }}%
                    </span>
                </td>
                <td style="text-align: center; font-family: monospace; font-weight: 700;">{{ $t['formatted_avg_mttr'] }}</td>
                <td style="text-align: center;">
                    <span class="badge {{ $t['interval_compliance_rate'] >= 85 ? 'badge-success' : 'badge-warning' }}">
                        {{ $t['interval_compliance_rate'] }}%
                    </span>
                </td>
                <td style="text-align: center; font-weight: 800; color: #1e40af; font-size: 8.5pt;">
                    {{ $t['kpi_score'] }} <span style="font-size: 6.8pt; color: #64748b; font-weight: normal;">/ 100</span>
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
                    <strong style="color: #0f172a;">{{ $h['name'] }}</strong><br>
                    <small style="color: #64748b; font-size: 7pt;">{{ $h['email'] }}</small>
                </td>
                <td style="text-align: center; font-weight: 700;">{{ $h['total_created'] }} tiket</td>
                <td style="text-align: center;">
                    <span class="badge badge-primary">{{ $h['total_verified'] }} tiket</span>
                </td>
                <td style="text-align: center; font-family: monospace; font-weight: 700;">{{ $h['formatted_avg_verification'] }}</td>
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
