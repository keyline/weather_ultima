<?php

namespace Database\Factories;

use App\Models\InstagramPost;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InstagramPost>
 */
class InstagramPostFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'image' => 'instagram-posts/'.fake()->unique()->numerify('post-###').'.png',
            'link_url' => fake()->optional()->url(),
            'display_order' => fake()->numberBetween(0, 20),
            'is_enabled' => true,
        ];
    }

    public function disabled(): static
    {
        return $this->state(fn (array $attributes): array => ['is_enabled' => false]);
    }
}
