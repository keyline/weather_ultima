<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

#[Fillable([
    'access_token',
    'instagram_user_id',
    'username',
    'token_expires_at',
    'is_active',
])]
class InstagramSetting extends Model
{
    /**
     * @return array<string, mixed>
     */
    public static function defaults(): array
    {
        return [
            'access_token' => null,
            'instagram_user_id' => null,
            'username' => null,
            'token_expires_at' => null,
            'is_active' => false,
        ];
    }

    public static function current(): self
    {
        return static::query()->firstOrCreate([], static::defaults());
    }

    public function hasAccessToken(): bool
    {
        return filled($this->access_token);
    }

    public function hasUserId(): bool
    {
        return filled($this->instagram_user_id);
    }

    /**
     * Both the access token and the Instagram Business Account ID are present.
     */
    public function hasCredentials(): bool
    {
        return $this->hasAccessToken() && $this->hasUserId();
    }

    /**
     * Live posts should be fetched and rendered — active and fully credentialed.
     */
    public function isConfigured(): bool
    {
        return $this->is_active && $this->hasCredentials();
    }

    /**
     * The saved long-lived token is expired or will expire within a week —
     * shown as a renewal warning on the admin settings page.
     */
    public function tokenExpiresSoon(): bool
    {
        return $this->token_expires_at !== null && now()->addDays(7)->greaterThanOrEqualTo($this->token_expires_at);
    }

    /**
     * A safe-to-render hint of the stored access token (last 4 characters only).
     */
    public function maskedAccessToken(): ?string
    {
        if (! $this->hasAccessToken()) {
            return null;
        }

        return Str::mask($this->access_token, '•', 0, max(mb_strlen($this->access_token) - 4, 4));
    }

    protected function casts(): array
    {
        return [
            'access_token' => 'encrypted',
            'token_expires_at' => 'date',
            'is_active' => 'boolean',
        ];
    }
}
