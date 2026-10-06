<?php

namespace Tests\Feature;

use App\Models\HomeSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HomeStatsVideoTest extends TestCase
{
    use LazilyRefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    public function test_admin_can_upload_mp4_without_changing_homepage_database_content(): void
    {
        Storage::fake('public');
        HomeSetting::query()->create(['banner_title' => 'Keep this heading']);
        $homeSettingsBefore = HomeSetting::query()->get()->toArray();
        Storage::disk('public')->put('home/stats-video.mp4', 'old clip');

        $this->actingAs($this->admin())
            ->put(route('admin.home.stats-video.update'), [
                'video' => UploadedFile::fake()->create('homepage.mp4', 1024, 'video/mp4'),
            ])
            ->assertRedirect(route('admin.home.stats-video.edit'))
            ->assertSessionHas('status');

        Storage::disk('public')->assertExists('home/stats-video.mp4');
        $this->assertNotSame('old clip', Storage::disk('public')->get('home/stats-video.mp4'));
        $this->assertSame($homeSettingsBefore, HomeSetting::query()->get()->toArray());
    }

    public function test_non_admin_cannot_upload_homepage_stats_video(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)
            ->put(route('admin.home.stats-video.update'), [
                'video' => UploadedFile::fake()->create('homepage.mp4', 1024, 'video/mp4'),
            ])
            ->assertRedirect(route('admin.login'));
    }

    public function test_upload_rejects_files_that_are_not_mp4(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin())
            ->put(route('admin.home.stats-video.update'), [
                'video' => UploadedFile::fake()->create('homepage.webm', 1024, 'video/webm'),
            ])
            ->assertSessionHasErrors([
                'video' => 'The video field must be a file of type: mp4.',
            ]);

        Storage::disk('public')->assertMissing('home/stats-video.mp4');
    }

    public function test_homepage_uses_the_uploaded_video_and_serves_it_as_mp4(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('home/stats-video.mp4', 'sample video');

        $this->get(route('home'))
            ->assertSee('storage/home/stats-video.mp4?v=', false)
            ->assertDontSee('material/images/product_img1.png', false)
            ->assertSee('autoplay', false)
            ->assertSee('muted', false)
            ->assertSee('loop', false);

        $this->get(route('storage.file', ['path' => 'home/stats-video.mp4']))
            ->assertOk()
            ->assertHeader('Content-Type', 'video/mp4');
    }

    public function test_homepage_shows_a_demo_image_until_an_admin_uploads_a_video(): void
    {
        Storage::fake('public');

        $this->get(route('home'))
            ->assertSee('material/images/product_img1.png', false)
            ->assertDontSee('drarindamrath.com/wp-content/themes/drrath/images/video.mp4', false)
            ->assertDontSee('<video', false);
    }

    public function test_admin_can_preview_the_uploaded_video_and_open_the_upload_form(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('home/stats-video.mp4', 'sample video');

        $this->actingAs($this->admin())
            ->get(route('admin.home.stats-video.edit'))
            ->assertSee('Current uploaded video')
            ->assertSee('storage/home/stats-video.mp4?v=', false)
            ->assertSee('Upload MP4 video');
    }
}
