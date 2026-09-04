<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateRecaptchaSettingsRequest;
use App\Models\RecaptchaSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RecaptchaSettingController extends Controller
{
    public function edit(): View
    {
        return view('admin.settings.recaptcha', ['settings' => RecaptchaSetting::current()]);
    }

    public function update(UpdateRecaptchaSettingsRequest $request): RedirectResponse
    {
        $data = $request->safe()->except(['secret_key', 'is_active']);
        $data['is_active'] = $request->boolean('is_active');

        if (filled($request->input('secret_key'))) {
            $data['secret_key'] = $request->string('secret_key')->toString();
        }

        RecaptchaSetting::current()->update($data);

        return back()->with('status', 'Google reCAPTCHA configuration saved.');
    }
}
