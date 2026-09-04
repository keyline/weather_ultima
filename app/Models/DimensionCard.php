<?php

namespace App\Models;

use Database\Factories\DimensionCardFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['title', 'description', 'image', 'link_url', 'display_order', 'is_enabled'])]
class DimensionCard extends Model
{
    /** @use HasFactory<DimensionCardFactory> */
    use HasFactory;

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

    protected function imageUrl(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->image ? asset('storage/'.$this->image) : null);
    }
}
