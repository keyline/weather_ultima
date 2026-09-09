<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class MaintenanceTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_guests_cannot_open_the_maintenance_page(): void
    {
        $this->get(route('admin.settings.maintenance.edit'))
            ->assertRedirect(route('admin.login'));
    }

    public function test_non_admins_cannot_clear_the_cache(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'user']))
            ->post(route('admin.settings.maintenance.clear-cache'))
            ->assertRedirect(route('admin.login'));
    }

    public function test_an_admin_can_view_the_maintenance_page(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->get(route('admin.settings.maintenance.edit'))
            ->assertOk()
            ->assertSee('Clear application caches');
    }

    public function test_an_admin_can_clear_the_application_caches(): void
    {
        $compiledView = storage_path('framework/views/_maintenance_probe.php');
        file_put_contents($compiledView, '<?php /* stale */');

        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->post(route('admin.settings.maintenance.clear-cache'))
            ->assertRedirect()
            ->assertSessionHas('status', fn (string $message): bool => str_contains($message, 'config:clear')
                && str_contains($message, 'route:clear')
                && str_contains($message, 'view:clear')
                && str_contains($message, 'cache:clear'));

        $this->assertFileDoesNotExist($compiledView);
    }
}
