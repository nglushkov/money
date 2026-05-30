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
        $mpOnly    = $request->boolean('mp');
        $draftOnly = $request->boolean('draft');
        $moveType  = $request->get('type');

        $operationsQuery = Operation::with(['bill', 'category', 'currency', 'place', 'user'])->latest();
        if ($mpOnly) {
            $operationsQuery->where('external_source', 'mercadopago');
        }
        if ($draftOnly) {
            $operationsQuery->where('is_draft', true);
        }
        $operations = $operationsQuery->get();

        if ($mpOnly || $draftOnly) {
            $moves = $operations;
        } else {
            $transfers = Transfer::with(['from', 'to', 'currency', 'user'])->latest()->get();
            $exchanges = Exchange::with(['from', 'to', 'bill', 'user', 'place'])->latest()->get();

            if ($moveType === MoveType::Operation->name) {
                $moves = $operations;
            } elseif ($moveType === MoveType::Transfer->name) {
                $moves = $transfers;
            } elseif ($moveType === MoveType::Exchange->name) {
                $moves = $exchanges;
            } else {
                $moves = $operations->concat($transfers)->concat($exchanges);
            }
        }

        $moves = $moves->sortByDesc(fn($m) => $m->date->format('U') . $m->created_at->format('U'));

        $paginator = $this->paginate($moves, 100);
        $moves     = $paginator->getCollection()->groupBy(fn($m) => $m->date->format('Y-m-d'));

        return view('moves.index', [
            'moves'           => $moves,
            'paginator'       => $paginator,
            'mpOnly'          => $mpOnly,
            'draftOnly'       => $draftOnly,
            'activeType'      => $moveType,
            'defaultCurrency' => Currency::getDefaultCurrency(),
            'plannedExpenses' => $this->plannedExpenseService->getPlannedExpensesToBeReminded(),
        ]);
    }

    public function runMpSync()
    {
        try {
            \Artisan::call('app:mp-sync');
            $output = \Artisan::output();
            return redirect()->route('home', ['mp' => 1])->with('success', trim($output) ?: 'Sync completed');
        } catch (\Throwable $e) {
            return redirect()->route('home')->with('error', $e->getMessage());
        }
    }

    public function paginate($items, $perPage = 15, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);

        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }
}
