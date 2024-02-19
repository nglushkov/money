<?php

namespace App\Service;

use App\Helpers\MoneyFormatter;
use App\Models\Operation;
use Illuminate\Support\Collection;

class ReportService
{
    /**
     * Получение суммы операций по категориям в валюте по умолчанию вида ["Категория => Сумма"]
     *
     * @param Collection $operations
     * @param string $month
     * @param string $year
     * @param string $defaultCurrencyName
     * @return Collection
     */
    public function getTotalByCategories(Collection $operations, string $month, string $year, string $defaultCurrencyName)
    {
        $totalByCategories = $operations->groupBy('category.name')->map(function ($operations) {
            return $operations->map(function ($operation) {
                return $operation->amount_in_default_currency;
            })->sum();
        })->sortDesc();

        $totalByCategories->transform(function ($total) use ($defaultCurrencyName) {
            return MoneyFormatter::getWithCurrencyName($total, $defaultCurrencyName);
        });

        return $totalByCategories;
    }

    // @toto: Move
    public function getOperations(string $month, string $year)
    {
        return Operation::whereMonth('date', $month)
            ->whereYear('date', $year)
            ->isExpense()
            ->isNotDraft()
            ->with(['category', 'currency'])
            ->get();
    }
}
