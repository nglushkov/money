<?php

namespace App\Models;

use App\Helpers\MoneyFormatter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use DateTimeInterface;

class Operation extends Model
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

    protected $casts = [
        'date' => 'datetime',
    ];

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
        return $this->type === 0 ? 'Расход' : 'Приход';
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
