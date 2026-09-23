<?php

namespace Tests\Unit;

use App\Models\Tiket;
use App\Models\User;
use App\Services\MttrService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MttrServiceTest extends TestCase
{
    use RefreshDatabase;

    protected MttrService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new MttrService();
    }

    public function test_calculate_mttr_between_open_and_close(): void
    {
        $user = User::factory()->create();
        $open = Carbon::now()->subMinutes(150);
        $close = Carbon::now();

        $tiket = Tiket::create([
            'no_tiket' => 'BDG-20260921-020',
            'status_link_impact' => 'Down',
            'backbone_segment' => 'SW BBLU - SW Reog',
            'tanggal_open' => $open,
            'tanggal_close' => $close,
            'status' => 'CLOSE',
            'created_by' => $user->id,
        ]);

        $mttr = $this->service->calculateMttr($tiket);
        $this->assertEquals(150, $mttr);
    }

    public function test_check_sla_compliance(): void
    {
        $user = User::factory()->create();

        // Case 1: Tepat SLA (120 <= 360)
        $tiketTepat = Tiket::create([
            'no_tiket' => 'BDG-20260921-021',
            'status_link_impact' => 'Down',
            'backbone_segment' => 'SW BBLU - SW Reog',
            'tanggal_open' => now()->subMinutes(120),
            'status' => 'CLOSE',
            'mttr_minutes' => 120,
            'sla_target_minutes' => 360,
            'created_by' => $user->id,
        ]);
        $this->assertEquals('TEPAT', $this->service->checkSlaCompliance($tiketTepat));

        // Case 2: Melebihi SLA (400 > 360)
        $tiketLebih = Tiket::create([
            'no_tiket' => 'BDG-20260921-022',
            'status_link_impact' => 'Down',
            'backbone_segment' => 'SW BBLU - SW Reog',
            'tanggal_open' => now()->subMinutes(400),
            'status' => 'CLOSE',
            'mttr_minutes' => 400,
            'sla_target_minutes' => 360,
            'created_by' => $user->id,
        ]);
        $this->assertEquals('LEBIH', $this->service->checkSlaCompliance($tiketLebih));
    }

    public function test_get_mttr_average_and_compliance_stats(): void
    {
        $user = User::factory()->create();

        Tiket::create([
            'no_tiket' => 'BDG-20260921-023',
            'status_link_impact' => 'Down',
            'backbone_segment' => 'SW BBLU - SW Reog',
            'tanggal_open' => now()->subMinutes(100),
            'tanggal_close' => now(),
            'status' => 'CLOSE',
            'mttr_minutes' => 100,
            'sla_target_minutes' => 360,
            'sla_status' => 'TEPAT',
            'created_by' => $user->id,
        ]);

        Tiket::create([
            'no_tiket' => 'BDG-20260921-024',
            'status_link_impact' => 'Down',
            'backbone_segment' => 'SW BBLU - SW Reog',
            'tanggal_open' => now()->subMinutes(200),
            'tanggal_close' => now(),
            'status' => 'CLOSE',
            'mttr_minutes' => 200,
            'sla_target_minutes' => 360,
            'sla_status' => 'TEPAT',
            'created_by' => $user->id,
        ]);

        $avg = $this->service->getMttrAverage();
        $this->assertEquals(150, $avg['average_minutes']);
        $this->assertEquals(2, $avg['total_closed']);

        $sla = $this->service->getSlaComplianceStats();
        $this->assertEquals(100.0, $sla['compliance_rate']);
        $this->assertEquals(2, $sla['total_tepat']);
    }
}
