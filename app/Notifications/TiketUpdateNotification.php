<?php

namespace App\Notifications;

use App\Models\Tiket;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TiketUpdateNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Tiket $tiket,
        public ?User $updater = null,
        public string $keterangan = 'Perbaruan data tiket'
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = route('tiket.show', $this->tiket->id);
        $updaterName = $this->updater ? $this->updater->name : 'NOC Operator';

        return (new MailMessage)
            ->subject("[UPDATE TIKET] {$this->tiket->no_tiket} - {$this->tiket->backbone_segment}")
            ->greeting("Halo {$notifiable->name},")
            ->line("Data tiket gangguan **{$this->tiket->no_tiket}** telah diperbarui oleh {$updaterName}:")
            ->line("Segment Backbone: **{$this->tiket->backbone_segment}**")
            ->line("Dampak Gangguan: {$this->tiket->status_link_impact}")
            ->line("Keterangan: {$this->keterangan}")
            ->action('Lihat Detail Tiket', $url);
    }

    public function toDatabase(object $notifiable): array
    {
        $updaterName = $this->updater ? $this->updater->name : 'NOC';

        return [
            'type' => 'TIKET_UPDATE',
            'id_tiket' => $this->tiket->id,
            'no_tiket' => $this->tiket->no_tiket,
            'title' => "Tiket Diperbarui: {$this->tiket->no_tiket}",
            'message' => "{$updaterName}: {$this->keterangan} pada {$this->tiket->backbone_segment}",
            'icon' => 'bi-pencil-square',
            'color' => 'warning',
            'url' => route('tiket.show', $this->tiket->id),
            'created_at' => now()->toIso8601String(),
        ];
    }
}
