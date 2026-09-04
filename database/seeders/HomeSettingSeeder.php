<?php

namespace Database\Seeders;

use App\Models\HomeSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class HomeSettingSeeder extends Seeder
{
    public function run(): void
    {
        $home = HomeSetting::current();

        $defaults = [
            'banner_title' => 'One Vision. Five Dimensions.',
            'banner_subtitle' => 'Explore our specialised solutions across weather intelligence, water, environmental monitoring, education and sustainable energy.',
            'founder_name' => 'Mr. Rabindra Goenka',
            'founder_designation' => '~ Founder & CEO',
            'founder_intro' => 'Mr. Rabindra Goenka, Founder & CEO of Weather Ultima, brings together a deep passion for meteorology with a commitment to making weather knowledge more accessible and meaningful.',
            'founder_description' => "As a weather forecaster, analyst and interpreter, his journey has been driven by a simple belief — understanding weather is not just about predicting what comes next, but about helping people prepare, adapt and make informed decisions.\n\nWith his experience in weather analysis and teaching Geography, Mr. Goenka continues to champion a more informed approach to understanding our atmosphere, climate and the forces that shape our everyday lives.",
        ];

        $defaults['founder_image_path'] = $this->copyAsset('owner_img.png', 'home/founder.png') ?? $home->founder_image_path;

        $home->forceFill(collect($defaults)->filter(fn ($value, $key) => blank($home->{$key}))->all())->save();
    }

    private function copyAsset(string $source, string $target): ?string
    {
        $path = public_path('material/images/'.$source);

        if (! File::exists($path)) {
            return null;
        }

        if (! Storage::disk('public')->exists($target)) {
            Storage::disk('public')->put($target, File::get($path));
        }

        return $target;
    }
}
