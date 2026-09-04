<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateServicePageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'banner_title' => ['nullable', 'string', 'max:120'],
            'intro_heading' => ['nullable', 'string', 'max:500'],
            'intro_body' => ['nullable', 'string', 'max:4000'],
            'intro_statement' => ['nullable', 'string', 'max:255'],
        ];
    }
}
