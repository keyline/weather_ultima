<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ServiceManagementTest extends TestCase
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
            'name' => 'SkyWatch Live',
            'category' => 'Weather Forecasting & Intelligence',
            'tags' => 'Sports • Agriculture',
            'statement' => "Know the Weather.\nMake Your Move.",
            'body' => "First paragraph.\n\nSecond paragraph.",
            'result' => 'The result: better decisions.',
            'display_order' => 1,
            'is_enabled' => '1',
        ], $overrides);
    }

    public function test_non_admins_cannot_manage_services(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)->get(route('admin.services.create'))->assertRedirect(route('admin.login'));
        $this->actingAs($user)->post(route('admin.services.store'), $this->payload())->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_create_a_service_and_the_slug_is_generated(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.services.store'), $this->payload(['name' => 'MetEdge Consulting']))
            ->assertRedirect();

        $service = Service::firstWhere('name', 'MetEdge Consulting');
        $this->assertSame('metedge-consulting', $service->slug);
        $this->assertCount(2, $service->body_paragraphs);
    }

    public function test_slugs_stay_unique(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.services.store'), $this->payload(['name' => 'Duplicate']));
        $this->actingAs($admin)->post(route('admin.services.store'), $this->payload(['name' => 'Duplicate']));

        $slugs = Service::where('name', 'Duplicate')->pluck('slug');
        $this->assertSame(['duplicate', 'duplicate-2'], $slugs->sort()->values()->all());
    }

    public function test_name_and_body_are_required(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.services.store'), ['name' => '', 'body' => ''])
            ->assertSessionHasErrors(['name', 'body']);
    }

    public function test_admin_can_update_toggle_and_delete_a_service(): void
    {
        $admin = $this->admin();
        $service = Service::factory()->create(['is_enabled' => true]);

        $this->actingAs($admin)
            ->put(route('admin.services.update', $service), $this->payload(['name' => 'Renamed Service']))
            ->assertRedirect(route('admin.services.index'));
        $this->assertSame('Renamed Service', $service->fresh()->name);

        $this->actingAs($admin)->patch(route('admin.services.toggle', $service))->assertRedirect();
        $this->assertFalse($service->fresh()->is_enabled);

        $this->actingAs($admin)->delete(route('admin.services.destroy', $service))->assertRedirect(route('admin.services.index'));
        $this->assertModelMissing($service);
    }

    public function test_deleting_a_service_removes_its_image_files(): void
    {
        Storage::fake('public');
        $admin = $this->admin();
        $service = Service::factory()->create();

        $this->actingAs($admin)->post(route('admin.services.images.store', $service), [
            'image' => UploadedFile::fake()->image('a.png'),
        ]);
        $path = $service->images()->value('image');
        Storage::disk('public')->assertExists($path);

        $this->actingAs($admin)->delete(route('admin.services.destroy', $service));
        Storage::disk('public')->assertMissing($path);
        $this->assertDatabaseCount('service_images', 0);
    }

    public function test_bulk_delete_and_search_and_pagination(): void
    {
        $admin = $this->admin();

        $bulk = Service::factory()->count(3)->create();
        $this->actingAs($admin)
            ->delete(route('admin.services.bulk-destroy'), ['selected' => $bulk->pluck('id')->all()])
            ->assertRedirect()
            ->assertSessionHas('status');
        $this->assertDatabaseMissing('services', ['id' => $bulk->first()->id]);

        $this->actingAs($admin)
            ->delete(route('admin.services.bulk-destroy'), ['selected' => []])
            ->assertSessionHasErrors('selected');

        Service::factory()->count(25)->create();
        $match = Service::factory()->create(['name' => 'Findable Service']);

        $this->actingAs($admin)->get(route('admin.services.index'))->assertViewHas('perPage', 20);
        $items = $this->actingAs($admin)
            ->get(route('admin.services.index', ['search' => 'Findable']))
            ->viewData('services');
        $this->assertSame([$match->id], $items->pluck('id')->all());
    }

    public function test_admin_can_add_reorder_and_delete_service_images(): void
    {
        Storage::fake('public');
        $admin = $this->admin();
        $service = Service::factory()->create();

        $this->actingAs($admin)->post(route('admin.services.images.store', $service), ['image' => UploadedFile::fake()->image('one.png'), 'alt_text' => 'One']);
        $this->actingAs($admin)->post(route('admin.services.images.store', $service), ['image' => UploadedFile::fake()->image('two.png')]);
        $this->assertCount(2, $service->images()->get());

        [$first, $second] = $service->images()->get()->all();

        $this->actingAs($admin)->put(route('admin.services.images.update', $service), [
            'images' => [
                ['id' => $first->id, 'display_order' => 5, 'alt_text' => 'Updated caption'],
                ['id' => $second->id, 'display_order' => 1, 'alt_text' => null],
            ],
        ])->assertRedirect();

        $this->assertSame('Updated caption', $first->fresh()->alt_text);
        $this->assertSame($second->id, $service->images()->first()->id);

        $path = $first->fresh()->image;
        $this->actingAs($admin)->delete(route('admin.services.images.destroy', [$service, $first]))->assertRedirect();
        Storage::disk('public')->assertMissing($path);
        $this->assertModelMissing($first);
    }

    public function test_image_endpoints_reject_non_admins(): void
    {
        $service = Service::factory()->create();
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)
            ->post(route('admin.services.images.store', $service), ['image' => UploadedFile::fake()->image('x.png')])
            ->assertRedirect(route('admin.login'));

        $this->assertDatabaseCount('service_images', 0);
    }
}
