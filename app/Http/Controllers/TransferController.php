<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransferRequest;
use App\Http\Requests\UpdateTransferRequest;
use App\Models\Transfer;
use App\Models\Bill;
use App\Models\Currency;

class TransferController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('transfers.index', [
            'transfers' => Transfer::with('from', 'to', 'currency')->orderBy('date', 'desc')->paginate(20)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('transfers.create', [
            'bills' => Bill::orderBy('name')->get(),
            'currencies' => Currency::orderBy('is_default', 'desc')->orderBy('name', 'asc')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTransferRequest $request)
    {
        $transfer = new Transfer();
        $transfer->fill($request->validated());
        $transfer->user_id = auth()->id();
        $transfer->save();

        return redirect()->route('home');
    }

    /**
     * Display the specified resource.
     */
    public function show(Transfer $transfer)
    {
        return view('transfers.show', [
            'transfer' => $transfer
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transfer $transfer)
    {
        return view('transfers.edit', [
            'transfer' => $transfer,
            'bills' => Bill::orderBy('name')->get(),
            'currencies' => Currency::orderBy('name')->orderBy('is_default')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTransferRequest $request, Transfer $transfer)
    {
        $transfer->fill($request->validated());
        $transfer->date = $request->date;
        $transfer->save();

        return redirect()->route('transfers.show', $transfer);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transfer $transfer)
    {
        $transfer->delete();

        return redirect()->route('home');
    }
}
