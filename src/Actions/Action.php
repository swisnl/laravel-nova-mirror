<?php

namespace Laravel\Nova\Actions;

use Closure;
use JsonSerializable;
use Laravel\Nova\Nova;
use Laravel\Nova\Metable;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Laravel\Nova\AuthorizedToSee;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\ProxiesCanSeeToGate;
use Laravel\Nova\Http\Requests\ActionRequest;
use Laravel\Nova\Exceptions\MissingActionHandlerException;

class Action implements JsonSerializable
{
    use Metable;
    use AuthorizedToSee;
    use ProxiesCanSeeToGate;

    /**
     * The displayable name of the action.
     *
     * @var string
     */
    public $name;

    /**
     * The action's component.
     *
     * @var string
     */
    public $component = 'confirm-action-modal';

    /**
     * Indicates if need to skip log action events for models.
     *
     * @var bool
     */
    public $withoutActionEvents = false;

    /**
     * Indicates if this action is available to run against the entire resource.
     *
     * @var bool
     */
    public $availableForEntireResource = false;

    /**
     * Determine where the action redirection should be without confirmation.
     *
     * @var bool
     */
    public $withoutConfirmation = false;

    /**
     * Indicates if this action is only available on the resource detail view.
     *
     * @var bool
     */
    public $onlyOnIndex = false;

    /**
     * Indicates if this action is only available on the resource detail view.
     *
     * @var bool
     */
    public $onlyOnDetail = false;

    /**
     * The current batch ID being handled by the action.
     *
     * @var string|null
     */
    public $batchId;

    /**
     * The callback used to authorize running the action.
     *
     * @var \Closure|null
     */
    public $runCallback;

    /**
     * The number of models that should be included in each chunk.
     *
     * @var int
     */
    public static $chunkCount = 200;

    /**
     * Determine if the action is executable for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return bool
     */
    public function authorizedToRun(Request $request, $model)
    {
        return $this->runCallback ? call_user_func($this->runCallback, $request, $model) : true;
    }

    /**
     * Return a message response from the action.
     *
     * @param  string  $message
     * @return array
     */
    public static function message($message)
    {
        return ['message' => $message];
    }

    /**
     * Return a dangerous message response from the action.
     *
     * @param  string  $message
     * @return array
     */
    public static function danger($message)
    {
        return ['danger' => $message];
    }

    /**
     * Return a delete response from the action.
     *
     * @return array
     */
    public static function deleted()
    {
        return ['deleted' => true];
    }

    /**
     * Return a redirect response from the action.
     *
     * @param  string  $url
     * @return array
     */
    public static function redirect($url)
    {
        return ['redirect' => $url];
    }

    /**
     * Return an open new tab response from the action.
     *
     * @param  string  $url
     * @return array
     */
    public static function openInNewTab($url)
    {
        return ['openInNewTab' => $url];
    }

    /**
     * Return a download response from the action.
     *
     * @param  string  $url
     * @param  string  $name
     * @return array
     */
    public static function download($url, $name)
    {
        return ['download' => $url, 'name' => $name];
    }

    /**
     * Execute the action for the given request.
     *
     * @param  \Laravel\Nova\Http\Requests\ActionRequest  $request
     * @return mixed
     * @throws MissingActionHandlerException
     */
    public function handleRequest(ActionRequest $request)
    {
        $method = ActionMethod::determine($this, $request->targetModel());

        if (! method_exists($this, $method)) {
            throw MissingActionHandlerException::make($this, $method);
        }

        $wasExecuted = false;

        $fields = $request->resolveFields();

        $results = $request->chunks(
            static::$chunkCount, function ($models) use ($fields, $request, $method, &$wasExecuted) {
                $models = $models->filterForExecution($request);

                if (count($models) > 0) {
                    $wasExecuted = true;
                }

                return DispatchAction::forModels(
                    $request, $this, $method, $models, $fields
                );
            }
        );

        if (! $wasExecuted) {
            return static::danger(__('Sorry! You are not authorized to perform this action.'));
        }

        return $this->handleResult($fields, $results);
    }

    /**
     * Handle chunk results.
     *
     * @param  \Laravel\Nova\Fields\ActionFields $fields
     * @param  array $results
     *
     * @return mixed
     */
    public function handleResult(ActionFields $fields, $results)
    {
        return count($results) ? end($results) : null;
    }

    /**
     * Mark the action event record for the model as finished.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return int
     */
    protected function markAsFinished($model)
    {
        return $this->batchId ? ActionEvent::markAsFinished($this->batchId, $model) : 0;
    }

    /**
     * Mark the action event record for the model as failed.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  \Throwable|string  $e
     * @return int
     */
    protected function markAsFailed($model, $e = null)
    {
        return $this->batchId ? ActionEvent::markAsFailed($this->batchId, $model, $e) : 0;
    }

    /**
     * Get the fields available on the action.
     *
     * @return array
     */
    public function fields()
    {
        return [];
    }

    /**
     * Indicate that this action can be run for the entire resource at once.
     *
     * @param  bool  $value
     * @return $this
     */
    public function availableForEntireResource($value = true)
    {
        $this->availableForEntireResource = $value;

        return $this;
    }

    /**
     * Indicate that this action is only available on the resource index view.
     *
     * @param  bool  $value
     * @return $this
     */
    public function onlyOnIndex($value = true)
    {
        $this->onlyOnIndex = $value;
        $this->onlyOnDetail = ! $value;

        return $this;
    }

    /**
     * Indicate that this action is only available on the resource detail view.
     *
     * @param  bool  $value
     * @return $this
     */
    public function onlyOnDetail($value = true)
    {
        $this->onlyOnDetail = $value;
        $this->onlyOnIndex = ! $value;

        return $this;
    }

    /**
     * Set the current batch ID being handled by the action.
     *
     * @param  string  $batchId
     * @return $this
     */
    public function withBatchId($batchId)
    {
        $this->batchId = $batchId;

        return $this;
    }

    /**
     * Set the callback to be run to authorize running the action.
     *
     * @param  \Closure  $callback
     * @return $this
     */
    public function canRun(Closure $callback)
    {
        $this->runCallback = $callback;

        return $this;
    }

    /**
     * Get the component name for the action.
     *
     * @return string
     */
    public function component()
    {
        return $this->component;
    }

    /**
     * Get the displayable name of the action.
     *
     * @return string
     */
    public function name()
    {
        return $this->name ?: Nova::humanize($this);
    }

    /**
     * Get the URI key for the action.
     *
     * @return string
     */
    public function uriKey()
    {
        return Str::slug($this->name());
    }

    /**
     * Set the action to execute instantly.
     *
     * @return string
     */
    public function withoutConfirmation()
    {
        $this->withoutConfirmation = true;

        return $this;
    }

    /**
     * Set the action to skip action events for models.
     *
     * @return $this
     */
    public function withoutActionEvents()
    {
        $this->withoutActionEvents = true;

        return $this;
    }

    /**
     * Prepare the action for JSON serialization.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return array_merge([
            'component' => $this->component(),
            'destructive' => $this instanceof DestructiveAction,
            'name' => $this->name(),
            'uriKey' => $this->uriKey(),
            'fields' => collect($this->fields())->each->resolve(new class {
            })->all(),
            'availableForEntireResource' => $this->availableForEntireResource,
            'onlyOnDetail' => $this->onlyOnDetail,
            'onlyOnIndex' => $this->onlyOnIndex,
            'withoutConfirmation' => $this->withoutConfirmation,
        ], $this->meta());
    }
}
