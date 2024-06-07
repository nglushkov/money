<?php

namespace App\Enum;

enum CacheKey
{
    case dismissed_planned_expense;
    case default_currency;
    case default_crypto_currency;
    case currency_rate;
}
