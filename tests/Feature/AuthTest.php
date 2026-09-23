<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UserSeeder::class);
    }
    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/');
        $response->assertRedirect('/login');

        $dashboardResponse = $this->get('/dashboard');
        $dashboardResponse->assertRedirect('/login');
    }

    /**
     * Test login screen can be rendered.
     */
    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Sistem Tiketing Gangguan Backbone');
        $response->assertSee('admin@connecti.id');
    }

    /**
     * Test user can authenticate with valid credentials.
     */
    public function test_user_can_authenticate_with_valid_credentials(): void
    {
        $user = User::where('email', 'admin@connecti.id')->first();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/dashboard');
    }

    /**
     * Test user cannot authenticate with invalid password.
     */
    public function test_user_cannot_authenticate_with_invalid_password(): void
    {
        $user = User::where('email', 'admin@connecti.id')->first();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    /**
     * Test dashboard renders for all roles with customized data.
     */
    public function test_dashboard_renders_for_each_role(): void
    {
        $roles = ['admin', 'helpdesk', 'teknis', 'sa_cs'];

        foreach ($roles as $role) {
            $user = User::where('role', $role)->first();
            $this->assertNotNull($user, "User for role {$role} must exist.");

            $response = $this->actingAs($user)->get('/dashboard');
            $response->assertStatus(200);
            $response->assertSee($user->name);
            $response->assertSee($user->role_label);
        }
    }

    /**
     * Test profile can be rendered and updated.
     */
    public function test_profile_can_be_viewed_and_updated(): void
    {
        $user = User::where('email', 'teknis@connecti.id')->first();

        $viewResponse = $this->actingAs($user)->get('/profile');
        $viewResponse->assertStatus(200);
        $viewResponse->assertSee($user->name);

        $updateResponse = $this->actingAs($user)->put('/profile', [
            'name' => 'Rian Suryana Updated',
            'email' => $user->email,
            'phone' => '081299998888',
        ]);

        $updateResponse->assertSessionHas('success');
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Rian Suryana Updated',
            'phone' => '081299998888',
        ]);
    }

    /**
     * Test logout functionality.
     */
    public function test_user_can_logout(): void
    {
        $user = User::where('email', 'admin@connecti.id')->first();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/login');
    }
}
