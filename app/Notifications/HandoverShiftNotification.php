<?php

namespace App\Notifications;

use App\Models\TiketHandoverShift;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class HandoverShiftNotification extends Notification
{
    use Queueable;

    public function __construct(
        public TiketHandoverShift $handover
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $tiket = $this->handover->tiket;
        $sender = $this->handover->userFrom;
        $url = route('tiket.show', $tiket->id);

        return (new MailMessage)
            ->subject("[OPER SHIFT] Penyerahan Tiket {$tiket->no_tiket} ({$this->handover->shift_to})")
            ->greeting("Halo {$notifiable->name},")
            ->line("Anda telah menerima serah terima (handover) penanganan tiket gangguan dari **{$sender?->name}**:")
            ->line("• No Tiket: **{$tiket->no_tiket}**")
            ->line("• Segment: **{$tiket->backbone_segment}**")
            ->line("• Shift: **{$this->handover->shift_from}** ➔ **{$this->handover->shift_to}**")
            ->line("• Catatan Handover: {$this->handover->catatan_handover}")
            ->action('Buka & Lanjutkan Penanganan Tiket', $url);
    }

    public function toDatabase(object $notifiable): array
    {
        $tiket = $this->handover->tiket;
        $sender = $this->handover->userFrom;

        return [
            'type' => 'HANDOVER_SHIFT',
            'id_tiket' => $tiket->id,
            'no_tiket' => $tiket->no_tiket,
            'title' => "🔄 Handover Tiket: {$tiket->no_tiket}",
            'message' => "Serah terima {$this->handover->shift_from} ➔ {$this->handover->shift_to} dari {$sender?->name}: {$this->handover->catatan_handover}",
            'icon' => 'bi-arrow-left-right',
            'color' => 'primary',
            'url' => route('tiket.show', $tiket->id),
            'created_at' => now()->toIso8601String(),
        ];
    }
}
