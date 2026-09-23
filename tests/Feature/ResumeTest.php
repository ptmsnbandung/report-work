<?php

namespace Tests\Feature;

use App\Models\Material;
use App\Models\ResumePekerjaan;
use App\Models\Tiket;
use App\Models\TitikPerbaikan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResumeTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $helpdesk;
    protected User $teknis;
    protected User $client;
    protected Tiket $tiket;

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

        $this->client = User::factory()->create([
            'role' => 'client',
            'is_active' => true,
        ]);

        $this->tiket = Tiket::create([
            'no_tiket' => 'BDG-20260914-001',
            'status_link_impact' => 'SW BBLU - SW Reog Down',
            'backbone_segment' => 'SW BBLU - SW Reog',
            'tanggal_open' => Carbon::now(),
            'status' => 'OPEN',
            'sla_target_minutes' => 360,
            'created_by' => $this->helpdesk->id,
        ]);
    }

    public function test_teknis_can_store_resume(): void
    {
        $response = $this->actingAs($this->teknis)->post("/tiket/{$this->tiket->id}/resume", [
            'team_om'        => ['Rian Suryana', 'Budi Santoso'],
            'problem_temuan' => 'Kabel Tertarik Truk di Crossingan Jalan',
            'action'         => 'Jumper Kabel 150 meter, Pemasangan New JC 2 Titik',
        ]);

        $response->assertRedirect("/tiket/{$this->tiket->id}");
        $this->assertDatabaseHas('resume_pekerjaan', [
            'id_tiket'       => $this->tiket->id,
            'problem_temuan' => 'Kabel Tertarik Truk di Crossingan Jalan',
        ]);
    }

    public function test_teknis_can_add_and_delete_material(): void
    {
        $response = $this->actingAs($this->teknis)->post("/tiket/{$this->tiket->id}/material", [
            'nama_material' => 'Kabel ADSS 24C',
            'jumlah'        => 150,
            'satuan'        => 'meter',
        ]);

        $response->assertRedirect("/tiket/{$this->tiket->id}");
        $material = Material::where('nama_material', 'Kabel ADSS 24C')->first();
        $this->assertNotNull($material);

        $deleteResponse = $this->actingAs($this->teknis)->delete("/tiket/{$this->tiket->id}/material/{$material->id}");
        $deleteResponse->assertRedirect("/tiket/{$this->tiket->id}");
        $this->assertDatabaseMissing('material', ['id' => $material->id]);
    }

    public function test_teknis_can_add_and_delete_titik_perbaikan(): void
    {
        $response = $this->actingAs($this->teknis)->post("/tiket/{$this->tiket->id}/titik-perbaikan", [
            'nama_titik' => 'JC1',
            'latitude'   => -6.379199,
            'longitude'  => 106.846552,
            'keterangan' => 'Closure Tiang 1',
        ]);

        $response->assertRedirect("/tiket/{$this->tiket->id}");
        $titik = TitikPerbaikan::where('nama_titik', 'JC1')->first();
        $this->assertNotNull($titik);

        $deleteResponse = $this->actingAs($this->teknis)->delete("/tiket/{$this->tiket->id}/titik-perbaikan/{$titik->id}");
        $deleteResponse->assertRedirect("/tiket/{$this->tiket->id}");
        $this->assertDatabaseMissing('titik_perbaikan', ['id' => $titik->id]);
    }

    public function test_client_cannot_modify_resume_or_materials(): void
    {
        $response = $this->actingAs($this->client)->post("/tiket/{$this->tiket->id}/resume", [
            'team_om'        => ['Hacker'],
            'problem_temuan' => 'Invalid',
            'action'         => 'Invalid',
        ]);

        $response->assertStatus(403);
    }
}
