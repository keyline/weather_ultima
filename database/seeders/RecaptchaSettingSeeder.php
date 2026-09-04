<?php

namespace Database\Seeders;

use App\Models\RecaptchaSetting;
use Illuminate\Database\Seeder;

class RecaptchaSettingSeeder extends Seeder
{
    public function run(): void
    {
        // Creates the single inactive row; the admin adds keys from the panel.
        RecaptchaSetting::current();
    }
}
