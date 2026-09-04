<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

#[Fillable([
    'site_key',
    'secret_key',
    'version',
    'minimum_score',
    'is_active',
])]
class RecaptchaSetting extends Model
{
    public const VERSIONS = [
        'v2' => 'reCAPTCHA v2 ("I\'m not a robot" checkbox)',
        'v3' => 'reCAPTCHA v3 (invisible, score based)',
    ];

    /**
     * @return array<string, mixed>
     */
    public static function defaults(): array
    {
        return [
            'site_key' => null,
            'secret_key' => null,
            'version' => 'v2',
            'minimum_score' => 0.5,
            'is_active' => false,
        ];
    }

    public static function current(): self
    {
        return static::query()->firstOrCreate([], static::defaults());
    }

    /**
     * Whether reCAPTCHA should be enforced and rendered — active with both keys present.
     */
    public function isEnforced(): bool
    {
        return $this->is_active && filled($this->site_key) && filled($this->secret_key);
    }

    public function isV3(): bool
    {
        return $this->version === 'v3';
    }

    public function hasSecretKey(): bool
    {
        return filled($this->secret_key);
    }

    /**
     * A safe-to-render hint of the stored secret key (last 4 characters only).
     */
    public function maskedSecretKey(): ?string
    {
        if (! $this->hasSecretKey()) {
            return null;
        }

        return Str::mask($this->secret_key, '•', 0, max(mb_strlen($this->secret_key) - 4, 4));
    }

    /**
     * The Google API script URL for the configured version.
     */
    public function scriptUrl(): string
    {
        return $this->isV3()
            ? 'https://www.google.com/recaptcha/api.js?render='.$this->site_key
            : 'https://www.google.com/recaptcha/api.js';
    }

    protected function casts(): array
    {
        return [
            'secret_key' => 'encrypted',
            'minimum_score' => 'float',
            'is_active' => 'boolean',
        ];
    }
}
