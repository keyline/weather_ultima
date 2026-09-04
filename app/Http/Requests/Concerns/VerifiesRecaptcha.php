<?php

namespace App\Http\Requests\Concerns;

use App\Models\RecaptchaSetting;
use App\Rules\Recaptcha;

/**
 * Adds an optional reCAPTCHA check to a FormRequest. The token field is
 * normalised to a string so the rule always runs (and can fail when the
 * token is missing), and the rule itself is a no-op while reCAPTCHA is off.
 */
trait VerifiesRecaptcha
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'g-recaptcha-response' => (string) $this->input('g-recaptcha-response', ''),
        ]);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    protected function recaptchaRules(?string $action = null): array
    {
        if (! RecaptchaSetting::current()->isEnforced()) {
            return [];
        }

        return ['g-recaptcha-response' => [new Recaptcha($action)]];
    }
}
