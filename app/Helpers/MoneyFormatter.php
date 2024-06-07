<?php

namespace App\Helpers;

use App\Models\Enum\OperationType;

// @todo: Refactor this class
class MoneyFormatter
{
    public static function get(string $amount, int $decimals = 2): string
    {
        return number_format($amount, $decimals, '.', ' ');
    }

    public static function getWithCurrencyName(string $amount, string $currencyName): string
    {
        return self::get($amount) . ' ' . $currencyName;
    }

    public static function getWithCurrencyNameAndSign(string $amount, string $currencyName, $type): string
    {
        $formattedAmount = self::getWithCurrencyName($amount, $currencyName);

        return $type === OperationType::Expense->name ? '-' . $formattedAmount : '+' . $formattedAmount;
    }

    public static function getWithoutDecimals(string $amount): string
    {
        return number_format($amount, 0, '.', ' ');
    }

    public static function getWithoutTrailingZeros(string $amount): string
    {
        return rtrim(rtrim($amount, '0'), '.');
    }

    public static function getCryptoWithCurrencyName(string $amount, string $currencyName): string
    {
        return self::getWithoutTrailingZeros($amount) . ' ' . $currencyName;
    }

    public static function getCryptoWithCurrencyNameAndSign(string $amount, string $currencyName, $type): string
    {
        $formattedAmount = self::getWithoutTrailingZeros($amount) . ' ' . $currencyName;

        return $type === OperationType::Expense->name ? '-' . $formattedAmount : '+' . $formattedAmount;
    }
}
