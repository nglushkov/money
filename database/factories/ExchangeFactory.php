<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Exchange>
 */
class ExchangeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'from_currency_id' => rand(1, 5),
            'amount_from' => $this->faker->randomFloat(2, 1, 1000),
            'to_currency_id' => rand(6, 10),
            'amount_to' => $this->faker->randomFloat(2, 1, 1000),
            'bill_id' => rand(1, 10),
            'date' => $this->faker->dateTimeThisYear()->format('Y-m-d'),
            'user_id' => rand(1, 10),
            'notes' => rand(1, 5) === 1 ? $this->faker->text(rand(10, 150)) : '',
        ];
    }
}
