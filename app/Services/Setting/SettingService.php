<?php

namespace App\Services\Setting;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingService
{
    /**
     * Retrieve all settings grouped as a key-value dictionary.
     */
    public function getAll(): array
    {
        return Cache::rememberForever('system_settings', function () {
            return Setting::query()->pluck('value', 'key')->toArray();
        });
    }

    /**
     * Get a single setting by key.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return Setting::get($key, $default);
    }

    /**
     * Bulk update key-value pairs.
     */
    public function updateMany(array $settings): void
    {
        foreach ($settings as $key => $value) {
            Setting::query()->updateOrCreate(
                ['key' => $key],
                ['value' => is_bool($value) ? ($value ? '1' : '0') : (string) $value]
            );
        }

        Setting::clearCache();
    }

    /**
     * Clear system settings cache.
     */
    public function clearCache(): void
    {
        Setting::clearCache();
    }

    /**
     * Upload settings image/icon to public storage.
     */
    public function uploadImage(\Illuminate\Http\UploadedFile $file, string $folder = 'settings'): string
    {
        return $file->store($folder, 'public');
    }

    /**
     * Delete stored image file if exists.
     */
    public function deleteImage(?string $path): void
    {
        if ($path && \Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($path);
        }
    }
}
