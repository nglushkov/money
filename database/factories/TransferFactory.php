<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transfer>
 */
class TransferFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'from_bill_id' => $this->faker->numberBetween(1, 10),
            'to_bill_id' => $this->faker->numberBetween(1, 10),
            'amount' => $this->faker->randomFloat(2, 1, 1000),
            'currency_id' => $this->faker->numberBetween(1, 10),
            'date' => $this->faker->dateTimeThisYear(),
            'user_id' => $this->faker->numberBetween(1, 10),
        ];
    }
}
