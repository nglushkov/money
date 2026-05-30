<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ExchangePlaceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->company(),
        ];
    }
}
