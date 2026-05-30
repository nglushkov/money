<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExchangerRequest;
use App\Models\Bill;
use App\Models\Currency;
use App\Models\ExchangePlace;
use App\Service\ExchangerService;

class ExchangerController extends Controller
{
    public function __construct(private readonly ExchangerService $service) {}

    public function create()
    {
        $cryptoBills      = Bill::isCrypto()->with('user')->orderBy('name')->get();
        $cryptoCurrencies = Currency::isCrypto()->orderBy('name')->get();
        $fiatCurrencies   = Currency::where('is_crypto', false)->orderBy('name')->get();
        $targetBills      = Bill::where('is_crypto', false)->with('user')->orderBy('name')->get();
        $places           = ExchangePlace::orderBy('name')->get();

        $defaultCryptoCurrency = Currency::getDefaultCurrency(true);

        return view('exchanger.create', [
            'cryptoBills'          => $cryptoBills,
            'cryptoCurrencies'     => $cryptoCurrencies,
            'fiatCurrencies'       => $fiatCurrencies,
            'targetBills'          => $targetBills,
            'places'               => $places,
            'defaultCryptoCurrency'=> $defaultCryptoCurrency,
        ]);
    }

    public function store(StoreExchangerRequest $request)
    {
        $this->service->store($request->validated());

        return redirect()->route('exchanges.index');
    }
}
