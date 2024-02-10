<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Operation>
 */
class OperationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'amount' => $this->faker->randomFloat(2, 1, 100000),
            'type' => $this->faker->randomElement([0, 1]), // 0 - expense, 1 - income
            'bill_id' => rand(1, 10),
            'category_id' => rand(1, 50),
            'currency_id' => rand(1, 10),
            'place_id' => rand(1, 100),
            'user_id' => rand(1, 10),
            'notes' => $this->faker->text(100),
            'date' => $this->faker->dateTime(),
        ];
    }
}
