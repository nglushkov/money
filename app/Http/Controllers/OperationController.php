<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOperationRequest;
use App\Http\Requests\UpdateOperationRequest;
use App\Models\Operation;
use App\Models\Bill;
use App\Models\Category;
use Illuminate\Http\Request;

class OperationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // add filters
        $operations = Operation::orderBy('date', 'desc');

        if ($request->has('bill_id')) {
            $operations->where('bill_id', $request->bill_id);
        }

        if ($request->has('category_id')) {
            $operations->where('category_id', $request->category_id);
        }

        return view('operations.index', [
            'operations' => $operations->paginate(50),
            'bills' => Bill::orderBy('name', 'asc')->get(),
            'categories' => Category::orderBy('name', 'asc')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('operations.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOperationRequest $request)
    {
        Operation::create($request->validated());

        return redirect()->route('operations.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Operation $operation)
    {
        return view('operations.show', [
            'operation' => $operation
        ]);       
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Operation $operation)
    {
        return view('operations.edit', [
            'operation' => $operation
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOperationRequest $request, Operation $operation)
    {
        $operation->update($request->validated());

        return redirect()->route('operations.show', $operation);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Operation $operation)
    {
        $operation->delete();

        return redirect()->route('operations.index');
    }
}
