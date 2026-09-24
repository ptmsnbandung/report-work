<?php

namespace App\Notifications;

use App\Models\Tiket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TiketClosedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Tiket $tiket
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = route('tiket.show', $this->tiket->id);
        $slaStatus = $this->tiket->sla_status === 'TEPAT' ? 'SESUAI TARGET SLA' : 'MELEBIHI TARGET SLA';

        return (new MailMessage)
            ->subject("[TIKET CLOSED] {$this->tiket->no_tiket} - {$this->tiket->backbone_segment} (MTTR: {$this->tiket->formatted_mttr})")
            ->greeting("Halo {$notifiable->name},")
            ->line("Tiket gangguan **{$this->tiket->no_tiket}** telah berhasil diselesaikan dan di-CLOSE.")
            ->line("Segment Backbone: **{$this->tiket->backbone_segment}**")
            ->line("Durasi MTTR: **{$this->tiket->formatted_mttr}** ({$this->tiket->mttr_minutes} menit)")
            ->line("Status SLA: **{$slaStatus}** (Target: {$this->tiket->formatted_sla_target})")
            ->action('Lihat Rekap & Berita Acara', $url)
            ->line('Silakan informasikan update perbaikan ini kepada pelanggan/client terkait.');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'TIKET_CLOSED',
            'id_tiket' => $this->tiket->id,
            'no_tiket' => $this->tiket->no_tiket,
            'title' => "Tiket Closed: {$this->tiket->no_tiket}",
            'message' => "Perbaikan {$this->tiket->backbone_segment} selesai. MTTR: {$this->tiket->formatted_mttr} ({$this->tiket->sla_status}).",
            'icon' => 'bi-check-circle-fill',
            'color' => 'success',
            'url' => route('tiket.show', $this->tiket->id),
            'created_at' => now()->toIso8601String(),
        ];
    }
}
