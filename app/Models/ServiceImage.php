<?php

namespace App\Models;

use Database\Factories\ServiceImageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['image', 'alt_text', 'display_order'])]
class ServiceImage extends Model
{
    /** @use HasFactory<ServiceImageFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return ['display_order' => 'integer'];
    }

    /**
     * @return BelongsTo<Service, $this>
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    protected function imageUrl(): Attribute
    {
        return Attribute::get(fn (): string => asset('storage/'.$this->image));
    }
}
