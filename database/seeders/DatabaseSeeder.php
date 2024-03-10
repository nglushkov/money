<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $seeders = [
            UserSeeder::class,
            BillSeeder::class,
            CurrencySeeder::class,
        ];
        if (app()->environment('testing')) {
            $seeders = array_merge($seeders, [
                CategorySeeder::class,
                PlaceSeeder::class,
            ]);
        }
        $this->call($seeders);
    }
}
