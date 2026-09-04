<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHomeBannerRequest extends FormRequest
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
            'banner_title' => ['required', 'string', 'max:180'],
            'banner_subtitle' => ['nullable', 'string', 'max:500'],
        ];
    }
}
