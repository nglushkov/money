<?php

namespace App\Models;

use App\Helpers\MoneyFormatter;
use App\Models\Scopes\IsNotCorrectionScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;

#[ScopedBy([IsNotCorrectionScope::class])]
class Operation extends Move
{
    use HasFactory;

    // @todo: refactor to enum
    CONST TYPE_EXPENSE = 0;
    CONST TYPE_INCOME = 1;
    CONST TYPE_CORRECTION = 2;
    const TYPES = [
        self::TYPE_EXPENSE,
        self::TYPE_INCOME,
        self::TYPE_CORRECTION,
    ];

    protected $fillable = [
        'amount',
        'type',
        'bill_id',
        'category_id',
        'currency_id',
        'place_id',
        'notes',
        'date',
        'is_correction',
        'is_draft',
    ];

    protected $casts = [
        'date' => 'date',
        'is_draft' => 'boolean',
        'is_correction' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new IsNotCorrectionScope);
    }

    public function getIsExpenseAttribute(): bool
    {
        return $this->type === self::TYPE_EXPENSE;
    }

    public function getIsIncomeAttribute(): bool
    {
        return $this->type === self::TYPE_INCOME;
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
        return MoneyFormatter::getWithCurrencyNameAndSign($this->amount, $this->currency->name, $this->type);
    }

    public function getAmountTextWithCurrencyAttribute(): string
    {
        return MoneyFormatter::getWithCurrencyName($this->amount, $this->currency->name);
    }

    public function getAmountFormattedAttribute(): string
    {
        return MoneyFormatter::get($this->amount);
    }

    public function getAmountInDefaultCurrencyAttribute(): string
    {
        return Currency::convertToDefault($this->currency, $this->amount, $this->date);
    }

    public function getAmountInDefaultCurrencyFormattedAttribute(): string
    {
        if ($this->amount_in_default_currency == 0) {
            return '';
        }
        return MoneyFormatter::getWithCurrencyName($this->amount_in_default_currency, Currency::getDefaultCurrencyName());
    }

    public function scopeLatestDate($query)
    {
        return $query->orderBy('date', 'desc');
    }

    public function scopeIsNotDraft($query)
    {
        return $query->where('is_draft', false);
    }

    public function scopeIsCorrection($query)
    {
        return $query->where('is_correction', true);
    }

    public function scopeIsNotCorrection($query)
    {
        return $query->where('is_correction', false);
    }

    public function scopeIsExpense($query)
    {
        return $query->where('type', self::TYPE_EXPENSE);
    }

    public function scopeIsIncome($query)
    {
        return $query->where('type', self::TYPE_INCOME);
    }
}
