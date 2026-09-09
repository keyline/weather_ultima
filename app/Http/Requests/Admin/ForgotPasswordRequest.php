<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Concerns\VerifiesRecaptcha;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ForgotPasswordRequest extends FormRequest
{
    use VerifiesRecaptcha;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'max:255'],
            ...$this->recaptchaRules('forgot_password'),
        ];
    }
}
