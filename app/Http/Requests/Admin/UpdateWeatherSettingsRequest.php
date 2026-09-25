<?php

namespace App\Http\Requests\Admin;

use App\Models\WeatherSetting;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateWeatherSettingsRequest extends FormRequest
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
        $image = ['nullable', 'file', 'mimes:jpeg,jpg,png,webp', 'max:4096'];

        return [
            'kolkata_image' => $image,
            'deoghar_image' => $image,
            'sundarban_image' => $image,
            'bardhaman_image' => $image,
            'remove_kolkata_image' => ['nullable', 'boolean'],
            'remove_deoghar_image' => ['nullable', 'boolean'],
            'remove_sundarban_image' => ['nullable', 'boolean'],
            'remove_bardhaman_image' => ['nullable', 'boolean'],
            'application_key' => ['nullable', 'string', 'max:255'],
            'api_key' => ['nullable', 'string', 'max:255'],
            'kolkata_mac' => ['nullable', 'string', 'max:64'],
            'deoghar_mac' => ['nullable', 'string', 'max:64'],
            'sundarban_mac' => ['nullable', 'string', 'max:64'],
            'bardhaman_mac' => ['nullable', 'string', 'max:64'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'application_key' => 'application key',
            'api_key' => 'API key',
            'kolkata_mac' => 'Kolkata device MAC address',
            'deoghar_mac' => 'Deoghar device MAC address',
            'sundarban_mac' => 'Sundarban device MAC address',
            'bardhaman_mac' => 'Bardhaman device MAC address',
            'kolkata_image' => 'Kolkata station image',
            'deoghar_image' => 'Deoghar station image',
            'sundarban_image' => 'Sundarban station image',
            'bardhaman_image' => 'Bardhaman station image',
        ];
    }

    protected function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if (! $this->boolean('is_active')) {
                return;
            }

            $current = WeatherSetting::current();

            $applicationKeyPresent = filled($this->input('application_key')) || $current->hasApplicationKey();
            $apiKeyPresent = filled($this->input('api_key')) || $current->hasApiKey();

            if (! $applicationKeyPresent || ! $apiKeyPresent) {
                $validator->errors()->add('is_active', 'Add an application key and an API key before enabling live weather data.');
            }
        });
    }
}
