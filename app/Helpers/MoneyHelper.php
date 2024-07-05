<?php

namespace App\Helpers;

use InvalidArgumentException;

class MoneyHelper
{
    const SCALE = 18;

    const SCALE_SHORT = 8;

    private static function performOperation($operation, $amount1, $amount2, $scale = self::SCALE): string
    {
        $normalizedAmount1 = self::normalizeNumber($amount1);
        $normalizedAmount2 = self::normalizeNumber($amount2);

        switch ($operation) {
            case 'add':
                return bcadd($normalizedAmount1, $normalizedAmount2, $scale);
            case 'subtract':
                return bcsub($normalizedAmount1, $normalizedAmount2, $scale);
            case 'multiply':
                return bcmul($normalizedAmount1, $normalizedAmount2, $scale);
            case 'divide':
                return bcdiv($normalizedAmount1, $normalizedAmount2, $scale);
            default:
                throw new InvalidArgumentException("Unsupported operation: $operation");
        }
    }

    public static function add($amount1, $amount2, $scale = self::SCALE): string
    {
        return self::performOperation('add', $amount1, $amount2, $scale);
    }

    public static function subtract($amount1, $amount2, $scale = self::SCALE): string
    {
        return self::performOperation('subtract', $amount1, $amount2, $scale);
    }

    public static function multiply($amount, $multiplier, $scale = self::SCALE): string
    {
        return self::performOperation('multiply', $amount, $multiplier, $scale);
    }

    public static function divide($amount, $divider, $scale = self::SCALE): string
    {
        return self::performOperation('divide', $amount, $divider, $scale);
    }

    public static function normalizeNumber($number): string
    {
        return number_format($number, self::SCALE, '.', '');
    }
}
