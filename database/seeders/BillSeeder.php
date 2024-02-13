<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // \App\Models\Bill::factory(10)->create();
        \App\Models\Bill::create([
            'name' => 'Cash',
            'notes' => 'Cash in wallet',
            'user_id' => 1,
        ]);
    }
}
