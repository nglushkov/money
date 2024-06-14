<?php

namespace App\Http\Controllers;

use App\Enum\CacheKey;
use App\Models\Bill;
use App\Models\CryptoBill;
use App\Models\Currency;
use Illuminate\Http\Request;

class CryptoController extends Controller
{
    public function index()
    {
        $ratesUpdatedAt = '';
        if (cache()->has(CacheKey::crypto_currency_rate_update_time->name)) {
            $ratesUpdatedAt = cache()->get(CacheKey::crypto_currency_rate_update_time->name)->format('d.m.Y H:i:s');
        }

        $bills = Bill::isCrypto();

        return view('crypto.index', [
            'cryptoCurrencies' => Currency::isCrypto()->orderBy('name')->get(),
            'bills' => $bills->orderBy('name')->get(),
            'ratesUpdatedAt' => $ratesUpdatedAt,
        ]);
    }

    public function setTotalInvestedAmount(Request $request, Bill $bill)
    {
        CryptoBill::updateOrCreate([
            'bill_id' => $bill->id,
            'currency_id' => $request->get('currency_id'),
        ], [
            'total_invested_amount' => $request->get('amount'),
        ]);

        return redirect()->route('crypto.index');
    }
}
