<?php

namespace App\Http\Controllers;

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
        $operations = Operation::orderBy('date', 'desc')->get();
        $transfers = Transfer::orderBy('date', 'desc')->get();
        $exchanges = Exchange::orderBy('date', 'desc')->get();

        $moves = $operations->concat($transfers)->concat($exchanges)->sortByDesc('date');

        $paginator = $this->paginate($moves, 15);
        $moves = $paginator->items();

        return view('moves.index', compact('moves'), compact('paginator'));
    }

    public function paginate($items, $perPage = 15, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);

        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }
}
