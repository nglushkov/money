<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePlannedExpenseRequest;
use App\Http\Requests\UpdatePlannedExpenseRequest;
use App\Models\Category;
use App\Models\Currency;
use App\Models\Place;
use App\Models\PlannedExpense;
use Carbon\Carbon;
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
     * todo: Refactor this method
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function dismiss($id)
    {
        $plannedExpense = PlannedExpense::find($id);
        // 10 дней
        Cache::set('dismissed_planned_expense_' . $plannedExpense->id, true, 60 * 60 * 24 * 10);
        return back();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('planned-expenses.create', [
            'categories' => Category::orderBy('name', 'asc')->get(),
            'places' => Place::orderBy('name', 'asc')->get(),
            'currencies' => Currency::orderBy('name', 'asc')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePlannedExpenseRequest $request)
    {
        $plannedExpense = new PlannedExpense();
        $plannedExpense->fill($request->validated());
        $plannedExpense->user_id = auth()->id();
        $plannedExpense->save();

        return redirect()->route('planned-expenses.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(PlannedExpense $plannedExpense)
    {
        return view('planned-expenses.show', [
            'plannedExpense' => $plannedExpense,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PlannedExpense $plannedExpense)
    {
        return view('planned-expenses.edit', [
            'plannedExpense' => $plannedExpense,
            'categories' => Category::orderBy('name', 'asc')->get(),
            'places' => Place::orderBy('name', 'asc')->get(),
            'currencies' => Currency::orderBy('name', 'asc')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePlannedExpenseRequest $request, PlannedExpense $plannedExpense)
    {
        $plannedExpense->fill($request->validated());
        $plannedExpense->save();

        return redirect()->route('planned-expenses.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PlannedExpense $plannedExpense)
    {
        $plannedExpense->delete();
        return redirect()->route('planned-expenses.index');
    }
}
