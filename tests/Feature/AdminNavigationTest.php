<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class AdminNavigationTest extends TestCase
{
    use LazilyRefreshDatabase;

    /**
     * Assert that exactly one sidebar leaf link carries the active treatment, and it is the expected one.
     */
    private function assertActiveNavItem(string $html, string $label): void
    {
        // Match data-nav="X" ... class="..." within the same opening tag.
        preg_match_all('/data-nav="([^"]+)"[^>]*class="([^"]*)"/', $html, $all, PREG_SET_ORDER);

        $active = collect($all)
            ->filter(fn (array $m): bool => str_contains($m[2], 'nav-link--active'))
            ->pluck(1)
            ->values()
            ->all();

        $this->assertSame([$label], $active, "Expected only \"{$label}\" to be the active sidebar item.");
    }

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    public function test_dashboard_is_active_on_the_dashboard(): void
    {
        $response = $this->actingAs($this->admin())->get(route('admin.dashboard'));

        $response->assertOk();
        $this->assertActiveNavItem($response->getContent(), 'Dashboard');
    }

    public function test_home_children_are_active_and_the_group_opens(): void
    {
        $admin = $this->admin();

        $cases = [
            ['route' => 'admin.home.logo.index', 'label' => 'Brand Logo'],
            ['route' => 'admin.home.banner.edit', 'label' => 'Top Banner'],
            ['route' => 'admin.home.founder.edit', 'label' => 'About Founder'],
            ['route' => 'admin.home.core-values.index', 'label' => 'Core Values'],
            ['route' => 'admin.home.core-values.create', 'label' => 'Core Values'],
            ['route' => 'admin.home.cards.create', 'label' => 'Top Banner'],
        ];

        foreach ($cases as $case) {
            $response = $this->actingAs($admin)->get(route($case['route']));
            $response->assertOk();
            $this->assertActiveNavItem($response->getContent(), $case['label']);
            $this->assertMatchesRegularExpression('/<details[^>]*data-nav-group="Home"[^>]*\bopen\b/', $response->getContent());
        }
    }

    public function test_products_is_active_on_the_product_list_and_create(): void
    {
        $admin = $this->admin();

        $index = $this->actingAs($admin)->get(route('admin.products.index'));
        $index->assertOk();
        $this->assertActiveNavItem($index->getContent(), 'Products');
        $this->assertStringContainsString('data-nav-group="Products"', $index->getContent());

        $create = $this->actingAs($admin)->get(route('admin.products.create'));
        $create->assertOk();
        $this->assertActiveNavItem($create->getContent(), 'Products');
    }

    public function test_product_enquiries_is_active_and_its_group_is_open(): void
    {
        $response = $this->actingAs($this->admin())->get(route('admin.product-enquiries.index'));

        $response->assertOk();
        $this->assertActiveNavItem($response->getContent(), 'Product Enquiries');
        $this->assertMatchesRegularExpression('/<details[^>]*data-nav-group="Products"[^>]*\bopen\b/', $response->getContent());
    }

    public function test_contact_enquiries_is_active_on_its_page(): void
    {
        $response = $this->actingAs($this->admin())->get(route('admin.contact-enquiries.index'));

        $response->assertOk();
        $this->assertActiveNavItem($response->getContent(), 'Contact Enquiries');
    }

    public function test_testimonials_is_active_on_list_and_create(): void
    {
        $admin = $this->admin();

        $index = $this->actingAs($admin)->get(route('admin.testimonials.index'));
        $index->assertOk();
        $this->assertActiveNavItem($index->getContent(), 'Testimonials');

        $create = $this->actingAs($admin)->get(route('admin.testimonials.create'));
        $create->assertOk();
        $this->assertActiveNavItem($create->getContent(), 'Testimonials');
    }

    public function test_services_is_active_on_list_and_create(): void
    {
        $admin = $this->admin();

        $index = $this->actingAs($admin)->get(route('admin.services.index'));
        $index->assertOk();
        $this->assertActiveNavItem($index->getContent(), 'Services');

        $create = $this->actingAs($admin)->get(route('admin.services.create'));
        $create->assertOk();
        $this->assertActiveNavItem($create->getContent(), 'Services');
    }

    public function test_settings_children_are_active_and_the_group_opens(): void
    {
        $admin = $this->admin();

        $cases = [
            ['route' => 'admin.settings.index', 'label' => 'Overview'],
            ['route' => 'admin.settings.email.edit', 'label' => 'Email Settings'],
            ['route' => 'admin.settings.smtp.edit', 'label' => 'SMTP Settings'],
            ['route' => 'admin.settings.brevo.edit', 'label' => 'Brevo'],
            ['route' => 'admin.settings.recaptcha.edit', 'label' => 'Google reCAPTCHA'],
            ['route' => 'admin.settings.site.edit', 'label' => 'Site Settings'],
        ];

        foreach ($cases as $case) {
            $response = $this->actingAs($admin)->get(route($case['route']));
            $response->assertOk();
            $this->assertActiveNavItem($response->getContent(), $case['label']);
            $this->assertMatchesRegularExpression('/<details[^>]*data-nav-group="Settings"[^>]*\bopen\b/', $response->getContent());
        }
    }

    public function test_removed_menus_are_absent(): void
    {
        $html = $this->actingAs($this->admin())->get(route('admin.dashboard'))->getContent();

        $this->assertStringNotContainsString('>Users', $html);
        $this->assertStringNotContainsString('>Reports', $html);
    }
}
