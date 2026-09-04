<?php

namespace Tests\Feature;

use App\Mail\MailChannelConfigurator;
use App\Models\BrevoSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class BrevoSettingsTest extends TestCase
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
            'api_key' => 'xkeysib-abcdef123456',
            'sender_name' => 'Weather Ultima',
            'sender_email' => 'no-reply@weather.test',
            'reply_to_email' => 'hello@weather.test',
            'is_active' => '1',
        ], $overrides);
    }

    public function test_non_admins_cannot_access_brevo_settings(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)->get(route('admin.settings.brevo.edit'))->assertRedirect(route('admin.login'));
        $this->actingAs($user)->put(route('admin.settings.brevo.update'), $this->payload())->assertRedirect(route('admin.login'));
        $this->actingAs($user)->post(route('admin.settings.brevo.test'), ['test_email' => 'a@b.test'])->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_save_the_configuration(): void
    {
        $this->actingAs($this->admin())
            ->put(route('admin.settings.brevo.update'), $this->payload())
            ->assertRedirect()
            ->assertSessionHas('status');

        $settings = BrevoSetting::current();
        $this->assertSame('xkeysib-abcdef123456', $settings->api_key);
        $this->assertSame('no-reply@weather.test', $settings->sender_email);
        $this->assertSame('hello@weather.test', $settings->reply_to_email);
        $this->assertTrue($settings->is_active);
    }

    public function test_api_key_is_encrypted_masked_and_never_rendered(): void
    {
        $this->actingAs($this->admin())->put(route('admin.settings.brevo.update'), $this->payload());

        $raw = DB::table('brevo_settings')->value('api_key');
        $this->assertNotSame('xkeysib-abcdef123456', $raw);

        $this->assertSame('3456', substr((string) BrevoSetting::current()->maskedApiKey(), -4));
        $this->assertStringNotContainsString('abcdef', (string) BrevoSetting::current()->maskedApiKey());

        $this->actingAs($this->admin())
            ->get(route('admin.settings.brevo.edit'))
            ->assertOk()
            ->assertDontSee('xkeysib-abcdef123456');
    }

    public function test_blank_api_key_keeps_the_stored_one(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin)->put(route('admin.settings.brevo.update'), $this->payload());

        $this->actingAs($admin)->put(route('admin.settings.brevo.update'), $this->payload([
            'api_key' => '',
            'sender_name' => 'Changed Name',
        ]));

        $settings = BrevoSetting::current();
        $this->assertSame('Changed Name', $settings->sender_name);
        $this->assertSame('xkeysib-abcdef123456', $settings->api_key);
    }

    public function test_validation_rejects_bad_input(): void
    {
        $this->actingAs($this->admin())
            ->put(route('admin.settings.brevo.update'), [
                'sender_name' => '',
                'sender_email' => 'nope',
                'reply_to_email' => 'also-nope',
            ])
            ->assertSessionHasErrors(['sender_name', 'sender_email', 'reply_to_email']);
    }

    public function test_active_configuration_overrides_mail_config(): void
    {
        BrevoSetting::current()->update([
            'api_key' => 'xkeysib-live',
            'sender_email' => 'brevo@weather.test',
            'sender_name' => 'Brevo Sender',
            'is_active' => true,
        ]);

        MailChannelConfigurator::configure();

        $this->assertSame('brevo', config('mail.default'));
        $this->assertSame('xkeysib-live', config('mail.mailers.brevo.key'));
        $this->assertSame('brevo@weather.test', config('mail.from.address'));
    }

    public function test_test_email_requires_a_saved_key(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.settings.brevo.test'), ['test_email' => 'inbox@weather.test'])
            ->assertRedirect()
            ->assertSessionHas('brevo_test_error');
    }

    public function test_admin_can_send_a_test_email_once_configured(): void
    {
        Mail::fake();
        BrevoSetting::current()->update(['api_key' => 'xkeysib-live', 'is_active' => true]);

        $this->actingAs($this->admin())
            ->post(route('admin.settings.brevo.test'), ['test_email' => 'inbox@weather.test'])
            ->assertRedirect()
            ->assertSessionHas('status')
            ->assertSessionMissing('brevo_test_error');
    }
}
