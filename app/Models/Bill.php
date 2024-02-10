<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Bill extends Model
{
    use HasFactory;

    public function currencies()
    {
        return $this->belongsToMany(Currency::class, 'bill_currencies')
                    ->withPivot('amount')
                    ->withTimestamps();
    }
    
}
