<?php

namespace Tests\Feature;

use App\Models\Dokumentasi;
use App\Models\MasterSla;
use App\Models\ResumePekerjaan;
use App\Models\Tiket;
use App\Models\TitikPerbaikan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TiketWorkflowFase1Test extends TestCase
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
            'no_tiket' => 'TKT/MSN/' . date('Ymd') . '/0099',
            'status_link_impact' => 'Backbone : SW BBLU - SW Reog Down',
            'backbone_segment' => 'SW BBLU - SW Reog',
            'tanggal_open' => Carbon::parse('2026-09-24 08:00:00'),
            'sla_target_minutes' => 240,
            'status' => $status,
            'created_by' => $this->helpdesk->id,
        ]);
    }

    public function test_teknisi_cannot_closing_awal_if_mandatory_prerequisites_not_met(): void
    {
        $tiket = $this->createSampleTiket('PROSES');

        // Missing: Resume, Foto Dokumentasi, Titik Perbaikan, Tipe Penanganan
        $response = $this->actingAs($this->teknisi)->post("/tiket/{$tiket->id}/closing-awal", [
            'catatan_closing_teknisi' => 'Pekerjaan selesai di lapangan.',
        ]);

        $response->assertSessionHas('error');
        $tiket->refresh();
        $this->assertEquals('PROSES', $tiket->status);
    }

    public function test_teknisi_can_closing_awal_when_all_prerequisites_met(): void
    {
        $tiket = $this->createSampleTiket('PROSES');

        // 1. Add Resume
        ResumePekerjaan::create([
            'id_tiket' => $tiket->id,
            'team_om' => ['Ahmad', 'Budi'],
            'problem_temuan' => 'Kabel FO Putus akibat pohon tumbang',
            'action' => 'Splicing 12 core dan proteksi closure',
            'tipe_penanganan' => 'JOINTING_LURUS',
            'joint_closure_type' => 'Closure Dome 24C',
            'core_count_jointed' => 12,
        ]);

        // 2. Add Titik Perbaikan
        TitikPerbaikan::create([
            'id_tiket' => $tiket->id,
            'nama_titik' => 'JC 1 KM 12+200',
            'latitude' => -6.9175,
            'longitude' => 107.6191,
        ]);

        // 3. Add Dokumentasi
        Dokumentasi::create([
            'id_tiket' => $tiket->id,
            'file_path' => 'dokumentasi/dummy.jpg',
            'file_name' => 'dummy.jpg',
            'file_size' => 102400,
            'mime_type' => 'image/jpeg',
            'kategori' => 'DOKUMENTASI',
            'timestamp' => now(),
            'uploaded_by' => $this->teknisi->id,
        ]);

        // Sync tipe penanganan to tiket
        $tiket->update(['tipe_penanganan' => 'JOINTING_LURUS']);

        // Check prerequisites
        $prereqs = $tiket->checkClosingPrerequisites();
        $this->assertTrue($prereqs['ready']);

        // Submit Closing Awal
        $response = $this->actingAs($this->teknisi)->post("/tiket/{$tiket->id}/closing-awal", [
            'catatan' => 'Splicing 12 core selesai dan redaman -18 dBm aman.',
        ]);

        $response->assertRedirect("/tiket/{$tiket->id}");
        $tiket->refresh();
        $this->assertEquals('PENDING_VERIFIKASI', $tiket->status);
        $this->assertEquals($this->teknisi->id, $tiket->resolved_by);
        $this->assertNotNull($tiket->resolved_at);
        $this->assertEquals('Splicing 12 core selesai dan redaman -18 dBm aman.', $tiket->closing_notes_teknisi);
    }

    public function test_helpdesk_can_reject_closing_awal_back_to_proses(): void
    {
        $tiket = $this->createSampleTiket('PENDING_VERIFIKASI');
        $tiket->update([
            'resolved_by' => $this->teknisi->id,
            'resolved_at' => now(),
            'closing_notes_teknisi' => 'Siap verifikasi.',
        ]);

        $response = $this->actingAs($this->helpdesk)->post("/tiket/{$tiket->id}/reject-closing-awal", [
            'alasan_reject' => 'Redaman di OLT masih tinggi -27 dBm, tolong cek core nomor 6.',
        ]);

        $response->assertRedirect("/tiket/{$tiket->id}");
        $tiket->refresh();
        $this->assertEquals('PROSES', $tiket->status);
    }

    public function test_helpdesk_can_perform_final_close_with_sla_and_mttr_calculation(): void
    {
        $tiket = $this->createSampleTiket('PENDING_VERIFIKASI');

        $closeTime = Carbon::parse('2026-09-24 10:30:00'); // 150 minutes duration (under 240 SLA target)

        $response = $this->actingAs($this->helpdesk)->post("/tiket/{$tiket->id}/close", [
            'tanggal_close' => $closeTime->format('Y-m-d H:i'),
            'catatan_closing' => 'Link UP stabil setelah verifikasi NOC.',
        ]);

        $response->assertRedirect("/tiket/{$tiket->id}");
        $tiket->refresh();
        $this->assertEquals('CLOSE', $tiket->status);
        $this->assertEquals('TEPAT', $tiket->sla_status);
        $this->assertEquals(150, $tiket->mttr_minutes);
        $this->assertEquals($this->helpdesk->id, $tiket->closed_by);
    }

    public function test_stop_clock_pauses_sla_and_deducts_duration_from_mttr(): void
    {
        $tiket = $this->createSampleTiket('PROSES');

        // Start Stop Clock (e.g. at 09:00)
        $response = $this->actingAs($this->teknisi)->post("/tiket/{$tiket->id}/stop-clock/start", [
            'reason' => 'CUACA_BURUK',
            'notes' => 'Hujan badai petir di lokasi KM 14.',
        ]);

        $response->assertRedirect("/tiket/{$tiket->id}");
        $tiket->refresh();
        $this->assertTrue((bool)$tiket->is_stop_clock);
        $this->assertNotNull($tiket->activeStopClock);
        $this->assertEquals('CUACA_BURUK', $tiket->activeStopClock->reason);

        // Simulate 60 minutes passing during stop clock
        $activeSc = $tiket->activeStopClock;
        $activeSc->start_time = Carbon::parse('2026-09-24 09:00:00');
        $activeSc->save();

        // Resume Clock at 10:00 (60 minutes stopped)
        Carbon::setTestNow(Carbon::parse('2026-09-24 10:00:00'));
        $response = $this->actingAs($this->helpdesk)->post("/tiket/{$tiket->id}/stop-clock/stop");

        $response->assertRedirect("/tiket/{$tiket->id}");
        $tiket->refresh();
        $this->assertFalse((bool)$tiket->is_stop_clock);
        $this->assertEquals(60, $tiket->total_stop_clock_minutes);

        // Now close ticket at 12:30 (Gross: 270 minutes from 08:00 to 12:30)
        // With 60 minutes stop clock deduction -> Net MTTR = 210 minutes (<= 240 SLA target => TEPAT)
        $tiket->update(['status' => 'PENDING_VERIFIKASI']);
        $closeTime = Carbon::parse('2026-09-24 12:30:00');
        $this->actingAs($this->helpdesk)->post("/tiket/{$tiket->id}/close", [
            'tanggal_close' => $closeTime->format('Y-m-d H:i'),
        ]);

        $tiket->refresh();
        $this->assertEquals('CLOSE', $tiket->status);
        $this->assertEquals(210, $tiket->mttr_minutes); // 270 - 60 = 210
        $this->assertEquals('TEPAT', $tiket->sla_status);

        Carbon::setTestNow(); // reset
    }

    public function test_handover_shift_records_and_creates_kronologis(): void
    {
        $tiket = $this->createSampleTiket('PROSES');

        $response = $this->actingAs($this->teknisi)->post("/tiket/{$tiket->id}/handover-shift", [
            'shift_sebelum' => 'Shift Siang',
            'shift_tujuan' => 'MALAM',
            'status_lapangan' => 'Kabel sudah ditarik dan dikupas.',
            'kendala_pending' => 'Menunggu splicer cadangan.',
            'alokasi_team' => 'Budi, Hendra',
        ]);

        $response->assertRedirect("/tiket/{$tiket->id}");
        $tiket->refresh();

        $this->assertDatabaseHas('tiket_handover_shifts', [
            'id_tiket' => $tiket->id,
            'shift_to' => 'MALAM',
        ]);

        $this->assertDatabaseHas('kronologis', [
            'id_tiket' => $tiket->id,
            'kategori' => 'LAIN',
        ]);
    }
}
