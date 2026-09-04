<?php

namespace Tests\Feature;

use App\Mail\MailChannelConfigurator;
use App\Models\SmtpSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SmtpSettingsTest extends TestCase
{
    use LazilyRefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function payload(array $overrides = []): array
    {
        return array_merge([
            'host' => 'smtp.mailgun.org',
            'port' => '587',
            'username' => 'postmaster@weather.test',
            'password' => 'super-secret',
            'encryption' => 'tls',
            'from_address' => 'no-reply@weather.test',
            'from_name' => 'Weather Ultima',
            'is_active' => '1',
        ], $overrides);
    }

    public function test_non_admins_cannot_access_smtp_settings(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)->get(route('admin.settings.smtp.edit'))->assertRedirect(route('admin.login'));
        $this->actingAs($user)->put(route('admin.settings.smtp.update'), $this->payload())->assertRedirect(route('admin.login'));
        $this->actingAs($user)->post(route('admin.settings.smtp.test'), ['test_email' => 'a@b.test'])->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_save_the_configuration(): void
    {
        $this->actingAs($this->admin())
            ->put(route('admin.settings.smtp.update'), $this->payload())
            ->assertRedirect()
            ->assertSessionHas('status');

        $settings = SmtpSetting::current();
        $this->assertSame('smtp.mailgun.org', $settings->host);
        $this->assertSame(587, $settings->port);
        $this->assertSame('tls', $settings->encryption);
        $this->assertSame('super-secret', $settings->password);
        $this->assertTrue($settings->is_active);
    }

    public function test_password_is_stored_encrypted_and_never_rendered(): void
    {
        $this->actingAs($this->admin())->put(route('admin.settings.smtp.update'), $this->payload());

        $raw = DB::table('smtp_settings')->value('password');
        $this->assertNotSame('super-secret', $raw);
        $this->assertNotEmpty($raw);
        $this->assertSame('super-secret', SmtpSetting::current()->password);

        $this->actingAs($this->admin())
            ->get(route('admin.settings.smtp.edit'))
            ->assertOk()
            ->assertDontSee('super-secret');
    }

    public function test_blank_password_keeps_the_stored_one(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin)->put(route('admin.settings.smtp.update'), $this->payload());

        $this->actingAs($admin)->put(route('admin.settings.smtp.update'), $this->payload([
            'password' => '',
            'host' => 'smtp.newhost.test',
        ]));

        $settings = SmtpSetting::current();
        $this->assertSame('smtp.newhost.test', $settings->host);
        $this->assertSame('super-secret', $settings->password);
    }

    public function test_validation_rejects_bad_input(): void
    {
        $this->actingAs($this->admin())
            ->put(route('admin.settings.smtp.update'), [
                'host' => '',
                'port' => 'not-a-number',
                'encryption' => 'weird',
                'from_address' => 'nope',
                'from_name' => '',
            ])
            ->assertSessionHasErrors(['host', 'port', 'encryption', 'from_address', 'from_name']);
    }

    public function test_empty_encryption_is_saved_as_none(): void
    {
        $this->actingAs($this->admin())
            ->put(route('admin.settings.smtp.update'), $this->payload(['encryption' => '']))
            ->assertSessionHasNoErrors();

        $this->assertNull(SmtpSetting::current()->encryption);
    }

    public function test_active_configuration_overrides_mail_config(): void
    {
        SmtpSetting::current()->update($this->arrayWithoutStringFlags($this->payload()));

        MailChannelConfigurator::configure();

        $this->assertSame('smtp', config('mail.default'));
        $this->assertSame('smtp.mailgun.org', config('mail.mailers.smtp.host'));
        $this->assertSame(587, config('mail.mailers.smtp.port'));
        $this->assertSame('no-reply@weather.test', config('mail.from.address'));
    }

    public function test_inactive_configuration_leaves_mail_config_untouched(): void
    {
        config(['mail.default' => 'log']);
        SmtpSetting::current()->update($this->arrayWithoutStringFlags($this->payload(['is_active' => false])));

        MailChannelConfigurator::configure();

        $this->assertSame('log', config('mail.default'));
    }

    public function test_admin_can_send_a_test_email(): void
    {
        Mail::fake();

        $this->actingAs($this->admin())
            ->post(route('admin.settings.smtp.test'), ['test_email' => 'inbox@weather.test'])
            ->assertRedirect()
            ->assertSessionHas('status')
            ->assertSessionMissing('smtp_test_error');
    }

    public function test_test_email_requires_a_valid_recipient(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.settings.smtp.test'), ['test_email' => 'not-an-email'])
            ->assertSessionHasErrors('test_email');
    }

    /**
     * The controller payload uses string "1"/"" flags; the model wants real types.
     *
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function arrayWithoutStringFlags(array $payload): array
    {
        $payload['is_active'] = (bool) $payload['is_active'];
        $payload['port'] = (int) $payload['port'];
        if ($payload['encryption'] === '') {
            $payload['encryption'] = null;
        }

        return $payload;
    }
}
