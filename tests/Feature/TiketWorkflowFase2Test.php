<?php

namespace Tests\Feature;

use App\Models\Kronologis;
use App\Models\MasterSla;
use App\Models\Tiket;
use App\Models\TiketStopClock;
use App\Models\User;
use App\Services\KpiService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TiketWorkflowFase2Test extends TestCase
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
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->helpdesk = User::factory()->create([
            'role' => 'helpdesk',
            'is_active' => true,
        ]);

        $this->teknisi = User::factory()->create([
            'role' => 'teknis',
            'is_active' => true,
        ]);

        $this->masterSla = MasterSla::create([
            'backbone_segment' => 'SW BBLU - SW Reog',
            'sla_target_minutes' => 240, // 4 hours
        ]);
    }

    protected function createSampleTiket(string $status = 'PROSES'): Tiket
    {
        return Tiket::create([
            'no_tiket' => 'TKT/MSN/' . date('Ymd') . '/' . rand(1000, 9999),
            'status_link_impact' => 'Backbone : SW BBLU - SW Reog Down',
            'backbone_segment' => 'SW BBLU - SW Reog',
            'tanggal_open' => Carbon::now()->subHours(3),
            'sla_target_minutes' => 240,
            'status' => $status,
            'created_by' => $this->helpdesk->id,
            'resolved_by' => $this->teknisi->id,
        ]);
    }

    public function test_sla_timeline_stages_returns_5_stages_with_accurate_status(): void
    {
        $openTime = Carbon::now()->subHours(2);
        $tiket = Tiket::create([
            'no_tiket' => 'TKT/MSN/20260924/1111',
            'status_link_impact' => 'Backbone : SW BBLU - SW Reog Down',
            'backbone_segment' => 'SW BBLU - SW Reog',
            'tanggal_open' => $openTime,
            'first_response_at' => $openTime->copy()->addMinutes(15),
            'response_time_minutes' => 15,
            'total_stop_clock_minutes' => 30,
            'sla_target_minutes' => 240,
            'status' => 'PROSES',
            'created_by' => $this->helpdesk->id,
        ]);

        // Add first kronologis 15 minutes after open
        Kronologis::create([
            'id_tiket' => $tiket->id,
            'user_id' => $this->teknisi->id,
            'informasi' => 'Teknisi sedang meluncur ke lokasi patching',
            'kategori' => 'IZIN',
            'timestamp' => $openTime->copy()->addMinutes(15),
            'created_at' => $openTime->copy()->addMinutes(15),
        ]);

        // Add stop clock
        TiketStopClock::create([
            'id_tiket' => $tiket->id,
            'requested_by' => $this->helpdesk->id,
            'stopped_by' => $this->helpdesk->id,
            'alasan_kategori' => 'MENUNGGU_PLN',
            'start_time' => $openTime->copy()->addMinutes(30),
            'end_time' => $openTime->copy()->addMinutes(60),
            'duration_minutes' => 30,
            'is_active' => false,
        ]);

        $stages = $tiket->sla_timeline_stages;

        $this->assertCount(5, $stages);
        $this->assertEquals('OPEN', $stages[0]['key']);
        $this->assertTrue($stages[0]['is_completed']);

        $this->assertEquals('FIRST_RESPONSE', $stages[1]['key']);
        $this->assertTrue($stages[1]['is_completed']);
        $this->assertEquals('15 mnt', $stages[1]['badge']);

        $this->assertEquals('STOP_CLOCK', $stages[2]['key']);
        $this->assertTrue($stages[2]['is_completed']);
        $this->assertEquals('30 mnt', $stages[2]['badge']);

        $this->assertEquals('CLOSING_AWAL', $stages[3]['key']);
        $this->assertFalse($stages[3]['is_completed']);

        $this->assertEquals('CLOSING_AKHIR', $stages[4]['key']);
        $this->assertFalse($stages[4]['is_completed']);
    }

    public function test_field_update_status_overdue_warning_and_normal_logic(): void
    {
        // 1. Tiket with no kronologis created 40 mins ago -> OVERDUE (> 30 min)
        $tiketOverdue = Tiket::create([
            'no_tiket' => 'TKT/MSN/20260924/2222',
            'status_link_impact' => 'Backbone Down',
            'backbone_segment' => 'SW BBLU - SW Reog',
            'tanggal_open' => Carbon::now()->subMinutes(40),
            'sla_target_minutes' => 240,
            'status' => 'PROSES',
            'created_by' => $this->helpdesk->id,
        ]);
        $this->assertEquals('OVERDUE', $tiketOverdue->field_update_status);

        // 2. Tiket with recent kronologis (5 mins ago) -> NORMAL
        $tiketNormal = Tiket::create([
            'no_tiket' => 'TKT/MSN/20260924/3333',
            'status_link_impact' => 'Backbone Down',
            'backbone_segment' => 'SW BBLU - SW Reog',
            'tanggal_open' => Carbon::now()->subMinutes(60),
            'sla_target_minutes' => 240,
            'status' => 'PROSES',
            'created_by' => $this->helpdesk->id,
        ]);
        Kronologis::create([
            'id_tiket' => $tiketNormal->id,
            'user_id' => $this->teknisi->id,
            'informasi' => 'Proses core alignment sedang berlangsung',
            'kategori' => 'JOINTING',
            'timestamp' => Carbon::now()->subMinutes(5),
            'created_at' => Carbon::now()->subMinutes(5),
        ]);
        $this->assertEquals('NORMAL', $tiketNormal->field_update_status);

        // 3. Tiket with kronologis 25 mins ago -> WARNING (between 20-30 min)
        $tiketWarning = Tiket::create([
            'no_tiket' => 'TKT/MSN/20260924/4444',
            'status_link_impact' => 'Backbone Down',
            'backbone_segment' => 'SW BBLU - SW Reog',
            'tanggal_open' => Carbon::now()->subMinutes(60),
            'sla_target_minutes' => 240,
            'status' => 'PROSES',
            'created_by' => $this->helpdesk->id,
        ]);
        Kronologis::create([
            'id_tiket' => $tiketWarning->id,
            'user_id' => $this->teknisi->id,
            'informasi' => 'Tracing kabel selesai',
            'kategori' => 'TRACING',
            'timestamp' => Carbon::now()->subMinutes(25),
            'created_at' => Carbon::now()->subMinutes(25),
        ]);
        $this->assertEquals('WARNING', $tiketWarning->field_update_status);
    }

    public function test_average_report_interval_and_verification_duration(): void
    {
        $openTime = Carbon::parse('2026-09-24 08:00:00');
        $tiket = Tiket::create([
            'no_tiket' => 'TKT/MSN/20260924/5555',
            'status_link_impact' => 'Backbone Down',
            'backbone_segment' => 'SW BBLU - SW Reog',
            'tanggal_open' => $openTime,
            'sla_target_minutes' => 240,
            'status' => 'CLOSE',
            'created_by' => $this->helpdesk->id,
            'resolved_at' => $openTime->copy()->addMinutes(120),
            'tanggal_close' => $openTime->copy()->addMinutes(135),
            'closed_by' => $this->helpdesk->id,
        ]);

        Kronologis::create([
            'id_tiket' => $tiket->id,
            'user_id' => $this->teknisi->id,
            'informasi' => 'Update 1',
            'kategori' => 'LAIN',
            'timestamp' => $openTime->copy()->addMinutes(30),
            'created_at' => $openTime->copy()->addMinutes(30),
        ]);
        Kronologis::create([
            'id_tiket' => $tiket->id,
            'user_id' => $this->teknisi->id,
            'informasi' => 'Update 2',
            'kategori' => 'LAIN',
            'timestamp' => $openTime->copy()->addMinutes(60),
            'created_at' => $openTime->copy()->addMinutes(60),
        ]);
        Kronologis::create([
            'id_tiket' => $tiket->id,
            'user_id' => $this->teknisi->id,
            'informasi' => 'Update 3',
            'kategori' => 'LAIN',
            'timestamp' => $openTime->copy()->addMinutes(100),
            'created_at' => $openTime->copy()->addMinutes(100),
        ]);

        // Interval between 30->60 (30m), 60->100 (40m) = avg (30+40)/2 = 35m
        $this->assertEquals(35, $tiket->average_report_interval_minutes);

        // Verification duration: resolved at +120m, closed at +135m -> 15 minutes
        $this->assertEquals(15, $tiket->verification_duration_minutes);
    }

    public function test_kpi_service_executive_summary_and_technician_rankings(): void
    {
        $openTime = Carbon::parse('2026-09-24 08:00:00');

        // Create 2 closed tickets
        $t1 = Tiket::create([
            'no_tiket' => 'TKT/MSN/20260924/6661',
            'status_link_impact' => 'Backbone Down 1',
            'backbone_segment' => 'SW BBLU - SW Reog',
            'tanggal_open' => $openTime,
            'sla_target_minutes' => 240,
            'status' => 'CLOSE',
            'created_by' => $this->helpdesk->id,
            'resolved_by' => $this->teknisi->id,
            'resolved_at' => $openTime->copy()->addMinutes(90),
            'tanggal_close' => $openTime->copy()->addMinutes(100),
            'mttr_minutes' => 100,
            'sla_status' => 'TEPAT',
            'closed_by' => $this->helpdesk->id,
        ]);
        Kronologis::create([
            'id_tiket' => $t1->id,
            'user_id' => $this->teknisi->id,
            'informasi' => 'Respon cepat',
            'kategori' => 'IZIN',
            'timestamp' => $openTime->copy()->addMinutes(10),
            'created_at' => $openTime->copy()->addMinutes(10),
        ]);

        $t2 = Tiket::create([
            'no_tiket' => 'TKT/MSN/20260924/6662',
            'status_link_impact' => 'Backbone Down 2',
            'backbone_segment' => 'SW BBLU - SW Reog',
            'tanggal_open' => $openTime,
            'sla_target_minutes' => 60,
            'status' => 'CLOSE',
            'created_by' => $this->helpdesk->id,
            'resolved_by' => $this->teknisi->id,
            'resolved_at' => $openTime->copy()->addMinutes(80),
            'tanggal_close' => $openTime->copy()->addMinutes(90),
            'mttr_minutes' => 90,
            'sla_status' => 'LEBIH',
            'closed_by' => $this->helpdesk->id,
        ]);

        $kpiService = new KpiService();
        $summary = $kpiService->getExecutiveKpiSummary();

        $this->assertEquals(2, $summary['total_tiket']);
        $this->assertEquals(2, $summary['total_closed']);
        $this->assertEquals(1, $summary['total_tepat_sla']);
        $this->assertEquals(50.0, $summary['sla_compliance_rate']);

        $teknisiList = $kpiService->getTeknisiKpiList();
        $this->assertNotEmpty($teknisiList);
        $this->assertEquals($this->teknisi->id, $teknisiList[0]['user_id']);
        $this->assertEquals(2, $teknisiList[0]['total_resolved']);
        $this->assertEquals(50.0, $teknisiList[0]['sla_compliance_rate']);
    }

    public function test_kpi_dashboard_page_renders_successfully(): void
    {
        $response = $this->actingAs($this->admin)->get(route('reports.kpi'));

        $response->assertStatus(200);
        $response->assertSee('Key Performance Indicators (KPI)');
        $response->assertSee('Leaderboard KPI Teknisi Lapangan');
        $response->assertSee('KPI HelpDesk / NOC');
    }
}
