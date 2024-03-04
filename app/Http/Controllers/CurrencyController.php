<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCurrencyRequest;
use App\Http\Requests\UpdateCurrencyRequest;
use App\Models\Currency;
use App\Models\Rate;

class CurrencyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('currencies.index', [
            'currencies' => Currency::orderBy('name')->get()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('currencies.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCurrencyRequest $request)
    {
        Currency::create($request->validated());

        return redirect()->route('currencies.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Currency $currency)
    {
        $currencies = [];
        if ($currency->is_default) {
            $currencies = Currency::where('is_default', false)->orderBy('name')->get();
        }
        $lastOperations = $currency->operations()->with([
            'bill',
            'category',
            'currency',
            'place',
        ])->latestDate()->paginate(10);

        $currencyRates = $currency->ratesFrom()
            ->with(['currencyFrom'])
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc');
        if (request()->has('rate_currency_id')) {
            $currencyRates->where('to_currency_id', request('rate_currency_id'));
        }
        $currencyRates = $currencyRates->paginate(20);
        $currencyRates->appends(request()->query());

        return view('currencies.show', compact('currency', 'currencies', 'currencyRates', 'lastOperations'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Currency $currency)
    {
        return view('currencies.edit', compact('currency'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCurrencyRequest $request, Currency $currency)
    {
        $currency->name = $request->name;
        $currency->save();

        return redirect()->route('currencies.show', $currency);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Currency $currency)
    {
        if ($currency->operations()->count() > 0) {
            return redirect()->route('currencies.show',$currency)->withErrors(['error' => 'This currency is used in operations and cannot be deleted.']);
        }
        $currency->delete();

        return redirect()->route('currencies.index');
    }
}
