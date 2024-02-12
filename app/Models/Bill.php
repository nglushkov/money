<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Bill extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'notes'];

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
        if ($amount) {
            return $amount;
        }

        $operations = $this->operations->where('currency_id', $currencyId);
        $amount = $this->currenciesInitial->find($currencyId)->pivot->amount ?? 0;

        foreach ($operations as $operation) {
            if ($operation->type === 0) {
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

        $this->setAmountInCache($currencyId, $amount);

        return $amount;

    }

    private function setAmountInCache(int $currencyId, float $amount): void
    {
        $key = 'bill_amount_' . $this->id . '_currency_' . $currencyId;
        cache()->put($key, $amount, now()->addYears(10));
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
        foreach (Currency::all() as $currency) {
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
}
