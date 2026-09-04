<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'site_name',
    'header_logo_path',
    'footer_logo_path',
    'favicon_path',
    'contact_email',
    'contact_phone',
    'contact_address',
    'social_facebook',
    'social_instagram',
    'social_linkedin',
    'social_twitter',
    'social_youtube',
])]
class SiteSetting extends Model
{
    /**
     * The fallback logo bundled with the theme.
     */
    public const DEFAULT_LOGO = 'material/images/logo.png';

    /**
     * The social networks that can be configured, keyed by their column name.
     *
     * @var array<string, array{icon: string, label: string}>
     */
    public const SOCIAL_NETWORKS = [
        'social_facebook' => ['icon' => 'fa-brands fa-facebook-f', 'label' => 'Facebook'],
        'social_instagram' => ['icon' => 'fa-brands fa-instagram', 'label' => 'Instagram'],
        'social_linkedin' => ['icon' => 'fa-brands fa-linkedin-in', 'label' => 'LinkedIn'],
        'social_twitter' => ['icon' => 'fa-brands fa-x-twitter', 'label' => 'X'],
        'social_youtube' => ['icon' => 'fa-brands fa-youtube', 'label' => 'YouTube'],
    ];

    /**
     * The single settings row, creating an empty one on first access.
     */
    public static function current(): self
    {
        return static::query()->firstOrCreate();
    }

    protected function headerLogoUrl(): Attribute
    {
        return Attribute::get(fn (): string => $this->logoUrl($this->header_logo_path));
    }

    protected function footerLogoUrl(): Attribute
    {
        return Attribute::get(fn (): string => $this->logoUrl($this->footer_logo_path));
    }

    protected function faviconUrl(): Attribute
    {
        return Attribute::get(fn (): string => $this->favicon_path
            ? asset('storage/'.$this->favicon_path)
            : asset(self::DEFAULT_LOGO));
    }

    protected function displayName(): Attribute
    {
        return Attribute::get(fn (): string => $this->site_name ?: 'Weather Ultima');
    }

    /**
     * @return list<array{key: string, url: string, icon: string, label: string}>
     */
    protected function socialLinks(): Attribute
    {
        return Attribute::get(function (): array {
            $links = [];

            foreach (self::SOCIAL_NETWORKS as $column => $meta) {
                if (filled($this->{$column})) {
                    $links[] = ['key' => $column, 'url' => $this->{$column}, 'icon' => $meta['icon'], 'label' => $meta['label']];
                }
            }

            return $links;
        });
    }

    private function logoUrl(?string $path): string
    {
        return $path ? asset('storage/'.$path) : asset(self::DEFAULT_LOGO);
    }
}
