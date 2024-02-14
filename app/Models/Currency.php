<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'active', 'is_default'];

    protected $casts = [
        'active' => 'boolean',
        'is_default' => 'boolean'
    ];

    public function billsInitial()
    {
        return $this->belongsToMany(Bill::class, 'bill_currency_initial')
                    ->withPivot('amount')
                    ->withTimestamps();
    }

    public function operations()
    {
        return $this->hasMany(Operation::class);
    }

    public function getSum($bills)
    {
        $sum = 0;
        foreach ($bills as $bill) {
            $sum += $bill->getAmount($this->id);
        }
        return $sum;
    }
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function ratesTo()
    {
        return $this->hasMany(Rate::class, 'to_currency_id');
    }

    public function ratesFrom()
    {
        return $this->hasMany(Rate::class, 'from_currency_id');
    }

    public function getDefaultCurrencyAttribute()
    {
        return $this->where('is_default', true)->first();
    }

    public function convertToDefault($amount, $date)
    {
        if ($this->is_default) {
            return $amount;
        }

        $rate = $this->ratesTo()->where('from_currency_id', $this->defaultCurrency->id)
            ->where('date', '<=', $date)
            ->orderBy('date', 'desc')
            ->first();

        return $rate ? bcdiv($amount, $rate->rate, 2) : 0;
    }
}
