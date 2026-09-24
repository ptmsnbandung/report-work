<?php

namespace Tests\Feature;

use App\Exports\KpiExport;
use App\Models\MasterSla;
use App\Models\Tiket;
use App\Models\TiketManuverCore;
use App\Models\TiketStopClock;
use App\Models\TiketTitikPerbaikan;
use App\Models\User;
use App\Services\MttrService;
use App\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class TiketWorkflowFase4Test extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $helpdesk;
    protected User $teknisi;
    protected MasterSla $masterSla;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name' => 'Admin CJP',
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->helpdesk = User::factory()->create([
            'name' => 'Helpdesk NOC',
            'role' => 'helpdesk',
            'is_active' => true,
        ]);

        $this->teknisi = User::factory()->create([
            'name' => 'Teknisi Alpha',
            'role' => 'teknis',
            'is_active' => true,
        ]);

        $this->masterSla = MasterSla::create([
            'backbone_segment' => 'BDG-JKT Fiber Backbone',
            'sla_target_minutes' => 240,
        ]);
    }

    protected function createSampleTiket(array $attributes = []): Tiket
    {
        return Tiket::create(array_merge([
            'no_tiket' => 'TKT-2026-' . rand(1000, 9999),
            'source' => 'CJP',
            'status_link_impact' => 'Loss Core Fiber Cut',
            'backbone_segment' => 'BDG-JKT Fiber Backbone',
            'tipe_penanganan' => 'Jointing Lurus',
            'tipe_perbaikan' => 'Splicing 24 Core',
            'status' => 'CLOSE',
            'sla_status' => 'TEPAT',
            'sla_target_minutes' => 240,
            'mttr_minutes' => 120,
            'tanggal_open' => Carbon::now()->subHours(4),
            'tanggal_close' => Carbon::now()->subHours(2),
            'assigned_to' => $this->teknisi->id,
            'created_by' => $this->helpdesk->id,
            'closed_by' => $this->helpdesk->id,
        ], $attributes));
    }

    public function test_it_calculates_category_breakdown_analytics()
    {
        // 1 Tiket with Jointing Lurus
        $t1 = $this->createSampleTiket([
            'tipe_penanganan' => 'Jointing Lurus',
            'mttr_minutes' => 120,
        ]);

        // 1 Tiket with Manuver Core
        $t2 = $this->createSampleTiket([
            'tipe_penanganan' => 'Manuver Core',
            'mttr_minutes' => 60,
        ]);

        $mttrService = app(MttrService::class);
        $breakdown = $mttrService->getCategoryBreakdown();

        $this->assertArrayHasKey('labels', $breakdown);
        $this->assertArrayHasKey('counts', $breakdown);
        $this->assertArrayHasKey('avg_mttr', $breakdown);
        $this->assertEquals(2, $breakdown['total_all']);
        $this->assertContains('Jointing Lurus', $breakdown['labels']);
        $this->assertContains('Manuver Core', $breakdown['labels']);
    }

    public function test_it_calculates_stop_clock_impact_analytics()
    {
        $tiket = $this->createSampleTiket();

        TiketStopClock::create([
            'id_tiket' => $tiket->id,
            'start_time' => Carbon::now()->subHours(3),
            'stop_time' => Carbon::now()->subHours(2),
            'duration_minutes' => 60,
            'alasan_kategori' => 'akses_lokasi',
            'alasan_detail' => 'Menunggu izin masuk gedung',
            'paused_by' => $this->helpdesk->id,
            'resumed_by' => $this->helpdesk->id,
        ]);

        TiketStopClock::create([
            'id_tiket' => $tiket->id,
            'start_time' => Carbon::now()->subHours(2),
            'stop_time' => Carbon::now()->subHours(1)->subMinutes(30),
            'duration_minutes' => 30,
            'alasan_kategori' => 'cuaca_buruk',
            'alasan_detail' => 'Hujan badai petir',
            'paused_by' => $this->helpdesk->id,
            'resumed_by' => $this->helpdesk->id,
        ]);

        $mttrService = app(MttrService::class);
        $impact = $mttrService->getStopClockImpactAnalytics();

        $this->assertEquals(90, $impact['total_paused_minutes']);
        $this->assertEquals(2, $impact['total_events']);
        $this->assertCount(2, $impact['by_reason']);
    }

    public function test_it_calculates_hourly_incident_distribution_24_hours()
    {
        $this->createSampleTiket([
            'tanggal_open' => Carbon::today()->setHour(9)->setMinute(15),
        ]);
        $this->createSampleTiket([
            'tanggal_open' => Carbon::today()->setHour(9)->setMinute(45),
        ]);
        $this->createSampleTiket([
            'tanggal_open' => Carbon::today()->setHour(14)->setMinute(20),
        ]);

        $mttrService = app(MttrService::class);
        $distribution = $mttrService->getHourlyDistribution();

        $this->assertCount(24, $distribution['labels']);
        $this->assertCount(24, $distribution['counts']);
        $this->assertEquals(2, $distribution['counts'][9]);
        $this->assertEquals(1, $distribution['counts'][14]);
    }

    public function test_reports_index_page_renders_with_advanced_analytics()
    {
        $this->createSampleTiket();

        $response = $this->actingAs($this->admin)->get(route('reports.index'));

        $response->assertStatus(200);
        $response->assertViewHas('categoryBreakdown');
        $response->assertViewHas('stopClockImpact');
        $response->assertViewHas('hourlyDistribution');
        $response->assertSee('Distribusi Jam Kejadian Gangguan');
        $response->assertSee('Tipe Penanganan Lapangan');
        $response->assertSee('Dampak Efisiensi Stop Clock');
    }

    public function test_it_generates_and_exports_kpi_pdf_successfully()
    {
        $this->createSampleTiket();

        $response = $this->actingAs($this->admin)->get(route('reports.export.kpi.pdf'));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_it_downloads_kpi_excel_successfully()
    {
        $this->createSampleTiket();

        $response = $this->actingAs($this->admin)->get(route('reports.export.kpi.excel'));

        $response->assertStatus(200);
        $response->assertHeader('content-disposition');
    }
}
