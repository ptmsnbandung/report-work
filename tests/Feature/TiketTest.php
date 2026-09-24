<?php

namespace Tests\Feature;

use App\Models\MasterSla;
use App\Models\Tiket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TiketTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $helpdesk;
    protected User $teknis;
    protected User $saCs;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->helpdesk = User::factory()->create([
            'role' => 'helpdesk',
            'is_active' => true,
        ]);

        $this->teknis = User::factory()->create([
            'role' => 'teknis',
            'is_active' => true,
        ]);

        $this->saCs = User::factory()->create([
            'role' => 'sa_cs',
            'is_active' => true,
        ]);

        MasterSla::create([
            'backbone_segment' => 'SW BBLU - SW Reog',
            'sla_target_minutes' => 360,
        ]);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/tiket');
        $response->assertRedirect('/login');

        $response = $this->get('/tiket/create');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_tiket_index(): void
    {
        $response = $this->actingAs($this->teknis)->get('/tiket');
        $response->assertStatus(200);
        $response->assertSee('Daftar Tiket Gangguan Backbone');
    }

    public function test_helpdesk_can_view_create_page(): void
    {
        $response = $this->actingAs($this->helpdesk)->get('/tiket/create');
        $response->assertStatus(200);
        $response->assertSee('Open Tiket Gangguan Baru');
    }

    public function test_teknis_cannot_access_create_page(): void
    {
        $response = $this->actingAs($this->teknis)->get('/tiket/create');
        $response->assertStatus(403);
    }

    public function test_sa_cs_cannot_access_create_page(): void
    {
        $response = $this->actingAs($this->saCs)->get('/tiket/create');
        $response->assertStatus(403);
    }

    public function test_helpdesk_can_store_new_tiket(): void
    {
        $data = [
            'status_link_impact' => 'Backbone : SW BBLU - SW Reog Down',
            'backbone_segment' => 'SW BBLU - SW Reog',
            'tanggal_open' => '2026-09-14 14:30',
            'sla_target_minutes' => 360,
            'deskripsi' => 'LOS detected on SW BBLU.',
        ];

        $response = $this->actingAs($this->helpdesk)->post('/tiket', $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('tiket', [
            'status_link_impact' => 'Backbone : SW BBLU - SW Reog Down',
            'backbone_segment' => 'SW BBLU - SW Reog',
            'status' => 'OPEN',
            'created_by' => $this->helpdesk->id,
        ]);
    }

    public function test_can_view_tiket_detail(): void
    {
        $tiket = Tiket::create([
            'no_tiket' => 'BDG-20260914-001',
            'status_link_impact' => 'SW BBLU - SW Reog Down',
            'backbone_segment' => 'SW BBLU - SW Reog',
            'tanggal_open' => Carbon::now(),
            'status' => 'OPEN',
            'sla_target_minutes' => 360,
            'created_by' => $this->helpdesk->id,
        ]);

        $response = $this->actingAs($this->saCs)->get("/tiket/{$tiket->id}");
        $response->assertStatus(200);
        $response->assertSee('BDG-20260914-001');
        $response->assertSee('SW BBLU - SW Reog Down');
    }

    public function test_helpdesk_can_edit_open_tiket(): void
    {
        $tiket = Tiket::create([
            'no_tiket' => 'BDG-20260914-001',
            'status_link_impact' => 'SW BBLU - SW Reog Down',
            'backbone_segment' => 'SW BBLU - SW Reog',
            'tanggal_open' => Carbon::now(),
            'status' => 'OPEN',
            'sla_target_minutes' => 360,
            'created_by' => $this->helpdesk->id,
        ]);

        $response = $this->actingAs($this->helpdesk)->get("/tiket/{$tiket->id}/edit");
        $response->assertStatus(200);

        $updateResponse = $this->actingAs($this->helpdesk)->put("/tiket/{$tiket->id}", [
            'status_link_impact' => 'SW BBLU - SW Reog Down Updated',
            'backbone_segment' => 'SW BBLU - SW Reog',
            'tanggal_open' => Carbon::now()->toDateTimeString(),
            'sla_target_minutes' => 360,
        ]);

        $updateResponse->assertRedirect("/tiket/{$tiket->id}");
        $this->assertDatabaseHas('tiket', [
            'id' => $tiket->id,
            'status_link_impact' => 'SW BBLU - SW Reog Down Updated',
        ]);
    }

    public function test_helpdesk_can_close_tiket(): void
    {
        $openDate = Carbon::parse('2026-09-14 14:30:00');
        $closeDate = Carbon::parse('2026-09-14 20:15:00');

        $tiket = Tiket::create([
            'no_tiket' => 'BDG-20260914-001',
            'status_link_impact' => 'SW BBLU - SW Reog Down',
            'backbone_segment' => 'SW BBLU - SW Reog',
            'tanggal_open' => $openDate,
            'status' => 'PENDING_VERIFIKASI',
            'sla_target_minutes' => 360,
            'created_by' => $this->helpdesk->id,
        ]);

        $response = $this->actingAs($this->helpdesk)->post("/tiket/{$tiket->id}/close", [
            'tanggal_close' => $closeDate->toDateTimeString(),
            'catatan_closing' => 'Perbaikan selesai dan normal.',
        ]);

        $response->assertRedirect("/tiket/{$tiket->id}");
        $this->assertDatabaseHas('tiket', [
            'id' => $tiket->id,
            'status' => 'CLOSE',
            'mttr_minutes' => 345,
            'sla_status' => 'TEPAT',
            'closed_by' => $this->helpdesk->id,
        ]);
    }

    public function test_helpdesk_cannot_close_tiket_if_not_pending_verifikasi(): void
    {
        $tiket = Tiket::create([
            'no_tiket' => 'BDG-20260914-002',
            'status_link_impact' => 'SW BBLU - SW Reog Down',
            'backbone_segment' => 'SW BBLU - SW Reog',
            'tanggal_open' => Carbon::now(),
            'status' => 'PROSES',
            'sla_target_minutes' => 360,
            'created_by' => $this->helpdesk->id,
        ]);

        $response = $this->actingAs($this->helpdesk)->post("/tiket/{$tiket->id}/close", [
            'tanggal_close' => Carbon::now()->toDateTimeString(),
        ]);

        $response->assertRedirect("/tiket/{$tiket->id}");
        $response->assertSessionHas('error');
        $tiket->refresh();
        $this->assertEquals('PROSES', $tiket->status);
    }

    public function test_teknis_cannot_close_tiket(): void
    {
        $tiket = Tiket::create([
            'no_tiket' => 'BDG-20260914-001',
            'status_link_impact' => 'SW BBLU - SW Reog Down',
            'backbone_segment' => 'SW BBLU - SW Reog',
            'tanggal_open' => Carbon::now(),
            'status' => 'OPEN',
            'sla_target_minutes' => 360,
            'created_by' => $this->helpdesk->id,
        ]);

        $response = $this->actingAs($this->teknis)->post("/tiket/{$tiket->id}/close", [
            'tanggal_close' => Carbon::now()->toDateTimeString(),
        ]);

        $response->assertStatus(403);
    }

    public function test_non_admin_cannot_delete_tiket(): void
    {
        $tiket = Tiket::create([
            'no_tiket' => 'BDG-20260914-001',
            'status_link_impact' => 'SW BBLU - SW Reog Down',
            'backbone_segment' => 'SW BBLU - SW Reog',
            'tanggal_open' => Carbon::now(),
            'status' => 'OPEN',
            'sla_target_minutes' => 360,
            'created_by' => $this->helpdesk->id,
        ]);

        $response = $this->actingAs($this->helpdesk)->delete("/tiket/{$tiket->id}");
        $response->assertStatus(403);
    }

    public function test_admin_can_delete_tiket(): void
    {
        $tiket = Tiket::create([
            'no_tiket' => 'BDG-20260914-001',
            'status_link_impact' => 'SW BBLU - SW Reog Down',
            'backbone_segment' => 'SW BBLU - SW Reog',
            'tanggal_open' => Carbon::now(),
            'status' => 'OPEN',
            'sla_target_minutes' => 360,
            'created_by' => $this->helpdesk->id,
        ]);

        $response = $this->actingAs($this->admin)->delete("/tiket/{$tiket->id}");
        $response->assertRedirect('/tiket');
        $this->assertDatabaseMissing('tiket', ['id' => $tiket->id]);
    }
}
