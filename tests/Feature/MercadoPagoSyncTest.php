<?php

namespace Tests\Feature;

use App\Models\Bill;
use App\Models\Category;
use App\Models\Currency;
use App\Models\MercadoPagoMapping;
use App\Models\Operation;
use App\Models\User;
use App\Service\OperationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * OperationService::createFromExternal
 *   - creates operation with external_id and external_source
 *   - returns null and skips duplicate by external_id
 *
 * MercadoPagoSync command
 *   - imports only approved payments, skips non-approved
 *   - regular_payment → Expense, money_transfer → Income
 *   - idempotent: second run creates 0 new operations
 */
class MercadoPagoSyncTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Bill $bill;
    private Currency $currency;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->user     = User::first();
        $this->currency = Currency::where('name', 'ARS')->first();

        $this->bill = Bill::factory()->create([
            'name'    => 'Mercado Pago',
            'user_id' => $this->user->id,
        ]);

        config(['mercadopago.accounts' => [
            ['access_token' => 'test-token', 'user_id' => $this->user->id],
        ]]);
    }

    public function test_create_from_external_persists_operation(): void
    {
        $service = app(OperationService::class);

        $operation = $service->createFromExternal([
            'external_id'     => 'mp_123',
            'external_source' => 'mercadopago',
            'amount'          => 1500.00,
            'type'            => 'Expense',
            'bill_id'         => $this->bill->id,
            'currency_id'     => $this->currency->id,
            'category_id'     => null,
            'place_id'        => null,
            'date'            => now(),
            'notes'           => 'Netflix',
            'user_id'         => $this->user->id,
        ]);

        $this->assertNotNull($operation);
        $this->assertDatabaseHas('operations', [
            'external_id'     => 'mp_123',
            'external_source' => 'mercadopago',
            'amount'          => 1500.00,
        ]);
    }

    public function test_create_from_external_skips_duplicate(): void
    {
        $service = app(OperationService::class);
        $data = [
            'external_id'     => 'mp_dup',
            'external_source' => 'mercadopago',
            'amount'          => 100.00,
            'type'            => 'Expense',
            'bill_id'         => $this->bill->id,
            'currency_id'     => $this->currency->id,
            'category_id'     => null,
            'place_id'        => null,
            'date'            => now(),
            'notes'           => null,
            'user_id'         => $this->user->id,
        ];

        $service->createFromExternal($data);
        $result = $service->createFromExternal($data);

        $this->assertNull($result);
        $this->assertCount(1, Operation::where('external_id', 'mp_dup')->get());
    }

    public function test_sync_imports_approved_and_skips_non_approved(): void
    {
        Http::fake([
            'api.mercadopago.com/v1/payments/search*' => Http::response([
                'results' => [
                    $this->makePayment('pay_1', 'approved', 'regular_payment', 500),
                    $this->makePayment('pay_2', 'approved', 'money_transfer', 1000),
                    $this->makePayment('pay_3', 'pending', 'regular_payment', 200),
                ],
                'paging' => ['total' => 3, 'limit' => 100, 'offset' => 0],
            ]),
        ]);

        $this->artisan('app:mp-sync --days=1')->assertExitCode(0);

        $this->assertCount(2, Operation::where('external_source', 'mercadopago')->get());
        $this->assertDatabaseHas('operations', ['external_id' => 'pay_1', 'type' => 'Expense']);
        $this->assertDatabaseHas('operations', ['external_id' => 'pay_2', 'type' => 'Expense', 'is_draft' => true]);
        $this->assertDatabaseMissing('operations', ['external_id' => 'pay_3']);
    }

    public function test_sync_sets_place_on_operation(): void
    {
        Http::fake([
            'api.mercadopago.com/v1/payments/search*' => Http::response([
                'results' => [
                    $this->makePayment('pay_place', 'approved', 'regular_payment', 100, 'Netflix.com'),
                ],
                'paging' => ['total' => 1, 'limit' => 100, 'offset' => 0],
            ]),
        ]);

        $this->artisan('app:mp-sync --days=1')->assertExitCode(0);

        $operation = Operation::where('external_id', 'pay_place')->first();
        $this->assertNotNull($operation->place_id);
        $this->assertDatabaseHas('places', ['name' => 'Netflix']);
    }

    public function test_sync_is_idempotent(): void
    {
        Http::fake([
            'api.mercadopago.com/v1/payments/search*' => Http::response([
                'results' => [
                    $this->makePayment('pay_idem', 'approved', 'regular_payment', 300),
                ],
                'paging' => ['total' => 1, 'limit' => 100, 'offset' => 0],
            ]),
        ]);

        $this->artisan('app:mp-sync --days=1')->assertExitCode(0);
        $this->artisan('app:mp-sync --days=1')->assertExitCode(0);

        $this->assertCount(1, Operation::where('external_id', 'pay_idem')->get());
    }

    public function test_sync_sets_review_status_pending_for_large_amount(): void
    {
        \App\Models\AppSetting::set('mp_review_threshold', 300000, $this->user->id);

        Http::fake([
            'api.mercadopago.com/v1/payments/search*' => Http::response([
                'results' => [
                    $this->makePayment('pay_big', 'approved', 'regular_payment', 350000),
                    $this->makePayment('pay_small', 'approved', 'regular_payment', 100),
                ],
                'paging' => ['total' => 2, 'limit' => 100, 'offset' => 0],
            ]),
        ]);

        $this->artisan('app:mp-sync --days=1')->assertExitCode(0);

        $this->assertDatabaseHas('operations', ['external_id' => 'pay_big', 'mp_review_status' => 'pending']);
        $this->assertDatabaseHas('operations', ['external_id' => 'pay_small', 'mp_review_status' => null]);
    }

    private function makePayment(string $id, string $status, string $operationType, float $amount, string $description = 'Test payment'): array
    {
        return [
            'id'                 => $id,
            'status'             => $status,
            'operation_type'     => $operationType,
            'transaction_amount' => $amount,
            'description'        => $description,
            'date_approved'      => now()->toIso8601String(),
            'date_created'       => now()->toIso8601String(),
            'payment_method_id'  => 'account_money',
        ];
    }
}
