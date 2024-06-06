<?php

namespace App\Helpers;

class MoneyHelper
{
    const SCALE = 18;

    public static function multiply($amount, $multiplier, $scale = self::SCALE): string
    {
        return bcmul($amount, $multiplier, $scale);
    }

    public static function divide($amount, $divider, $scale = self::SCALE): string
    {
        return bcdiv($amount, $divider, $scale);
    }

    public static function add($amount1, $amount2, $scale = self::SCALE): string
    {
        return bcadd($amount1, $amount2, $scale);
    }

    public static function subtract($amount1, $amount2, $scale = self::SCALE): string
    {
        return bcsub($amount1, $amount2, $scale);
    }
}
