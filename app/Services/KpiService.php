<?php

namespace App\Services;

use App\Models\Tiket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class KpiService
{
    /**
     * Hitung ringkasan KPI eksekutif secara keseluruhan
     */
    public function getExecutiveKpiSummary(?string $startDate = null, ?string $endDate = null): array
    {
        $query = Tiket::query();

        if ($startDate) {
            $query->whereDate('tanggal_open', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('tanggal_open', '<=', $endDate);
        }

        $totalTiket = $query->count();
        $closedQuery = (clone $query)->where('status', 'CLOSE');
        $totalClosed = $closedQuery->count();

        // 1. Kepatuhan SLA
        $totalTepatSla = (clone $closedQuery)->where('sla_status', 'TEPAT')->count();
        $totalLebihSla = (clone $closedQuery)->where('sla_status', 'LEBIH')->count();
        $slaComplianceRate = $totalClosed > 0 ? round(($totalTepatSla / $totalClosed) * 100, 1) : 100.0;

        // 2. MTTR Rata-rata
        $avgMttrMinutes = (int) round($closedQuery->avg('mttr_minutes') ?? 0);

        // 3. Respon Pertama Teknisi (Response Time Avg)
        $avgResponseMinutes = (int) round((clone $query)->whereNotNull('response_time_minutes')->avg('response_time_minutes') ?? 0);

        // 4. Efisiensi Stop Clock (Durasi jeda yang sah)
        $totalStopClockMinutes = (int) (clone $query)->sum('total_stop_clock_minutes');

        // 5. Durasi Verifikasi Helpdesk Rata-rata
        $verifiedTikets = (clone $closedQuery)->whereNotNull('resolved_at')->whereNotNull('tanggal_close')->get();
        $verificationDurations = [];
        foreach ($verifiedTikets as $t) {
            $dur = $t->verification_duration_minutes;
            if ($dur !== null) {
                $verificationDurations[] = $dur;
            }
        }
        $avgVerificationMinutes = count($verificationDurations) > 0
            ? (int) round(array_sum($verificationDurations) / count($verificationDurations))
            : 0;

        return [
            'total_tiket' => $totalTiket,
            'total_closed' => $totalClosed,
            'sla_compliance_rate' => $slaComplianceRate,
            'total_tepat_sla' => $totalTepatSla,
            'total_lebih_sla' => $totalLebihSla,
            'avg_mttr_minutes' => $avgMttrMinutes,
            'formatted_avg_mttr' => $this->formatMinutes($avgMttrMinutes),
            'avg_response_minutes' => $avgResponseMinutes,
            'formatted_avg_response' => $this->formatMinutes($avgResponseMinutes),
            'total_stop_clock_minutes' => $totalStopClockMinutes,
            'formatted_total_stop_clock' => $this->formatMinutes($totalStopClockMinutes),
            'avg_verification_minutes' => $avgVerificationMinutes,
            'formatted_avg_verification' => $this->formatMinutes($avgVerificationMinutes),
        ];
    }

    /**
     * Dapatkan KPI detail per Teknisi Lapangan
     */
    public function getTeknisiKpiList(?string $startDate = null, ?string $endDate = null): Collection
    {
        $teknisiUsers = User::where('role', 'teknis')->orderBy('name')->get();

        return $teknisiUsers->map(function ($user) use ($startDate, $endDate) {
            $query = Tiket::where(function ($q) use ($user) {
                $q->where('resolved_by', $user->id)
                  ->orWhereHas('kronologis', function ($kq) use ($user) {
                      $kq->where('user_id', $user->id);
                  });
            });

            if ($startDate) {
                $query->whereDate('tanggal_open', '>=', $startDate);
            }
            if ($endDate) {
                $query->whereDate('tanggal_open', '<=', $endDate);
            }

            $resolvedQuery = (clone $query)->where('resolved_by', $user->id);
            $totalResolved = $resolvedQuery->count();

            $closedQuery = (clone $resolvedQuery)->where('status', 'CLOSE');
            $totalClosed = $closedQuery->count();
            $totalTepatSla = (clone $closedQuery)->where('sla_status', 'TEPAT')->count();
            $slaRate = $totalClosed > 0 ? round(($totalTepatSla / $totalClosed) * 100, 1) : 100.0;

            $avgMttr = (int) round($closedQuery->avg('mttr_minutes') ?? 0);
            $avgResponse = (int) round((clone $query)->whereNotNull('response_time_minutes')->avg('response_time_minutes') ?? 0);

            // Hitung kepatuhan interval 30 menit
            $allTikets = (clone $query)->get();
            $totalUpdates = 0;
            $overdueUpdates = 0;
            foreach ($allTikets as $t) {
                $kronologis = $t->kronologis()->where('user_id', $user->id)->orderBy('timestamp', 'asc')->get();
                if ($kronologis->count() >= 2) {
                    for ($i = 1; $i < $kronologis->count(); $i++) {
                        $diff = Carbon::parse($kronologis[$i - 1]->timestamp)->diffInMinutes(Carbon::parse($kronologis[$i]->timestamp));
                        $totalUpdates++;
                        if ($diff > 30) {
                            $overdueUpdates++;
                        }
                    }
                }
            }

            $intervalComplianceRate = $totalUpdates > 0
                ? round((($totalUpdates - $overdueUpdates) / $totalUpdates) * 100, 1)
                : 100.0;

            // Skor Keseluruhan KPI (0-100)
            $score = round(($slaRate * 0.45) + ($intervalComplianceRate * 0.35) + (max(0, 100 - min(100, $avgResponse * 2)) * 0.20), 1);

            return [
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'total_resolved' => $totalResolved,
                'total_closed' => $totalClosed,
                'sla_compliance_rate' => $slaRate,
                'avg_mttr_minutes' => $avgMttr,
                'formatted_avg_mttr' => $this->formatMinutes($avgMttr),
                'avg_response_minutes' => $avgResponse,
                'formatted_avg_response' => $this->formatMinutes($avgResponse),
                'total_updates' => $totalUpdates,
                'overdue_updates' => $overdueUpdates,
                'interval_compliance_rate' => $intervalComplianceRate,
                'kpi_score' => $score,
            ];
        })->sortByDesc('kpi_score')->values();
    }

    /**
     * Dapatkan KPI detail per Helpdesk NOC
     */
    public function getHelpdeskKpiList(?string $startDate = null, ?string $endDate = null): Collection
    {
        $hdUsers = User::whereIn('role', ['helpdesk', 'admin'])->orderBy('name')->get();

        return $hdUsers->map(function ($user) use ($startDate, $endDate) {
            $createdQuery = Tiket::where('created_by', $user->id);
            $closedQuery = Tiket::where('closed_by', $user->id);

            if ($startDate) {
                $createdQuery->whereDate('tanggal_open', '>=', $startDate);
                $closedQuery->whereDate('tanggal_open', '>=', $startDate);
            }
            if ($endDate) {
                $createdQuery->whereDate('tanggal_open', '<=', $endDate);
                $closedQuery->whereDate('tanggal_open', '<=', $endDate);
            }

            $totalCreated = $createdQuery->count();
            $totalClosed = $closedQuery->count();

            $closedTikets = $closedQuery->whereNotNull('resolved_at')->whereNotNull('tanggal_close')->get();
            $verificationDurations = [];
            foreach ($closedTikets as $t) {
                $dur = $t->verification_duration_minutes;
                if ($dur !== null) {
                    $verificationDurations[] = $dur;
                }
            }

            $avgVerification = count($verificationDurations) > 0
                ? (int) round(array_sum($verificationDurations) / count($verificationDurations))
                : 0;

            $totalTepatSla = (clone $closedQuery)->where('sla_status', 'TEPAT')->count();
            $slaRate = $totalClosed > 0 ? round(($totalTepatSla / $totalClosed) * 100, 1) : 100.0;

            return [
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'total_created' => $totalCreated,
                'total_closed' => $totalClosed,
                'total_verified' => $totalClosed,
                'sla_compliance_rate' => $slaRate,
                'avg_verification_minutes' => $avgVerification,
                'formatted_avg_verification' => $this->formatMinutes($avgVerification),
            ];
        })->sortByDesc('total_closed')->values();
    }

    /**
     * Format menit ke representasi jam & menit
     */
    protected function formatMinutes(int $minutes): string
    {
        return \App\Models\Tiket::formatDuration($minutes);
    }
}
