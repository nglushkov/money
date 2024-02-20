<?php

namespace Tests\Feature;

use App\Models\Currency;
use App\Models\Place;

class CurrencyTest extends TestBase
{
    public function testIndex()
    {
        $response = $this->actingAs($this->user)->get('/currencies');

        $response->assertStatus(200);
    }

    public function testCreate()
    {
        $response = $this->actingAs($this->user)->get('/currencies');

        $response->assertStatus(200);
    }

    public function testShow()
    {
        $currency = Currency::factory()->create();
        $response = $this->actingAs($this->user)->get("/currencies/{$currency->id}");

        $response->assertStatus(200);
    }

    public function testStore()
    {
        $fields = $this->getFields();

        $response = $this->actingAs($this->user)->post('/currencies', $fields);
        $this->assertDatabaseHas('currencies', $fields);

        $response->assertRedirectToRoute('currencies.index');
    }

    public function testEdit()
    {
        $currency = Currency::factory()->create();
        $response = $this->actingAs($this->user)->get("/currencies/{$currency->id}/edit");

        $response->assertStatus(200);
    }

    public function testUpdate()
    {
        $currency = Currency::factory()->create();
        $fields = $this->getFields();

        $response = $this->actingAs($this->user)->put("/currencies/{$currency->id}", $fields);
        $this->assertDatabaseHas('currencies', $fields);

        $response->assertRedirectToRoute('currencies.show', $currency->id);
        $response->assertSessionHasNoErrors();
    }

    public function testDestroy()
    {
        $currency = Currency::factory()->create();
        $response = $this->actingAs($this->user)->delete("/currencies/{$currency->id}");

        $this->assertDatabaseMissing('currencies', $currency->toArray());
        $response->assertRedirectToRoute('currencies.index');
    }

    private function getFields()
    {
        return [
            'name' => 'TST',
        ];
    }
}
