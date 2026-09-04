<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\VerifiesRecaptcha;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductEnquiryRequest extends FormRequest
{
    use VerifiesRecaptcha;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'message' => ['nullable', 'string', 'max:5000'],
            ...$this->recaptchaRules('product_enquiry'),
        ];
    }
}
