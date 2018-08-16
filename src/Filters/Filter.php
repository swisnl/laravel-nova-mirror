<?php

namespace Laravel\Nova\Filters;

use Closure;
use JsonSerializable;
use Laravel\Nova\Nova;
use Illuminate\Http\Request;
use Illuminate\Container\Container;
use Laravel\Nova\ProxiesCanSeeToGate;

abstract class Filter implements JsonSerializable
{
    use ProxiesCanSeeToGate;

    /**
     * The displayable name of the action.
     *
     * @var string
     */
    public $name;

    /**
     * The callback used to authorize viewing the filter.
     *
     * @var \Closure|null
     */
    public $seeCallback;

    /**
     * Apply the filter to the given query.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    abstract public function apply(Request $request, $query, $value);

    /**
     * Get the filter's available options.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    abstract public function options(Request $request);

    /**
     * Determine if the filter should be available for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public function authorizedToSee(Request $request)
    {
        return $this->seeCallback ? call_user_func($this->seeCallback, $request) : true;
    }

    /**
     * Set the callback to be run to authorize viewing the filter.
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
     * Get the displayable name of the filter.
     *
     * @return string
     */
    public function name()
    {
        return $this->name ?: Nova::humanize($this);
    }

    /**
     * Prepare the filter for JSON serialization.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        $container = Container::getInstance();

        return [
            'class' => get_class($this),
            'name' => $this->name(),
            'options' => collect($this->options($container->make(Request::class)))->map(function ($value, $key) {
                return ['name' => $key, 'value' => $value];
            })->values()->all(),
            'currentValue' => '',
        ];
    }
}
