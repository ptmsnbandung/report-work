<?php

namespace App\Exports;

use App\Services\KpiService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KpiExport implements FromArray, ShouldAutoSize, WithStyles
{
    protected ?string $startDate;
    protected ?string $endDate;

    public function __construct(?string $startDate = null, ?string $endDate = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function array(): array
    {
        $kpiService = new KpiService();
        $summary = $kpiService->getExecutiveKpiSummary($this->startDate, $this->endDate);
        $teknisiList = $kpiService->getTeknisiKpiList($this->startDate, $this->endDate);
        $helpdeskList = $kpiService->getHelpdeskKpiList($this->startDate, $this->endDate);

        $rows = [];

        // ── TITLE SECTION ──
        $rows[] = ['PT MEGA SIBER NUSANTARA - LAPORAN KEY PERFORMANCE INDICATORS (KPI)'];
        $rows[] = ['Periode:', ($this->startDate ?? 'Awal') . ' s/d ' . ($this->endDate ?? 'Sekarang'), '', '', '', '', '', 'Dicetak:', now()->format('d/m/Y H:i')];
        $rows[] = ['']; // Empty separator

        // ── 1. EXECUTIVE SUMMARY SECTION ──
        $rows[] = ['1. RINGKASAN EKSEKUTIF KPI KINERJA'];
        $rows[] = ['Indikator Metrik', 'Nilai / Realisasi', 'Keterangan'];
        $rows[] = ['Total Tiket Gangguan', $summary['total_tiket'], 'Total insiden backbone dalam periode'];
        $rows[] = ['Tiket Diselesaikan (Closed)', $summary['total_closed'], 'Tiket berhasil di-recovery dan close'];
        $rows[] = ['Tingkat Kepatuhan SLA (%)', $summary['sla_compliance_rate'] . '%', $summary['total_tepat_sla'] . ' Tepat SLA / ' . $summary['total_lebih_sla'] . ' Over SLA'];
        $rows[] = ['Rata-rata MTTR (Mean Time to Repair)', $summary['formatted_avg_mttr'], 'Durasi rata-rata penyelesaian gangguan'];
        $rows[] = ['Rata-rata Respon Pertama (Response Time)', $summary['formatted_avg_response'], 'Kecepatan respon teknisi sejak tiket open'];
        $rows[] = ['Total Jeda SLA (Stop Clock)', $summary['formatted_total_stop_clock'], 'Jeda waktu kendala eksternal yang disahkan'];
        $rows[] = ['Rata-rata Verifikasi NOC', $summary['formatted_avg_verification'], 'Waktu verifikasi closing awal ke closing akhir'];
        $rows[] = ['']; // Empty separator

        // ── 2. TEKNISI LEADERBOARD SECTION ──
        $rows[] = ['2. PERINGKAT & EVALUASI KINERJA TEKNISI LAPANGAN'];
        $rows[] = [
            'Rank',
            'Nama Teknisi',
            'Email',
            'Total Tiket',
            'Kepatuhan SLA (%)',
            'Avg MTTR',
            'Avg Respon',
            'Kepatuhan 30 Mnt (%)',
            'Skor KPI (0-100)'
        ];

        foreach ($teknisiList as $index => $t) {
            $rows[] = [
                $index + 1,
                $t['name'],
                $t['email'],
                $t['total_resolved'],
                $t['sla_compliance_rate'] . '%',
                $t['formatted_avg_mttr'],
                $t['formatted_avg_response'],
                $t['interval_compliance_rate'] . '%',
                $t['kpi_score'] . ' / 100',
            ];
        }
        $rows[] = ['']; // Empty separator

        // ── 3. HELPDESK & NOC VERIFIER SECTION ──
        $rows[] = ['3. KINERJA HELPDESK & NOC VERIFIER'];
        $rows[] = [
            'No',
            'Nama Helpdesk / Operator',
            'Email',
            'Total Tiket Dibuat',
            'Total Verifikasi Closing',
            'Avg Durasi Verifikasi'
        ];

        foreach ($helpdeskList as $index => $h) {
            $rows[] = [
                $index + 1,
                $h['name'],
                $h['email'],
                $h['total_created'],
                $h['total_verified'],
                $h['formatted_avg_verification'],
            ];
        }

        return $rows;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 13, 'color' => ['argb' => 'FF1E40AF']]],
            4 => ['font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0F172A']]],
            5 => ['font' => ['bold' => true, 'color' => ['argb' => 'FF0F172A']], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFE2E8F0']]],
            14 => ['font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0F172A']]],
            15 => ['font' => ['bold' => true, 'color' => ['argb' => 'FF0F172A']], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFE2E8F0']]],
        ];
    }
}
