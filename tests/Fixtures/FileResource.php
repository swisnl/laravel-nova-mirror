<?php

namespace Laravel\Nova\Tests\Fixtures;

use Laravel\Nova\Resource;
use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Image;

class FileResource extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \Laravel\Nova\Tests\Fixtures\File::class;

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        if (isset($_SERVER['nova.fileResource.imageField'])) {
            $field = $_SERVER['nova.fileResource.imageField']($request);
        }

        return [
            ID::make('ID', 'id'),

            $field ?? Image::make('Avatar', 'avatar', null, function ($request, $model) {
                return $request->avatar->storeAs('avatars', 'avatar.png');
            })->rules('required')->delete(function ($request) {
                $_SERVER['__nova.fileDeleted'] = true;

                return $_SERVER['__nova.fileDelete'] ?? null;
            })->prunable(),
        ];
    }

    /**
     * Get the URI key for the resource.
     *
     * @return string
     */
    public static function uriKey()
    {
        return 'files';
    }
}
