<?php

namespace App\Models\Enum;

enum OperationType
{
    use EnumToArray;

    case Expense;
    case Income;
}
