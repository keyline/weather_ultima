<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            EmailSettingSeeder::class,
            SmtpSettingSeeder::class,
            BrevoSettingSeeder::class,
            RecaptchaSettingSeeder::class,
            SiteSettingSeeder::class,
            HomeSettingSeeder::class,
            DimensionCardSeeder::class,
            BrandLogoSeeder::class,
            CoreValueSeeder::class,
            ProductSeeder::class,
            TestimonialSeeder::class,
            ServicePageSettingSeeder::class,
            ServiceSeeder::class,
        ]);
    }
}
