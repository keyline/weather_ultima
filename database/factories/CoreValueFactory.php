<?php

namespace Database\Factories;

use App\Models\CoreValue;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CoreValue>
 */
class CoreValueFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'icon' => strtoupper(fake()->randomLetter()),
            'title' => fake()->unique()->word(),
            'description' => fake()->sentence(16),
            'display_order' => fake()->numberBetween(0, 20),
            'is_enabled' => true,
        ];
    }

    public function disabled(): static
    {
        return $this->state(fn (array $attributes): array => ['is_enabled' => false]);
    }
}
