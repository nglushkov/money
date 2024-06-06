<?php

namespace App\Helpers;

class MoneyHelper
{
    const SCALE = 18;

    public static function multiply($amount, $multiplier)
    {
        return bcmul($amount, $multiplier, self::SCALE);
    }

    public static function divide($amount, $divider)
    {
        return bcdiv($amount, $divider, self::SCALE);
    }

    public static function add($amount1, $amount2)
    {
        return bcadd($amount1, $amount2, self::SCALE);
    }

    public static function subtract($amount1, $amount2)
    {
        return bcsub($amount1, $amount2, self::SCALE);
    }
}
