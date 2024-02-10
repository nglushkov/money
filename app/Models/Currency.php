<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    public function bills()
    {
        return $this->belongsToMany(Bill::class, 'bill_currencies')
                    ->withPivot('amount')
                    ->withTimestamps();
    }
}
