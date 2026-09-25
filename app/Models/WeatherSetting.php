<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

#[Fillable([
    'application_key',
    'api_key',
    'kolkata_mac',
    'deoghar_mac',
    'sundarban_mac',
    'bardhaman_mac',
    'kolkata_image_path',
    'deoghar_image_path',
    'sundarban_image_path',
    'bardhaman_image_path',
    'is_active',
])]
class WeatherSetting extends Model
{
    /**
     * Weather station tab label => the column holding that station's
     * Ambient Weather device MAC address. Order matches the homepage tabs.
     *
     * @var array<string, string>
     */
    public const STATIONS = [
        'Kolkata' => 'kolkata_mac',
        'Deoghar' => 'deoghar_mac',
        'Sundarban' => 'sundarban_mac',
        'Bardhaman' => 'bardhaman_mac',
    ];

    /**
     * The image shown beside a station's readings when no custom one is uploaded.
     */
    public const DEFAULT_STATION_IMAGE = 'material/images/service1.png';

    /**
     * Weather station tab label => the column holding its uploaded image path.
     *
     * @var array<string, string>
     */
    public const STATION_IMAGES = [
        'Kolkata' => 'kolkata_image_path',
        'Deoghar' => 'deoghar_image_path',
        'Sundarban' => 'sundarban_image_path',
        'Bardhaman' => 'bardhaman_image_path',
    ];

    /**
     * @return array<string, mixed>
     */
    public static function defaults(): array
    {
        return [
            'application_key' => null,
            'api_key' => null,
            'kolkata_mac' => null,
            'deoghar_mac' => null,
            'sundarban_mac' => null,
            'bardhaman_mac' => null,
            'is_active' => false,
        ];
    }

    public static function current(): self
    {
        return static::query()->firstOrCreate([], static::defaults());
    }

    public function hasApplicationKey(): bool
    {
        return filled($this->application_key);
    }

    public function hasApiKey(): bool
    {
        return filled($this->api_key);
    }

    /**
     * Both Ambient Weather credentials are present.
     */
    public function hasCredentials(): bool
    {
        return $this->hasApplicationKey() && $this->hasApiKey();
    }

    /**
     * Live data should be fetched and rendered — active and fully credentialed.
     */
    public function isConfigured(): bool
    {
        return $this->is_active && $this->hasCredentials();
    }

    /**
     * The configured device MAC address for a station tab, if any.
     */
    public function macFor(string $station): ?string
    {
        $column = self::STATIONS[$station] ?? null;

        return $column ? $this->{$column} : null;
    }

    /**
     * The public URL of a station's image, falling back to the bundled default.
     */
    public function imageUrlFor(string $station): string
    {
        $path = ($column = self::STATION_IMAGES[$station] ?? null) ? $this->{$column} : null;

        return $path ? asset('storage/'.$path) : asset(self::DEFAULT_STATION_IMAGE);
    }

    /**
     * Image URL for every station tab, keyed by tab label.
     *
     * @return array<string, string>
     */
    public function imageUrls(): array
    {
        return collect(array_keys(self::STATION_IMAGES))
            ->mapWithKeys(fn (string $station): array => [$station => $this->imageUrlFor($station)])
            ->all();
    }

    public function maskedApplicationKey(): ?string
    {
        return $this->mask($this->application_key);
    }

    public function maskedApiKey(): ?string
    {
        return $this->mask($this->api_key);
    }

    /**
     * A safe-to-render hint of a stored key (last 4 characters only).
     */
    private function mask(?string $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        return Str::mask($value, '•', 0, max(mb_strlen($value) - 4, 4));
    }

    protected function casts(): array
    {
        return [
            'application_key' => 'encrypted',
            'api_key' => 'encrypted',
            'is_active' => 'boolean',
        ];
    }
}
