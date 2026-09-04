<?php

namespace App\Rules;

use App\Models\RecaptchaSetting;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;
use Illuminate\Translation\PotentiallyTranslatedString;

class Recaptcha implements ValidationRule
{
    private const VERIFY_URL = 'https://www.google.com/recaptcha/api/siteverify';

    /**
     * Run even when the token field is empty so a missing challenge is caught.
     */
    public bool $implicit = true;

    public function __construct(private readonly ?string $action = null) {}

    /**
     * @param  Closure(string): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $settings = RecaptchaSetting::current();

        if (! $settings->isEnforced()) {
            return;
        }

        if (blank($value)) {
            $fail('Please complete the reCAPTCHA verification.');

            return;
        }

        $body = Http::asForm()
            ->post(self::VERIFY_URL, [
                'secret' => $settings->secret_key,
                'response' => $value,
                'remoteip' => request()->ip(),
            ])
            ->json();

        if (! is_array($body) || ($body['success'] ?? false) !== true) {
            $fail('reCAPTCHA verification failed. Please try again.');

            return;
        }

        if ($settings->isV3()) {
            if ((float) ($body['score'] ?? 0) < $settings->minimum_score) {
                $fail('reCAPTCHA verification failed. Please try again.');

                return;
            }

            if ($this->action !== null && isset($body['action']) && $body['action'] !== $this->action) {
                $fail('reCAPTCHA verification failed. Please try again.');
            }
        }
    }
}
