<?php

namespace Laravel\Nova;

use Laravel\Nova\Http\Requests\NovaRequest;

trait FillsFields
{
    /**
     * Fill a new model instance using the given request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return array
     */
    public static function fill(NovaRequest $request, $model)
    {
        return static::fillFields(
            $request, $model,
            (new static($model))->creationFields($request)
        );
    }

    /**
     * Fill a new model instance using the given request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return array
     */
    public static function fillForUpdate(NovaRequest $request, $model)
    {
        return static::fillFields(
            $request, $model,
            (new static($model))->updateFields($request)
        );
    }

    /**
     * Fill a new pivot model instance using the given request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  \Illuminate\Database\Eloquent\Relations\Pivot  $pivot
     * @return array
     */
    public static function fillPivot(NovaRequest $request, $model, $pivot)
    {
        $instance = new static($model);

        return static::fillFields(
            $request, $pivot,
            $instance->creationPivotFields($request, $request->relatedResource)
        );
    }

    /**
     * Fill the given fields for the model.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  \Illuminate\Support\Collection  $fields
     * @return array
     */
    protected static function fillFields(NovaRequest $request, $model, $fields)
    {
        return [$model, $fields->map->fill($request, $model)->filter(function ($callback) {
            return is_callable($callback);
        })->values()->all()];
    }
}
