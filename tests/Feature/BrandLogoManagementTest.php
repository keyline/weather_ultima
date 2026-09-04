<?php

namespace Tests\Feature;

use App\Models\BrandLogo;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BrandLogoManagementTest extends TestCase
{
    use LazilyRefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    public function test_non_admins_cannot_manage_logos(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)->get(route('admin.home.logo.index'))->assertRedirect(route('admin.login'));
        $this->actingAs($user)->post(route('admin.home.logo.store'), [])->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_create_a_logo_with_an_image(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin())
            ->post(route('admin.home.logo.store'), [
                'image' => UploadedFile::fake()->image('logo.png'),
                'alt_text' => 'Example News',
                'display_order' => 3,
                'is_enabled' => '1',
            ])
            ->assertRedirect(route('admin.home.logo.index'));

        $logo = BrandLogo::firstWhere('alt_text', 'Example News');
        $this->assertNotNull($logo);
        Storage::disk('public')->assertExists($logo->image);
    }

    public function test_image_is_required_on_create(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.home.logo.store'), ['alt_text' => 'No image'])
            ->assertSessionHasErrors('image');
    }

    public function test_replacing_and_deleting_removes_the_stored_file(): void
    {
        Storage::fake('public');
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.home.logo.store'), [
            'image' => UploadedFile::fake()->image('old.png'), 'alt_text' => 'Swap',
        ]);
        $logo = BrandLogo::firstWhere('alt_text', 'Swap');
        $old = $logo->image;

        $this->actingAs($admin)->put(route('admin.home.logo.update', $logo), [
            'image' => UploadedFile::fake()->image('new.png'), 'alt_text' => 'Swap',
        ]);
        $logo->refresh();
        Storage::disk('public')->assertMissing($old);
        Storage::disk('public')->assertExists($logo->image);

        $current = $logo->image;
        $this->actingAs($admin)->delete(route('admin.home.logo.destroy', $logo))->assertRedirect(route('admin.home.logo.index'));
        $this->assertModelMissing($logo);
        Storage::disk('public')->assertMissing($current);
    }

    public function test_toggle_and_bulk_delete(): void
    {
        Storage::fake('public');
        $admin = $this->admin();

        $logo = BrandLogo::factory()->create(['is_enabled' => true]);
        $this->actingAs($admin)->patch(route('admin.home.logo.toggle', $logo))->assertRedirect();
        $this->assertFalse($logo->fresh()->is_enabled);

        $bulk = BrandLogo::factory()->count(3)->create();
        $this->actingAs($admin)
            ->delete(route('admin.home.logo.bulk-destroy'), ['selected' => $bulk->pluck('id')->all()])
            ->assertRedirect()
            ->assertSessionHas('status');
        $this->assertDatabaseMissing('brand_logos', ['id' => $bulk->first()->id]);
    }

    public function test_bulk_delete_requires_a_selection(): void
    {
        BrandLogo::factory()->count(2)->create();

        $this->actingAs($this->admin())
            ->delete(route('admin.home.logo.bulk-destroy'), ['selected' => []])
            ->assertSessionHasErrors('selected');
    }

    public function test_list_is_paginated(): void
    {
        BrandLogo::factory()->count(25)->create();

        $this->actingAs($this->admin())
            ->get(route('admin.home.logo.index'))
            ->assertOk()
            ->assertViewHas('perPage', 20);
    }
}
