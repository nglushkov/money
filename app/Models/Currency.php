<?php

namespace App\Models;

use App\Events\CurrencyProcessed;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Currency extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    protected $casts = [
        'active' => 'boolean',
        'is_default' => 'boolean'
    ];

    protected $dispatchesEvents = [
        'created' => CurrencyProcessed::class,
        'updated' => CurrencyProcessed::class,
        'deleted' => CurrencyProcessed::class,
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

    public function convertToDefault(string $amount, Carbon $date): string
    {
        if ($this->is_default) {
            return $amount;
        }

        $rate = $this->getCurrencyRate($date);

        if ($rate === null) {
            return '0';
        }

        return $rate->rate ? bcdiv($amount, $rate->rate, 2) : '0';
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public static function getDefaultCurrency(): Currency
    {
        $cacheKey = 'default_currency';
        if (cache()->has($cacheKey)) {
            return cache()->get($cacheKey);
        }

        $currency = self::default()->first();
        cache()->forever($cacheKey, $currency);

        return $currency;
    }

    public static function getDefaultCurrencyName(): string
    {
        return self::getDefaultCurrency()->name;
    }

    public static function getDefaultCurrencyId(): int
    {
        return self::getDefaultCurrency()->id;
    }

    public function getCurrencyRate(Carbon $date): ?Rate
    {
        $rateCacheKey = sprintf('currency_rate_%s_%s', $this->id, $date->toDateString());
        $rate = cache()->tags(['currency_rates'])->get($rateCacheKey);
        if ($rate !== null) {
            return $rate;
        }

        $rate = $this->ratesTo()->where('from_currency_id', Currency::getDefaultCurrencyId())
            ->where('date', '<=', $date)
            ->orderBy('date', 'desc')
            ->first();

        cache()->tags(['currency_rates'])->put($rateCacheKey, $rate ?? null);

        return $rate;
    }
}
