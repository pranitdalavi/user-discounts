<?php

namespace PranitDalavi\UserDiscounts;

use Illuminate\Support\ServiceProvider;

class UserDiscountsServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/Config/discounts.php', 'discounts');
    }

    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->publishes([
            __DIR__.'/Config/discounts.php' => config_path('discounts.php'),
        ], 'config');
    }
}