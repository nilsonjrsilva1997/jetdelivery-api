<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'display_name', 'value', 'type'];

    // Para facilitar a obtenção das configurações:
    public static function getValue($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function getType($key)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->type : null;
    }
}
