<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\MercadoPagoMapping;
use Illuminate\Database\Seeder;

class MercadoPagoMappingSeeder extends Seeder
{
    public function run(): void
    {
        $podpiski  = Category::firstOrCreate(['name' => 'Онлайн подписки']);
        $taxi      = Category::firstOrCreate(['name' => 'Такси']);
        $transport = Category::firstOrCreate(['name' => 'Общественный транспорт']);
        $produkty  = Category::firstOrCreate(['name' => 'Продукты']);
        $internet  = Category::firstOrCreate(['name' => 'Интернет']);
        $uslugi    = Category::firstOrCreate(['name' => 'Услуги']);

        $mappings = [
            ['keyword' => 'netflix',     'category_id' => $podpiski->id,  'place_name' => 'Netflix'],
            ['keyword' => 'spotify',     'category_id' => $podpiski->id,  'place_name' => 'Spotify'],
            ['keyword' => 'apple',       'category_id' => $podpiski->id,  'place_name' => 'Apple'],
            ['keyword' => 'microsoft',   'category_id' => $podpiski->id,  'place_name' => 'Microsoft'],
            ['keyword' => 'google',      'category_id' => $podpiski->id,  'place_name' => 'Google'],
            ['keyword' => 'uber',        'category_id' => $taxi->id,      'place_name' => 'Uber'],
            ['keyword' => 'didi',        'category_id' => $taxi->id,      'place_name' => 'DiDi'],
            ['keyword' => 'subte',       'category_id' => $transport->id, 'place_name' => 'Subte'],
            ['keyword' => 'jumbo',       'category_id' => $produkty->id,  'place_name' => 'Jumbo'],
            ['keyword' => 'iplan',       'category_id' => $internet->id,  'place_name' => 'Iplan'],
            ['keyword' => '__default__', 'category_id' => $uslugi->id,    'place_name' => null, 'is_default' => true],
        ];

        foreach ($mappings as $mapping) {
            MercadoPagoMapping::updateOrCreate(
                ['keyword' => $mapping['keyword']],
                $mapping,
            );
        }
    }
}
