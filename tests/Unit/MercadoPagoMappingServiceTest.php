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
}
