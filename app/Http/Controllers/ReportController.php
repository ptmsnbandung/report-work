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
        $user = $request->user();
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

        $metrics = $this->reportService->getSummaryMetrics($filters, $user);
        $tikets = $this->reportService->getFilteredQuery($filters, $user)->paginate(15)->withQueryString();

        $segments = MasterSla::select('backbone_segment')->distinct()->orderBy('backbone_segment')->get();
        $monthlyTrend = $this->mttrService->getMonthlyMttrTrend(null, $user);
        $topSegments = $this->mttrService->getTopSegmentsByIncident(5, $user);
        $categoryBreakdown = $this->mttrService->getCategoryBreakdown($startDate, $endDate, $user);
        $stopClockImpact = $this->mttrService->getStopClockImpactAnalytics($startDate, $endDate, $user);
        $hourlyDistribution = $this->mttrService->getHourlyDistribution($startDate, $endDate, $user);

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
        $user = $request->user();
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $executiveKpi = $kpiService->getExecutiveKpiSummary($startDate, $endDate, $user);
        $teknisiKpi = $kpiService->getTeknisiKpiList($startDate, $endDate, $user);
        $helpdeskKpi = $kpiService->getHelpdeskKpiList($startDate, $endDate, $user);

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
        $user = $request->user();
        $year = $request->input('year') ? (int) $request->input('year') : null;
        $trend = $this->mttrService->getMonthlyMttrTrend($year, $user);

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
        $user = $request->user();
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $stats = $this->mttrService->getSlaComplianceStats($startDate, $endDate, $user);

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
        $user = $request->user();
        $filters = $request->only([
            'start_date',
            'end_date',
            'segment',
            'status',
            'sla_status',
            'search'
        ]);

        $filename = 'Rekap_Laporan_Tiket_CJP_' . date('Ymd_His') . '.pdf';
        return $this->reportService->generateSummaryPdf($filters, $user)->download($filename);
    }

    /**
     * Export Berita Acara 1 Tiket ke PDF Resmi (Portrait)
     * Mendukung opsi: internal (default) dan customer
     */
    public function exportTiketPdf(Request $request, Tiket $tiket)
    {
        $type = $request->query('type', 'internal');
        $safeNoTiket = str_replace(['/', '\\'], '_', $tiket->no_tiket);
        
        if ($type === 'customer') {
            $filename = 'Berita_Acara_Pelanggan_' . $safeNoTiket . '.pdf';
        } else {
            $filename = 'Berita_Acara_Internal_' . $safeNoTiket . '.pdf';
        }

        return $this->reportService->generateSingleTiketPdf($tiket, $type)->download($filename);
    }

    /**
     * Export Rekap Data Tiket ke File Excel (.xlsx)
     */
    public function exportExcel(Request $request): BinaryFileResponse
    {
        $user = $request->user();
        $filters = $request->only([
            'start_date',
            'end_date',
            'segment',
            'status',
            'sla_status',
            'search'
        ]);

        return $this->reportService->downloadExcel($filters, $user);
    }

    /**
     * Halaman Audit & Rekapitulasi Handover / Oper Shift Tiket
     */
    public function shifts(Request $request): View
    {
        $user = $request->user();
        $filters = $request->only([
            'start_date',
            'end_date',
            'shift_from',
            'shift_to',
            'user_id',
            'search',
        ]);

        $metrics = $this->reportService->getHandoverShiftsMetrics($filters, $user);
        $handovers = $this->reportService->getFilteredHandoverShiftsQuery($filters, $user)->paginate(15)->withQueryString();
        
        $usersQuery = \App\Models\User::where('is_active', true)->orderBy('name');
        if (!in_array($user?->role, ['admin', 'manager_teknisi'])) {
            $usersQuery->where('id', $user?->id);
        }
        $users = $usersQuery->get();

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
        $user = $request->user();
        $filters = $request->only([
            'start_date',
            'end_date',
            'shift_from',
            'shift_to',
            'user_id',
            'search',
        ]);

        $filename = 'Rekap_Handover_Shift_' . date('Ymd_His') . '.pdf';
        return $this->reportService->generateHandoverShiftsPdf($filters, $user)->download($filename);
    }

    /**
     * Export Rekapitulasi Handover Shift ke Excel
     */
    public function exportShiftsExcel(Request $request): BinaryFileResponse
    {
        $user = $request->user();
        $filters = $request->only([
            'start_date',
            'end_date',
            'shift_from',
            'shift_to',
            'user_id',
            'search',
        ]);

        return $this->reportService->downloadHandoverShiftsExcel($filters, $user);
    }

    /**
     * Export Laporan KPI Eksekutif ke PDF
     */
    public function exportKpiPdf(Request $request)
    {
        $user = $request->user();
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $filename = 'Laporan_Eksekutif_KPI_' . date('Ymd_His') . '.pdf';
        return $this->reportService->generateKpiPdf($startDate, $endDate, $user)->download($filename);
    }

    /**
     * Export Laporan KPI Eksekutif ke Excel
     */
    public function exportKpiExcel(Request $request): BinaryFileResponse
    {
        $user = $request->user();
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        return $this->reportService->downloadKpiExcel($startDate, $endDate, $user);
    }
}
