<?php

namespace App\Models;

use App\Helpers\MoneyFormatter;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

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
    ];

    public function scopeIsMonthly($query)
    {
        return $query->where('frequency', 'monthly');
    }

    public function scopeIsAnnually($query)
    {
        return $query->where('frequency', 'annually');
    }

    public function getNextPaymentDate(Carbon $today): Carbon
    {
        if ($this->frequency === 'monthly') {
            if ($this->day >= $today->day) {
                return $today->setDay($this->day);
            }
            return $today->addMonthsNoOverflow()->setDay($this->day);
        }

        if ($this->day >= $today->day && $this->month >= $today->month) {
            return $today->setDay($this->day)->setMonth($this->month);

        }
        return $today->addYearsNoOverflow()->setDay($this->day)->setMonth($this->month);
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
        // with sprintf function and month name
        return sprintf('Every year on %s %d', Carbon::create()->month($this->month)->format('F'), $this->day);
    }

    public function getAmountFormattedAttribute(): string
    {
        return MoneyFormatter::getWithSymbol($this->amount, $this->currency->name);
    }

    /**
     * Получить ближайшие планируемые расходы
     * @todo: move to Service
     * @return Collection
     */
    public static function getNearest(): Collection
    {
        $plannedExpenses = PlannedExpense::all();
        $plannedExpensesToBePaid = collect();

        foreach ($plannedExpenses as $plannedExpense) {
            if ($plannedExpense->isNearest()) {
                $plannedExpensesToBePaid->push($plannedExpense);
            }
        }

        return $plannedExpensesToBePaid->sortBy('next_payment_date');
    }

    public function isNearest(): bool
    {
        return $this->next_payment_date->diffInDays(today()) <= $this->reminder_days;
    }

    public function isDismissed(): bool
    {
        return Cache::get('dismissed_planned_expense_' . $this->id) === true;
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
}
