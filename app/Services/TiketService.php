<?php

namespace App\Services;

use App\Models\MasterSla;
use App\Models\Tiket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class TiketService
{
    protected ?NotificationService $notificationService;

    public function __construct(
        ?NotificationService $notificationService = null
    ) {
        $this->notificationService = $notificationService 
            ?? (function_exists('app') && app()->bound(NotificationService::class) ? app(NotificationService::class) : null);
    }
    /**
     * Generate Nomor Tiket otomatis dengan format: BDG-YYYYMMDD-XXX
     */
    public function generateNoTiket(?Carbon $date = null): string
    {
        $date = $date ?: Carbon::now();
        $prefix = 'BDG-' . $date->format('Ymd') . '-';

        $lastTiket = Tiket::where('no_tiket', 'like', $prefix . '%')
            ->orderBy('no_tiket', 'desc')
            ->first();

        $urutan = 1;
        if ($lastTiket) {
            $parts = explode('-', $lastTiket->no_tiket);
            $lastNum = (int) end($parts);
            $urutan = $lastNum + 1;
        }

        return $prefix . str_pad($urutan, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Hitung durasi gangguan (MTTR) dalam menit.
     */
    public function hitungMttr(Carbon|string $tanggalOpen, Carbon|string|null $tanggalClose = null): int
    {
        $open = $tanggalOpen instanceof Carbon ? $tanggalOpen : Carbon::parse($tanggalOpen);
        $close = $tanggalClose instanceof Carbon ? $tanggalClose : ($tanggalClose ? Carbon::parse($tanggalClose) : Carbon::now());

        return max(0, (int) $open->diffInMinutes($close));
    }

    /**
     * Cek status kepatuhan SLA: TEPAT atau LEBIH
     */
    public function cekSla(int $mttrMinutes, ?int $slaTargetMinutes): string
    {
        if ($slaTargetMinutes === null || $slaTargetMinutes <= 0) {
            return 'NA';
        }

        return $mttrMinutes <= $slaTargetMinutes ? 'TEPAT' : 'LEBIH';
    }

    /**
     * Dapatkan target SLA default dari MasterSla berdasarkan segment
     */
    public function getDefaultSlaTarget(string $segment): int
    {
        $masterSla = MasterSla::where('backbone_segment', $segment)->first();
        if ($masterSla && $masterSla->sla_target_minutes > 0) {
            return $masterSla->sla_target_minutes;
        }

        // Default 6 jam (360 menit) jika tidak ditemukan
        return 360;
    }

    /**
     * Buat tiket baru (Open Tiket)
     */
    public function createTiket(array $data, User $creator): Tiket
    {
        return DB::transaction(function () use ($data, $creator) {
            $tanggalOpen = !empty($data['tanggal_open'])
                ? Carbon::parse($data['tanggal_open'])
                : Carbon::now();

            $noTiket = !empty($data['no_tiket'])
                ? $data['no_tiket']
                : $this->generateNoTiket($tanggalOpen);

            $slaTarget = !empty($data['sla_target_minutes'])
                ? (int) $data['sla_target_minutes']
                : $this->getDefaultSlaTarget($data['backbone_segment']);

            $tiket = Tiket::create([
                'no_tiket' => $noTiket,
                'status_link_impact' => $data['status_link_impact'],
                'backbone_segment' => $data['backbone_segment'],
                'deskripsi' => $data['deskripsi'] ?? null,
                'tanggal_open' => $tanggalOpen,
                'tanggal_close' => null,
                'status' => 'OPEN',
                'mttr_minutes' => null,
                'sla_target_minutes' => $slaTarget,
                'sla_status' => 'NA',
                'created_by' => $creator->id,
                'closed_by' => null,
            ]);

            if ($this->notificationService) {
                try {
                    $this->notificationService->notifyTiketBaru($tiket);
                } catch (\Throwable $e) {
                    // Fail silently to not abort main transaction
                }
            }

            return $tiket;
        });
    }

    /**
     * Update data tiket (Hanya jika status OPEN)
     */
    public function updateTiket(Tiket $tiket, array $data, ?User $updater = null): Tiket
    {
        if ($tiket->status !== 'OPEN') {
            throw new InvalidArgumentException('Tiket dengan status ' . $tiket->status . ' tidak dapat diedit kembali.');
        }

        $updateData = [
            'status_link_impact' => $data['status_link_impact'] ?? $tiket->status_link_impact,
            'backbone_segment' => $data['backbone_segment'] ?? $tiket->backbone_segment,
            'deskripsi' => array_key_exists('deskripsi', $data) ? $data['deskripsi'] : $tiket->deskripsi,
        ];

        if (!empty($data['tanggal_open'])) {
            $updateData['tanggal_open'] = Carbon::parse($data['tanggal_open']);
        }

        if (!empty($data['sla_target_minutes'])) {
            $updateData['sla_target_minutes'] = (int) $data['sla_target_minutes'];
        }

        $tiket->update($updateData);

        $fresh = $tiket->fresh();

        if ($this->notificationService) {
            try {
                $this->notificationService->notifyTiketUpdated($fresh, $updater);
            } catch (\Throwable $e) {
                // Fail silently to not abort main transaction
            }
        }

        return $fresh;
    }

    /**
     * Close Tiket (Penutupan Tiket)
     */
    public function closeTiket(Tiket $tiket, User $closer, ?array $closeData = []): Tiket
    {
        if ($tiket->status === 'CLOSE') {
            throw new InvalidArgumentException('Tiket ' . $tiket->no_tiket . ' sudah dalam status CLOSE.');
        }

        return DB::transaction(function () use ($tiket, $closer, $closeData) {
            $tanggalClose = !empty($closeData['tanggal_close'])
                ? Carbon::parse($closeData['tanggal_close'])
                : Carbon::now();

            $mttrMinutes = $this->hitungMttr($tiket->tanggal_open, $tanggalClose);
            $slaStatus = $this->cekSla($mttrMinutes, $tiket->sla_target_minutes);

            $tiket->update([
                'tanggal_close' => $tanggalClose,
                'status' => 'CLOSE',
                'mttr_minutes' => $mttrMinutes,
                'sla_status' => $slaStatus,
                'closed_by' => $closer->id,
            ]);

            $freshTiket = $tiket->fresh(['closer']);

            if ($this->notificationService) {
                try {
                    $this->notificationService->notifyTiketClosed($freshTiket);
                } catch (\Throwable $e) {
                    // Fail silently
                }
            }

            return $freshTiket;
        });
    }

    /**
     * Hapus Tiket (Admin Only)
     */
    public function deleteTiket(Tiket $tiket, User $user): bool
    {
        if (!$user->hasRole('admin')) {
            throw new InvalidArgumentException('Hanya Admin yang berhak menghapus tiket.');
        }

        return (bool) $tiket->delete();
    }

    /**
     * Query tiket terfilter & terpaginasi
     */
    public function getFilteredTiket(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = Tiket::with(['creator', 'closer'])
            ->latest('tanggal_open');

        // Filter search keyword (no_tiket, status_link_impact)
        if (!empty($filters['q'])) {
            $search = trim($filters['q']);
            $query->where(function (Builder $q) use ($search) {
                $q->where('no_tiket', 'like', "%{$search}%")
                  ->orWhere('status_link_impact', 'like', "%{$search}%")
                  ->orWhere('backbone_segment', 'like', "%{$search}%");
            });
        }

        // Filter status
        if (!empty($filters['status'])) {
            if ($filters['status'] === 'AKTIF') {
                $query->whereIn('status', ['OPEN', 'PROSES']);
            } elseif (in_array($filters['status'], ['OPEN', 'PROSES', 'CLOSE'], true)) {
                $query->where('status', $filters['status']);
            }
        }

        // Filter SLA status
        if (!empty($filters['sla_status']) && in_array($filters['sla_status'], ['TEPAT', 'LEBIH', 'NA'], true)) {
            $query->where('sla_status', $filters['sla_status']);
        }

        // Filter Backbone Segment
        if (!empty($filters['segment'])) {
            $query->where('backbone_segment', $filters['segment']);
        }

        // Filter Tanggal Open (Single Date or Range)
        if (!empty($filters['tanggal'])) {
            $query->whereDate('tanggal_open', $filters['tanggal']);
        }

        if (!empty($filters['start_date'])) {
            $query->whereDate('tanggal_open', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->whereDate('tanggal_open', '<=', $filters['end_date']);
        }

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Dapatkan ringkasan statistik tiket
     */
    public function getTiketSummary(): array
    {
        $total = Tiket::count();
        $open = Tiket::where('status', 'OPEN')->count();
        $proses = Tiket::where('status', 'PROSES')->count();
        $close = Tiket::where('status', 'CLOSE')->count();
        $lebihSla = Tiket::where('sla_status', 'LEBIH')->count();
        $tepatSla = Tiket::where('sla_status', 'TEPAT')->count();

        return [
            'total' => $total,
            'open' => $open,
            'proses' => $proses,
            'close' => $close,
            'lebih_sla' => $lebihSla,
            'tepat_sla' => $tepatSla,
        ];
    }
}
