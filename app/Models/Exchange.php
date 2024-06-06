<?php

namespace App\Models;

use App\Helpers\MoneyFormatter;
use App\Helpers\MoneyHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exchange extends Move
{
    use HasFactory;

    protected $fillable = [
        'from_currency_id',
        'amount_from',
        'to_currency_id',
        'amount_to',
        'bill_id',
        'date',
        'notes',
        'place_id',
    ];

    public function from()
    {
        return $this->belongsTo(Currency::class, 'from_currency_id');
    }

    public function to()
    {
        return $this->belongsTo(Currency::class, 'to_currency_id');
    }

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function place()
    {
        return $this->belongsTo(ExchangePlace::class);
    }

    public function getAmountFromFormattedAttribute(): string
    {
        if ($this->from->is_crypto) {
            return MoneyFormatter::getCryptoWithCurrencyName($this->amount_from, $this->from->name);
        }
        return MoneyFormatter::getWithCurrencyName($this->amount_from, $this->from->name);
    }

    public function getAmountToFormattedAttribute(): string
    {
        if ($this->to->is_crypto) {
            return MoneyFormatter::getCryptoWithCurrencyName($this->amount_to, $this->to->name);
        }
        return MoneyFormatter::getWithCurrencyName($this->amount_to, $this->to->name);
    }

    public function getRateFormattedAttribute(): string
    {
        if ($this->from->is_crypto && $this->to->is_crypto) {
            return MoneyFormatter::getWithoutTrailingZeros(
                MoneyHelper::divide($this->amount_from, $this->amount_to, 6)
            );
        }
        return MoneyFormatter::get(bcdiv($this->amount_to, $this->amount_from, 6));
    }

    public function getRateTextAttribute(): string
    {
        if ($this->from->is_crypto && $this->to->is_crypto) {
            return sprintf('1 %s = %s %s', $this->to->name, $this->rate_formatted, $this->from->name);
        }
        return sprintf('1 %s = %s %s', $this->from->name, $this->rate_formatted, $this->to->name);
    }
}
