<?php

namespace Laravel\Nova\Http\Requests;

use Laravel\Nova\Nova;
use Laravel\Nova\Metrics\Metric;

class DashboardMetricRequest extends NovaRequest
{
    /**
     * Get the metric instance for the given request.
     *
     * @return \Laravel\Nova\Metrics\Metric
     */
    public function metric()
    {
        return $this->availableMetrics()->first(function ($metric) {
            return $this->metric === $metric->uriKey();
        }) ?: abort(404);
    }

    /**
     * Get all of the possible metrics for the request.
     *
     * @return \Illuminate\Support\Collection
     */
    public function availableMetrics()
    {
        return Nova::availableDashboardCards($this)->whereInstanceOf(Metric::class);
    }
}
