<?php

namespace Tests\Unit;

use App\Models\Kronologis;
use App\Models\NotifikasiLog;
use App\Models\Tiket;
use App\Models\User;
use App\Notifications\KronologisBaruNotification;
use App\Notifications\TiketBaruNotification;
use App\Notifications\TiketClosedNotification;
use App\Services\NotificationService;
use App\Services\WebPushService;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NotificationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected NotificationService $notificationService;
    protected WhatsAppService $waService;
    protected WebPushService $webPushService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->waService = new WhatsAppService();
        $this->webPushService = $this->createMock(WebPushService::class);
        $this->notificationService = new NotificationService($this->waService, $this->webPushService);
    }

    public function test_whatsapp_phone_number_formatting(): void
    {
        $this->assertEquals('628123456789', $this->waService->formatPhoneNumber('08123456789'));
        $this->assertEquals('628123456789', $this->waService->formatPhoneNumber('628123456789'));
        $this->assertEquals('628123456789', $this->waService->formatPhoneNumber('+62 812-3456-789'));
    }

    public function test_notify_tiket_baru_dispatches_notification_and_logs(): void
    {
        Notification::fake();

        $teknis = User::factory()->create(['role' => 'teknis', 'is_active' => true, 'phone' => '08123456789']);
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $creator = User::factory()->create(['role' => 'helpdesk', 'is_active' => true]);

        $tiket = Tiket::create([
            'no_tiket' => 'BDG-20260921-001',
            'status_link_impact' => 'DOWN',
            'backbone_segment' => 'SW BBLU - SW Reog',
            'tanggal_open' => Carbon::now(),
            'status' => 'OPEN',
            'sla_target_minutes' => 360,
            'created_by' => $creator->id,
        ]);

        $this->notificationService->notifyTiketBaru($tiket);

        Notification::assertSentTo(
            [$teknis, $admin],
            TiketBaruNotification::class
        );

        $this->assertDatabaseHas('notifikasi_log', [
            'id_tiket' => $tiket->id,
            'tipe' => 'INAPP',
        ]);
    }

    public function test_notify_kronologis_baru_dispatches_notification(): void
    {
        Notification::fake();

        $helpdesk = User::factory()->create(['role' => 'helpdesk', 'is_active' => true]);
        $teknis = User::factory()->create(['role' => 'teknis', 'is_active' => true]);

        $tiket = Tiket::create([
            'no_tiket' => 'BDG-20260921-002',
            'status_link_impact' => 'DEGRADASI',
            'backbone_segment' => 'SW Cikutra - SW Dago',
            'tanggal_open' => Carbon::now(),
            'status' => 'PROSES',
            'sla_target_minutes' => 360,
            'created_by' => $helpdesk->id,
        ]);

        $kronologis = Kronologis::create([
            'id_tiket' => $tiket->id,
            'timestamp' => Carbon::now(),
            'user_id' => $teknis->id,
            'kategori' => 'OTDR',
            'informasi' => 'Titik cut terdeteksi pada 1.2 KM',
        ]);

        $this->notificationService->notifyKronologisBaru($tiket, $kronologis);

        Notification::assertSentTo(
            [$helpdesk],
            KronologisBaruNotification::class
        );
    }

    public function test_notify_tiket_closed_dispatches_notification(): void
    {
        Notification::fake();

        $saCs = User::factory()->create(['role' => 'sa_cs', 'is_active' => true]);
        $teknis = User::factory()->create(['role' => 'teknis', 'is_active' => true]);
        $closer = User::factory()->create(['role' => 'helpdesk', 'is_active' => true]);

        $tiket = Tiket::create([
            'no_tiket' => 'BDG-20260921-003',
            'status_link_impact' => 'DOWN',
            'backbone_segment' => 'SW Pasteur - SW Sukajadi',
            'tanggal_open' => Carbon::now()->subHours(2),
            'tanggal_close' => Carbon::now(),
            'status' => 'CLOSE',
            'mttr_minutes' => 120,
            'sla_target_minutes' => 240,
            'sla_status' => 'TEPAT',
            'created_by' => $closer->id,
            'closed_by' => $closer->id,
        ]);

        $this->notificationService->notifyTiketClosed($tiket);

        Notification::assertSentTo(
            [$saCs, $teknis],
            TiketClosedNotification::class
        );
    }
}
