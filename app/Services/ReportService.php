<?php

namespace App\Services;

use App\Exports\TiketExport;
use App\Models\Tiket;
use App\Models\User;
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
     * Buat query builder tiket dengan filter dan scoping hak akses monitoring
     */
    public function getFilteredQuery(array $filters = [], ?User $user = null): Builder
    {
        $user = $user ?? (auth()->check() ? auth()->user() : null);

        $query = Tiket::with(['creator', 'closer', 'resume', 'materials', 'titikPerbaikans', 'dokumentasis', 'manuverCores'])
            ->forUserMonitoring($user)
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
    public function getFilteredTikets(array $filters = [], ?User $user = null): Collection
    {
        return $this->getFilteredQuery($filters, $user)->get();
    }

    /**
     * Hitung ringkasan metrik untuk halaman reporting
     */
    public function getSummaryMetrics(array $filters = [], ?User $user = null): array
    {
        $tikets = $this->getFilteredTikets($filters, $user);

        $totalTiket = $tikets->count();
        $totalOpen = $tikets->where('status', 'OPEN')->count();
        $totalProses = $tikets->where('status', 'PROSES')->count();
        $totalClose = $tikets->where('status', 'CLOSE')->count();

        $closedTikets = $tikets->where('status', 'CLOSE');
        $totalTepat = $closedTikets->where('sla_status', 'TEPAT')->count();
        $totalLebih = $closedTikets->where('sla_status', 'LEBIH')->count();

        $avgMttrMinutes = (int) round($closedTikets->avg('mttr_minutes') ?? 0);
        $avgMttrFormatted = $avgMttrMinutes > 0 ? \App\Models\Tiket::formatDuration($avgMttrMinutes) : '-';

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
     * @param Tiket $tiket
     * @param string $type ('internal' | 'customer')
     */
    public function generateSingleTiketPdf(Tiket $tiket, string $type = 'internal')
    {
        if ($type === 'customer') {
            $tiket->loadMissing([
                'creator',
                'closer',
                'resume',
                'dokumentasis',
                'titikPerbaikans',
            ]);

            $pdf = Pdf::loadView('reports.pdf_customer_tiket', [
                'tiket' => $tiket,
                'printDate' => now()->translatedFormat('l, d F Y H:i') . ' WIB',
            ])->setPaper('a4', 'portrait');

            return $pdf;
        }

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
            'handoverShifts.userFrom',
            'handoverShifts.userTo',
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
    public function generateSummaryPdf(array $filters = [], ?User $user = null)
    {
        $user = $user ?? (auth()->check() ? auth()->user() : null);
        $tikets = $this->getFilteredTikets($filters, $user);
        $metrics = $this->getSummaryMetrics($filters, $user);

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
    public function downloadExcel(array $filters = [], ?User $user = null): BinaryFileResponse
    {
        $user = $user ?? (auth()->check() ? auth()->user() : null);
        $filename = 'Laporan_Tiket_Gangguan_' . date('Ymd_His') . '.xlsx';
        return Excel::download(new TiketExport($filters, $user), $filename);
    }

    /**
     * Query builder data serah terima / handover shift dengan filter role
     */
    public function getFilteredHandoverShiftsQuery(array $filters = [], ?User $user = null): Builder
    {
        $user = $user ?? (auth()->check() ? auth()->user() : null);

        $query = \App\Models\TiketHandoverShift::with(['tiket', 'userFrom', 'userTo'])
            ->orderBy('created_at', 'desc');

        // Jika bukan admin dan bukan manager teknisi, batasi hanya riwayat shift miliknya
        if ($user && !$user->hasRole(['admin', 'manager_teknisi'])) {
            $query->where(function ($q) use ($user) {
                $q->where('user_from_id', $user->id)
                  ->orWhere('user_to_id', $user->id);
            });
        }

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
    public function getHandoverShiftsMetrics(array $filters = [], ?User $user = null): array
    {
        $all = $this->getFilteredHandoverShiftsQuery($filters, $user)->get();
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
    public function generateHandoverShiftsPdf(array $filters = [], ?User $user = null)
    {
        $user = $user ?? (auth()->check() ? auth()->user() : null);
        $handovers = $this->getFilteredHandoverShiftsQuery($filters, $user)->get();
        $metrics = $this->getHandoverShiftsMetrics($filters, $user);

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
    public function downloadHandoverShiftsExcel(array $filters = [], ?User $user = null): BinaryFileResponse
    {
        $user = $user ?? (auth()->check() ? auth()->user() : null);
        $filename = 'Rekap_Handover_Shift_' . date('Ymd_His') . '.xlsx';
        return Excel::download(new \App\Exports\HandoverShiftExport($filters, $user), $filename);
    }

    /**
     * Generate file PDF Laporan KPI Eksekutif
     */
    public function generateKpiPdf(?string $startDate = null, ?string $endDate = null, ?User $user = null)
    {
        $user = $user ?? (auth()->check() ? auth()->user() : null);
        $kpiService = new \App\Services\KpiService();
        $executiveKpi = $kpiService->getExecutiveKpiSummary($startDate, $endDate, $user);
        $teknisiKpi = $kpiService->getTeknisiKpiList($startDate, $endDate, $user);
        $helpdeskKpi = $kpiService->getHelpdeskKpiList($startDate, $endDate, $user);

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
    public function downloadKpiExcel(?string $startDate = null, ?string $endDate = null, ?User $user = null): BinaryFileResponse
    {
        $user = $user ?? (auth()->check() ? auth()->user() : null);
        $filename = 'Laporan_KPI_Kinerja_' . date('Ymd_His') . '.xlsx';
        return Excel::download(new \App\Exports\KpiExport($startDate, $endDate, $user), $filename);
    }
}
