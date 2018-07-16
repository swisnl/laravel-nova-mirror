<?php

namespace Laravel\Nova\Query;

use Illuminate\Http\Request;
use Illuminate\Container\Container;

class ApplyFilter
{
    /**
     * The class name of the filter being applied.
     *
     * @var string
     */
    public $class;

    /**
     * The value of the filter.
     *
     * @var mixed
     */
    public $value;

    /**
     * Create a new invokable filter applier.
     *
     * @param  string  $class
     * @param  mixed  $value
     * @return void
     */
    public function __construct($class, $value)
    {
        $this->class = $class;
        $this->value = $value;
    }

    /**
     * Apply the filter to the given query.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function __invoke(Request $request, $query)
    {
        Container::getInstance()->make($this->class)->apply(
            $request, $query, $this->value
        );

        return $query;
    }
}
