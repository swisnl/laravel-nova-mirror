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
}
