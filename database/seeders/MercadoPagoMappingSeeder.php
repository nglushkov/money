<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\MercadoPagoMapping;
use App\Models\Place;
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

        $netflix   = Place::firstOrCreate(['name' => 'Netflix']);
        $spotify   = Place::firstOrCreate(['name' => 'Spotify']);
        $apple     = Place::firstOrCreate(['name' => 'Apple']);
        $microsoft = Place::firstOrCreate(['name' => 'Microsoft']);
        $google    = Place::firstOrCreate(['name' => 'Google']);
        $uber      = Place::firstOrCreate(['name' => 'Uber']);
        $didi      = Place::firstOrCreate(['name' => 'DiDi']);
        $subte     = Place::firstOrCreate(['name' => 'Subte']);
        $jumbo     = Place::firstOrCreate(['name' => 'Jumbo']);
        $iplan     = Place::firstOrCreate(['name' => 'Iplan']);

        $mappings = [
            ['keyword' => 'netflix',   'category_id' => $podpiski->id,  'place_id' => $netflix->id],
            ['keyword' => 'spotify',   'category_id' => $podpiski->id,  'place_id' => $spotify->id],
            ['keyword' => 'apple',     'category_id' => $podpiski->id,  'place_id' => $apple->id],
            ['keyword' => 'microsoft', 'category_id' => $podpiski->id,  'place_id' => $microsoft->id],
            ['keyword' => 'google',    'category_id' => $podpiski->id,  'place_id' => $google->id],
            ['keyword' => 'uber',      'category_id' => $taxi->id,      'place_id' => $uber->id],
            ['keyword' => 'didi',      'category_id' => $taxi->id,      'place_id' => $didi->id],
            ['keyword' => 'subte',     'category_id' => $transport->id, 'place_id' => $subte->id],
            ['keyword' => 'jumbo',     'category_id' => $produkty->id,  'place_id' => $jumbo->id],
            ['keyword' => 'iplan',     'category_id' => $internet->id,  'place_id' => $iplan->id],
        ];

        foreach ($mappings as $mapping) {
            MercadoPagoMapping::updateOrCreate(
                ['keyword' => $mapping['keyword']],
                $mapping,
            );
        }
    }
}
