<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillCurrencyInitial extends Model
{
    use HasFactory;

    protected $table = 'bill_currency_initial';

    protected $fillable = ['bill_id', 'currency_id', 'amount'];
}
