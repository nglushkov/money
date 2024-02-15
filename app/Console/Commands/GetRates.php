<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\ExternalRate;
use App\Models\Currency;
use App\Models\Rate;

class GetRates extends Command
{
    const FROM_CURRENCY_ID = 'USD';
    const TO_CURRENCY_ID = 'RUB';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rates:get';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get rates from external API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            logger()->info('Getting rates');

            $fromCurrencyId = Currency::where('name', self::FROM_CURRENCY_ID)->first()->id;
            $toCurrencyId = Currency::where('name', self::TO_CURRENCY_ID)->first()->id;

            $response = Http::get('https://www.cbr-xml-daily.ru/daily_json.js');
            $data = $response->json();

            $dataDate = date('Y-m-d', strtotime($data['Timestamp']));
            $rate = ExternalRate::where('from_currency_id', $fromCurrencyId)
                ->where('to_currency_id', $toCurrencyId)
                ->where('date', $dataDate)
                ->count();

            if ($rate > 0) {
                logger()->info('Rates already updated');
                return;
            }

            $fromValue = $data['Valute'][self::FROM_CURRENCY_ID]['Value'];

            $rate = new ExternalRate();
            $rate->from_currency_id = $fromCurrencyId;
            $rate->to_currency_id = $toCurrencyId;
            $rate->date = $dataDate;
            $rate->rate = $fromValue;
            $rate->save();

            $rate = Rate::where('from_currency_id', $fromCurrencyId)
                ->where('to_currency_id', $toCurrencyId)
                ->where('date', $dataDate)
                ->count();

            $rate = new Rate();
            $rate->from_currency_id = $fromCurrencyId;
            $rate->to_currency_id = $toCurrencyId;
            $rate->date = $dataDate;
            $rate->rate = $fromValue;
            $rate->save();
            
        } catch (\Exception $e) {
            logger()->error('Error while getting rates', ['error' => $e->getMessage()]);
        }

        logger()->info('Rates updated');
        
    }
}
