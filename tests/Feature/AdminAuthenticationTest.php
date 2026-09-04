<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAuthenticationTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_unauthenticated_users_are_redirected_to_the_admin_login(): void
    {
        $this->get('/admin/dashboard')
            ->assertRedirect('/admin/login');
    }

    public function test_non_admin_users_cannot_access_the_admin_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)
            ->get('/admin/dashboard')
            ->assertRedirect('/admin/login');
    }

    public function test_login_requires_an_email_and_password(): void
    {
        $this->post('/admin/login', [])
            ->assertRedirect()
            ->assertSessionHasErrors([
                'email' => 'The email field is required.',
                'password' => 'The password field is required.',
            ]);
    }

    public function test_administrator_can_sign_in_and_access_the_dashboard(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('secret-password'),
            'role' => 'admin',
        ]);

        $this->post('/admin/login', [
            'email' => $admin->email,
            'password' => 'secret-password',
        ])->assertRedirect('/admin/dashboard');

        $this->assertAuthenticatedAs($admin);

        $this->get('/admin/dashboard')
            ->assertOk()
            ->assertSee('Dashboard overview');
    }

    public function test_non_admin_credentials_are_rejected(): void
    {
        $user = User::factory()->create([
            'email' => 'member@example.com',
            'password' => Hash::make('secret-password'),
            'role' => 'user',
        ]);

        $this->post('/admin/login', [
            'email' => $user->email,
            'password' => 'secret-password',
        ])->assertRedirect()
            ->assertSessionHasErrors(['email' => 'The provided credentials are incorrect.']);

        $this->assertGuest();
    }

    public function test_administrator_can_log_out(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->post('/admin/logout')
            ->assertRedirect('/admin/login')
            ->assertSessionHas('status', 'You have been logged out securely.');

        $this->assertGuest();
    }

    public function test_admin_seeder_creates_a_hashed_administrator_account(): void
    {
        $this->seed(AdminUserSeeder::class);

        $admin = User::query()->where('email', 'info@weather.com')->firstOrFail();

        $this->assertSame('admin', $admin->role);
        $this->assertTrue(Hash::check('weather@123', $admin->password));
        $this->assertNotSame('weather@123', $admin->password);
    }
}
