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
            'Наличные',
            'Карта Сбер 0846',
            'Карта BankOld 6742',
        ];
        foreach ($names as $key => $name) {
            Bill::create([
                'name' => $name,
                'active' => $key === 0 ? true : false,
                'user_id' => User::inRandomOrder()->first()->id,
            ]);
        }
    }
}
