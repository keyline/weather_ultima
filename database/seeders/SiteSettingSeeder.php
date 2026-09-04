<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = SiteSetting::query()->firstOrCreate([], [
            'site_name' => 'Weather Ultima',
            'contact_email' => 'weatherultima@gmail.com',
            'contact_phone' => '+91 8910296427',
            'contact_address' => 'Kolkata, West Bengal, India',
        ]);

        if (blank($settings->site_name)) {
            $settings->forceFill(['site_name' => 'Weather Ultima'])->save();
        }
    }
}
