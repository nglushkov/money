<?php

namespace App\Listeners;

use App\Enum\CacheKey;
use App\Events\CurrencyProcessed;

class ClearDefaultCurrencyCache
{
    /**
     * Create the event listener.
     */
    public function __construct(CurrencyProcessed $event)
    {
    }

    /**
     * Handle the event.
     */
    public function handle(CurrencyProcessed $event): void
    {
        cache()->forget(CacheKey::default_currency->name);
        logger('CurrencyProcessed event handled', ['id' => $event->currency->id, 'type' => get_class($event->currency)]);
    }
}
