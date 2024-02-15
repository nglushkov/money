<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\MoneyFormatter;

class ExternalRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'from_currency_id',
        'to_currency_id',
        'date',
        'buy',
        'sell'
    ];

    protected $casts = [
        'date' => 'datetime'
    ];

    public function getDateAttribute($value): string
    {
        return date('d.m.Y', strtotime($value));
    }

    public function getBuyAttribute($value): string
    {
        return MoneyFormatter::get($value);
    }

    public function getSellAttribute($value): string
    {
        return MoneyFormatter::get($value);
    }

    public function fromCurrency()
    {
        return $this->belongsTo(Currency::class, 'from_currency_id');
    }

    public function toCurrency()
    {
        return $this->belongsTo(Currency::class, 'to_currency_id');
    }
}
