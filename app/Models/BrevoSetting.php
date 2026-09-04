<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

#[Fillable([
    'api_key',
    'sender_name',
    'sender_email',
    'reply_to_email',
    'is_active',
])]
class BrevoSetting extends Model
{
    /**
     * @return array<string, mixed>
     */
    public static function defaults(): array
    {
        return [
            'api_key' => null,
            'sender_name' => 'Weather Ultima',
            'sender_email' => 'hello@example.com',
            'reply_to_email' => null,
            'is_active' => false,
        ];
    }

    public static function current(): self
    {
        return static::query()->firstOrCreate([], static::defaults());
    }

    public function hasApiKey(): bool
    {
        return filled($this->api_key);
    }

    /**
     * A safe-to-render hint of the stored API key (last 4 characters only).
     */
    public function maskedApiKey(): ?string
    {
        if (! $this->hasApiKey()) {
            return null;
        }

        return Str::mask($this->api_key, '•', 0, max(mb_strlen($this->api_key) - 4, 4));
    }

    /**
     * Point Laravel's mail config at this Brevo configuration.
     * Applies only when active, unless $force is set (used by the test-email action).
     */
    public function applyToMailConfig(bool $force = false): void
    {
        if (! $force && ! $this->is_active) {
            return;
        }

        Config::set('mail.default', 'brevo');
        Config::set('mail.mailers.brevo.transport', 'brevo');
        Config::set('mail.mailers.brevo.key', $this->api_key);
        Config::set('mail.from.address', $this->sender_email);
        Config::set('mail.from.name', $this->sender_name);
    }

    protected function casts(): array
    {
        return [
            'api_key' => 'encrypted',
            'is_active' => 'boolean',
        ];
    }
}
