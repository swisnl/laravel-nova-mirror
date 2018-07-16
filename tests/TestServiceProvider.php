<?php

namespace Laravel\Nova\Tests;

use Laravel\Nova\Nova;
use Illuminate\Support\ServiceProvider;

class TestServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        Nova::routes()->withAuthenticationRoutes()
                      ->withPasswordResetRoutes();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
