<?php

namespace Database\Factories;

use App\Models\DimensionCard;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DimensionCard>
 */
class DimensionCardFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->unique()->words(2, true),
            'description' => fake()->sentence(8),
            'image' => null,
            'link_url' => null,
            'display_order' => fake()->numberBetween(0, 20),
            'is_enabled' => true,
        ];
    }

    public function disabled(): static
    {
        return $this->state(fn (array $attributes): array => ['is_enabled' => false]);
    }
}
