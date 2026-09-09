<?php

namespace App\Services;

use App\Models\WeatherSetting;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Throwable;

/**
 * Reads live observations from the Ambient Weather Network REST API
 * (https://ambientweather.docs.apiary.io/) for the homepage weather station
 * section. This is the same `/v1/devices` endpoint the `Jafo232/ambient_api`
 * package wraps — that package pins Guzzle 6 and cannot install alongside
 * Laravel 13, so the call is made directly through Laravel's HTTP client.
 */
class WeatherStationService
{
    private const ENDPOINT = 'https://rt.ambientweather.net/v1/devices';

    private const CACHE_KEY = 'weather_station.devices';

    /**
     * Ambient Weather allows one request per second / per API key. The homepage
     * only needs a recent snapshot, so responses are cached for a few minutes.
     */
    private const CACHE_TTL = 300;

    /**
     * Normalised readings keyed by station tab label. Value is `null` when that
     * station has no live data (not configured, no device MAC, or API failure).
     *
     * @return array<string, array<string, mixed>|null>
     */
    public function stations(): array
    {
        $settings = WeatherSetting::current();
        $readings = [];

        foreach (array_keys(WeatherSetting::STATIONS) as $station) {
            $readings[$station] = $this->readingFor($settings, $station);
        }

        return $readings;
    }

    /**
     * Live connectivity check for the admin settings screen. Bypasses the cache
     * and lists the devices visible to the saved credentials so the admin can
     * copy each station's MAC address.
     *
     * @return array{ok: bool, message: string}
     */
    public function probe(): array
    {
        $settings = WeatherSetting::current();

        if (! $settings->hasCredentials()) {
            return ['ok' => false, 'message' => 'Save an application key and an API key first, then test the connection.'];
        }

        try {
            $devices = $this->fetchDevices($settings);
        } catch (Throwable $exception) {
            report($exception);

            return ['ok' => false, 'message' => 'Could not reach the Ambient Weather API: '.$exception->getMessage()];
        }

        if ($devices->isEmpty()) {
            return ['ok' => false, 'message' => 'The Ambient Weather API responded, but no devices are linked to these keys. Check the keys in your Ambient Weather account.'];
        }

        $list = $devices
            ->map(fn (array $device): string => sprintf(
                '%s (%s)',
                data_get($device, 'info.name', 'Unnamed device'),
                data_get($device, 'macAddress', 'no MAC'),
            ))
            ->implode(', ');

        return ['ok' => true, 'message' => 'Connected. '.$devices->count().' device(s) available: '.$list.'.'];
    }

