<?php

namespace App\Service;

use App\Models\AppSetting;
use App\Models\Bill;
use App\Models\Currency;
use App\Models\Exchange;
use App\Models\Operation;
use App\Models\Transfer;
use Illuminate\Support\Facades\DB;

class P2PService
{
    public function create(array $data, ?Operation $sourceOperation = null): void
    {
        $bybitBillName = AppSetting::get('p2p_bybit_bill_name', 'Bybit');
        $bybitBill     = Bill::where('name', $bybitBillName)->firstOrFail();
        $mpBill        = $sourceOperation
            ? $sourceOperation->bill
            : Bill::where('name', 'Mercado Pago')->firstOrFail();

        $usdtCurrency = Currency::where('name', 'USDT')->firstOrFail();
        $arsCurrency  = Currency::where('name', 'ARS')->firstOrFail();

        DB::transaction(function () use ($data, $bybitBill, $mpBill, $usdtCurrency, $arsCurrency, $sourceOperation) {
            $exchange             = new Exchange();
            $exchange->bill_id         = $bybitBill->id;
            $exchange->from_currency_id = $usdtCurrency->id;
            $exchange->amount_from     = $data['usdt_amount'];
            $exchange->to_currency_id  = $arsCurrency->id;
            $exchange->amount_to       = $data['ars_amount'];
            $exchange->date            = $data['date'];
            $exchange->notes           = 'P2P';
            $exchange->user_id         = auth()->id();
            $exchange->save();

            $transfer              = new Transfer();
            $transfer->from_bill_id = $bybitBill->id;
            $transfer->to_bill_id   = $mpBill->id;
            $transfer->currency_id  = $arsCurrency->id;
            $transfer->amount       = $data['ars_amount'];
            $transfer->date         = $data['date'];
            $transfer->notes        = 'P2P';
            $transfer->user_id      = auth()->id();
            $transfer->save();

            if ($sourceOperation) {
                $sourceOperation->delete();
            }
        });
    }
}
