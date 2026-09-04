<?php

namespace App\Models;

use Database\Factories\TestimonialFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'designation', 'company', 'review', 'rating', 'display_order', 'is_enabled'])]
class Testimonial extends Model
{
    /** @use HasFactory<TestimonialFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'display_order' => 'integer',
            'is_enabled' => 'boolean',
        ];
    }

    #[Scope]
    protected function enabled(Builder $query): void
    {
        $query->where('is_enabled', true);
    }

    /**
     * Order by the admin-defined position, then oldest first.
     */
    #[Scope]
    protected function ordered(Builder $query): void
    {
        $query->orderBy('display_order')->orderBy('created_at');
    }

    protected function roleLine(): Attribute
    {
        return Attribute::get(fn (): string => collect([$this->designation, $this->company])->filter()->implode(', '));
    }
}
