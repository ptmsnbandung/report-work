<?php

namespace Tests\Unit;

use App\Models\Tiket;
use App\Models\User;
use App\Services\MttrService;
use App\Services\ReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ReportService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ReportService(new MttrService());
    }

    public function test_get_filtered_tikets(): void
    {
        $user = User::factory()->create();

        Tiket::create([
            'no_tiket' => 'BDG-20260921-030',
            'status_link_impact' => 'Down 10G',
            'backbone_segment' => 'SW BBLU - SW Reog',
            'tanggal_open' => now(),
            'status' => 'OPEN',
            'created_by' => $user->id,
        ]);

        Tiket::create([
            'no_tiket' => 'BDG-20260921-031',
            'status_link_impact' => 'Degraded',
            'backbone_segment' => 'SW Cicaheum - SW Ujungberung',
            'tanggal_open' => now(),
            'status' => 'CLOSE',
            'mttr_minutes' => 90,
            'created_by' => $user->id,
        ]);

        $filtered = $this->service->getFilteredTikets(['status' => 'OPEN']);
        $this->assertCount(1, $filtered);
        $this->assertEquals('BDG-20260921-030', $filtered->first()->no_tiket);
    }

    public function test_get_summary_metrics(): void
    {
        $user = User::factory()->create();

        Tiket::create([
            'no_tiket' => 'BDG-20260921-032',
            'status_link_impact' => 'Down',
            'backbone_segment' => 'SW BBLU - SW Reog',
            'tanggal_open' => now(),
            'status' => 'OPEN',
            'created_by' => $user->id,
        ]);

        Tiket::create([
            'no_tiket' => 'BDG-20260921-033',
            'status_link_impact' => 'Down',
            'backbone_segment' => 'SW BBLU - SW Reog',
            'tanggal_open' => now(),
            'status' => 'CLOSE',
            'mttr_minutes' => 120,
            'sla_status' => 'TEPAT',
            'created_by' => $user->id,
        ]);

        $metrics = $this->service->getSummaryMetrics();
        $this->assertEquals(2, $metrics['total_tiket']);
        $this->assertEquals(1, $metrics['total_open']);
        $this->assertEquals(1, $metrics['total_close']);
        $this->assertEquals(100.0, $metrics['sla_compliance_rate']);
    }

    public function test_generate_single_tiket_pdf(): void
    {
        $user = User::factory()->create();

        $tiket = Tiket::create([
            'no_tiket' => 'BDG-20260921-034',
            'status_link_impact' => 'Down',
            'backbone_segment' => 'SW BBLU - SW Reog',
            'tanggal_open' => now(),
            'status' => 'CLOSE',
            'mttr_minutes' => 180,
            'sla_target_minutes' => 360,
            'sla_status' => 'TEPAT',
            'created_by' => $user->id,
        ]);

        $pdf = $this->service->generateSingleTiketPdf($tiket);
        $this->assertNotEmpty($pdf->output());
    }
}
