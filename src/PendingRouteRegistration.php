<?php

namespace Laravel\Nova;

use Illuminate\Support\Facades\Route;

class PendingRouteRegistration
{
    /**
     * Register the Nova authentication routes.
     *
     * @param  array  $middleware
     * @return $this
     */
    public function withAuthenticationRoutes($middleware = ['web'])
    {
        Route::namespace('Laravel\Nova\Http\Controllers')->middleware($middleware)->group(function () {
            Route::get(Nova::path().'/login', 'LoginController@showLoginForm');
            Route::post(Nova::path().'/login', 'LoginController@login')->name('nova.login');
            Route::get(Nova::path().'/logout', 'LoginController@logout');
        });

        return $this;
    }

    /**
     * Register the Nova password reset routes.
     *
     * @param  array  $middleware
     * @return $this
     */
    public function withPasswordResetRoutes($middleware = ['web'])
    {
        Nova::$resetsPasswords = true;

        Route::namespace('Laravel\Nova\Http\Controllers')->middleware($middleware)->group(function () {
            Route::get(Nova::path().'/password/reset', 'ForgotPasswordController@showLinkRequestForm')->name('nova.password.request');
            Route::post(Nova::path().'/password/email', 'ForgotPasswordController@sendResetLinkEmail')->name('nova.password.email');
            Route::get(Nova::path().'/password/reset/{token}', 'ResetPasswordController@showResetForm')->name('nova.password.reset');
            Route::post(Nova::path().'/password/reset', 'ResetPasswordController@reset');
        });

        return $this;
    }

    /**
     * Handle the object's destruction and register the router route.
     *
     * @return void
     */
    public function __destruct()
    {
        Route::view(Nova::path(), 'nova::router')->middleware(config('nova.middleware', []));

        Route::middleware(config('nova.middleware', []))->get(Nova::path().'/{view}', function ($view) {
            return view('nova::router');
        })->where('view', '.*');
    }
}
