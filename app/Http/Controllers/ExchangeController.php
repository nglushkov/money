<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExchangeRequest;
use App\Http\Requests\UpdateExchangeRequest;
use App\Models\Exchange;
use App\Models\Currency;
use App\Models\Bill;

class ExchangeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('exchanges.index', [
            'exchanges' => Exchange::with('from', 'to', 'bill')->orderBy('date', 'desc')->latest()->paginate(20)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('exchanges.create', [
            'currencies' => Currency::orderBy('name')->get(),
            'bills' => Bill::all()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreExchangeRequest $request)
    {
        $exchange = new Exchange($request->validated());
        $exchange->user_id = auth()->id();
        $exchange->save();
        
        return redirect()->route('home');
    }

    /**
     * Display the specified resource.
     */
    public function show(Exchange $exchange)
    {
        return view('exchanges.show', [
            'exchange' => $exchange
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Exchange $exchange)
    {
        return view('exchanges.edit', [
            'exchange' => $exchange,
            'currencies' => Currency::all(),
            'bills' => Bill::all()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateExchangeRequest $request, Exchange $exchange)
    {
        $exchange->update($request->validated());
        return redirect()->route('exchanges.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Exchange $exchange)
    {
        $exchange->delete();
        return redirect()->route('exchanges.index');
    }
}
