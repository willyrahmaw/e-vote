<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'key',
        'value',
        'group',
        'type',
    ];

    /**
     * Get a setting value by key with optional default and type casting.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        try {
            $all = Cache::rememberForever('system_settings', function () {
                return self::query()->pluck('value', 'key')->toArray();
            });

            if (! is_array($all) || ! array_key_exists($key, $all)) {
                return $default;
            }

            return $all[$key] ?? $default;
        } catch (\Throwable $e) {
            return $default;
        }
    }

    /**
     * Set a setting value and clear cache.
     */
    public static function set(string $key, mixed $value, string $group = 'general', string $type = 'string'): self
    {
        $setting = self::query()->updateOrCreate(
            ['key' => $key],
            [
                'value' => is_bool($value) ? ($value ? '1' : '0') : (string) $value,
                'group' => $group,
                'type' => $type,
            ]
        );

        Cache::forget('system_settings');

        return $setting;
    }

    /**
     * Clear all cached settings.
     */
    public static function clearCache(): void
    {
        Cache::forget('system_settings');
    }
}
