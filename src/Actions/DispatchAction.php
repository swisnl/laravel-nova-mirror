<?php

namespace Laravel\Nova\Actions;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Queue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Laravel\Nova\Http\Requests\ActionRequest;

class DispatchAction
{
    /**
     * Dispatch the given action.
     *
     * @param  \Laravel\Nova\Http\Requests\ActionRequest  $request
     * @param  \Laravel\Nova\Actions\Action  $action
     * @param  string  $method
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    public static function forModels(ActionRequest $request, Action $action, $method, Collection $models)
    {
        if ($models->isEmpty()) {
            return;
        }

        if ($action instanceof ShouldQueue) {
            return static::queueForModels($request, $action, $method, $models);
        }

        return Transaction::run(function ($batchId) use ($request, $action, $method, $models) {
            ActionEvent::createForModels($request, $action, $batchId, $models);

            return $action->withBatchId($batchId)->{$method}($request->resolveFields(), $models);
        }, function ($batchId) {
            ActionEvent::markBatchAsFinished($batchId);
        });
    }

    /**
     * Dispatch the given action in the background.
     *
     * @param  \Laravel\Nova\Http\Requests\ActionRequest  $request
     * @param  \Laravel\Nova\Actions\Action  $action
     * @param  string  $method
     * @param  \Illuminate\Support\Collection  $models
     * @return void
     */
    protected static function queueForModels(ActionRequest $request, Action $action, $method, Collection $models)
    {
        return Transaction::run(function ($batchId) use ($request, $action, $method, $models) {
            ActionEvent::createForModels($request, $action, $batchId, $models, 'waiting');

            Queue::connection(static::connection($action))->pushOn(
                static::queue($action),
                new CallQueuedAction(
                    $action, $method, $request->resolveFields(), $models, $batchId
                )
            );
        });
    }

    /**
     * Extract the queue connection for the action.
     *
     * @param  \Laravel\Nova\Actions\Action  $action
     * @return string|null
     */
    protected static function connection($action)
    {
        return property_exists($action, 'connection') ? $action->connection : null;
    }

    /**
     * Extract the queue name for the action.
     *
     * @param  \Laravel\Nova\Actions\Action  $action
     * @return string|null
     */
    protected static function queue($action)
    {
        return property_exists($action, 'queue') ? $action->queue : null;
    }
}
