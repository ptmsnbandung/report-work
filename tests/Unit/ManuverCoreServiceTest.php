<?php

namespace Tests\Unit;

use App\Models\Tiket;
use App\Models\User;
use App\Services\ManuverCoreService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManuverCoreServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ManuverCoreService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ManuverCoreService();
    }

    public function test_add_manuver_successfully(): void
    {
        $user = User::factory()->create(['role' => 'teknis']);
        $tiket = Tiket::create([
            'no_tiket' => 'BDG-20260921-004',
            'status_link_impact' => 'Down',
            'backbone_segment' => 'SW BBLU - SW Reog',
            'tanggal_open' => now(),
            'status' => 'OPEN',
            'created_by' => $user->id,
        ]);

        $manuver = $this->service->addManuver($tiket, [
            'titik' => 'JC1',
            'core_asal' => 'Tube 2 Core 1',
            'core_tujuan' => 'Tube 2 Core 1',
            'tipe' => 'SESUDAH',
        ]);

        $this->assertEquals('JC1', $manuver->titik);
        $this->assertEquals('SESUDAH', $manuver->tipe);
        $this->assertDatabaseHas('manuver_core', [
            'id_tiket' => $tiket->id,
            'titik' => 'JC1',
            'core_asal' => 'Tube 2 Core 1',
        ]);

        // Tiket status should transition to PROSES
        $this->assertEquals('PROSES', $tiket->fresh()->status);
    }

    public function test_delete_manuver_successfully(): void
    {
        $user = User::factory()->create(['role' => 'teknis']);
        $tiket = Tiket::create([
            'no_tiket' => 'BDG-20260921-005',
            'status_link_impact' => 'Down',
            'backbone_segment' => 'SW BBLU - SW Reog',
            'tanggal_open' => now(),
            'status' => 'OPEN',
            'created_by' => $user->id,
        ]);

        $manuver = $this->service->addManuver($tiket, [
            'titik' => 'JC2',
            'core_asal' => 'Tube 1 Core 1',
            'core_tujuan' => 'Tube 1 Core 2',
            'tipe' => 'SEBELUM',
        ]);

        $deleted = $this->service->deleteManuver($manuver);
        $this->assertTrue($deleted);
        $this->assertDatabaseMissing('manuver_core', ['id' => $manuver->id]);
    }
}
