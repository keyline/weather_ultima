<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\WeatherSetting;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class WeatherStationSettingsTest extends TestCase
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
            'application_key' => 'ambient-application-key',
            'api_key' => 'ambient-api-key',
            'kolkata_mac' => 'AA:BB:CC:DD:EE:01',
            'deoghar_mac' => '',
            'sundarban_mac' => '',
            'bardhaman_mac' => '',
            'is_active' => '1',
        ], $overrides);
    }

    public function test_non_admins_cannot_access_weather_settings(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)->get(route('admin.settings.weather.edit'))->assertRedirect(route('admin.login'));
        $this->actingAs($user)->put(route('admin.settings.weather.update'), $this->payload())->assertRedirect(route('admin.login'));
        $this->actingAs($user)->post(route('admin.settings.weather.test'))->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_save_the_configuration(): void
    {
        $this->actingAs($this->admin())
            ->put(route('admin.settings.weather.update'), $this->payload(['deoghar_mac' => 'AA:BB:CC:DD:EE:02']))
            ->assertRedirect()
            ->assertSessionHas('status');

        $settings = WeatherSetting::current();
        $this->assertSame('ambient-application-key', $settings->application_key);
        $this->assertSame('ambient-api-key', $settings->api_key);
        $this->assertSame('AA:BB:CC:DD:EE:01', $settings->kolkata_mac);
        $this->assertSame('AA:BB:CC:DD:EE:02', $settings->deoghar_mac);
        $this->assertTrue($settings->is_active);
        $this->assertTrue($settings->isConfigured());
    }

    public function test_keys_are_encrypted_masked_and_never_rendered(): void
    {
        $this->actingAs($this->admin())->put(route('admin.settings.weather.update'), $this->payload());

        $rawApp = DB::table('weather_settings')->value('application_key');
        $rawApi = DB::table('weather_settings')->value('api_key');
        $this->assertNotSame('ambient-application-key', $rawApp);
        $this->assertNotSame('ambient-api-key', $rawApi);

        $this->actingAs($this->admin())
            ->get(route('admin.settings.weather.edit'))
            ->assertOk()
            ->assertDontSee('ambient-application-key')
            ->assertDontSee('ambient-api-key');
    }

    public function test_blank_keys_keep_the_stored_ones(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin)->put(route('admin.settings.weather.update'), $this->payload());

        $this->actingAs($admin)->put(route('admin.settings.weather.update'), $this->payload([
            'application_key' => '',
            'api_key' => '',
            'kolkata_mac' => 'AA:BB:CC:DD:EE:09',
        ]));

        $settings = WeatherSetting::current();
        $this->assertSame('ambient-application-key', $settings->application_key);
        $this->assertSame('ambient-api-key', $settings->api_key);
        $this->assertSame('AA:BB:CC:DD:EE:09', $settings->kolkata_mac);
    }

    public function test_admin_can_upload_replace_and_remove_a_station_image(): void
    {
        Storage::fake('public');
        $admin = $this->admin();

        $this->actingAs($admin)->put(route('admin.settings.weather.update'), $this->payload([
            'kolkata_image' => UploadedFile::fake()->image('first.jpg'),
        ]))->assertSessionHasNoErrors();

        $first = WeatherSetting::current()->kolkata_image_path;
        Storage::disk('public')->assertExists($first);
        $this->assertStringContainsString($first, WeatherSetting::current()->imageUrlFor('Kolkata'));

        $this->actingAs($admin)->put(route('admin.settings.weather.update'), $this->payload([
            'kolkata_image' => UploadedFile::fake()->image('second.jpg'),
        ]));
        Storage::disk('public')->assertMissing($first);

        $second = WeatherSetting::current()->kolkata_image_path;
        $this->actingAs($admin)->put(route('admin.settings.weather.update'), $this->payload([
            'remove_kolkata_image' => '1',
        ]));

        Storage::disk('public')->assertMissing($second);
        $this->assertNull(WeatherSetting::current()->kolkata_image_path);
        $this->assertStringContainsString('service1.png', WeatherSetting::current()->imageUrlFor('Kolkata'));
    }

    public function test_station_image_must_be_an_image(): void
    {
        $this->actingAs($this->admin())
            ->put(route('admin.settings.weather.update'), $this->payload([
                'kolkata_image' => UploadedFile::fake()->create('notes.pdf', 10, 'application/pdf'),
            ]))
            ->assertSessionHasErrors('kolkata_image');
    }

    public function test_cannot_activate_without_both_keys(): void
    {
        $this->actingAs($this->admin())
            ->put(route('admin.settings.weather.update'), $this->payload([
                'application_key' => '',
                'api_key' => '',
            ]))
            ->assertSessionHasErrors('is_active');

        $this->assertFalse(WeatherSetting::current()->is_active);
    }

    public function test_test_connection_reports_the_devices_it_can_see(): void
    {
        Http::fake([
            'rt.ambientweather.net/*' => Http::response([
                ['macAddress' => 'AA:BB:CC:DD:EE:01', 'info' => ['name' => 'Kolkata Rooftop'], 'lastData' => ['tempf' => 80]],
            ]),
        ]);

        $this->actingAs($this->admin())->put(route('admin.settings.weather.update'), $this->payload());

        $this->actingAs($this->admin())
            ->post(route('admin.settings.weather.test'))
            ->assertRedirect()
            ->assertSessionHas('status', fn (string $message): bool => str_contains($message, 'Kolkata Rooftop')
                && str_contains($message, 'AA:BB:CC:DD:EE:01'));
    }

    public function test_test_connection_requires_saved_keys(): void
    {
        Http::preventStrayRequests();

        $this->actingAs($this->admin())
            ->post(route('admin.settings.weather.test'))
            ->assertRedirect()
            ->assertSessionHas('weather_test_error');
    }

    public function test_test_connection_surfaces_api_errors(): void
    {
        Http::fake(['rt.ambientweather.net/*' => Http::response('denied', 401)]);

        $this->actingAs($this->admin())->put(route('admin.settings.weather.update'), $this->payload());

        $this->actingAs($this->admin())
            ->post(route('admin.settings.weather.test'))
            ->assertRedirect()
            ->assertSessionHas('weather_test_error');
    }
}
