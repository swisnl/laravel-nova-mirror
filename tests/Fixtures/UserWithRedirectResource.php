<?php

namespace Laravel\Nova\Tests\Fixtures;

use Laravel\Nova\Http\Requests\CreateResourceRequest;
use Laravel\Nova\Http\Requests\UpdateResourceRequest;

class UserWithRedirectResource extends UserResource
{

    public static function uriKey()
    {
        return 'users-with-redirects';
    }

    public static function redirectAfterCreate(CreateResourceRequest $request, $newResource)
    {
        return 'https://yahoo.com';
    }

    public static function redirectAfterUpdate(UpdateResourceRequest $request, $newResource)
    {
        return 'https://google.com';
    }
}
