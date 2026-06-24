<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type'
    ];

    // Remove the casts array to avoid the mixed cast error
    // protected $casts = [
    //     'value' => 'string'
    // ];

    /**
     * Set the value attribute with proper type casting
     */
    public function setValueAttribute($value)
    {
        // Store the value as JSON string
        $this->attributes['value'] = is_null($value) ? null : json_encode($value);
    }

    /**
     * Get the value attribute with proper type casting
     */
    public function getValueAttribute($value)
    {
        if (is_null($value)) {
            return null;
        }

        $decoded = json_decode($value, true);
        // If decoding fails, return the original value
        if (json_last_error() !== JSON_ERROR_NONE) {
            return $value;
        }
        
        // For numeric values, convert them to appropriate types
        if (is_numeric($decoded)) {
            // Check if it's an integer
            if (ctype_digit(strval($decoded)) || is_int($decoded)) {
                return (int) $decoded;
            }
            // Otherwise treat as float
            return (float) $decoded;
        }
        
        return $decoded;
    }

    /**
     * Get a setting value by key
     */
    public static function get($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Set a setting value by key
     */
    public static function set($key, $value)
    {
        $setting = self::where('key', $key)->first();

        if (!$setting) {
            $setting = new self();
            $setting->key = $key;
        }

        $setting->value = $value;
        $setting->type = gettype($value);
        $setting->save();

        return $setting;
    }
}