<?php

namespace Laravel\Nova\Tests\Fixtures;

use Laravel\Nova\Resource;
use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;

class CustomKeyResource extends Resource
{
    public static $model = CustomKey::class;

    public static function uriKey()
    {
        return 'custom-keys';
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make(),
        ];
    }
}
