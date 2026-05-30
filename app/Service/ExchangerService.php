<?php

namespace App\Service;

use App\Models\Exchange;
use App\Models\Rate;
use App\Models\Transfer;
use Illuminate\Support\Facades\DB;

class ExchangerService
{
    public function store(array $data): void
    {
        DB::transaction(function () use ($data) {
            foreach ($data['rows'] as $row) {
                $exchange = new Exchange([
                    'bill_id'          => $data['from_bill_id'],
                    'from_currency_id' => $data['from_currency_id'],
                    'amount_from'      => $row['from_amount'],
                    'to_currency_id'   => $row['currency_id'],
                    'amount_to'        => $row['amount'],
                    'date'             => $data['date'],
                    'place_id'         => $data['place_id'] ?? null,
                    'notes'            => $data['notes'] ?? null,
                ]);
                $exchange->user_id = auth()->id();
                $exchange->save();

                Rate::firstOrCreate(
                    [
                        'from_currency_id' => $data['from_currency_id'],
                        'to_currency_id'   => $row['currency_id'],
                        'date'             => $data['date'],
                    ],
                    [
                        'rate'        => bcdiv($row['amount'], $row['from_amount'], 2),
                        'exchange_id' => $exchange->id,
                    ]
                );

                $transfer = new Transfer([
                    'from_bill_id' => $data['from_bill_id'],
                    'to_bill_id'   => $row['bill_id'],
                    'currency_id'  => $row['currency_id'],
                    'amount'       => $row['amount'],
                    'date'         => $data['date'],
                    'notes'        => $data['notes'] ?? null,
                ]);
                $transfer->user_id = auth()->id();
                $transfer->save();
            }
        });
    }
}
