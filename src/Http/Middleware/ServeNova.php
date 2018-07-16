<?php

namespace Laravel\Nova\Http\Middleware;

use Laravel\Nova\Nova;
use Laravel\Nova\NovaServiceProvider;

class ServeNova
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Illuminate\Http\Response
     */
    public function handle($request, $next)
    {
        if ($this->isNovaRequest($request)) {
            app()->register(NovaServiceProvider::class);
        }

        return $next($request);
    }

    /**
     * Determine if the given request is intended for Nova.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function isNovaRequest($request)
    {
        $path = trim(Nova::path(), '/');

        return $request->is($path) ||
               $request->is($path.'/*') ||
               $request->is('nova-api/*');
    }
}
