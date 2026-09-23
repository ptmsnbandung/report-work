<?php

namespace App\Http\Controllers;

use App\Models\MasterSla;
use App\Models\Tiket;
use App\Services\MttrService;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportController extends Controller
{
    public function __construct(
        protected ReportService $reportService,
        protected MttrService $mttrService
    ) {}

    /**
     * Halaman Utama Report & Analisis MTTR / SLA
     */
    public function index(Request $request): View
    {
        $filters = $request->only([
            'start_date',
            'end_date',
            'segment',
            'status',
            'sla_status',
            'search'
        ]);

        $metrics = $this->reportService->getSummaryMetrics($filters);
        $tikets = $this->reportService->getFilteredQuery($filters)->paginate(15)->withQueryString();

        $segments = MasterSla::select('backbone_segment')->distinct()->orderBy('backbone_segment')->get();
        $monthlyTrend = $this->mttrService->getMonthlyMttrTrend();
        $topSegments = $this->mttrService->getTopSegmentsByIncident(5);

        return view('reports.index', compact(
            'tikets',
            'metrics',
            'filters',
            'segments',
            'monthlyTrend',
            'topSegments'
        ));
    }

    /**
     * API Endpoint JSON Data MTTR Bulanan untuk Chart.js
     */
    public function mttr(Request $request): JsonResponse
    {
        $year = $request->input('year') ? (int) $request->input('year') : null;
        $trend = $this->mttrService->getMonthlyMttrTrend($year);

        return response()->json([
            'status' => 'success',
            'data' => $trend,
        ]);
    }

    /**
     * API Endpoint JSON Data Distribusi Kepatuhan SLA untuk Chart.js
     */
    public function sla(Request $request): JsonResponse
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $stats = $this->mttrService->getSlaComplianceStats($startDate, $endDate);

        return response()->json([
            'status' => 'success',
            'data' => $stats,
        ]);
    }

    /**
     * Export Rekapitulasi Data Tiket ke PDF (Landscape)
     */
    public function exportPdf(Request $request)
    {
        $filters = $request->only([
            'start_date',
            'end_date',
            'segment',
            'status',
            'sla_status',
            'search'
        ]);

        $filename = 'Rekap_Laporan_Tiket_CJP_' . date('Ymd_His') . '.pdf';
        return $this->reportService->generateSummaryPdf($filters)->download($filename);
    }

    /**
     * Export Berita Acara 1 Tiket ke PDF Resmi (Portrait)
     */
    public function exportTiketPdf(Tiket $tiket)
    {
        $filename = 'Berita_Acara_Gangguan_' . $tiket->no_tiket . '.pdf';
        return $this->reportService->generateSingleTiketPdf($tiket)->download($filename);
    }

    /**
     * Export Rekap Data Tiket ke File Excel (.xlsx)
     */
    public function exportExcel(Request $request): BinaryFileResponse
    {
        $filters = $request->only([
            'start_date',
            'end_date',
            'segment',
            'status',
            'sla_status',
            'search'
        ]);

        return $this->reportService->downloadExcel($filters);
    }
}
