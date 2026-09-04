<?php

namespace Database\Seeders;

use App\Models\ServicePageSetting;
use Illuminate\Database\Seeder;

class ServicePageSettingSeeder extends Seeder
{
    public function run(): void
    {
        $page = ServicePageSetting::current();

        $defaults = [
            'banner_title' => 'Services',
            'intro_heading' => "Science Above Us. Technology Around Us.\nSolutions For What Comes Next.",
            'intro_body' => implode("\n\n", [
                'Weather affects far more than what we wear or whether we carry an umbrella. It influences the food we grow, the games we play, the journeys we take, the energy we generate, the water we manage and the environment we leave behind.',
                'At Weather Ultima, we connect meteorological science with technology and real-world needs.',
                'From forecasting and weather stations to solar energy, meteorological consulting, education, environmental solutions and water management, every Weather Ultima service has one thing in common:',
            ]),
            'intro_statement' => 'We measure. We understand. We help you act.',
        ];

        $page->forceFill(collect($defaults)->filter(fn ($value, $key) => blank($page->{$key}))->all())->save();
    }
}
