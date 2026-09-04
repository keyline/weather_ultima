<?php

namespace App\Http\Requests\Admin;

use App\Models\RecaptchaSetting;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateRecaptchaSettingsRequest extends FormRequest
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
            'site_key' => ['nullable', 'string', 'max:255'],
            'secret_key' => ['nullable', 'string', 'max:255'],
            'version' => ['required', Rule::in(array_keys(RecaptchaSetting::VERSIONS))],
            'minimum_score' => ['required', 'numeric', 'between:0,1'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'site_key' => 'site key',
            'secret_key' => 'secret key',
            'minimum_score' => 'minimum score',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'is_active.accepted' => 'Add a site key and secret key before enabling reCAPTCHA.',
        ];
    }

    protected function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if (! $this->boolean('is_active')) {
                return;
            }

            $secretPresent = filled($this->input('secret_key')) || RecaptchaSetting::current()->hasSecretKey();

            if (blank($this->input('site_key')) || ! $secretPresent) {
                $validator->errors()->add('is_active', 'Add a site key and secret key before enabling reCAPTCHA.');
            }
        });
    }
}
