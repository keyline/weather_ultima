<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSiteSettingsRequest;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SiteSettingController extends Controller
{
    public function edit(): View
    {
        return view('admin.settings.site', ['settings' => SiteSetting::current()]);
    }

    public function update(UpdateSiteSettingsRequest $request): RedirectResponse
    {
        $settings = SiteSetting::current();

        $data = $request->safe()->only([
            'site_name',
            'contact_email',
            'contact_phone',
            'contact_address',
            'social_facebook',
            'social_instagram',
            'social_linkedin',
            'social_twitter',
            'social_youtube',
        ]);

        foreach (['header_logo', 'footer_logo', 'favicon'] as $field) {
            $column = $field.'_path';

            if ($request->hasFile($field)) {
                $this->deleteStoredFile($settings->{$column});
                $data[$column] = $request->file($field)->store('site', 'public');
            } elseif ($request->boolean('remove_'.$field)) {
                $this->deleteStoredFile($settings->{$column});
                $data[$column] = null;
            }
        }

        $settings->update($data);

        return back()->with('status', 'Website settings saved.');
    }

    private function deleteStoredFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
