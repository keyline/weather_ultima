<?php

namespace Database\Seeders;

use App\Models\DimensionCard;
use Illuminate\Database\Seeder;

class DimensionCardSeeder extends Seeder
{
    public function run(): void
    {
        $cards = [
            ['title' => 'SkyWatch Live', 'description' => 'Sports • Agriculture • Mountaineering • Outdoor Operations'],
            ['title' => 'WaterSphere', 'description' => 'Treatment • Purification • Water Quality • Sustainable Water Solutions'],
            ['title' => 'StationCraft', 'description' => 'Installation • Monitoring • Instruments • Data'],
            ['title' => 'WeatherWise Academy', 'description' => 'Seminars • Workshops • Classes • Hands-on Weather Learning'],
            ['title' => 'SolarSphere', 'description' => 'Solar Technology • Products • Systems • Sustainable Energy'],
        ];

        foreach ($cards as $index => $card) {
            DimensionCard::query()->firstOrCreate(
                ['title' => $card['title']],
                [
                    'description' => $card['description'],
                    'link_url' => null,
                    'display_order' => $index + 1,
                    'is_enabled' => true,
                ],
            );
        }
    }
}
