<?php

namespace App\Models;

use App\Enum\CacheKey;
use App\Enum\CacheTag;
use App\Events\CurrencyProcessed;
use App\Helpers\MoneyHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 */
class Currency extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'is_crypto'];

    protected $casts = [
        'active' => 'boolean',
        'is_default' => 'boolean',
        'is_crypto' => 'boolean',
    ];

    protected $dispatchesEvents = [
        'created' => CurrencyProcessed::class,
        'updated' => CurrencyProcessed::class,
        'deleted' => CurrencyProcessed::class,
    ];

    public function operations()
    {
        return $this->hasMany(Operation::class);
    }

    public function getSum($bills): string
    {
        $sum = 0;
        foreach ($bills as $bill) {
            $sum = MoneyHelper::add($sum, $bill->getAmount($this->id));
        }
        return $sum;
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true)->isNotCrypto();
    }

    public function scopeDefaultCrypto($query)
    {
        return $query->where('is_default', true)->isCrypto();
    }

    public function scopeIsCrypto($query)
    {
        return $query->where('is_crypto', true);
    }

    public function scopeIsNotCrypto($query)
    {
        return $query->where('is_crypto', false);
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

    public static function getDefaultCurrency(bool $isCrypto = false): Currency
    {
        $cacheKey = $isCrypto ? CacheKey::default_crypto_currency->name : CacheKey::default_currency->name;
        if (cache()->has($cacheKey)) {
            return cache()->get($cacheKey);
        }

        $currency = $isCrypto ? self::defaultCrypto()->first() : self::default()->first();
        cache()->forever($cacheKey, $currency);

        return $currency;
    }

    public static function getDefaultCurrencyName(bool $isCrypto = false): string
    {
        return self::getDefaultCurrency($isCrypto)->name;
    }

    public static function getDefaultCurrencyId(bool $isCrypto = false): int
    {
        return self::getDefaultCurrency($isCrypto)->id;
    }

    public function getCurrencyRate(Carbon $date): ?Rate
    {
        $rateCacheKey = sprintf('%s_%s_%s', CacheKey::currency_rate->name, $this->id, $date->toDateString());
        $rate = cache()->tags(CacheTag::currency_rates->name)->get($rateCacheKey);
        if ($rate !== null) {
            return $rate;
        }

        $rate = $this->ratesTo()->where('from_currency_id', Currency::getDefaultCurrencyId($this->is_crypto))
            ->where('date', '<=', $date)
            ->orderBy('id', 'desc')
            ->first();

        cache()->tags(CacheTag::currency_rates->name)->put($rateCacheKey, $rate ?? null);

        return $rate;
    }

    public function getCurrentRate(): ?Rate
    {
        return $this->getCurrencyRate(Carbon::now());
    }

    public function getCurrentInvertedRateAsString(): string
    {
        if ($this->id === self::getDefaultCurrencyId($this->is_crypto)) {
            return '1';
        }
        $rate = $this->getCurrencyRate(Carbon::now());
        if ($rate === null || $rate->rate === 0) {
            return '';
        }

        return MoneyHelper::divide('1', $rate->rate, MoneyHelper::SCALE_SHORT);
    }

    public function getAmountByInvertedRate(Bill $bill): string
    {
        $rate = $this->getCurrentInvertedRateAsString();
        return MoneyHelper::multiply($bill->getAmount($this->id), $rate);
    }

    public function getTotalByInvertedRate(Bill $bill): string
    {
        $sum = 0;
        foreach ($bill->getAmountsNotNull() as $amount) {
            if ($amount->getCurrency()->is_default) {
                continue;
            }
            $rate = $amount->getCurrency()->getCurrentInvertedRateAsString();
            $sum = MoneyHelper::add($sum, MoneyHelper::multiply($amount->getAmount(), $rate));
        }
        return $sum;
    }
}
