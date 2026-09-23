<?php

namespace Tests\Feature;

use App\Models\Tiket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_reports_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        Tiket::create([
            'no_tiket' => 'BDG-20260921-040',
            'status_link_impact' => 'Down 10G',
            'backbone_segment' => 'SW BBLU - SW Reog',
            'tanggal_open' => now(),
            'status' => 'OPEN',
            'created_by' => $admin->id,
        ]);

        $response = $this->actingAs($admin)->get(route('reports.index'));
        $response->assertStatus(200);
        $response->assertSee('Laporan & Analisis MTTR / SLA');
        $response->assertSee('BDG-20260921-040');
    }

    public function test_get_mttr_data_json(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('reports.mttr'));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'data' => [
                'year',
                'labels',
                'mttr_minutes',
                'tiket_counts',
            ]
        ]);
    }

    public function test_get_sla_data_json(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('reports.sla'));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'data' => [
                'total_closed',
                'total_tepat',
                'total_lebih',
                'compliance_rate',
            ]
        ]);
    }

    public function test_export_single_tiket_pdf(): void
    {
        $helpdesk = User::factory()->create(['role' => 'helpdesk']);

        $tiket = Tiket::create([
            'no_tiket' => 'BDG-20260921-041',
            'status_link_impact' => 'Down',
            'backbone_segment' => 'SW BBLU - SW Reog',
            'tanggal_open' => now(),
            'status' => 'CLOSE',
            'mttr_minutes' => 120,
            'created_by' => $helpdesk->id,
        ]);

        $response = $this->actingAs($helpdesk)->get(route('reports.export.tiket.pdf', $tiket->id));
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_export_summary_pdf(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        Tiket::create([
            'no_tiket' => 'BDG-20260921-042',
            'status_link_impact' => 'Down',
            'backbone_segment' => 'SW BBLU - SW Reog',
            'tanggal_open' => now(),
            'status' => 'CLOSE',
            'mttr_minutes' => 60,
            'created_by' => $admin->id,
        ]);

        $response = $this->actingAs($admin)->get(route('reports.export.pdf'));
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_export_excel_report(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        Tiket::create([
            'no_tiket' => 'BDG-20260921-043',
            'status_link_impact' => 'Down',
            'backbone_segment' => 'SW BBLU - SW Reog',
            'tanggal_open' => now(),
            'status' => 'CLOSE',
            'mttr_minutes' => 60,
            'created_by' => $admin->id,
        ]);

        $response = $this->actingAs($admin)->get(route('reports.export.excel'));
        $response->assertStatus(200);
        $this->assertTrue(
            str_contains($response->headers->get('content-type'), 'spreadsheetml') ||
            str_contains($response->headers->get('content-disposition'), '.xlsx')
        );
    }
}
