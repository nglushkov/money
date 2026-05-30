<?php

namespace App\Http\Controllers;

use App\Enum\StorageFilePath;
use App\Models\Currency;
use App\Models\Enum\MoveType;
use App\Models\MercadoPagoDismissed;
use App\Service\PlannedExpenseService;
use Illuminate\Http\Request;
use App\Models\Operation;
use App\Models\Transfer;
use App\Models\Exchange;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
        if ($mpOnly || $draftOnly || $moveType === MoveType::Operation->name) {
            $moves = $operationsQuery->get();
        } elseif ($moveType === MoveType::Transfer->name) {
            $moves = Transfer::with(['from', 'to', 'currency', 'user'])->latest()->get();
        } elseif ($moveType === MoveType::Exchange->name) {
            $moves = Exchange::with(['from', 'to', 'bill', 'user', 'place'])->latest()->get();
        } else {
            $operations = $operationsQuery->get();
            $transfers  = Transfer::with(['from', 'to', 'currency', 'user'])->latest()->get();
            $exchanges  = Exchange::with(['from', 'to', 'bill', 'user', 'place'])->latest()->get();
            $moves      = $operations->concat($transfers)->concat($exchanges);
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

    public function bulkDelete(Request $request)
    {
        $selected = $request->input('selected', []);

        if (empty($selected)) {
            return redirect()->back();
        }

        $operationIds = [];
        $transferIds  = [];
        $exchangeIds  = [];

        foreach ($selected as $item) {
            [$type, $id] = explode(':', $item, 2);
            match ($type) {
                'operation' => $operationIds[] = (int) $id,
                'transfer'  => $transferIds[]  = (int) $id,
                'exchange'  => $exchangeIds[]  = (int) $id,
                default     => null,
            };
        }

        DB::transaction(function () use ($operationIds, $transferIds, $exchangeIds) {
            if ($operationIds) {
                $operations = Operation::whereIn('id', $operationIds)->get();
                foreach ($operations as $operation) {
                    if ($operation->external_source === 'mercadopago' && $operation->external_id) {
                        MercadoPagoDismissed::dismiss($operation->external_id, $operation->user_id);
                    }
                    if ($operation->attachment) {
                        Storage::delete(StorageFilePath::OperationAttachments->value . '/' .
                            md5($operation->id . $operation->attachment));
                    }
                    $operation->delete();
                }
            }

            if ($transferIds) {
                Transfer::whereIn('id', $transferIds)->each(fn($t) => $t->delete());
            }

            if ($exchangeIds) {
                Exchange::whereIn('id', $exchangeIds)->each(fn($e) => $e->delete());
            }
        });

        $count = count($selected);
        return redirect()->back()->with('success', "Deleted {$count} item(s)");
    }

    public function paginate($items, $perPage = 15, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);

        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }
}
