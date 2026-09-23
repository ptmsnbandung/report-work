<?php

namespace Tests\Feature;

use App\Models\Tiket;
use App\Models\User;
use App\Notifications\TiketBaruNotification;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_notifications_page_and_auto_marks_as_read(): void
    {
        $user = User::factory()->create(['role' => 'teknis', 'is_active' => true]);
        $creator = User::factory()->create(['role' => 'helpdesk', 'is_active' => true]);

        $tiket = Tiket::create([
            'no_tiket' => 'BDG-20260921-099',
            'status_link_impact' => 'DOWN',
            'backbone_segment' => 'SW BBLU - SW Reog',
            'tanggal_open' => Carbon::now(),
            'status' => 'OPEN',
            'sla_target_minutes' => 360,
            'created_by' => $creator->id,
        ]);

        $user->notify(new TiketBaruNotification($tiket));
        $this->assertEquals(1, $user->unreadNotifications()->count());

        $response = $this->actingAs($user)->get('/notifications');
        $response->assertStatus(200);
        $response->assertSee('Pusat Notifikasi');

        $this->assertEquals(0, $user->fresh()->unreadNotifications()->count());
    }

    public function test_user_can_mark_notification_as_read(): void
    {
        $user = User::factory()->create(['role' => 'teknis', 'is_active' => true]);
        $creator = User::factory()->create(['role' => 'helpdesk', 'is_active' => true]);

        $tiket = Tiket::create([
            'no_tiket' => 'BDG-20260921-100',
            'status_link_impact' => 'DOWN',
            'backbone_segment' => 'SW BBLU - SW Reog',
            'tanggal_open' => Carbon::now(),
            'status' => 'OPEN',
            'sla_target_minutes' => 360,
            'created_by' => $creator->id,
        ]);

        $user->notify(new TiketBaruNotification($tiket));

        $notification = $user->unreadNotifications()->first();
        $this->assertNotNull($notification);

        $response = $this->actingAs($user)->post("/notifications/{$notification->id}/read");
        $response->assertRedirect(route('tiket.show', $tiket->id));

        $this->assertEquals(0, $user->fresh()->unreadNotifications()->count());
    }

    public function test_user_can_mark_all_notifications_as_read(): void
    {
        $user = User::factory()->create(['role' => 'teknis', 'is_active' => true]);
        $creator = User::factory()->create(['role' => 'helpdesk', 'is_active' => true]);

        $tiket = Tiket::create([
            'no_tiket' => 'BDG-20260921-101',
            'status_link_impact' => 'DOWN',
            'backbone_segment' => 'SW BBLU - SW Reog',
            'tanggal_open' => Carbon::now(),
            'status' => 'OPEN',
            'sla_target_minutes' => 360,
            'created_by' => $creator->id,
        ]);

        $user->notify(new TiketBaruNotification($tiket));
        $this->assertEquals(1, $user->unreadNotifications()->count());

        $response = $this->actingAs($user)->post('/notifications/read-all');
        $response->assertSessionHas('success');
        $this->assertEquals(0, $user->fresh()->unreadNotifications()->count());
    }

    public function test_get_unread_notifications_json_endpoint(): void
    {
        $user = User::factory()->create(['role' => 'teknis', 'is_active' => true]);
        $creator = User::factory()->create(['role' => 'helpdesk', 'is_active' => true]);

        $tiket = Tiket::create([
            'no_tiket' => 'BDG-20260921-102',
            'status_link_impact' => 'DOWN',
            'backbone_segment' => 'SW BBLU - SW Reog',
            'tanggal_open' => Carbon::now(),
            'status' => 'OPEN',
            'sla_target_minutes' => 360,
            'created_by' => $creator->id,
        ]);

        $user->notify(new TiketBaruNotification($tiket));

        $response = $this->actingAs($user)->getJson('/notifications/unread-json');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'unread_count',
            'notifications',
        ]);
        $response->assertJson([
            'unread_count' => 1,
        ]);
    }
}
