<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\MoneyFormatter;

class Transfer extends Move
{
    use HasFactory;

    protected $fillable = [
        'amount',
        'from_bill_id',
        'to_bill_id',
        'user_id',
        'notes',
        'date',
        'currency_id',
    ];

    protected $casts = [
        'date' => 'datetime',
    ];
    
    public function from()
    {
        return $this->belongsTo(Bill::class, 'from_bill_id');
    }

    public function to()
    {
        return $this->belongsTo(Bill::class, 'to_bill_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function getAmountAsMoneyAttribute(): string
    {
        return MoneyFormatter::get($this->amount);
    }

    public function getAmountTextAttribute($value): string
    {
        return MoneyFormatter::getWithSymbolAndSign($this->amount, $this->currency->name, $this->type);
    }

    public function getDateFormattedAttribute($value): string
    {
        return $this->date->format('d.m.Y');
    }

    public function getAmountTextWithCurrencyAttribute($value): string
    {
        return MoneyFormatter::getWithSymbol($this->amount, $this->currency->name);
    }
}
