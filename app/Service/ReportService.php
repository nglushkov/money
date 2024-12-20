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
        })->sortByDesc('total')->values();

        $data->transform(function ($item) use ($defaultCurrencyName) {
            $item['total'] = MoneyFormatter::getWithCurrencyName($item['total'], $defaultCurrencyName);
            return $item;
        });

        return $data;
    }

    // todo: Move
    public function getOperations(string $month, string $year, array $filterCategoryIds = [], $billId = null): Collection
    {
        $operations = Operation::whereMonth('date', $month)
            ->whereYear('date', $year)
            ->isExpense()
            ->isNotDraft()
            ->when($billId, function ($query) use ($billId) {
                return $query->whereBillId($billId);
            })
            ->whereNotIn('category_id', $filterCategoryIds)
            ->with(['category', 'currency']);

        return $operations->get();
    }
}
