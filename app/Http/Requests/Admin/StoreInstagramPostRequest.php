<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreInstagramPostRequest extends FormRequest
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
            'image' => ['required', 'file', 'mimes:jpeg,jpg,png,webp', 'max:4096'],
            'link_url' => ['nullable', 'url:http,https', 'max:255'],
            'display_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_enabled' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['display_order' => $this->input('display_order', 0) ?: 0]);
    }
}
