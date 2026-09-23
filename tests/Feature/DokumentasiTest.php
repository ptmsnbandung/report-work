<?php

namespace Tests\Feature;

use App\Models\Dokumentasi;
use App\Models\Tiket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DokumentasiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_teknis_can_upload_dokumentasi(): void
    {
        $teknis = User::factory()->create(['role' => 'teknis']);
        $tiket = Tiket::create([
            'no_tiket' => 'BDG-20260921-006',
            'status_link_impact' => 'Down',
            'backbone_segment' => 'SW BBLU - SW Reog',
            'tanggal_open' => now(),
            'status' => 'OPEN',
            'created_by' => $teknis->id,
        ]);

        $file = UploadedFile::fake()->image('test_otdr.jpg');

        $response = $this->actingAs($teknis)->post(route('tiket.dokumentasi.store', $tiket->id), [
            'kategori' => 'Hasil OTDR',
            'photos' => [$file],
            'latitude' => -6.379199,
            'longitude' => 106.846552,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('dokumentasi', [
            'id_tiket' => $tiket->id,
            'kategori' => 'Hasil OTDR',
        ]);
    }

    public function test_user_can_delete_dokumentasi(): void
    {
        $teknis = User::factory()->create(['role' => 'teknis']);
        $tiket = Tiket::create([
            'no_tiket' => 'BDG-20260921-007',
            'status_link_impact' => 'Down',
            'backbone_segment' => 'SW BBLU - SW Reog',
            'tanggal_open' => now(),
            'status' => 'OPEN',
            'created_by' => $teknis->id,
        ]);

        $doc = Dokumentasi::create([
            'id_tiket' => $tiket->id,
            'kategori' => 'Closure Terpasang',
            'file_path' => 'dokumentasi/test.jpg',
            'timestamp' => now(),
        ]);

        $response = $this->actingAs($teknis)->delete(route('tiket.dokumentasi.destroy', $doc->id));
        $response->assertRedirect();
        $this->assertDatabaseMissing('dokumentasi', ['id' => $doc->id]);
    }

    public function test_client_cannot_upload_dokumentasi(): void
    {
        $client = User::factory()->create(['role' => 'client']);
        $helpdesk = User::factory()->create(['role' => 'helpdesk']);
        $tiket = Tiket::create([
            'no_tiket' => 'BDG-20260921-008',
            'status_link_impact' => 'Down',
            'backbone_segment' => 'SW BBLU - SW Reog',
            'tanggal_open' => now(),
            'status' => 'OPEN',
            'created_by' => $helpdesk->id,
        ]);

        $response = $this->actingAs($client)->post(route('tiket.dokumentasi.store', $tiket->id), [
            'kategori' => 'Hasil Jointing',
        ]);

        $response->assertStatus(403);
    }
}
