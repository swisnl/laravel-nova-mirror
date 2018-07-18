<?php

namespace Laravel\Nova\Tests\Fixtures;

use Laravel\Nova\Resource;
use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\MorphMany;
use Laravel\Nova\Fields\MorphToMany;
use Laravel\Nova\Http\Requests\NovaRequest;

class PostResource extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \Laravel\Nova\Tests\Fixtures\Post::class;

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'title',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            BelongsTo::make('User', 'user', UserResource::class),
            Text::make('Title', 'title')->rules('required', 'string', 'max:255'),
            MorphMany::make('Comments', 'comments', CommentResource::class),
            MorphToMany::make('Tags', 'tags', TagResource::class)->display(function ($tag) {
                return strtoupper($tag->name);
            })->searchable()->fields(function () {
                return [
                    Text::make('Admin', 'admin')->rules('required'),
                ];
            }),
        ];
    }

    /**
     * Build a "relatable" query for the given resource.
     *
     * This query determines which instances of the model may be attached to other resources.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function relatableQuery(NovaRequest $request, $query)
    {
        return $query->where('id', '<', 3);
    }

    /**
     * Build a "relatable" query for the users.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function relatableUsers(NovaRequest $request, $query)
    {
        if (! isset($_SERVER['nova.post.useCustomRelatableUsers'])) {
            return UserResource::relatableQuery($request, $query);
        }

        $_SERVER['nova.post.relatableUsers'] = $query;

        return $query->where('id', '<', 3);
    }

    /**
     * Build a "relatable" query for the tags.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function relatableTags(NovaRequest $request, $query)
    {
        if (! isset($_SERVER['nova.post.useCustomRelatableTags'])) {
            return TagResource::relatableQuery($request, $query);
        }

        $_SERVER['nova.post.relatableTags'] = $query;

        return $query->where('id', '<', 3);
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [
            new PostWordCount,
            new PostCountTrend,
            new PostAverageTrend,
            new PostSumTrend,
            new PostMaxTrend,
            new PostMinTrend,
            new PostsByUserPartition,
            new WordCountByUserPartition,
        ];
    }

    /**
     * Get the URI key for the resource.
     *
     * @return string
     */
    public static function uriKey()
    {
        return 'posts';
    }
}
