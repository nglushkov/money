<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\ExternalRate;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use App\Models\Currency;

class GetUsdArsRates extends Command
{
    const FROM_CURRENCY_ID = 'USD';
    const TO_CURRENCY_ID = 'ARS';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rates:get-usd-ars';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get USD/ARS rates from external API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            logger()->info('Getting USD/ARS rates');

            $fromCurrencyId = Currency::where('name', self::FROM_CURRENCY_ID)->first()->id;
            $toCurrencyId = Currency::where('name', self::TO_CURRENCY_ID)->first()->id;

            $response = Http::get('https://dolarapi.com/v1/dolares/blue');
            $data = $response->json();
            logger()->info('USD/ARS rates received', ['data' => $data]);

            $dataDate = date('Y-m-d', strtotime($data['fechaActualizacion']));
            $rate = ExternalRate::where('from_currency_id', $fromCurrencyId)
                ->where('to_currency_id', $toCurrencyId)
                ->where('date', $dataDate)
                ->count();

            if ($rate > 0) {
                logger()->info('USD/ARS rates already updated');
                return;
            }

            $rate = new ExternalRate();
            $rate->from_currency_id = $fromCurrencyId;
            $rate->to_currency_id = $toCurrencyId;
            $rate->date = $dataDate;
            $rate->buy = $data['compra'];
            $rate->sell = $data['venta'];
            $rate->save();
            
        } catch (\Exception $e) {
            logger()->error('Error while getting rates', ['error' => $e->getMessage()]);
        }

        logger()->info('USD/ARS rates updated');
    }
}
