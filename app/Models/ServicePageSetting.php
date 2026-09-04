<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['banner_title', 'intro_heading', 'intro_body', 'intro_statement'])]
class ServicePageSetting extends Model
{
    /**
     * The single Services-page content row, created on first access.
     */
    public static function current(): self
    {
        return static::query()->firstOrCreate();
    }

    /**
     * The intro body split into paragraphs on blank lines.
     *
     * @return list<string>
     */
    protected function introParagraphs(): Attribute
    {
        return Attribute::get(function (): array {
            $text = trim((string) $this->intro_body);

            return $text === '' ? [] : preg_split('/\r?\n\s*\r?\n/', $text);
        });
    }
}
