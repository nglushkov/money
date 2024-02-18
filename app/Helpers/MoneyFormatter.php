<?php

namespace App\Helpers;

class MoneyFormatter
{
    public static function get(float $amount, int $decimals = 2): string
    {
        return number_format($amount, $decimals, '.', ' ');
    }

    public static function getWithCurrencyName(float $amount, string $currencyName): string
    {
        return self::get($amount) . ' ' . $currencyName;
    }

    public static function getWithCurrencyNameAndSign(float $amount, string $currencyName, $type): string
    {
        $formattedAmount = self::getWithCurrencyName($amount, $currencyName);

        return $type === 0 ? '-' . $formattedAmount : '+' . $formattedAmount;
    }

    public static function  getWithoutDecimals(float $amount): string
    {
        return number_format($amount, 0, '.', ' ');
    }
}
