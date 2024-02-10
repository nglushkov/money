<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use DateTimeInterface;

class Operation extends Model
{
    use HasFactory;

    protected $casts = [
        'date' => 'datetime',
    ];

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
        return $this->type === 0 ? 'Расход' : 'Приход';
    }

    public function getAmountAttribute($value): string
    {
        return $this->type === 0 ? '-' . $value : '+' . $value;
    }

    public function getDateAttribute($value): string
    {
        return Carbon::parse($value)->format('d.m.Y');
    }
}
