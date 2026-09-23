<?php

namespace Tests\Unit;

use App\Models\Kronologis;
use App\Models\Tiket;
use App\Models\User;
use App\Services\KronologisService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use InvalidArgumentException;
use Tests\TestCase;

class KronologisServiceTest extends TestCase
{
    use RefreshDatabase;

    protected KronologisService $service;
    protected User $teknis;
    protected User $admin;
    protected Tiket $tiket;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new KronologisService();

        $this->teknis = User::factory()->create([
            'role' => 'teknis',
            'is_active' => true,
        ]);

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->tiket = Tiket::create([
            'no_tiket' => 'BDG-20260914-001',
            'status_link_impact' => 'SW BBLU - SW Reog Down',
            'backbone_segment' => 'SW BBLU - SW Reog',
            'tanggal_open' => Carbon::now(),
            'status' => 'OPEN',
            'sla_target_minutes' => 360,
            'created_by' => $this->admin->id,
        ]);
    }

    public function test_add_kronologis_successfully(): void
    {
        $data = [
            'kategori'  => 'OTDR',
            'informasi' => 'Hasil OTDR dari SW BBLU putus di KM 12+400',
            'latitude'  => -6.379199,
            'longitude' => 106.846552,
        ];

        $krono = $this->service->addKronologis($this->tiket, $data, $this->teknis);

        $this->assertDatabaseHas('kronologis', [
            'id'        => $krono->id,
            'id_tiket'  => $this->tiket->id,
            'user_id'   => $this->teknis->id,
            'kategori'  => 'OTDR',
            'informasi' => 'Hasil OTDR dari SW BBLU putus di KM 12+400',
            'latitude'  => -6.379199,
            'longitude' => 106.846552,
        ]);

        // Status tiket berubah dari OPEN menjadi PROSES otomatis
        $this->tiket->refresh();
        $this->assertEquals('PROSES', $this->tiket->status);
    }

    public function test_add_kronologis_with_uploaded_photo(): void
    {
        $fakePhoto = UploadedFile::fake()->image('hasil_otdr.jpg');

        $data = [
            'kategori'  => 'JOINTING',
            'informasi' => 'Proses penyambungan core 1-24 di closure JC1',
        ];

        $krono = $this->service->addKronologis($this->tiket, $data, $this->teknis, $fakePhoto);

        $this->assertNotNull($krono->foto_url);
        $this->assertStringContainsString('uploads/kronologis/', $krono->foto_url);
        $this->assertFileExists(public_path($krono->foto_url));

        // Clean up test file
        if (file_exists(public_path($krono->foto_url))) {
            @unlink(public_path($krono->foto_url));
        }
    }

    public function test_admin_can_delete_kronologis(): void
    {
        $krono = Kronologis::create([
            'id_tiket'  => $this->tiket->id,
            'timestamp' => Carbon::now(),
            'user_id'   => $this->teknis->id,
            'kategori'  => 'IZIN',
            'informasi' => 'Izin masuk POP',
        ]);

        $deleted = $this->service->deleteKronologis($krono, $this->admin);
        $this->assertTrue($deleted);
        $this->assertDatabaseMissing('kronologis', ['id' => $krono->id]);
    }

    public function test_non_admin_cannot_delete_kronologis(): void
    {
        $krono = Kronologis::create([
            'id_tiket'  => $this->tiket->id,
            'timestamp' => Carbon::now(),
            'user_id'   => $this->teknis->id,
            'kategori'  => 'IZIN',
            'informasi' => 'Izin masuk POP',
        ]);

        $this->expectException(InvalidArgumentException::class);
        $this->service->deleteKronologis($krono, $this->teknis);
    }
}
