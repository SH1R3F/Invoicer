<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Support\Str;
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
            'sku' => Str::random(8),
            'name' => fake()->sentence,
            'description' => fake()->paragraph(3),
            'category_id' => Category::factory()->create()->id,
            'price' => rand(100, 1000)
        ];
    }
}
