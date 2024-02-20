<?php

namespace Tests\Feature;

use App\Models\Place;
use PHPUnit\Framework\TestCase;

class PlaceTest extends TestBase
{
    public function testIndex()
    {
        $response = $this->actingAs($this->user)->get('/places');

        $response->assertStatus(200);
    }

    public function testCreate()
    {
        $response = $this->actingAs($this->user)->get('/categories');

        $response->assertStatus(200);
    }

    public function testShow()
    {
        $place = Place::factory()->create();
        $response = $this->actingAs($this->user)->get("/places/{$place->id}");

        $response->assertStatus(200);
    }

    public function testStore()
    {
        $fields = $this->getFields();

        $response = $this->actingAs($this->user)->post('/places', $fields);
        $this->assertDatabaseHas('places', $fields);

        $response->assertRedirectToRoute('places.create');
    }

    public function testEdit()
    {
        $place = Place::factory()->create();
        $response = $this->actingAs($this->user)->get("/places/{$place->id}/edit");

        $response->assertStatus(200);
    }

    public function testUpdate()
    {
        $place = Place::factory()->create();
        $fields = $this->getFields();

        $response = $this->actingAs($this->user)->put("/places/{$place->id}", $fields);
        $this->assertDatabaseHas('places', $fields);

        $response->assertRedirectToRoute('places.index');
    }

    public function testDestroy()
    {
        $place = Place::factory()->create();
        $response = $this->actingAs($this->user)->delete("/places/{$place->id}");

        $this->assertDatabaseMissing('places', $place->toArray());

        $response->assertRedirectToRoute('places.index');
    }

    private function getFields()
    {
        return [
            'name' => 'Name of category',
            'notes' => 'Category notes',
        ];
    }
}
