<?php

namespace Tests\Feature;

use App\Models\RecaptchaSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RecaptchaSettingsTest extends TestCase
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
            'site_key' => '6Lc-site-key',
            'secret_key' => '6Lc-secret-key-value',
            'version' => 'v2',
            'minimum_score' => '0.5',
            'is_active' => '1',
        ], $overrides);
    }

    public function test_non_admins_cannot_access_recaptcha_settings(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)->get(route('admin.settings.recaptcha.edit'))->assertRedirect(route('admin.login'));
        $this->actingAs($user)->put(route('admin.settings.recaptcha.update'), $this->payload())->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_save_the_configuration(): void
    {
        $this->actingAs($this->admin())
            ->put(route('admin.settings.recaptcha.update'), $this->payload(['version' => 'v3', 'minimum_score' => '0.7']))
            ->assertRedirect()
            ->assertSessionHas('status');

        $settings = RecaptchaSetting::current();
        $this->assertSame('6Lc-site-key', $settings->site_key);
        $this->assertSame('6Lc-secret-key-value', $settings->secret_key);
        $this->assertSame('v3', $settings->version);
        $this->assertSame(0.7, $settings->minimum_score);
        $this->assertTrue($settings->is_active);
    }

    public function test_secret_key_is_encrypted_masked_and_never_rendered(): void
    {
        $this->actingAs($this->admin())->put(route('admin.settings.recaptcha.update'), $this->payload());

        $raw = DB::table('recaptcha_settings')->value('secret_key');
        $this->assertNotSame('6Lc-secret-key-value', $raw);

        $this->assertStringNotContainsString('secret-key-value', (string) RecaptchaSetting::current()->maskedSecretKey());

        $this->actingAs($this->admin())
            ->get(route('admin.settings.recaptcha.edit'))
            ->assertOk()
            ->assertDontSee('6Lc-secret-key-value');
    }

    public function test_blank_secret_key_keeps_the_stored_one(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin)->put(route('admin.settings.recaptcha.update'), $this->payload());

        $this->actingAs($admin)->put(route('admin.settings.recaptcha.update'), $this->payload([
            'secret_key' => '',
            'site_key' => '6Lc-new-site',
        ]));

        $settings = RecaptchaSetting::current();
        $this->assertSame('6Lc-new-site', $settings->site_key);
        $this->assertSame('6Lc-secret-key-value', $settings->secret_key);
    }

    public function test_validation_rejects_bad_input(): void
    {
        $this->actingAs($this->admin())
            ->put(route('admin.settings.recaptcha.update'), $this->payload([
                'version' => 'v9',
                'minimum_score' => '3',
            ]))
            ->assertSessionHasErrors(['version', 'minimum_score']);
    }

    public function test_cannot_activate_without_both_keys(): void
    {
        $this->actingAs($this->admin())
            ->put(route('admin.settings.recaptcha.update'), $this->payload([
                'site_key' => '',
                'secret_key' => '',
            ]))
            ->assertSessionHasErrors('is_active');

        $this->assertFalse(RecaptchaSetting::current()->is_active);
    }
}
