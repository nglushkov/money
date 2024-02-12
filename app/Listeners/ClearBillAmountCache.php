<?php

namespace App\Listeners;

use App\Events\MoveProcessed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log as Monolog;

class ClearBillAmountCache
{
    /**
     * Create the event listener.
     */
    public function __construct(MoveProcessed $event)
    {
    }

    /**
     * Handle the event.
     */
    public function handle(MoveProcessed $event): void
    {
        $relatedBills = $event->move->getRelatedBills();
        Monolog::info('MoveProcessed event handled', ['id' => $event->move->id, 'type' => get_class($event->move)]);
        foreach ($relatedBills as $bill) {
            $bill->clearAmountCache();
            Monolog::info('Bill amount cache cleared for bill', ['id' => $bill->id, 'name' => $bill->name]);
        }
    }
}
