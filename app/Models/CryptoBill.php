<?php

namespace App\Models;

use App\Dto\CurrencyAmountDto;
use App\Helpers\MoneyFormatter;
use App\Helpers\MoneyHelper;
use App\Models\Enum\OperationType;
use App\Models\Scopes\IsNotCorrectionScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;


class CryptoBill extends Model
{
    use HasFactory;

    protected $fillable = [
        'bill_id',
        'currency_id',
        'total_invested_amount',
    ];

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }


}
