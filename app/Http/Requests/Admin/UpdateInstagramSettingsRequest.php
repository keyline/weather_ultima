<?php

namespace App\Http\Requests\Admin;

use App\Models\InstagramSetting;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateInstagramSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'embed_code' => ['nullable', 'string', 'max:20000'],
            'access_token' => ['nullable', 'string', 'max:4096'],
            'instagram_user_id' => ['nullable', 'string', 'max:64'],
            'token_expires_at' => ['nullable', 'date'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'access_token' => 'access token',
            'instagram_user_id' => 'Instagram Business Account ID',
            'token_expires_at' => 'token expiry date',
        ];
    }

    protected function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if (! $this->boolean('is_active')) {
                return;
            }

            $current = InstagramSetting::current();

            $tokenPresent = filled($this->input('access_token')) || $current->hasAccessToken();
            $userIdPresent = filled($this->input('instagram_user_id')) || $current->hasUserId();

            if (! $tokenPresent || ! $userIdPresent) {
                $validator->errors()->add('is_active', 'Add an access token and an Instagram Business Account ID before enabling the live feed.');
            }
        });
    }
}
