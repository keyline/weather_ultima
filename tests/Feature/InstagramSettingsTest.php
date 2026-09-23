<?php

namespace Tests\Feature;

use App\Models\InstagramPost;
use App\Models\InstagramSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class InstagramSettingsTest extends TestCase
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
            'access_token' => 'ig-access-token',
            'instagram_user_id' => '17841400000000000',
            'is_active' => '1',
        ], $overrides);
    }

    public function test_non_admins_cannot_access_instagram_settings(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)->get(route('admin.settings.instagram.edit'))->assertRedirect(route('admin.login'));
        $this->actingAs($user)->put(route('admin.settings.instagram.update'), $this->payload())->assertRedirect(route('admin.login'));
        $this->actingAs($user)->post(route('admin.settings.instagram.test'))->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_save_the_configuration(): void
    {
        $this->actingAs($this->admin())
            ->put(route('admin.settings.instagram.update'), $this->payload())
            ->assertRedirect()
            ->assertSessionHas('status');

        $settings = InstagramSetting::current();
        $this->assertSame('ig-access-token', $settings->access_token);
        $this->assertSame('17841400000000000', $settings->instagram_user_id);
        $this->assertTrue($settings->is_active);
        $this->assertTrue($settings->isConfigured());
    }

    public function test_token_is_encrypted_masked_and_never_rendered(): void
    {
        $this->actingAs($this->admin())->put(route('admin.settings.instagram.update'), $this->payload());

        $raw = DB::table('instagram_settings')->value('access_token');
        $this->assertNotSame('ig-access-token', $raw);

        $this->actingAs($this->admin())
            ->get(route('admin.settings.instagram.edit'))
            ->assertOk()
            ->assertDontSee('ig-access-token');
    }

    public function test_blank_token_keeps_the_stored_one(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin)->put(route('admin.settings.instagram.update'), $this->payload());

        $this->actingAs($admin)->put(route('admin.settings.instagram.update'), $this->payload([
            'access_token' => '',
            'instagram_user_id' => '17841499999999999',
        ]));

        $settings = InstagramSetting::current();
        $this->assertSame('ig-access-token', $settings->access_token);
        $this->assertSame('17841499999999999', $settings->instagram_user_id);
    }

    public function test_cannot_activate_without_credentials(): void
    {
        $this->actingAs($this->admin())
            ->put(route('admin.settings.instagram.update'), $this->payload([
                'access_token' => '',
                'instagram_user_id' => '',
            ]))
            ->assertSessionHasErrors('is_active');

        $this->assertFalse(InstagramSetting::current()->is_active);
    }

    public function test_test_connection_reports_the_account_it_can_see(): void
    {
        Http::fake([
            'graph.facebook.com/*' => Http::response(['username' => 'weatherultima', 'media_count' => 42]),
        ]);

        $this->actingAs($this->admin())->put(route('admin.settings.instagram.update'), $this->payload());

        $this->actingAs($this->admin())
            ->post(route('admin.settings.instagram.test'))
            ->assertRedirect()
            ->assertSessionHas('status', fn (string $message): bool => str_contains($message, 'weatherultima')
                && str_contains($message, '42'));

        $this->assertSame('weatherultima', InstagramSetting::current()->username);
    }

    public function test_test_connection_requires_saved_credentials(): void
    {
        Http::preventStrayRequests();

        $this->actingAs($this->admin())
            ->post(route('admin.settings.instagram.test'))
            ->assertRedirect()
            ->assertSessionHas('instagram_test_error');
    }

    public function test_test_connection_surfaces_api_errors(): void
    {
        Http::fake(['graph.facebook.com/*' => Http::response(['error' => ['message' => 'Invalid OAuth access token.']], 401)]);

        $this->actingAs($this->admin())->put(route('admin.settings.instagram.update'), $this->payload());

        $this->actingAs($this->admin())
            ->post(route('admin.settings.instagram.test'))
            ->assertRedirect()
            ->assertSessionHas('instagram_test_error', fn (string $message): bool => str_contains($message, 'Invalid OAuth access token.'));
    }

    public function test_homepage_shows_live_media_over_the_fallback_when_configured(): void
    {
        InstagramPost::factory()->create(['is_enabled' => true]);

        Http::fake([
            'graph.facebook.com/*/media*' => Http::response(['data' => [
                ['media_type' => 'IMAGE', 'media_url' => 'https://example.test/live1.jpg', 'permalink' => 'https://instagram.com/p/1'],
            ]]),
        ]);

        InstagramSetting::query()->create(['access_token' => 'token', 'instagram_user_id' => '123', 'is_active' => true]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('https://example.test/live1.jpg', false);
    }

    public function test_homepage_falls_back_to_manual_photos_when_not_configured(): void
    {
        Http::preventStrayRequests();
        $post = InstagramPost::factory()->create(['is_enabled' => true]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee($post->image_url, false);
    }

    public function test_embed_code_takes_priority_over_the_graph_api_and_fallback(): void
    {
        Http::preventStrayRequests();
        InstagramPost::factory()->create(['is_enabled' => true]);

        InstagramSetting::query()->create([
            'embed_code' => '<div class="fake-widget" data-testid="widget-marker"></div>',
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('data-testid="widget-marker"', false);
    }

    public function test_admin_can_save_an_embed_code(): void
    {
        $this->actingAs($this->admin())
            ->put(route('admin.settings.instagram.update'), ['embed_code' => '<script src="https://snapwidget.com/js/snapwidget.js"></script>'])
            ->assertRedirect()
            ->assertSessionHas('status');

        $this->assertSame(
            '<script src="https://snapwidget.com/js/snapwidget.js"></script>',
            InstagramSetting::current()->embed_code,
        );
    }
}
