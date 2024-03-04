<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExchangePlace extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function exchanges()
    {
        return $this->hasMany(Exchange::class);
    }
}
