<?php

namespace App\Helpers;

class MoneyFormatter
{
    public static function get(float $amount, int $decimals = 2): string
    {
        return number_format($amount, $decimals, '.', ' ');
    }

    public static function getWithSymbol(float $amount, string $currencySymbol): string
    {
        return self::get($amount) . ' ' . $currencySymbol;
    }

    public static function getWithSymbolAndSign(float $amount, string $currencySymbol, $type): string
    {
        $formattedAmount = self::getWithSymbol($amount, $currencySymbol);

        return $type === 0 ? '-' . $formattedAmount : '+' . $formattedAmount;
    }

    public static function  getWithoutDecimals(float $amount): string
    {
        return number_format($amount, 0, '.', ' ');
    }
}