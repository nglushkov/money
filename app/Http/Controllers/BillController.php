<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBillRequest;
use App\Http\Requests\UpdateBillRequest;
use App\Models\Bill;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BillController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bills = Bill::orderBy('name');

        return view('bills.index', [
            'bills' => $bills->get(),
            'currencies' => Currency::orderBy('name')->get(),
            'bill'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('bills.create', [
            'currencies' => Currency::orderBy('name')->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBillRequest $request)
    {
        if (!$request->validated()) {
            return redirect()->route('bills.create')->withErrors($request->errors());
        }
        
        DB::transaction(function () use ($request) {
            $bill = new Bill($request->validated());
            $bill->user_id = auth()->id();
            $bill->save();

            foreach ($request->input('amount') as $currencyId => $amount) {
                $bill->currencies()->attach($currencyId, ['amount' => $amount]);
            }
        });


        return redirect()->route('bills.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Bill $bill)
    {
        return view('bills.show', [
            'bill' => $bill
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bill $bill)
    {
        return view('bills.edit', [
            'bill' => $bill
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBillRequest $request, Bill $bill)
    {
        $bill->update($request->validated());

        return redirect()->route('bills.show', $bill);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bill $bill)
    {
        if ($bill->operations()->count() > 0) {
            return redirect()->route('bills.show', $bill)->withErrors(['error' => 'This bill is used in operations and cannot be deleted.']);
        }

        try {
            $bill->delete();
        } catch (\Exception $e) {
            return redirect()->route('bills.show', $bill)->with('error', 'Error deleting bill.');
        }

        return redirect()->route('bills.index');
    }
}
