<?php

namespace ItmoSoft\Settings;

use Illuminate\Support\ServiceProvider;

class SettingsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/settings.php',
            'itmosoft-settings'
        );

        $this->app->singleton(Settings::class);

        $this->app->alias(Settings::class, 'itmosoft.settings');
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(
            __DIR__ . '/../database/migrations'
        );

        $this->publishes([
            __DIR__ . '/../config/settings.php' => config_path('itmosoft-settings.php'),
        ], 'itmosoft-settings-config');
    }
}