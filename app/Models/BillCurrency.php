<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillCurrency extends Model
{
    use HasFactory;

    protected $fillable = ['bill_id', 'currency_id', 'amount'];
}
