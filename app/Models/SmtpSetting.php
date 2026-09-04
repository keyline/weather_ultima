<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

#[Fillable([
    'host',
    'port',
    'username',
    'password',
    'encryption',
    'from_address',
    'from_name',
    'is_active',
])]
class SmtpSetting extends Model
{
    /**
     * Sensible defaults for a fresh settings row.
     *
     * @return array<string, mixed>
     */
    public static function defaults(): array
    {
        return [
            'host' => '127.0.0.1',
            'port' => 587,
            'username' => null,
            'password' => null,
            'encryption' => 'tls',
            'from_address' => 'hello@example.com',
            'from_name' => 'Weather Ultima',
            'is_active' => false,
        ];
    }

    /**
     * The single settings row, creating one with defaults on first access.
     */
    public static function current(): self
    {
        return static::query()->firstOrCreate([], static::defaults());
    }

    /**
     * Override the runtime mail config with this row.
     * Only applies when the configuration is active, unless $force is set
     * (used by the "send test email" action so it can be tried before activation).
     */
    public function applyToMailConfig(bool $force = false): void
    {
        if (! $force && ! $this->is_active) {
            return;
        }

        Config::set('mail.default', 'smtp');
        Config::set('mail.mailers.smtp.transport', 'smtp');
        Config::set('mail.mailers.smtp.host', $this->host);
        Config::set('mail.mailers.smtp.port', $this->port);
        Config::set('mail.mailers.smtp.username', $this->username ?: null);
        Config::set('mail.mailers.smtp.password', $this->password ?: null);
        Config::set('mail.mailers.smtp.scheme', $this->transportScheme());
        Config::set('mail.from.address', $this->from_address);
        Config::set('mail.from.name', $this->from_name);
    }

    /**
     * Whether a password is stored, without revealing it.
     */
    public function hasPassword(): bool
    {
        return filled($this->password);
    }

    /**
     * A safe-to-render hint of the stored password (last 2 characters only).
     */
    public function maskedPassword(): ?string
    {
        if (! $this->hasPassword()) {
            return null;
        }

        return Str::mask($this->password, '•', 0, max(mb_strlen($this->password) - 2, 4));
    }

    /**
     * The mail transport scheme implied by the chosen encryption.
     * SSL uses an implicit-TLS connection (smtps); everything else negotiates STARTTLS.
     */
    public function transportScheme(): ?string
    {
        return $this->encryption === 'ssl' ? 'smtps' : null;
    }

    protected function casts(): array
    {
        return [
            'port' => 'integer',
            'password' => 'encrypted',
            'is_active' => 'boolean',
        ];
    }
}
