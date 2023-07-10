<?php

namespace Database\Factories;

use App\Enums\QuoteStatus;
use App\Models\Quote;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Quote>
 */
class QuoteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id'        => User::factory()->create()->id,
            'quote_number'   => str_pad(((string)(fake()->unique()->randomNumber(3)) + 1), 4, '0', STR_PAD_LEFT),
            'quote_date'     => fake()->date,
            'due_date'       => fake()->date,
            'status'         => fake()->randomElement(QuoteStatus::cases()),
            'discount_type'  => $type = rand(0, 1) ? fake()->randomElement(['fixed', 'percentage']) : null,
            'discount_value' => $type ? rand(1, 100) : 0,
            'notes'          => rand(0, 1) ? fake()->paragraph(rand(1, 7)) : null
        ];
    }
}
