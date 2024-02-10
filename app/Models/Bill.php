<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Bill extends Model
{
    use HasFactory;

    public function currencies()
    {
        return $this->belongsToMany(Currency::class, 'bill_currencies')
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
    
}
