<?php

namespace App\Http\Controllers;

use App\Models\BrandLogo;
use App\Models\CoreValue;
use App\Models\DimensionCard;
use App\Models\HomeSetting;
use App\Models\InstagramSetting;
use App\Models\Testimonial;
use App\Models\WeatherSetting;
use App\Services\InstagramFeedService;
use App\Services\WeatherStationService;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(WeatherStationService $weatherStation, InstagramFeedService $instagramFeed): View
    {
        $statsVideoPath = (string) config('homepage.stats_video_path');
        $publicDisk = Storage::disk('public');
        $statsVideoUrl = $publicDisk->exists($statsVideoPath)
            ? route('storage.file', ['path' => $statsVideoPath, 'v' => $publicDisk->lastModified($statsVideoPath)])
            : null;

        return view('home', [
            'home' => HomeSetting::current(),
            'statsVideoUrl' => $statsVideoUrl,
            'dimensionCards' => DimensionCard::query()->enabled()->ordered()->get(),
            'brandLogos' => BrandLogo::query()->enabled()->ordered()->get(),
            'coreValues' => CoreValue::query()->enabled()->ordered()->get(),
            'instagramEmbedCode' => InstagramSetting::current()->embed_code,
            'instagramPosts' => $instagramFeed->homepageItems(10),
            'testimonials' => Testimonial::query()->enabled()->ordered()->get(),
            'weatherStations' => $weatherStation->stations(),
            'weatherStationImages' => WeatherSetting::current()->imageUrls(),
        ]);
    }
}
