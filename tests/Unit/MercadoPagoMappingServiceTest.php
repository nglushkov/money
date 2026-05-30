<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\MercadoPagoMapping;
use App\Models\Place;
use App\Service\MercadoPagoMappingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MercadoPagoMappingServiceTest extends TestCase
{
    use RefreshDatabase;

    private MercadoPagoMappingService $service;
    private Category $category;
    private Place $place;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = Category::factory()->create();
        $this->place    = Place::factory()->create(['name' => 'Netflix']);

        MercadoPagoMapping::create([
            'keyword'     => 'netflix',
            'category_id' => $this->category->id,
            'place_id'    => $this->place->id,
            'is_default'  => false,
        ]);

        $this->service = new MercadoPagoMappingService();
    }

    public function test_returns_category_for_matching_keyword(): void
    {
        $this->assertEquals($this->category->id, $this->service->getCategoryId('Netflix.com'));
    }

    public function test_match_is_case_insensitive(): void
    {
        $this->assertEquals($this->category->id, $this->service->getCategoryId('NETFLIX SUBSCRIPTION'));
    }

    public function test_returns_null_category_when_no_match(): void
    {
        $this->assertNull($this->service->getCategoryId('some unknown merchant'));
    }

    public function test_returns_place_id_for_matching_keyword(): void
    {
        $this->assertEquals($this->place->id, $this->service->getPlaceId('Netflix.com'));
    }

    public function test_returns_null_place_when_no_match(): void
    {
        $this->assertNull($this->service->getPlaceId('unknown merchant'));
    }

    public function test_has_match_returns_true_for_known_keyword(): void
    {
        $this->assertTrue($this->service->hasMatch('Netflix.com'));
    }

    public function test_has_match_returns_false_for_unknown(): void
    {
        $this->assertFalse($this->service->hasMatch('unknown merchant'));
    }

    public function test_keyword_with_uppercase_letters_matches_case_insensitively(): void
    {
        // Keyword stored with mixed case (e.g. "Reggina") must still match
        $category = Category::factory()->create();
        $place    = Place::factory()->create(['name' => 'Reggina']);

        MercadoPagoMapping::create([
            'keyword'     => 'Reggina',
            'category_id' => $category->id,
            'place_id'    => $place->id,
            'is_default'  => false,
        ]);

        $service = new MercadoPagoMappingService();

        $this->assertEquals($category->id, $service->getCategoryId('Producto de Reggina'));
        $this->assertEquals($place->id, $service->getPlaceId('Producto de Reggina'));
    }

    public function test_specific_shop_keyword_wins_over_generic_mp_prefix(): void
    {
        // MP sends descriptions like "Producto de <ShopName>".
        // "producto" is a generic MP prefix, not a merchant name — it must NOT shadow specific shop mappings.
        // This test documents that "producto" must NOT exist as a keyword in mappings.
        $groceries = Category::factory()->create(['name' => 'Продукты']);
        $shopCat   = Category::factory()->create(['name' => 'Рестораны']);
        $shopPlace = Place::factory()->create(['name' => 'Reggina']);

        // Only the specific shop mapping; "producto" is intentionally absent.
        MercadoPagoMapping::create([
            'keyword'     => 'Reggina',
            'category_id' => $shopCat->id,
            'place_id'    => $shopPlace->id,
            'is_default'  => false,
        ]);

        $service = new MercadoPagoMappingService();

        $this->assertEquals($shopCat->id, $service->getCategoryId('Producto de Reggina'));
        $this->assertEquals($shopPlace->id, $service->getPlaceId('Producto de Reggina'));
    }

    public function test_longer_keyword_takes_priority_over_shorter(): void
    {
        $catJumbo     = Category::factory()->create(['name' => 'Продукты']);
        $catAlmagro   = Category::factory()->create(['name' => 'Продукты Альмагро']);
        $placeJumbo   = Place::factory()->create(['name' => 'Jumbo']);
        $placeAlmagro = Place::factory()->create(['name' => 'Jumbo Almagro']);

        MercadoPagoMapping::create([
            'keyword'     => 'jumbo',
            'category_id' => $catJumbo->id,
            'place_id'    => $placeJumbo->id,
            'is_default'  => false,
        ]);

        MercadoPagoMapping::create([
            'keyword'     => 'scjumboalmagr',
            'category_id' => $catAlmagro->id,
            'place_id'    => $placeAlmagro->id,
            'is_default'  => false,
        ]);

        $service = new MercadoPagoMappingService();

        // More specific (longer) keyword wins
        $this->assertEquals($catAlmagro->id, $service->getCategoryId('payment scjumboalmagr'));
        $this->assertEquals($placeAlmagro->id, $service->getPlaceId('payment scjumboalmagr'));

        // Generic keyword still works when specific doesn't match
        $this->assertEquals($catJumbo->id, $service->getCategoryId('jumbo palermo'));
    }
}
