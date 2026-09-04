<?php

namespace Tests\Feature;

use App\Models\ServicePageSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ServicePageManagementTest extends TestCase
{
    use LazilyRefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    public function test_non_admins_cannot_edit_the_services_page(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)->get(route('admin.services.index'))->assertRedirect(route('admin.login'));
        $this->actingAs($user)->put(route('admin.services.page.update'), [])->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_update_the_intro_block(): void
    {
        $this->actingAs($this->admin())
            ->put(route('admin.services.page.update'), [
                'banner_title' => 'Our Services',
                'intro_heading' => "One Line\nSecond Line",
                'intro_body' => "First paragraph.\n\nSecond paragraph.",
                'intro_statement' => 'We measure. We act.',
            ])
            ->assertRedirect()
            ->assertSessionHas('status');

        $page = ServicePageSetting::current();
        $this->assertSame('Our Services', $page->banner_title);
        $this->assertCount(2, $page->intro_paragraphs);
    }

    public function test_the_services_page_renders_the_dynamic_intro(): void
    {
        ServicePageSetting::query()->create([
            'banner_title' => 'What We Do',
            'intro_heading' => 'Dynamic Heading',
            'intro_body' => 'Only paragraph here.',
            'intro_statement' => 'The dynamic statement.',
        ]);

        $this->get(route('services'))
            ->assertOk()
            ->assertSee('What We Do')
            ->assertSee('Dynamic Heading')
            ->assertSee('Only paragraph here.')
            ->assertSee('The dynamic statement.');
    }
}
