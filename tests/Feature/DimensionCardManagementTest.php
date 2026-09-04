<?php

namespace Tests\Feature;

use App\Models\DimensionCard;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DimensionCardManagementTest extends TestCase
{
    use LazilyRefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    public function test_non_admins_cannot_manage_cards(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)->get(route('admin.home.cards.create'))->assertRedirect(route('admin.login'));
        $this->actingAs($user)->post(route('admin.home.cards.store'), [])->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_create_a_card_with_an_image_and_link(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin())
            ->post(route('admin.home.cards.store'), [
                'title' => 'SkyWatch Live',
                'description' => 'Sports • Agriculture • Outdoor Operations',
                'link_url' => '/services',
                'image' => UploadedFile::fake()->image('card.jpg'),
                'display_order' => 1,
                'is_enabled' => '1',
            ])
            ->assertRedirect(route('admin.home.banner.edit'));

        $card = DimensionCard::firstWhere('title', 'SkyWatch Live');
        $this->assertSame('/services', $card->link_url);
        Storage::disk('public')->assertExists($card->image);
    }

    public function test_title_and_description_are_required(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.home.cards.store'), ['link_url' => '/x'])
            ->assertSessionHasErrors(['title', 'description']);
    }

    public function test_updating_the_image_replaces_the_old_file_and_remove_flag_clears_it(): void
    {
        Storage::fake('public');
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.home.cards.store'), [
            'title' => 'Card', 'description' => 'Desc', 'image' => UploadedFile::fake()->image('a.png'),
        ]);
        $card = DimensionCard::firstWhere('title', 'Card');
        $old = $card->image;

        $this->actingAs($admin)->put(route('admin.home.cards.update', $card), [
            'title' => 'Card', 'description' => 'Desc', 'image' => UploadedFile::fake()->image('b.png'),
        ]);
        $card->refresh();
        Storage::disk('public')->assertMissing($old);
        Storage::disk('public')->assertExists($card->image);

        $current = $card->image;
        $this->actingAs($admin)->put(route('admin.home.cards.update', $card), [
            'title' => 'Card', 'description' => 'Desc', 'remove_image' => '1',
        ]);
        $this->assertNull($card->fresh()->image);
        Storage::disk('public')->assertMissing($current);
    }

    public function test_toggle_delete_and_bulk_delete(): void
    {
        $admin = $this->admin();

        $card = DimensionCard::factory()->create(['is_enabled' => true]);
        $this->actingAs($admin)->patch(route('admin.home.cards.toggle', $card))->assertRedirect();
        $this->assertFalse($card->fresh()->is_enabled);

        $this->actingAs($admin)->delete(route('admin.home.cards.destroy', $card))->assertRedirect(route('admin.home.banner.edit'));
        $this->assertModelMissing($card);

        $bulk = DimensionCard::factory()->count(3)->create();
        $this->actingAs($admin)
            ->delete(route('admin.home.cards.bulk-destroy'), ['selected' => $bulk->pluck('id')->all()])
            ->assertRedirect(route('admin.home.banner.edit'))
            ->assertSessionHas('status');
        $this->assertDatabaseMissing('dimension_cards', ['id' => $bulk->first()->id]);
    }

    public function test_bulk_delete_requires_a_selection(): void
    {
        DimensionCard::factory()->count(2)->create();

        $this->actingAs($this->admin())
            ->delete(route('admin.home.cards.bulk-destroy'), ['selected' => []])
            ->assertSessionHasErrors('selected');
    }

    public function test_banner_page_lists_the_cards(): void
    {
        DimensionCard::factory()->create(['title' => 'Listed Card']);

        $this->actingAs($this->admin())
            ->get(route('admin.home.banner.edit'))
            ->assertOk()
            ->assertViewHas('cards')
            ->assertSee('Listed Card');
    }
}
