<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductEnquiry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductEnquiry>
 */
class ProductEnquiryFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $product = Product::factory();

        return [
            'product_id' => $product,
            'product_name' => fn (array $attributes): string => Product::find($attributes['product_id'])?->name ?? fake()->words(2, true),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->optional()->numerify('+91 ##########'),
            'message' => fake()->paragraph(),
            'created_at' => fake()->dateTimeBetween('-2 months'),
        ];
    }

    public function forProduct(Product $product): static
    {
        return $this->state(fn (array $attributes): array => [
            'product_id' => $product->id,
            'product_name' => $product->name,
        ]);
    }
}
