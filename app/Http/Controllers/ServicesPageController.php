<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServicePageSetting;
use Illuminate\View\View;

class ServicesPageController extends Controller
{
    public function show(): View
    {
        return view('services', [
            'page' => ServicePageSetting::current(),
            'services' => Service::query()->enabled()->ordered()->with('images')->get(),
        ]);
    }
}
