<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:150'],
            'category' => ['nullable', 'string', 'max:255'],
            'tags' => ['nullable', 'string', 'max:255'],
            'statement' => ['nullable', 'string', 'max:1000'],
            'body' => ['required', 'string', 'max:8000'],
            'result' => ['nullable', 'string', 'max:1000'],
            'display_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_enabled' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['display_order' => $this->input('display_order', 0) ?: 0]);
    }
}
