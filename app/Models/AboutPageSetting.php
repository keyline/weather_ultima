<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['content'])]
class AboutPageSetting extends Model
{
    protected function casts(): array
    {
        return ['content' => 'array'];
    }

    /**
     * Return the saved About content or the original page content on first edit.
     *
     * @return array<string, mixed>
     */
    public function pageContent(): array
    {
        return $this->content ?? config('about');
    }

    public static function current(): self
    {
        return static::query()->first() ?? new static(['content' => config('about')]);
    }
}
