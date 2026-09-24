<?php

namespace Tests\Feature;

use App\Models\MasterSla;
use App\Models\Tiket;
use App\Models\TiketHandoverShift;
use App\Models\User;
use App\Notifications\HandoverShiftNotification;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class TiketWorkflowFase3Test extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $helpdesk;
    protected User $teknisi1;
    protected User $teknisi2;
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

        $this->teknisi1 = User::factory()->create([
            'name' => 'Teknisi Shift Pagi',
            'role' => 'teknis',
            'is_active' => true,
        ]);

        $this->teknisi2 = User::factory()->create([
            'name' => 'Teknisi Shift Siang',
            'role' => 'teknis',
            'is_active' => true,
        ]);

        $this->masterSla = MasterSla::create([
            'backbone_segment' => 'SW BBLU - SW Reog',
            'sla_target_minutes' => 240,
        ]);
    }

    protected function createSampleTiket(string $status = 'PROSES'): Tiket
    {
        return Tiket::create([
            'no_tiket' => 'TKT/MSN/' . date('Ymd') . '/' . rand(1000, 9999),
            'status_link_impact' => 'Backbone : SW BBLU - SW Reog Down',
            'backbone_segment' => 'SW BBLU - SW Reog',
            'tanggal_open' => Carbon::now()->subHours(4),
            'sla_target_minutes' => 240,
            'status' => $status,
            'created_by' => $this->helpdesk->id,
            'resolved_by' => $this->teknisi1->id,
        ]);
    }

    public function test_handover_shift_creates_record_kronologis_and_dispatches_notification(): void
    {
        Notification::fake();

        $tiket = $this->createSampleTiket('PROSES');

        $response = $this->actingAs($this->teknisi1)->post(route('tiket.handover-shift', $tiket->id), [
            'shift_from' => 'Shift 1 (Pagi 07:00-15:00)',
            'shift_to' => 'Shift 2 (Siang 15:00-23:00)',
            'user_to_id' => $this->teknisi2->id,
            'status_lapangan' => 'Penarikan kabel 150m selesai, persiapan splicing core 1-12 di JC-02.',
            'kendala_pending' => 'Baterai splicer menipis, butuh charger di posko.',
        ]);

        $response->assertRedirect(route('tiket.show', $tiket->id));
        $response->assertSessionHas('success');

        // Assert record created in database
        $this->assertDatabaseHas('tiket_handover_shifts', [
            'id_tiket' => $tiket->id,
            'shift_from' => 'Shift 1 (Pagi 07:00-15:00)',
            'shift_to' => 'Shift 2 (Siang 15:00-23:00)',
            'user_from_id' => $this->teknisi1->id,
            'user_to_id' => $this->teknisi2->id,
        ]);

        // Assert kronologis created
        $this->assertDatabaseHas('kronologis', [
            'id_tiket' => $tiket->id,
            'user_id' => $this->teknisi1->id,
            'kategori' => 'LAIN',
        ]);

        // Assert notification sent to recipient user
        Notification::assertSentTo(
            $this->teknisi2,
            HandoverShiftNotification::class
        );
    }

    public function test_reports_shifts_page_renders_with_metrics_and_filters(): void
    {
        $tiket = $this->createSampleTiket('PROSES');

        TiketHandoverShift::create([
            'id_tiket' => $tiket->id,
            'shift_from' => 'Shift 1 (Pagi 07:00-15:00)',
            'shift_to' => 'Shift 2 (Siang 15:00-23:00)',
            'user_from_id' => $this->teknisi1->id,
            'user_to_id' => $this->teknisi2->id,
            'catatan_handover' => 'Serah terima penanganan kabel putus.',
        ]);

        $response = $this->actingAs($this->admin)->get(route('reports.shifts'));

        $response->assertStatus(200);
        $response->assertSee('Audit &amp; Rekapitulasi Handover / Oper Shift', false);
        $response->assertSee('TOTAL HANDOVER');
        $response->assertSee('Shift 1 (Pagi');
        $response->assertSee('Shift 2 (Siang');
        $response->assertSee('Serah terima penanganan kabel putus.');
    }

    public function test_reports_export_shifts_pdf_generates_file(): void
    {
        $tiket = $this->createSampleTiket('PROSES');

        TiketHandoverShift::create([
            'id_tiket' => $tiket->id,
            'shift_from' => 'Shift 1 (Pagi 07:00-15:00)',
            'shift_to' => 'Shift 2 (Siang 15:00-23:00)',
            'user_from_id' => $this->teknisi1->id,
            'user_to_id' => $this->teknisi2->id,
            'catatan_handover' => 'Catatan serah terima shift.',
        ]);

        $response = $this->actingAs($this->admin)->get(route('reports.export.shifts.pdf'));

        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('content-type'));
    }

    public function test_reports_export_shifts_excel_downloads_file(): void
    {
        $tiket = $this->createSampleTiket('PROSES');

        TiketHandoverShift::create([
            'id_tiket' => $tiket->id,
            'shift_from' => 'Shift 1 (Pagi 07:00-15:00)',
            'shift_to' => 'Shift 2 (Siang 15:00-23:00)',
            'user_from_id' => $this->teknisi1->id,
            'user_to_id' => $this->teknisi2->id,
            'catatan_handover' => 'Catatan serah terima shift excel.',
        ]);

        $response = $this->actingAs($this->admin)->get(route('reports.export.shifts.excel'));

        $response->assertStatus(200);
    }

    public function test_single_tiket_pdf_includes_handover_shift_section_when_present(): void
    {
        $tiket = $this->createSampleTiket('PROSES');

        TiketHandoverShift::create([
            'id_tiket' => $tiket->id,
            'shift_from' => 'Shift 1 (Pagi 07:00-15:00)',
            'shift_to' => 'Shift 2 (Siang 15:00-23:00)',
            'user_from_id' => $this->teknisi1->id,
            'user_to_id' => $this->teknisi2->id,
            'catatan_handover' => 'Handover shift khusus untuk Berita Acara.',
        ]);

        $response = $this->actingAs($this->admin)->get(route('reports.export.tiket.pdf', $tiket->id));

        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('content-type'));
    }
}
