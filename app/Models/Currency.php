<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'active', 'is_default'];

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
}
