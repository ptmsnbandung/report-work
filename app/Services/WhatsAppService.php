<?php

namespace App\Services;

use App\Models\Kronologis;
use App\Models\NotifikasiLog;
use App\Models\Tiket;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected ?string $apiUrl;
    protected ?string $apiKey;
    protected bool $isSimulated;

    public function __construct()
    {
        $this->apiUrl = config('services.whatsapp.api_url', env('WA_API_URL', 'https://api.fonnte.com/send'));
        $this->apiKey = config('services.whatsapp.api_key', env('WA_API_KEY', ''));
        $this->isSimulated = config('services.whatsapp.simulate', env('WA_SIMULATE', true));
    }

    /**
     * Kirim pesan WhatsApp ke nomor tujuan
     *
     * @param string $phone
     * @param string $message
     * @param int|null $idTiket
     * @return bool
     */
    public function sendMessage(string $phone, string $message, ?int $idTiket = null): bool
    {
        $phone = $this->formatPhoneNumber($phone);
        $status = 'SENT';

        if ($this->isSimulated || empty($this->apiKey)) {
            // Mode Simulasi / Logging
            Log::info("[WhatsApp Simulated] Sent to {$phone}: \n{$message}");
        } else {
            try {
                $response = Http::withHeaders([
                    'Authorization' => $this->apiKey,
                ])->timeout(10)->post($this->apiUrl, [
                    'target' => $phone,
                    'message' => $message,
                ]);

                if (!$response->successful()) {
                    $status = 'FAILED';
                    Log::error("[WhatsApp API Error] {$response->status()}: " . $response->body());
                }
            } catch (\Throwable $e) {
                $status = 'FAILED';
                Log::error("[WhatsApp Exception] " . $e->getMessage());
            }
        }

        // Catat log jika terkait tiket
        if ($idTiket) {
            try {
                NotifikasiLog::create([
                    'id_tiket' => $idTiket,
                    'tipe' => 'WA',
                    'penerima' => $phone,
                    'pesan' => $message,
                    'status' => $status,
                ]);
            } catch (\Throwable $e) {
                Log::warning("Failed to log notification: " . $e->getMessage());
            }
        }

        return $status === 'SENT';
    }

    /**
     * Format nomor HP agar standar (628xxx)
     */
    public function formatPhoneNumber(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        return $phone;
    }

    /**
     * Format template pesan WhatsApp untuk Tiket Baru
     */
    public function formatTiketBaruMessage(Tiket $tiket): string
    {
        $tgl = $tiket->tanggal_open ? $tiket->tanggal_open->format('d/m/Y H:i') : now()->format('d/m/Y H:i');
        $keterangan = $tiket->keterangan ?: '-';

        return "🚨 *[NOTIFIKASI NOC CJP - TIKET GANGGUAN BARU]*\n\n"
            . "📌 *No Tiket:* {$tiket->no_tiket}\n"
            . "📍 *Segment:* {$tiket->backbone_segment}\n"
            . "⏰ *Waktu Open:* {$tgl} WIB\n"
            . "🎯 *SLA Target:* {$tiket->sla_target_minutes} Menit\n"
            . "📝 *Keterangan:* {$keterangan}\n\n"
            . "Mohon tim teknis segera bersiap dan melakukan investigasi lapangan.\n"
            . "🔗 " . route('tiket.show', $tiket->id);
    }

    /**
     * Format template pesan WhatsApp untuk Update Kronologis
     */
    public function formatKronologisMessage(Tiket $tiket, Kronologis $kronologis): string
    {
        $userNama = $kronologis->user ? $kronologis->user->name : 'Teknis';

        return "⏱️ *[UPDATE PROGRESS GANGGUAN - NOC CJP]*\n\n"
            . "📌 *Tiket:* {$tiket->no_tiket} ({$tiket->backbone_segment})\n"
            . "🏷️ *Kategori:* [{$kronologis->kategori}]\n"
            . "⏰ *Waktu:* {$kronologis->jam_kronologis} WIB\n"
            . "👤 *Petugas:* {$userNama}\n"
            . "📄 *Update:* {$kronologis->keterangan}\n\n"
            . "🔗 " . route('tiket.show', $tiket->id);
    }

    /**
     * Format template pesan WhatsApp untuk Tiket Selesai / Closed
     */
    public function formatTiketClosedMessage(Tiket $tiket): string
    {
        $slaStatus = $tiket->sla_status === 'TEPAT' ? '✅ Sesuai SLA' : '⚠️ Melebihi SLA';

        return "🎉 *[GANGGUAN SELESAI / TIKET CLOSED - NOC CJP]*\n\n"
            . "📌 *No Tiket:* {$tiket->no_tiket}\n"
            . "📍 *Segment:* {$tiket->backbone_segment}\n"
            . "⏱️ *MTTR:* {$tiket->formatted_mttr} ({$tiket->mttr_minutes} mnt)\n"
            . "📊 *Status SLA:* {$slaStatus} (Target: {$tiket->sla_target_minutes} mnt)\n"
            . "👤 *Ditutup Oleh:* " . ($tiket->closer ? $tiket->closer->name : 'HelpDesk') . "\n\n"
            . "Jaringan telah kembali normal. Terima kasih atas kerjasamanya.\n"
            . "🔗 " . route('tiket.show', $tiket->id);
    }
}
