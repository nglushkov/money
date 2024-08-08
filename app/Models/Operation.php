<?php

namespace App\Models;

use App\Helpers\MoneyFormatter;
use App\Models\Enum\OperationType;
use App\Models\Interfaces\Copyable;
use App\Models\Scopes\IsNotCorrectionScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;

/**
 * @property Currency currency
 */
#[ScopedBy([IsNotCorrectionScope::class])]
class Operation extends Move implements Copyable
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
        return $this->type === OperationType::Expense->name;
    }

    public function getIsIncomeAttribute(): bool
    {
        return $this->type === OperationType::Income->name;
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
        return $this->is_expense ? OperationType::Expense->name : OperationType::Income->name;
    }

    public function getAmountTextAttribute(): string
    {
        if ($this->currency->is_crypto) {
            return MoneyFormatter::getCryptoWithCurrencyNameAndSign($this->amount, $this->currency->name, $this->type);
        }
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
        return $this->currency->convertToDefault($this->amount, $this->date);
    }

    public function getAmountInDefaultCurrencyFormattedAttribute(): string
    {
        if ($this->amount_in_default_currency == 0) {
            return '';
        }
        return MoneyFormatter::getWithCurrencyName($this->amount_in_default_currency, Currency::getDefaultCurrencyName());
    }

    public function getCurrencyRate(): ?Rate
    {
        return $this->currency->getCurrencyRate($this->date);
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
        return $query->where('type', OperationType::Expense->name);
    }

    public function scopeIsIncome($query)
    {
        return $query->where('type', OperationType::Income->name);
    }
}
