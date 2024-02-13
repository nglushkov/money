<?php

namespace App\Models;

use App\Helpers\MoneyFormatter;
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

    public function getAmountFromFormattedAttribute(): string
    {
        return MoneyFormatter::getWithSymbol($this->amount_from, $this->from->name);
    }

    public function getAmountToFormattedAttribute(): string
    {
        return MoneyFormatter::getWithSymbol($this->amount_to, $this->to->name);
    }

    public function getRateFormattedAttribute(): string
    {
        return MoneyFormatter::get(bcdiv($this->amount_to, $this->amount_from, 6));
    }

    public function getRateTextAttribute($value): string
    {
        return sprintf('1 %s = %s %s', $this->to->name, $this->rate_formatted, $this->from->name);
    }
}
