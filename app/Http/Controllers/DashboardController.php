<?php

namespace App\Http\Controllers;

use App\Models\MasterSla;
use App\Models\Tiket;
use App\Models\User;
use App\Services\MttrService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected MttrService $mttrService
    ) {}

    /**
     * Tampilkan dashboard sesuai role pengguna dengan metrik MTTR, SLA, dan data visualisasi.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        // Statistik Tiket Umum
        $totalTiket = Tiket::count();
        $totalOpen = Tiket::where('status', 'OPEN')->count();
        $totalProses = Tiket::where('status', 'PROSES')->count();
        $totalClose = Tiket::where('status', 'CLOSE')->count();
        $totalSlaExceeded = Tiket::where('sla_status', 'LEBIH')->count();

        // Rata-rata MTTR (dalam menit) untuk tiket yang sudah close
        $mttrStats = $this->mttrService->getMttrAverage();
        $avgMttrMinutes = $mttrStats['average_minutes'];
        $avgMttrFormatted = $mttrStats['formatted'];

        // SLA Compliance Stats
        $slaStats = $this->mttrService->getSlaComplianceStats();
        $slaComplianceRate = $slaStats['compliance_rate'];

        // Tiket melebihi SLA yang masih aktif (Alert merah untuk Helpdesk & Admin)
        $overSlaActiveTikets = Tiket::whereIn('status', ['OPEN', 'PROSES'])
            ->get()
            ->filter(function ($t) {
                if (!$t->tanggal_open || !$t->sla_target_minutes) return false;
                $elapsed = Carbon::parse($t->tanggal_open)->diffInMinutes(now());
                return $elapsed > $t->sla_target_minutes;
            });

        // Tiket terbaru (5 tiket terakhir)
        $latestTikets = Tiket::with(['creator', 'closer', 'resume'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Tiket yang sedang aktif (OPEN & PROSES)
        $activeTikets = Tiket::whereIn('status', ['OPEN', 'PROSES'])
            ->with(['creator', 'resume', 'kronologis'])
            ->orderBy('tanggal_open', 'asc')
            ->get();

        // Data Chart: Tiket per hari (7 hari terakhir)
        $dailyLabels = [];
        $dailyCounts = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dailyLabels[] = $date->translatedFormat('D, d M');
            $dailyCounts[] = Tiket::whereDate('tanggal_open', $date)->count();
        }

        // Data Chart: Tren MTTR Bulanan
        $monthlyTrend = $this->mttrService->getMonthlyMttrTrend();

        // Top Segments dengan gangguan terbanyak
        $topSegments = $this->mttrService->getTopSegmentsByIncident(5);

        // Data tambahan admin
        $totalUsers = User::count();
        $totalSegments = MasterSla::count();

        return view('dashboard.index', compact(
            'user',
            'totalTiket',
            'totalOpen',
            'totalProses',
            'totalClose',
            'totalSlaExceeded',
            'overSlaActiveTikets',
            'avgMttrMinutes',
            'avgMttrFormatted',
            'slaStats',
            'slaComplianceRate',
            'latestTikets',
            'activeTikets',
            'dailyLabels',
            'dailyCounts',
            'monthlyTrend',
            'topSegments',
            'totalUsers',
            'totalSegments'
        ));
    }
}
