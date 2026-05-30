<?php

namespace App\Console\Commands;

use App\Models\Bill;
use App\Models\Currency;
use App\Models\Enum\OperationType;
use App\Service\MercadoPagoMappingService;
use App\Service\MercadoPagoService;
use App\Service\OperationService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class MercadoPagoSync extends Command
{
    protected $signature = 'app:mp-sync {--days=1 : Number of days to sync}';
    protected $description = 'Sync Mercado Pago transactions as Operations';

    public function __construct(
        private readonly OperationService $operationService,
        private readonly MercadoPagoMappingService $mappingService,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $days     = (int) $this->option('days');
        $from     = Carbon::now()->subDays($days)->startOfDay();
        $to       = Carbon::now()->endOfDay();
        $accounts = config('mercadopago.accounts', []);

        $currency = Currency::where('name', 'ARS')->firstOrFail();

        foreach ($accounts as $account) {
            $this->syncAccount($account, $currency->id, $from, $to);
        }

        return self::SUCCESS;
    }

    private function syncAccount(array $account, int $currencyId, Carbon $from, Carbon $to): void
    {
        $userId = $account['user_id'];

        $bill = Bill::where('name', 'Mercado Pago')->where('user_id', $userId)->firstOrFail();

        $mpService = app(MercadoPagoService::class, ['accessToken' => $account['access_token']]);
        $payments  = $mpService->getPayments($from, $to);

        $created = 0;
        $skipped = 0;

        foreach ($payments as $payment) {
            if (($payment['status'] ?? '') !== 'approved') {
                continue;
            }

            $date        = Carbon::parse($payment['date_approved'] ?? $payment['date_created'])->setTimezone(config('app.timezone'));
            $description = $payment['description'] ?? $payment['payment_method_id'] ?? '';

            if ($payment['operation_type'] === 'money_transfer') {
                $operation = $this->operationService->createFromExternal([
                    'external_id'     => (string) $payment['id'],
                    'external_source' => 'mercadopago',
                    'amount'          => $payment['transaction_amount'],
                    'type'            => OperationType::Expense->name,
                    'bill_id'         => $bill->id,
                    'currency_id'     => $currencyId,
                    'category_id'     => null,
                    'place_id'        => null,
                    'force_draft'     => true,
                    'date'            => $date,
                    'notes'           => $description ?: 'Money transfer',
                    'user_id'         => $userId,
                ]);

                $operation ? $created++ : $skipped++;
                continue;
            }

            $hasMatch   = $this->mappingService->hasMatch($description);
            $categoryId = $hasMatch ? $this->mappingService->getCategoryId($description) : null;
            $placeId    = $hasMatch ? $this->mappingService->getPlaceId($description) : null;

            $operation = $this->operationService->createFromExternal([
                'external_id'      => (string) $payment['id'],
                'external_source'  => 'mercadopago',
                'amount'           => $payment['transaction_amount'],
                'type'             => OperationType::Expense->name,
                'bill_id'          => $bill->id,
                'currency_id'      => $currencyId,
                'category_id'      => $categoryId,
                'place_id'         => $placeId,
                'force_draft'      => !$hasMatch,
                'date'             => $date,
                'notes'            => $description ?: null,
                'user_id'          => $userId,
            ]);

            $operation ? $created++ : $skipped++;
        }

        $this->info("User {$userId}: created {$created}, skipped {$skipped}");
    }
}
