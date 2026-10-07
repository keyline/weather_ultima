<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use App\Models\User;
use App\Services\FooterMenuService;
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
            ->assertSee('shown@weather.test')
            ->assertSee('Footer menu order')
            ->assertSee('https://keylines.in/dev/weather/blog/');
    }

    public function test_public_footer_includes_the_blog_link(): void
    {
        Storage::fake('local');

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('href="https://keylines.in/dev/weather/blog/" class="wx-footer-nav-link">Blog</a>', false);
    }

    public function test_admin_can_change_footer_link_order_without_changing_site_settings(): void
    {
        Storage::fake('local');
        $settings = SiteSetting::query()->create($this->validPayload(['site_name' => 'Keep this setting']));
        $settingsBefore = $settings->fresh()->getAttributes();
        ksort($settingsBefore);

        $this->actingAs($this->admin())
            ->put(route('admin.settings.site.footer-menu.update'), [
                'menu_order' => [
                    'blog' => 1,
                    'home' => 2,
                    'about' => 3,
                    'products' => 4,
                    'services' => 5,
                    'contact' => 6,
                ],
            ])
            ->assertRedirect(route('admin.settings.site.edit'))
            ->assertSessionHas('status', 'Footer menu order saved.');

        Storage::disk('local')->assertExists('site-settings/footer-menu-order.json');
        $settingsAfter = $settings->fresh()->getAttributes();
        ksort($settingsAfter);
        $this->assertSame($settingsBefore, $settingsAfter);
        $this->assertSame(
            ['blog', 'home', 'about', 'products', 'services', 'contact'],
            array_column(app(FooterMenuService::class)->items(), 'key'),
        );

        $html = $this->get(route('home'))->assertOk()->getContent();
        $footerStart = strpos($html, '<footer class="wx-footer-new">');
        $this->assertNotFalse($footerStart);
        $footer = substr($html, $footerStart);
        $blogPosition = strpos($footer, 'href="https://keylines.in/dev/weather/blog/"');
        $homePosition = strpos($footer, 'href="'.route('home').'" class="wx-footer-nav-link"');

        $this->assertNotFalse($blogPosition);
        $this->assertNotFalse($homePosition);
        $this->assertLessThan($homePosition, $blogPosition);
    }

    public function test_footer_link_positions_must_be_unique(): void
    {
        Storage::fake('local');

        $this->actingAs($this->admin())
            ->put(route('admin.settings.site.footer-menu.update'), [
                'menu_order' => [
                    'home' => 1,
                    'about' => 2,
                    'products' => 3,
                    'services' => 4,
                    'contact' => 5,
                    'blog' => 5,
                ],
            ])
            ->assertSessionHasErrors();

        Storage::disk('local')->assertMissing('site-settings/footer-menu-order.json');
    }

    public function test_non_admin_cannot_change_footer_menu_order(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)
            ->put(route('admin.settings.site.footer-menu.update'), [])
            ->assertRedirect(route('admin.login'));
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

    public function test_admin_can_set_a_whatsapp_number_and_the_public_button_links_to_it(): void
    {
        $this->actingAs($this->admin())
            ->put(route('admin.settings.site.update'), $this->validPayload([
                'whatsapp_number' => '+91 89102 96427',
            ]))
            ->assertRedirect();

        $this->assertSame('https://wa.me/918910296427', SiteSetting::current()->whatsapp_link);

        $this->get(route('home'))->assertOk()->assertSee('https://wa.me/918910296427', false);
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
