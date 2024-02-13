<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // \App\Models\Currency::factory(10)->create();
        $names = [
            'USD',
            'KZT',
            'RUB',
            'EUR',
            'ARS',
        ];
        foreach ($names as $name) {
            \App\Models\Currency::factory()->create([
                'name' => $name,
                'is_default' => $name === 'USD',
            ]);
        }
    }
}
