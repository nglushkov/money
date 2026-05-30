<?php

namespace Tests\Browser;

use App\Models\Bill;
use App\Models\Currency;
use App\Models\ExchangePlace;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ExchangerTest extends DuskTestCase
{
    use DatabaseMigrations;

    private User $user;
    private Bill $cryptoBill;
    private Bill $targetBill;
    private Currency $usdt;
    private Currency $ars;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->usdt = Currency::factory()->create(['name' => 'USDT', 'is_crypto' => true, 'is_default' => true]);
        $this->ars  = Currency::factory()->create(['name' => 'ARS',  'is_crypto' => false, 'is_default' => true]);

        $this->cryptoBill = Bill::factory()->create(['name' => 'Bybit', 'is_crypto' => true,  'user_id' => $this->user->id]);
        $this->targetBill = Bill::factory()->create(['name' => 'MP',    'is_crypto' => false, 'user_id' => $this->user->id]);
    }

    public function test_form_loads_with_default_row(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit(route('exchanger.create'))
                ->assertSeeIn('h5', 'Exchanger')
                ->assertPresent('[dusk="row-from-amount-0"]')
                ->assertPresent('[dusk="row-amount-0"]')
                ->assertPresent('[dusk="add-row"]')
                ->assertMissing('[dusk="row-remove-0"]');
        });
    }

    public function test_can_add_and_remove_rows(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit(route('exchanger.create'))
                ->click('[dusk="add-row"]')
                ->assertPresent('[dusk="row-from-amount-1"]')
                ->assertPresent('[dusk="row-remove-0"]')
                ->assertPresent('[dusk="row-remove-1"]')
                ->click('[dusk="row-remove-1"]')
                ->assertMissing('[dusk="row-from-amount-1"]')
                ->assertMissing('[dusk="row-remove-0"]');
        });
    }

    public function test_rate_is_calculated_on_input(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit(route('exchanger.create'))
                ->type('[dusk="row-from-amount-0"]', '100')
                ->type('[dusk="row-amount-0"]', '90000')
                ->waitForText('900')
                ->assertSeeIn('[dusk="rate-display"]', '900');
        });
    }

    public function test_remove_button_hidden_when_single_row(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit(route('exchanger.create'))
                ->assertMissing('[dusk="row-remove-0"]')
                ->click('[dusk="add-row"]')
                ->assertVisible('[dusk="row-remove-0"]')
                ->click('[dusk="row-remove-0"]')
                ->assertMissing('[dusk="row-remove-0"]');
        });
    }

    public function test_form_submits_successfully(): void
    {
        $place = ExchangePlace::factory()->create(['name' => 'Brave Rate']);

        $this->browse(function (Browser $browser) use ($place) {
            $browser->loginAs($this->user)
                ->visit(route('exchanger.create'))
                ->tap(fn($b) => $this->selectTS($b, 'place_id', $place->id))
                ->type('[dusk="row-from-amount-0"]', '100')
                ->type('[dusk="row-amount-0"]', '90000')
                ->select('[dusk="row-currency-0"]', $this->ars->id)
                ->select('[dusk="row-bill-0"]', $this->targetBill->id)
                ->click('[dusk="submit"]')
                ->assertRouteIs('exchanges.index');
        });
    }
}
