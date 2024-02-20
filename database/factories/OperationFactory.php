<?php

namespace Database\Factories;

use App\Models\Bill;
use App\Models\Category;
use App\Models\Currency;
use App\Models\Place;
use App\Models\User;
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
            'bill_id' => Bill::inRandomOrder()->first()->id,
            'category_id' => Category::inRandomOrder()->first()->id,
            'currency_id' => Currency::inRandomOrder()->first()->id,
            'place_id' => Place::inRandomOrder()->first()->id,
            'user_id' => User::inRandomOrder()->first()->id,
            'notes' => rand(1, 5) === 1 ? $this->faker->text(rand(10, 150)) : '',
            'date' => $this->faker->dateTimeThisYear()->format('Y-m-d'),
        ];
    }
}
