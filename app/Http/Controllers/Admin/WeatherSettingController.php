<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateWeatherSettingsRequest;
use App\Models\WeatherSetting;
use App\Services\WeatherStationService;
use Illuminate\Http\RedirectResponse;
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
        $settings = WeatherSetting::current();

        $data = $request->safe()->except(['application_key', 'api_key', 'is_active', ...$this->imageFields()]);
        $data['is_active'] = $request->boolean('is_active');

        if (filled($request->input('application_key'))) {
            $data['application_key'] = $request->string('application_key')->toString();
        }

        if (filled($request->input('api_key'))) {
            $data['api_key'] = $request->string('api_key')->toString();
        }

        foreach (WeatherSetting::STATION_IMAGES as $station => $column) {
            $field = str($column)->beforeLast('_path')->toString();

            if ($request->hasFile($field)) {
                $this->deleteStoredFile($settings->{$column});
                $data[$column] = $request->file($field)->store('weather-stations', 'public');
            } elseif ($request->boolean('remove_'.$field)) {
                $this->deleteStoredFile($settings->{$column});
                $data[$column] = null;
            }
        }

        $settings->update($data);
        $weatherStation->flush();

        return back()->with('status', 'Weather station configuration saved.');
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
     * Upload / remove form fields that are not columns on the settings row.
     *
     * @return list<string>
     */
    private function imageFields(): array
    {
        return collect(WeatherSetting::STATION_IMAGES)
            ->map(fn (string $column): string => str($column)->beforeLast('_path')->toString())
            ->flatMap(fn (string $field): array => [$field, 'remove_'.$field])
            ->values()
            ->all();
    }

    private function deleteStoredFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
