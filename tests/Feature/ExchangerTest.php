<?php

namespace Tests\Feature;

use App\Models\Bill;
use App\Models\Currency;
use App\Models\Exchange;
use App\Models\ExchangePlace;
use App\Models\Rate;
use App\Models\Transfer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExchangerTest extends TestCase
{
    use RefreshDatabase;

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

        $this->cryptoBill = Bill::factory()->create(['is_crypto' => true,  'user_id' => $this->user->id]);
        $this->targetBill = Bill::factory()->create(['is_crypto' => false, 'user_id' => $this->user->id]);
    }

    public function test_create_page_loads(): void
    {
        $this->actingAs($this->user)
            ->get(route('exchanger.create'))
            ->assertOk()
            ->assertSee('Обменник');
    }

    public function test_store_creates_exchange_and_transfer(): void
    {
        $this->actingAs($this->user)->post(route('exchanger.store'), [
            'date'             => '2026-05-29',
            'from_bill_id'     => $this->cryptoBill->id,
            'from_currency_id' => $this->usdt->id,
            'rows'             => [
                [
                    'from_amount' => '100',
                    'amount'      => '90000',
                    'currency_id' => $this->ars->id,
                    'bill_id'     => $this->targetBill->id,
                ],
            ],
        ])->assertRedirect(route('exchanges.index'));

        $this->assertDatabaseHas('exchanges', [
            'bill_id'          => $this->cryptoBill->id,
            'from_currency_id' => $this->usdt->id,
            'amount_from'      => '100',
            'to_currency_id'   => $this->ars->id,
            'amount_to'        => '90000',
            'date'             => '2026-05-29',
        ]);

        $this->assertDatabaseHas('transfers', [
            'from_bill_id' => $this->cryptoBill->id,
            'to_bill_id'   => $this->targetBill->id,
            'currency_id'  => $this->ars->id,
            'amount'       => '90000',
        ]);

        $this->assertDatabaseHas('rates', [
            'from_currency_id' => $this->usdt->id,
            'to_currency_id'   => $this->ars->id,
            'date'             => '2026-05-29',
            'rate'             => '900.00',
        ]);
    }

    public function test_store_multiple_rows_creates_multiple_exchanges_and_transfers(): void
    {
        $usd  = Currency::factory()->create(['name' => 'USD', 'is_crypto' => false]);
        $bill2 = Bill::factory()->create(['is_crypto' => false, 'user_id' => $this->user->id]);

        $this->actingAs($this->user)->post(route('exchanger.store'), [
            'date'             => '2026-05-29',
            'from_bill_id'     => $this->cryptoBill->id,
            'from_currency_id' => $this->usdt->id,
            'rows'             => [
                [
                    'from_amount' => '80',
                    'amount'      => '72000',
                    'currency_id' => $this->ars->id,
                    'bill_id'     => $this->targetBill->id,
                ],
                [
                    'from_amount' => '20',
                    'amount'      => '19',
                    'currency_id' => $usd->id,
                    'bill_id'     => $bill2->id,
                ],
            ],
        ])->assertRedirect(route('exchanges.index'));

        $this->assertSame(2, Exchange::count());
        $this->assertSame(2, Transfer::count());
        $this->assertSame(2, Rate::count());
    }

    public function test_store_with_exchange_place(): void
    {
        $place = ExchangePlace::factory()->create();

        $this->actingAs($this->user)->post(route('exchanger.store'), [
            'date'             => '2026-05-29',
            'place_id'         => $place->id,
            'from_bill_id'     => $this->cryptoBill->id,
            'from_currency_id' => $this->usdt->id,
            'rows'             => [
                [
                    'from_amount' => '50',
                    'amount'      => '45000',
                    'currency_id' => $this->ars->id,
                    'bill_id'     => $this->targetBill->id,
                ],
            ],
        ])->assertRedirect();

        $this->assertDatabaseHas('exchanges', ['place_id' => $place->id]);
    }

    public function test_store_does_not_duplicate_rate_for_same_day(): void
    {
        Rate::create([
            'from_currency_id' => $this->usdt->id,
            'to_currency_id'   => $this->ars->id,
            'date'             => '2026-05-29',
            'rate'             => '850.00',
        ]);

        $this->actingAs($this->user)->post(route('exchanger.store'), [
            'date'             => '2026-05-29',
            'from_bill_id'     => $this->cryptoBill->id,
            'from_currency_id' => $this->usdt->id,
            'rows'             => [[
                'from_amount' => '100',
                'amount'      => '90000',
                'currency_id' => $this->ars->id,
                'bill_id'     => $this->targetBill->id,
            ]],
        ])->assertRedirect();

        $this->assertSame(1, Rate::count());
        $this->assertEqualsWithDelta(850.0, (float) Rate::first()->rate, 0.01);
    }

    public function test_store_validation_requires_rows(): void
    {
        $this->actingAs($this->user)->post(route('exchanger.store'), [
            'date'             => '2026-05-29',
            'from_bill_id'     => $this->cryptoBill->id,
            'from_currency_id' => $this->usdt->id,
        ])->assertSessionHasErrors('rows');
    }

    public function test_guest_is_redirected(): void
    {
        $this->get(route('exchanger.create'))->assertRedirect(route('login'));
    }
}
