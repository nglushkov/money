<?php

namespace Database\Seeders;

use App\Models\Bill;
use App\Models\User;
use Illuminate\Database\Seeder;

class BillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $names = [
            'Cash',
            'Card Another Bank 0846',
            'Card BankOld 6742',
        ];
        foreach ($names as $key => $name) {
            Bill::create([
                'name' => $name,
                'default' => $key === 0,
                'user_id' => User::inRandomOrder()->first()->id,
            ]);
        }
    }
}
