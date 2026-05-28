<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\MercadoPagoMapping;
use Illuminate\Database\Seeder;

class MercadoPagoMappingSeeder extends Seeder
{
    public function run(): void
    {
        $entretenimiento = Category::firstOrCreate(['name' => 'Entretenimiento']);
        $transporte      = Category::firstOrCreate(['name' => 'Transporte']);
        $supermercado    = Category::firstOrCreate(['name' => 'Supermercado']);
        $internet        = Category::firstOrCreate(['name' => 'Internet']);
        $otros           = Category::firstOrCreate(['name' => 'Otros']);

        $mappings = [
            ['keyword' => 'netflix',    'category_id' => $entretenimiento->id, 'place_name' => 'Netflix'],
            ['keyword' => 'spotify',    'category_id' => $entretenimiento->id, 'place_name' => 'Spotify'],
            ['keyword' => 'apple',      'category_id' => $entretenimiento->id, 'place_name' => 'Apple'],
            ['keyword' => 'microsoft',  'category_id' => $entretenimiento->id, 'place_name' => 'Microsoft'],
            ['keyword' => 'google',     'category_id' => $entretenimiento->id, 'place_name' => 'Google'],
            ['keyword' => 'uber',       'category_id' => $transporte->id,      'place_name' => 'Uber'],
            ['keyword' => 'didi',       'category_id' => $transporte->id,      'place_name' => 'DiDi'],
            ['keyword' => 'subte',      'category_id' => $transporte->id,      'place_name' => 'Subte'],
            ['keyword' => 'jumbo',      'category_id' => $supermercado->id,    'place_name' => 'Jumbo'],
            ['keyword' => 'iplan',      'category_id' => $internet->id,        'place_name' => 'Iplan'],
            ['keyword' => '__default__', 'category_id' => $otros->id,          'place_name' => null, 'is_default' => true],
        ];

        foreach ($mappings as $mapping) {
            MercadoPagoMapping::firstOrCreate(
                ['keyword' => $mapping['keyword']],
                $mapping,
            );
        }
    }
}
