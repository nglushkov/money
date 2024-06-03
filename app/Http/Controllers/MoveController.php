<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\PlannedExpense;
use App\Service\PlannedExpenseService;
use Illuminate\Http\Request;
use App\Models\Operation;
use App\Models\Transfer;
use App\Models\Exchange;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

class MoveController extends Controller
{
    private PlannedExpenseService $plannedExpenseService;

    public function __construct(PlannedExpenseService $plannedExpenseService)
    {
        $this->plannedExpenseService = $plannedExpenseService;
    }

    public function index(Request $request)
    {
        $operations = Operation::with([
            'bill',
            'category',
            'currency',
            'place',
            'user'
        ])->latest();
        $transfers = Transfer::with([
            'from',
            'to',
            'currency',
            'user',
        ])->latest();
        $exchanges = Exchange::with([
            'from',
            'to',
            'bill',
            'user',
            'place'
        ])->latest();
        if ($request->has('date')) {
            $operations->where('date', $request->date);
            $transfers->where('date', $request->date);
            $exchanges->where('date', $request->date);
        }
        $operations = $operations->get();
        $transfers = $transfers->get();
        $exchanges = $exchanges->get();

        $moves = $operations->concat($transfers)->concat($exchanges)->sortByDesc(function ($move) {
            return $move->date->format('U') . $move->created_at->format('U');
        });

        $paginator = $this->paginate($moves, 100);
        $moves = $paginator->items();
        $defaultCurrency = Currency::getDefaultCurrency();

        $plannedExpenses = $this->plannedExpenseService->getPlannedExpensesToBeReminded();

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
