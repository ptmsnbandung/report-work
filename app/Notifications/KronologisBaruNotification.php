<?php

namespace App\Notifications;

use App\Models\Kronologis;
use App\Models\Tiket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class KronologisBaruNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Tiket $tiket,
        public Kronologis $kronologis
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = route('tiket.show', $this->tiket->id);
        $userNama = $this->kronologis->user ? $this->kronologis->user->name : 'Teknis';

        return (new MailMessage)
            ->subject("[UPDATE KOORDINASI] {$this->tiket->no_tiket}")
            ->greeting("Halo {$notifiable->name},")
            ->line("Ada update koordinasi terbaru pada tiket **{$this->tiket->no_tiket}** ({$this->tiket->backbone_segment}):")
            ->line("Waktu: " . ($this->kronologis->timestamp ? $this->kronologis->timestamp->format('H:i') . ' WIB' : '-'))
            ->line("Oleh: {$userNama}")
            ->line("Informasi: {$this->kronologis->informasi}")
            ->action('Lihat Timeline Tiket', $url);
    }

    public function toDatabase(object $notifiable): array
    {
        $userNama = $this->kronologis->user ? $this->kronologis->user->name : 'Teknis';

        return [
            'type' => 'KRONOLOGIS_BARU',
            'id_tiket' => $this->tiket->id,
            'no_tiket' => $this->tiket->no_tiket,
            'title' => "Update Koordinasi - {$this->tiket->no_tiket}",
            'message' => "{$userNama}: {$this->kronologis->informasi}",
            'icon' => 'bi-chat-dots-fill',
            'color' => 'info',
            'url' => route('tiket.show', $this->tiket->id),
            'created_at' => now()->toIso8601String(),
        ];
    }
}
