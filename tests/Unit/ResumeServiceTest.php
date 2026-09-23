<?php

namespace Tests\Unit;

use App\Models\Material;
use App\Models\ResumePekerjaan;
use App\Models\Tiket;
use App\Models\TitikPerbaikan;
use App\Models\User;
use App\Services\ResumeService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResumeServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ResumeService $service;
    protected User $teknis;
    protected User $admin;
    protected Tiket $tiket;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ResumeService();

        $this->teknis = User::factory()->create([
            'role' => 'teknis',
            'is_active' => true,
        ]);

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->tiket = Tiket::create([
            'no_tiket' => 'BDG-20260914-001',
            'status_link_impact' => 'SW BBLU - SW Reog Down',
            'backbone_segment' => 'SW BBLU - SW Reog',
            'tanggal_open' => Carbon::now(),
            'status' => 'OPEN',
            'sla_target_minutes' => 360,
            'created_by' => $this->admin->id,
        ]);
    }

    public function test_save_or_update_resume_successfully(): void
    {
        $data = [
            'team_om'          => ['Rian Suryana', 'Budi Santoso'],
            'problem_temuan'   => 'Kabel Tertarik Truk di Crossingan Jalan',
            'action'           => 'Jumper Kabel 150 meter, Pemasangan New JC 2 Titik',
            'catatan_tambahan' => 'Redaman setelah penyambungan -18 dBm normal',
        ];

        $resume = $this->service->saveOrUpdateResume($this->tiket, $data);

        $this->assertDatabaseHas('resume_pekerjaan', [
            'id_tiket'        => $this->tiket->id,
            'problem_temuan'  => 'Kabel Tertarik Truk di Crossingan Jalan',
            'action'          => 'Jumper Kabel 150 meter, Pemasangan New JC 2 Titik',
        ]);

        $this->assertEquals(['Rian Suryana', 'Budi Santoso'], $resume->team_om);
        $this->assertEquals('Rian Suryana, Budi Santoso', $resume->team_om_string);

        // Status tiket berubah dari OPEN ke PROSES
        $this->tiket->refresh();
        $this->assertEquals('PROSES', $this->tiket->status);
    }

    public function test_add_and_delete_material(): void
    {
        $materialData = [
            'nama_material' => 'JC 24C',
            'jumlah'        => 2,
            'satuan'        => 'pcs',
        ];

        $material = $this->service->addMaterial($this->tiket, $materialData);

        $this->assertDatabaseHas('material', [
            'id_tiket'      => $this->tiket->id,
            'nama_material' => 'JC 24C',
            'jumlah'        => 2,
            'satuan'        => 'pcs',
        ]);

        $deleted = $this->service->deleteMaterial($material, $this->teknis);
        $this->assertTrue($deleted);
        $this->assertDatabaseMissing('material', ['id' => $material->id]);
    }

    public function test_add_and_delete_titik_perbaikan(): void
    {
        $titikData = [
            'nama_titik' => 'JC1',
            'latitude'   => -6.379199,
            'longitude'  => 106.846552,
            'keterangan' => 'Closure Tiang 1',
        ];

        $titik = $this->service->addTitikPerbaikan($this->tiket, $titikData);

        $this->assertDatabaseHas('titik_perbaikan', [
            'id_tiket'   => $this->tiket->id,
            'nama_titik' => 'JC1',
            'latitude'   => -6.379199,
            'longitude'  => 106.846552,
        ]);

        $this->assertStringContainsString('https://www.google.com/maps?q=-6.379199,106.846552', $titik->google_maps_url);

        $deleted = $this->service->deleteTitikPerbaikan($titik, $this->teknis);
        $this->assertTrue($deleted);
        $this->assertDatabaseMissing('titik_perbaikan', ['id' => $titik->id]);
    }
}
