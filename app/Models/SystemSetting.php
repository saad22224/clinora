<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = ['key', 'value', 'description'];

    /**
     * Get setting value by key with fallback default.
     */
    public static function get(string $key, $default = null)
    {
        try {
            $setting = static::where('key', $key)->first();

            return $setting !== null ? $setting->value : $default;
        } catch (\Exception $e) {
            return $default;
        }
    }

    /**
     * Set setting value by key.
     */
    public static function set(string $key, $value, ?string $description = null)
    {
        return static::updateOrCreate(
            ['key' => $key],
            ['value' => (string) $value, 'description' => $description]
        );
    }

    /**
     * Check if WhatsApp service is globally enabled across the platform.
     * Defaults to true if not set yet.
     */
    public static function isWhatsAppEnabled(): bool
    {
        $val = static::get('whatsapp_enabled', '1');

        return $val === '1' || $val === 'true' || $val === true || $val === 1;
    }
}
