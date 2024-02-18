<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOperationRequest;
use App\Http\Requests\UpdateOperationRequest;
use App\Models\Operation;
use App\Models\Bill;
use App\Models\Category;
use App\Models\PlannedExpense;
use App\Models\Scopes\IsNotCorrectionScope;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Place;
use App\Models\Currency;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class OperationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $operations = Operation::orderBy('date', 'desc')->withoutGlobalScope(IsNotCorrectionScope::class)->latest();

        if ($request->get('bill_id')) {
            $operations->where('bill_id', $request->bill_id);
        }

        if ($request->get('category_id')) {
            $operations->where('category_id', $request->category_id);
        }

        if ($request->get('date_from')) {
            $operations->where('date', '>=', $request->date_from);
        }

        if ($request->get('date_to')) {
            $operations->where('date', '<=', $request->date_to);
        }

        if (in_array($request->type, ['0', '1'], true)) {
            $operations->where('type', $request->type);
        }

        if ($request->get('user_id')) {
            $operations->where('user_id', $request->user_id);
        }

        if ($request->get('place_id')) {
            $operations->where('place_id', $request->place_id);
        }

        if ($request->get('notes')) {
            $operations->where('notes', 'like', '%' . $request->notes . '%');
        }

        if ($request->get('amount_from')) {
            $operations->where('amount', '>=', $request->amount_from);
        }

        if ($request->get('amount_to')) {
            $operations->where('amount', '<=', $request->amount_to);
        }

        return view('operations.index', [
            'operations' => $operations->isNotDraft()->with(['bill', 'category', 'user', 'place', 'currency'])->paginate(50),
            'bills' => Bill::orderBy('name', 'asc')->get(),
            'categories' => Category::orderBy('name', 'asc')->get(),
            'users' => User::orderBy('name', 'asc')->get(),
            'places' => Place::orderBy('name', 'asc')->get(),
            'defaultCurrency' => Currency::default()->first(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        if ($request->get('planned_expense_id')) {
            $plannedExpense = PlannedExpense::findOrFail($request->planned_expense_id);
        }

        return view('operations.create', [
            'bills' => Bill::orderBy('name', 'asc')->get(),
            'categories' => Category::orderBy('name', 'asc')->get(),
            'users' => User::orderBy('name', 'asc')->get(),
            'places' => Place::orderBy('name', 'asc')->get(),
            'currencies' => Currency::orderBy('name', 'asc')->get(),

            'topCategories' => Category::select('categories.*', DB::raw('COUNT(operations.category_id) as count'))
                ->leftJoin('operations', 'categories.id', '=', 'operations.category_id')
                ->groupBy('categories.id')
                ->orderBy('count', 'desc')
                ->take(10)
                ->get(),

            'topPlaces' => Place::select('places.*', DB::raw('COUNT(operations.place_id) as count'))
                ->leftJoin('operations', 'places.id', '=', 'operations.place_id')
                ->groupBy('places.id')
                ->orderBy('count', 'desc')
                ->take(15)
                ->get(),
            'topBills' => Bill::select('bills.*', DB::raw('COUNT(operations.bill_id) as count'))
                ->leftJoin('operations', 'bills.id', '=', 'operations.bill_id')
                ->groupBy('bills.id')
                ->orderBy('count', 'desc')
                ->take(5)
                ->get(),
            'plannedExpense' => $plannedExpense ?? null,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOperationRequest $request)
    {
        $operation = new Operation();
        $operation->fill($request->validated());
        $operation->user_id = auth()->id();
        $operation->save();

        return redirect()->route('home');
    }

    /**
     * Display the specified resource.
     */
    public function show(Operation $operation)
    {
        return view('operations.show', [
            'operation' => $operation,
            'defaultCurrency' => Currency::default()->first(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Operation $operation)
    {
        return view('operations.edit', [
            'operation' => $operation,
            'bills' => Bill::orderBy('name', 'asc')->get(),
            'categories' => Category::orderBy('name', 'asc')->get(),
            'users' => User::orderBy('name', 'asc')->get(),
            'places' => Place::orderBy('name', 'asc')->get(),
            'currencies' => Currency::orderBy('name', 'asc')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOperationRequest $request, Operation $operation)
    {
        $operation->fill($request->validated());
        $operation->date = $request->date;
        $operation->is_draft = false;
        $operation->save();

        return redirect()->route('operations.show', $operation);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Operation::withoutGlobalScope(IsNotCorrectionScope::class)->findOrFail($id)->delete();

        return redirect()->route('operations.index');
    }
}
