<?php

namespace App\Console\Commands;

use App\Models\Currency;
use App\Models\Rate;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class GetCryptoRates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:get-crypto-rates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch the latest cryptocurrency rates from CoinMarketCap';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Замените 'your-api-key' на ваш ключ API CoinMarketCap
        $apiKey = env('COINMARKETCAP_API_KEY');

        $symbolsStrings = Currency::where('is_crypto', true)
            ->where('id', '!=', Currency::getDefaultCurrencyId(true))
            ->pluck('name')->implode(',');

        $response = Http::withHeaders([
            'Accepts' => 'application/json',
            'X-CMC_PRO_API_KEY' => $apiKey,
        ])->get('https://pro-api.coinmarketcap.com/v1/cryptocurrency/quotes/latest', [
            'symbol' => $symbolsStrings,
            'convert' => Currency::getDefaultCurrencyName(true),
        ]);

        if ($response->successful()) {
            $data = $response->json();
            foreach ($data['data'] as $crypto) {
                $rateInverted = 1 / $crypto['quote']['USDT']['price'];
                $rate = Rate::firstOrNew([
                    'from_currency_id' => Currency::getDefaultCurrencyId(true),
                    'to_currency_id' => Currency::where('name', $crypto['symbol'])->first()->id,
                    'rate' => $rateInverted,
                    'date' => now(),
                ]);
                $rate->save();
                logger()->info('Fetched rate for ' . $crypto['symbol'] . ' at ' . $rate->rate);
            }
        } else {
            $this->error('Failed to fetch cryptocurrency rates.');
        }
    }
}
