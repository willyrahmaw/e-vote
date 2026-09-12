<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
                $timezone = \App\Models\Setting::get('app_timezone', config('app.timezone', 'Asia/Jakarta'));
                if ($timezone && in_array($timezone, ['Asia/Jakarta', 'Asia/Makassar', 'Asia/Jayapura'])) {
                    date_default_timezone_set($timezone);
                    \Illuminate\Support\Facades\Config::set('app.timezone', $timezone);
                }
            }
        } catch (\Throwable $e) {
            // Fallback during initial migrations
        }
    }
}
