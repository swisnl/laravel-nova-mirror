<?php

namespace Laravel\Nova\Http\Middleware;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as BaseAuthenticationMiddleware;
use Laravel\Nova\Exceptions\AuthenticationException as NovaAuthenticationException;

class Authenticate extends BaseAuthenticationMiddleware
{
    /**
     * Determine if the user is logged in to any of the given guards.
     *
     * @param  array  $guards
     * @return void
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    protected function authenticate(array $guards)
    {
        try {
            return parent::authenticate($guards);
        } catch (AuthenticationException $e) {
            throw new NovaAuthenticationException('Unauthenticated.', $e->guards());
        }
    }
}
