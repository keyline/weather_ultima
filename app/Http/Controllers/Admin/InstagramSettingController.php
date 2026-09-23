<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateInstagramSettingsRequest;
use App\Models\InstagramSetting;
use App\Services\InstagramFeedService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class InstagramSettingController extends Controller
{
    public function edit(): View
    {
        return view('admin.settings.instagram', ['settings' => InstagramSetting::current()]);
    }

    public function update(UpdateInstagramSettingsRequest $request, InstagramFeedService $instagramFeed): RedirectResponse
    {
        $data = $request->safe()->except(['access_token', 'instagram_user_id', 'is_active']);
        $data['is_active'] = $request->boolean('is_active');

        if (filled($request->input('access_token'))) {
            $data['access_token'] = $request->string('access_token')->toString();
        }

        if (filled($request->input('instagram_user_id'))) {
            $data['instagram_user_id'] = $request->string('instagram_user_id')->toString();
        }

        InstagramSetting::current()->update($data);
        $instagramFeed->flush();

        return back()->with('status', 'Instagram feed configuration saved.');
    }

    public function test(InstagramFeedService $instagramFeed): RedirectResponse
    {
        $result = $instagramFeed->probe();

        if (! $result['ok']) {
            return back()->with('instagram_test_error', $result['message']);
        }

        return back()->with('status', $result['message']);
    }
}
