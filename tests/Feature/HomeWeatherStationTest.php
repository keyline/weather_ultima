<?php

namespace Tests\Feature;

use App\Models\WeatherSetting;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class HomeWeatherStationTest extends TestCase
{
    use LazilyRefreshDatabase;

    /**
     * @param  array<string, mixed>  $lastDataOverrides
     * @return array<string, mixed>
     */
    private function device(string $mac, array $lastDataOverrides = []): array
    {
        return [
            'macAddress' => $mac,
            'info' => ['name' => 'Test Station'],
            'lastData' => array_merge([
                'date' => now()->subMinutes(5)->toIso8601String(),
                'tempf' => 71.6,
                'feelsLike' => 73.4,
                'humidity' => 70,
                'windspeedmph' => 10,
                'hourlyrainin' => 0.5,
                'dailyrainin' => 1,
                'dewPoint' => 60.8,
            ], $lastDataOverrides),
        ];
    }

    public function test_all_four_station_panels_are_rendered(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('data-station-panel="Kolkata"', false)
            ->assertSee('data-station-panel="Deoghar"', false)
            ->assertSee('data-station-panel="Sundarban"', false)
            ->assertSee('data-station-panel="Bardhaman"', false);
    }

    public function test_stations_show_coming_soon_when_not_configured(): void
    {
        Http::preventStrayRequests();

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Live station data for Kolkata is coming soon.')
            ->assertSee('Live station data for Bardhaman is coming soon.');
    }

    public function test_configured_station_shows_live_metric_readings(): void
    {
        Http::fake([
            'rt.ambientweather.net/*' => Http::response([$this->device('AA:BB:CC:DD:EE:FF')]),
        ]);

        WeatherSetting::current()->update([
            'application_key' => 'app-key',
            'api_key' => 'api-key',
            'kolkata_mac' => 'AA:BB:CC:DD:EE:FF',
            'is_active' => true,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('22.0°C')   // tempf 71.6 -> celsius
            ->assertSee('23.0°C')   // feelsLike 73.4 -> celsius
            ->assertSee('70%')      // humidity
            ->assertSee('16.1 km/h') // windspeedmph 10 -> km/h
            ->assertSee('12.7 mm')  // hourlyrainin 0.5 -> mm
            ->assertSee('25.4 mm')  // dailyrainin 1 -> mm
            ->assertDontSee('Live station data for Kolkata is coming soon.');
    }

    public function test_station_without_a_mac_stays_coming_soon_while_others_are_live(): void
    {
        Http::fake([
            'rt.ambientweather.net/*' => Http::response([$this->device('AA:BB:CC:DD:EE:FF')]),
        ]);

        WeatherSetting::current()->update([
            'application_key' => 'app-key',
            'api_key' => 'api-key',
            'kolkata_mac' => 'AA:BB:CC:DD:EE:FF',
            'is_active' => true,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('22.0°C')
            ->assertSee('Live station data for Deoghar is coming soon.');
    }

    public function test_api_failure_falls_back_to_coming_soon(): void
    {
        Http::fake([
            'rt.ambientweather.net/*' => Http::response('nope', 500),
        ]);

        WeatherSetting::current()->update([
            'application_key' => 'app-key',
            'api_key' => 'api-key',
            'kolkata_mac' => 'AA:BB:CC:DD:EE:FF',
            'is_active' => true,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Live station data for Kolkata is coming soon.');
    }

    public function test_console_only_device_without_outdoor_data_stays_coming_soon(): void
    {
        Http::fake([
            'rt.ambientweather.net/*' => Http::response([[
                'macAddress' => 'AA:BB:CC:DD:EE:FF',
                'info' => ['name' => 'Indoor console'],
                'lastData' => [
                    'date' => now()->subMinutes(2)->toIso8601String(),
                    'tempinf' => 82.6,
                    'humidityin' => 71,
                    'baromrelin' => 29.6,
                ],
            ]]),
        ]);

        WeatherSetting::current()->update([
            'application_key' => 'app-key',
            'api_key' => 'api-key',
            'kolkata_mac' => 'AA:BB:CC:DD:EE:FF',
            'is_active' => true,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Live station data for Kolkata is coming soon.');
    }

    public function test_an_old_reading_still_shows_with_its_age_in_the_caption(): void
    {
        Http::fake([
            'rt.ambientweather.net/*' => Http::response([
                $this->device('AA:BB:CC:DD:EE:FF', ['date' => now()->subWeeks(3)->toIso8601String()]),
            ]),
        ]);

        WeatherSetting::current()->update([
            'application_key' => 'app-key',
            'api_key' => 'api-key',
            'kolkata_mac' => 'AA:BB:CC:DD:EE:FF',
            'is_active' => true,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('22.0°C')
            ->assertSee('3 weeks ago')
            ->assertDontSee('Live station data for Kolkata is coming soon.');
    }
}
