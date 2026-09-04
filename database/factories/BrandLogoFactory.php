<?php

namespace Database\Factories;

use App\Models\BrandLogo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BrandLogo>
 */
class BrandLogoFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'image' => 'brand-logos/'.fake()->unique()->numerify('logo-###').'.png',
            'alt_text' => fake()->company(),
            'display_order' => fake()->numberBetween(0, 20),
            'is_enabled' => true,
        ];
    }

    public function disabled(): static
    {
        return $this->state(fn (array $attributes): array => ['is_enabled' => false]);
    }
}
