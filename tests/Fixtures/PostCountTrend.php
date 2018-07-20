<?php

namespace Laravel\Nova\Tests\Fixtures;

use Illuminate\Http\Request;
use Laravel\Nova\Metrics\Trend;
use Laravel\Nova\Metrics\Value;

class PostCountTrend extends Trend
{
    /**
     * Calculate the value of the metric.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function calculate(Request $request)
    {
        return $this->count(
            $request, (new Post)->newQuery(),
            $_SERVER['nova.postCountUnit'] ?? Trend::BY_MONTHS
        );
    }

    /**
     * Get the ranges available for the metric.
     *
     * @return array
     */
    public function ranges()
    {
        return $_SERVER['nova.postCountRanges'] ?? [
            3 => 'Last 3 Months',
            6 => 'Last 6 Months',
            12 => 'Last 12 Months',
        ];
    }

    /**
     * Get the URI key for the metric.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'post-count-trend';
    }
}
