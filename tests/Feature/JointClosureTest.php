<?php

namespace Tests\Feature;

use App\Models\Tiket;
use App\Models\TiketJointClosure;
use App\Models\TiketJointClosureCore;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JointClosureTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $teknis;
    protected User $client;
    protected Tiket $tiket;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->teknis = User::factory()->create(['role' => 'teknis']);
        $this->client = User::factory()->create(['role' => 'sa_cs']);

        $this->tiket = Tiket::create([
            'no_tiket' => 'BDG-20260924-001',
            'status_link_impact' => 'Down',
            'backbone_segment' => 'SW BBLU - SW Reog',
            'tanggal_open' => now(),
            'created_by' => $this->admin->id,
            'status' => 'OPEN',
            'sla_target_menit' => 240,
        ]);
    }

    public function test_teknisi_can_create_joint_closure_with_dynamic_cores()
    {
        $response = $this->actingAs($this->teknis)
            ->post(route('tiket.joint-closure.store', $this->tiket->id), [
                'nama_closure' => 'JC-01 Span KM 14',
                'jenis_closure' => 'DOME',
                'lokasi_fisik' => 'POLE',
                'kapasitas_kabel_asal' => 96,
                'jumlah_tube_asal' => 8,
                'kapasitas_kabel_jumper' => 48,
                'jumlah_tube_jumper' => 4,
                'is_aset_baru' => 1,
                'latitude' => -6.917464,
                'longitude' => 107.619123,
                'keterangan' => 'Pemasangan closure baru tiang KM 14',
                'tube_asal' => ['Tube 1', 'Tube 1'],
                'core_asal' => ['Core 1', 'Core 2'],
                'tube_jumper' => ['Tube 1', ''],
                'core_jumper' => ['Core 1', ''],
                'core_status' => ['TERHUBUNG', 'SPARE'],
                'loss_db' => [0.02, null],
                'core_keterangan' => ['Spliced to node C', 'Spare core'],
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tiket_joint_closures', [
            'id_tiket' => $this->tiket->id,
            'nama_closure' => 'JC-01 Span KM 14',
            'kapasitas_kabel_asal' => 96,
            'kapasitas_kabel_jumper' => 48,
            'status_aset' => 'ASET_BARU',
        ]);

        $jc = TiketJointClosure::where('nama_closure', 'JC-01 Span KM 14')->first();
        $this->assertNotNull($jc);
        $this->assertTrue($jc->is_aset_baru);
        $this->assertEquals(2, $jc->cores()->count());

        $this->assertDatabaseHas('tiket_joint_closure_cores', [
            'id_joint_closure' => $jc->id,
            'tube_asal' => 'Tube 1',
            'core_asal' => 'Core 1',
            'status_core' => 'TERHUBUNG',
        ]);

        $this->assertDatabaseHas('tiket_joint_closure_cores', [
            'id_joint_closure' => $jc->id,
            'tube_asal' => 'Tube 1',
            'core_asal' => 'Core 2',
            'status_core' => 'SPARE',
        ]);
    }

    public function test_teknisi_can_add_and_delete_single_core_to_existing_joint_closure()
    {
        $jc = TiketJointClosure::create([
            'id_tiket' => $this->tiket->id,
            'created_by' => $this->teknis->id,
            'nama_closure' => 'JC-02',
            'tipe_closure' => 'INLINE',
            'lokasi_penempatan' => 'MANHOLE',
            'kapasitas_kabel_asal' => 24,
            'jumlah_tube_asal' => 2,
            'kapasitas_kabel_jumper' => 24,
            'jumlah_tube_jumper' => 2,
            'status_aset' => 'EKSISTING',
        ]);

        $response = $this->actingAs($this->teknis)
            ->post(route('tiket.joint-closure.add-core', $jc->id), [
                'tube_asal' => 'Tube 2',
                'core_asal' => 'Core 1',
                'tube_jumper' => 'Tube 2',
                'core_jumper' => 'Core 1',
                'status' => 'TERHUBUNG',
                'loss_db' => 0.03,
                'keterangan' => 'Core 1 sambung lurus',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $core = TiketJointClosureCore::where('id_joint_closure', $jc->id)->first();
        $this->assertNotNull($core);
        $this->assertEquals('TERHUBUNG', $core->status);

        // Delete core
        $delResponse = $this->actingAs($this->teknis)
            ->delete(route('tiket.joint-closure.delete-core', $core->id));

        $delResponse->assertRedirect();
        $this->assertDatabaseMissing('tiket_joint_closure_cores', ['id' => $core->id]);
    }

    public function test_teknisi_can_delete_joint_closure()
    {
        $jc = TiketJointClosure::create([
            'id_tiket' => $this->tiket->id,
            'created_by' => $this->teknis->id,
            'nama_closure' => 'JC-Delete-Test',
            'tipe_closure' => 'DOME',
            'lokasi_penempatan' => 'POLE',
            'kapasitas_kabel_asal' => 12,
            'jumlah_tube_asal' => 1,
            'kapasitas_kabel_jumper' => 12,
            'jumlah_tube_jumper' => 1,
            'status_aset' => 'EKSISTING',
        ]);

        TiketJointClosureCore::create([
            'id_joint_closure' => $jc->id,
            'tube_asal' => 'Tube 1',
            'core_asal' => 'Core 1',
            'status_core' => 'SPARE',
        ]);

        $response = $this->actingAs($this->teknis)
            ->delete(route('tiket.joint-closure.destroy', $jc->id));

        $response->assertRedirect();
        $this->assertDatabaseMissing('tiket_joint_closures', ['id' => $jc->id]);
        $this->assertDatabaseMissing('tiket_joint_closure_cores', ['id_joint_closure' => $jc->id]);
    }

    public function test_client_cannot_modify_joint_closure()
    {
        $response = $this->actingAs($this->client)
            ->post(route('tiket.joint-closure.store', $this->tiket->id), [
                'nama_closure' => 'JC-HACK',
                'jenis_closure' => 'DOME',
                'lokasi_fisik' => 'POLE',
                'kapasitas_kabel_asal' => 24,
                'jumlah_tube_asal' => 2,
                'kapasitas_kabel_jumper' => 24,
                'jumlah_tube_jumper' => 2,
            ]);

        $response->assertForbidden();
    }

    public function test_enhanced_manuver_core_stores_new_attributes()
    {
        $response = $this->actingAs($this->teknis)
            ->post(route('tiket.manuver-core.store', $this->tiket->id), [
                'titik' => 'JC-01',
                'tipe' => 'SESUDAH',
                'core_asal' => 'Tube 1 Core 4',
                'core_tujuan' => 'Tube 1 Core 12',
                'lokasi_tipe' => 'CLOSURE_LAPANGAN',
                'core_dialihkan' => 'Core 4 dialihkan ke Core 12',
                'titik_kembali' => 'OTB Node B',
                'status_manuver' => 'TEMPORARY',
                'status_core_aset' => 'OCCUPIED_MANUVER',
                'keterangan' => 'Manuver darurat kabel putus span 14',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('manuver_core', [
            'id_tiket' => $this->tiket->id,
            'titik' => 'JC-01',
            'lokasi_tipe' => 'CLOSURE_LAPANGAN',
            'core_dialihkan' => 'Core 4 dialihkan ke Core 12',
            'status_manuver' => 'TEMPORARY',
            'status_core_aset' => 'OCCUPIED_MANUVER',
        ]);
    }
}
