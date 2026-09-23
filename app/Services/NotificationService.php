<?php

namespace App\Services;

use App\Models\Kronologis;
use App\Models\NotifikasiLog;
use App\Models\Tiket;
use App\Models\User;
use App\Notifications\KronologisBaruNotification;
use App\Notifications\TiketBaruNotification;
use App\Notifications\TiketClosedNotification;
use App\Notifications\TiketUpdateNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class NotificationService
{
    public function __construct(
        protected WhatsAppService $waService
    ) {}

    /**
     * Kirim notifikasi saat tiket baru dibuat (ke Seluruh Tim NOC: Admin, Helpdesk, Teknis, SA/CS)
     */
    public function notifyTiketBaru(Tiket $tiket): void
    {
        $recipients = User::where('is_active', true)
            ->whereIn('role', ['admin', 'helpdesk', 'teknis', 'sa_cs'])
            ->get();

        if ($recipients->isNotEmpty()) {
            Notification::send($recipients, new TiketBaruNotification($tiket));

            // Log In-App
            foreach ($recipients as $u) {
                NotifikasiLog::create([
                    'id_tiket' => $tiket->id,
                    'tipe' => 'INAPP',
                    'penerima' => "{$u->name} ({$u->role})",
                    'pesan' => "Tiket Baru Open: {$tiket->no_tiket} - {$tiket->backbone_segment}",
                    'status' => 'SENT',
                ]);
            }
        }

        // Kirim WhatsApp ke nomor HP yang tersedia
        $waMessage = $this->waService->formatTiketBaruMessage($tiket);
        foreach ($recipients->whereNotNull('phone') as $u) {
            $this->waService->sendMessage($u->phone, $waMessage, $tiket->id);
        }
    }

    /**
     * Kirim notifikasi saat kronologis baru ditambahkan (ke Admin, Helpdesk, Teknis, SA/CS)
     */
    public function notifyKronologisBaru(Tiket $tiket, Kronologis $kronologis): void
    {
        $recipients = User::where('is_active', true)
            ->whereIn('role', ['admin', 'helpdesk', 'teknis', 'sa_cs'])
            ->get();

        if ($recipients->isNotEmpty()) {
            Notification::send($recipients, new KronologisBaruNotification($tiket, $kronologis));

            foreach ($recipients as $u) {
                NotifikasiLog::create([
                    'id_tiket' => $tiket->id,
                    'tipe' => 'INAPP',
                    'penerima' => "{$u->name} ({$u->role})",
                    'pesan' => "Update Kronologis [{$kronologis->kategori}] pada tiket {$tiket->no_tiket}",
                    'status' => 'SENT',
                ]);
            }
        }

        // WhatsApp ke staf yang memiliki nomor HP
        $waMessage = $this->waService->formatKronologisMessage($tiket, $kronologis);
        foreach ($recipients->whereNotNull('phone') as $u) {
            $this->waService->sendMessage($u->phone, $waMessage, $tiket->id);
        }
    }

    /**
     * Kirim notifikasi saat data tiket diperbarui (ke Admin, Helpdesk, Teknis, SA/CS)
     */
    public function notifyTiketUpdated(Tiket $tiket, ?User $updater = null, string $keterangan = 'Perbaruan data tiket'): void
    {
        $recipients = User::where('is_active', true)
            ->whereIn('role', ['admin', 'helpdesk', 'teknis', 'sa_cs'])
            ->get();

        if ($recipients->isNotEmpty()) {
            Notification::send($recipients, new TiketUpdateNotification($tiket, $updater, $keterangan));

            foreach ($recipients as $u) {
                NotifikasiLog::create([
                    'id_tiket' => $tiket->id,
                    'tipe' => 'INAPP',
                    'penerima' => "{$u->name} ({$u->role})",
                    'pesan' => "Tiket Diperbarui: {$tiket->no_tiket} - {$keterangan}",
                    'status' => 'SENT',
                ]);
            }
        }
    }

    /**
     * Kirim notifikasi progres pekerjaan lapangan (Resume, Material, Titik, Manuver, Dokumentasi)
     */
    public function notifyProgresPekerjaan(Tiket $tiket, string $keterangan, User $user): void
    {
        $this->notifyTiketUpdated($tiket, $user, $keterangan);
    }

    /**
     * Kirim notifikasi saat tiket selesai / closed (ke Seluruh Tim NOC & Client)
     */
    public function notifyTiketClosed(Tiket $tiket): void
    {
        $recipients = User::where('is_active', true)
            ->whereIn('role', ['admin', 'helpdesk', 'teknis', 'sa_cs', 'client'])
            ->get();

        if ($recipients->isNotEmpty()) {
            Notification::send($recipients, new TiketClosedNotification($tiket));

            foreach ($recipients as $u) {
                NotifikasiLog::create([
                    'id_tiket' => $tiket->id,
                    'tipe' => 'INAPP',
                    'penerima' => "{$u->name} ({$u->role})",
                    'pesan' => "Tiket Closed: {$tiket->no_tiket} - MTTR: {$tiket->formatted_mttr} ({$tiket->sla_status})",
                    'status' => 'SENT',
                ]);
            }
        }

        // WhatsApp ke seluruh pihak yang memiliki nomor HP
        $waMessage = $this->waService->formatTiketClosedMessage($tiket);
        foreach ($recipients->whereNotNull('phone') as $u) {
            $this->waService->sendMessage($u->phone, $waMessage, $tiket->id);
        }
    }
}
