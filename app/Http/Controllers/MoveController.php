<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\Enum\MoveType;
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
        $operations = $operations->get();
        $transfers = $transfers->get();
        $exchanges = $exchanges->get();

        $moveType = $request->get('type');

        if ($moveType === MoveType::Operation->name) {
            $moves = $operations;
        } else if ($moveType === MoveType::Transfer->name) {
            $moves = $transfers;
        } else if ($moveType === MoveType::Exchange->name) {
            $moves = $exchanges;
        } else {
            $moves = $operations->concat($transfers)->concat($exchanges);
        }

        $moves = $moves->sortByDesc(function ($move) {
            return $move->date->format('U') . $move->created_at->format('U');
        });

        $paginator = $this->paginate($moves, 100);
        $moves = $paginator->getCollection();

        $moves = $moves->groupBy(function($move) {
            return $move->date->format('Y-m-d');
        });
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
