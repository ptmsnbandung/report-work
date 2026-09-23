<?php

namespace Tests\Feature;

use App\Models\PushSubscription;
use App\Models\Tiket;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\WebPushService;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebPushTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_vapid_public_key(): void
    {
        $user = User::factory()->create(['role' => 'teknis', 'is_active' => true]);

        $response = $this->actingAs($user)->getJson('/push/vapid-public-key');

        $response->assertStatus(200);
        $response->assertJsonStructure(['public_key']);
        $this->assertNotEmpty($response->json('public_key'));
    }

    public function test_can_store_push_subscription(): void
    {
        $user = User::factory()->create(['role' => 'teknis', 'is_active' => true]);

        $payload = [
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/fake-endpoint-token-12345',
            'keys' => [
                'p256dh' => 'BNcRdreALRFXTkOOUHK1EtK2wtaz5Ry4YfYCA_0QTpQtUbVlUls0VJXg7A8u-Ts1XbjhazAkj7I99e8QcYP7DkM',
                'auth' => 'tBHItJI5svbpez7KI4CCXg',
            ],
            'content_encoding' => 'aesgcm',
        ];

        $response = $this->actingAs($user)->postJson('/push/subscribe', $payload);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('push_subscriptions', [
            'user_id' => $user->id,
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/fake-endpoint-token-12345',
        ]);
    }

    public function test_can_unsubscribe_endpoint(): void
    {
        $user = User::factory()->create(['role' => 'teknis', 'is_active' => true]);

        PushSubscription::create([
            'user_id' => $user->id,
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/fake-endpoint-to-delete',
            'public_key' => 'key123',
            'auth_token' => 'auth123',
        ]);

        $response = $this->actingAs($user)->postJson('/push/unsubscribe', [
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/fake-endpoint-to-delete',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseMissing('push_subscriptions', [
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/fake-endpoint-to-delete',
        ]);
    }

    public function test_notification_service_triggers_web_push(): void
    {
        $creator = User::factory()->create(['role' => 'helpdesk', 'is_active' => true]);
        $teknisi = User::factory()->create(['role' => 'teknis', 'is_active' => true]);

        // Create a mock push subscription for teknisi
        PushSubscription::create([
            'user_id' => $teknisi->id,
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/fake-endpoint-sub-test',
            'public_key' => 'key123',
            'auth_token' => 'auth123',
        ]);

        $tiket = Tiket::create([
            'no_tiket' => 'BDG-20260923-999',
            'status_link_impact' => 'DOWN',
            'backbone_segment' => 'SW BBLU - SW Reog',
            'tanggal_open' => Carbon::now(),
            'status' => 'OPEN',
            'sla_target_minutes' => 360,
            'created_by' => $creator->id,
        ]);

        // Mock WebPushService so we don't send real HTTP requests to Google FCM
        $webPushMock = $this->createMock(WebPushService::class);
        $webPushMock->expects($this->once())
            ->method('sendToUsers')
            ->with(
                $this->anything(),
                $this->stringContains($tiket->no_tiket),
                $this->anything(),
                $this->anything()
            );

        $waService = new WhatsAppService();
        $notificationService = new NotificationService($waService, $webPushMock);
        $notificationService->notifyTiketBaru($tiket);

        $this->assertTrue(true);
    }
}
