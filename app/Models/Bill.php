<?php

namespace App\Models;

use App\Helpers\MoneyFormatter;
use App\Models\Enum\OperationType;
use App\Models\Scopes\IsNotCorrectionScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Bill extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'notes', 'user_id'];

    protected $casts = [
        'default' => 'boolean',
    ];

    public function currenciesInitial()
    {
        return $this->belongsToMany(Currency::class, 'bill_currency_initial')
                    ->withPivot('amount')
                    ->withTimestamps();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function operations(): HasMany
    {
        return $this->hasMany(Operation::class);
    }

    public function getAmount(int $currencyId): float
    {
        $amount = $this->getAmountFromCache($currencyId);
        if ($amount !== null) {
            return $amount;
        }

        $operations = $this->operations()
            ->withoutGlobalScope(IsNotCorrectionScope::class)
            ->isNotDraft()
            ->where('currency_id', $currencyId)->get();

        $amount = $this->currenciesInitial->find($currencyId)->pivot->amount ?? 0;

        foreach ($operations as $operation) {
            if ($operation->type === OperationType::Expense->name) {
                $amount -= $operation->amount;
            } else {
                $amount += $operation->amount;
            }
        }

        $transfers = Transfer::where('from_bill_id', $this->id)
            ->where('currency_id', $currencyId)
            ->sum('amount');

        $amount -= $transfers;

        $transfers = Transfer::where('to_bill_id', $this->id)
            ->where('currency_id', $currencyId)
            ->sum('amount');
        $amount += $transfers;

        $exchangeFrom = Exchange::where('bill_id', $this->id)
            ->where('from_currency_id', $currencyId)
            ->sum('amount_from');
        $amount -= $exchangeFrom;

        $exchangeTo = Exchange::where('bill_id', $this->id)
            ->where('to_currency_id', $currencyId)
            ->sum('amount_to');
        $amount += $exchangeTo;

        $this->setAmountInCache($currencyId, $amount);

        return $amount;
    }

    public function getAmountWithCurrency(int $currencyId): string
    {
        return MoneyFormatter::getWithCurrencyName($this->getAmount($currencyId), Currency::find($currencyId)->name);
    }

    private function setAmountInCache(int $currencyId, float $amount): void
    {
        $key = 'bill_amount_' . $this->id . '_currency_' . $currencyId;
        cache()->forever($key, $amount);
    }

    private function getAmountFromCache(int $currencyId): ?float
    {
        $key = 'bill_amount_' . $this->id . '_currency_' . $currencyId;
        return cache()->get($key);
    }

    public function clearAmountCache(): void
    {
        foreach (Currency::all() as $currency) {
            $key = 'bill_amount_' . $this->id . '_currency_' . $currency->id;
            cache()->forget($key);
        }
    }

    public function getAmounts(): array
    {
        $amounts = [];
        foreach (Currency::orderBy('name')->get() as $currency) {
            $amounts[$currency->name] = $this->getAmount($currency->id);
        }
        return $amounts;
    }

    public function getAmountNotNull(): array
    {
        $amounts = $this->getAmounts();
        foreach ($amounts as $currency => $amount) {
            if ($amount === .0) {
                unset($amounts[$currency]);
            }
        }
        return $amounts;
    }

    public function transfersFrom(): HasMany
    {
        return $this->hasMany(Transfer::class, 'from_bill_id');
    }

    public function getNameWithUserAttribute(): string
    {
        if ($this->isMyBill()) {
            return $this->name;
        } else if ($this->isCommonBill()) {
            return $this->name . ' (Common)';
        }
        return $this->name . ' (' . $this->user->name . ')';
    }

    public function scopeDefault($query)
    {
        return $query->where('default', true);
    }

    /**
     * @param Bill $bill
     * @param string $currencyName
     * @param float $amount
     * @return void
     * @todo: Move to service
     */
    public function correctAmount(Bill $bill, string $currencyName, float $amount)
    {
        $currency = Currency::where(['name' => $currencyName])->first();
        $billAmount = $bill->getAmount($currency->id);

        $fields = [
            'bill_id' => $bill->id,
            'currency_id' => $currency->id,
            'amount' => $amount > $billAmount ? $amount - $billAmount : $billAmount - $amount,
            'type' => $amount > $billAmount ? OperationType::Income->name : OperationType::Expense->name,
            'date' => now(),
            'notes' => 'Correction',
            'user_id' => auth()->id(),
            'is_correction' => true,
        ];

        if ($billAmount === $amount) {
            return;
        } else {
            $operation = new Operation($fields);
            $operation->user_id = auth()->id();
            $operation->save();
        }
    }

    public function getOwnerNameAttribute(): string
    {
        return $this->user->name ?? '<Common>';
    }

    private function isMyBill(): bool
    {
        return $this->user_id === auth()->id();
    }

    private function isCommonBill(): bool
    {
        return $this->user_id === null;
    }
}
