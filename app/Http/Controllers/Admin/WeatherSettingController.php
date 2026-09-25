<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateWeatherSettingsRequest;
use App\Http\Requests\Admin\UpdateWeatherStationImageRequest;
use App\Models\WeatherSetting;
use App\Services\WeatherStationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class WeatherSettingController extends Controller
{
    public function edit(): View
    {
        return view('admin.settings.weather', ['settings' => WeatherSetting::current()]);
    }

    public function update(UpdateWeatherSettingsRequest $request, WeatherStationService $weatherStation): RedirectResponse
    {
        $data = $request->safe()->except(['application_key', 'api_key', 'is_active']);
        $data['is_active'] = $request->boolean('is_active');

        if (filled($request->input('application_key'))) {
            $data['application_key'] = $request->string('application_key')->toString();
        }

        if (filled($request->input('api_key'))) {
            $data['api_key'] = $request->string('api_key')->toString();
        }

        WeatherSetting::current()->update($data);
        $weatherStation->flush();

        return back()->with('status', 'Weather station configuration saved.');
    }

    public function updateImage(UpdateWeatherStationImageRequest $request, string $station): RedirectResponse
    {
        [$label, $column] = $this->stationImageColumn($station);
        $settings = WeatherSetting::current();

        $path = $request->file('image')->store('weather-stations', 'public');
        $this->deleteStoredFile($settings->{$column});
        $settings->update([$column => $path]);

        return back()->with('status', "{$label} station image saved.");
    }

    public function destroyImage(Request $request, string $station): RedirectResponse
    {
        [$label, $column] = $this->stationImageColumn($station);
        $settings = WeatherSetting::current();

        $this->deleteStoredFile($settings->{$column});
        $settings->update([$column => null]);

        return back()->with('status', "{$label} station image removed — the default is shown again.");
    }

    public function test(WeatherStationService $weatherStation): RedirectResponse
    {
        $weatherStation->flush();
        $result = $weatherStation->probe();

        if (! $result['ok']) {
            return back()->with('weather_test_error', $result['message']);
        }

        return back()->with('status', $result['message']);
    }

    /**
     * Resolve a URL slug (e.g. "kolkata") to its station label and image column.
     *
     * @return array{0: string, 1: string}
     */
    private function stationImageColumn(string $station): array
    {
        $label = collect(array_keys(WeatherSetting::STATION_IMAGES))
            ->first(fn (string $label): bool => strtolower($label) === strtolower($station));

        abort_if($label === null, 404);

        return [$label, WeatherSetting::STATION_IMAGES[$label]];
    }

    private function deleteStoredFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
