<?php

namespace Database\Seeders;

use App\Models\SmtpSetting;
use Illuminate\Database\Seeder;

class SmtpSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = SmtpSetting::current();

        // Prefill from the environment so the admin has a starting point.
        // Left inactive on purpose — the .env config keeps working until an admin turns it on.
        $envUsername = (string) config('mail.mailers.smtp.username');
        $envPassword = (string) config('mail.mailers.smtp.password');

        $settings->fill([
            'host' => (string) config('mail.mailers.smtp.host') ?: $settings->host,
            'port' => (int) config('mail.mailers.smtp.port') ?: $settings->port,
            'username' => $envUsername !== '' && $envUsername !== 'null' ? $envUsername : $settings->username,
            'from_address' => (string) config('mail.from.address') ?: $settings->from_address,
            'from_name' => (string) config('mail.from.name') ?: $settings->from_name,
        ]);

        if ($envPassword !== '' && $envPassword !== 'null' && ! $settings->hasPassword()) {
            $settings->password = $envPassword;
        }

        $settings->save();
    }
}
