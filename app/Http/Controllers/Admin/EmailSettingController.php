<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateEmailSettingsRequest;
use App\Models\EmailSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EmailSettingController extends Controller
{
    public function edit(): View
    {
        return view('admin.settings.email', ['settings' => EmailSetting::current()]);
    }

    public function update(UpdateEmailSettingsRequest $request): RedirectResponse
    {
        EmailSetting::current()->update($request->safe()->except([
            'contact_notifications_enabled',
            'product_notifications_enabled',
        ]) + [
            'contact_notifications_enabled' => $request->boolean('contact_notifications_enabled'),
            'product_notifications_enabled' => $request->boolean('product_notifications_enabled'),
        ]);

        return back()->with('status', 'Email notification settings saved.');
    }
}
