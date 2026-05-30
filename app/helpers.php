<?php

use App\Helpers\MoneyFormatter;

if (!function_exists('money_input')) {
    function money_input(mixed $amount): string
    {
        return MoneyFormatter::forInput($amount);
    }
}
