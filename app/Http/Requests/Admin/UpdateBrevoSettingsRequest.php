<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBrevoSettingsRequest extends FormRequest
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
            'api_key' => ['nullable', 'string', 'max:255'],
            'sender_name' => ['required', 'string', 'max:120'],
            'sender_email' => ['required', 'email', 'max:255'],
            'reply_to_email' => ['nullable', 'email', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'api_key' => 'API key',
            'sender_name' => 'sender name',
            'sender_email' => 'sender email',
            'reply_to_email' => 'reply-to email',
        ];
    }
}
