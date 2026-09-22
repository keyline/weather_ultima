<?php

namespace Tests\Feature;

use App\Models\InstagramPost;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class InstagramPostManagementTest extends TestCase
{
    use LazilyRefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    public function test_non_admins_cannot_manage_instagram_posts(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)->get(route('admin.home.instagram.index'))->assertRedirect(route('admin.login'));
        $this->actingAs($user)->post(route('admin.home.instagram.store'), [])->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_create_a_photo_with_an_image(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin())
            ->post(route('admin.home.instagram.store'), [
                'image' => UploadedFile::fake()->image('post.png'),
                'link_url' => 'https://www.instagram.com/p/example',
                'display_order' => 2,
                'is_enabled' => '1',
            ])
            ->assertRedirect(route('admin.home.instagram.index'));

        $post = InstagramPost::firstWhere('link_url', 'https://www.instagram.com/p/example');
        $this->assertNotNull($post);
        Storage::disk('public')->assertExists($post->image);
    }

    public function test_image_is_required_on_create(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.home.instagram.store'), ['link_url' => 'https://www.instagram.com/p/example'])
            ->assertSessionHasErrors('image');
    }

    public function test_replacing_and_deleting_removes_the_stored_file(): void
    {
        Storage::fake('public');
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.home.instagram.store'), [
            'image' => UploadedFile::fake()->image('old.png'), 'link_url' => 'https://www.instagram.com/p/swap',
        ]);
        $post = InstagramPost::firstWhere('link_url', 'https://www.instagram.com/p/swap');
        $old = $post->image;

        $this->actingAs($admin)->put(route('admin.home.instagram.update', $post), [
            'image' => UploadedFile::fake()->image('new.png'), 'link_url' => 'https://www.instagram.com/p/swap',
        ]);
        $post->refresh();
        Storage::disk('public')->assertMissing($old);
        Storage::disk('public')->assertExists($post->image);

        $current = $post->image;
        $this->actingAs($admin)->delete(route('admin.home.instagram.destroy', $post))->assertRedirect(route('admin.home.instagram.index'));
        $this->assertModelMissing($post);
        Storage::disk('public')->assertMissing($current);
    }

    public function test_toggle_and_bulk_delete(): void
    {
        Storage::fake('public');
        $admin = $this->admin();

        $post = InstagramPost::factory()->create(['is_enabled' => true]);
        $this->actingAs($admin)->patch(route('admin.home.instagram.toggle', $post))->assertRedirect();
        $this->assertFalse($post->fresh()->is_enabled);

        $bulk = InstagramPost::factory()->count(3)->create();
        $this->actingAs($admin)
            ->delete(route('admin.home.instagram.bulk-destroy'), ['selected' => $bulk->pluck('id')->all()])
            ->assertRedirect()
            ->assertSessionHas('status');
        $this->assertDatabaseMissing('instagram_posts', ['id' => $bulk->first()->id]);
    }

    public function test_bulk_delete_requires_a_selection(): void
    {
        InstagramPost::factory()->count(2)->create();

        $this->actingAs($this->admin())
            ->delete(route('admin.home.instagram.bulk-destroy'), ['selected' => []])
            ->assertSessionHasErrors('selected');
    }

    public function test_list_is_paginated(): void
    {
        InstagramPost::factory()->count(25)->create();

        $this->actingAs($this->admin())
            ->get(route('admin.home.instagram.index'))
            ->assertOk()
            ->assertViewHas('perPage', 20);
    }

    public function test_homepage_renders_enabled_instagram_photos_only(): void
    {
        InstagramPost::factory()->create(['is_enabled' => true]);
        InstagramPost::factory()->create(['is_enabled' => false]);

        $this->get(route('home'))->assertOk()->assertViewHas('instagramPosts', fn ($posts) => $posts->count() === 1);
    }
}
