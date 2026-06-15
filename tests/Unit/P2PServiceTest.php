<?php

namespace Tests\Unit;

use App\Models\AppSetting;
use App\Models\Bill;
use App\Models\Category;
use App\Models\Currency;
use App\Models\Exchange;
use App\Models\Operation;
use App\Models\Transfer;
use App\Models\User;
use App\Service\P2PService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class P2PServiceTest extends TestCase
{
    use RefreshDatabase;

    private P2PService $service;
    private Bill $bybitBill;
    private Bill $mpBill;
    private Currency $usdt;
    private Currency $ars;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->user = User::first();
        $this->actingAs($this->user);

        $this->bybitBill = Bill::factory()->create(['name' => 'Bybit', 'user_id' => $this->user->id]);
        $this->mpBill    = Bill::factory()->create(['name' => 'Mercado Pago', 'user_id' => $this->user->id]);
        $this->usdt      = Currency::factory()->create(['name' => 'USDT', 'is_crypto' => true]);
        $this->ars       = Currency::where('name', 'ARS')->firstOrFail();

        AppSetting::set('p2p_bybit_bill_id', $this->bybitBill->id, $this->user->id);
        AppSetting::set('p2p_mp_bill_id', $this->mpBill->id, $this->user->id);

        $this->service = new P2PService();
    }

    public function test_creates_exchange_on_bybit_bill(): void
    {
        $this->service->create(['date' => '2026-05-10', 'usdt_amount' => 250, 'ars_amount' => 368000, 'bybit_bill_id' => $this->bybitBill->id]);

        $exchange = Exchange::first();
        $this->assertNotNull($exchange);
        $this->assertEquals($this->bybitBill->id, $exchange->bill_id);
        $this->assertEquals($this->usdt->id, $exchange->from_currency_id);
        $this->assertEquals(250, $exchange->amount_from);
        $this->assertEquals($this->ars->id, $exchange->to_currency_id);
        $this->assertEquals(368000, $exchange->amount_to);
    }

    public function test_creates_transfer_from_bybit_to_mp(): void
    {
        $this->service->create(['date' => '2026-05-10', 'usdt_amount' => 250, 'ars_amount' => 368000, 'bybit_bill_id' => $this->bybitBill->id]);

        $transfer = Transfer::first();
        $this->assertNotNull($transfer);
        $this->assertEquals($this->bybitBill->id, $transfer->from_bill_id);
        $this->assertEquals($this->mpBill->id, $transfer->to_bill_id);
        $this->assertEquals(368000, $transfer->amount);
        $this->assertEquals($this->ars->id, $transfer->currency_id);
    }

    public function test_deletes_source_operation(): void
    {
        $category = Category::first();
        $operation = Operation::factory()->create([
            'bill_id'     => $this->mpBill->id,
            'currency_id' => $this->ars->id,
            'user_id'     => $this->user->id,
            'category_id' => $category->id,
        ]);

        $this->service->create(
            ['date' => '2026-05-10', 'usdt_amount' => 250, 'ars_amount' => 368000, 'bybit_bill_id' => $this->bybitBill->id],
            $operation
        );

        $this->assertDatabaseMissing('operations', ['id' => $operation->id]);
    }

    public function test_creates_without_source_operation(): void
    {
        $this->service->create(['date' => '2026-05-10', 'usdt_amount' => 250, 'ars_amount' => 368000, 'bybit_bill_id' => $this->bybitBill->id]);

        $this->assertCount(1, Exchange::all());
        $this->assertCount(1, Transfer::all());
        $this->assertCount(0, Operation::all());
    }
}
