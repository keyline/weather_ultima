<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SiteSettingsManagementTest extends TestCase
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
    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'site_name' => 'Weather Ultima',
            'contact_email' => 'hello@weather.test',
            'contact_phone' => '+91 90000 00000',
            'contact_address' => 'Kolkata, West Bengal, India',
        ], $overrides);
    }

    public function test_non_admins_cannot_open_or_save_site_settings(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)->get(route('admin.settings.site.edit'))->assertRedirect(route('admin.login'));
        $this->actingAs($user)->put(route('admin.settings.site.update'), $this->validPayload())->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_view_the_site_settings_form(): void
    {
        SiteSetting::query()->create($this->validPayload(['contact_email' => 'shown@weather.test']));

        $this->actingAs($this->admin())
            ->get(route('admin.settings.site.edit'))
            ->assertOk()
            ->assertSee('shown@weather.test');
    }

    public function test_admin_can_update_general_contact_and_social_details(): void
    {
        $this->actingAs($this->admin())
            ->put(route('admin.settings.site.update'), $this->validPayload([
                'site_name' => 'Weather Ultima Group',
                'contact_email' => 'support@weather.test',
                'social_facebook' => 'https://facebook.com/weatherultima',
            ]))
            ->assertRedirect()
            ->assertSessionHas('status');

        $settings = SiteSetting::current();
        $this->assertSame('Weather Ultima Group', $settings->site_name);
        $this->assertSame('support@weather.test', $settings->contact_email);
        $this->assertCount(1, $settings->social_links);
    }

    public function test_site_name_is_required_and_urls_are_validated(): void
    {
        $this->actingAs($this->admin())
            ->put(route('admin.settings.site.update'), $this->validPayload([
                'site_name' => '',
                'social_facebook' => 'javascript:alert(1)',
            ]))
            ->assertSessionHasErrors(['site_name', 'social_facebook']);
    }

    public function test_admin_can_upload_logos_and_a_favicon(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin())
            ->put(route('admin.settings.site.update'), $this->validPayload([
                'header_logo' => UploadedFile::fake()->image('header.png', 320, 90),
                'footer_logo' => UploadedFile::fake()->image('footer.png', 320, 90),
                'favicon' => UploadedFile::fake()->image('favicon.png', 64, 64),
            ]))
            ->assertRedirect();

        $settings = SiteSetting::current();
        Storage::disk('public')->assertExists($settings->header_logo_path);
        Storage::disk('public')->assertExists($settings->footer_logo_path);
        Storage::disk('public')->assertExists($settings->favicon_path);
    }

    public function test_uploading_a_new_logo_removes_the_previous_file(): void
    {
        Storage::fake('public');
        $admin = $this->admin();

        $this->actingAs($admin)->put(route('admin.settings.site.update'), $this->validPayload([
            'header_logo' => UploadedFile::fake()->image('first.png'),
        ]));
        $first = SiteSetting::current()->header_logo_path;

        $this->actingAs($admin)->put(route('admin.settings.site.update'), $this->validPayload([
            'header_logo' => UploadedFile::fake()->image('second.png'),
        ]));

        Storage::disk('public')->assertMissing($first);
    }

    public function test_public_pages_render_the_configured_details(): void
    {
        SiteSetting::query()->create($this->validPayload([
            'site_name' => 'Weather Ultima',
            'contact_email' => 'frontdesk@weather.test',
            'contact_phone' => '+91 55555 44444',
            'social_instagram' => 'https://instagram.com/weatherultima',
        ]));

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('frontdesk@weather.test')
            ->assertSee('https://instagram.com/weatherultima', false);

        $this->get(route('contact'))
            ->assertOk()
            ->assertSee('+91 55555 44444');
    }
}