    /**
     * Drop the cached device payload — called after the credentials change.
     */
    public function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * @return array<string, mixed>|null
     */
    private function readingFor(WeatherSetting $settings, string $station): ?array
    {
        if (! $settings->isConfigured()) {
            return null;
        }

        $mac = $settings->macFor($station);

        if (blank($mac)) {
            return null;
        }

        $mac = strtoupper(trim($mac));

        try {
            $device = $this->cachedDevices($settings)
                ->first(fn (array $device): bool => strtoupper(trim((string) ($device['macAddress'] ?? ''))) === $mac);
        } catch (Throwable $exception) {
            report($exception);

            return null;
        }

        $lastData = $device['lastData'] ?? null;

        if (blank($device) || ! is_array($lastData)) {
            return null;
        }

        // Ambient consoles report indoor-only fields (tempinf, humidityin) when
        // no outdoor array is connected/reporting. Without an outdoor temperature
        // there is nothing meaningful to show for a public weather station, so
        // that station keeps its "coming soon" note. A reading that is simply
        // old still shows — the panel caption states how long ago it was taken.
        if (! is_numeric($lastData['tempf'] ?? null)) {
            return null;
        }

        return $this->normalise($lastData);
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function cachedDevices(WeatherSetting $settings): Collection
    {
        $payload = Cache::remember(
            self::CACHE_KEY,
            self::CACHE_TTL,
            fn (): array => $this->fetchDevices($settings)->all(),
        );

        return collect($payload);
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function fetchDevices(WeatherSetting $settings): Collection
    {
        try {
            $response = Http::timeout(8)
                ->retry(2, 500, throw: false)
                ->get(self::ENDPOINT, [
                    'applicationKey' => $settings->application_key,
                    'apiKey' => $settings->api_key,
                ]);
        } catch (ConnectionException $exception) {
            throw new RuntimeException($exception->getMessage(), previous: $exception);
        }

        if ($response->failed()) {
            throw new RuntimeException('Ambient Weather API returned HTTP '.$response->status().'.');
        }

        return collect($response->json())
            ->filter(fn ($device): bool => is_array($device))
            ->values();
    }

    /**
     * Map an Ambient Weather `lastData` payload to the display strings the
     * homepage station grid renders. Ambient reports imperial units; the site
     * shows metric.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function normalise(array $data): array
    {
        $celsius = fn (mixed $f): ?float => is_numeric($f) ? round(((float) $f - 32) * 5 / 9, 1) : null;
        $kmh = fn (mixed $mph): ?float => is_numeric($mph) ? round((float) $mph * 1.609344, 1) : null;
        $mm = fn (mixed $inches): ?float => is_numeric($inches) ? round((float) $inches * 25.4, 1) : null;
        $hpa = fn (mixed $inHg): ?float => is_numeric($inHg) ? round((float) $inHg * 33.8639, 0) : null;

        // Field names vary slightly between Ambient station firmwares.
        $first = fn (array $keys): mixed => collect($keys)
            ->map(fn (string $key): mixed => $data[$key] ?? null)
            ->first(fn (mixed $value): bool => is_numeric($value));

        $observedAt = null;

        try {
            if (filled($data['date'] ?? null)) {
                $observedAt = now()->parse($data['date']);
            } elseif (is_numeric($data['dateutc'] ?? null)) {
                $observedAt = now()->createFromTimestampMs((int) $data['dateutc']);
            }
        } catch (Throwable) {
            $observedAt = null;
        }

        return [
            'available' => true,
            'observed_at' => $observedAt?->toIso8601String(),
            'observed_for_humans' => $observedAt?->diffForHumans(),
            'temperature' => $this->value($celsius($data['tempf'] ?? null), '°C'),
            'feels_like' => $this->value($celsius($first(['feelsLike', 'feelslike', 'realfeel'])), '°C'),
            'humidity' => $this->value($data['humidity'] ?? null, '%', 0),
            'wind_speed' => $this->value($kmh($data['windspeedmph'] ?? null), ' km/h'),
            'wind_gust' => $this->value($kmh($data['windgustmph'] ?? null), ' km/h'),
            'wind_direction' => $this->compass($data['winddir'] ?? null),
            'hourly_rainfall' => $this->value($mm($data['hourlyrainin'] ?? null), ' mm'),
            'daily_rainfall' => $this->value($mm($data['dailyrainin'] ?? null), ' mm'),
            'dew_point' => $this->value($celsius($first(['dewPoint', 'dewpoint'])), '°C'),
            'pressure' => $this->value($hpa($data['baromrelin'] ?? null), ' hPa', 0),
            'uv_index' => $this->value($data['uv'] ?? null, '', 0),
        ];
    }

    private function value(mixed $number, string $suffix, int $decimals = 1): string
    {
        if (! is_numeric($number)) {
            return '—';
        }

        return number_format((float) $number, $decimals).$suffix;
    }

    private function compass(mixed $degrees): string
    {
        if (! is_numeric($degrees)) {
            return '—';
        }

        $points = ['N', 'NNE', 'NE', 'ENE', 'E', 'ESE', 'SE', 'SSE', 'S', 'SSW', 'SW', 'WSW', 'W', 'WNW', 'NW', 'NNW'];

        return $points[(int) round(((float) $degrees % 360) / 22.5) % 16];
    }
}
