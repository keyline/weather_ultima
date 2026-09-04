<?php

namespace Tests\Feature;

use App\Models\ContactEnquiry;
use App\Models\RecaptchaSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RecaptchaEnforcementTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    private function enableRecaptcha(string $version = 'v2'): void
    {
        RecaptchaSetting::current()->update([
            'site_key' => 'site-key',
            'secret_key' => 'secret-key',
            'version' => $version,
            'minimum_score' => 0.5,
            'is_active' => true,
        ]);
    }

    /**
     * @return array<string, string>
     */
    private function contactPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Jane Tester',
            'email' => 'jane@example.test',
            'subject' => 'Hello',
            'message' => 'A message long enough.',
        ], $overrides);
    }

    public function test_contact_form_works_without_a_token_when_recaptcha_is_off(): void
    {
        $this->post(route('contact.store'), $this->contactPayload())
            ->assertRedirect(route('contact'));

        $this->assertDatabaseCount('contact_enquiries', 1);
    }

    public function test_contact_form_is_rejected_when_token_is_missing(): void
    {
        $this->enableRecaptcha();

        $this->post(route('contact.store'), $this->contactPayload())
            ->assertSessionHasErrors('g-recaptcha-response');

        $this->assertDatabaseCount('contact_enquiries', 0);
    }

    public function test_contact_form_is_rejected_when_google_rejects_the_token(): void
    {
        $this->enableRecaptcha();
        Http::fake(['www.google.com/*' => Http::response(['success' => false])]);

        $this->post(route('contact.store'), $this->contactPayload(['g-recaptcha-response' => 'bad-token']))
            ->assertSessionHasErrors('g-recaptcha-response');

        $this->assertDatabaseCount('contact_enquiries', 0);
    }

    public function test_contact_form_passes_with_a_valid_token(): void
    {
        $this->enableRecaptcha();
        Http::fake(['www.google.com/*' => Http::response(['success' => true])]);

        $this->post(route('contact.store'), $this->contactPayload(['g-recaptcha-response' => 'good-token']))
            ->assertRedirect(route('contact'));

        $this->assertDatabaseCount('contact_enquiries', 1);
        $this->assertNull(ContactEnquiry::query()->first()->getAttribute('g-recaptcha-response'));
    }

    public function test_v3_low_score_is_rejected(): void
    {
        $this->enableRecaptcha('v3');
        Http::fake(['www.google.com/*' => Http::response(['success' => true, 'score' => 0.1, 'action' => 'contact'])]);

        $this->post(route('contact.store'), $this->contactPayload(['g-recaptcha-response' => 'low-score']))
            ->assertSessionHasErrors('g-recaptcha-response');
    }

    public function test_admin_login_is_blocked_when_recaptcha_fails(): void
    {
        $this->enableRecaptcha();
        Http::fake(['www.google.com/*' => Http::response(['success' => false])]);

        $admin = User::factory()->create(['role' => 'admin', 'password' => bcrypt('secret-password')]);

        $this->post(route('admin.login.store'), [
            'email' => $admin->email,
            'password' => 'secret-password',
            'g-recaptcha-response' => 'bad',
        ])->assertSessionHasErrors('g-recaptcha-response');

        $this->assertGuest();
    }

    public function test_admin_login_succeeds_with_a_valid_token(): void
    {
        $this->enableRecaptcha();
        Http::fake(['www.google.com/*' => Http::response(['success' => true])]);

        $admin = User::factory()->create(['role' => 'admin', 'password' => bcrypt('secret-password')]);

        $this->post(route('admin.login.store'), [
            'email' => $admin->email,
            'password' => 'secret-password',
            'g-recaptcha-response' => 'good',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($admin);
    }
}
