<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExchangeRequest;
use App\Http\Requests\UpdateExchangeRequest;
use App\Models\Exchange;
use App\Models\Currency;
use App\Models\Bill;
use App\Models\ExchangePlace;
use App\Models\Rate;
use Illuminate\Support\Facades\DB;

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
            'bills' => Bill::orderBy('name')->get(),
            'places' => ExchangePlace::orderBy('name')->get(),
            'defaultCurrency' => Currency::getDefaultCurrency(),
        ]);
    }

    public function store(StoreExchangeRequest $request)
    {
        try {
            $data = $request->validated();

            /** @var Bill $bill */
            $bill = Bill::findOrFail($data['bill_id']);

            $fromCurrencyId = $data['from_currency_id'];

            $billAmount = $bill->getAmount($fromCurrencyId);
            if ($billAmount < $data['amount_from']) {
                throw new \Exception(
                    'Not enough money in the bill: ' . $bill->getAmountWithCurrency($fromCurrencyId) . '<br>' .
                    '<a target="_blank" href="' . route('bills.show', $bill) . '">Go to the bill</a>'
                );
            }

            DB::transaction(function () use ($data) {
                $placeId = $data['place_id'];

                $place = $placeId ? ExchangePlace::findOrFail($placeId) : null;
                $placeName = $data['place_name'] ?? null;

                if (!empty($placeName)) {
                    $place = ExchangePlace::create(['name' => $placeName]);
                }
                $data['place_id'] = $place->id;

                $exchange = new Exchange($data);

                if (isset($data['create_currency_rate'])) {
                    $rate = Rate::where('from_currency_id', $exchange->from_currency_id)
                        ->where('to_currency_id', $exchange->to_currency_id)
                        ->where('date', $exchange->date)
                        ->count();

                    if ($rate > 0) {
                        throw new \Exception('Rate already exists');
                    }

                    $rate = Rate::create([
                        'from_currency_id' => $exchange->from_currency_id,
                        'to_currency_id' => $exchange->to_currency_id,
                        'date' => $exchange->date,
                        'rate' => bcdiv($exchange->amount_to, $exchange->amount_from, 2),
                    ]);
                }

                $exchange->user_id = auth()->id();
                $exchange->save();

                if (isset($rate)) {
                    $rate->exchange_id = $exchange->id;
                    $rate->save();
                }
            });
        } catch (\Exception $e) {
            return redirect()->route('exchanges.create')
                ->withErrors([$e->getMessage()])
                ->withInput($request->all());
        }

        return redirect()->route('home');
    }

    /**
     * Display the specified resource.
     */
    public function show(Exchange $exchange)
    {
        return view('exchanges.show', [
            'exchange' => $exchange,
            'defaultCurrency' => Currency::getDefaultCurrency(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Exchange $exchange)
    {
        return view('exchanges.edit', [
            'exchange' => $exchange,
            'currencies' => Currency::orderBy('name')->get(),
            'bills' => Bill::orderBy('name')->get(),
            'places' => ExchangePlace::orderBy('name')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateExchangeRequest $request, Exchange $exchange)
    {
        try {
            $data = $request->validated();

            /** @var Bill $bill */
            $bill = Bill::findOrFail($data['bill_id']);

            $fromCurrencyId = $data['from_currency_id'];

            $billAmount = $bill->getAmount($fromCurrencyId);
            if ($data['amount_from'] > $exchange->amount_from && $billAmount < $data['amount_from'] - $exchange->amount_from) {
                throw new \Exception(
                    'Not enough money in the bill: ' . $bill->getAmountWithCurrency($fromCurrencyId) . '<br>' .
                    '<a target="_blank" href="' . route('bills.show', $bill) . '">Go to the bill</a>'
                );
            }

            DB::transaction(function () use ($data, $exchange) {
                $placeId = $data['place_id'];

                $place = $placeId ? ExchangePlace::findOrFail($placeId) : null;
                $placeName = $data['place_name'] ?? null;

                if (!empty($placeName)) {
                    $place = ExchangePlace::create(['name' => $placeName]);
                }
                $data['place_id'] = $place->id;

                $exchange->fill($data);
                $exchange->save();
            });
        } catch (\Exception $e) {
            return redirect()->route('exchanges.edit', $exchange)
                ->withErrors([$e->getMessage()])
                ->withInput($request->all());
        }

        return redirect()->route('exchanges.show', $exchange);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Exchange $exchange)
    {
        $exchange->delete();
        return redirect()->route('home');
    }
}
