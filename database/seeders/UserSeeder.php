<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //User::factory(10)->create();
        User::create([
            'name' => 'Default User',
            'email' => 'default@example.com',
            'email_verified_at' => now(),
            'password' => 'password',
            'remember_token' => Str::random(10),
        ]);
    }
}
