<?php

namespace Tests\Feature;

use App\Models\Bill;
use App\Models\Category;
use App\Models\Currency;
use App\Models\Operation;
use App\Models\Place;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Testing operations routes
 */
class OperationsTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->user = User::inRandomOrder()->first();
    }

    public function testOperationsIndex()
    {
        $response = $this->actingAs($this->user)->get('/operations');

        $response->assertStatus(200);
    }

    public function testOperationsCreate()
    {
        $response = $this->actingAs($this->user)->get('/operations/create');

        $response->assertStatus(200);
    }

    public function testShowOperation()
    {
        $operation = Operation::factory()->create();
        $response = $this->actingAs($this->user)->get("/operations/{$operation->id}");

        $response->assertStatus(200);
    }

    public function testOperationsStore()
    {
        $fields = $this->getFields();

        $response = $this->actingAs($this->user)->post('/operations', $fields);
        $this->assertDatabaseHas('operations', $fields);

        $response->assertRedirectToRoute('home');
    }

    public function testOperationsEdit()
    {
        $operation = Operation::factory()->create();
        $response = $this->actingAs($this->user)->get("/operations/{$operation->id}/edit");

        $response->assertStatus(200);
    }

    public function testOperationsUpdate()
    {
        $fields = $this->getFields();

        $operation = Operation::factory()->create();

        $response = $this->actingAs($this->user)->put("/operations/{$operation->id}", $fields);
        $this->assertDatabaseHas('operations', $fields);

        $response->assertRedirectToRoute('operations.show', $operation);
    }

    private function getFields(): array
    {
        return [
            'date' => '2021-01-01',
            'amount' => rand(0, 100000000) / 100,
            'type' => Operation::TYPE_EXPENSE,
            'bill_id' => Bill::inRandomOrder()->first()->id,
            'currency_id' => Currency::inRandomOrder()->first()->id,
            'category_id' => Category::inRandomOrder()->first()->id,
            'place_id' => Place::inRandomOrder()->first()->id,
            'notes' => __METHOD__,
        ];
    }

    public function testOperationsDestroy()
    {
        $operation = Operation::factory()->create();
        $response = $this->actingAs($this->user)->delete("/operations/{$operation->id}");

        $response->assertRedirectToRoute('operations.index');
        $this->assertDatabaseMissing('operations', ['id' => $operation->id]);
    }
}
