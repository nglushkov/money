<?php

namespace App\Service;

use App\Helpers\MoneyHelper;
use App\Models\Bill;
use App\Models\Currency;

class CurrencyService
{
    public function getTotalInvestedByBill(Bill $bill, Currency $currency): string
    {
        $sum = 0;
        foreach ($bill->exchangesFrom as $exchange) {
            if ($exchange->from_currency_id === $currency->id) {
                $sum = MoneyHelper::subtract($sum, $exchange->amount_from);
            }
        }
        foreach ($bill->exchangesTo as $exchange) {
            if ($exchange->to_currency_id === $currency->id) {
                $sum = MoneyHelper::add($sum, $exchange->amount_to);
            }
        }
        return $sum;
    }
}
