<?php

namespace App\Services;

use App\Models\Kronologis;
use App\Models\MasterSla;
use App\Models\Tiket;
use App\Models\TiketHandoverShift;
use App\Models\TiketStopClock;
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
     * Hitung durasi gangguan (MTTR) dalam menit dengan kompensasi Stop Clock.
     */
    public function hitungMttr(Carbon|string $tanggalOpen, Carbon|string|null $tanggalClose = null, int $totalStopClockMinutes = 0): int
    {
        $open = $tanggalOpen instanceof Carbon ? $tanggalOpen : Carbon::parse($tanggalOpen);
        $close = $tanggalClose instanceof Carbon ? $tanggalClose : ($tanggalClose ? Carbon::parse($tanggalClose) : Carbon::now());

        $grossMinutes = max(0, (int) $open->diffInMinutes($close));
        $adjustedMinutes = max(0, $grossMinutes - $totalStopClockMinutes);

        return $adjustedMinutes;
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
                'tipe_penanganan' => $data['tipe_penanganan'] ?? 'JOINTING_LURUS',
                'tanggal_open' => $tanggalOpen,
                'tanggal_close' => null,
                'status' => 'OPEN',
                'mttr_minutes' => null,
                'total_stop_clock_minutes' => 0,
                'is_stop_clock' => false,
                'sla_target_minutes' => $slaTarget,
                'sla_status' => 'NA',
                'created_by' => $creator->id,
                'closed_by' => null,
            ]);

            if ($this->notificationService) {
                try {
                    $this->notificationService->notifyTiketBaru($tiket);
                } catch (\Throwable $e) {
                    // Fail silently
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
            'tipe_penanganan' => $data['tipe_penanganan'] ?? $tiket->tipe_penanganan,
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
                // Fail silently
            }
        }

        return $fresh;
    }

    /**
     * Catat waktu respon pertama teknisi
     */
    public function recordFirstResponse(Tiket $tiket, User $user): void
    {
        if ($tiket->first_response_at === null && $user->hasRole('teknis')) {
            $firstResponseAt = Carbon::now();
            $responseTime = max(0, (int) $tiket->tanggal_open->diffInMinutes($firstResponseAt));

            $tiket->update([
                'first_response_at' => $firstResponseAt,
                'response_time_minutes' => $responseTime,
                'status' => $tiket->status === 'OPEN' ? 'PROSES' : $tiket->status,
            ]);
        }
    }

    /**
     * Tahap 1: Closing Awal oleh Teknisi Lapangan
     */
    public function closingAwal(Tiket $tiket, User $teknisi, array $data): Tiket
    {
        if ($tiket->status === 'CLOSE') {
            throw new InvalidArgumentException('Tiket sudah berstatus CLOSE.');
        }

        // Cek prasyarat mandatori
        $prerequisites = $tiket->checkClosingPrerequisites();
        if (!$prerequisites['is_eligible']) {
            throw new InvalidArgumentException(
                'Prasyarat Closing Awal belum lengkap: ' . implode(' ', $prerequisites['missing'])
            );
        }

        return DB::transaction(function () use ($tiket, $teknisi, $data) {
            $resolvedAt = !empty($data['resolved_at'])
                ? Carbon::parse($data['resolved_at'])
                : Carbon::now();

            $tipePenanganan = $data['tipe_penanganan'] ?? ($tiket->tipe_penanganan ?: 'JOINTING_LURUS');
            $catatan = $data['closing_notes_teknisi'] ?? ($data['catatan'] ?? null);

            // Jika sedang stop clock, otomatis stop
            if ($tiket->is_stop_clock) {
                $this->stopStopClock($tiket, $teknisi);
            }

            $tiket->update([
                'status' => 'PENDING_VERIFIKASI',
                'resolved_by' => $teknisi->id,
                'resolved_at' => $resolvedAt,
                'closing_notes_teknisi' => $catatan,
                'tipe_penanganan' => $tipePenanganan,
            ]);

            // Sinkronkan tipe penanganan ke Resume Pekerjaan jika ada
            if ($tiket->resume) {
                $tiket->resume->update([
                    'tipe_penanganan' => $tipePenanganan,
                    'joint_closure_type' => $data['joint_closure_type'] ?? $tiket->resume->joint_closure_type,
                    'core_count_jointed' => $data['core_count_jointed'] ?? $tiket->resume->core_count_jointed,
                ]);
            }

            // Catat ke kronologis otomatis
            $tipeLabel = match ($tipePenanganan) {
                'MANUVER_CORE' => 'Manuver Core (Swapping Core)',
                'JOINTING_LURUS' => 'Jointing Lurus (Straight Splice)',
                default => 'Lainnya / Normalisasi',
            };

            Kronologis::create([
                'id_tiket' => $tiket->id,
                'user_id' => $teknisi->id,
                'informasi' => "🛠️ [CLOSING AWAL - LAPANGAN SELESAI]\nTeknisi {$teknisi->name} telah menyelesaikan perbaikan fisik di lapangan.\n• Tipe Penanganan: {$tipeLabel}\n• Catatan: " . ($catatan ?: 'Pekerjaan perbaikan fisik telah selesai.') . "\nMenunggu verifikasi akhir & Closing Tiket oleh HelpDesk NOC.",
                'kategori' => 'SELESAI',
                'timestamp' => $resolvedAt,
            ]);

            $fresh = $tiket->fresh(['resolver', 'resume']);

            if ($this->notificationService) {
                try {
                    $this->notificationService->notifyTiketUpdated($fresh, $teknisi);
                } catch (\Throwable $e) {}
            }

            return $fresh;
        });
    }

    /**
     * Tahap 2: Closing Akhir / Verifikasi oleh HelpDesk / Admin
     */
    public function closeTiket(Tiket $tiket, User $closer, ?array $closeData = []): Tiket
    {
        if ($tiket->status === 'CLOSE') {
            throw new InvalidArgumentException('Tiket ' . $tiket->no_tiket . ' sudah dalam status CLOSE.');
        }

        if ($tiket->status !== 'PENDING_VERIFIKASI') {
            throw new InvalidArgumentException("Tiket {$tiket->no_tiket} tidak dapat ditutup karena teknisi belum melakukan Closing Awal (Status tiket saat ini: {$tiket->status}).");
        }

        return DB::transaction(function () use ($tiket, $closer, $closeData) {
            $tanggalClose = !empty($closeData['tanggal_close'])
                ? Carbon::parse($closeData['tanggal_close'])
                : Carbon::now();

            // Jika sedang stop clock, otomatis stop
            if ($tiket->is_stop_clock) {
                $this->stopStopClock($tiket, $closer);
            }

            // Hitung MTTR yang disesuaikan dengan total stop clock
            $totalStopClock = (int) ($tiket->stopClocks()->sum('duration_minutes') ?: $tiket->total_stop_clock_minutes);
            $mttrMinutes = $this->hitungMttr($tiket->tanggal_open, $tanggalClose, $totalStopClock);
            $slaStatus = $this->cekSla($mttrMinutes, $tiket->sla_target_minutes);

            $tiket->update([
                'tanggal_close' => $tanggalClose,
                'status' => 'CLOSE',
                'mttr_minutes' => $mttrMinutes,
                'total_stop_clock_minutes' => $totalStopClock,
                'sla_status' => $slaStatus,
                'closed_by' => $closer->id,
            ]);

            // Catat log kronologis
            $slaLabel = $slaStatus === 'TEPAT' ? 'TEPAT SLA' : 'MELEBIHI SLA';
            $jam = floor($mttrMinutes / 60);
            $menit = $mttrMinutes % 60;
            $mttrStr = $jam > 0 ? "{$jam} jam {$menit} menit" : "{$menit} menit";

            $stopClockInfo = $totalStopClock > 0 ? " (Total Stop Clock: {$totalStopClock} menit)" : "";

            Kronologis::create([
                'id_tiket' => $tiket->id,
                'user_id' => $closer->id,
                'informasi' => "✅ [CLOSING AKHIR - VERIFIKASI HD SELESAI]\nHelpDesk NOC ({$closer->name}) telah memverifikasi link normal dan menutup tiket ini.\n• MTTR Bersih: {$mttrStr}{$stopClockInfo}\n• Status SLA: {$slaLabel}\n• Catatan HD: " . ($closeData['catatan_hd'] ?? 'Link telah terpantau UP dan stabil.'),
                'kategori' => 'LINK_UP',
                'timestamp' => $tanggalClose,
            ]);

            $freshTiket = $tiket->fresh(['closer', 'resolver']);

            if ($this->notificationService) {
                try {
                    $this->notificationService->notifyTiketClosed($freshTiket);
                } catch (\Throwable $e) {}
            }

            return $freshTiket;
        });
    }

    /**
     * Kembalikan tiket dari Closing Awal ke PROSES (Reject / Re-open oleh HelpDesk)
     */
    public function rejectClosingAwal(Tiket $tiket, User $hd, string $alasan): Tiket
    {
        if ($tiket->status !== 'PENDING_VERIFIKASI') {
            throw new InvalidArgumentException('Hanya tiket yang berstatus PENDING VERIFIKASI yang dapat dikembalikan.');
        }

        return DB::transaction(function () use ($tiket, $hd, $alasan) {
            $tiket->update([
                'status' => 'PROSES',
            ]);

            Kronologis::create([
                'id_tiket' => $tiket->id,
                'user_id' => $hd->id,
                'informasi' => "⚠️ [VERIFIKASI DITOLAK - KEMBALI PROSES]\nHelpDesk NOC ({$hd->name}) mengembalikan status tiket ke PROSES.\n• Alasan: {$alasan}\nMohon tim teknis memeriksa kembali kondisi lapangan.",
                'kategori' => 'LAIN',
                'timestamp' => Carbon::now(),
            ]);

            $fresh = $tiket->fresh();

            if ($this->notificationService) {
                try {
                    $this->notificationService->notifyTiketUpdated($fresh, $hd);
                } catch (\Throwable $e) {}
            }

            return $fresh;
        });
    }

    /**
     * Mulai Jeda SLA (Start Stop Clock)
     */
    public function startStopClock(Tiket $tiket, User $user, array $data): TiketStopClock
    {
        if ($tiket->status === 'CLOSE') {
            throw new InvalidArgumentException('Tiket sudah CLOSE, tidak dapat memulai Stop Clock.');
        }

        if ($tiket->is_stop_clock) {
            throw new InvalidArgumentException('Tiket saat ini sudah dalam kondisi Stop Clock.');
        }

        return DB::transaction(function () use ($tiket, $user, $data) {
            $startTime = !empty($data['start_time']) ? Carbon::parse($data['start_time']) : Carbon::now();

            $stopClock = TiketStopClock::create([
                'id_tiket' => $tiket->id,
                'start_time' => $startTime,
                'end_time' => null,
                'duration_minutes' => 0,
                'alasan_kategori' => $data['alasan_kategori'] ?? 'IZIN_AKSES',
                'alasan_detail' => $data['alasan_detail'] ?? null,
                'requested_by' => $user->id,
                'stopped_by' => null,
                'is_active' => true,
            ]);

            $tiket->update([
                'is_stop_clock' => true,
            ]);

            $alasanLabel = $stopClock->alasan_kategori_label;

            Kronologis::create([
                'id_tiket' => $tiket->id,
                'user_id' => $user->id,
                'informasi' => "⏸️ [STOP CLOCK AKTIF - SLA DIJEDA]\nUser ({$user->name}) mengaktifkan Stop Clock.\n• Kategori: {$alasanLabel}\n• Keterangan: " . ($data['alasan_detail'] ?: '-'),
                'kategori' => 'LAIN',
                'timestamp' => $startTime,
            ]);

            return $stopClock;
        });
    }

    /**
     * Hentikan Jeda SLA (Stop / Resume Stop Clock)
     */
    public function stopStopClock(Tiket $tiket, User $user): ?TiketStopClock
    {
        $activeStop = $tiket->activeStopClock;
        if (!$activeStop) {
            $tiket->update(['is_stop_clock' => false]);
            return null;
        }

        return DB::transaction(function () use ($tiket, $user, $activeStop) {
            $endTime = Carbon::now();
            $durationMinutes = max(1, (int) $activeStop->start_time->diffInMinutes($endTime));

            $activeStop->update([
                'end_time' => $endTime,
                'duration_minutes' => $durationMinutes,
                'stopped_by' => $user->id,
                'is_active' => false,
            ]);

            $totalStopClock = (int) $tiket->stopClocks()->sum('duration_minutes');

            $tiket->update([
                'is_stop_clock' => false,
                'total_stop_clock_minutes' => $totalStopClock,
            ]);

            Kronologis::create([
                'id_tiket' => $tiket->id,
                'user_id' => $user->id,
                'informasi' => "▶️ [RESUME CLOCK - SLA DILANJUTKAN]\nUser ({$user->name}) mengakhiri Stop Clock.\n• Durasi Jeda: {$durationMinutes} menit\n• Total Jeda SLA Tiket: {$totalStopClock} menit",
                'kategori' => 'LAIN',
                'timestamp' => $endTime,
            ]);

            return $activeStop->fresh();
        });
    }

    /**
     * Handover / Oper Shift Tiket
     */
    public function handoverShift(Tiket $tiket, User $user, array $data): TiketHandoverShift
    {
        return DB::transaction(function () use ($tiket, $user, $data) {
            $handover = TiketHandoverShift::create([
                'id_tiket' => $tiket->id,
                'shift_from' => $data['shift_from'],
                'shift_to' => $data['shift_to'],
                'user_from_id' => $user->id,
                'user_to_id' => $data['user_to_id'] ?? null,
                'catatan_handover' => $data['catatan_handover'],
            ]);

            $targetUserName = $handover->userTo?->name ?? 'PIC Shift Baru';

            Kronologis::create([
                'id_tiket' => $tiket->id,
                'user_id' => $user->id,
                'informasi' => "🔄 [OPER SHIFT / HANDOVER]\nSerah terima penanganan tiket dari {$data['shift_from']} ke {$data['shift_to']}.\n• Dari: {$user->name}\n• Kepada: {$targetUserName}\n• Catatan: {$data['catatan_handover']}",
                'kategori' => 'LAIN',
                'timestamp' => Carbon::now(),
            ]);

            if ($this->notificationService) {
                $this->notificationService->notifyHandoverShift($handover);
            }

            return $handover;
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
        $query = Tiket::with(['creator', 'resolver', 'closer', 'activeStopClock'])
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
                $query->whereIn('status', ['OPEN', 'PROSES', 'PENDING_VERIFIKASI']);
            } elseif (in_array($filters['status'], ['OPEN', 'PROSES', 'PENDING_VERIFIKASI', 'CLOSE'], true)) {
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
        $pendingVerifikasi = Tiket::where('status', 'PENDING_VERIFIKASI')->count();
        $close = Tiket::where('status', 'CLOSE')->count();
        $lebihSla = Tiket::where('sla_status', 'LEBIH')->count();
        $tepatSla = Tiket::where('sla_status', 'TEPAT')->count();
        $stopClockCount = Tiket::where('is_stop_clock', true)->count();

        return [
            'total' => $total,
            'open' => $open,
            'proses' => $proses,
            'pending_verifikasi' => $pendingVerifikasi,
            'close' => $close,
            'lebih_sla' => $lebihSla,
            'tepat_sla' => $tepatSla,
            'stop_clock_count' => $stopClockCount,
        ];
    }
}
