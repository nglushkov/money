<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MercadoPagoDismissed extends Model
{
    public $timestamps = false;

    protected $fillable = ['external_id', 'user_id'];

    public static function isDismissed(string $externalId): bool
    {
        return static::where('external_id', $externalId)->exists();
    }

    public static function dismiss(string $externalId, int $userId): void
    {
        static::firstOrCreate(
            ['external_id' => $externalId],
            ['user_id' => $userId]
        );
    }
}
