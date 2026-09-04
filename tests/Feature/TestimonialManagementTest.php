<?php

namespace Tests\Feature;

use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class TestimonialManagementTest extends TestCase
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
            'name' => 'Anita Rao',
            'designation' => 'Farm Owner',
            'company' => 'Green Fields',
            'review' => 'Reliable forecasts that our whole team depends on.',
            'rating' => 5,
            'display_order' => 2,
            'is_enabled' => '1',
        ], $overrides);
    }

    public function test_non_admins_cannot_manage_testimonials(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)->get(route('admin.testimonials.index'))->assertRedirect(route('admin.login'));
        $this->actingAs($user)->post(route('admin.testimonials.store'), $this->payload())->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_create_and_edit_a_testimonial(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->post(route('admin.testimonials.store'), $this->payload(['name' => 'Priya Sen']))
            ->assertRedirect(route('admin.testimonials.index'));

        $testimonial = Testimonial::firstWhere('name', 'Priya Sen');
        $this->assertSame(5, $testimonial->rating);
        $this->assertSame('Farm Owner, Green Fields', $testimonial->role_line);

        $this->actingAs($admin)
            ->put(route('admin.testimonials.update', $testimonial), $this->payload(['name' => 'Priya Sen', 'rating' => 4]))
            ->assertRedirect(route('admin.testimonials.index'));
        $this->assertSame(4, $testimonial->fresh()->rating);
    }

    public function test_name_and_review_and_rating_are_required(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.testimonials.store'), ['name' => '', 'review' => '', 'rating' => 9])
            ->assertSessionHasErrors(['name', 'review', 'rating']);
    }

    public function test_admin_can_toggle_and_delete_and_bulk_delete(): void
    {
        $admin = $this->admin();
        $enabled = Testimonial::factory()->create(['is_enabled' => true]);

        $this->actingAs($admin)->patch(route('admin.testimonials.toggle', $enabled))->assertRedirect();
        $this->assertFalse($enabled->fresh()->is_enabled);

        $one = Testimonial::factory()->create();
        $this->actingAs($admin)->delete(route('admin.testimonials.destroy', $one))->assertRedirect(route('admin.testimonials.index'));
        $this->assertModelMissing($one);

        $bulk = Testimonial::factory()->count(3)->create();
        $this->actingAs($admin)
            ->delete(route('admin.testimonials.bulk-destroy'), ['selected' => $bulk->pluck('id')->all()])
            ->assertRedirect()
            ->assertSessionHas('status');
        $this->assertDatabaseMissing('testimonials', ['id' => $bulk->first()->id]);
    }

    public function test_bulk_delete_requires_a_selection(): void
    {
        Testimonial::factory()->count(2)->create();

        $this->actingAs($this->admin())
            ->delete(route('admin.testimonials.bulk-destroy'), ['selected' => []])
            ->assertSessionHasErrors('selected');
    }

    public function test_list_is_searchable_and_paginated(): void
    {
        Testimonial::factory()->count(25)->create();
        $match = Testimonial::factory()->create(['name' => 'Findable Person']);
        $admin = $this->admin();

        $this->actingAs($admin)->get(route('admin.testimonials.index'))->assertViewHas('perPage', 20);

        $items = $this->actingAs($admin)
            ->get(route('admin.testimonials.index', ['search' => 'Findable']))
            ->viewData('testimonials');
        $this->assertSame([$match->id], $items->pluck('id')->all());
    }
}
