<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExchangeRequest;
use App\Http\Requests\UpdateExchangeRequest;
use App\Models\Exchange;
use App\Models\Currency;
use App\Models\Bill;
use App\Models\ExchangePlace;
use App\Models\Rate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExchangeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $exchanges = Exchange::with('from', 'to', 'bill')->orderBy('date', 'desc');
        if ($request->filled('currency_id')) {
            $exchanges = $exchanges->where('from_currency_id', $request->get('currency_id'))
                ->orWhere('to_currency_id', $request->get('currency_id'))
                ->orderBy('date', 'desc')
                ->orderBy('id', 'desc');
        }
        $exchanges = $exchanges->paginate(50)->appends($request->all());

        return view('exchanges.index', [
            'exchanges' => $exchanges,
            'currencies' => Currency::orderby('is_crypto')->orderBy('name')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        if ($request->has('is_crypto')) {
            $currencies = Currency::where('is_crypto', true)->orderBy('name')->get();
            $bills = Bill::isCrypto()->orderBy('name')->get();
        } else {
            $currencies = Currency::orderBy('name')->get();
            $bills = Bill::orderBy('name')->get();
        }

        return view('exchanges.create', [
            'currencies' => $currencies,
            'bills' => $bills,
            'places' => ExchangePlace::orderBy('name')->get(),
            'defaultCurrency' => Currency::getDefaultCurrency($request->has('is_crypto')),
            'copyExchange' => Exchange::findOrNew($request->get('copy_id')),
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
                // @todo: Refactor this
                throw new \Exception(
                    'Not enough money in the bill: ' . $bill->getAmountWithCurrency($fromCurrencyId) . '<br>' .
                    '<a target="_blank" href="' . route('bills.show', $bill) . '">Go to the bill</a>'
                );
            }

            DB::transaction(function () use ($data) {
                $placeName = $data['place_name'] ?? null;

                if (!empty($placeName)) {
                    $place = ExchangePlace::create(['name' => $placeName]);
                    $data['place_id'] = $place->id;
                }

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
            return redirect()->route('exchanges.create', $request->query())
                ->withErrors([$e->getMessage()])
                ->withInput($request->all());
        }

        return redirect()->route('exchanges.index');
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
                $placeName = $data['place_name'] ?? null;

                if (!empty($placeName)) {
                    $place = ExchangePlace::create(['name' => $placeName]);
                    $data['place_id'] = $place->id;
                }

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
        DB::transaction(function () use ($exchange) {
            Rate::find($exchange->id)->delete();
            $exchange->delete();
        });
        return redirect()->route('home');
    }
}
