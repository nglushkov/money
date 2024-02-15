<?php

namespace App\Models;

use App\Helpers\MoneyFormatter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use DateTimeInterface;

class Operation extends Move
{
    use HasFactory;

    protected $fillable = [
        'amount',
        'type',
        'bill_id',
        'category_id',
        'currency_id',
        'place_id',
        'notes',
        'date',
    ];

    public function getIsExpenseAttribute(): bool
    {
        return $this->type === 0;
    }

    public function getIsIncomeAttribute(): bool
    {
        return $this->type === 1;
    }

    public function bill(): BelongsTo
    {
        return $this->belongsTo(Bill::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function place(): BelongsTo
    {
        return $this->belongsTo(Place::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getTypeNameAttribute(): string
    {
        return $this->is_expense ? 'Расход' : 'Приход';
    }

    public function getAmountTextAttribute(): string
    {
        return MoneyFormatter::getWithSymbolAndSign($this->amount, $this->currency->name, $this->type);
    }

    public function getAmountTextWithCurrencyAttribute(): string
    {
        return MoneyFormatter::getWithSymbol($this->amount, $this->currency->name);
    }

    public function getAmountFormattedAttribute(): string
    {
        return MoneyFormatter::get($this->amount);
    }

    public function getAmountInDefaultCurrencyAttribute(): float
    {
        return $this->currency->convertToDefault($this->amount, $this->date);
    }

    public function getAmountInDefaultCurrencyFormattedAttribute(): string
    {
        if ($this->amount_in_default_currency == 0) {
            return '';
        }
        return MoneyFormatter::getWithSymbol($this->amount_in_default_currency, $this->currency->defaultCurrency->name);
    }

    public function scopeLatestDate($query)
    {
        return $query->orderBy('date', 'desc');
    }
}
