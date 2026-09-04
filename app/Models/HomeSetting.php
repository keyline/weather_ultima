<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'banner_title',
    'banner_subtitle',
    'founder_name',
    'founder_designation',
    'founder_intro',
    'founder_description',
    'founder_image_path',
    'founder_signature_path',
])]
class HomeSetting extends Model
{
    /**
     * The single homepage-content row, created on first access.
     */
    public static function current(): self
    {
        return static::query()->firstOrCreate();
    }

    protected function founderImageUrl(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->founder_image_path ? asset('storage/'.$this->founder_image_path) : null);
    }

    protected function founderSignatureUrl(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->founder_signature_path ? asset('storage/'.$this->founder_signature_path) : null);
    }

    /**
     * The founder description split into paragraphs on blank lines.
     *
     * @return list<string>
     */
    protected function founderParagraphs(): Attribute
    {
        return Attribute::get(function (): array {
            $text = trim((string) $this->founder_description);

            return $text === '' ? [] : preg_split('/\r?\n\s*\r?\n/', $text);
        });
    }
}
