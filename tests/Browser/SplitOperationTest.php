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

class SplitOperationTest extends DuskTestCase
{
    use DatabaseMigrations;

    private User $user;
    private Bill $bill;
    private Currency $currency;
    private Place $place;
    private Category $cat1;
    private Category $cat2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user     = User::factory()->create();
        $this->currency = Currency::factory()->create(['name' => 'ARS', 'is_default' => true]);
        $this->bill     = Bill::factory()->create(['user_id' => $this->user->id]);
        $this->place    = Place::factory()->create();
        $this->cat1     = Category::factory()->create(['name' => 'Food']);
        $this->cat2     = Category::factory()->create(['name' => 'Tobacco']);
    }

    public function test_split_toggle_shows_split_section(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit(route('operations.create'))
                ->assertMissing('[dusk="split-section"]')
                ->click('[dusk="split-toggle"]')
                ->waitFor('[dusk="split-section"]')
                ->assertVisible('[dusk="split-section"]');
        });
    }

    public function test_split_toggle_hides_again(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit(route('operations.create'))
                ->click('[dusk="split-toggle"]')
                ->waitFor('[dusk="split-section"]')
                ->click('[dusk="split-toggle"]')
                ->waitUntilMissing('[dusk="split-section"]')
                ->assertMissing('[dusk="split-section"]');
        });
    }

    public function test_remaining_recalculates(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit(route('operations.create'))
                ->type('#amount', '5000')
                ->click('[dusk="split-toggle"]')
                ->waitFor('[dusk="split-section"]')
                ->type('[dusk="split-amt-0"]', '3000')
                ->waitForText('2000.00')
                ->assertSeeIn('[dusk="split-remaining"]', '2000.00');
        });
    }

    public function test_fill_remaining_button(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit(route('operations.create'))
                ->type('#amount', '5000')
                ->click('[dusk="split-toggle"]')
                ->waitFor('[dusk="split-section"]')
                ->type('[dusk="split-amt-0"]', '3000')
                ->click('[dusk="split-add-row"]')
                ->waitFor('[dusk="split-amt-1"]')
                ->click('[dusk="split-rest-1"]')
                ->assertInputValue('[dusk="split-amt-1"]', '2000.00');
        });
    }

    public function test_can_add_and_remove_rows(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit(route('operations.create'))
                ->click('[dusk="split-toggle"]')
                ->waitFor('[dusk="split-section"]')
                ->assertMissing('[dusk="split-del-0"]')
                ->click('[dusk="split-add-row"]')
                ->waitFor('[dusk="split-row-1"]')
                ->assertVisible('[dusk="split-del-0"]')
                ->assertVisible('[dusk="split-del-1"]')
                ->click('[dusk="split-del-1"]')
                ->waitUntilMissing('[dusk="split-row-1"]')
                ->assertMissing('[dusk="split-del-0"]');
        });
    }

    public function test_balanced_indicator_when_amounts_match(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit(route('operations.create'))
                ->type('#amount', '1000')
                ->click('[dusk="split-toggle"]')
                ->waitFor('[dusk="split-section"]')
                ->assertMissing('[dusk="split-balanced"]')
                ->type('[dusk="split-amt-0"]', '1000')
                ->waitFor('[dusk="split-balanced"]')
                ->assertVisible('[dusk="split-balanced"]');
        });
    }
}
