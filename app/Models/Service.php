<?php

namespace App\Models;

use Database\Factories\ServiceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable(['name', 'category', 'tags', 'statement', 'body', 'result', 'display_order', 'is_enabled'])]
class Service extends Model
{
    /** @use HasFactory<ServiceFactory> */
    use HasFactory;

    protected static function booted(): void
    {
        static::saving(function (Service $service): void {
            if (blank($service->slug) || $service->isDirty('name')) {
                $service->slug = $service->uniqueSlug();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'display_order' => 'integer',
            'is_enabled' => 'boolean',
        ];
    }

    #[Scope]
    protected function enabled(Builder $query): void
    {
        $query->where('is_enabled', true);
    }

    #[Scope]
    protected function ordered(Builder $query): void
    {
        $query->orderBy('display_order')->orderBy('created_at');
    }

    /**
     * @return HasMany<ServiceImage, $this>
     */
    public function images(): HasMany
    {
        return $this->hasMany(ServiceImage::class)->orderBy('display_order')->orderBy('id');
    }

    /**
     * The body split into paragraphs on blank lines.
     *
     * @return list<string>
     */
    protected function bodyParagraphs(): Attribute
    {
        return Attribute::get(function (): array {
            $text = trim((string) $this->body);

            return $text === '' ? [] : preg_split('/\r?\n\s*\r?\n/', $text);
        });
    }

    private function uniqueSlug(): string
    {
        $base = Str::slug((string) $this->name) ?: 'service';
        $slug = $base;
        $suffix = 1;

        while (
            static::query()
                ->where('slug', $slug)
                ->when($this->exists, fn (Builder $query) => $query->whereKeyNot($this->getKey()))
                ->exists()
        ) {
            $slug = $base.'-'.(++$suffix);
        }

        return $slug;
    }
}
