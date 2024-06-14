<?php

namespace App\Http\Controllers;

use App\Enum\CacheKey;
use App\Models\Bill;
use App\Models\Currency;
use Illuminate\Http\Request;

class CryptoController extends Controller
{
    public function index(Request $request)
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
}
