<?php

namespace PranitDalavi\UserDiscounts;

use Illuminate\Support\ServiceProvider;

class UserDiscountsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register()
    {
        // Merge package config with app config
        $this->mergeConfigFrom(__DIR__ . '/Config/discounts.php', 'discounts');
    }

    /**
     * Bootstrap services.
     */
    public function boot()
    {
        // Load migrations directly from package
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // Publish config file
        $this->publishes([
            __DIR__ . '/Config/discounts.php' => config_path('discounts.php'),
        ], 'config');

        // Publish tests to the application's tests/Unit/UserDiscounts folder
        $this->publishes([
            __DIR__ . '/../tests/' => base_path('tests/Unit/UserDiscounts'),
        ], 'tests');

        // Publish migrations to the application's database/migrations folder
        $this->publishes([
            __DIR__ . '/../database/migrations/' => database_path('migrations'),
        ], 'migrations');
    }
}
