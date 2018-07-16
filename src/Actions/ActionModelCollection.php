<?php

namespace Laravel\Nova\Actions;

use Laravel\Nova\Http\Requests\ActionRequest;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class ActionModelCollection extends EloquentCollection
{
    /**
     * Remove models the user does not have permission to execute the action against.
     *
     * @param  \Laravel\Nova\Http\Requests\ActionRequest  $request
     * @return static
     */
    public function filterForExecution(ActionRequest $request)
    {
        $models = $this->mapInto($request->resource())
                       ->filter->authorizedToUpdate($request)->map->resource;

        $action = $request->action();

        if ($action instanceof DestructiveAction) {
            $models = $this->mapInto($request->resource())
                           ->filter->authorizedToDelete($request)->map->resource;
        }

        return static::make($models->filter(function ($model) use ($request, $action) {
            return $action->authorizedToRun($request, $model);
        }));
    }
}
