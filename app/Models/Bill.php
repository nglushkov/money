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
        $operations = $this->operations->where('currency_id', $currencyId);
        $initialAmount = $this->currenciesInitial->find($currencyId)->pivot->amount ?? 0;

        foreach ($operations as $operation) {
            if ($operation->type === 0) {
                $initialAmount -= $operation->amount;
            } else {
                $initialAmount += $operation->amount;
            }
        }

        $transfers = Transfer::where('from_bill_id', $this->id)
            ->where('currency_id', $currencyId)
            ->sum('amount');

        $initialAmount -= $transfers;

        $transfers = Transfer::where('to_bill_id', $this->id)
            ->where('currency_id', $currencyId)
            ->sum('amount');
        $initialAmount += $transfers;

        return $initialAmount;
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
}
