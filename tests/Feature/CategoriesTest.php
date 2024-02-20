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
class CategoriesTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->user = User::inRandomOrder()->first();
    }

    public function testIndex()
    {
        $response = $this->actingAs($this->user)->get('/categories');

        $response->assertStatus(200);
    }

    public function testCreate()
    {
        $response = $this->actingAs($this->user)->get('/categories/create');

        $response->assertStatus(200);
    }

    public function testShow()
    {
        $category = Category::factory()->create();
        $response = $this->actingAs($this->user)->get("/categories/{$category->id}");

        $response->assertStatus(200);
    }

    public function testStore()
    {
        $fields = $this->getFields();

        $response = $this->actingAs($this->user)->post('/categories', $fields);
        $this->assertDatabaseHas('categories', $fields);

        $response->assertRedirectToRoute('categories.create');
    }

    public function testEdit()
    {
        $category = Category::factory()->create();
        $response = $this->actingAs($this->user)->get("/categories/{$category->id}/edit");

        $response->assertStatus(200);
    }

    public function testUpdate()
    {
        $category = Category::factory()->create();
        $fields = $this->getFields();

        $response = $this->actingAs($this->user)->put("/categories/{$category->id}", $fields);
        $this->assertDatabaseHas('categories', $fields);

        $response->assertRedirectToRoute('categories.index');
    }

    public function testDestroy()
    {
        $category = Category::factory()->create();
        $response = $this->actingAs($this->user)->delete("/categories/{$category->id}");

        $this->assertDatabaseMissing('categories', $category->toArray());

        $response->assertRedirectToRoute('categories.index');
    }

    public function testDestroyWithOperations()
    {
        $category = Category::factory()->create();
        Operation::factory()->create(['category_id' => $category->id]);
        $response = $this->actingAs($this->user)->delete("/categories/{$category->id}");

        $response->assertStatus(302);
        $response->assertSessionHasErrors();
    }

    private function getFields(): array
    {
        return [
            'name' => 'Name of category',
            'notes' => 'Category notes',
        ];
    }

}
