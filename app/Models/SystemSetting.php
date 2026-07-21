<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = ['setting_key', 'setting_value'];

    public static function get(string $key, ?string $default = null): ?string
    {
        return static::where('setting_key', $key)->value('setting_value') ?? $default;
    }
}
