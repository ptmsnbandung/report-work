<?php

namespace Tests\Feature;

use App\Models\ManuverCore;
use App\Models\Tiket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManuverCoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_teknis_can_add_manuver_core(): void
    {
        $teknis = User::factory()->create(['role' => 'teknis']);
        $tiket = Tiket::create([
            'no_tiket' => 'BDG-20260921-009',
            'status_link_impact' => 'Down',
            'backbone_segment' => 'SW BBLU - SW Reog',
            'tanggal_open' => now(),
            'status' => 'OPEN',
            'created_by' => $teknis->id,
        ]);

        $response = $this->actingAs($teknis)->post(route('tiket.manuver-core.store', $tiket->id), [
            'titik' => 'JC1',
            'core_asal' => 'Tube 2 Core 1',
            'core_tujuan' => 'Tube 2 Core 1',
            'tipe' => 'SESUDAH',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('manuver_core', [
            'id_tiket' => $tiket->id,
            'titik' => 'JC1',
            'core_asal' => 'Tube 2 Core 1',
            'tipe' => 'SESUDAH',
        ]);
    }

    public function test_teknis_can_delete_manuver_core(): void
    {
        $teknis = User::factory()->create(['role' => 'teknis']);
        $tiket = Tiket::create([
            'no_tiket' => 'BDG-20260921-010',
            'status_link_impact' => 'Down',
            'backbone_segment' => 'SW BBLU - SW Reog',
            'tanggal_open' => now(),
            'status' => 'OPEN',
            'created_by' => $teknis->id,
        ]);

        $manuver = ManuverCore::create([
            'id_tiket' => $tiket->id,
            'titik' => 'JC1',
            'core_asal' => 'Tube 1 Core 1',
            'core_tujuan' => 'Tube 1 Core 1',
            'tipe' => 'SEBELUM',
        ]);

        $response = $this->actingAs($teknis)->delete(route('tiket.manuver-core.destroy', $manuver->id));
        $response->assertRedirect();
        $this->assertDatabaseMissing('manuver_core', ['id' => $manuver->id]);
    }

    public function test_client_cannot_add_manuver_core(): void
    {
        $client = User::factory()->create(['role' => 'client']);
        $helpdesk = User::factory()->create(['role' => 'helpdesk']);
        $tiket = Tiket::create([
            'no_tiket' => 'BDG-20260921-011',
            'status_link_impact' => 'Down',
            'backbone_segment' => 'SW BBLU - SW Reog',
            'tanggal_open' => now(),
            'status' => 'OPEN',
            'created_by' => $helpdesk->id,
        ]);

        $response = $this->actingAs($client)->post(route('tiket.manuver-core.store', $tiket->id), [
            'titik' => 'JC1',
            'core_asal' => 'Tube 2 Core 1',
            'core_tujuan' => 'Tube 2 Core 1',
            'tipe' => 'SESUDAH',
        ]);

        $response->assertStatus(403);
    }
}
