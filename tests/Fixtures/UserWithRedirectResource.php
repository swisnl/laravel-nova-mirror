<?php

namespace Laravel\Nova\Tests\Fixtures;

use Laravel\Nova\Http\Requests\CreateResourceRequest;
use Laravel\Nova\Http\Requests\UpdateResourceRequest;
use Laravel\Nova\Panel;
use Laravel\Nova\Resource;
use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\File;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\HasOne;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\KeyValue;
use Laravel\Nova\ResourceToolElement;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Http\Requests\NovaRequest;

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
