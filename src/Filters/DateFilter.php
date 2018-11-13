<?php

namespace Laravel\Nova\Filters;

use Illuminate\Http\Request;
use Illuminate\Container\Container;

abstract class DateFilter extends Filter
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'date-filter';

    /**
     * Get the filter's available options.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function options(Request $request)
    {
        //
    }
}
