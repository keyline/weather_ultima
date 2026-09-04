<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmailSettingsRequest extends FormRequest
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
            'contact_notification_email' => ['required', 'email', 'max:255'],
            'product_notification_email' => ['required', 'email', 'max:255'],
            'sender_name' => ['required', 'string', 'max:120'],
            'contact_subject' => ['required', 'string', 'max:180'],
            'product_subject' => ['required', 'string', 'max:180'],
            'contact_notifications_enabled' => ['nullable', 'boolean'],
            'product_notifications_enabled' => ['nullable', 'boolean'],
        ];
    }
}
