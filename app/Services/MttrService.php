<?php

namespace App\Services;

use App\Models\Tiket;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MttrService
{
    /**
     * Hitung durasi MTTR dalam menit antara tanggal_open dan tanggal_close
     */
    public function calculateMttr(Tiket $tiket): int
    {
        if (!$tiket->tanggal_open || !$tiket->tanggal_close) {
            return 0;
        }

        $open = Carbon::parse($tiket->tanggal_open);
        $close = Carbon::parse($tiket->tanggal_close);

        return max(0, $open->diffInMinutes($close));
    }

    /**
     * Tentukan status kepatuhan SLA ('TEPAT' atau 'LEBIH')
     */
    public function checkSlaCompliance(Tiket $tiket): string
    {
        if ($tiket->mttr_minutes === null || $tiket->sla_target_minutes === null) {
            return 'NA';
        }

        return $tiket->mttr_minutes <= $tiket->sla_target_minutes ? 'TEPAT' : 'LEBIH';
    }

    /**
     * Ambil rata-rata MTTR (dalam menit & terformat)
     *
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array
     */
    public function getMttrAverage(?string $startDate = null, ?string $endDate = null): array
    {
        $query = Tiket::where('status', 'CLOSE')->whereNotNull('mttr_minutes');

        if ($startDate) {
            $query->whereDate('tanggal_open', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('tanggal_open', '<=', $endDate);
        }

        $avgMinutes = (int) round($query->avg('mttr_minutes') ?? 0);
        $totalClosed = $query->count();

        $jam = floor($avgMinutes / 60);
        $menit = $avgMinutes % 60;
        $formatted = $avgMinutes > 0 ? "{$jam} jam {$menit} mnt" : '-';

        return [
            'average_minutes' => $avgMinutes,
            'formatted' => $formatted,
            'total_closed' => $totalClosed,
        ];
    }

    /**
     * Ambil statistik kepatuhan SLA (Compliance Rate %)
     *
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array
     */
    public function getSlaComplianceStats(?string $startDate = null, ?string $endDate = null): array
    {
        $query = Tiket::where('status', 'CLOSE');

        if ($startDate) {
            $query->whereDate('tanggal_open', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('tanggal_open', '<=', $endDate);
        }

        $totalClosed = $query->count();
        $totalTepat = (clone $query)->where('sla_status', 'TEPAT')->count();
        $totalLebih = (clone $query)->where('sla_status', 'LEBIH')->count();

        $complianceRate = $totalClosed > 0
            ? round(($totalTepat / $totalClosed) * 100, 1)
            : 0.0;

        return [
            'total_closed' => $totalClosed,
            'total_tepat' => $totalTepat,
            'total_lebih' => $totalLebih,
            'compliance_rate' => $complianceRate,
        ];
    }

    /**
     * Ambil tren MTTR bulanan untuk Chart.js (12 bulan terakhir)
     *
     * @param int|null $year
     * @return array
     */
    public function getMonthlyMttrTrend(?int $year = null): array
    {
        $year = $year ?: (int) date('Y');

        $labels = [
            'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
            'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'
        ];

        $mttrData = array_fill(0, 12, 0);
        $tiketCounts = array_fill(0, 12, 0);

        $driver = DB::connection()->getDriverName();
        $monthExpr = $driver === 'sqlite' ? "cast(strftime('%m', tanggal_open) as integer)" : 'MONTH(tanggal_open)';

        $results = Tiket::select(
            DB::raw("{$monthExpr} as bulan"),
            DB::raw('ROUND(AVG(mttr_minutes)) as avg_mttr'),
            DB::raw('COUNT(id) as total_tiket')
        )
        ->whereYear('tanggal_open', $year)
        ->where('status', 'CLOSE')
        ->groupBy(DB::raw($monthExpr))
        ->get();

        foreach ($results as $row) {
            $monthIndex = (int) $row->bulan - 1;
            if ($monthIndex >= 0 && $monthIndex < 12) {
                $mttrData[$monthIndex] = (int) $row->avg_mttr;
                $tiketCounts[$monthIndex] = (int) $row->total_tiket;
            }
        }

        return [
            'year' => $year,
            'labels' => $labels,
            'mttr_minutes' => $mttrData,
            'tiket_counts' => $tiketCounts,
        ];
    }

    /**
     * Ambil segment backbone dengan gangguan terbanyak
     *
     * @param int $limit
     * @return array
     */
    public function getTopSegmentsByIncident(int $limit = 5): array
    {
        return Tiket::select(
            'backbone_segment',
            DB::raw('COUNT(id) as total_gangguan'),
            DB::raw('ROUND(AVG(CASE WHEN mttr_minutes IS NOT NULL THEN mttr_minutes ELSE 0 END)) as avg_mttr'),
            DB::raw('SUM(CASE WHEN sla_status = "LEBIH" THEN 1 ELSE 0 END) as total_over_sla')
        )
        ->groupBy('backbone_segment')
        ->orderByDesc('total_gangguan')
        ->limit($limit)
        ->get()
        ->toArray();
    }
}
