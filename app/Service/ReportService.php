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
    public function getTotalByCategories(Collection $operations, string $defaultCurrencyName)
    {
        $data = $operations->groupBy('category_id')->map(function (Collection $operations) use($defaultCurrencyName) {
            return collect([
                'categoryName' => $operations->first()->category->name,
                'categoryId' => $operations->first()->category->id,
                'total' => $operations->sum('amount_in_default_currency'),
            ]);
        })->values();

        $data->transform(function ($item) use ($defaultCurrencyName) {
            $item['total'] = MoneyFormatter::getWithCurrencyName($item['total'], $defaultCurrencyName);
            return $item;
        });

        return $data->sortByDesc('total')->values();
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
