<?php

namespace App\Models;

use App\Events\MoveProcessed;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\MoneyFormatter;

class Move extends Model
{
    protected $dispatchesEvents = [
        'created' => MoveProcessed::class,
        'updated' => MoveProcessed::class,
        'deleted' => MoveProcessed::class,
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    public function getDateFormattedAttribute($value): string
    {
        return $this->date->format('d.m.Y');
    }

    public function getDateHumansAttribute(): string
    {
        return $this->date->diffForHumans();
    }

    public function getRelatedBills(): array
    {
        if ($this instanceof Operation) {
            return [$this->bill];
        }
        else if ($this instanceof Transfer) {
            return [$this->from, $this->to];
        }
        else if ($this instanceof Exchange) {
            return [$this->bill];
        }
        return [];
    }
}
