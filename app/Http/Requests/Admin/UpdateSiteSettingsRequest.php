<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSiteSettingsRequest extends FormRequest
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
        $logo = ['nullable', 'file', 'mimes:jpeg,jpg,png,webp,svg', 'max:2048'];
        $url = ['nullable', 'url:http,https', 'max:255'];

        return [
            'site_name' => ['required', 'string', 'max:120'],
            'contact_email' => ['required', 'email', 'max:255'],
            'contact_phone' => ['required', 'string', 'max:40'],
            'contact_address' => ['nullable', 'string', 'max:255'],
            'social_facebook' => $url,
            'social_instagram' => $url,
            'social_linkedin' => $url,
            'social_twitter' => $url,
            'social_youtube' => $url,
            'header_logo' => $logo,
            'footer_logo' => $logo,
            'favicon' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp,svg,ico', 'max:1024'],
            'remove_header_logo' => ['nullable', 'boolean'],
            'remove_footer_logo' => ['nullable', 'boolean'],
            'remove_favicon' => ['nullable', 'boolean'],
        ];
    }
}
