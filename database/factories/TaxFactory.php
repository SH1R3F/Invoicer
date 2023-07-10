<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tax>
 */
class TaxFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'    => fake()->unique()->name,
            'value'   => rand(1, 100),
            'type'    => fake()->randomElement(['fixed', 'percentage']),
            'default' => rand(0, 1)
        ];
    }
}
