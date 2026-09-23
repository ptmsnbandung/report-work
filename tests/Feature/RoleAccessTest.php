<?php

namespace Tests\Feature;

use App\Models\MasterSla;
use App\Models\Tiket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $helpdesk;
    protected User $teknis;
    protected User $saCs;
    protected User $client;
    protected Tiket $tiket;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $this->helpdesk = User::factory()->create(['role' => 'helpdesk', 'is_active' => true]);
        $this->teknis = User::factory()->create(['role' => 'teknis', 'is_active' => true]);
        $this->saCs = User::factory()->create(['role' => 'sa_cs', 'is_active' => true]);
        $this->client = User::factory()->create(['role' => 'client', 'is_active' => true]);

        MasterSla::create([
            'backbone_segment' => 'SW Test - SW Test 2',
            'sla_target_minutes' => 360,
        ]);

        $this->tiket = Tiket::create([
            'no_tiket' => 'BDG-20260921-999',
            'status_link_impact' => 'DOWN',
            'backbone_segment' => 'SW Test - SW Test 2',
            'tanggal_open' => Carbon::now(),
            'status' => 'OPEN',
            'sla_target_minutes' => 360,
            'created_by' => $this->helpdesk->id,
        ]);
    }

    public function test_guest_is_redirected_to_login_for_all_protected_routes(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
        $this->get('/tiket')->assertRedirect('/login');
        $this->get('/tiket/create')->assertRedirect('/login');
        $this->get('/reports')->assertRedirect('/login');
        $this->get('/notifications')->assertRedirect('/login');
        $this->get('/master/users')->assertRedirect('/login');
    }

    public function test_admin_has_full_access_to_all_sections(): void
    {
        $this->actingAs($this->admin)->get('/dashboard')->assertStatus(200);
        $this->actingAs($this->admin)->get('/tiket')->assertStatus(200);
        $this->actingAs($this->admin)->get('/tiket/create')->assertStatus(200);
        $this->actingAs($this->admin)->get("/tiket/{$this->tiket->id}")->assertStatus(200);
        $this->actingAs($this->admin)->get('/reports')->assertStatus(200);
        $this->actingAs($this->admin)->get('/notifications')->assertStatus(200);
        $this->actingAs($this->admin)->get('/master/segments')->assertStatus(200);
        $this->actingAs($this->admin)->get('/master/materials')->assertStatus(200);
        $this->actingAs($this->admin)->get('/master/sla')->assertStatus(200);
        $this->actingAs($this->admin)->get('/master/users')->assertStatus(200);
    }

    public function test_helpdesk_access_matrix(): void
    {
        $this->actingAs($this->helpdesk)->get('/dashboard')->assertStatus(200);
        $this->actingAs($this->helpdesk)->get('/tiket')->assertStatus(200);
        $this->actingAs($this->helpdesk)->get('/tiket/create')->assertStatus(200);
        $this->actingAs($this->helpdesk)->get("/tiket/{$this->tiket->id}")->assertStatus(200);
        $this->actingAs($this->helpdesk)->get('/reports')->assertStatus(200);
        $this->actingAs($this->helpdesk)->get('/notifications')->assertStatus(200);

        // HelpDesk cannot access master data
        $this->actingAs($this->helpdesk)->get('/master/segments')->assertStatus(403);
        $this->actingAs($this->helpdesk)->get('/master/users')->assertStatus(403);
    }

    public function test_teknis_access_matrix(): void
    {
        $this->actingAs($this->teknis)->get('/dashboard')->assertStatus(200);
        $this->actingAs($this->teknis)->get('/tiket')->assertStatus(200);
        $this->actingAs($this->teknis)->get("/tiket/{$this->tiket->id}")->assertStatus(200);
        $this->actingAs($this->teknis)->get('/reports')->assertStatus(200);
        $this->actingAs($this->teknis)->get('/notifications')->assertStatus(200);

        // Teknis cannot create ticket or access master
        $this->actingAs($this->teknis)->get('/tiket/create')->assertStatus(403);
        $this->actingAs($this->teknis)->get('/master/users')->assertStatus(403);
    }

    public function test_sa_cs_access_matrix(): void
    {
        $this->actingAs($this->saCs)->get('/dashboard')->assertStatus(200);
        $this->actingAs($this->saCs)->get('/tiket')->assertStatus(200);
        $this->actingAs($this->saCs)->get("/tiket/{$this->tiket->id}")->assertStatus(200);
        $this->actingAs($this->saCs)->get('/reports')->assertStatus(200);
        $this->actingAs($this->saCs)->get('/notifications')->assertStatus(200);

        // SA/CS cannot create ticket or access master
        $this->actingAs($this->saCs)->get('/tiket/create')->assertStatus(403);
        $this->actingAs($this->saCs)->get('/master/users')->assertStatus(403);
    }

    public function test_client_access_matrix(): void
    {
        $this->actingAs($this->client)->get('/dashboard')->assertStatus(200);
        $this->actingAs($this->client)->get('/tiket')->assertStatus(200);
        $this->actingAs($this->client)->get("/tiket/{$this->tiket->id}")->assertStatus(200);
        $this->actingAs($this->client)->get('/notifications')->assertStatus(200);

        // Client cannot create ticket or access master
        $this->actingAs($this->client)->get('/tiket/create')->assertStatus(403);
        $this->actingAs($this->client)->get('/master/users')->assertStatus(403);
    }
}
