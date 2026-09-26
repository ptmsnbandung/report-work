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
        protected WhatsAppService $waService,
        protected WebPushService $webPushService
    ) {}

    /**
     * Kirim notifikasi saat tiket baru dibuat (ke Seluruh Tim NOC: Admin, Helpdesk, Teknis, SA/CS)
     */
    public function notifyTiketBaru(Tiket $tiket): void
    {
        $creatorId = $tiket->created_by;

        $recipients = User::where('is_active', true)
            ->whereIn('role', ['admin', 'helpdesk', 'teknis', 'sa_cs'])
            ->when($creatorId, fn($q) => $q->where('id', '!=', $creatorId))
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

            // Web Push Notification ke HP & Browser pengguna
            try {
                $this->webPushService->sendToUsers(
                    $recipients,
                    "🚨 Tiket Baru: {$tiket->no_tiket}",
                    "Gangguan di {$tiket->backbone_segment}. Target SLA: {$tiket->formatted_sla_target}.",
                    route('tiket.show', $tiket->id)
                );
            } catch (\Throwable $e) {
                Log::warning('WebPush failed for new ticket: ' . $e->getMessage());
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
     * Hanya dikirim ke pengguna lain (pengirim pesan tidak akan menerima notifikasi)
     */
    public function notifyKronologisBaru(Tiket $tiket, Kronologis $kronologis): void
    {
        $senderId = $kronologis->user_id;

        $recipients = User::where('is_active', true)
            ->whereIn('role', ['admin', 'helpdesk', 'teknis', 'sa_cs'])
            ->when($senderId, fn($q) => $q->where('id', '!=', $senderId))
            ->get();

        if ($recipients->isNotEmpty()) {
            Notification::send($recipients, new KronologisBaruNotification($tiket, $kronologis));

            foreach ($recipients as $u) {
                NotifikasiLog::create([
                    'id_tiket' => $tiket->id,
                    'tipe' => 'INAPP',
                    'penerima' => "{$u->name} ({$u->role})",
                    'pesan' => "Update Koordinasi pada tiket {$tiket->no_tiket}",
                    'status' => 'SENT',
                ]);
            }

            // Web Push Notification ke HP & Browser pengguna (Format WhatsApp-Style)
            try {
                $senderName = $kronologis->user ? $kronologis->user->name : 'Teknis';
                $senderAvatar = $kronologis->user?->avatar_url 
                    ?: ('https://ui-avatars.com/api/?name=' . urlencode($senderName) . '&background=0284c7&color=fff&size=192&bold=true&rounded=true');
                $cleanInfo = preg_replace('/^>\s*/m', '', (string) $kronologis->informasi);
                $this->webPushService->sendToUsers(
                    $recipients,
                    $senderName,
                    "{$tiket->no_tiket}: " . ($cleanInfo ?: 'Pembaruan koordinasi lapangan.'),
                    route('tiket.show', $tiket->id),
                    $senderAvatar
                );
            } catch (\Throwable $e) {
                Log::warning('WebPush failed for new kronologis: ' . $e->getMessage());
            }
        }

        // WhatsApp ke staf yang memiliki nomor HP (selain pengirim)
        $waMessage = $this->waService->formatKronologisMessage($tiket, $kronologis);
        foreach ($recipients->whereNotNull('phone') as $u) {
            $this->waService->sendMessage($u->phone, $waMessage, $tiket->id);
        }
    }

    /**
     * Kirim notifikasi saat data tiket diperbarui (ke Admin, Helpdesk, Teknis, SA/CS)
     * Hanya dikirim ke pengguna lain (pengubah data tidak akan menerima notifikasi)
     */
    public function notifyTiketUpdated(Tiket $tiket, ?User $updater = null, string $keterangan = 'Perbaruan data tiket'): void
    {
        $recipients = User::where('is_active', true)
            ->whereIn('role', ['admin', 'helpdesk', 'teknis', 'sa_cs'])
            ->when($updater, fn($q) => $q->where('id', '!=', $updater->id))
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
     * Hanya dikirim ke pengguna lain selain yang menutup tiket
     */
    public function notifyTiketClosed(Tiket $tiket): void
    {
        $closerId = $tiket->closed_by;

        $recipients = User::where('is_active', true)
            ->whereIn('role', ['admin', 'helpdesk', 'teknis', 'sa_cs'])
            ->when($closerId, fn($q) => $q->where('id', '!=', $closerId))
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

            // Web Push Notification ke HP & Browser pengguna
            try {
                $this->webPushService->sendToUsers(
                    $recipients,
                    "✅ Tiket Closed: {$tiket->no_tiket}",
                    "Perbaikan {$tiket->backbone_segment} selesai. MTTR: {$tiket->formatted_mttr} ({$tiket->sla_status}).",
                    route('tiket.show', $tiket->id)
                );
            } catch (\Throwable $e) {
                Log::warning('WebPush failed for closed ticket: ' . $e->getMessage());
            }
        }

        // WhatsApp ke seluruh pihak yang memiliki nomor HP
        $waMessage = $this->waService->formatTiketClosedMessage($tiket);
        foreach ($recipients->whereNotNull('phone') as $u) {
            $this->waService->sendMessage($u->phone, $waMessage, $tiket->id);
        }
    }

    /**
     * Kirim notifikasi serah terima / handover shift ke petugas penerima
     */
    public function notifyHandoverShift(\App\Models\TiketHandoverShift $handover): void
    {
        $targetUser = $handover->userTo;
        $tiket = $handover->tiket;
        $sender = $handover->userFrom;

        if (!$targetUser || !$tiket) {
            return;
        }

        // 1. Database & Mail Notification
        $targetUser->notify(new \App\Notifications\HandoverShiftNotification($handover));

        // 2. In-App Notification Log
        NotifikasiLog::create([
            'id_tiket' => $tiket->id,
            'tipe' => 'INAPP',
            'penerima' => "{$targetUser->name} ({$targetUser->role})",
            'pesan' => "Handover Shift {$handover->shift_from} ke {$handover->shift_to} dari {$sender?->name}",
            'status' => 'SENT',
        ]);

        // 3. Web Push Notification ke HP & Browser petugas penerima
        try {
            $this->webPushService->sendToUser(
                $targetUser,
                "🔄 Handover Tiket: {$tiket->no_tiket}",
                "Serah terima ({$handover->shift_from} ➔ {$handover->shift_to}) dari {$sender?->name}. Catatan: {$handover->catatan_handover}",
                route('tiket.show', $tiket->id)
            );
        } catch (\Throwable $e) {
            Log::warning('WebPush failed for handover shift: ' . $e->getMessage());
        }
    }
}
