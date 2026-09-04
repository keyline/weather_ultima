<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceImagesRequest extends FormRequest
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
            'images' => ['nullable', 'array'],
            'images.*.id' => ['required', 'integer', 'exists:service_images,id'],
            'images.*.display_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'images.*.alt_text' => ['nullable', 'string', 'max:200'],
        ];
    }
}
