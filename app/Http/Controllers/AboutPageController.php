<?php

namespace App\Http\Controllers;

use App\Models\AboutPageSetting;
use App\Models\SiteSetting;
use Illuminate\View\View;

class AboutPageController extends Controller
{
    public function show(): View
    {
        return view('about', [
            'about' => AboutPageSetting::current()->pageContent(),
            'siteSettings' => SiteSetting::current(),
        ]);
    }
}
