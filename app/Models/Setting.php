<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    //

    protected $table = 'settings';

    protected $fillable = [
        'key_name',
        'value',
        'type',
    ];

    public static function get($key, $default = null)
    {
        $setting = self::where('key_name', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Lưu giá trị setting theo key_name.
     */
    public static function set($key, $value, $type = 'string')
    {
        return self::updateOrCreate(
            ['key_name' => $key],
            ['value' => $value, 'type' => $type]
        );
    }
}
