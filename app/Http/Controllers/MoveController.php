<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\PlannedExpense;
use Illuminate\Http\Request;
use App\Models\Operation;
use App\Models\Transfer;
use App\Models\Exchange;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

class MoveController extends Controller
{
    public function index()
    {
        $operations = Operation::orderBy('date', 'desc')->latest()->get();
        $transfers = Transfer::orderBy('date', 'desc')->latest()->get();
        $exchanges = Exchange::orderBy('date', 'desc')->latest()->get();

        $moves = $operations->concat($transfers)->concat($exchanges)->sortByDesc('date');

        $paginator = $this->paginate($moves, 50);
        $moves = $paginator->items();
        $defaultCurrency = Currency::default()->first();

        $plannedExpenses = PlannedExpense::getNearest()->filter(function ($plannedExpense) {
            return !$plannedExpense->isDismissed();
        });

        return view('moves.index', [
            'moves' => $moves,
            'paginator' => $paginator,
            'defaultCurrency' => $defaultCurrency,
            'plannedExpenses' => $plannedExpenses,
        ]);
    }

    public function paginate($items, $perPage = 15, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);

        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }
}
