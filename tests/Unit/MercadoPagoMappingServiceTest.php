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
    private Category $defaultCategory;
    private Place $place;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category        = Category::factory()->create();
        $this->defaultCategory = Category::factory()->create();
        $this->place           = Place::factory()->create(['name' => 'Netflix']);

        MercadoPagoMapping::create([
            'keyword'     => 'netflix',
            'category_id' => $this->category->id,
            'place_id'    => $this->place->id,
            'is_default'  => false,
        ]);

        MercadoPagoMapping::create([
            'keyword'     => '__default__',
            'category_id' => $this->defaultCategory->id,
            'place_id'    => null,
            'is_default'  => true,
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

    public function test_returns_default_category_when_no_match(): void
    {
        $this->assertEquals($this->defaultCategory->id, $this->service->getCategoryId('some unknown merchant'));
    }

    public function test_returns_place_id_for_matching_keyword(): void
    {
        $this->assertEquals($this->place->id, $this->service->getPlaceId('Netflix.com'));
    }

    public function test_returns_null_place_for_default_mapping(): void
    {
        $this->assertNull($this->service->getPlaceId('unknown merchant'));
    }
}
