<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BillCurrencyInitialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('bill_currency_initial')->insert([
            [
                'bill_id' => rand(1, 2),
                'currency_id' => rand(1, 2),
                'amount' => rand(1, 100000),
            ],
            [
                'bill_id' => rand(3, 4),
                'currency_id' => rand(3, 4),
                'amount' => rand(1, 100000),
            ],
            [
                'bill_id' => rand(5, 6),
                'currency_id' => rand(5, 6),
                'amount' => rand(1, 100000),
            ],
            [
                'bill_id' => rand(7, 8),
                'currency_id' => rand(7, 8),
                'amount' => rand(1, 100000),
            ],
            [
                'bill_id' => rand(9, 10),
                'currency_id' => rand(9, 10),
                'amount' => rand(1, 100000),
            ],
        ]);
    }
}
