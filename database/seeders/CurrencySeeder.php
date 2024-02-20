<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $names = [
            'USD',
            'KZT',
            'RUB',
            'EUR',
            'ARS',
        ];
        foreach ($names as $name) {
            Currency::factory()->create([
                'name' => $name,
                'is_default' => $name === 'USD',
            ]);
        }
    }
}
