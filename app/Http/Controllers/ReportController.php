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

        $startDate = $filters['start_date'] ?? null;
        $endDate = $filters['end_date'] ?? null;

        $metrics = $this->reportService->getSummaryMetrics($filters);
        $tikets = $this->reportService->getFilteredQuery($filters)->paginate(15)->withQueryString();

        $segments = MasterSla::select('backbone_segment')->distinct()->orderBy('backbone_segment')->get();
        $monthlyTrend = $this->mttrService->getMonthlyMttrTrend();
        $topSegments = $this->mttrService->getTopSegmentsByIncident(5);
        $categoryBreakdown = $this->mttrService->getCategoryBreakdown($startDate, $endDate);
        $stopClockImpact = $this->mttrService->getStopClockImpactAnalytics($startDate, $endDate);
        $hourlyDistribution = $this->mttrService->getHourlyDistribution($startDate, $endDate);

        return view('reports.index', compact(
            'tikets',
            'metrics',
            'filters',
            'segments',
            'monthlyTrend',
            'topSegments',
            'categoryBreakdown',
            'stopClockImpact',
            'hourlyDistribution'
        ));
    }

    /**
     * Halaman Dashboard KPI (Key Performance Indicator) Teknisi & NOC
     */
    public function kpi(Request $request, \App\Services\KpiService $kpiService): View
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $executiveKpi = $kpiService->getExecutiveKpiSummary($startDate, $endDate);
        $teknisiKpi = $kpiService->getTeknisiKpiList($startDate, $endDate);
        $helpdeskKpi = $kpiService->getHelpdeskKpiList($startDate, $endDate);

        return view('reports.kpi', compact(
            'executiveKpi',
            'teknisiKpi',
            'helpdeskKpi',
            'startDate',
            'endDate'
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
        $safeNoTiket = str_replace(['/', '\\'], '_', $tiket->no_tiket);
        $filename = 'Berita_Acara_Gangguan_' . $safeNoTiket . '.pdf';
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

    /**
     * Halaman Audit & Rekapitulasi Handover / Oper Shift Tiket
     */
    public function shifts(Request $request): View
    {
        $filters = $request->only([
            'start_date',
            'end_date',
            'shift_from',
            'shift_to',
            'user_id',
            'search',
        ]);

        $metrics = $this->reportService->getHandoverShiftsMetrics($filters);
        $handovers = $this->reportService->getFilteredHandoverShiftsQuery($filters)->paginate(15)->withQueryString();
        $users = \App\Models\User::where('is_active', true)->orderBy('name')->get();

        return view('reports.shifts', compact(
            'handovers',
            'metrics',
            'filters',
            'users'
        ));
    }

    /**
     * Export Rekapitulasi Handover Shift ke PDF
     */
    public function exportShiftsPdf(Request $request)
    {
        $filters = $request->only([
            'start_date',
            'end_date',
            'shift_from',
            'shift_to',
            'user_id',
            'search',
        ]);

        $filename = 'Rekap_Handover_Shift_' . date('Ymd_His') . '.pdf';
        return $this->reportService->generateHandoverShiftsPdf($filters)->download($filename);
    }

    /**
     * Export Rekapitulasi Handover Shift ke Excel
     */
    public function exportShiftsExcel(Request $request): BinaryFileResponse
    {
        $filters = $request->only([
            'start_date',
            'end_date',
            'shift_from',
            'shift_to',
            'user_id',
            'search',
        ]);

        return $this->reportService->downloadHandoverShiftsExcel($filters);
    }

    /**
     * Export Laporan KPI Eksekutif ke PDF
     */
    public function exportKpiPdf(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $filename = 'Laporan_Eksekutif_KPI_' . date('Ymd_His') . '.pdf';
        return $this->reportService->generateKpiPdf($startDate, $endDate)->download($filename);
    }

    /**
     * Export Laporan KPI Eksekutif ke Excel
     */
    public function exportKpiExcel(Request $request): BinaryFileResponse
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        return $this->reportService->downloadKpiExcel($startDate, $endDate);
    }
}
