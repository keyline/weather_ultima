<?php

namespace Tests\Feature;

use App\Models\CoreValue;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class CoreValueManagementTest extends TestCase
{
    use LazilyRefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    public function test_non_admins_cannot_manage_core_values(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)->get(route('admin.home.core-values.index'))->assertRedirect(route('admin.login'));
        $this->actingAs($user)->post(route('admin.home.core-values.store'), [])->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_create_edit_and_delete_a_core_value(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.home.core-values.store'), [
            'icon' => 'R', 'title' => 'Reliability', 'description' => 'We keep our word.', 'display_order' => 1, 'is_enabled' => '1',
        ])->assertRedirect(route('admin.home.core-values.index'));

        $value = CoreValue::firstWhere('title', 'Reliability');
        $this->assertSame('R', $value->icon);

        $this->actingAs($admin)->put(route('admin.home.core-values.update', $value), [
            'icon' => 'A', 'title' => 'Accuracy', 'description' => 'Precision matters.', 'display_order' => 2,
        ])->assertRedirect(route('admin.home.core-values.index'));
        $this->assertSame('Accuracy', $value->fresh()->title);
        $this->assertFalse($value->fresh()->is_enabled);

        $this->actingAs($admin)->delete(route('admin.home.core-values.destroy', $value))->assertRedirect();
        $this->assertModelMissing($value);
    }

    public function test_title_and_description_are_required(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.home.core-values.store'), ['icon' => 'X'])
            ->assertSessionHasErrors(['title', 'description']);
    }

    public function test_toggle_and_bulk_delete_and_pagination(): void
    {
        $admin = $this->admin();

        $value = CoreValue::factory()->create(['is_enabled' => true]);
        $this->actingAs($admin)->patch(route('admin.home.core-values.toggle', $value))->assertRedirect();
        $this->assertFalse($value->fresh()->is_enabled);

        $bulk = CoreValue::factory()->count(3)->create();
        $this->actingAs($admin)
            ->delete(route('admin.home.core-values.bulk-destroy'), ['selected' => $bulk->pluck('id')->all()])
            ->assertRedirect()
            ->assertSessionHas('status');

        CoreValue::factory()->count(25)->create();
        $this->actingAs($admin)->get(route('admin.home.core-values.index'))->assertViewHas('perPage', 20);

        $this->actingAs($admin)
            ->delete(route('admin.home.core-values.bulk-destroy'), ['selected' => []])
            ->assertSessionHasErrors('selected');
    }
}
