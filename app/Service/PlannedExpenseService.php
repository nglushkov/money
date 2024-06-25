<?php

namespace App\Service;

use App\Enum\CacheKey;
use App\Enum\Time;
use App\Models\PlannedExpense;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class PlannedExpenseService
{
    private CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Set dismissed status for planned expense.
     *
     * @param PlannedExpense $plannedExpense
     * @return void
     */
    public function setDismissed(PlannedExpense $plannedExpense): void
    {
        $this->cacheService->set($this->getDismissedCacheKey($plannedExpense), true, Time::secondsIn10Days->value);
    }

    public function setDismissedAll(): void
    {
        $plannedExpenses = PlannedExpense::all();
        foreach ($plannedExpenses as $plannedExpense) {
            $this->setDismissed($plannedExpense);
        }
    }

    /**
     * Get dismissed status for planned expense.
     *
     * @param PlannedExpense $plannedExpense
     * @return bool|null
     */
    public function isDismissed(PlannedExpense $plannedExpense): ?bool
    {
        return $this->cacheService->get($this->getDismissedCacheKey($plannedExpense));
    }

    /**
     * Get planned expenses to be reminded.
     *
     * @return Collection
     */
    public function getPlannedExpensesToBeReminded(): Collection
    {
        $plannedExpenses = PlannedExpense::all()->sortBy('next_payment_date');
        $plannedExpensesToBePaid = collect();

        foreach ($plannedExpenses as $plannedExpense) {
            if ($this->canRemind($plannedExpense)) {
                $plannedExpensesToBePaid->push($plannedExpense);
            }
        }

        $plannedExpensesToBePaid = $plannedExpensesToBePaid->sortBy('next_payment_date');

        return $plannedExpensesToBePaid->filter(function ($plannedExpense) {
            return !$this->isDismissed($plannedExpense);
        });
    }

    public function getPlannedExpensesForToday(): Collection
    {
        $plannedExpenses = PlannedExpense::all();
        return $plannedExpenses->filter(function ($plannedExpense) {
            return $plannedExpense->next_payment_date->isToday();
        });
    }

    /**
     * Check if planned expense can be reminded.
     *
     * @param PlannedExpense $plannedExpense
     * @return bool
     */
    public function canRemind(PlannedExpense $plannedExpense): bool
    {
        return $plannedExpense->next_payment_date->diffInDays(today()) <= $plannedExpense->reminder_days;
    }

    /**
     * Get cache key for dismissed planned expense.
     *
     * @param PlannedExpense $plannedExpense
     * @return string
     */
    public function getDismissedCacheKey(PlannedExpense $plannedExpense): string
    {
        return sprintf('%s_%s_%s', CacheKey::dismissed_planned_expense->name, $plannedExpense->id, auth()->id());
    }
}
