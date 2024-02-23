<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Move;
use Illuminate\Support\Facades\Log as Monolog;

class MoveProcessed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Move $move;

    /**
     * Create a new event instance.
     */
    public function __construct(Move $move)
    {
        $this->move = $move;
        MonoLog::info('MoveProcessed event created', ['id' => $move->id, 'type' => get_class($move)]);
    }
}
