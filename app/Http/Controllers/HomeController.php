<?php

namespace App\Http\Controllers;

use App\Models\BrandLogo;
use App\Models\CoreValue;
use App\Models\DimensionCard;
use App\Models\HomeSetting;
use App\Models\Testimonial;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        return view('home', [
            'home' => HomeSetting::current(),
            'dimensionCards' => DimensionCard::query()->enabled()->ordered()->get(),
            'brandLogos' => BrandLogo::query()->enabled()->ordered()->get(),
            'coreValues' => CoreValue::query()->enabled()->ordered()->get(),
            'testimonials' => Testimonial::query()->enabled()->ordered()->get(),
        ]);
    }
}
