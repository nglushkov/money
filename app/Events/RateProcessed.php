<?php

namespace App\Events;

use App\Models\Rate;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RateProcessed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Rate $rate;

    /**
     * Create a new event instance.
     */
    public function __construct(Rate $rate)
    {
        $this->rate = $rate;
        logger('RateProcessed event created', ['id' => $rate->id]);
    }
}
