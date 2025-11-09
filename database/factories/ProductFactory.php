<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => \App\Models\Category::factory(),
            'SKU' => fake()->unique()->bothify('SKU-####-????'),
            'name' => fake()->words(3, true),
            'stock' => fake()->numberBetween(0, 100),
            'unit_name' => fake()->randomElement(['pcs', 'kg', 'liter', 'box', 'pack']),
            'price' => fake()->randomFloat(2, 1000, 1000000),
        ];
    }
}
