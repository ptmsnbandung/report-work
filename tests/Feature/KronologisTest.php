<?php

namespace Tests\Feature;

use App\Models\Kronologis;
use App\Models\Tiket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class KronologisTest extends TestCase
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
            'role' => 'sa_cs',
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

    public function test_teknis_can_submit_kronologis(): void
    {
        $response = $this->actingAs($this->teknis)->post("/tiket/{$this->tiket->id}/kronologis", [
            'kategori'  => 'OTDR',
            'informasi' => 'Hasil ukur OTDR jarak 12.4 km terjadi bending kabel optik.',
            'latitude'  => -6.379199,
            'longitude' => 106.846552,
        ]);

        $response->assertRedirect("/tiket/{$this->tiket->id}");
        $this->assertDatabaseHas('kronologis', [
            'id_tiket'  => $this->tiket->id,
            'user_id'   => $this->teknis->id,
            'kategori'  => 'OTDR',
            'informasi' => 'Hasil ukur OTDR jarak 12.4 km terjadi bending kabel optik.',
        ]);

        // Verify status automatically changes to PROSES
        $this->tiket->refresh();
        $this->assertEquals('PROSES', $this->tiket->status);
    }

    public function test_client_cannot_submit_kronologis(): void
    {
        $response = $this->actingAs($this->client)->post("/tiket/{$this->tiket->id}/kronologis", [
            'kategori'  => 'OTDR',
            'informasi' => 'Informasi tidak sah dari client.',
        ]);

        $response->assertStatus(403);
    }

    public function test_ajax_get_kronologis_timeline(): void
    {
        Kronologis::create([
            'id_tiket'  => $this->tiket->id,
            'timestamp' => Carbon::now(),
            'user_id'   => $this->teknis->id,
            'kategori'  => 'IZIN',
            'informasi' => 'Izin masuk lokasi perbaikan.',
        ]);

        $response = $this->actingAs($this->teknis)->getJson("/tiket/{$this->tiket->id}/kronologis");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'count',
                'data' => [
                    '*' => [
                        'id',
                        'timestamp',
                        'formatted_time',
                        'formatted_date',
                        'kategori',
                        'kategori_label',
                        'informasi',
                        'user_name',
                    ]
                ]
            ]);
    }

    public function test_admin_can_delete_kronologis(): void
    {
        $krono = Kronologis::create([
            'id_tiket'  => $this->tiket->id,
            'timestamp' => Carbon::now(),
            'user_id'   => $this->teknis->id,
            'kategori'  => 'IZIN',
            'informasi' => 'Catatan salah input.',
        ]);

        $response = $this->actingAs($this->admin)->delete("/tiket/{$this->tiket->id}/kronologis/{$krono->id}");
        $response->assertRedirect("/tiket/{$this->tiket->id}");
        $this->assertDatabaseMissing('kronologis', ['id' => $krono->id]);
    }

    public function test_teknis_cannot_delete_kronologis(): void
    {
        $krono = Kronologis::create([
            'id_tiket'  => $this->tiket->id,
            'timestamp' => Carbon::now(),
            'user_id'   => $this->teknis->id,
            'kategori'  => 'IZIN',
            'informasi' => 'Catatan penting.',
        ]);

        $response = $this->actingAs($this->teknis)->delete("/tiket/{$this->tiket->id}/kronologis/{$krono->id}");
        $response->assertStatus(403);
    }
}
