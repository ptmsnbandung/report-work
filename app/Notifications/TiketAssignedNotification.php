<?php

namespace App\Notifications;

use App\Models\Tiket;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TiketAssignedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Tiket $tiket,
        public ?User $manager = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $isAssigned = $this->tiket->isUserAssigned($notifiable);
        $managerName = $this->manager?->name ?? ($this->tiket->assignedBy?->name ?? 'Manager Teknis');
        $leadName = $this->tiket->assignedLead?->name ?? 'Lead Teknis';

        if ($isAssigned) {
            $isLead = (int) $this->tiket->assigned_lead_id === (int) $notifiable->id;
            $roleInJob = $isLead ? 'Lead Teknisi (PIC Utama)' : 'Anggota Tim Lapangan';

            return [
                'type' => 'TIKET_ASSIGNED_DIRECT',
                'id_tiket' => $this->tiket->id,
                'no_tiket' => $this->tiket->no_tiket,
                'title' => "👨‍🔧 TUGAS ANDA: Tiket {$this->tiket->no_tiket}",
                'message' => "Anda ditugaskan sebagai {$roleInJob} oleh {$managerName} pada segmen {$this->tiket->backbone_segment}.",
                'icon' => 'bi-person-check-fill',
                'color' => 'primary',
                'is_assigned' => true,
                'url' => route('tiket.show', $this->tiket->id),
                'created_at' => now()->toIso8601String(),
            ];
        }

        return [
            'type' => 'TIKET_ASSIGNED_INFO',
            'id_tiket' => $this->tiket->id,
            'no_tiket' => $this->tiket->no_tiket,
            'title' => "ℹ️ Info Penugasan: {$this->tiket->no_tiket}",
            'message' => "Tiket {$this->tiket->backbone_segment} telah ditugaskan ke {$leadName} & Tim oleh {$managerName}. (Mode Pemantauan)",
            'icon' => 'bi-info-circle-fill',
            'color' => 'info',
            'is_assigned' => false,
            'url' => route('tiket.show', $this->tiket->id),
            'created_at' => now()->toIso8601String(),
        ];
    }
}
