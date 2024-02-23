<?php

namespace App\Events;

use App\Models\Currency;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log as Monolog;

class CurrencyProcessed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Currency $currency;

    /**
     * Create a new event instance.
     */
    public function __construct(Currency $currency)
    {
        $this->currency = $currency;
        MonoLog::info('CurrencyProcessed event created', ['id' => $currency->id]);
    }
}
