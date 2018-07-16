<?php

namespace Laravel\Nova\Http\Controllers;

use Illuminate\Support\Carbon;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Laravel\Nova\Actions\ActionEvent;
use Laravel\Nova\Http\Requests\UpdateResourceRequest;

class ResourceUpdateController extends Controller
{
    /**
     * Create a new resource.
     *
     * @param  \Laravel\Nova\Http\Requests\UpdateResourceRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function handle(UpdateResourceRequest $request)
    {
        $request->findResourceOrFail()->authorizeToUpdate($request);

        $resource = $request->resource();

        $resource::validateForUpdate($request);

        return DB::transaction(function () use ($request, $resource) {
            $model = $request->findModelQuery()->lockForUpdate()->firstOrFail();

            if ($this->modelHasBeenUpdatedSinceRetrieval($request, $model)) {
                return response('', 409);
            }

            [$model, $callbacks] = $resource::fillForUpdate($request, $model);

            return tap(tap($model)->save(), function ($model) use ($request, $callbacks) {
                ActionEvent::forResourceUpdate($request->user(), $model)->save();

                collect($callbacks)->each->__invoke();
            });
        });
    }

    /**
     * Determine if the model has been updated since it was retrieved.
     *
     * @param  \Laravel\Nova\Http\Requests\UpdateResourceRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    protected function modelHasBeenUpdatedSinceRetrieval(UpdateResourceRequest $request, $model)
    {
        $column = $model->getUpdatedAtColumn();

        if (! $model->{$column}) {
            return false;
        }

        return $request->input('_retrieved_at') && $model->usesTimestamps() && $model->{$column}->gt(
            Carbon::createFromTimestamp($request->input('_retrieved_at'))
        );
    }
}
