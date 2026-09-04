<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\ServiceImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ServiceImage>
 */
class ServiceImageFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'service_id' => Service::factory(),
            'image' => 'services/'.fake()->unique()->numerify('img-###').'.png',
            'alt_text' => fake()->sentence(3),
            'display_order' => fake()->numberBetween(0, 10),
        ];
    }
}
