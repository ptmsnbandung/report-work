<?php

namespace App\Notifications;

use App\Models\Tiket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TiketBaruNotification extends Notification
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

        return (new MailMessage)
            ->subject("[TIKET BARU OPEN] {$this->tiket->no_tiket} - {$this->tiket->backbone_segment}")
            ->greeting("Halo {$notifiable->name},")
            ->line("Tiket gangguan baru telah dibuka dengan nomor tiket: **{$this->tiket->no_tiket}**")
            ->line("Segment Backbone: **{$this->tiket->backbone_segment}**")
            ->line("Waktu Kejadian / Open: " . ($this->tiket->tanggal_open ? $this->tiket->tanggal_open->format('d/m/Y H:i') : '-'))
            ->line("Target SLA: {$this->tiket->sla_target_minutes} Menit")
            ->line("Keterangan: " . ($this->tiket->deskripsi ?: '-'))
            ->action('Lihat Detail Tiket', $url)
            ->line('Silakan segera menuju lokasi titik koordinat atau lakukan tindakan investigasi awal.');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'TIKET_BARU',
            'id_tiket' => $this->tiket->id,
            'no_tiket' => $this->tiket->no_tiket,
            'title' => "Tiket Baru Dibuka: {$this->tiket->no_tiket}",
            'message' => "Gangguan backbone di {$this->tiket->backbone_segment}. Target SLA: {$this->tiket->formatted_sla_target}.",
            'icon' => 'bi-ticket-detailed-fill',
            'color' => 'danger',
            'url' => route('tiket.show', $this->tiket->id),
            'created_at' => now()->toIso8601String(),
        ];
    }
}
