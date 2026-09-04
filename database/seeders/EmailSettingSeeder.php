<?php

namespace Database\Seeders;

use App\Models\EmailSetting;
use Illuminate\Database\Seeder;

class EmailSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = EmailSetting::current();

        // Backfill product-enquiry fields for settings rows created before they existed.
        $settings->forceFill([
            'product_notification_email' => $settings->product_notification_email ?: $settings->contact_notification_email ?: 'info@weather.com',
            'product_subject' => $settings->product_subject ?: 'New website product enquiry',
        ])->save();
    }
}
