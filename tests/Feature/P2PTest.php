<?php

namespace Tests\Feature;

use App\Models\AppSetting;
use App\Models\Bill;
use App\Models\Currency;
use App\Models\Exchange;
use App\Models\Operation;
use App\Models\Transfer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class P2PTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Bill $bybitBill;
    private Bill $mpBill;
    private Currency $usdtCurrency;
    private Currency $arsCurrency;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->user = User::first();

        $this->bybitBill = Bill::factory()->create(['name' => 'Bybit', 'user_id' => $this->user->id]);
        $this->mpBill    = Bill::factory()->create(['name' => 'Mercado Pago', 'user_id' => $this->user->id]);

        $this->usdtCurrency = Currency::factory()->create(['name' => 'USDT', 'is_crypto' => true]);
        $this->arsCurrency  = Currency::where('name', 'ARS')->firstOrFail();

        AppSetting::set('p2p_bybit_bill_id', $this->bybitBill->id, $this->user->id);
        AppSetting::set('p2p_mp_bill_id', $this->mpBill->id, $this->user->id);
        AppSetting::set('mp_review_threshold', 300000, $this->user->id);
    }

    public function test_create_page_loads(): void
    {
        $this->actingAs($this->user)->get(route('p2p.create'))->assertStatus(200);
    }

    public function test_create_page_prefills_from_operation(): void
    {
        $operation = $this->makeMpOperation(368000);

        $response = $this->actingAs($this->user)
            ->get(route('p2p.create', ['from_operation' => $operation->id]));

        $response->assertStatus(200);
        $response->assertSee($operation->id);
        $response->assertSee('368000');
    }

    public function test_store_creates_exchange_and_transfer(): void
    {
        $this->actingAs($this->user)->post(route('p2p.store'), [
            'date'          => '2026-05-10',
            'usdt_amount'   => 250,
            'ars_amount'    => 368000,
            'bybit_bill_id' => $this->bybitBill->id,
        ])->assertRedirect(route('home'));

        $this->assertDatabaseHas('exchanges', [
            'bill_id'          => $this->bybitBill->id,
            'from_currency_id' => $this->usdtCurrency->id,
            'amount_from'      => 250,
            'to_currency_id'   => $this->arsCurrency->id,
            'amount_to'        => 368000,
        ]);

        $this->assertDatabaseHas('transfers', [
            'from_bill_id' => $this->bybitBill->id,
            'to_bill_id'   => $this->mpBill->id,
            'amount'       => 368000,
            'currency_id'  => $this->arsCurrency->id,
        ]);
    }

    public function test_store_with_source_operation_deletes_it(): void
    {
        $operation = $this->makeMpOperation(368000);

        $this->actingAs($this->user)->post(route('p2p.store'), [
            'date'              => '2026-05-10',
            'usdt_amount'       => 250,
            'ars_amount'        => 368000,
            'bybit_bill_id'     => $this->bybitBill->id,
            'from_operation_id' => $operation->id,
        ])->assertRedirect(route('home'));

        $this->assertDatabaseMissing('operations', ['id' => $operation->id]);
        $this->assertCount(1, Exchange::all());
        $this->assertCount(1, Transfer::all());
    }

    public function test_store_validation_requires_fields(): void
    {
        $this->actingAs($this->user)
            ->post(route('p2p.store'), [])
            ->assertSessionHasErrors(['date', 'usdt_amount', 'ars_amount', 'bybit_bill_id']);
    }

    private function makeMpOperation(float $amount): Operation
    {
        $op = Operation::factory()->create([
            'bill_id'          => $this->mpBill->id,
            'currency_id'      => $this->arsCurrency->id,
            'user_id'          => $this->user->id,
            'amount'           => $amount,
            'external_source'  => 'mercadopago',
            'external_id'      => 'test_' . uniqid(),
            'mp_review_status' => 'pending',
        ]);
        return $op;
    }
}
