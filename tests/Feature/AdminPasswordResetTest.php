<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class AdminPasswordResetTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_forgot_password_screen_can_be_rendered(): void
    {
        $this->get('/admin/forgot-password')
            ->assertOk()
            ->assertSee('Forgot your password?');
    }

    public function test_the_login_page_links_to_the_forgot_password_screen(): void
    {
        $this->get('/admin/login')
            ->assertOk()
            ->assertSee(route('admin.password.request'));
    }

    public function test_a_reset_link_is_emailed_to_an_administrator(): void
    {
        Notification::fake();

        $admin = User::factory()->create(['role' => 'admin']);

        $this->post('/admin/forgot-password', ['email' => $admin->email])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        Notification::assertSentTo($admin, ResetPassword::class);
    }

    public function test_a_reset_link_is_not_emailed_to_a_non_admin_user(): void
    {
        Notification::fake();

        $user = User::factory()->create(['role' => 'user']);

        $this->post('/admin/forgot-password', ['email' => $user->email])
            ->assertSessionHasNoErrors();

        Notification::assertNothingSent();
    }

    public function test_reset_password_screen_can_be_rendered(): void
    {
        Notification::fake();

        $admin = User::factory()->create(['role' => 'admin']);

        $this->post('/admin/forgot-password', ['email' => $admin->email]);

        Notification::assertSentTo($admin, ResetPassword::class, function (ResetPassword $notification) {
            $this->get('/admin/reset-password/'.$notification->token)
                ->assertOk()
                ->assertSee('Choose a new password');

            return true;
        });
    }

    public function test_an_administrator_can_reset_their_password_with_a_valid_token(): void
    {
        Notification::fake();

        $admin = User::factory()->create(['role' => 'admin']);

        $this->post('/admin/forgot-password', ['email' => $admin->email]);

        Notification::assertSentTo($admin, ResetPassword::class, function (ResetPassword $notification) use ($admin) {
            $response = $this->post('/admin/reset-password', [
                'token' => $notification->token,
                'email' => $admin->email,
                'password' => 'new-secret-password',
                'password_confirmation' => 'new-secret-password',
            ]);

            $response->assertSessionHasNoErrors()->assertRedirect('/admin/login');

            $this->assertTrue(Hash::check('new-secret-password', $admin->fresh()->password));

            return true;
        });
    }

    public function test_the_reset_form_rejects_an_invalid_token(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'password' => Hash::make('original-password'),
        ]);

        $this->post('/admin/reset-password', [
            'token' => 'not-a-real-token',
            'email' => $admin->email,
            'password' => 'new-secret-password',
            'password_confirmation' => 'new-secret-password',
        ])->assertSessionHasErrors('email');

        $this->assertTrue(Hash::check('original-password', $admin->fresh()->password));
    }

    public function test_password_reset_requires_a_confirmed_password(): void
    {
        Notification::fake();

        $admin = User::factory()->create(['role' => 'admin']);
        $this->post('/admin/forgot-password', ['email' => $admin->email]);

        Notification::assertSentTo($admin, ResetPassword::class, function (ResetPassword $notification) use ($admin) {
            $this->post('/admin/reset-password', [
                'token' => $notification->token,
                'email' => $admin->email,
                'password' => 'new-secret-password',
                'password_confirmation' => 'does-not-match',
            ])->assertSessionHasErrors('password');

            return true;
        });
    }

    public function test_a_non_admin_cannot_reset_a_password_through_the_admin_broker(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
            'password' => Hash::make('original-password'),
        ]);

        $token = Password::broker()->createToken($user);

        $this->post('/admin/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-secret-password',
            'password_confirmation' => 'new-secret-password',
        ])->assertSessionHasErrors('email');

        $this->assertTrue(Hash::check('original-password', $user->fresh()->password));
    }
}
