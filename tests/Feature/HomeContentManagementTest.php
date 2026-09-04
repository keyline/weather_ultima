<?php

namespace Tests\Feature;

use App\Models\HomeSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HomeContentManagementTest extends TestCase
{
    use LazilyRefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    public function test_non_admins_cannot_reach_home_content_pages(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)->get(route('admin.home.banner.edit'))->assertRedirect(route('admin.login'));
        $this->actingAs($user)->put(route('admin.home.banner.update'), [])->assertRedirect(route('admin.login'));
        $this->actingAs($user)->get(route('admin.home.founder.edit'))->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_update_the_banner_heading(): void
    {
        $this->actingAs($this->admin())
            ->put(route('admin.home.banner.update'), [
                'banner_title' => 'Weather, Understood',
                'banner_subtitle' => 'Clear guidance for every decision.',
            ])
            ->assertRedirect()
            ->assertSessionHas('status');

        $home = HomeSetting::current();
        $this->assertSame('Weather, Understood', $home->banner_title);
        $this->assertSame('Clear guidance for every decision.', $home->banner_subtitle);
    }

    public function test_banner_title_is_required(): void
    {
        $this->actingAs($this->admin())
            ->put(route('admin.home.banner.update'), ['banner_title' => ''])
            ->assertSessionHasErrors('banner_title');
    }

    public function test_admin_can_update_the_founder_section(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin())
            ->put(route('admin.home.founder.update'), [
                'founder_name' => 'Dr. A. Sharma',
                'founder_designation' => 'Chief Meteorologist',
                'founder_intro' => 'A lifelong student of the sky.',
                'founder_description' => "First paragraph.\n\nSecond paragraph.",
                'founder_image' => UploadedFile::fake()->image('founder.png'),
            ])
            ->assertRedirect()
            ->assertSessionHas('status');

        $home = HomeSetting::current();
        $this->assertSame('Dr. A. Sharma', $home->founder_name);
        $this->assertCount(2, $home->founder_paragraphs);
        Storage::disk('public')->assertExists($home->founder_image_path);
    }

    public function test_founder_name_and_intro_are_required(): void
    {
        $this->actingAs($this->admin())
            ->put(route('admin.home.founder.update'), ['founder_name' => '', 'founder_intro' => ''])
            ->assertSessionHasErrors(['founder_name', 'founder_intro']);
    }
}
