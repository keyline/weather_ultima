---
paths:
  - 'app/Services/**'
  - 'app/Models/WeatherSetting.php'
  - 'app/Http/Controllers/Admin/WeatherSettingController.php'
  - 'resources/views/partials/weather-station-panel.blade.php'
  - 'resources/views/home.blade.php'
---

# Partials

## Homepage weather station uses Ambient Weather API directly
The homepage "Our Weather Station" section (home.blade.php → partials/weather-station-panel.blade.php, one .wx-station-body panel per tab, toggled by main.js) is fed by App\Services\WeatherStationService, which calls the Ambient Weather REST endpoint https://rt.ambientweather.net/v1/devices via Http:: and caches for 5 min (key weather_station.devices; call ->flush() after credential changes).

Do NOT try to add the jafo232/ambientapi (Jafo232/ambient_api) composer package — it pins guzzle ^6.2 and cannot resolve against Laravel 13.

Credentials + per-station device MAC addresses live in the single-row weather_settings table (WeatherSetting::current(), keys encrypted), managed at Settings → Weather Station (admin.settings.weather.{edit,update,test}). STATIONS const maps tab label → MAC column. A station with no MAC, or is_active off / missing keys, renders the "coming soon" placeholder. Ambient reports imperial; the service converts to °C / km/h / mm / hPa.
