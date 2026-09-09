<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateWeatherSettingsRequest;
use App\Models\WeatherSetting;
use App\Services\WeatherStationService;
use Illuminate\Http\RedirectResponse;
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

    public function test(WeatherStationService $weatherStation): RedirectResponse
    {
        $weatherStation->flush();
        $result = $weatherStation->probe();

        if (! $result['ok']) {
            return back()->with('weather_test_error', $result['message']);
        }

        return back()->with('status', $result['message']);
    }
}
