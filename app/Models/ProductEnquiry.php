<?php

namespace App\Models;

use Database\Factories\ProductEnquiryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['product_id', 'product_name', 'name', 'email', 'phone', 'message'])]
class ProductEnquiry extends Model
{
    /** @use HasFactory<ProductEnquiryFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return ['is_read' => 'boolean'];
    }

    #[Scope]
    protected function unread(Builder $query): void
    {
        $query->where('is_read', false);
    }

    /**
     * @return BelongsTo<Product, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
