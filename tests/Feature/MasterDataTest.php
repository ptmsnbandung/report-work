<?php

namespace Tests\Feature;

use App\Models\MasterMaterial;
use App\Models\MasterSla;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MasterDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_master_segment_index(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        MasterSla::create([
            'backbone_segment' => 'SW Test - SW Test 2',
            'sla_target_minutes' => 300,
        ]);

        $response = $this->actingAs($admin)->get('/master/segments');
        $response->assertStatus(200);
        $response->assertSee('SW Test - SW Test 2');
    }

    public function test_admin_can_create_update_and_delete_segment(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        // Create
        $response = $this->actingAs($admin)->post('/master/segments', [
            'backbone_segment' => 'SW Dago - SW Lembang',
            'sla_target_minutes' => 360,
        ]);
        $response->assertRedirect('/master/segments');
        $this->assertDatabaseHas('master_sla', ['backbone_segment' => 'SW Dago - SW Lembang']);

        $segment = MasterSla::where('backbone_segment', 'SW Dago - SW Lembang')->first();

        // Update
        $response = $this->actingAs($admin)->put("/master/segments/{$segment->id}", [
            'backbone_segment' => 'SW Dago - SW Lembang Updated',
            'sla_target_minutes' => 240,
        ]);
        $response->assertRedirect('/master/segments');
        $this->assertDatabaseHas('master_sla', ['backbone_segment' => 'SW Dago - SW Lembang Updated']);

        // Delete
        $response = $this->actingAs($admin)->delete("/master/segments/{$segment->id}");
        $response->assertRedirect('/master/segments');
        $this->assertDatabaseMissing('master_sla', ['id' => $segment->id]);
    }

    public function test_admin_can_create_update_and_delete_material(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        // Create
        $response = $this->actingAs($admin)->post('/master/materials', [
            'kode_material' => 'MAT-TEST-01',
            'nama_material' => 'Kabel Test 12 Core',
            'satuan' => 'Meter',
            'stok_tersedia' => 500,
            'keterangan' => 'Kabel test FO',
            'is_active' => 1,
        ]);
        $response->assertRedirect('/master/materials');
        $this->assertDatabaseHas('master_materials', ['kode_material' => 'MAT-TEST-01']);

        $material = MasterMaterial::where('kode_material', 'MAT-TEST-01')->first();

        // Update
        $response = $this->actingAs($admin)->put("/master/materials/{$material->id}", [
            'kode_material' => 'MAT-TEST-01',
            'nama_material' => 'Kabel Test 24 Core Edit',
            'satuan' => 'Meter',
            'stok_tersedia' => 600,
            'is_active' => 1,
        ]);
        $response->assertRedirect('/master/materials');
        $this->assertDatabaseHas('master_materials', ['nama_material' => 'Kabel Test 24 Core Edit']);

        // Delete
        $response = $this->actingAs($admin)->delete("/master/materials/{$material->id}");
        $response->assertRedirect('/master/materials');
        $this->assertDatabaseMissing('master_materials', ['id' => $material->id]);
    }

    public function test_admin_can_create_update_and_delete_sla(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        // Create
        $response = $this->actingAs($admin)->post('/master/sla', [
            'backbone_segment' => 'SW Buah Batu - SW Kopo',
            'sla_target_minutes' => 180,
        ]);
        $response->assertRedirect('/master/sla');
        $this->assertDatabaseHas('master_sla', ['backbone_segment' => 'SW Buah Batu - SW Kopo']);

        $sla = MasterSla::where('backbone_segment', 'SW Buah Batu - SW Kopo')->first();

        // Update
        $response = $this->actingAs($admin)->put("/master/sla/{$sla->id}", [
            'backbone_segment' => 'SW Buah Batu - SW Kopo',
            'sla_target_minutes' => 240,
        ]);
        $response->assertRedirect('/master/sla');
        $this->assertDatabaseHas('master_sla', ['sla_target_minutes' => 240]);

        // Delete
        $response = $this->actingAs($admin)->delete("/master/sla/{$sla->id}");
        $response->assertRedirect('/master/sla');
        $this->assertDatabaseMissing('master_sla', ['id' => $sla->id]);
    }

    public function test_admin_can_manage_users(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        // Create User
        $response = $this->actingAs($admin)->post('/master/users', [
            'name' => 'Teknis Baru',
            'email' => 'teknisbaru@connecti.id',
            'role' => 'teknis',
            'phone' => '081987654321',
            'password' => 'secret123',
            'is_active' => 1,
        ]);
        $response->assertRedirect('/master/users');
        $this->assertDatabaseHas('users', ['email' => 'teknisbaru@connecti.id']);

        $newUser = User::where('email', 'teknisbaru@connecti.id')->first();

        // Update User
        $response = $this->actingAs($admin)->put("/master/users/{$newUser->id}", [
            'name' => 'Teknis Baru Updated',
            'email' => 'teknisbaru@connecti.id',
            'role' => 'helpdesk',
            'phone' => '081987654322',
            'is_active' => 1,
        ]);
        $response->assertRedirect('/master/users');
        $this->assertDatabaseHas('users', ['name' => 'Teknis Baru Updated', 'role' => 'helpdesk']);

        // Toggle Active
        $response = $this->actingAs($admin)->patch("/master/users/{$newUser->id}/toggle-active");
        $response->assertRedirect('/master/users');
        $this->assertEquals(false, $newUser->fresh()->is_active);

        // Reset Password
        $response = $this->actingAs($admin)->post("/master/users/{$newUser->id}/reset-password", [
            'new_password' => 'newpassword123',
        ]);
        $response->assertRedirect('/master/users');
        $this->assertTrue(Hash::check('newpassword123', $newUser->fresh()->password));

        // Delete User
        $response = $this->actingAs($admin)->delete("/master/users/{$newUser->id}");
        $response->assertRedirect('/master/users');
        $this->assertDatabaseMissing('users', ['id' => $newUser->id]);
    }

    public function test_non_admin_cannot_access_master_data(): void
    {
        $teknis = User::factory()->create(['role' => 'teknis', 'is_active' => true]);

        $response = $this->actingAs($teknis)->get('/master/segments');
        $response->assertStatus(403);

        $response = $this->actingAs($teknis)->get('/master/materials');
        $response->assertStatus(403);

        $response = $this->actingAs($teknis)->get('/master/sla');
        $response->assertStatus(403);

        $response = $this->actingAs($teknis)->get('/master/users');
        $response->assertStatus(403);
    }
}
