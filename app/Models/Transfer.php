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

    public function getAmountTextAttribute($value): string
    {
        if ($this->currency->is_crypto) {
            return MoneyFormatter::getCryptoWithCurrencyNameAndSign($this->amount, $this->currency->name, $this->type);
        }
        return MoneyFormatter::getWithCurrencyNameAndSign($this->amount, $this->currency->name, $this->type);
    }

    public function getAmountTextWithCurrencyAttribute($value): string
    {
        if ($this->currency->is_crypto) {
            return MoneyFormatter::getCryptoWithCurrencyName($this->amount, $this->currency->name);
        }
        return MoneyFormatter::getWithCurrencyName($this->amount, $this->currency->name);
    }

    public function getAmountFormattedAttribute(): string
    {
        return MoneyFormatter::get($this->amount);
    }
}
