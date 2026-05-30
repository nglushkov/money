<?php

namespace Tests\Browser;

use App\Models\Bill;
use App\Models\Category;
use App\Models\Currency;
use App\Models\Operation;
use App\Models\Place;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AutocompleteTest extends DuskTestCase
{
    use DatabaseMigrations;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        Currency::factory()->create(['name' => 'ARS', 'is_default' => true]);
        Bill::factory()->create(['user_id' => $this->user->id]);
        Place::factory()->create();
        Category::factory()->create();
    }

    public function test_create_operation_amount_has_autocomplete_off(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit(route('operations.create'))
                ->assertAttribute('#amount', 'autocomplete', 'off');
        });
    }

    public function test_create_operation_notes_has_autocomplete_off(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit(route('operations.create'))
                ->assertAttribute('#notes', 'autocomplete', 'off');
        });
    }

    public function test_edit_operation_amount_has_autocomplete_off(): void
    {
        $operation = Operation::factory()->create();

        $this->browse(function (Browser $browser) use ($operation) {
            $browser->loginAs($this->user)
                ->visit(route('operations.edit', $operation))
                ->assertAttribute('#amount', 'autocomplete', 'off');
        });
    }

    public function test_form_itself_has_autocomplete_off(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit(route('operations.create'))
                ->assertAttribute('#op-form', 'autocomplete', 'off');
        });
    }
}
