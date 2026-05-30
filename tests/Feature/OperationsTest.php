<?php

namespace Tests\Feature;

use App\Models\Bill;
use App\Models\Category;
use App\Models\Currency;
use App\Models\Enum\OperationType;
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
        $response->assertSee('form-card');
        $response->assertSee('op-form');
        $response->assertSee('type-toggle');
        $response->assertSee('input-amount');
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
        $response->assertSee('form-card');
        $response->assertSee('op-form');
        $response->assertSee('type-toggle');
        $response->assertSee('input-amount');
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
            'type' => OperationType::Expense->name,
            'bill_id' => Bill::inRandomOrder()->first()->id,
            'currency_id' => Currency::inRandomOrder()->first()->id,
            'category_id' => Category::inRandomOrder()->first()->id,
            'place_id' => Place::inRandomOrder()->first()->id,
            'notes' => __METHOD__,
        ];
    }

    public function testSplitStore()
    {
        $bill     = Bill::inRandomOrder()->first();
        $currency = Currency::inRandomOrder()->first();
        $place    = Place::inRandomOrder()->first();
        $cat1     = Category::inRandomOrder()->first();
        $cat2     = Category::inRandomOrder()->skip(1)->first();

        $countBefore = Operation::count();

        $response = $this->actingAs($this->user)->post('/operations', [
            'date'       => '2021-01-01',
            'amount'     => '5000',
            'type'       => OperationType::Expense->name,
            'bill_id'    => $bill->id,
            'currency_id'=> $currency->id,
            'place_id'   => $place->id,
            'split_mode' => '1',
            'splits'     => [
                ['category_id' => $cat1->id, 'amount' => '3000'],
                ['category_id' => $cat2->id, 'amount' => '2000'],
            ],
        ]);

        $response->assertRedirectToRoute('home');
        $this->assertEquals($countBefore + 2, Operation::count());

        $this->assertDatabaseHas('operations', ['category_id' => $cat1->id, 'amount' => '3000.00']);
        $this->assertDatabaseHas('operations', ['category_id' => $cat2->id, 'amount' => '2000.00']);
    }

    public function testCopyOperation()
    {
        $operation = Operation::factory()->create([
            'external_id' => 'mp-test-123',
            'external_source' => 'mercadopago',
            'mp_review_status' => 'pending',
        ]);

        $response = $this->actingAs($this->user)->get("/operations/{$operation->id}/copy");

        $response->assertRedirect();
        $copy = Operation::where('id', '!=', $operation->id)->latest('id')->first();
        $this->assertNull($copy->external_id);
        $this->assertNull($copy->external_source);
        $this->assertNull($copy->mp_review_status);
        $this->assertTrue($copy->is_draft);
    }

    public function testOperationsDestroy()
    {
        $operation = Operation::factory()->create();
        $response = $this->actingAs($this->user)->delete("/operations/{$operation->id}");

        $response->assertRedirectToRoute('operations.index');
        $this->assertDatabaseMissing('operations', ['id' => $operation->id]);
    }
}
