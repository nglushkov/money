<?php

namespace Tests\Unit;

use App\Helpers\MoneyFormatter;
use PHPUnit\Framework\TestCase;

class MoneyFormatterTest extends TestCase
{
    public function test_get_without_trailing_zeros_removes_decimal_zeros(): void
    {
        $this->assertEquals('369582.5', MoneyFormatter::getWithoutTrailingZeros('369582.500000000000000000'));
    }

    public function test_get_without_trailing_zeros_keeps_integer(): void
    {
        $this->assertEquals('100', MoneyFormatter::getWithoutTrailingZeros('100'));
    }

    public function test_get_without_trailing_zeros_handles_zero_string(): void
    {
        $this->assertEquals('0', MoneyFormatter::getWithoutTrailingZeros('0'));
    }

    public function test_get_without_trailing_zeros_handles_zero_decimal(): void
    {
        $this->assertEquals('0', MoneyFormatter::getWithoutTrailingZeros('0.000000000000000000'));
    }

    public function test_get_without_trailing_zeros_keeps_significant_decimals(): void
    {
        $this->assertEquals('1869', MoneyFormatter::getWithoutTrailingZeros('1869.000000000000000000'));
        $this->assertEquals('1.5', MoneyFormatter::getWithoutTrailingZeros('1.500000000000000000'));
        $this->assertEquals('1.23', MoneyFormatter::getWithoutTrailingZeros('1.230000000000000000'));
    }

    public function test_for_input_returns_empty_for_null(): void
    {
        $this->assertEquals('', MoneyFormatter::forInput(null));
    }

    public function test_for_input_returns_empty_for_empty_string(): void
    {
        $this->assertEquals('', MoneyFormatter::forInput(''));
    }

    public function test_for_input_handles_integer_zero(): void
    {
        $this->assertEquals('0', MoneyFormatter::forInput(0));
    }

    public function test_for_input_handles_bcmath_string(): void
    {
        $this->assertEquals('369582.5', MoneyFormatter::forInput('369582.500000000000000000'));
    }

    public function test_for_input_handles_float(): void
    {
        $this->assertEquals('1.5', MoneyFormatter::forInput(1.5));
    }

    public function test_money_input_helper_matches_for_input(): void
    {
        $this->assertEquals(MoneyFormatter::forInput('369582.5'), money_input('369582.5'));
        $this->assertEquals(MoneyFormatter::forInput(null), money_input(null));
        $this->assertEquals(MoneyFormatter::forInput(0), money_input(0));
    }
}
