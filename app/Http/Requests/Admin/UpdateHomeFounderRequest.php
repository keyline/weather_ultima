<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHomeFounderRequest extends FormRequest
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
            'founder_name' => ['required', 'string', 'max:150'],
            'founder_designation' => ['nullable', 'string', 'max:150'],
            'founder_intro' => ['required', 'string', 'max:1000'],
            'founder_description' => ['nullable', 'string', 'max:4000'],
            'founder_image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
            'founder_signature' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp,svg', 'max:1024'],
            'remove_founder_image' => ['nullable', 'boolean'],
            'remove_founder_signature' => ['nullable', 'boolean'],
        ];
    }
}
