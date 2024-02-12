<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Move extends Model
{
    protected $dispatchesEvents = [
        'created' => \App\Events\MoveProcessed::class,
        'updated' => \App\Events\MoveProcessed::class,
        'deleted' => \App\Events\MoveProcessed::class,
    ];

    public function getRelatedBills(): array
    {
        if ($this instanceof Operation) {
            return [$this->bill];
        }
        else if ($this instanceof Transfer) {
            return [$this->from, $this->to];
        }
        else if ($this instanceof Exchange) {
            return [$this->from, $this->to];
        }
        return [];
    }
}