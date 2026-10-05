<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateAboutPageRequest;
use App\Models\AboutPageSetting;
use App\Services\AboutPageContentUpdater;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AboutPageController extends Controller
{
    private const SECTION_TITLES = [
        'banner-intro' => 'Banner & Introduction',
        'mission' => 'Mission',
        'vision' => 'Vision',
        'founder' => 'Founder story',
        'team' => 'Team members',
        'northstar' => 'Northstar people',
    ];

    public function edit(Request $request): View
    {
        $section = $request->query('section', 'banner-intro');
        abort_unless(is_string($section) && isset(self::SECTION_TITLES[$section]), 404);

        return view('admin.about.edit', [
            'content' => AboutPageSetting::current()->pageContent(),
            'selectedSection' => $section,
            'sectionTitle' => self::SECTION_TITLES[$section],
        ]);
    }

    public function update(UpdateAboutPageRequest $request, AboutPageContentUpdater $updater): RedirectResponse
    {
        $updater->update($request, AboutPageSetting::current());

        return back()->with('status', 'About section saved.');
    }
}
