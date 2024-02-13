<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\MoneyFormatter;

class Move extends Model
{
    protected $dispatchesEvents = [
        'created' => \App\Events\MoveProcessed::class,
        'updated' => \App\Events\MoveProcessed::class,
        'deleted' => \App\Events\MoveProcessed::class,
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    public function getAmountAsMoneyAttribute(): string
    {
        return MoneyFormatter::get($this->amount);
    }

    public function getDateFormattedAttribute($value): string
    {
        return $this->date->format('d.m.Y');
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
