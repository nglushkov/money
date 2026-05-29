<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AppSetting extends Model
{
    protected $primaryKey = null;
    public    $incrementing = false;

    protected $fillable = ['key', 'user_id', 'value'];

    public static function get(string $key, mixed $default = null, ?int $userId = null): mixed
    {
        $userId ??= Auth::id();
        return static::where('user_id', $userId)->where('key', $key)->value('value') ?? $default;
    }

    public static function set(string $key, mixed $value, ?int $userId = null): void
    {
        $userId ??= Auth::id();
        static::updateOrCreate(['user_id' => $userId, 'key' => $key], ['value' => $value]);
    }
}
