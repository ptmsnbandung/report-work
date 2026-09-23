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
}
