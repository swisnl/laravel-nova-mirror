<?php

namespace Laravel\Nova\Lenses;

use Closure;
use stdClass;
use ArrayAccess;
use JsonSerializable;
use Laravel\Nova\Nova;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\ResolvesFilters;
use Illuminate\Support\Collection;
use Laravel\Nova\ProxiesCanSeeToGate;
use Laravel\Nova\Fields\FieldCollection;
use Laravel\Nova\Contracts\ListableField;
use Laravel\Nova\Http\Requests\LensRequest;
use Laravel\Nova\Http\Requests\NovaRequest;
use Illuminate\Contracts\Routing\UrlRoutable;
use Illuminate\Http\Resources\DelegatesToResource;
use Illuminate\Http\Resources\ConditionallyLoadsAttributes;

abstract class Lens implements ArrayAccess, JsonSerializable, UrlRoutable
{
    use ConditionallyLoadsAttributes,
        DelegatesToResource,
        ProxiesCanSeeToGate,
        ResolvesFilters;

    /**
     * The displayable name of the lens.
     *
     * @var string
     */
    public $name;

    /**
     * The underlying model resource instance.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    public $resource;

    /**
     * The callback used to authorize viewing the action.
     *
     * @var \Closure|null
     */
    public $seeCallback;

    /**
     * Execute the query for the lens.
     *
     * @param  \Laravel\Nova\Http\Requests\LensRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return mixed
     */
    abstract public static function query(LensRequest $request, $query);

    /**
     * Get the fields displayed by the lens.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    abstract public function fields(Request $request);

    /**
     * Create a new lens instance.
     *
     * @param  \Illuminate\Database\Eloquent\Model|null  $resource
     * @return void
     */
    public function __construct($resource = null)
    {
        $this->resource = $resource ?: new stdClass;
    }

    /**
     * Determine if the action should be available for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public function authorizedToSee(Request $request)
    {
        return $this->seeCallback ? call_user_func($this->seeCallback, $request) : true;
    }

    /**
     * Set the callback to be run to authorize viewing the action.
     *
     * @param  \Closure  $callback
     * @return $this
     */
    public function canSee(Closure $callback)
    {
        $this->seeCallback = $callback;

        return $this;
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
     * Get the URI key for the lens.
     *
     * @return string
     */
    public function uriKey()
    {
        return Str::slug($this->name());
    }

    /**
     * Prepare the resource for JSON serialization.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function serializeForIndex(NovaRequest $request)
    {
        return $this->serializeWithId($this->resolveFields($request)
                ->reject(function ($field) {
                    return $field instanceof ListableField || ! $field->showOnIndex;
                }));
    }

    /**
     * Resolve the given fields to their values.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Laravel\Nova\Fields\FieldCollection
     */
    public function resolveFields(NovaRequest $request)
    {
        return new FieldCollection(
            $this->availableFields($request)
                ->each->resolve($this->resource)
                ->filter->authorize($request)
                ->each->resolveForDisplay($this->resource)
                ->values()->all()
        );
    }

    /**
     * Get the fields that are available for the given request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Illuminate\Support\Collection
     */
    protected function availableFields(NovaRequest $request)
    {
        return collect(array_values($this->filter($this->fields($request))));
    }

    /**
     * Prepare the lens for JSON serialization using the given fields.
     *
     * @param  \Illuminate\Support\Collection  $fields
     * @return array
     */
    protected function serializeWithId(Collection $fields)
    {
        return [
            'id' => $fields->whereInstanceOf(ID::class)->first() ?: ID::forModel($this->resource),
            'fields' => $fields->all(),
        ];
    }

    /**
     * Prepare the lens for JSON serialization.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'name' => $this->name(),
            'uriKey' => $this->uriKey(),
        ];
    }
}
