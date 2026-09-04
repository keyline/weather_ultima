<?php

namespace Database\Seeders;

use App\Models\BrevoSetting;
use Illuminate\Database\Seeder;

class BrevoSettingSeeder extends Seeder
{
    public function run(): void
    {
        // Creates the single inactive row; the admin fills it in from the panel.
        $settings = BrevoSetting::current();

        $settings->fill([
            'sender_email' => (string) config('mail.from.address') ?: $settings->sender_email,
            'sender_name' => (string) config('mail.from.name') ?: $settings->sender_name,
        ])->save();
    }
}
