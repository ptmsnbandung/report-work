<?php

namespace App\Services;

use App\Exports\TiketExport;
use App\Models\Tiket;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportService
{
    public function __construct(
        protected MttrService $mttrService
    ) {}

    /**
     * Buat query builder tiket dengan filter
     */
    public function getFilteredQuery(array $filters = []): Builder
    {
        $query = Tiket::with(['creator', 'closer', 'resume', 'materials', 'titikPerbaikans', 'dokumentasis', 'manuverCores'])
            ->orderBy('tanggal_open', 'desc');

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['sla_status'])) {
            $query->where('sla_status', $filters['sla_status']);
        }

        if (!empty($filters['segment'])) {
            $query->where('backbone_segment', $filters['segment']);
        }

        if (!empty($filters['start_date'])) {
            $query->whereDate('tanggal_open', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->whereDate('tanggal_open', '<=', $filters['end_date']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('no_tiket', 'like', "%{$search}%")
                  ->orWhere('status_link_impact', 'like', "%{$search}%")
                  ->orWhere('backbone_segment', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    /**
     * Ambil data tiket terfilter
     */
    public function getFilteredTikets(array $filters = []): Collection
    {
        return $this->getFilteredQuery($filters)->get();
    }

    /**
     * Hitung ringkasan metrik untuk halaman reporting
     */
    public function getSummaryMetrics(array $filters = []): array
    {
        $tikets = $this->getFilteredTikets($filters);

        $totalTiket = $tikets->count();
        $totalOpen = $tikets->where('status', 'OPEN')->count();
        $totalProses = $tikets->where('status', 'PROSES')->count();
        $totalClose = $tikets->where('status', 'CLOSE')->count();

        $closedTikets = $tikets->where('status', 'CLOSE');
        $totalTepat = $closedTikets->where('sla_status', 'TEPAT')->count();
        $totalLebih = $closedTikets->where('sla_status', 'LEBIH')->count();

        $avgMttrMinutes = (int) round($closedTikets->avg('mttr_minutes') ?? 0);
        $jam = floor($avgMttrMinutes / 60);
        $menit = $avgMttrMinutes % 60;
        $avgMttrFormatted = $avgMttrMinutes > 0 ? "{$jam} jam {$menit} mnt" : '-';

        $slaComplianceRate = $totalClose > 0
            ? round(($totalTepat / $totalClose) * 100, 1)
            : 0.0;

        return [
            'total_tiket' => $totalTiket,
            'total_open' => $totalOpen,
            'total_proses' => $totalProses,
            'total_close' => $totalClose,
            'total_tepat' => $totalTepat,
            'total_lebih' => $totalLebih,
            'avg_mttr_minutes' => $avgMttrMinutes,
            'avg_mttr_formatted' => $avgMttrFormatted,
            'sla_compliance_rate' => $slaComplianceRate,
        ];
    }

    /**
     * Generate file PDF Berita Acara / Laporan Resmi 1 Tiket
     */
    public function generateSingleTiketPdf(Tiket $tiket)
    {
        $tiket->loadMissing([
            'creator',
            'closer',
            'kronologis.user',
            'resume',
            'materials',
            'titikPerbaikans',
            'jointClosures.cores',
            'jointClosures.creator',
            'manuverCores',
            'dokumentasis'
        ]);

        $pdf = Pdf::loadView('reports.pdf_single_tiket', [
            'tiket' => $tiket,
            'printDate' => now()->translatedFormat('l, d F Y H:i') . ' WIB',
        ])->setPaper('a4', 'portrait');

        return $pdf;
    }

    /**
     * Generate file PDF Rekapitulasi Laporan Banyak Tiket
     */
    public function generateSummaryPdf(array $filters = [])
    {
        $tikets = $this->getFilteredTikets($filters);
        $metrics = $this->getSummaryMetrics($filters);

        $pdf = Pdf::loadView('reports.pdf_summary', [
            'tikets' => $tikets,
            'metrics' => $metrics,
            'filters' => $filters,
            'printDate' => now()->translatedFormat('l, d F Y H:i') . ' WIB',
        ])->setPaper('a4', 'landscape');

        return $pdf;
    }

    /**
     * Download Excel rekap data tiket
     */
    public function downloadExcel(array $filters = []): BinaryFileResponse
    {
        $filename = 'Laporan_Tiket_Gangguan_' . date('Ymd_His') . '.xlsx';
        return Excel::download(new TiketExport($filters), $filename);
    }

    /**
     * Query builder data serah terima / handover shift
     */
    public function getFilteredHandoverShiftsQuery(array $filters = []): Builder
    {
        $query = \App\Models\TiketHandoverShift::with(['tiket', 'userFrom', 'userTo'])
            ->orderBy('created_at', 'desc');

        if (!empty($filters['shift_from'])) {
            $query->where('shift_from', $filters['shift_from']);
        }

        if (!empty($filters['shift_to'])) {
            $query->where('shift_to', $filters['shift_to']);
        }

        if (!empty($filters['start_date'])) {
            $query->whereDate('created_at', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->whereDate('created_at', '<=', $filters['end_date']);
        }

        if (!empty($filters['user_id'])) {
            $userId = $filters['user_id'];
            $query->where(function ($q) use ($userId) {
                $q->where('user_from_id', $userId)
                  ->orWhere('user_to_id', $userId);
            });
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('tiket', function ($q) use ($search) {
                $q->where('no_tiket', 'like', "%{$search}%")
                  ->orWhere('backbone_segment', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    /**
     * Ringkasan metrik statistik handover shift
     */
    public function getHandoverShiftsMetrics(array $filters = []): array
    {
        $all = $this->getFilteredHandoverShiftsQuery($filters)->get();
        $totalHandover = $all->count();

        $shiftPagi = $all->where('shift_to', 'Shift 1 (Pagi 07:00-15:00)')->count();
        $shiftSiang = $all->where('shift_to', 'Shift 2 (Siang 15:00-23:00)')->count();
        $shiftMalam = $all->where('shift_to', 'Shift 3 (Malam 23:00-07:00)')->count();

        // Unique tikets involved
        $uniqueTiketsCount = $all->pluck('id_tiket')->unique()->count();

        return [
            'total_handover' => $totalHandover,
            'shift_pagi' => $shiftPagi,
            'shift_siang' => $shiftSiang,
            'shift_malam' => $shiftMalam,
            'unique_tikets_count' => $uniqueTiketsCount,
        ];
    }

    /**
     * Generate file PDF Rekapitulasi Handover Shift
     */
    public function generateHandoverShiftsPdf(array $filters = [])
    {
        $handovers = $this->getFilteredHandoverShiftsQuery($filters)->get();
        $metrics = $this->getHandoverShiftsMetrics($filters);

        $pdf = Pdf::loadView('reports.pdf_shifts_summary', [
            'handovers' => $handovers,
            'metrics' => $metrics,
            'filters' => $filters,
            'printDate' => now()->translatedFormat('l, d F Y H:i') . ' WIB',
        ])->setPaper('a4', 'landscape');

        return $pdf;
    }

    /**
     * Download Excel rekap data handover shift
     */
    public function downloadHandoverShiftsExcel(array $filters = []): BinaryFileResponse
    {
        $filename = 'Rekap_Handover_Shift_' . date('Ymd_His') . '.xlsx';
        return Excel::download(new \App\Exports\HandoverShiftExport($filters), $filename);
    }

    /**
     * Generate file PDF Laporan KPI Eksekutif
     */
    public function generateKpiPdf(?string $startDate = null, ?string $endDate = null)
    {
        $kpiService = new \App\Services\KpiService();
        $executiveKpi = $kpiService->getExecutiveKpiSummary($startDate, $endDate);
        $teknisiKpi = $kpiService->getTeknisiKpiList($startDate, $endDate);
        $helpdeskKpi = $kpiService->getHelpdeskKpiList($startDate, $endDate);

        $pdf = Pdf::loadView('reports.pdf_kpi_summary', [
            'executiveKpi' => $executiveKpi,
            'teknisiKpi' => $teknisiKpi,
            'helpdeskKpi' => $helpdeskKpi,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'printDate' => now()->translatedFormat('l, d F Y H:i') . ' WIB',
        ])->setPaper('a4', 'portrait');

        return $pdf;
    }

    /**
     * Download Excel Laporan KPI Eksekutif
     */
    public function downloadKpiExcel(?string $startDate = null, ?string $endDate = null): BinaryFileResponse
    {
        $filename = 'Laporan_KPI_Kinerja_' . date('Ymd_His') . '.xlsx';
        return Excel::download(new \App\Exports\KpiExport($startDate, $endDate), $filename);
    }
}
