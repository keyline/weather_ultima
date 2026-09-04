<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'short_description', 'image', 'is_active'])]
class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    /**
     * The fallback image used when a product has no uploaded image.
     */
    public const PLACEHOLDER_IMAGE = 'material/images/product_img1.png';

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('is_active', true);
    }

    /**
     * @return HasMany<ProductEnquiry, $this>
     */
    public function productEnquiries(): HasMany
    {
        return $this->hasMany(ProductEnquiry::class);
    }

    protected function imageUrl(): Attribute
    {
        return Attribute::get(fn (): string => $this->image
            ? asset('storage/'.$this->image)
            : asset(self::PLACEHOLDER_IMAGE));
    }
}
