<?php

namespace Tests\Unit;

use App\Models\MasterSla;
use App\Models\Tiket;
use App\Models\User;
use App\Services\TiketService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class TiketServiceTest extends TestCase
{
    use RefreshDatabase;

    protected TiketService $service;
    protected User $helpdesk;
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new TiketService();

        $this->helpdesk = User::factory()->create([
            'role' => 'helpdesk',
            'is_active' => true,
        ]);

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        MasterSla::create([
            'backbone_segment' => 'SW BBLU - SW Reog',
            'sla_target_minutes' => 360,
        ]);
    }

    public function test_generate_no_tiket_format(): void
    {
        $date = Carbon::parse('2026-09-14 10:00:00');
        $noTiket = $this->service->generateNoTiket($date);

        $this->assertEquals('BDG-20260914-001', $noTiket);
    }

    public function test_generate_no_tiket_increments_sequence(): void
    {
        $date = Carbon::parse('2026-09-14 10:00:00');

        Tiket::create([
            'no_tiket' => 'BDG-20260914-001',
            'status_link_impact' => 'SW BBLU - SW Reog Down',
            'backbone_segment' => 'SW BBLU - SW Reog',
            'tanggal_open' => $date,
            'status' => 'OPEN',
            'sla_target_minutes' => 360,
            'created_by' => $this->helpdesk->id,
        ]);

        $nextNoTiket = $this->service->generateNoTiket($date);
        $this->assertEquals('BDG-20260914-002', $nextNoTiket);
    }

    public function test_hitung_mttr_in_minutes(): void
    {
        $open = Carbon::parse('2026-09-14 14:30:00');
        $close = Carbon::parse('2026-09-14 20:15:00');

        $mttr = $this->service->hitungMttr($open, $close);
        // 14:30 to 20:15 = 5 hours 45 minutes = 345 minutes
        $this->assertEquals(345, $mttr);
    }

    public function test_cek_sla_status(): void
    {
        // SLA target 360 min (6 hours)
        $this->assertEquals('TEPAT', $this->service->cekSla(345, 360));
        $this->assertEquals('TEPAT', $this->service->cekSla(360, 360));
        $this->assertEquals('LEBIH', $this->service->cekSla(361, 360));
        $this->assertEquals('LEBIH', $this->service->cekSla(500, 360));
        $this->assertEquals('NA', $this->service->cekSla(100, null));
    }

    public function test_create_tiket_successfully(): void
    {
        $data = [
            'status_link_impact' => 'Backbone : SW BBLU - SW Reog Down',
            'backbone_segment' => 'SW BBLU - SW Reog',
            'tanggal_open' => '2026-09-14 14:30:00',
            'deskripsi' => 'Fiber cut detected near crossing.',
        ];

        $tiket = $this->service->createTiket($data, $this->helpdesk);

        $this->assertDatabaseHas('tiket', [
            'id' => $tiket->id,
            'no_tiket' => 'BDG-20260914-001',
            'status' => 'OPEN',
            'sla_target_minutes' => 360,
            'sla_status' => 'NA',
            'created_by' => $this->helpdesk->id,
        ]);
    }

    public function test_update_tiket_when_open(): void
    {
        $tiket = $this->service->createTiket([
            'status_link_impact' => 'SW BBLU - SW Reog Down',
            'backbone_segment' => 'SW BBLU - SW Reog',
            'tanggal_open' => '2026-09-14 14:30:00',
        ], $this->helpdesk);

        $updated = $this->service->updateTiket($tiket, [
            'status_link_impact' => 'SW BBLU - SW Reog Down (Updated Info)',
        ]);

        $this->assertEquals('SW BBLU - SW Reog Down (Updated Info)', $updated->status_link_impact);
    }

    public function test_cannot_update_closed_tiket(): void
    {
        $tiket = $this->service->createTiket([
            'status_link_impact' => 'SW BBLU - SW Reog Down',
            'backbone_segment' => 'SW BBLU - SW Reog',
            'tanggal_open' => '2026-09-14 14:30:00',
        ], $this->helpdesk);

        $tiket->update(['status' => 'PENDING_VERIFIKASI']);

        $this->service->closeTiket($tiket, $this->helpdesk, [
            'tanggal_close' => '2026-09-14 19:00:00',
        ]);

        $this->expectException(InvalidArgumentException::class);
        $this->service->updateTiket($tiket, [
            'status_link_impact' => 'Should Fail',
        ]);
    }

    public function test_close_tiket_calculates_mttr_and_sla(): void
    {
        $openDate = Carbon::parse('2026-09-14 14:30:00');
        $closeDate = Carbon::parse('2026-09-14 20:15:00'); // 345 minutes -> <= 360 -> TEPAT

        $tiket = $this->service->createTiket([
            'status_link_impact' => 'SW BBLU - SW Reog Down',
            'backbone_segment' => 'SW BBLU - SW Reog',
            'tanggal_open' => $openDate->toDateTimeString(),
            'sla_target_minutes' => 360,
        ], $this->helpdesk);

        $tiket->update(['status' => 'PENDING_VERIFIKASI']);

        $closed = $this->service->closeTiket($tiket, $this->helpdesk, [
            'tanggal_close' => $closeDate->toDateTimeString(),
        ]);

        $this->assertEquals('CLOSE', $closed->status);
        $this->assertEquals(345, $closed->mttr_minutes);
        $this->assertEquals('TEPAT', $closed->sla_status);
        $this->assertEquals($this->helpdesk->id, $closed->closed_by);
    }

    public function test_cannot_close_tiket_if_not_pending_verifikasi(): void
    {
        $tiket = $this->service->createTiket([
            'status_link_impact' => 'SW BBLU - SW Reog Down',
            'backbone_segment' => 'SW BBLU - SW Reog',
            'tanggal_open' => '2026-09-14 14:30:00',
        ], $this->helpdesk);

        $this->expectException(InvalidArgumentException::class);
        $this->service->closeTiket($tiket, $this->helpdesk, [
            'tanggal_close' => '2026-09-14 19:00:00',
        ]);
    }
}
