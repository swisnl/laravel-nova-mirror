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
        Route::namespace('Laravel\Nova\Http\Controllers')
            ->middleware($middleware)
            ->as('nova.')
            ->prefix(Nova::path())
            ->group(function () {
                Route::get('/login', 'LoginController@showLoginForm');
                Route::post('/login', 'LoginController@login')->name('login');
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

        Route::namespace('Laravel\Nova\Http\Controllers')
            ->middleware($middleware)
            ->as('nova.')
            ->prefix(Nova::path())
            ->group(function () {
                Route::get('/password/reset', 'ForgotPasswordController@showLinkRequestForm')->name('password.request');
                Route::post('/password/email', 'ForgotPasswordController@sendResetLinkEmail')->name('password.email');
                Route::get('/password/reset/{token}', 'ResetPasswordController@showResetForm')->name('password.reset');
                Route::post('/password/reset', 'ResetPasswordController@reset');
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
        Route::namespace('Laravel\Nova\Http\Controllers')
            ->middleware(config('nova.middleware', []))
            ->as('nova.')
            ->prefix(Nova::path())
            ->group(function () {
                Route::get('/logout', 'LoginController@logout');
            });

        Route::view(Nova::path(), 'nova::router')
            ->middleware(config('nova.middleware', []))
            ->name('nova.index');

        Route::middleware(config('nova.middleware', []))
            ->as('nova.')
            ->prefix(Nova::path())
            ->get('/{view}', function () {
                return view('nova::router');
            })
            ->where('view', '.*');
    }
}
