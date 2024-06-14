<?php

namespace App\Models;

use App\Events\RateProcessed;
use App\Helpers\MoneyFormatter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property Currency currencyFrom
 * @property Currency currencyTo
 */
class Rate extends Model
{
    use HasFactory;

    protected $fillable = [
        'from_currency_id',
        'to_currency_id',
        'date',
        'rate',
        'exchange_id',
    ];

    protected $dispatchesEvents = [
        'created' => RateProcessed::class,
        'updated' => RateProcessed::class,
        'deleted' => RateProcessed::class,
    ];

    public function currencyFrom()
    {
        return $this->belongsTo(Currency::class, 'from_currency_id');
    }

    public function currencyTo()
    {
        return $this->belongsTo(Currency::class, 'to_currency_id');
    }

    public function exchange()
    {
        return $this->belongsTo(Exchange::class);
    }

    public function getDateFormattedAttribute()
    {
        return date('d.m.Y', strtotime($this->date));
    }

    public function getRateHumanReadableAttribute()
    {
        return sprintf(
            '1 %s = %s %s (%s)',
            $this->currencyFrom->name,
            MoneyFormatter::getWithoutTrailingZeros($this->rate),
            $this->currencyTo->name,
            $this->date_formatted
        );
    }
}
