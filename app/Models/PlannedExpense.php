<?php

namespace App\Models;

use App\Helpers\MoneyFormatter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property Currency currency
 * @property Category category
 * @property Place place
 * @property User user
 * @property Bill bill
 */
class PlannedExpense extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount',
        'day',
        'month',
        'frequency',
        'currency_id',
        'category_id',
        'place_id',
        'user_id',
        'notes',
        'reminder_days',
        'bill_id',
    ];

    public function scopeIsMonthly($query)
    {
        return $query->where('frequency', 'monthly');
    }

    public function scopeIsAnnually($query)
    {
        return $query->where('frequency', 'annually');
    }

    /**
     * todo: Move to service
     * @param Carbon $today
     * @return Carbon
     */
    public function getNextPaymentDate(Carbon $today): Carbon
    {
        if ($this->frequency === 'monthly') {
            if ($this->day >= $today->day) {
                return $today->setDay($this->day);
            }
            return $today->addMonthsNoOverflow()->setDay($this->day);
        }
        if ($this->month < $today->month) {
            return $today->addYearsNoOverflow()->setDay($this->day)->setMonth($this->month);
        }
        if ($this->month == $today->month && $this->day < $today->day) {
            return $today->addYearsNoOverflow()->setDay($this->day)->setMonth($this->month);
        }

        return $today->setDay($this->day)->setMonth($this->month);
    }

    public function getNextPaymentDateFormattedAttribute(): string
    {
        return $this->getNextPaymentDate(Carbon::today())->format('d.m.Y');
    }

    public function getNextPaymentDateHumansAttribute(): string
    {
        if ($this->getNextPaymentDate(Carbon::today())->isToday()) {
            return 'Сегодня';
        }
        return $this->getNextPaymentDate(Carbon::today())->diffForHumans();
    }

    public function getNextPaymentDateAttribute(): Carbon
    {
        return $this->getNextPaymentDate(Carbon::today());
    }

    public function getFrequencyTextAttribute(): string
    {
        if ($this->frequency === 'monthly') {
            return "Every month on day {$this->day}";
        }

        return sprintf('Every year on %s %d', Carbon::create()->month($this->month)->format('F'), $this->day);
    }

    public function getAmountFormattedAttribute(): string
    {
        return MoneyFormatter::getWithCurrencyName($this->amount, $this->currency->name);
    }

    public function getAmountInDefaultCurrencyAttribute(): float
    {
        return $this->currency->convertToDefault($this->amount, Carbon::today());
    }

    public function getAmountInDefaultCurrencyFormattedAttribute(): string
    {
        if ($this->amount_in_default_currency == 0) {
            return '';
        }
        return MoneyFormatter::getWithCurrencyName($this->amount_in_default_currency, Currency::getDefaultCurrencyName());
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function place()
    {
        return $this->belongsTo(Place::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

    public function setMonthAttribute($value)
    {
        $this->attributes['month'] = $value;
        if ($this->frequency === 'monthly') {
            $this->attributes['month'] = null;
        }
    }
}
