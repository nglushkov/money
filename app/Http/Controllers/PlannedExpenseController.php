<?php

namespace App\Http\Controllers;

use App\Models\PlannedExpense;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class PlannedExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $plannedExpenses = PlannedExpense::query()
            ->with(['currency', 'category', 'place'])
            ->get();
        $plannedExpenses = $plannedExpenses->sortBy('next_payment_date');
        $paginator = $this->paginate($plannedExpenses, 21, null, ['path' => route('planned-expenses.index')]);
        $plannedExpenses = $paginator->items();

        return view('planned-expenses.index', [
            'plannedExpenses' => $plannedExpenses,
            'paginator' => $paginator,
        ]);
    }

    /**
     * Paginate the given collection.
     * @todo: Сделать метод универсальным
     */
    public function paginate($items, $perPage = 15, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

    /**
     * Скрыть сообщение о запланированном расходе.
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function dismiss($id)
    {
        $plannedExpense = PlannedExpense::find($id);
        Cache::set('dismissed_planned_expense_' . $plannedExpense->id, true, 60 * 60 * 24);
        return back();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(PlannedExpense $plannedExpense)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PlannedExpense $plannedExpense)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PlannedExpense $plannedExpense)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PlannedExpense $plannedExpense)
    {
        //
    }
}
