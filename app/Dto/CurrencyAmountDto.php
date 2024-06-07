<?php

namespace App\Dto;

use App\Models\Currency;

class CurrencyAmountDto
{
    private Currency $currency;
    private string $amount;

    public function __construct(Currency $currency, string $amount)
    {
        $this->currency = $currency;
        $this->amount = $amount;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function getAmount(): string
    {
        return $this->amount;
    }
}
