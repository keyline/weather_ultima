<?php

namespace Database\Seeders;

use App\Models\BrandLogo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class BrandLogoSeeder extends Seeder
{
    public function run(): void
    {
        $logos = [
            ['source' => 'brand-logo1.png', 'alt' => 'All India Radio'],
            ['source' => 'brand-logo2.png', 'alt' => 'Doordarshan'],
            ['source' => 'brand-logo3.png', 'alt' => 'Khobor Online'],
            ['source' => 'brand-logo4.png', 'alt' => 'The Indian Express'],
            ['source' => 'brand-logo5.png', 'alt' => 'Hindustan Times'],
            ['source' => 'brand-logo6.png', 'alt' => 'Aajkal'],
            ['source' => 'brand-logo7.png', 'alt' => 'Ganashakti'],
            ['source' => 'brand-logo8.png', 'alt' => 'News24'],
            ['source' => 'brand-logo12.png', 'alt' => 'ABP Ananda'],
            ['source' => 'brand-logo9.png', 'alt' => 'Rajasthan Patrika'],
            ['source' => 'brand-logo10.svg', 'alt' => 'Ei Samay'],
            ['source' => 'brand-logo11.svg', 'alt' => 'NDTV India'],
            ['source' => 'brand-logo13.png', 'alt' => 'Sangbad Pratidin'],
        ];

        foreach ($logos as $index => $logo) {
            $source = public_path('material/images/'.$logo['source']);

            if (! File::exists($source)) {
                continue;
            }

            $target = 'brand-logos/'.$logo['source'];

            if (! Storage::disk('public')->exists($target)) {
                Storage::disk('public')->put($target, File::get($source));
            }

            BrandLogo::query()->firstOrCreate(
                ['alt_text' => $logo['alt']],
                ['image' => $target, 'display_order' => $index + 1, 'is_enabled' => true],
            );
        }
    }
}
