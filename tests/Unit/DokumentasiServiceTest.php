<?php

namespace Tests\Unit;

use App\Models\Dokumentasi;
use App\Models\Tiket;
use App\Models\User;
use App\Services\DokumentasiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DokumentasiServiceTest extends TestCase
{
    use RefreshDatabase;

    protected DokumentasiService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new DokumentasiService();
        Storage::fake('public');
    }

    public function test_upload_single_photo_successfully(): void
    {
        $user = User::factory()->create(['role' => 'teknis']);
        $tiket = Tiket::create([
            'no_tiket' => 'BDG-20260921-001',
            'status_link_impact' => 'Down',
            'backbone_segment' => 'SW BBLU - SW Reog',
            'tanggal_open' => now(),
            'status' => 'OPEN',
            'created_by' => $user->id,
        ]);

        $file = UploadedFile::fake()->image('jointing.jpg', 600, 400);

        $data = [
            'kategori' => 'Hasil Jointing',
            'latitude' => -6.379199,
            'longitude' => 106.846552,
        ];

        $docs = $this->service->upload($tiket, $data, $file);

        $this->assertCount(1, $docs);
        $this->assertDatabaseHas('dokumentasi', [
            'id_tiket' => $tiket->id,
            'kategori' => 'Hasil Jointing',
        ]);

        // Tiket status should transition to PROSES
        $this->assertEquals('PROSES', $tiket->fresh()->status);
    }

    public function test_upload_multiple_photos_successfully(): void
    {
        $user = User::factory()->create(['role' => 'teknis']);
        $tiket = Tiket::create([
            'no_tiket' => 'BDG-20260921-002',
            'status_link_impact' => 'Down',
            'backbone_segment' => 'SW BBLU - SW Reog',
            'tanggal_open' => now(),
            'status' => 'OPEN',
            'created_by' => $user->id,
        ]);

        $files = [
            UploadedFile::fake()->image('closure1.jpg'),
            UploadedFile::fake()->image('closure2.jpg'),
        ];

        $data = [
            'kategori' => 'Closure Terpasang',
        ];

        $docs = $this->service->upload($tiket, $data, $files);

        $this->assertCount(2, $docs);
        $this->assertEquals(2, $tiket->dokumentasis()->count());
    }

    public function test_delete_dokumentasi(): void
    {
        $user = User::factory()->create(['role' => 'teknis']);
        $tiket = Tiket::create([
            'no_tiket' => 'BDG-20260921-003',
            'status_link_impact' => 'Down',
            'backbone_segment' => 'SW BBLU - SW Reog',
            'tanggal_open' => now(),
            'status' => 'OPEN',
            'created_by' => $user->id,
        ]);

        $file = UploadedFile::fake()->image('sample.jpg');
        $docs = $this->service->upload($tiket, ['kategori' => 'Kondisi Lokasi'], $file);
        $doc = $docs->first();

        $deleted = $this->service->delete($doc);
        $this->assertTrue($deleted);
        $this->assertDatabaseMissing('dokumentasi', ['id' => $doc->id]);
    }
}
